<?php


	namespace App\Support\Calculations;


	class CurrencyConvertion
	{
		/**
		 * @var array
		 * first indexes is primary currency
		 * second indexes is secondary currency
		 * primary currency amount is 1 and it is equal to secondary currency index value
		 * e.g. 1 EUR = 1.1497 USD
		 */
		private $exchange = [
			'EUR' => [
				'USD' => 1.1497,
				'JPY' => 129.53,
			],
		];

		/**
		 * @param int $amount
		 * @param string $from
		 * @param string $to
		 * @return false|float|int|string
		 *
		 * This method if currency convertion rate is set.
		 * Currently cant convert USD to JPY
		 */
		public function convert(float $amount, string $from, string $to)
		{
			if ($from === $to) {
				$ret = $amount;
			} elseif (array_key_exists($from, $this->exchange)) {
				$ret = $this->convertPrimaryToSecondary($amount, $from, $to);
			} elseif (array_key_exists($to, $this->exchange)) {
				$ret = $this->convertSecondaryToPrimary($amount, $from, $to);
			} else {
				$ret = $this->convertThrowPrimary($amount, $from, $to);
			}

			return $ret;
		}

		private function convertPrimaryToSecondary(float $amount, string $from, string $to)
		{
			if (isset($this->exchange[$from][$to])) {
				$ret = round($amount * $this->exchange[$from][$to], 6);
			} else {
				$ret = 'cant convert to that currency';
			}

			return $ret;
		}

		private function convertSecondaryToPrimary(float $amount, string $from, string $to)
		{
			if (isset($this->exchange[$to][$from])) {
				$ret = round($amount / $this->exchange[$to][$from], 6);
			} else {
				$ret = 'cant convert to that currency';
			}

			return $ret;
		}

		private function convertThrowPrimary(float $amount, string $from, string $to)
		{
			foreach ($this->exchange as $primary => $secArray) {
				if (array_key_exists($from, $secArray) && array_key_exists($to, $secArray)) {
					$amount = $this->convertSecondaryToPrimary($amount, $from, $primary);
					$ret = $this->convertPrimaryToSecondary($amount, $primary, $to);
					break;
				}
				$ret = 'cant convert to that currency';
			}

			return $ret;
		}
	}