<?php

namespace Sda\Mastercross;

/**
 * Class TypedCollection
 * @package Sda\Cross
 */
class TypedCollection implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var object[]
     */
    private $items = [];

    /**
     * @var string
     */
    private $itemType = '';

    /**
     * @param string $itemType
     */
    protected function setItemType($itemType)
    {
        if (empty($this->itemType)) {
            $this->itemType = $itemType;
        }
    }

    /**
     * @param object $item
     * @throws \InvalidArgumentException
     */
    public function add($item)
    {
        if (empty($this->itemType) ||
            false === is_object($item) ||
            false === ($item instanceof $this->itemType)
        ) {
            throw new \InvalidArgumentException('Invalid collection item');
        }

        $this->items[] = $item;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    public function clear()
    {
        $this->items = [];
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return 0 === $this->count();
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $result = [];

        foreach ($this->getIterator() as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
