<?php

namespace Flatbase;

class Collection extends \ArrayObject implements \IteratorAggregate, \Traversable, \ArrayAccess, \Serializable, \Countable
{
    public function first()
    {
        return $this->offsetExists(0) ? $this->offsetGet(0) : null;
    }
}
