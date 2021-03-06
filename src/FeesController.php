<?php

	declare(strict_types=1);

	namespace App;

	use App\Model\Users;
	use App\Support\Calculations\Fees;
	use App\Support\Files\CsvReader;

	/**
	 * Class FeesController.
	 */
	class FeesController
	{
		protected $file;
		protected $operations;
		protected $users;
		protected $fees;
		protected $indexes;

		/**
		 * FeesController constructor.
		 */
		public function __construct()
		{
			$this->file = new CsvReader();
			$this->users = new Users();
			$this->fees = new Fees();
			$this->indexes = include 'config/inputFileIndexes.php';
		}

		/**
		 * @param bool $returnOperations if true it will return operation used for tests
		 *
		 * @return array|null
		 *                    get all operations and sets then in private parameter
		 */
		public function getOperations(string $fileName, bool $returnOperations = false)
		{
			$error = $this->file->openFile($fileName);
			if (isset($error['error'])) {
				return $error;
			}

			$this->operations = $this->file->setIndexes($this->indexes)
				->getContent(true);

			foreach ($this->operations as $key => $operation) {
				$this->operations[$key]['user_id'] = (int)$operation['user_id'];
				$this->operations[$key]['amount'] = (float)$operation['amount'];
			}

			return $returnOperations ? $this->operations : null;
		}

		/**
		 * @return array
		 *               calculated fees and return only them in array
		 */
		public function calculateFees()
		{
			foreach ($this->operations as $operation) {
				$userOperations = $this->users->getUserOperations($operation['user_id']);
				$operation['fee'] = $this->fees->getOperationFee($operation, $userOperations);
				$this->users->addOperation($operation);
			}

			return $this->users->getFeesOrderByRow();
		}

		/**
		 * @return Users
		 */
		public function getUsersInstance()
		{
			return $this->users;
		}

		/**
		 * @return CsvReader
		 */
		public function getFilesInstance()
		{
			return $this->file;
		}

		/**
		 * @return Fees
		 */
		public function getFeesInstance()
		{
			return $this->fees;
		}
	}
