<?php

namespace ShopifyTemplate\Drops;


class ThemeDrop extends \Liquid\Models\Drop
{


    function __construct($theme, $schema = [], $settings = [])
    {

        $this->theme = $theme;
        $this->theme_info = array_shift($schema);
        $this->settingsSchema = $schema;

        $settings = array_merge($settings['presets']['Default'] ?? [], $settings['current'] ?? []);

        $this->sections = $settings['sections'];
        unset($settings['sections']);
        $this->attributes = $this->settingsToAttributes($settings);
    }

    private function settingsToAttributes($settings)
    {
        $types = [];
        foreach ($this->settingsSchema as $group) {
            foreach ($group['settings'] as $row) {
                if (isset($row['id'])) {
                    $types[$row['id']] = $row['type'];
                }
            }
        }

        $_settings = [];
        foreach ($settings as $id => $setting) {
            if (isset($types[$id])) {
                $_settings[$id] = $this->theme->getThemeDrop($types[$id], $setting);
            }
        }
        return $_settings;
    }

    public function __toString()
    {
        $data = [];
        foreach ($this->attributes as $key => $value) {
            if (!is_object($value)) {
                $data[$key] = $value;
            }
        }
        return json_encode($data);
    }
}
