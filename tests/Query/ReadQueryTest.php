<?php

namespace Flatbase\Query;

use Flatbase\FlatbaseTestCase;

class ReadQueryTest extends FlatbaseTestCase
{
    public function testFluentAliases()
    {
        $flatbase = $this->getFlatbaseWithSampleData();
        $query = $flatbase->read()->in('users')->where('name', '=', 'Adam');
        $users = $flatbase->execute($query);
        $this->assertEquals($users->count(), 3);
    }
}