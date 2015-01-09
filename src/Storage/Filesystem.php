<?php

namespace Flatbase\Storage;

class Filesystem implements Storage
{
    function __construct($storageDir)
    {
        $this->storageDir = $storageDir;
    }

    public function get($key)
    {
        if (!file_exists($this->getFilename($key))) {
            $this->set($key, []);
        }

        return unserialize(file_get_contents($this->getFilename($key)));
    }

    public function set($key, $data)
    {
        file_put_contents($this->getFilename($key), serialize($data));
    }

    protected function getFilename($collection)
    {
        return rtrim($this->storageDir, '/') . '/' . $collection;
    }
}