<?php

namespace Flatbase\Handler;

use Flatbase\Flatbase;
use Flatbase\Query\Query;

class QueryHandler
{
    public function __construct(Flatbase $flatbase)
    {
        $this->flatbase = $flatbase;
    }

    protected function recordMatchesQuery($record, Query $query)
    {
        $results = [];

        foreach ($query->getConditions() as $condition) {
            $results[] = $this->assertCondition($record, $condition);
        }

        $failed = in_array(false, $results);

        return !$failed;
    }

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

    protected function getRecordField($record, $field)
    {
        return isset($record[$field]) ? $record[$field] : null;
    }

    protected function validateQuery(Query $query)
    {
        if (!$query->getCollection()) {
            throw new \Exception('No colleciton set');
        }
    }

    protected function read($collection)
    {
        if (!file_exists($this->getFilename($collection))) {
            $this->write($collection, []);
        }

        return unserialize(file_get_contents($this->getFilename($collection)));
    }

    protected function write($collection, $data)
    {
        file_put_contents($this->getFilename($collection), serialize($data), LOCK_EX);
    }

    protected function getFilename($collection)
    {
        return rtrim($this->flatbase->dir, '/') . '/' . $collection;
    }
}