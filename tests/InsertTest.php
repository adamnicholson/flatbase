<?php

namespace Flatbase;

class InsertTest extends FlatbaseTestCase
{
    public function testDatabaseNotCorruptAfterInsert()
    {
        $flatbase = $this->getFlatbase();
        $collection = 'test';
        $db = $this->storageDir . '/' . $collection;
        $flatbase->insert()->in($collection)->setValues(['foo' => 'bar'])->execute();
        $dbContents = file_get_contents($db);
        if (!@unserialize($dbContents)) {
            $this->fail('Insert corrupted the database');
        }
    }
}