<?php

require __DIR__ . '/bootstrap.php';

use Flatbase\Storage\Filesystem;
use Flatbase\Flatbase;

$databaseSize = 10000;
echo 'Setting up database with ' . number_format($databaseSize) . ' records' . PHP_EOL;

$data = [];
for ($i=0; $i<=$databaseSize; $i++) {
    $data[] = ['id' => $i, 'time' => microtime()];
}
file_put_contents(__DIR__ . '/storage/test-read-collection', serialize($data));

$bench = new Ubench;
$bench->start();

$flatbase = new Flatbase(new Filesystem(__DIR__ . '/storage'));

$limit = 100;
for ($i = 0; $i <= $limit; $i++) {
    $insertQuery = new \Flatbase\Query\ReadQuery();
    $insertQuery->setCollection('test-insert-collection');
    $flatbase->execute($insertQuery);
}

$bench->end();

echo number_format($limit) . ' reads of a database with ' . number_format($databaseSize) . ' records completed' . PHP_EOL;
echo 'Execution time: ' . $bench->getTime() . PHP_EOL;
echo 'Memory peak: ' . $bench->getMemoryPeak() . PHP_EOL;