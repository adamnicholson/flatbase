<?php

namespace Flatbase;

use Flatbase\Handler\DeleteQueryHandler;
use Flatbase\Handler\InsertQueryHandler;
use Flatbase\Handler\ReadQueryHandler;
use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\Query;
use Flatbase\Query\ReadQuery;

class Flatbase
{
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function execute(Query $query)
    {
        $handler = $this->resolveHandler($query);

        return $handler->handle($query);
    }

    protected function resolveHandler(Query $query)
    {
        if ($query instanceof ReadQuery) {
            return new ReadQueryHandler($this->dir);
        }

        if ($query instanceof InsertQuery) {
            return new InsertQueryHandler($this->dir);
        }

        if ($query instanceof DeleteQuery) {
            return new DeleteQueryHandler($this->dir);
        }

        throw new \Exception('Could not resolve handler for query');
    }
}