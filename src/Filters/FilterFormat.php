<?php

namespace ShopifyTemplate\Filters;

use ShopifyTemplate\Drops\Font;

// https://shopify.dev/api/liquid/filters/font-filters

class FilterFormat
{


    public static function json($input)
    {
        return @json_encode($input);
    }

    public static function weight_with_unit($input, $unit = 'kg')
    {
        return ($input == 'kg' ? ($input / 1000) : $input) . ' ' . $unit;
    }
}
