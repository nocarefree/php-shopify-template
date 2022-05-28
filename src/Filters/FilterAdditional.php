<?php 

namespace Ncf\ShopifyLiquid\Filters;

use Liquid\LiquidException;

//  https://shopify.dev/api/liquid/filters/array-filters

class FilterAdditional{


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

    public static function placeholder_svg_tag($input, $class){
        $allowSvgs = [
            'collection-1',
            'collection-2',
            'collection-3',
            'collection-4',
            'collection-5',
            'collection-6',
            'lifestyle-1',
            'lifestyle-2',
            'image',
            'product-1',
            'product-2',
            'product-3',
            'product-4',
            'product-5',
            'product-6',
        ];

        if(!in_array($input,$allowSvgs)){
            throw new LiquidException("Unknown SVG placeholder 'collection-7'");
        }

        $content = file_get_contents(dirname(dirname(__DIR__)).'/icons/'.$input.'.svg');
        if(is_string($class)){
            $content = str_replace('<svg','<svg class="'.$class.'"', $content);
        }
        return $content;
    }


}