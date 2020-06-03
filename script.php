<?php

	require __DIR__ . '/vendor/autoload.php';

	use App\FeesController;

	$stdout = fopen('php://stdout', 'w');

	// check if given correct number of parameters
	if (count($argv) === 2) {

		// call to main machanism
		$app = new FeesController();
		$error = $app->getOperations($argv[1]);

		// if error while opening file print it
		if ($error) {
			fwrite($stdout, $error['error'] . "\n");

			// else calculete fees and print them
		} else {
			$result = $app->calculateFees();

			foreach ($result as $row) {
				fwrite($stdout, $row . "\n");
			}

		}

		exit();

	} else {
		fwrite($stdout, 'Enter only 1 argument after script.php' . "\n");
		exit();
	}