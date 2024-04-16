<?php

namespace ShopifyTemplate\Drops;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Settings extends \Liquid\Models\Drop
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

        $this->schema = $schema;
        $this->data = $data;

        $this->validate();
    }

    public function validate()
    {

        $attributes = [];

        $_settings = [];
        $colorSchemeGroup = null;
        foreach ($this->schema as $settingsGroup) {
            if (isset($settingsGroup['theme_name'])) {
                continue;
            }

            foreach ($settingsGroup['settings'] as $settingSchema) {

                if (!isset($settingSchema['id'])) {
                    continue;
                }

                $id = $settingSchema['id'];
                $type = $settingSchema['type'];
                $default = $settingSchema['default'] ?? null;
                $config = $this->data[$id] ?? null;

                if (!$default && !$config && !$type) {
                    continue;
                }


                if ($type == 'color_scheme_group') {
                    // new "\ShopifyTemplate\Drops\\" . Str::studly($type);
                    $colorSchemeGroup = new ColorSchemeGroup(['id' => $id, 'definition' => $settingSchema['definition'], 'settings' => $config]);

                    $this->attributes[$id] = $colorSchemeGroup;
                } else {
                    $_settings[] = ['id' => $id, 'type' => $type, 'default' => $default, 'settings' => $config];
                }
            }
        }


        if (!$colorSchemeGroup) {
            throw new \Exception("ColorSchemeGroup");
        }

        foreach ($_settings as $setting) {
            $config = $setting['settings'] ?: $setting['default'];
            $type = $setting['type'];
            $id = $setting['id'];

            switch ($type) {
                case "color_scheme":
                    $this->attributes[$id] = $colorSchemeGroup->get($config);
                    break;
                case "image_picker":
                    $this->attributes[$id] = new Image($config);
                    break;
                case "font_picker":
                    $this->attributes[$id] = new Font($config);
                    break;
                default:
                    $dropName = "\ShopifyTemplate\Drops\\" . Str::studly($type);
                    if (class_exists($dropName)) {
                        $this->attributes[$id] = new $dropName($config);
                    } else {
                        $this->attributes[$id] = $config;
                    }
                    break;
            }
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(array_keys($this->attributes));
    }
}
