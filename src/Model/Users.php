<?php


	namespace App\Model;


	class Users
	{
		private $users = [];

		/**
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
		 *    ];
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

		public function all()
		{
			return $this->users;
		}

		private function createUser(array $operation)
		{
			$this->users[$operation['user_id']] = [
				'user_id' => $operation['user_id'],
				'user_type' => $operation['user_type'],
				'operations' => [],
			];
		}

		public function getUserOperations(int $id) :array
		{
			return isset($this->users[$id]) ? $this->users[$id]['operations'] : [];
		}

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
	}