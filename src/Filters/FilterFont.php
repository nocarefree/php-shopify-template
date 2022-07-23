<?php 

namespace Ncf\ShopifyLiquid\Filters;

use Ncf\ShopifyLiquid\Drops\FontDrop;

// https://shopify.dev/api/liquid/filters/font-filters

class FilterFont{


    public static function font_modify(FontDrop $input , $key, $value){
        if(!($input instanceof FontDrop)){
            return '';
        }

        return $input->modify($key, $value);
    }

    public static function font_face(FontDrop $input , $key){
        if(!($input instanceof FontDrop)){
            return '';
        }

        return $input->toHtml($key);
    }

    public static function font_url(FontDrop $input, $key = null){
        if(!($input instanceof FontDrop)){
            return '';
        }
        
        return $input->url($key);
    }


}