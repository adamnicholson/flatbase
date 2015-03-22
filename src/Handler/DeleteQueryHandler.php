<?php

namespace Flatbase\Handler;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\Query;

class DeleteQueryHandler extends QueryHandler
{
    public function handle(DeleteQuery $query)
    {
        $this->validateQuery($query);

        $stream = $this->getIterator($query->getCollection());

        foreach ($stream as $record) {
            if ($this->recordMatchesQuery($record, $query)) {
                $stream->remove();
            }
        }
    }

    protected function validateQuery(Query $query)
    {
        parent::validateQuery($query);
    }
}
