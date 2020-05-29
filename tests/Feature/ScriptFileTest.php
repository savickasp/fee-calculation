<?php


	namespace Tests\Feature;


	use PHPUnit\Framework\TestCase;

	class ScriptFileTest extends TestCase
	{
		/** @test */
		public function can_only_enter_one_argument_after_script_file()
		{
			$terminal = shell_exec('php script.php');

			$this->assertEquals("Enter only 1 argument after script.php\n", $terminal);

			$terminal = shell_exec('php script.php asd asd');

			$this->assertEquals("Enter only 1 argument after script.php\n", $terminal);
		}

		/** @test */
		public function expect_errors_then_given_file_name_is_not_found_or_wrong_extension()
		{
			$terminal = shell_exec('php script.php file.txt');

			$this->assertEquals('File format must be ".csv"' . "\n", $terminal);

			$terminal = shell_exec('php script.php file.csv');

			$this->assertEquals('File not found' . "\n", $terminal);
		}

	}