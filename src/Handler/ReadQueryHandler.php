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

        $returnCollection = new Collection();

        $records = $this->read($query->getCollection());

        $matched = 0;
        $used = 0;

        foreach ($records as $key => $record) {
            if ($this->recordMatchesQuery($record, $query)) {
                $matched++;
                $used++;

                if ($matched <= $query->getOffset()) {
                    continue;
                }

                $returnCollection->append($record);

                if (!is_null($query->getLimit()) && $used >= $query->getLimit()) {
                    return $returnCollection;
                }
            }
        }

        return $returnCollection;
    }

    protected function handleNoConditionRead(ReadQuery $query)
    {
        // Limit the results
        $records = array_slice($this->read($query->getCollection()), $query->getOffset(), $query->getLimit());
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