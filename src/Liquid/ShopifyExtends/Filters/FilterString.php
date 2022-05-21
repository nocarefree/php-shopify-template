<?php 

namespace Liquid\ShopifyExtends\Filters;


// https://shopify.dev/api/liquid/filters/html-filters

class FilterString{

    public static function base64_encode($input, $key = null){
        return base64_encode($input);
    }

    public static function base64_decode($input, $key = null){
        return base64_decode($input);
    }

    public static function base64_url_safe_encode($input, $key = null){
        return str_replace(['+','/'],['-','_'],base64_encode($input));
    }

    public static function base64_url_safe_decode($input, $key = null){
        return base64_decode(str_replace(['-','_'],['+','/'],$input));
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
        return null;
    }
    
    public static function time_tag($input){
        return null;
    }

}