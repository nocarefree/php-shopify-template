<?php

namespace ShopifyTemplate\Drops\Settings;

class ColorSchema extends \Liquid\Models\Drop
{

    function __construct($values)
    {
        foreach ($values as $key => $value) {
            $this->attributes[$key] = new Color($value);
        }
    }

    function __toString()
    {
        return $this->color;
    }
}
