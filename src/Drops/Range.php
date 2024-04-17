<?php

namespace ShopifyTemplate\Drops;

class Range extends InputSetting
{
    function __toString()
    {
        return $this->value;
    }
}
