<?php

require __DIR__ . '/bootstrap.php';

use Flatbase\Storage\Filesystem;
use Flatbase\Flatbase;

$bench = new Ubench;

$bench->start();

$flatbase = new Flatbase(new Filesystem(__DIR__ . '/storage'));

$limit = 100;
for ($i = 0; $i <= $limit; $i++) {
    $insertQuery = new \Flatbase\Query\InsertQuery();
    $insertQuery->setCollection('test-insert-collection');
    $insertQuery->setValues(['id' => $i]);
    $flatbase->execute($insertQuery);
}

$bench->end();

echo $limit . ' inserts completed' . PHP_EOL;
echo 'Execution time: ' . $bench->getTime() . PHP_EOL;
echo 'Memory peak: ' . $bench->getMemoryPeak() . PHP_EOL;