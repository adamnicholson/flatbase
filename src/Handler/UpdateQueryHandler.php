<?php

namespace Flatbase\Handler;

use Flatbase\Query\ReadQuery;
use Flatbase\Query\UpdateQuery;

class UpdateQueryHandler extends QueryHandler
{
    public function handle(UpdateQuery $query)
    {
        // Validate the query
        $this->validateQuery($query);

        // Read the existing data
        $readQuery = new ReadQuery();
        $readQuery->setCollection($query->getCollection());
        $existing = $this->flatbase->execute($readQuery);

        // Update the items which should be updated
        $toUpdate = [];
        foreach ($existing as $offset => $record) {
            if ($this->recordMatchesQuery($record, $query)) {
                $toUpdate[] = $offset;
            }
        }
        foreach ($toUpdate as $offset) {
            foreach ($query->getValues() as $key => $value) {
                $existing[$offset][$key] = $value;
            }
        }

        // Write back to the storage
        $this->write($query->getCollection(), (array) $existing);
    }
}