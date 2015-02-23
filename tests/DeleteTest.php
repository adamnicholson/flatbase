<?php

namespace Flatbase;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\ReadQuery;

class DeleteTest extends FlatbaseTestCase
{
    public function testDeleteWithNoConditions()
    {
        $flatbase = $this->getFlatbaseWithSampleData();

        $flatbase->delete()->in('users')->execute();

        $count = $flatbase->read()->in('users')->count();

        $this->assertEquals($count, 0);
    }

    public function testDeleteWithSingleEqualsCondition()
    {
        $flatbase = $this->getFlatbaseWithSampleData();

        $countPreDelete = $flatbase->read()->in('users')->count();

        // Delete one thing
        $flatbase->delete()->in('users')->where('age', '=', 24)->execute();

        // Re-count them. Should be $countPreDelete minus one
        $countPostDelete = $flatbase->read()->in('users')->count();

        $this->assertEquals($countPostDelete, $countPreDelete-1);
    }
}