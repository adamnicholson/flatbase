<?php

require __DIR__.'/bootstrap.php';

use Flatbase\Storage\Filesystem;
use Flatbase\Flatbase;

// Empty the database
file_put_contents(__DIR__.'/storage/test-insert-collection', serialize([]));

// Start the clock
$bench = new Ubench;
$bench->start();

$flatbase = new Flatbase(new Filesystem(__DIR__.'/storage'));

$limit = 70000;
for ($i = 0; $i <= $limit; $i++) {
    $insertQuery = new \Flatbase\Query\InsertQuery();
    $insertQuery->setCollection('test-insert-collection');
    $insertQuery->setValues(['id' => $i]);
    $flatbase->execute($insertQuery);
}

// Stop the clock
$bench->end();

// Post the results
echo number_format($limit).' inserts completed'.PHP_EOL;
echo 'Execution time: '.$bench->getTime().PHP_EOL;
echo 'Memory peak: '.$bench->getMemoryPeak().PHP_EOL;