<?php


	namespace Tests\Unit;


	use App\FeesController;
	use PHPUnit\Framework\TestCase;

	class FeeControllerUnitTest extends TestCase
	{
		/** @test */
		public function has_access_to_users_class()
		{
			$app = new FeesController();
			$this->assertInstanceOf('App\Model\Users', $app->getUsersInstance());
		}

		/** @test */
		public function has_access_to_CsvReader_class()
		{
			$app = new FeesController();
			$this->assertInstanceOf('App\Support\Files\CsvReader', $app->getFilesInstance());
		}

		/** @test */
		public function has_access_to_Fees_class()
		{
			$app = new FeesController();
			$this->assertInstanceOf('App\Support\Calculations\Fees', $app->getFeesInstance());
		}


	}