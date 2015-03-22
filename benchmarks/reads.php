<?php

require __DIR__.'/bootstrap.php';

use Flatbase\Storage\Filesystem;
use Flatbase\Flatbase;

// Setup the database to read from
$databaseSize = 1000;
echo 'Setting up database with '.number_format($databaseSize).' records'.PHP_EOL;
$data = [];
for ($i = 0; $i <= $databaseSize; $i++) {
    $data[] = ['id' => $i, 'time' => microtime()];
}
file_put_contents(__DIR__.'/storage/test-read-collection', serialize($data));

// Start the clock
$bench = new Ubench;
$bench->start();

echo "Starting reads".PHP_EOL;
$flatbase = new Flatbase(new Filesystem(__DIR__.'/storage'));

$limit = 50;
for ($i = 0; $i <= $limit; $i++) {
    $flatbase->read()->in('test-read-collection')->get();
}

// Stop the clock
$bench->end();

// Post results
echo number_format($limit).' reads of a database with '.number_format($databaseSize).' records completed'.PHP_EOL;
echo 'Execution time: '.$bench->getTime().PHP_EOL;
echo 'Memory peak: '.$bench->getMemoryPeak().PHP_EOL;