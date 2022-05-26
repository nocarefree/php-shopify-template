<?php 

namespace Ncf\ShopifyLiquid\Filters;


// https://shopify.dev/api/liquid/filters/html-filters

class FilterUrl{

    public static function asset_url ($input){
        return 'assets/' . $input;
    }

    public static function asset_img_url($input, $key = null){
        return base64_decode($input);
    }

    public static function file_url ($input, $key = null){
        return str_replace(['+','/'],['-','_'],base64_encode($input));
    }

    public static function file_img_url($input, $key = null){
        return base64_decode(str_replace(['-','_'],['+','/'],$input));
    }

    public static function customer_login_link ($input){
        return null;
    }

    public static function customer_logout_link ($input){
        return null;
    }

    public static function customer_register_link ($input){
        return null;
    }

    public static function global_asset_url($input){
        return null;
    }

    public static function image_url($input){
        return null;
    }

    public static function link_to($input){
        return null;
    }
    
    public static function link_to_vendor ($input){
        return null;
    }

    public static function link_to_type ($input){
        return null;
    }

    public static function link_to_tag($input){
        return null;
    }

    public static function link_to_add_tag($input){
        return null;
    }
    
    public static function link_to_remove_tag ($input){
        return null;
    }
        
    public static function payment_type_img_url ($input){
        return null;
    }

    public static function shopify_asset_url($input){
        return null;
    }
    
    public static function sort_by ($input){
        return null;
    }
        
    public static function url_for_type  ($input){
        return null;
    }

    public static function url_for_vendor ($input){
        return null;
    }
        
    public static function within ($input){
        return null;
    }
}