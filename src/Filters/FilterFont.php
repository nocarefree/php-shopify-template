<?php

namespace ShopifyTemplate\Filters;

use ShopifyTemplate\Drops\FontDrop;

// https://shopify.dev/api/liquid/filters/font-filters

class FilterFont
{


    public static function font_modify($input, $key, $value)
    {
        if ($key == 'style') {
            if (in_array($value, ['normal', 'italic', 'oblique'])) {
                $input['style'] = $value;
            }
        }

        if ($key == 'weight') {
            if (in_array($value, ['normal', 'bold', 'lighter', 'bolder'])) {
                $input['weight'] = $value;
            }
        }
        return $input;
    }

    public static function font_face($input, $key = null)
    {
        return null;
    }

    public static function font_url($input, $key = null)
    {
        return $input;
    }
}
