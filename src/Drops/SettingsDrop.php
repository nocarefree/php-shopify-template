<?php

namespace ShopifyTemplate\Drops;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SettingsDrop extends \Liquid\Models\Drop
{

    protected $schema;
    protected $data;

    public $types = [
        "checkbox",
        "number",
        "radio",
        "range",
        "select",
        "text",
        "textarea",
        "article",
        "blog",
        "collection",
        "collection_list",
        "color",
        "color_background",
        "color_scheme",
        "color_scheme_group",
        "font_picker",
        "html",
        "image_picker",
        "inline_richtext",
        "link_list",
        "liquid",
        "page",
        "product",
        "product_list",
        "richtext",
        "text_alignment",
        "url",
        "video",
        "video_url",
    ];


    public function __construct($schema, $datas)
    {

        $data = $datas['current'];
        if (is_string($data)) {
            $data = $datas['presets'][$data];
        }
        $this->schema = $schema[1];
        $this->data = $data;
    }

    public function validate()
    {

        $attributes = [];
        foreach ($this->schema as $settingsGroup) {
            foreach ($settingsGroup['settings'] as $settingSchema) {

                $id = $settingSchema['id'];
                $type = $settingSchema['type'];
                $default = $settingSchema['default'] ?? null;
                $setting = $this->data[$id] ?? null;

                if (!$default && !$setting) {
                    continue;
                }

                if (in_array($type, $this->types)) {

                    $dropName =  "\ShopifyTemplate\Drops\Settings\\" . Str::studly($type);

                    if (class_exists($dropName)) {

                        $config = Arr::only($settingSchema, ['id', 'default', 'unit', 'definition']);
                        if ($setting) {
                            $config['setting'] = $setting;
                        }
                        $attributes[$id] = new $dropName($config);
                    } else {
                        $attributes[$id] = $settings ?? $default ?? null;
                    }
                }
            }
        }
        $this->attributes = $attributes;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(array_keys($this->attributes));
    }
}
