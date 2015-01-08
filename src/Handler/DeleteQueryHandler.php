<?php

namespace Flatbase\Handler;

use Flatbase\Flatbase;
use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\Query;
use Flatbase\Query\ReadQuery;

class DeleteQueryHandler extends QueryHandler
{
    public function handle(DeleteQuery $query)
    {
        // Validate the query
        $this->validateQuery($query);

        // Read the existing data
        $readQuery = new ReadQuery();
        $readQuery->setCollection($query->getCollection());
        $existing = $this->flatbase->execute($readQuery);

        // Remove the items which should be deleted
        $toRemove = [];
        foreach ($existing as $offset => $record) {
            if ($this->recordMatchesQuery($record, $query)) {
                $toRemove[] = $offset;
            }
        }
        foreach ($toRemove as $offset) {
            $existing->offsetUnset($offset);
        }

        // Write back to the storage
        $this->write($query->getCollection(), (array) $existing);
    }

    protected function validateQuery(Query $query)
    {
        parent::validateQuery($query);
    }
}