<?php

require __DIR__ . '/../vendor/autoload.php';

// Empty the test database
$files = glob(__DIR__ . '/storage/*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file)) {
        unlink($file); // delete file
    }
}