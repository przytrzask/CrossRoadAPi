<?php

namespace Sda\Crossroad\Light;

/**
 * Class LightValidator
 * @package Sda\Trystar\Light
 */
class LightValidator
{
    /**
     * @param Light $light
     */
    public function validateLight(Light $light)
    {
        return in_array($light->getState(),Light::$awalableStates,true);
    }
}