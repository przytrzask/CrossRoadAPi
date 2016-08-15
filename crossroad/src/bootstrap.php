<?php

use Sda\Crossroad\Config\Config;
use Sda\Crossroad\Controller\ApiController;
use Sda\Crossroad\Db\DbConnection;
use Sda\Crossroad\Request\Request;
use Sda\Crossroad\Response\Response;

require_once(__DIR__ . '/../vendor/autoload.php');

$api = new ApiController(
    new Request(),
    new Response(),
    new DbConnection(Config::$connectionParams)
);

$api->run();