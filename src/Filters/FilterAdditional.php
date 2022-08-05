<?php 

namespace Ncf\ShopifyTemplate\Filters;


//  https://shopify.dev/api/liquid/filters/array-filters

class FilterAdditional{


    public static function default($input , $value = ''){
        if(is_array($value)){
            if(isset($value['allow_false']) && $value['allow_false'] = true){
                $value = $input == false ? false : $value[0];
            }else{
                $value = $value[0];
            }
        }
        return $input ? $input : $value;
    }

    public function default_errors(array $error ,$array){
        return 'Please enter a valid ' .implode(' ',$error['messages']);
    }

    public function default_pagination($input ,$data = []){
        return '
        <span class="page current">1</span>
        <span class="page"><a href="/collections/all?page=2" title="">2</a></span>
        <span class="page"><a href="/collections/all?page=3" title="">3</a></span>
        <span class="deco">…</span>
        <span class="page"><a href="/collections/all?page=17" title="">10</a></span>
        <span class="next"><a href="/collections/all?page=2" title="">Next »</a></span>
        ';
    }

    public function format_address($input , string $key, $value){
        return '
            <p>
            Elizabeth Gonzalez<br>
            1507 Wayside Lane<br>
            San Francisco<br>
            CA<br>
            94103<br>
            United States
            </p>
        ';
    }

    public function highlight($input , string $key){
        return str_replace($key, '<strong class="highlight">'.$key.'</strong>', $input);
    }

    // public function t($input, $data = []){
    //     return $this->app->translate($input, $data);
    // }

    public static function json($input){
        return $input?json_encode($input):'';
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
           return "Unknown SVG placeholder 'collection-7'";
        }

        $content = file_get_contents(__DIR__.'/../../assets/icons/'.$input.'.svg');
        if(is_string($class)){
            $content = str_replace('<svg','<svg class="'.$class.'"', $content);
        }
        return $content;
    }


}