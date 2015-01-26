<?php

namespace Flatbase\Query;

class ReadQuery extends Query
{
    protected $offset = 0;
    protected $limit = null;

    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function limit($limit)
    {
        return $this->setLimit($limit);
    }

    public function skip($offset)
    {
        return $this->setOffset($offset);
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