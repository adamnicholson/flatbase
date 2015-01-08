<?php

namespace Flatbase;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\ReadQuery;

class DeleteTest extends FlatbaseTestCase
{
    public function testDeleteWithNoConditions()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = new DeleteQuery();
        $query->setCollection('users');
        $flatbase->execute($query);
        // Now re-count them. Should be 0
        $query = new ReadQuery();
        $query->setCollection('users');
        $this->assertEquals($flatbase->execute($query)->count(), 0);
    }

    public function testDeleteWithSingleEqualsCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        // Count the records
        $query = new ReadQuery();
        $query->setCollection('users');
        $countPreDelete = $flatbase->execute($query)->count();
        // Delete one thing
        $query = new DeleteQuery();
        $query->setCollection('users');
        $query->addCondition('age', '=', 24);
        $collection = $flatbase->execute($query);
        // Re-count them. Should be $countPreDelete mins one
        $query = new ReadQuery();
        $query->setCollection('users');
        $this->assertEquals($flatbase->execute($query)->count(), $countPreDelete-1);
    }
}