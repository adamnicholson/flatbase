<?php

namespace Flatbase;

use Flatbase\Query\InsertQuery;
use Flatbase\Query\ReadQuery;

class FlatbaseTest extends FlatbaseTestCase
{
    public function testInstance()
    {
        $flatbase = new Flatbase(__DIR__ . '/storage');
        $this->assertTrue($flatbase instanceof Flatbase);
    }

    public function testReadQueryReturnsData()
    {
        $flatbase = $this->getFlatbase();
        $query = new ReadQuery();
        $query->setCollection('users');
        $collection = $flatbase->execute($query);
        $this->assertTrue($collection instanceof Collection);
    }

    public function testInsertQueryDoesNotThrowErrors()
    {
        $flatbase = $this->getFlatbase();
        $query = new InsertQuery();
        $query->setCollection('users');
        $query->setValues([
            'firstname' => 'Adam',
            'lastname' => 'Nicholson'
        ]);
        $flatbase->execute($query);
    }

    public function testInsertIncreasesReadRecordCountByOne()
    {
        $flatbase = $this->getFlatbase();
        // Read the current count
        $query = new ReadQuery();
        $query->setCollection('users');
        $countPreInsert = $flatbase->execute($query)->count();
        // Insert something
        $query = new InsertQuery();
        $query->setCollection('users');
        $query->setValues([
            'firstname' => 'Adam',
            'lastname' => 'Nicholson'
        ]);
        $flatbase->execute($query);
        // Read the new count
        $query = new ReadQuery();
        $query->setCollection('users');
        $countPostInsert = $flatbase->execute($query)->count();
        $this->assertEquals($countPostInsert, $countPreInsert+1);
    }

    protected function getFlatbase()
    {
        return new Flatbase(__DIR__ . '/storage');
    }
}