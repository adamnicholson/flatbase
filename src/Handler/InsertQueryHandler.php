<?php

namespace Flatbase\Handler;

use Flatbase\Query\InsertQuery;
use Flatbase\Query\Query;
use Flatbase\Query\ReadQuery;

class InsertQueryHandler extends QueryHandler
{
    public function handle(InsertQuery $query)
    {
        // Validate the query
        $this->validateQuery($query);

        // Read the existing data
        $readQuery = new ReadQuery();
        $readQuery->setCollection($query->getCollection());
        $existing = $this->flatbase->execute($readQuery);

        // Append the new item to it
        $existing->append($query->getValues());

        // Write back to the storage
        $this->write($query->getCollection(), (array) $existing);
    }

    protected function validateQuery(Query $query)
    {
        parent::validateQuery($query);

        if (!$query->getValues()) {
            throw new \Exception('No values given to insert');
        }
    }
}