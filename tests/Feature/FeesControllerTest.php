<?php


	namespace Tests\Feature;


	use App\FeesController;
	use PHPUnit\Framework\TestCase;

	class FeesControllerTest extends TestCase
	{
		/** @test */
		public function expect_error_then_reading_file_with_bad_filename_or_extension()
		{
			$app = new FeesController();

			$error = $app->getOperations('file.txt');
			$this->assertEquals('File format must be ".csv"', $error['error']);

			$error = $app->getOperations('file.csv');
			$this->assertEquals('File not found', $error['error']);
		}

		/** @test */
		public function compare_getOperations_method_result()
		{
			$output = [
				"row_id" => 0,
				"date" => "2014-12-31",
				"user_id" => "4",
				"user_type" => "natural",
				"operation_type" => "cash_out",
				"amount" => "1200.00",
				"currency" => "EUR",
			];


			$app = new FeesController();
			$operations = $app->getOperations('input.csv', true);

			$this->assertEquals($output, $operations[0]);
		}

		/** @test */
		public function get_all_fees_and_compare_result()
		{
			$expected = [0.60, 3.00, 0.00, 0.06, 0.90, 0.00, 0.70, 0.30, 0.30, 5.00, 0.00, 0.00, 8612,];
			$app = new FeesController();
			$app->getOperations('input.csv');
			$output = $app->calculateFees();

			$this->assertEquals($expected, $output);
		}

//		/** @test */
//		public function after_calculation_of_operation_fee_all_oprations_are_asignned_to_user()
//		{
//			$output = [
//				4 => [
//					"user_id" => "4",
//					"user_type" => "natural",
//					"operations" => [
//						0 => [
//							"row_id" => 0,
//							"date" => "2014-12-31",
//							"operation_type" => "cash_out",
//							"amount" => "1200.00",
//							"currency" => "EUR",
//							"fee" => 0.60,
//						],
//						1 => [
//							"row_id" => 1,
//							"date" => "2015-01-01",
//							"operation_type" => "cash_out",
//							"amount" => "1000.00",
//							"currency" => "EUR",
//							"fee" => 3.00,
//						],
//						2 => [
//							"row_id" => 2,
//							"date" => "2016-01-05",
//							"operation_type" => "cash_out",
//							"amount" => "1000.00",
//							"currency" => "EUR",
//							"fee" => 0.00,
//						],
//					],
//				],
//				1 => [
//					"user_id" => "1",
//					"user_type" => "natural",
//					"operations" => [
//						0 => [
//							"row_id" => 3,
//							"date" => "2016-01-05",
//							"operation_type" => "cash_in",
//							"amount" => "200.00",
//							"currency" => "EUR",
//							"fee" => 0.06,
//						],
//						1 => [
//							"row_id" => 5,
//							"date" => "2016-01-06",
//							"operation_type" => "cash_out",
//							"amount" => "30000",
//							"currency" => "JPY",
//							"fee" => 0.00,
//						],
//						2 => [
//							"row_id" => 6,
//							"date" => "2016-01-07",
//							"operation_type" => "cash_out",
//							"amount" => "1000.00",
//							"currency" => "EUR",
//							"fee" => 0.70,
//						],
//						3 => [
//							"row_id" => 7,
//							"date" => "2016-01-07",
//							"operation_type" => "cash_out",
//							"amount" => "100.00",
//							"currency" => "USD",
//							"fee" => 0.30,
//						],
//						4 => [
//							"row_id" => 8,
//							"date" => "2016-01-10",
//							"operation_type" => "cash_out",
//							"amount" => "100.00",
//							"currency" => "EUR",
//							"fee" => 0.30,
//						],
//						5 => [
//							"row_id" => 11,
//							"date" => "2016-02-15",
//							"operation_type" => "cash_out",
//							"amount" => "300.00",
//							"currency" => "EUR",
//							"fee" => 0.00,
//						],
//					],
//				],
//				2 => [
//					"user_id" => "2",
//					"user_type" => "legal",
//					"operations" => [
//						0 => [
//							"row_id" => 4,
//							"date" => "2016-01-06",
//							"operation_type" => "cash_out",
//							"amount" => "300.00",
//							"currency" => "EUR",
//							"fee" => 0.90,
//						],
//						1 => [
//							"row_id" => 9,
//							"date" => "2016-01-10",
//							"operation_type" => "cash_in",
//							"amount" => "1000000.00",
//							"currency" => "EUR",
//							"fee" => 5.00,
//						],
//					],
//				],
//				3 => [
//					"user_id" => "3",
//					"user_type" => "natural",
//					"operations" => [
//						0 => [
//							"row_id" => 10,
//							"date" => "2016-01-10",
//							"operation_type" => "cash_out",
//							"amount" => "1000.00",
//							"currency" => "EUR",
//							"fee" => 0.00,
//						],
//					],
//				],
//				5 => [
//					"user_id" => "5",
//					"user_type" => "natural",
//					"operations" => [
//						0 => [
//							"row_id" => 12,
//							"date" => "2016-02-19",
//							"operation_type" => "cash_out",
//							"amount" => "3000000",
//							"currency" => "JPY",
//							"fee" => 8612,
//						],
//					],
//				],
//			];
//
//			$app = new FeesController();
//			$app->getOperations('input.csv');
//			$users = $app->calculateFees();
//
//			$this->assertEquals($output, $users);
//		}

	}