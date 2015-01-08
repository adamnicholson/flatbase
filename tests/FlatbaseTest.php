<?php

namespace Flatbase;

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
        $flatbase = new Flatbase(__DIR__ . '/storage');
        $query = new ReadQuery();
        $query->setCollection('users');
        $collection = $flatbase->execute($query);
        $this->assertTrue($collection instanceof Collection);
    }
}