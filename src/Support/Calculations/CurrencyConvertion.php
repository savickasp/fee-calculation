<?php


	namespace App\Support\Calculations;


	class CurrencyConvertion
	{
		private	$exchange = [
			'EUR' => [
				'USD' => 1.1497,
				'JPY' => 129.53,
			],
		];


		public function convert(int $amount, string $from, string $to)
		{
			if (array_key_exists($from, $this->exchange)){
				if (array_key_exists($to, $this->exchange[$from])){
					$ret = $amount * $this->exchange[$from][$to];
				} else {
					$ret = 'cant convert to that currency';
				}
			} else {
				foreach ($this->exchange as $key => $array) {
					if ($key === $to && array_key_exists($from, $array)) {
						$ret = round($amount / $this->exchange[$to][$from], 6);
						break;
					}
					$ret = 'cant convert to that currency';
				}
			}

			return $ret;
		}
	}