<?php

namespace Flatbase\Query;

abstract class Query
{
    protected $collection;
    protected $conditions;

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function addCondition($recordField, $operator, $value)
    {
        $this->conditions[] = [
            $recordField,
            $operator,
            $value
        ];
    }

    public function getConditions()
    {
        return $this->conditions;
    }
}