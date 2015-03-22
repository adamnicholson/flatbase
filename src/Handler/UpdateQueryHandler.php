<?php

namespace Flatbase\Handler;

use Flatbase\Query\UpdateQuery;

class UpdateQueryHandler extends QueryHandler
{
    public function handle(UpdateQuery $query)
    {
        $this->validateQuery($query);

        $stream = $this->getIterator($query->getCollection());

        $toUpdate = [];

        foreach ($stream as $record) {
            if ($this->recordMatchesQuery($record, $query)) {
                $stream->remove();
                $toUpdate[] = $record;
            }
        }

        foreach ($toUpdate as $record) {
            foreach ($query->getValues() as $key => $value) {
                $record[$key] = $value;
            }
            $stream->append($record);
        }
    }
}
