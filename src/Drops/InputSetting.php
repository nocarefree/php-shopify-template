<?php

namespace ShopifyTemplate\Drops;

class InputSetting
{
    protected $value;

    function __construct($config)
    {
        $this->value = $config['setting'] ?? $config['default'] ?? null;
    }

    function __toString()
    {
        return $this->value;
    }
}
