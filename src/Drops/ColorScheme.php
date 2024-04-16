<?php

namespace ShopifyTemplate\Drops;

class ColorScheme extends \Liquid\Models\Drop
{

    function __construct($config, $definition)
    {
        foreach ($definition as $value) {

            $id = $value["id"];
            $color = $config[$id] ?? $value['default'] ?? null;
            $this->attributes[$id] = $color ? new Color($color) : null;
        }
    }

    function __toString()
    {
        return $this->color;
    }
}
