<?php

namespace Flatbase\Query;

class ReadQuery extends Query
{
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
        return $this->get()->first();
    }
}