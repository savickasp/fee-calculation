<?php


	namespace App;


	use App\Model\Users;
	use App\Support\Calculations\Fees;
	use App\Support\Files\CsvReader;

	class FeesController
	{
		protected $file;
		protected $operations;
		protected $users;
		protected $fees;
		protected $indexes = ['date', 'user_id', 'user_type', 'operation_type', 'amount', 'currency'];

		public function __construct()
		{
			$this->file = new CsvReader();
			$this->users = new Users();
			$this->fees = new Fees();
		}

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


		public function calculateFees()
		{
			foreach ($this->operations as $operation) {
				$userOperations = $this->users->getUserOperations($operation['user_id']);
				$operation = $this->fees->calculateFee($operation, $userOperations);
				$this->users->addOperation($operation);
			}

			return $this->users->getFeesOrderByRow();
		}

		public function getUsersInstance()
		{
			return $this->users;
		}

		public function getFilesInstance()
		{
			return $this->file;
		}

		public function getFeesInstance()
		{
			return $this->fees;
		}
	}