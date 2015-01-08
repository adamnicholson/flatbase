<?php

namespace Flatbase\Handler;

use Flatbase\Collection;
use Flatbase\Query\ReadQuery;

class ReadQueryHandler extends QueryHandler
{
    public function handle(ReadQuery $query)
    {
        $this->validateQuery($query);

        return new Collection($this->read($query->getCollection()));
    }
}