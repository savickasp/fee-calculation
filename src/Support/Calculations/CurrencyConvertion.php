<?php

	declare(strict_types=1);

	namespace App\Support\Calculations;

	class CurrencyConvertion
	{
		/**
		 * @var array
		 *            constructor sets rates from config file
		 *
		 * @example [
		 *    'EUR' => [
		 *         'USD' => 1.1497,
		 *    ]
		 * ]
		 */
		private $rates;

		public function __construct()
		{
			$this->rates = include 'config/currencyConvertionRates.php';
		}

		/**
		 * @return float|string
		 *                      method check if rates parameter has value set from config file. if it has rate ir converts @amount and returns @converted
		 *                      if rate not fount it check if currency given in @from and @to are same and return @amount
		 *                      else method returns string 'Currency conversion rate not found'
		 * s         */
		public function convert(float $amount, string $from, string $to)
		{
			if (isset($this->rates[$from][$to])) {
				$converted = round($amount * $this->rates[$from][$to], 6);
			} elseif (isset($this->rates[$to][$from])) {
				$converted = round($amount / $this->rates[$to][$from], 6);
			} elseif ($from === $to) {
				return $amount;
			} else {
				return 'Currency conversion rate not found';
			}

			return $converted;
		}
	}
