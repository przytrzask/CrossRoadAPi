<?php

namespace Sda\Crossroad\Config;

class Config
{
    const KEY_FROM_HEADER='trzasq';
    public static $connectionParams = array(
        'dbname' => 'cross',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
        'charset' => 'utf8'
    );
}