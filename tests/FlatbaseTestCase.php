<?php

namespace Flatbase;

use Flatbase\Query\DeleteQuery;
use Flatbase\Query\InsertQuery;
use Flatbase\Storage\Filesystem;

abstract class FlatbaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected $storageDir;

    public function setup()
    {
        $this->storageDir = __DIR__ . '/storage';
        parent::setup();
    }

    protected function getFlatbase()
    {
        $storage = new Filesystem($this->storageDir);
        $flatbase = new Flatbase($storage);
        return $flatbase;
    }

    protected function getFlatbaseWithSampleData()
    {
        $flatbase = $this->getFlatbase();

        // Empty it
        @unlink(__DIR__ . '/storage/users');

        $data = [
            [
                'name' => 'Adam',
                'age' => 23,
                'height' => "6'3",
                'company' => 'Foo Inc',
                'weight' => 200
            ],
            [
                'name' => 'Adam',
                'age' => 24,
                'height' => "6'4",
                'company' => 'Foo Inc',
                'weight' => 180
            ],
            [
                'name' => 'Adam',
                'age' => 25,
                'height' => "6'5",
                'company' => 'Bar Inc',
                'weight' => 210
            ],
            [
                'name' => 'Michael',
                'age' => 26,
                'height' => "6'6",
                'company' => 'Foo Inc',
                'weight' => 170
            ],
        ];

        foreach ($data as $record) {
            $flatbase->insert()
                ->in('users')
                ->setValues($record)
                ->execute();
        }

        return $flatbase;
    }
}
