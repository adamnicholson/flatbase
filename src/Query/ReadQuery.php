<?php

namespace Flatbase\Query;

class ReadQuery extends Query
{
    protected $offset = 0;
    protected $limit = null;
    protected $sort = null;

    /**
     * Set the number of records to skip from the start of the collection
     *
     * @param $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get the offset
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the maximum number of records to return
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get the limit
     * @return integer|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the field the records should be ordered by
     *
     * @param $field
     * @param string $direction
     * @return $this
     */
    public function setSortBy($field, $direction = 'ASC')
    {
        $this->sort = [$field, $direction];
        return $this;
    }

    /**
     * Get the sortBy
     *
     * @return null
     */
    public function getSortBy()
    {
        return $this->sort;
    }

    /**
     * Alias of setLimit()
     *
     * @param $limit
     * @return ReadQuery
     */
    public function limit($limit)
    {
        return $this->setLimit($limit);
    }

    /**
     * Alias of setOffset()
     *
     * @param $offset
     * @return ReadQuery
     */
    public function skip($offset)
    {
        return $this->setOffset($offset);
    }

    /**
     * Alias of setSortBy() in default direction
     *
     * @param $field
     * @return ReadQuery
     */
    public function sort($field)
    {
        return $this->setSortBy($field);
    }

    /**
     * Alias of setSortBy in DESC order
     *
     * @param $field
     * @return ReadQuery
     */
    public function sortDesc($field)
    {
        return $this->setSortBy($field, 'DESC');
    }

    /**
     * Alias of execute()
     *
     * @return \Flatbase\Collection
     * @throws \Exception
     */
    public function get()
    {
        return $this->execute();
    }

    /**
     * Execute the query and return the first element
     *
     * @return mixed|null
     */
    public function first()
    {
        foreach ($this->get() as $item) {
            return $item;
        }
        return null;
    }

    /**
     * Count the records matching the query conditions
     *
     * @return int
     */
    public function count()
    {
        return $this->get()->count();
    }
}
