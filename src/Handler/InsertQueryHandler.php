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

        // Open the file resource
        $file = $this->flatbase->getStorage()->storageDir . '/' . $query->getCollection();
        if (!file_exists($file)) {
            file_put_contents($file, serialize([]));
        }
        $fp = fopen($file, 'r+');

        $count = $this->countItemsInFile($fp);

        // Determine the serialized string size declaration
        $oldDeclaration = 'a:' . $count . ':';
        $newDeclaration = 'a:' . ($count+1) . ':';

        if (strlen($oldDeclaration) === strlen($newDeclaration)) {
            // The new serialized string has the same length declaration as it did previously,
            // so we can switch the numbers and append the new item quickly.

            // Update the length declaration
            fseek($fp, 0);
            fwrite($fp, 'a:' . ($count+1));

            // Append the new item to the string
            fseek($fp, -1, SEEK_END);
            fwrite($fp, 'i:' . $count . ';' . serialize($query->getValues()) . '}');
            fclose($fp);
            return;
        }

        // The new serialized string will have a different character length for the size
        // declaration. Not sure how to "push" the rest of the characters back so for now we'll
        // just use the legacy way inserting

        $file = $this->flatbase->getStorage()->storageDir . '/' . $query->getCollection();
        $serialized = file_get_contents($file);
        $this->appendToSerializedString($serialized, $query->getValues());
        file_put_contents($file, $serialized);
    }


    protected function countItemsInFile($fp)
    {
        $colonsEncountered = 0;
        $count = '';
        for ($i=0; $i<=10; $i++) {
            $char = fgetc($fp);
            if ($colonsEncountered === 2) {
                break;
            }
            if ($colonsEncountered === 1 && $char !== ':') {
                $count .= $char;
            }
            if ($char === ':') {
                $colonsEncountered++;
            }
        }

        return $count;
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