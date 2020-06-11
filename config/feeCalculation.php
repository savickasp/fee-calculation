<?php

	return [
		'cashIn' => [
			'feePercent' => 0.03,
			'maxSumEur' => 5.00,
		],
		'cashOut' => [
			'natural' => [
				'feePercent' => 0.3,
				'freeOfChargeSumPerWeakEur' => 1000,
				'freeOperationNumber' => 3,
			],
			'legal' => [
				'feePercent' => 0.3,
				'minSumEur' => 0.5,
			],
		],
	];