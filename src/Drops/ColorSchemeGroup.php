<?php

namespace ShopifyTemplate\Drops;

use IteratorIterator;

class ColorSchemeGroup extends \Liquid\Models\Drop
{
    protected $schemes;

    function __construct($config)
    {
        $definition = $config['definition'];
        if (empty($definition)) {
            throw new \Exception("definition empty");
        }

        foreach ($config['setting'] as $id => $value) {
            //$_config = Arr:only($value,["id","type",""])
            $this->schemes[$id] = new ColorScheme(['id' => $id, 'setting' => $value['settings'], 'definition' => $definition]);
        }
    }

    function getIterator(): \Traversable
    {
        return new IteratorIterator($this->schemes);
    }
}
