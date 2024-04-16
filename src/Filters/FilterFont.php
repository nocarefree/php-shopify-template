<?php

namespace ShopifyTemplate\Filters;

use ShopifyTemplate\Drops\Font;

// https://shopify.dev/api/liquid/filters/font-filters

class FilterFont
{


    public static function font_modify(Font $input, string $key, string |int $value)
    {
        return $input->modify([$key => $value]);
    }

    public static function font_face(Font $input, $key = null)
    {
        return $input->face($key);
    }

    public static function font_url(Font $input, $key = null)
    {
        return $input->url($key);
    }
}
