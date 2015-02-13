<?php

namespace Flatbase;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\ReadQuery;

class ReadTest extends FlatbaseTestCase
{
    public function testReadWithNoConditions()
    {
        $this->assertEquals($this->getFlatbaseWithSampleData()->read()->in('users')->count(), 4);
    }

    public function testReadWithSingleEqualsCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $this->assertEquals($flatbase->read()->in('users')->where('age', '=', 24)->count(), 1);
        $this->assertEquals($flatbase->read()->in('users')->where('name', '=', 'Adam')->count(), 3);
        $this->assertEquals($flatbase->read()->in('users')->where('height', '=', "6'4")->count(), 1);
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
        $count = $flatbase->read()->in('users')
            ->where('age', '==', 24)
            ->where('name', '==', 'Adam')
            ->get()->count();
        $this->assertEquals($count, 1);
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

    public function testFluentQueryBuildingAliases()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = $flatbase->read()->in('users')->where('name', '=', 'Adam');
        $users = $flatbase->execute($query);
        $this->assertEquals($users->count(), 3);
    }

    public function testSelfExecutionWithCount()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $this->assertEquals($flatbase->read()->in('users')->count(), 4);
    }

    public function testSelfExecutionWithGet()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $this->assertTrue($flatbase->read()->in('users')->get() instanceof Collection);
    }

    public function testFluentAliases()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = $flatbase->read()->in('users')->where('name', '=', 'Adam');
        $users = $flatbase->execute($query);
        $this->assertEquals($users->count(), 3);
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

    public function testLimit()
    {
        $flatbase = $this->getFlatbaseWithSampleData();

        $users = $flatbase->read()->in('users')->where('name', '==', 'Adam')->setLimit(2)->execute();
        $this->assertEquals($users->count(), 2);

        $users = $flatbase->read()->in('users')->setLimit(2)->execute();
        $this->assertEquals($users->count(), 2);
    }

    public function testOffset()
    {
        $flatbase = $this->getFlatbaseWithSampleData();

        $user = $flatbase->read()->in('users')->where('name', '==', 'Adam')->setOffset(1)->first();
        $this->assertEquals($user['age'], 24);

        $user = $flatbase->read()->in('users')->where('name', '==', 'Adam')->setOffset(2)->first();
        $this->assertEquals($user['age'], 25);

        $users = $flatbase->read()->in('users')->where('name', '==', 'Adam')->setOffset(1)->get();
        $this->assertEquals($users->count(), 2);

        $users = $flatbase->read()->in('users')->setOffset(2)->get();
        $this->assertEquals($users->count(), 2);
    }

    public function testOffsetAndLimit()
    {
        $flatbase = $this->getFlatbaseWithSampleData();

        $users = $flatbase->read()->in('users')->setOffset(1)->setLimit(2)->get();
        $this->assertEquals($users->first()['age'], 24);
        $this->assertEquals($users->count(), 2);
    }

    public function testSortOrder()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $users = $flatbase->read()->in('users')->sort('weight')->get();
        $this->assertEquals($users[0]['weight'], 170);
        $this->assertEquals($users[1]['weight'], 180);
        $this->assertEquals($users[2]['weight'], 200);
        $this->assertEquals($users[3]['weight'], 210);
    }

    public function testSortOrderDesc()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $users = $flatbase->read()->in('users')->sortDesc('weight')->get();
        $this->assertEquals($users[0]['weight'], 210);
        $this->assertEquals($users[1]['weight'], 200);
        $this->assertEquals($users[2]['weight'], 180);
        $this->assertEquals($users[3]['weight'], 170);
    }

    public function testSortWithLimit()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $users = $flatbase->read()->in('users')->sortDesc('weight')->limit(2)->get();
        $this->assertEquals($users[0]['weight'], 210);
        $this->assertEquals($users[1]['weight'], 200);
        $this->assertEquals($users->count(), 2);
    }

    public function testSortWhenFieldDoesNotAlwaysExist()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $flatbase->update()->in('users')->where('age', '==', 24)->setValues(['foo' => 'bar'])->execute();
        $users = $flatbase->read()->in('users')->sort('foo')->get();
        $this->assertEquals($users[3]['foo'], 'bar');
    }

    public function testSortDescWhenFieldDoesNotAlwaysExist()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $flatbase->update()->in('users')->where('age', '==', 24)->setValues(['foo' => 'bar'])->execute();
        $users = $flatbase->read()->in('users')->sortDesc('foo')->get();
        $this->assertEquals($users[0]['foo'], 'bar');
    }
}
