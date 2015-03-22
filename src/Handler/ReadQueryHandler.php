<?php

namespace Flatbase\Handler;

use Flatbase\Collection;
use Flatbase\Query\ReadQuery;

class ReadQueryHandler extends QueryHandler
{
    /**
     * Handle a ReadQuery and return the records in a \Flatbase\Collection
     *
     * @param ReadQuery $query
     * @return Collection
     * @throws \Exception
     */
    public function handle(ReadQuery $query)
    {
        $this->validateQuery($query);

        // Get all the records matching the given query conditions
        $records = $this->getAllRecordsMatchingQueryConditions($query);

        // Sort them
        $this->sortRecords($records, $query);
        
        // Limit them
        $this->limitRecords($records, $query);

        // Return them as a Collection
        return new Collection($records);
    }

    /**
     * Get all the records in a collection matching the Query conditions
     *
     * @param ReadQuery $query
     * @return array
     */
    protected function getAllRecordsMatchingQueryConditions(ReadQuery $query)
    {
        $records = [];
        foreach ($this->getIterator($query->getCollection()) as $record) {
            $records[] = $record;
        }

        if (!$query->getConditions()) {
            return $records;
        }

        foreach ($records as $key => $record) {
            if (!$this->recordMatchesQuery($record, $query)) {
                unset($records[$key]);
            }
        }

        return $records;
    }

    /**
     * Sort an array of records as per by a ReadQuery getSortBy()
     *
     * @param $results
     * @param ReadQuery $query
     */
    protected function sortRecords(&$results, ReadQuery $query)
    {
        if (list($sortField, $sortDirection) = $query->getSortBy()) {
            usort($results, function($a, $b) use ($sortField, $sortDirection) {

                $leftValue = $this->getRecordField($a, $sortField);
                $rightValue = $this->getRecordField($b, $sortField);

                if ($sortDirection == 'DESC') {
                    return strcmp((string) $rightValue, (string) $leftValue);
                } else {
                    return strcmp((string) $leftValue, (string) $rightValue);
                }
            });
        }
    }

    /**
     * Limit an array of records as per a ReadQuery getLimit() and getOffset() options
     * @param $records
     * @param ReadQuery $query
     */
    protected function limitRecords(&$records, ReadQuery $query)
    {
        $records = array_slice($records, $query->getOffset(), $query->getLimit());
    }
}
