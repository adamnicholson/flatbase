<?php

namespace Flatbase\Storage;

/**
 * The Storage interface is used for storing the database, and requires
 * a simple key-value pair
 */
interface Storage
{
    public function get($key);

    public function set($key, $data);
}