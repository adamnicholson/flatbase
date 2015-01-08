<?php

namespace Flatbase\Handler;

use Flatbase\Collection;
use Flatbase\Query\ReadQuery;

class ReadQueryHandler extends QueryHandler
{
    public function handle(ReadQuery $query)
    {
        $this->validateQuery($query);

        $collection = new Collection();

        foreach ($this->read($query->getCollection()) as $record) {
            if ($this->recordMatchesQuery($record, $query)) {
                $collection->append($record);
            }
        }

        return $collection;
    }
}