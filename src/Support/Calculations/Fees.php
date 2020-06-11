<?php

    declare(strict_types=1);

namespace App\Support\Calculations;

    class Fees
    {
        private $defaultCurrency = 'EUR';
        private $exchange;
        private $currenciesBySmallestDigit;
        private $cashInFeePercent;
        private $cashInMaxSumDefaultCurrency;
        private $cashOutNaturalFeePercent;
        private $cashOutNaturalFreeOfChargesSumDefaultCurrency;
        private $cashOutNaturalFreeOperations;
        private $cashOutLegalFeePercent;
        private $cashOutLegalMinSumDefaultCurrency;

        /**
         * Fees constructor.
         * creates instance of CurrencyConvertion class.
         * sets calculation Rules from config.
         */
        public function __construct()
        {
            $this->exchange = new CurrencyConvertion();
            $this->currenciesBySmallestDigit = include 'config/currenciesBySmallestDigits.php';

            $calculationRules = include 'config/feeCalculation.php';

            // setting these rules as parameters because if array was changed then all come must be revieved instead of constructor
            $this->cashInFeePercent = $calculationRules['cashIn']['feePercent'];
            $this->cashInMaxSumDefaultCurrency = $calculationRules['cashIn']['maxSumEur'];
            $this->cashOutNaturalFeePercent = $calculationRules['cashOut']['natural']['feePercent'];
            $this->cashOutNaturalFreeOfChargesSumDefaultCurrency = $calculationRules['cashOut']['natural']['freeOfChargeSumPerWeakEur'];
            $this->cashOutNaturalFreeOperations = $calculationRules['cashOut']['natural']['freeOperationNumber'];
            $this->cashOutLegalFeePercent = $calculationRules['cashOut']['legal']['feePercent'];
            $this->cashOutLegalMinSumDefaultCurrency = $calculationRules['cashOut']['legal']['minSumEur'];
        }

        public function getOperationFee(array $operation, array $previousOperations)
        {
            if ($operation['operation_type'] === 'cash_in') {
                $fee = $this->getCashInFee($operation);
            } elseif ($operation['operation_type'] === 'cash_out') {
                if ($operation['user_type'] === 'legal') {
                    $fee = $this->getLegalCashOutFee($operation);
                } elseif ($operation['user_type'] === 'natural') {
                    $weakCashouts = $this->getWeakOperations($operation['date'], $previousOperations, 'cash_out');
                    $fee = $this->getNaturalCashOutFee($operation, $weakCashouts);
                } else {
                    die('unknown user type');
                }
            } else {
                die('unknown operation');
            }

            return $this->roundNumber($fee, $operation['currency']);
        }

        // private

        /**
         * @return float|int
         */
        private function getLegalCashOutFee(array $operation)
        {
            // if operation isnt in default currency, then convert max fee limit to operation currency
            if ($operation['currency'] === $this->defaultCurrency) {
                $feeMinLimitOperationCurrency = $this->cashOutLegalMinSumDefaultCurrency;
            } else {
                $feeMinLimitOperationCurrency = $this->exchange->convert($this->cashOutLegalMinSumDefaultCurrency,
                    $this->defaultCurrency,
                    $operation['currency']);
            }

            $fee = $operation['amount'] * $this->cashOutLegalFeePercent / 100;

            // returns value of $fee if $fee is heighter then mininum operation fee, else ir return minimum operation fee value
            return ($fee > $feeMinLimitOperationCurrency) ? $fee : $feeMinLimitOperationCurrency;
        }

        /**
         * @return false|float|int
         */
        private function getNaturalCashOutFee(array $operation, array $weakCashouts)
        {
            $sumOverLimitsDefaultCurrency = 0;
            $weakCashoutSumDefaultCurrency = 0;

            // convert all cash outs to default currency and sum them all
            foreach ($weakCashouts as $cashout) {
                if ($cashout['currency'] !== $this->defaultCurrency) {
                    $weakCashoutSumDefaultCurrency += $this->exchange->convert($cashout['amount'],
                        $cashout['currency'],
                        $this->defaultCurrency);
                } else {
                    $weakCashoutSumDefaultCurrency += $cashout['amount'];
                }
            }

            // get current operation amount in default currency
            $operationDefaultCurrency = $this->exchange->convert($operation['amount'],
                $operation['currency'],
                $this->defaultCurrency);

            $weakCashoutSumDefaultCurrency += $operationDefaultCurrency;

            // checks if operation count per period isnt higher, else if adds current operation amount to $sumOverLimitsDefaultCurrency
            if (count($weakCashouts) + 1 > $this->cashOutNaturalFreeOperations) {
                $sumOverLimitsDefaultCurrency = $operation['amount'];

            // checks if all weak operation smount sum plius current operation isnt higher then limit
            } elseif ($weakCashoutSumDefaultCurrency > $this->cashOutNaturalFreeOfChargesSumDefaultCurrency) {
                $difference = $weakCashoutSumDefaultCurrency - $this->cashOutNaturalFreeOfChargesSumDefaultCurrency;
                $sumOverLimitsDefaultCurrency = ($difference < $operationDefaultCurrency) ? $difference : $operationDefaultCurrency;
            }

            // sum what is needed to calculate fee is converted back to operation surrancy
            $sumOverLimitsOperationCurrency = $this->exchange->convert($sumOverLimitsDefaultCurrency,
                $this->defaultCurrency,
                $operation['currency']);

            // fee calculated
            $fee = $sumOverLimitsOperationCurrency * $this->cashOutNaturalFeePercent / 100;

            return $this->roundNumber($fee, $operation['currency']);
        }

        /**
         * @param $operation
         *
         * @return false|float|int
         */
        private function getCashInFee($operation)
        {
            // if operation isnt in default currency, then convert max fee limit to operation currency
            if ($operation['currency'] === $this->defaultCurrency) {
                $feeMaxLimitOperationCurrency = $this->cashInMaxSumDefaultCurrency;
            } else {
                $feeMaxLimitOperationCurrency = $this->exchange->convert($this->cashInMaxSumDefaultCurrency,
                    $this->defaultCurrency,
                    $operation['currency']);
            }

            $fee = $operation['amount'] * $this->cashInFeePercent / 100;

            // check if fee isnt over the max fee limit
            $fee = ($fee > $feeMaxLimitOperationCurrency) ? $feeMaxLimitOperationCurrency : $fee;

            return $this->roundNumber($fee, $operation['currency']);
        }

        /**
         * @return false|float|int
         */
        private function roundNumber(float $fee, string $currency)
        {
            if (in_array($currency, $this->currenciesBySmallestDigit['1'], true)) {
                $rounded = ceil($fee);
            } elseif (in_array($currency, $this->currenciesBySmallestDigit['0.01'], true)) {
                $rounded = ceil($fee * 100) / 100;
            }

            return $rounded;
        }

        /**
         * @param null $filterOperationType
         */
        private function getWeakOperations(string $date, array $previousOperations, $filterOperationType = null): array
        {
            $weakOperations = [];
            $weakDay = date('N', strtotime($date));

            // calculate chich day is monday
            if ($weakDay > 1) {
                $difference = $weakDay - 1;
                $monday = date('Y-m-d', strtotime('-'.$difference.'day', strtotime($date)));
            } else {
                $monday = $date;
            }

            // filter throw operations to get all oprations from monday to weakday( current operation day)
            foreach ($previousOperations as $operation) {
                if ($operation['date'] >= $monday && $operation['date'] <= $date) {
                    if ($filterOperationType ? $filterOperationType === $operation['operation_type'] : true) {
                        $weakOperations[] = $operation;
                    }
                }
            }

            return $weakOperations;
        }
    }
