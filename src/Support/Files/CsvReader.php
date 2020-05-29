<?php

	declare(strict_types=1);


	namespace App\Support\Files;


	class CsvReader
	{
		private $openedFile;
		private $indexes;

		public function openFile(string $file)
		{
			if (!preg_match('/(.csv)$/', $file)) {
				return ['error' => 'File format must be ".csv"'];
			} elseif (!file_exists($file)) {
				return ['error' => 'File not found'];
			} else {
				$this->openedFile = fopen($file, 'r');
			}
		}

		public function getContent(bool $numberRows = false)
		{
			if (!$this->openedFile) {
				return ['error' => 'Open file first'];
			}

			$content = [];
			$i = 0;

			while (!feof($this->openedFile)) {
				$row = fgetcsv($this->openedFile);
				if ($numberRows) {
					$content[] = ['row_id' => $i] + ($this->indexes ? $this->setRowIndexes($row) : $row);
				} else {
					$content[] = ($this->indexes ? $this->setRowIndexes($row) : $row);
				}
				$i++;
			}

			return $content;
		}

		public function setIndexes(array $indexes)
		{
			$this->indexes = $indexes;

			return $this;
		}

		private function setRowIndexes($row)
		{
			$ret = [];

			foreach ($row as $fieldIndex => $field) {

				if (isset($this->indexes[$fieldIndex])) {
					$ret[$this->indexes[$fieldIndex]] = $field;
				} else {
					$ret[$fieldIndex] = $field;
				}
			}

			return $ret;
		}
	}