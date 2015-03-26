<?php

namespace Flatbase;

use Flatbase\Exception\Exception;
use Flatbase\Handler\DeleteQueryHandler;
use Flatbase\Handler\InsertQueryHandler;
use Flatbase\Handler\ReadQueryHandler;
use Flatbase\Handler\UpdateQueryHandler;
use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Query\Query;
use Flatbase\Query\ReadQuery;
use Flatbase\Query\UpdateQuery;
use Flatbase\Storage\Storage;

class Flatbase
{
    /**
     * @var \Flatbase\Storage\Storage
     */
    protected $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Execute a query
     *
     * @param Query $query
     * @return Collection|void
     * @throws \Exception
     */
    public function execute(Query $query)
    {
        $handler = $this->resolveHandler($query);

        return $handler->handle($query);
    }

    /**
     * Create a new insert query
     *
     * @return InsertQuery
     */
    public function insert()
    {
        return new InsertQuery($this);
    }

    /**
     * Create a new update query
     *
     * @return UpdateQuery
     */
    public function update()
    {
        return new UpdateQuery($this);
    }

    /**
     * Create a new read query
     *
     * @return ReadQuery
     */
    public function read()
    {
        return new ReadQuery($this);
    }

    /**
     * Create a new delete query
     *
     * @return DeleteQuery
     */
    public function delete()
    {
        return new DeleteQuery($this);
    }

    /**
     * Find the appropriate handler for a given Query
     *
     * @param Query $query
     * @return DeleteQueryHandler|InsertQueryHandler|ReadQueryHandler
     * @throws \Exception
     */
    protected function resolveHandler(Query $query)
    {
        if ($query instanceof ReadQuery) {
            return new ReadQueryHandler($this);
        }

        if ($query instanceof InsertQuery) {
            return new InsertQueryHandler($this);
        }

        if ($query instanceof DeleteQuery) {
            return new DeleteQueryHandler($this);
        }

        if ($query instanceof UpdateQuery) {
            return new UpdateQueryHandler($this);
        }

        throw new Exception('Could not resolve handler for query');
    }

    /**
     * @return \Flatbase\Storage\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
