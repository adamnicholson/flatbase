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

    protected function getFlatbaseWithSampleData()
    {
        $flatbase = $this->getFlatbase();

        // Empty it
        $deleteQuery = new DeleteQuery();
        $deleteQuery->setCollection('users');
        $flatbase->execute($deleteQuery);

        $data = [
            [
                'name' => 'Adam',
                'age' => 23,
                'height' => "6'3",
                'company' => 'Foo Inc'
            ],
            [
                'name' => 'Adam',
                'age' => 24,
                'height' => "6'4",
                'company' => 'Foo Inc'
            ],
            [
                'name' => 'Adam',
                'age' => 25,
                'height' => "6'5",
                'company' => 'Bar Inc'
            ],
            [
                'name' => 'Michael',
                'age' => 26,
                'height' => "6'6",
                'company' => 'Foo Inc'
            ],
        ];

        foreach ($data as $record) {
            $query = new InsertQuery();
            $query->setCollection('users');
            $query->setValues($record);
            $flatbase->execute($query);
        }

        return $flatbase;
    }
}