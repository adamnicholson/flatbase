<?php

namespace Flatbase;

use Flatbase\Query\InsertQuery;

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

    public function testSetSetsValuesWhenArrayPassed()
    {
        $query = new InsertQuery();

        $query->in('foo')->set($data = [
                'foo' => 'bar',
                'baz' => 'buz'
        ]);

        $this->assertEquals($query->getValues(), $data);
    }

    public function testSetAppendsValuesWhenArrayPassed()
    {
        $query = new InsertQuery();

        $query->in('foo')->set('bla', 'blo')->set($data = [
                'foo' => 'bar',
                'baz' => 'buz'
        ]);

        $this->assertEquals($query->getValues(), [
                'bla' => 'blo',
                'foo' => 'bar',
                'baz' => 'buz'
            ]);
    }

    public function testSetSetsValuesWhenKeyValuePairPassed()
    {
        $query = new InsertQuery();

        $query->in('foo')->set('foo', 'bar')->set('baz', 'buz');

        $this->assertEquals($query->getValues(), [
                'foo' => 'bar',
                'baz' => 'buz'
        ]);
    }
}
