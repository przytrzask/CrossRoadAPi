<?php

namespace Sda\Crossroad;

/**
 * Class TypedCollection
 * @package Sda\Millionaires
 */
/**
 * Class TypedCollection
 * @package Sda\Trystar
 */
class TypedCollection implements \IteratorAggregate, \Countable, \JsonSerializable
{

    /**
     * @var object[]
     */
    private $items;

    /**
     * @var string
     */
    private $itemType;

    /**
     * @param $item
     * @throws \InvalidArgumentException
     */
    public function add($item)
    {
        if (true === empty($this->itemType) ||
            false === is_object($item) ||
            false == ($item instanceof $this->itemType)
        ) {
            throw new \InvalidArgumentException('Invalid collection item');
        }

        $this->items[] = $item;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return 0 === $this->count();
    }

    public function clear()
    {
        $this->items = [];
    }

    /**
     * @param $itemType
     */
    protected function setItemType($itemType)
    {
        if (true === empty($this->itemType)) {
            $this->itemType = $itemType;
        }

    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
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