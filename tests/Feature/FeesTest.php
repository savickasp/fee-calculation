<?php

declare(strict_types=1);

namespace Tests\Unit;


use App\Support\Calculations\Fees;
use PHPUnit\Framework\TestCase;

	class FeesTest extends TestCase
	{
		private $fees;

		public function setUp()
		{
			parent::setUp();
			$this->fees = new Fees();
		}

		// this 2 methods below are same
		// but because this is test I think it should be more comfortable to use
		// I devided into 2 functons

		/**
		 * @test
		 * @dataProvider data_provider_calculate_commision_fee_for_cash_in
		 */
		public function calculate_commision_fee_for_cash_in(array $operation, array $userOperations, $expected)
		{
			$returned = $this->fees->calculateFee($operation, $userOperations, true);

			$this->assertEquals($expected, $returned);
		}

		/**
		 * @test
		 * @dataProvider data_provider_calculate_commision_fee_test_example_operations
		 */
		public function calculate_commision_fee_test_example_operations(array $operation, array $userOperations, $expected)
		{
			$returned = $this->fees->calculateFee($operation, $userOperations, true);

			$this->assertEquals($expected, $returned);
		}

		public function data_provider_calculate_commision_fee_for_cash_in()
		{
			return [
				'cash_in test type' => [
					[
						"row_id" => 0,
						"date" => "2014-12-31",
						"user_id" => 4,
						"user_type" => "natural",
						"operation_type" => "operacija",
						"amount" => 10.00,
						"currency" => "EUR",
					],
					[],
					'unknown operation',
				],
				'cash_in fee calc_1' => [
					[
						"operation_type" => "cash_in",
						"amount" => 10.00,
						"currency" => "EUR",
					],
					[],
					0.01,
				],
				'cash_in fee calc_2' => [
					[
						"operation_type" => "cash_in",
						"amount" => 100.00,
						"currency" => "EUR",
					],
					[],
					0.03,
				],
				'cash_in fee calc_3' => [
					[
						"operation_type" => "cash_in",
						"amount" => 17000.00,
						"currency" => "EUR",
					],
					[],
					5,
				],
				'cash_in fee calc_4' => [
					[
						"operation_type" => "cash_in",
						"amount" => 500.00,
						"currency" => "USD",
					],
					[],
					0.15,
				],
				'cash_in fee calc_5' => [
					[
						"operation_type" => "cash_in",
						"amount" => 500.00,
						"currency" => "USD",
					],
					[
						[
							"row_id" => 0,
							"date" => "2014-12-31",
							"operation_type" => "cash_out",
							"amount" => 1200.00,
							"currency" => "EUR",
							"fee" => 0,
						]
					],
					0.15,
				],
			];
		}

		public function data_provider_calculate_commision_fee_test_example_operations()
		{
			return [
				'first opp' => [
					[
						"row_id" => 0,
						"date" => "2014-12-31",
						"user_id" => 4,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 1200.00,
						"currency" => "EUR",
					],
					[],
					0.60,
				],
				'second opp' => [
					[
						"row_id" => 1,
						"date" => "2015-01-01",
						"user_id" => 4,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 1000.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 0,
							"date" => "2014-12-31",
							"operation_type" => "cash_out",
							"amount" => 1200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
					],
					3.00,
				],
				'third opp' => [
					[
						"row_id" => 2,
						"date" => "2016-01-05",
						"user_id" => 4,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 1000.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 0,
							"date" => "2014-12-31",
							"operation_type" => "cash_out",
							"amount" => 1200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 1,
							"date" => "2015-01-01",
							"operation_type" => "cash_out",
							"amount" => 1000.00,
							"currency" => "EUR",
							"fee" => 5,
						],
					],
					0.00,
				],
				'fourth opp' => [
					[
						"row_id" => 3,
						"date" => "2016-01-05",
						"user_id" => 1,
						"user_type" => 'natural',
						"operation_type" => "cash_in",
						"amount" => 200.00,
						"currency" => "EUR",
					],
					[],
					0.06,
				],
				'fifth opp' => [
					[
						"row_id" => 4,
						"date" => "2016-01-06",
						"user_id" => 2,
						"user_type" => 'legal',
						"operation_type" => "cash_out",
						"amount" => 300.00,
						"currency" => "EUR",
					],
					[],
					0.90,
				],
				'sixth opp' => [
					[
						"row_id" => 5,
						"date" => "2016-01-06",
						"user_id" => 1,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 30000,
						"currency" => "JPY",
					],
					[
						[
							"row_id" => 3,
							"date" => "2016-01-05",
							"operation_type" => "cash_in",
							"amount" => 200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
					],
					0.00,
				],
				'seventh opp' => [
					[
						"row_id" => 6,
						"date" => "2016-01-07",
						"user_id" => 1,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 1000.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 3,
							"date" => "2016-01-05",
							"operation_type" => "cash_in",
							"amount" => 200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 5,
							"date" => "2016-01-06",
							"operation_type" => "cash_out",
							"amount" => 30000,
							"currency" => "JPY",
							"fee" => 5,
						],
					],
					0.70,
				],
				'eighth opp' => [
					[
						"row_id" => 7,
						"date" => "2016-01-07",
						"user_id" => 1,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 100.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 3,
							"date" => "2016-01-05",
							"operation_type" => "cash_in",
							"amount" => 200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 5,
							"date" => "2016-01-06",
							"operation_type" => "cash_out",
							"amount" => 30000,
							"currency" => "JPY",
							"fee" => 5,
						],
						[
							"row_id" => 6,
							"date" => "2016-01-07",
							"operation_type" => "cash_out",
							"amount" => 1000.00,
							"currency" => "EUR",
							"fee" => 5,
						],
					],
					0.30,
				],
				'ninth opp' => [
					[
						"row_id" => 8,
						"date" => "2016-01-10",
						"user_id" => 1,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 100.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 3,
							"date" => "2016-01-05",
							"operation_type" => "cash_in",
							"amount" => 200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 5,
							"date" => "2016-01-06",
							"operation_type" => "cash_out",
							"amount" => 30000,
							"currency" => "JPY",
							"fee" => 5,
						],
						[
							"row_id" => 6,
							"date" => "2016-01-07",
							"operation_type" => "cash_out",
							"amount" => 1000.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 7,
							"date" => "2016-01-07",
							"operation_type" => "cash_out",
							"amount" => 100.00,
							"currency" => "USD",
							"fee" => 5,
						],
					],
					0.30,
				],
				'tenth opp' => [
					[
						"row_id" => 9,
						"date" => "2016-01-10",
						"user_id" => 2,
						"user_type" => 'legal',
						"operation_type" => "cash_in",
						"amount" => 1000000.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 4,
							"date" => "2016-01-06",
							"operation_type" => "cash_out",
							"amount" => 300.00,
							"currency" => "EUR",
							"fee" => 5,
						],
					],
					5.00,
				],
				'eleventh opp' => [
					[
						"row_id" => 10,
						"date" => "2016-01-10",
						"user_id" => 3,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 1000.00,
						"currency" => "EUR",
					],
					[],
					0.00,
				],
				'twelfth opp' => [
					[
						"row_id" => 11,
						"date" => "2016-02-15",
						"user_id" => 1,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 300.00,
						"currency" => "EUR",
					],
					[
						[
							"row_id" => 3,
							"date" => "2016-01-05",
							"operation_type" => "cash_in",
							"amount" => 200.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 5,
							"date" => "2016-01-06",
							"operation_type" => "cash_out",
							"amount" => "30000",
							"currency" => "JPY",
							"fee" => 5,
						],
						[
							"row_id" => 6,
							"date" => "2016-01-07",
							"operation_type" => "cash_out",
							"amount" => 1000.00,
							"currency" => "EUR",
							"fee" => 5,
						],
						[
							"row_id" => 7,
							"date" => "2016-01-07",
							"operation_type" => "cash_out",
							"amount" => 100.00,
							"currency" => "USD",
							"fee" => 5,
						],
						[
							"row_id" => 8,
							"date" => "2016-01-10",
							"operation_type" => "cash_out",
							"amount" => 100.00,
							"currency" => "EUR",
							"fee" => 5,
						],
					],
					0.00,
				],
				'thirteenth opp' => [
					[
						"row_id" => 12,
						"date" => "2016-02-19",
						"user_id" => 5,
						"user_type" => 'natural',
						"operation_type" => "cash_out",
						"amount" => 3000000,
						"currency" => "JPY",
					],
					[],
					8612,
				],

			];
		}
	}