<?php

namespace ShopifyTemplate\Drops;

use ShopifyTemplate\Theme;
use Liquid\Nodes\Document;

class ThemeSectionDrop extends \Liquid\Models\Drop
{

    function __construct($theme, $schema, $config)
    {
        $this->theme = $theme;
        $this->attributes = [];

        if ($schema) {

            if ($config['settings'] && $schema['settings']) {
                $this->attributes['settings'] = $this->settingsToAttributes($schema['settings'], $config['settings']);
            }

            if ($config['block_order'] && $schema['blocks']) {
                $this->attributes['blocks'] = $this->blockToAttributes($schema['blocks'], $config);
            }
        }
    }

    private function blockToAttributes($schemaBlocks, $config)
    {
        $blockTypes = [];
        foreach ($schemaBlocks as $row) {
            $blockTypes[$row['type']] = $row;
        }

        $blocks = [];
        foreach ($config['block_order'] as $id) {
            $type = $config['blocks'][$id]['type'] ?? '';
            $settings = $config['blocks'][$id]['settings'] ?? [];

            $blocks[] = $this->settingsToAttributes($blockTypes[$type]['settings'] ?? [], $settings);
        }
        return $blocks;
    }

    private function settingsToAttributes($schemaSettings, $settings)
    {
        $types = [];
        foreach ($schemaSettings as $row) {
            if ($row['id']) {
                $types[$row['id']] = $row['type'];
            }
        }

        $_settings = [];
        foreach ($settings as $id => $setting) {
            if (isset($types[$id])) {
                $_settings[$id] = $this->theme->getThemeDrop($types[$id]['type'], $setting);
            }
        }
        return $settings;
    }
}
