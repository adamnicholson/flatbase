<?php

namespace Flatbase;

use Flatbase\Handler\ReadQueryHandler;
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
        if ($query instanceof ReadQuery) {
            $handler = new ReadQueryHandler($this->dir);
        }

        return $handler->handle($query);
    }
}