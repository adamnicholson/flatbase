<?php

namespace Flatbase\Handler;

use Flatbase\Query\Query;

class QueryHandler
{
    public function __construct($dir)
    {
        $this->dir = $dir;
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
        file_put_contents($this->getFilename($collection), serialize($data));
    }

    protected function getFilename($collection)
    {
        return rtrim($this->dir, '/') . '/' . $collection;
    }
}