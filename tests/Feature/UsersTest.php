<?php

declare(strict_types=1);

namespace Tests\Feature;


use App\Model\Users;
use App\Support\Files\CsvReader;
use PHPUnit\Framework\TestCase;

	class UsersTest extends TestCase
	{
		/**
		 * @test
		 * @dataProvider  dataProvider_add_operation_to_user
		 */
		public function add_operation_to_user(array $output, string $file, array $indexes)
		{
			$csv = new CsvReader();
			$csv->openFile($file);

			$operations = $csv->setIndexes($indexes)->getContent(true);

			$users = new Users();
			$this->assertEquals([], $users->all());
			foreach ($operations as $operation) {
				$users->addOperation($operation + ['fee' => 5]);
			}

			$this->assertEquals($output, $users->all());
		}

		/**
		 * @test
		 * @dataProvider dataProvider_get_only_fees_by_row_order
		 */
		public function get_only_fees_by_row_order(array $output, string $file, array $indexes)
		{

			$csv = new CsvReader();
			$csv->openFile($file);

			$operations = $csv->setIndexes($indexes)->getContent(true);

			$users = new Users();
			$this->assertEquals([], $users->all());
			$i = 10;
			foreach ($operations as $operation) {
				$users->addOperation($operation + ['fee' => $i++]);
			}

			$this->assertEquals($output, $users->getFeesOrderByRow());
		}

		public function dataProvider_add_operation_to_user()
		{
			return [
				[
					[
						4 => [
							"user_id" => 4,
							"user_type" => "natural",
							"operations" => [
								0 => [
									"row_id" => 0,
									"date" => "2014-12-31",
									"operation_type" => "cash_out",
									"amount" => 1200.00,
									"currency" => "EUR",
									"fee" => 5,
								],
								1 => [
									"row_id" => 1,
									"date" => "2015-01-01",
									"operation_type" => "cash_out",
									"amount" => 1000.00,
									"currency" => "EUR",
									"fee" => 5,
								],
								2 => [
									"row_id" => 2,
									"date" => "2016-01-05",
									"operation_type" => "cash_out",
									"amount" => 1000.00,
									"currency" => "EUR",
									"fee" => 5,
								],
							],
						],
						1 => [
							"user_id" => 1,
							"user_type" => "natural",
							"operations" => [
								0 => [
									"row_id" => 3,
									"date" => "2016-01-05",
									"operation_type" => "cash_in",
									"amount" => 200.00,
									"currency" => "EUR",
									"fee" => 5,
								],
								1 => [
									"row_id" => 5,
									"date" => "2016-01-06",
									"operation_type" => "cash_out",
									"amount" => 30000,
									"currency" => "JPY",
									"fee" => 5,
								],
								2 => [
									"row_id" => 6,
									"date" => "2016-01-07",
									"operation_type" => "cash_out",
									"amount" => 1000.00,
									"currency" => "EUR",
									"fee" => 5,
								],
								3 => [
									"row_id" => 7,
									"date" => "2016-01-07",
									"operation_type" => "cash_out",
									"amount" => 100.00,
									"currency" => "USD",
									"fee" => 5,
								],
								4 => [
									"row_id" => 8,
									"date" => "2016-01-10",
									"operation_type" => "cash_out",
									"amount" => 100.00,
									"currency" => "EUR",
									"fee" => 5,
								],
								5 => [
									"row_id" => 11,
									"date" => "2016-02-15",
									"operation_type" => "cash_out",
									"amount" => 300.00,
									"currency" => "EUR",
									"fee" => 5,
								],
							],
						],
						2 => [
							"user_id" => 2,
							"user_type" => "legal",
							"operations" => [
								0 => [
									"row_id" => 4,
									"date" => "2016-01-06",
									"operation_type" => "cash_out",
									"amount" => 300.00,
									"currency" => "EUR",
									"fee" => 5,
								],
								1 => [
									"row_id" => 9,
									"date" => "2016-01-10",
									"operation_type" => "cash_in",
									"amount" => 1000000.00,
									"currency" => "EUR",
									"fee" => 5,
								],
							],
						],
						3 => [
							"user_id" => 3,
							"user_type" => "natural",
							"operations" => [
								0 => [
									"row_id" => 10,
									"date" => "2016-01-10",
									"operation_type" => "cash_out",
									"amount" => 1000.00,
									"currency" => "EUR",
									"fee" => 5,
								],
							],
						],
						5 => [
							"user_id" => 5,
							"user_type" => "natural",
							"operations" => [
								0 => [
									"row_id" => 12,
									"date" => "2016-02-19",
									"operation_type" => "cash_out",
									"amount" => 3000000,
									"currency" => "JPY",
									"fee" => 5,
								],
							],
						],
					],
					'input.csv',
					['date', 'user_id', 'user_type', 'operation_type', 'amount', 'currency'],
				]
			];
		}

		public function dataProvider_get_only_fees_by_row_order()
		{
			return [
				[
					[10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
					'input.csv',
					['date', 'user_id', 'user_type', 'operation_type', 'amount', 'currency'],
				]
			];
		}
	}