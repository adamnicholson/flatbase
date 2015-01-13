<?php

namespace Flatbase;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\ReadQuery;

class ReadTest extends FlatbaseTestCase
{
    public function testReadWithNoConditions()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 4);
    }

    public function testReadWithSingleEqualsCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        // New query
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '=', 24);
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 1);
        // New query
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('name', '=', 'Adam');
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 3);
        // New query
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('height', '=', "6'4");
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 1);
    }

    public function testReadWithMultipleEqualsConditions()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('name', '=', 'Adam');
        $query->addCondition('company', '=', 'Foo Inc');
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 2);
    }

    public function testReadWithLessThanCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '<', 25);
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 2);
    }

    public function testReadWithMoreThanCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '>', 23);
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 3);
    }

    public function testReadWithNotEqualToCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('name', '!=', 'Michael');
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 3);
    }

    public function testReadWithStrictEqualToCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '==', '24');
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 0);
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '==', 24);
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 1);
    }

    public function testReadWithStrictNotEqualToCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '!==', 24);
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 3);
        $query = new ReadQuery();
        $query->setCollection('users');
        $query->addCondition('age', '!==', '24');
        $collection = $flatbase->execute($query);
        $this->assertEquals($collection->count(), 4);
    }

    public function testSelfExecutionWithGet()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $this->assertTrue($flatbase->read()->in('users')->get() instanceof Collection);
    }

    public function testSelfExecutionWithFirstReturnsFirstItem()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $item1 = $flatbase->read()->in('users')->get()->first();
        $this->assertEquals($flatbase->read()->in('users')->first(), $item1);
    }

    public function testSelfExecutionWithFirstReturnsNullIfCollectionEmpty()
    {
        $flatbase = $this->getFlatbase();
        $flatbase->delete()->in('users')->execute();
        $this->assertEquals($flatbase->read()->in('users')->first(), null);
    }
}