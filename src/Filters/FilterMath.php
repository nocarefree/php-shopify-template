<?php

<<<<<<< HEAD
namespace ShopifyLiquid\Filters;
=======
namespace Ncf\ShopifyTemplate\Filters;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe


// https://shopify.dev/api/liquid/filters/math-filters

class FilterMath
{


<<<<<<< HEAD
    public static function abs($input)
    {
        return abs($input);
    }

    public static function at_most($input, $value)
    {
        return max($input, $value);
    }

    public static function at_least($input, $value)
    {
        return min($input, $value);
    }

    public static function divided_by($input, $value)
    {
        return is_numeric($value) && $value != 0 ? ceil($input / $value) : '';
    }

    public static function minus($input, $value)
    {
        return $input - $value;
    }

    public static function plus($input, $value)
    {
=======
    public static function abs($input){
        $input = is_numeric($input) ? $input : 0;
        return abs($input);
    }

    public static function at_most($input, $value){
        $input = is_numeric($input) ? $input : 0;
        $value = is_numeric($value) ? $value : 0;
        return min($input, $value);
    }

    public static function at_least($input, $value){
        $input = is_numeric($input) ? $input : 0;
        $value = is_numeric($value) ? $value : 0;
        return max($input, $value);
    }

    public static function divided_by($input, $value){

        $input = is_numeric($input) ? $input : 0;
        $value = is_numeric($value) ? $value : 0;

        return $value !=0 ? ceil($input / $value) : '';
    }

    public static function minus($input, $value){
        $input = is_numeric($input) ? $input : 0;
        $value = is_numeric($value) ? $value : 0;
        return $input - $value;
    }

    public static function plus($input, $value){
        $input = is_numeric($input) ? $input : 0;
        $value = is_numeric($value) ? $value : 0;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
        return $input + $value;
    }

    public static function times($input, $operand)
    {
        return (float)$input * (float)$operand;
    }
}
