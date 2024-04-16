<?php

namespace ShopifyTemplate\Drops;

class Image extends \Liquid\Models\Drop
{
    function __construct($handle)
    {
        $this->attributes = ['src' => $handle];
    }
}
