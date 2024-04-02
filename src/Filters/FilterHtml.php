<?php

<<<<<<< HEAD
namespace ShopifyLiquid\Filters;
=======
namespace Ncf\ShopifyTemplate\Filters;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe


// https://shopify.dev/api/liquid/filters/html-filters

class FilterHtml
{


    public static function image_tag($input, $key = null)
    {
        return null;
    }

    public static function payment_button($input)
    {
        return null;
    }

    public static function payment_termsAnchor($input)
    {
        return null;
    }

    public static function payment_type_svg_tag($input)
    {
        return null;
    }

    public static function preload_tag($input)
    {
        return null;
    }

    public static function script_tag($input)
    {
        return null;
    }

<<<<<<< HEAD
    public static function stylesheet_tag($input)
    {
        return '<link href="' . $input . '" rel="stylesheet" type="text/css" media="all" />';
=======
    public static function stylesheet_tag($input, $data = []){
        $media = $data['media'] ?? 'all';
        return '<link href="'.$input.'" rel="stylesheet" type="text/css" media="'.$media.'" />';
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
    }

    public static function time_tag($input)
    {
        return null;
    }
}
