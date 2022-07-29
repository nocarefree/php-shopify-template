<?php 

namespace Ncf\ShopifyTemplate\Filters;


// https://shopify.dev/api/liquid/filters/html-filters

class FilterHtml{


    public static function image_tag($input, $key = null){
        return null;
    }

    public static function payment_button($input){
        return null;
    }

    public static function payment_termsAnchor($input){
        return null;
    }

    public static function payment_type_svg_tag($input){
        return null;
    }

    public static function preload_tag($input){
        return null;
    }

    public static function script_tag($input){
        return null;
    }

    public static function stylesheet_tag($input){
        return '<link href="'.$input.'" rel="stylesheet" type="text/css" media="all" />';
    }
    
    public static function time_tag($input){
        return null;
    }

}