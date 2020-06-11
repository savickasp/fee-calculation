<?php

    declare(strict_types=1);

namespace App\Support\Files;

    class CsvReader
    {
        private $openedFile;
        private $indexes;

        /**
         * method opens file and sets private parameter.
         *
         * @return array
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
         * method gets content from openedFile parameter.
         *
         * @param bool $numberRows if set to true it adds index row_id and numerates all rows
         *
         * @return array
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
                ++$i;
            }

            return $content;
        }

        /**
         * method sets index before getting content from file
         * this method is optional.
         *
         * @return $this
         */
        public function setIndexes(array $indexes)
        {
            $this->indexes = $indexes;

            return $this;
        }

        // private methods

        /**
         * helper method which sets default index if not given
         * if it has indexes set is uses those idexes.
         *
         * @param $row
         *
         * @return array
         */
        private function setRowIndexes($row)
        {
            $rowWithIndexes = [];

            foreach ($row as $fieldIndex => $field) {
                if (preg_match('/^[0-9]+$/', $field)) {
                    $field = (int) $field;
                } elseif (preg_match('/^[0-9.]+$/', $field)) {
                    $field = (float) $field;
                } else {
                    $field = (string) $field;
                }
                if (isset($this->indexes[$fieldIndex])) {
                    $rowWithIndexes[$this->indexes[$fieldIndex]] = $field;
                } else {
                    $rowWithIndexes[$fieldIndex] = $field;
                }
            }

            return $rowWithIndexes;
        }
    }
