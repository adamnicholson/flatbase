<?php

namespace Flatbase;

class CollectionTest extends FlatbaseTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new Collection() instanceof Collection);
    }

    public function testArrayAccess()
    {
        $items = ['one', 'two', 'three'];
        $collection = new Collection($items);
        $this->assertEquals($items[0], $collection[0]);
        $this->assertEquals($items[1], $collection[1]);
        $this->assertEquals($items[2], $collection[2]);
        $this->assertEquals(count($collection), 3);
    }

    public function testFirst()
    {
        $items = ['one', 'two', 'three'];
        $collection = new Collection($items);
        $this->assertEquals($collection->first(), 'one');
    }
}