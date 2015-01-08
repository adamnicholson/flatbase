<?php

namespace Flatbase\Query;

abstract class Query
{
    protected $collection;

    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }
}