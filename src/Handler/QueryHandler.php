<?php

namespace Flatbase\Handler;

use Flatbase\Flatbase;
use Flatbase\Query\Query;

abstract class QueryHandler
{
    public function __construct(Flatbase $flatbase)
    {
        $this->flatbase = $flatbase;
    }

    /**
     * Check if a single record should be matched for a given Query's conditions ("where" clauses)
     *
     * @param $record
     * @param Query $query
     * @return bool
     * @throws \Exception
     */
    protected function recordMatchesQuery($record, Query $query)
    {
        $results = [];

        foreach ($query->getConditions() as $condition) {
            $results[] = $this->assertCondition($record, $condition);
        }

        $failed = in_array(false, $results);

        return !$failed;
    }

    /**
     * Check if a single record should be matched for a single Query condition
     *
     * @param $record
     * @param $condition
     * @return bool
     * @throws \Exception
     */
    protected function assertCondition($record, $condition)
    {
        $left = $condition[0];
        $op = $condition[1];
        $right = $condition[2];

        switch ($op) {
            case '=':
                $value = $this->getRecordField($record, $left);
                return $value == $right;

            case '==':
                $value = $this->getRecordField($record, $left);
                return $value === $right;

            case '!=':
                $value = $this->getRecordField($record, $left);
                return $value != $right;

            case '!==':
                $value = $this->getRecordField($record, $left);
                return $value !== $right;

            case '<':
                $value = $this->getRecordField($record, $left);
                return $value < $right;

            case '>':
                $value = $this->getRecordField($record, $left);
                return $value > $right;

            default:
                throw new \Exception('Operator [' . $op . '] is not supported');
        }

        return false;
    }

    /**
     * Get the value of a field on a record. Defaults to null if not set
     *
     * @param $record
     * @param $field
     * @return null
     */
    protected function getRecordField($record, $field)
    {
        return isset($record[$field]) ? $record[$field] : null;
    }

    /**
     * Validate a Query for execution
     *
     * @param Query $query
     * @throws \Exception
     */
    protected function validateQuery(Query $query)
    {
        if (!$query->getCollection()) {
            throw new \Exception('No colleciton set');
        }
    }

    protected function read($collection)
    {
        return $this->flatbase->getStorage()->get($collection);
    }

    protected function write($collection, $data)
    {
        $this->flatbase->getStorage()->set($collection, $data);
    }
}
