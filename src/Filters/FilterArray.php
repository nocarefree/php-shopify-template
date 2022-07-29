<?php 

namespace Ncf\ShopifyTemplate\Filters;


//  https://shopify.dev/api/liquid/filters/array-filters

class FilterArray{


    public static function concat($input ,$array){
        if ($input instanceof \Traversable && $array instanceof \Traversable) {
			$input = iterator_to_array($input);
            $array = iterator_to_array($array);
		}
		if (!is_array($input) || !is_array($array)) {
			return $input;
		}

        return array_merge($input, $array);
    }

    public static function where($input , string $key, $value){
        return array_filter($input, function($item) use ($key, $value){
            return (isset($item->$key) && $item->$key == $value) 
                || (isset($item[$key]) && $item[$key] == $value);
        });
    }


}