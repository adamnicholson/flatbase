<?php

namespace Flatbase;

abstract class FlatbaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getFlatbase()
    {
        return new Flatbase(__DIR__ . '/storage');
    }
}