<?php

namespace Flatbase\Handler;

use Flatbase\Query\InsertQuery;
use Flatbase\Query\Query;
use Flatbase\Query\ReadQuery;

class InsertQueryHandler extends QueryHandler
{
    public function handle(InsertQuery $query)
    {
        // Validate the query
        $this->validateQuery($query);

        $file = $this->flatbase->getStorage()->storageDir . '/' . $query->getCollection();
        $serialized = file_get_contents($file);
        $this->appendToSerializedString($serialized, $query->getValues());

        file_put_contents($file, $serialized);
    }

    protected function appendToSerializedString(&$serialized, $item)
    {
        // Calculate the number of items in the serialized string
        $colonsEncountered = 0;
        $count = '';
        for ($i=0; $i<=10; $i++) {
            if ($colonsEncountered === 2) {
                break;
            }
            if ($colonsEncountered === 1 && $serialized[$i] !== ':') {
                $count .= $serialized[$i];
            }
            if ($serialized[$i] === ':') {
                $colonsEncountered++;
            }
        }

        // Add the new item to the end of the serialized string, just before the closing brace
        $serialized = substr_replace($serialized, ('i:' . $count . ';' . serialize($item) . ''), (strlen($serialized) - 1), 0);

        // Update the item count at the start of the serialized string
        $oldDecleration = 'a:' . $count . ':';
        $newDecleration = 'a:' . ($count+1) . ':';
        $serialized = substr_replace($serialized, $newDecleration, 0, strlen($oldDecleration));

        return $serialized;
    }

    protected function validateQuery(Query $query)
    {
        parent::validateQuery($query);

        if (!$query->getValues()) {
            throw new \Exception('No values given to insert');
        }
    }
}