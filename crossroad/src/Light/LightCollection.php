<?php

namespace Sda\Crossroad\Light;

use Sda\Crossroad\TypedCollection;


class LightCollection extends TypedCollection
{

    public function __construct()
    {
        $this->setItemType(Light::class);
    }

}