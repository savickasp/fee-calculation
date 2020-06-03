<?php

	require __DIR__ . '/vendor/autoload.php';

	use App\FeesController;

	$stdout = fopen('php://stdout', 'w');

	if (count($argv) === 2) {
		$app = new FeesController();
		$error = $app->getOperations($argv[1]);
		if ($error) {
			fwrite($stdout, $error['error'] . "\n");
			exit();
		}
		$result = $app->calculateFees();

		foreach ($result as $row) {
			fwrite($stdout, $row . "\n");
		}

	} else {
		fwrite($stdout, 'Enter only 1 argument after script.php' . "\n");
		exit();
	}