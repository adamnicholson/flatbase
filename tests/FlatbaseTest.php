<?php

namespace Flatbase;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\ReadQuery;
use Flatbase\Query\UpdateQuery;
use Flatbase\Storage\Filesystem;

class FlatbaseTest extends FlatbaseTestCase
{
    public function testInstance()
    {
        $storage = new Filesystem(__DIR__ . '/storage');
        $flatbase = new Flatbase($storage);
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

    public function testDeleteDecrementsCountToZeroWithNoConditions()
    {
        $flatbase = $this->getFlatbase();
        // Insert something
        $query = new InsertQuery();
        $query->setCollection('users');
        $query->setValues([
            'firstname' => 'Adam',
            'lastname' => 'Nicholson'
        ]);
        $flatbase->execute($query);
        // Delete everything
        $query = new DeleteQuery();
        $query->setCollection('users');
        $flatbase->execute($query);
        // Read the new count
        $query = new ReadQuery();
        $query->setCollection('users');
        $countDeleteInsert = $flatbase->execute($query)->count();
        $this->assertEquals($countDeleteInsert, 0);
    }

    public function testUpdateChangesValueAndDoesNotAffectCollectionCount()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $countMatchingUsersBeforeUpdate = $flatbase->execute($flatbase->read()->in('users')->where('age', '=', 26))->count();
        $countAllUsersBeforeUpdate = $flatbase->execute($flatbase->read()->in('users'))->count();
        $query = $flatbase->update()->in('users')->where('age', '=', 26)->setValues([
            'age' => 35
        ]);
        $flatbase->execute($query);
        $this->assertEquals($flatbase->execute($flatbase->read()->in('users'))->count(), $countAllUsersBeforeUpdate);
        $this->assertEquals($flatbase->execute($flatbase->read()->in('users')->where('age', '=', 35))->count(), $countMatchingUsersBeforeUpdate);

    }

    public function testQueryBuilderConstructors()
    {
        $flatbase = $this->getFlatbase();
        $this->assertTrue($flatbase->insert() instanceof InsertQuery);
        $this->assertTrue($flatbase->update() instanceof UpdateQuery);
        $this->assertTrue($flatbase->read() instanceof ReadQuery);
        $this->assertTrue($flatbase->delete() instanceof DeleteQuery);
    }

    public function testQueriesCanSelfExecute()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $this->assertTrue($flatbase->read()->in('users')->execute() instanceof Collection);
    }
}