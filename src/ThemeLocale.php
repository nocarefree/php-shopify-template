<?php 

namespace Ncf\ShopifyTemplate;

use Illuminate\Support\Arr;

class ThemeLocale{

    public $data = [];

    public static function translate($input, $data = []){
        $content = Arr::get(static::$data, $input);
        if(is_array($data)){
            foreach($data as $key=>$value){
                $content = preg_replace("/{{\s*".preg_quote($key,'/')."\s*}}/", $value, $content);
            } 
        }
        return $content;
    }


}