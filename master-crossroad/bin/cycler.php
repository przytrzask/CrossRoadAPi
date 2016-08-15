<?php

use Sda\Mastercross\Config\Config;
use Sda\Mastercross\Cycler\Cycler;
use Sda\Mastercross\Db\DbConnection;

require_once(__DIR__ . '/../vendor/autoload.php');

$db = new DbConnection(
    Config::$connectionParams
);

$app = new Cycler(
    $db
);

$app->run();