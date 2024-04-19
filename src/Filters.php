<?php

namespace ShopifyTemplate;

use Illuminate\Support\Arr;

// https://shopify.dev/api/liquid/filters/font-filters

class Filters
{
    protected $locale;

    public function __construct($config = [])
    {
        $this->locale = $config['locale'] ?? [];
    }

    public function t($input, $data = [])
    {
        $input = Arr::get($this->locale, $input, '');
        foreach ($data as $key => $value) {
            $input = preg_replace("/{{\s*" . preg_quote($key, '/') . "\s*}}/", $value, $input);
        }
        return $input;
    }
}
