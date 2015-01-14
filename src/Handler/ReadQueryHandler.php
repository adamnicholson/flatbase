<?php

namespace Flatbase\Handler;

use Flatbase\Collection;
use Flatbase\Query\ReadQuery;

class ReadQueryHandler extends QueryHandler
{
    public function handle(ReadQuery $query)
    {
        $this->validateQuery($query);

        $records = $this->read($query->getCollection());

        if (!$query->getConditions()) {
            // No conditions, so just return the result
            return new Collection($records);
        }

        foreach ($records as $key => $record) {
            if (!$this->recordMatchesQuery($record, $query)) {
                unset($records[$key]);
            }
        }

        return new Collection($records);
    }
}