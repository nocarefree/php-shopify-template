<?php

<<<<<<< HEAD
namespace ShopifyLiquid\Filters;
=======
namespace Ncf\ShopifyTemplate\Filters;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe

use Ncf\ShopifyTemplate\Drops\FontDrop;

// https://shopify.dev/api/liquid/filters/font-filters

class FilterFont
{


<<<<<<< HEAD
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
=======
    public static function font_modify(FontDrop $input , $key, $value){
        return $input->modify($key, $value);
    }

    public static function font_face(FontDrop $input , $key){
        return $input->toHtml($key);
    }

    public static function font_url(FontDrop $input, $key = 'woff2'){
        return $input->url($key);
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
    }
}
