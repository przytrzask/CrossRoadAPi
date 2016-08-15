<?php

namespace Sda\Crossroad\Light;

/**
 * Class LightFactory
 * @package Sda\Trystar\Light
 */
class LightFactory
{
    /**
     * @param $id
     * @param array $param
     * @return Light
     * @throws LightFactoryException
     */
    public static function makeWithId($id, array $param)
    {
        if (false === array_key_exists('state', $param)) {
            throw new  LightFactoryException('Incorrect light params');
        }
        $builder = new LightBuilder();

        return $builder
            ->withId($id)
            ->withState($param['state'])
            ->build();
    }

    /**
     * @param array $param
     * @return Light
     * @throws LightFactoryException
     */
    public static function makeFromArray(array $param)
    {
        if (false === array_key_exists('id', $param) ||
            false === array_key_exists('state', $param)
        ) {
            throw new  LightFactoryException('Incorrect light params');
        }
        $builder = new LightBuilder();

        return $builder
            ->withId($param['id'])
            ->withState($param['state'])
            ->build();
    }


}