<?php

namespace Flatbase\Storage;

class Filesystem implements Storage
{
    /**
     * @var string
     */
    protected $storageDir;

    function __construct($storageDir)
    {
        $this->storageDir = $storageDir;
    }

    /**
     * @param $collection
     * @return \SplFileObject
     */
    public function getFileObject($collection)
    {
        $file = $this->getFilename($collection);

        return new \SplFileObject($file, 'r+');
    }

    /**
     * Get the corresponding file path for a given collection
     *
     * @param $collection
     * @return string
     */
    protected function getFilename($collection)
    {
        $file = rtrim($this->storageDir, '/').'/'.$collection;

        if (!file_exists($file)) {
            file_put_contents($file, serialize([]));
            chmod($file, 0777);
        }

        return $file;
    }
}
