<?php

namespace Flatbase\Handler;

use Flatbase\Flatbase;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\ReadQuery;

class InsertQueryHandler extends QueryHandler
{
    public function handle(InsertQuery $query)
    {
        $this->validateQuery($query);

        // Read the existing data
        $bus = new Flatbase($this->dir);
        $readQuery = new ReadQuery();
        $readQuery->setCollection($query->getCollection());
        $existing = $bus->execute($readQuery);

        // Append the new item to it
        $existing->append($query->getValues());

        // Write back to the storage
        $this->write($query->getCollection(), (array) $existing);
    }

    protected function validateQuery(InsertQuery $query)
    {
        parent::validateQuery($query);

        if (!$query->getValues()) {
            throw new \Exception('No values given to insert');
        }
    }
}