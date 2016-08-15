<?php

namespace Sda\Crossroad\Light;

/**
 * Class Light
 * @package Sda\Trystar\Light
 */
class Light implements \JsonSerializable
{
    const STATE_SUSPENDED = 'suspended';
    const STATE_SHUTDOWN = 'shutdown';
    const STATE_GREEN = 'green';
    const STATE_RED = 'red';
    const STATE_YELLOW = 'yellow';
    const STATE_RED_YELLOW = 'red_yellow';

    const DEFAULT_STATE=self::STATE_SUSPENDED;

    public static $awalableStates = [
        self::STATE_GREEN,
        self::STATE_RED,
        self::STATE_RED_YELLOW,
        self::STATE_SHUTDOWN,
        self::STATE_SUSPENDED,
        self::STATE_YELLOW
    ];

    /**
     * @var int
     */
    private $id;


    /**
     * Light constructor.
     * @param int $id
     * @param string $state
     */
    public function __construct($id, $state, LightValidator $validator)
{
$this->id = $id;
$this->state = $state;
$this->lightValidator=$validator;

}

/**
 * @return bool
 */
public function validate()
{
    return $this->lightValidator->validateLight($this);
}

/**
 * @return int
 */
public
function getId()
{
    return $this->id;
}

/**
 * @return string
 */
public
function getState()
{
    return $this->state;
}

/**
 * @var string
 */
private
$state;


function jsonSerialize()
{
    return [
        'id' => $this->id,
        'state' => $this->state
    ];
}
}