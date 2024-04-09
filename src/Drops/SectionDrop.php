<?php

namespace ShopifyTemplate\Drops;

use ShopifyTemplate\Nodes\SectionAttributeNode;

class SectionDrop extends \Liquid\Models\Drop
{

    function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}
