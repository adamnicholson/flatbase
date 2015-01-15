<?php

namespace Flatbase;

use Flatbase\Query\ReadQuery;

class FluentInterfaceTest extends FlatbaseTestCase
{
    public function testSettersReturnsQueryObject()
    {
        $query = new ReadQuery();
        $this->assertEquals($query->in('foo'), $query);
        $this->assertEquals($query->setCollection('foo'), $query);
        $this->assertEquals($query->addCondition('foo', '=', 'bar'), $query);
        $this->assertEquals($query->where('foo', '=', 'bar'), $query);
    }
}