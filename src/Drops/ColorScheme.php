<?php

namespace ShopifyTemplate\Drops;

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
