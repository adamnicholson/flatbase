<?php

namespace Flatbase\Query;

abstract class Query
{
    protected $collection;
    protected $values;
    protected $conditions = [];

    /**
     * Set the collection this query is querying
     *
     * @param $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get the collection this query is querying
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Add a condition to this query
     *
     * @param $recordField
     * @param $operator
     * @param $value
     * @return $this
     */
    public function addCondition($recordField, $operator, $value)
    {
        $this->conditions[] = [
            $recordField,
            $operator,
            $value
        ];
        return $this;
    }

    /**
     * Get all the conditions associated with this query
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param mixed $values
     * @return $this
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Alias of setCollection()
     *
     * @param $collection
     * @return $this
     */
    public function in($collection)
    {
        return $this->setCollection($collection);
    }

    /**
     * Alias of addCollection()
     *
     * @param $recordField
     * @param $operator
     * @param $value
     * @return $this
     */
    public function where($recordField, $operator, $value)
    {
        return $this->addCondition($recordField, $operator, $value);
    }
}