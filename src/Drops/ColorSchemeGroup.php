<?php

namespace ShopifyTemplate\Drops;

use IteratorIterator;
use Traversable;

class ColorSchemaGroupDrop extends \Liquid\Models\Drop
{
    protected $schemes;

    function __construct($config)
    {
        $definition = $config['definition'];
        if (empty($definition)) {
            throw new \Exception("definition empty");
        }

        foreach ($definition as $id => $value) {
            $this->schemes[$id] = new ColorSchema(['id' => $id, 'setting' => $value['settings']]);
        }
    }

    function getIterator(): \Traversable
    {
        return new IteratorIterator($this->schemes);
    }
}
