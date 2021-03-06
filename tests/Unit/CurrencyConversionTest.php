<?php

	declare(strict_types=1);

	namespace Tests\Unit;

	use App\Support\Calculations\CurrencyConvertion;
	use PHPUnit\Framework\TestCase;

	class CurrencyConversionTest extends TestCase
	{
		/**
		 * @test
		 * @dataProvider data_provider_curency_convertion_is_working
		 */
		public function curency_convertion_is_working(float $amount, string $from, string $to, $expected)
		{
			$exchange = new CurrencyConvertion();
			$returned = $exchange->convert($amount, $from, $to);
			$this->assertEquals($expected, $returned);
		}

		public function data_provider_curency_convertion_is_working(): array
		{
			return [
				[
					1,
					'EUR',
					'USD',
					1.1497,
				],
				[
					1,
					'EUR',
					'JPY',
					129.53,
				],
				[
					1,
					'EUR',
					'XXX',
					'Currency conversion rate not found',
				],
				[
					1,
					'USD',
					'EUR',
					0.869792,
				],
				[
					1,
					'JPY',
					'EUR',
					0.007720,
				],
				[
					1,
					'JPY',
					'DCS',
					'Currency conversion rate not found',
				],
				[
					1,
					'EUR',
					'EUR',
					1,
				],
				[
					1,
					'EUR',
					'GBP',
					0.9,
				],
			];
		}
	}
