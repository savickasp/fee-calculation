<?php


	namespace App\Support\Calculations;


	class Fees
	{
		private $operation;
		private $userOperations;
		private $exchange;
		private $cashInLimitEur = 5;
		private $cashInFeeMultiplier = 0.0003;
		private $cashOutMultiplier = 0.003;
		private $cashOutNaturalSumLimit = 1000;
		private $cashOutLegalFeeMin = 0.5;

		public function __construct()
		{
			$this->exchange = new CurrencyConvertion();
		}

		public function calculateFee(array $operation, array $userOperations, bool $returnOnlyFee = false)
		{
			$this->operation = $operation;
			$this->userOperations = $userOperations;

			if ($this->operation['operation_type'] == 'cash_in') {
				$ret = $this->getCashInFee();
			} elseif ($this->operation['operation_type'] == 'cash_out') {
				$ret = $this->getCashOutFee();
			} else {
				$ret = 'unknown operation';
			}

			if ($returnOnlyFee) {
				return $ret;
			} else {
				$operation['fee'] = $ret;
				return $operation;
			}
		}

		private function getCashInFee()
		{
			$fee = $this->cashInFeeMultiplier * $this->operation['amount'];

			return $this->roundOrLimitFee($fee);
		}

		private function getCashOutFee()
		{
			if ($this->operation['user_type'] == 'legal') {
				$ret = $this->legalPersonFees();
			} elseif ($this->operation['user_type'] == 'natural') {
				$ret = $this->naturalPersonFees();
			} else {
				$ret = 'user type not found';
			}

			return $ret;
		}

		private function roundOrLimitFee(float $fee)
		{
			if ($fee > $this->cashInLimitEur) {
				$ret = $this->cashInLimitEur;
			} else {
				$ret = $this->roundNumber($fee, $this->operation['currency']);
			}

			return $ret;
		}

		private function roundNumber(float $fee, string $currency)
		{
			if ($currency === 'JPY') {
				$ret = ceil($fee);
			} elseif ($currency === 'EUR' || $currency === 'USD') {
				$ret = ceil($fee * 100) / 100;
			}

			return $ret;
		}

		private function naturalPersonFees()
		{
			$cashOuts = $this->getWeakCashOuts($this->operation['date']);

			if ((count($cashOuts) + 1) <= 3) {
				$sum = $this->operation['amount'];
				foreach ($cashOuts as $cashOut) {
					$sum += $cashOut;
				}

				$casOutLimitOppCurrency = $this->exchange->convert($this->cashOutNaturalSumLimit, 'EUR', $this->operation['currency']);
				if ($sum <= $casOutLimitOppCurrency) {
					$ret = 0.00;
				} else {
					$sum -= $casOutLimitOppCurrency;

					$fee = (($sum > $this->operation['amount']) ? $this->operation['amount'] : $sum) * $this->cashOutMultiplier;

					$ret = $this->roundNumber($fee, $this->operation['currency']);

				}
			} else {
				$fee = $this->operation['amount'] * $this->cashOutMultiplier;

				$ret = $this->exchange->convert($fee, $this->operation['currency'], 'EUR');
				$ret = $this->roundNumber($ret, $this->operation['currency']);
			}

			return $ret;
		}

		private function legalPersonFees()
		{
			$fee = $this->operation['amount'] * $this->cashOutMultiplier;

			$ret = $this->exchange->convert($fee, $this->operation['currency'], 'EUR');
			$ret = $this->roundNumber($ret, $this->operation['currency']);
			$ret = ($ret > $this->cashOutLegalFeeMin) ? $ret : $this->cashOutLegalFeeMin;
			$ret = $this->exchange->convert($ret, 'EUR', $this->operation['currency']);
			$ret = $this->roundNumber($ret, $this->operation['currency']);
			return $ret;
		}

		private function getWeakCashOuts($date): array
		{
			$ret = [];
			$weakDay = date('N', strtotime($date));
			if ($weakDay > 1) {
				$dif = $weakDay - 1;
				$monday = date('Y-m-d', strtotime('-' . $dif . 'day', strtotime($date)));
			} else {
				$monday = $date;
			}
			foreach ($this->userOperations as $operation) {
				if ($operation['date'] >= $monday && $operation['date'] <= $date && $operation['operation_type'] === 'cash_out') {
					$ret[] = $this->exchange->convert($operation['amount'], $operation['currency'], $this->operation['currency']);
				}
			}

			return $ret;
		}
	}