<?php

	declare(strict_types=1);


	namespace App\Support\Files;


	class CsvReader
	{
		private $openedFile;
		private $indexes;

		/**
		 * @param string $file
		 * @return array
		 * method opens file and sets private parameter
		 */
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

		/**
		 * @param bool $numberRows if set to true it adds index row_id and numerates all rows
		 * @return array
		 * method gets gontent from openedFile parameter
		 */
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

		/**
		 * @param array $indexes
		 * @return $this
		 * method sets index before getting content from file
		 * this method is optional
		 */
		public function setIndexes(array $indexes)
		{
			$this->indexes = $indexes;

			return $this;
		}

		// private methods

		/**
		 * @param $row
		 * @return array
		 * helper method which sets default index if not given
		 * if it has indexes set is uses those idexes
		 */
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