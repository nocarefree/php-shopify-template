<?php

namespace ShopifyTemplate\Drops;

class InputSetting
{
    protected $value;

    function __construct($config)
    {
        $this->value = $config;
    }

    function __toString()
    {
        return $this->value;
    }
}
