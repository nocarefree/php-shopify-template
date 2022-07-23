<?php 

namespace Ncf\ShopifyLiquid\Filters;

use Illuminate\Support\Str;

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

    public static function camelcase($input){
        return Str::camel($input);
    }

    public static function capitalize($input){
        return Str::ucfirst($input);
    }

    public static function downcase($input){
        return Str::lower($input);
    }

    public static function escape ($input){
        return is_string($input) ? htmlentities($input, ENT_QUOTES, null, false) : $input;
    }

    public static function escape_once ($input){
        return is_string($input) ? htmlentities($input, ENT_QUOTES, null, false) : $input;
    }

    public static function handleize($input){
        $chars = [
            '~','!','@','#','$','%','^','&','*','(',')','=','+','[',']',
            '{','}','.',',','/','\\','\'','"','|',
            '，','。','【','】','·','‘','’','；','：','“','”','、','？','—',
            '\t','\s','\t'
        ];
        return Str::lower(trim(preg_replace('/-+/','-',str_replace($chars,'-',$input))),'-');
    }

    public static function handle($input){
        return self::handleize($input);
    }

    public static function md5($input){
        return hash('md5',$input);
    }
    
    public static function sha1($input){
        return hash('sha1',$input);
    }

    public static function sha256($input){
        return hash('sha256',$input);
    }

    public static function hmac_sha1 ($input){
        return hash_hmac('sha1', $input,'secret');
    }

    public static function hmac_sha256($input){
        return hash_hmac('sha256', $input,'secret');
    }

    public static function newline_to_br ($input){
        return str_replace("\n","\n<br>",$input);
    }
    
    public static function pluralize($input, $pix, $pix2){
        return $input == 0 ? "": ($input!=1?$pix2:$pix);
    }

    public static function prepend($input, $value){
        return $value . $input;
    }

    public static function remove ($input, $search){
        return Str::replace($search, "", $input);
    }

    public static function remove_first($input, $search){
        return Str::replaceFirst($search, "", $input);
    }

    public static function replace($input, $search ,$replace = ''){
        return Str::replace($search, $replace, $input);
    }
    
    public static function replace_first($input, $search ,$replace = ''){
        return Str::replaceFirst($search, $replace, $input);
    }

    public static function replace_last($input, $search ,$replace = ''){
        return Str::replaceLast($search, $replace, $input);
    }
        

    public static function strip ($input){
        return trim($input);
    }

    public static function lstrip ($input){
        return ltrim($input);
    }

    public static function rstrip ($input){
        return rtrim($input);
    }

    public static function url_escape ($input){
        return str_replace(['+','%2B','%26','%40'],['%20','+','&','@'],urlencode($input));
    }


    public static function url_param_escape ($input){
        return str_replace(['+','%2B','%40'],['%20','+','@'],urlencode($input));
    }



}