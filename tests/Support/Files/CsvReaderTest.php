<?php

	declare(strict_types=1);

	namespace Tests\Support\Files;


	use App\Support\Files\CsvReader;
	use PHPUnit\Framework\TestCase;

	class CsvReaderTest extends TestCase
	{
		/** @test */
		public function openFile_method_first_varable_must_have_csv_file_format()
		{
			$filename = 'file.txt';
			$csv = new CsvReader();
			$this->assertEquals('File format must be ".csv"', $csv->openFile($filename));
		}

		/** @test */
		public function if_file_not_found_return_error_message()
		{
			$filename = 'file.csv';
			$csv = new CsvReader();
			$this->assertEquals(['error' => 'File not found'], $csv->openFile($filename));
		}

		/** @test */
		public function then_file_is_opened_return_array_with_file_content()
		{
			$filename = 'input.csv';
			$csv = new CsvReader();
			$data = $csv->openFile($filename)->getContent();
			$output = [
				0 => [
					0 => "2014-12-31",
					1 => "4",
					2 => "natural",
					3 => "cash_out",
					4 => "1200.00",
					5 => "EUR",
				],
				1 => [
					0 => "2015-01-01",
					1 => "4",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				2 => [
					0 => "2016-01-05",
					1 => "4",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				3 => [
					0 => "2016-01-05",
					1 => "1",
					2 => "natural",
					3 => "cash_in",
					4 => "200.00",
					5 => "EUR",
				],
				4 => [
					0 => "2016-01-06",
					1 => "2",
					2 => "legal",
					3 => "cash_out",
					4 => "300.00",
					5 => "EUR",
				],
				5 => [
					0 => "2016-01-06",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "30000",
					5 => "JPY",
				],
				6 => [
					0 => "2016-01-07",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				7 => [
					0 => "2016-01-07",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "100.00",
					5 => "USD",
				],
				8 => [
					0 => "2016-01-10",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "100.00",
					5 => "EUR",
				],
				9 => [
					0 => "2016-01-10",
					1 => "2",
					2 => "legal",
					3 => "cash_in",
					4 => "1000000.00",
					5 => "EUR",
				],
				10 => [
					0 => "2016-01-10",
					1 => "3",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				11 => [
					0 => "2016-02-15",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "300.00",
					5 => "EUR",
				],
				12 => [
					0 => "2016-02-19",
					1 => "5",
					2 => "natural",
					3 => "cash_out",
					4 => "3000000",
					5 => "JPY",
				],
			];
			$this->assertEquals($output, $data);
		}

		/** @test */
		public function then_creating_array_of_file_content_add_number_of_row()
		{
			$filename = 'input.csv';
			$csv = new CsvReader();
			$data = $csv->openFile($filename)->getContent(true);
			$output = [
				0 => [
					'row_id' => 0,
					0 => "2014-12-31",
					1 => "4",
					2 => "natural",
					3 => "cash_out",
					4 => "1200.00",
					5 => "EUR",
				],
				1 => [
					'row_id' => 1,
					0 => "2015-01-01",
					1 => "4",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				2 => [
					'row_id' => 2,
					0 => "2016-01-05",
					1 => "4",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				3 => [
					'row_id' => 3,
					0 => "2016-01-05",
					1 => "1",
					2 => "natural",
					3 => "cash_in",
					4 => "200.00",
					5 => "EUR",
				],
				4 => [
					'row_id' => 4,
					0 => "2016-01-06",
					1 => "2",
					2 => "legal",
					3 => "cash_out",
					4 => "300.00",
					5 => "EUR",
				],
				5 => [
					'row_id' => 5,
					0 => "2016-01-06",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "30000",
					5 => "JPY",
				],
				6 => [
					'row_id' => 6,
					0 => "2016-01-07",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				7 => [
					'row_id' => 7,
					0 => "2016-01-07",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "100.00",
					5 => "USD",
				],
				8 => [
					'row_id' => 8,
					0 => "2016-01-10",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "100.00",
					5 => "EUR",
				],
				9 => [
					'row_id' => 9,
					0 => "2016-01-10",
					1 => "2",
					2 => "legal",
					3 => "cash_in",
					4 => "1000000.00",
					5 => "EUR",
				],
				10 => [
					'row_id' => 10,
					0 => "2016-01-10",
					1 => "3",
					2 => "natural",
					3 => "cash_out",
					4 => "1000.00",
					5 => "EUR",
				],
				11 => [
					'row_id' => 11,
					0 => "2016-02-15",
					1 => "1",
					2 => "natural",
					3 => "cash_out",
					4 => "300.00",
					5 => "EUR",
				],
				12 => [
					'row_id' => 12,
					0 => "2016-02-19",
					1 => "5",
					2 => "natural",
					3 => "cash_out",
					4 => "3000000",
					5 => "JPY",
				],
			];
			$this->assertEquals($output, $data);
		}


		/** @test */
		public function can_set_indexes_before_getting_getting_content()
		{
			$filename = 'input.csv';
			$csv = new CsvReader();

			$indexes = ['date', 'user_id', 'user_type', 'operation_type', 'amount', 'currency'];
			$data = $csv->openFile($filename)->setIndexes($indexes)->getContent(true);

			$output = [
				'row_id' => 0,
				'date' => "2014-12-31",
				'user_id' => "4",
				'user_type' => "natural",
				'operation_type' => "cash_out",
				'amount' => "1200.00",
				'currency' => "EUR",
			];

			$this->assertEquals($output, $data[0]);
		}
	}