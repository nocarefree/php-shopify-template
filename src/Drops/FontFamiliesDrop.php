<?php 

namespace Ncf\ShopifyTemplate\Drops;

class FontFamiliesDrop extends \Liquid\Models\Drop{

    function __construct()
    {
        $data = json_decode(file_get_contents(__DIR__.'/../../assets/json/shopify_font_families.json'),true);
        $this->attributes = $data['font_families']??[];
    }
    
    function getAttribute($key)
    {
        foreach($this->attributes as $font_family){
            foreach($font_family['variants'] as $font){
                if($font['handle'] == $key){
                    return $font;
                }
            }
        }
        return null;
    }

}