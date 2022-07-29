<?php 

namespace Ncf\ShopifyTemplate\Filters;

use Ncf\ShopifyTemplate\Drops\FontDrop;

// https://shopify.dev/api/liquid/filters/font-filters

class FilterFont{


    public static function font_modify(FontDrop $input , $key, $value){
        return $input->modify($key, $value);
    }

    public static function font_face(FontDrop $input , $key){
        return $input->toHtml($key);
    }

    public static function font_url(FontDrop $input, $key = null){
        return $input->url($key);
    }


}