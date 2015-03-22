<?php

namespace Flatbase\Storage;

interface Storage
{
    /**
     * @param $collection
     * @return \SplFileObject
     */
    public function getFileObject($collection);
}
