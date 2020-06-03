<?php

declare(strict_types=1);

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

        /**
         * Fees constructor.
         * creates instance of CurrencyConvertion class.
         */
        public function __construct()
        {
            $this->exchange = new CurrencyConvertion();
        }

        /**
         * @param bool $returnOnlyFee // Use it in test to check if fee was calculated correcly
         *
         * @return array|false|float|int|string
         *                                      it sets protedted parameters to reach them loacally in class
         *                                      it determens if it is cash_out or cash_in and use other methdos to calculate
         */
        public function calculateFee(array $operation, array $userOperations, bool $returnOnlyFee = false)
        {
            $this->operation = $operation;
            $this->userOperations = $userOperations;

            if ($this->operation['operation_type'] === 'cash_in') {
                $ret = $this->getCashInFee();
            } elseif ($this->operation['operation_type'] === 'cash_out') {
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

        // private methods

        /**
         * @return false|float|int
         *                         calculated cash_in fee and use other method to check if fee isnt over the limit
         */
        private function getCashInFee()
        {
            $fee = $this->cashInFeeMultiplier * $this->operation['amount'];

            return $this->roundOrLimitFee($fee);
        }

        /**
         * @return false|float|int|string
         *                                it determines by user_type what method and rules to apply
         */
        private function getCashOutFee()
        {
            if ($this->operation['user_type'] === 'legal') {
                $ret = $this->legalPersonFees();
            } elseif ($this->operation['user_type'] === 'natural') {
                $ret = $this->naturalPersonFees();
            } else {
                $ret = 'user type not found';
            }

            return $ret;
        }

        /**
         * @return false|float|int
         *                         if fee is over limit it return limit if not it rounds up and returns rounded fee
         */
        private function roundOrLimitFee(float $fee)
        {
            if ($fee > $this->cashInLimitEur) {
                $ret = $this->cashInLimitEur;
            } else {
                $ret = $this->roundNumber($fee, $this->operation['currency']);
            }

            return $ret;
        }

        /**
         * @return false|float|int
         *                         Rounding rules by their currency
         */
        private function roundNumber(float $fee, string $currency)
        {
            if ($currency === 'JPY') {
                $ret = ceil($fee);
            } elseif ($currency === 'EUR' || $currency === 'USD') {
                $ret = ceil($fee * 100) / 100;
            }

            return $ret;
        }

        /**
         * @return false|float|int|strings
         *                                 mehtod calculates fee for natural persosn cash_out
         */
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
                    $ret = (float) 0.00;
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

        /**
         * @return false|float|int|string
         *                                method calculates fee for natural persons cash_out
         */
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

        /**
         * @param $date
         *
         * @return array
         *               method goes throw all users operations and return array with only those operations which was made in single weak from monday
         *               amount is converted to current operation currency
         */
        private function getWeakCashOuts($date): array
        {
            $ret = [];
            $weakDay = date('N', strtotime($date));
            if ($weakDay > 1) {
                $dif = $weakDay - 1;
                $monday = date('Y-m-d', strtotime('-'.$dif.'day', strtotime($date)));
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
