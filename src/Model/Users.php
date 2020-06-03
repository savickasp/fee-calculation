<?php

declare(strict_types=1);

namespace App\Model;

    class Users
    {
        /**
         * 	example view of users array
         *    $users = [
         *        '555' => [
         *            'user_id' => 555,
         *            'user_type' => 'legal',
         *            'operation' => [
         *                'row_id' => 5,
         *                'date' => '2020-01-01',
         *                'operation_type' => 'cashout',
         *                'amount' => 5555,
         *                'currency' => 'EUR',
         *                'fee' => 1
         *            ],
         *        ],
         *    ];.
         */
        private $users = [];

        /**
         * @param array $operation
         *                         this method takes operation and adds operation details to user array
         */
        public function addOperation(array $operation)
        {
            if (!array_key_exists($operation['user_id'], $this->users)) {
                $this->createUser($operation);
            }
            $this->users[$operation['user_id']]['operations'][] = [
                'row_id' => $operation['row_id'],
                'date' => $operation['date'],
                'operation_type' => $operation['operation_type'],
                'amount' => $operation['amount'],
                'currency' => $operation['currency'],
                'fee' => $operation['fee'],
            ];
        }

        /**
         * @return array
         *               get all users array with all data
         */
        public function all(): array
        {
            return $this->users;
        }

        /**
         * @return array
         *               get single user all operations
         */
        public function getUserOperations(int $id): array
        {
            return isset($this->users[$id]) ? $this->users[$id]['operations'] : [];
        }

        /**
         * @return array
         *               get all users all operations orders by row_id
         */
        public function getFeesOrderByRow(): array
        {
            $ret = [];

            foreach ($this->users as $user) {
                foreach ($user['operations'] as $operation) {
                    $ret[$operation['row_id']] = $operation['fee'];
                }
            }
            ksort($ret);

            return  $ret;
        }

        // private methods

        /**
         * @param array $operation
         *                         helper method to create user if it didnt exist
         */
        private function createUser(array $operation)
        {
            $this->users[$operation['user_id']] = [
                'user_id' => $operation['user_id'],
                'user_type' => $operation['user_type'],
                'operations' => [],
            ];
        }
    }
