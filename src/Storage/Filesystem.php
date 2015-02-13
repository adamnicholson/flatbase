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
        return unserialize(file_get_contents($this->getFilename($key)));
    }

    public function set($key, $data)
    {
        file_put_contents($this->getFilename($key), serialize($data));
    }

    public function getFilename($collection)
    {
        $file = rtrim($this->storageDir, '/') . '/' . $collection;

        if (!file_exists($file)) {
            file_put_contents($file, serialize([]));
            chmod($file, 0777);
        }

        return $file;
    }
}
