<?php


	namespace App;


	use App\Support\Files\CsvReader;

	class FeesController
	{
		protected $file;
		protected $operations;
		protected $indexes = ['date', 'user_id', 'user_type', 'operation_type', 'amount', 'currency'];

		public function __construct()
		{
			$this->file = new CsvReader();
		}

		public function getOperations(string $fileName)
		{
			$error = $this->file->openFile($fileName);
			if ($error) return $error;

			$this->operations = $this->file->setIndexes($this->indexes)
				->getContent(true);

			return $this->operations;
		}

	}