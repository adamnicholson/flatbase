<?php

namespace Flatbase\Query;

use Flatbase\Flatbase;

abstract class Query
{
    protected $flatbase;
    protected $collection;
    protected $values;
    protected $conditions = [];

    public function __construct(Flatbase $flatbase = null)
    {
        $this->flatbase = $flatbase;
    }

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

    public function execute()
    {
        if (!$this->flatbase) {
            throw new \Exception('Query::execute() can only be called when the query was
                created by Flatbase, eg. Flatbase::read()');
        }

        return $this->flatbase->execute($this);
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