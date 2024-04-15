<?php

namespace ShopifyTemplate\Drops;

class Font extends \Liquid\Models\Drop
{
    public $allowModifies = [
        'style' => ['normal', 'italic', 'oblique'],
        'weight' => ['normal', 'bold', 'lighter', 'bolder'],
        'display' => ['auto', 'block', 'swap', 'fallback',  'optional'],
        'url' => ['woff', 'woff2']
    ];

    static $families = null;

    function __construct($handle)
    {
        $this->attributes = static::getFont($handle);
    }

    static function getFont($handle)
    {
        if (static::$families === null) {
            $data = json_decode(file_get_contents(__DIR__ . '/../../../assets/json/shopify_font_families.json'), true);
            $list = [];
            foreach ($data as $family) {
                foreach ($family['variants'] as $font) {
                    $list[$font['handle']] = $font;
                }
            }
            static::$families = $list;
        }

        return static::$families[$handle] ?? null;
    }

    function modify($key, $value)
    {

        if ($key == 'style') {
            $this->style($value);
        }

        if ($key == 'weight') {
            $this->weight($value);
        }
        return $this;
    }

    function style($value)
    {
        return $this->setAttribute('style', in_array($value, $this->allowModifies['style']) ? $value : '');
    }

    function weight($value)
    {
        $weight = $this->weight;
        if (in_array($value, $this->allowModifies['weight'])) {
            $weight = $value;
            $weight = $weight == 'normal' ? 400 : ($weight == 'bold' ? 700 : $weight);
            return $this->setAttribute('weight', $weight);
        }

        $weight = is_numeric($weight) ? $weight : ($weight == 'bold' ? 700 : 400);
        if (strpos($value, '+') === 0 && is_numeric($num = substr($value, 1))) {
            if (ceil($num / 100 * 100) == (int)$num) {
                $weight += $num;
            }
            return $this->setAttribute('weight', $weight);
        }
        return 400;
    }


    function url($key)
    {
        if (in_array($key, $this->allowModifies['url'])) {
            return $this->urls[$key];
        } else {
            return 'Liquid error: font_url only supports the woff2 and woff formats';
        }
    }

    function toHtml($data = [])
    {
        $display = null;
        if (isset($data['font_display'])) {
            if (in_array($data['font_display'], $this->allowModifies['display'])) {
                $display = $data['font_display'];
            } else {
                return 'Liquid error: font_display can only be set to auto, block, swap, fallback, and optional';
            }
        }

        $displayHtml = $display ? ("\n\tfont-display:" . $display) : '';

        return '@font-face {
    font-family: ' . $this->family . ';
    font-weight: ' . $this->weight . '; ' . $displayHtml . '
    font-style: normal;
    src: url("' . $this->url('woff2') . '") format("woff2"),
            url("' . $this->url('woff') . '") format("woff");
}';
    }
}
