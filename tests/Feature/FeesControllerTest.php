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
			$operations = $app->getOperations('input.csv');

			$this->assertEquals($output, $operations[0]);
		}

	}