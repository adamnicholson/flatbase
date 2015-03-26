<?php

namespace Flatbase\Handler;

use Flatbase\Exception\Exception;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\Query;

class InsertQueryHandler extends QueryHandler
{
    public function handle(InsertQuery $query)
    {
        $this->validateQuery($query);

        $stream = $this->getIterator($query->getCollection());

        $stream->append($query->getValues());
    }

    protected function validateQuery(Query $query)
    {
        parent::validateQuery($query);

        if (!$query->getValues()) {
            throw new Exception('No values given to insert');
        }
    }
}
