<?php

namespace ShopifyTemplate\Filters;


// https://shopify.dev/api/liquid/filters/math-filters

class FilterMath
{


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
        return $input + $value;
    }

    public static function times($input, $operand)
    {
        return (float)$input * (float)$operand;
    }
}
