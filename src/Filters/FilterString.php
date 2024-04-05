<?php

namespace ShopifyLiquid\Filters;

use Illuminate\Support\Str;

// https://shopify.dev/api/liquid/filters/html-filters

class FilterString
{

    public static function base64_encode($input, $key = null)
    {
        return base64_encode($input);
    }

    public static function base64_decode($input, $key = null)
    {
        return base64_decode($input);
    }

    public static function base64_url_safe_encode($input, $key = null)
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($input));
    }

    public static function base64_url_safe_decode($input, $key = null)
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $input));
    }

    public static function camelcase($input)
    {
        return null;
    }

    public static function capitalize($input)
    {
        return null;
    }

    public static function downcase($input)
    {
        return null;
    }

    public static function escape($input)
    {
        return null;
    }

    public static function handleize($input)
    {
        return null;
    }

    public static function md5($input)
    {
        return null;
    }

    public static function sha1($input)
    {
        return null;
    }

    public static function sha256($input)
    {
        return null;
    }

    public static function hmac_sha1($input)
    {
        return null;
    }

    public static function hmac_sha256($input)
    {
        return null;
    }

    public static function newline_to_br($input)
    {
        return null;
    }

    public static function pluralize($input)
    {
        return null;
    }

    public static function prepend($input)
    {
        return null;
    }

    public static function remove($input)
    {
        return null;
    }

    public static function remove_first($input)
    {
        return null;
    }

    public static function replace($input)
    {
        return null;
    }

    public static function replace_first($input)
    {
        return null;
    }

    public static function slice($input)
    {
        return null;
    }

    public static function split($input)
    {
        return null;
    }
        

    public static function strip($input)
    {
        return null;
    }

    public static function lstrip($input)
    {
        return null;
    }

    public static function rstrip($input)
    {
        return null;
    }

    public static function strip_html($input)
    {
        return null;
    }

    public static function truncate($input)
    {
        return null;
    }

    public static function strip_newlines($input)
    {
        return null;
    }

    public static function truncatewords($input)
    {
        return null;
    }

    public static function upcase($input)
    {
        return null;
    }

    public static function url_encode($input)
    {
        return null;
    }

    public static function url_escape($input)
    {
        return null;
    }

    public static function url_param_escape($input)
    {
        return null;
    }
}
