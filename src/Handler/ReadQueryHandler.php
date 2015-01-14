<?php

namespace Flatbase\Handler;

use Flatbase\Collection;
use Flatbase\Query\ReadQuery;

class ReadQueryHandler extends QueryHandler
{
    public function handle(ReadQuery $query)
    {
        $this->validateQuery($query);

        // Non-conditional reads
        if (!$query->getConditions()) {
            return $this->handleNoConditionRead($query);
        }

        $records = $this->read($query->getCollection());
        foreach ($records as $key => $record) {
            if (!$this->recordMatchesQuery($record, $query)) {
                unset($records[$key]);
            }
        }

        return new Collection($records);
    }

    protected function handleNoConditionRead(ReadQuery $query)
    {
        $records = $this->read($query->getCollection());
        return new Collection($records);
    }

    protected function isOnlyStrictConditionals(ReadQuery $query)
    {
        foreach ($query->getConditions() as $condition) {
            if ($condition[1] !== '==') {
                return false;
            }
        }

        return true;
    }
}