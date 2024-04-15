<?php

namespace ShopifyTemplate\Drops;

use ShopifyTemplate\Nodes\SectionAttributeNode;

class Section extends \Liquid\Models\Drop
{

    function __construct(\stdClass | array $section)
    {
        $this->attributes = is_object($section) ? json_decode(json_encode($section), true) : $section;
    }
}
