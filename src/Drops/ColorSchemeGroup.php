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

        foreach ($config['settings'] as $id => $value) {
            //$_config = Arr:only($value,["id","type",""])
            $this->schemes[$id] = new ColorScheme($value['settings'], $definition);
        }
    }


    function get($config)
    {
        return $this->schemes[$config];
    }

    function getIterator(): \Traversable
    {
        return new IteratorIterator($this->schemes);
    }
}
