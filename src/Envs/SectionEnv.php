<?php

namespace ShopifyTemplate\Envs;


class SectionEnv extends BaseEnv
{

    public function __construct($fileSystem = null)
    {
        parent::__construct($fileSystem);

        $this->registerTags(static::getInnerTags());
    }

    public static function getInnerTags()
    {
        return [
            //Config
            'schema' => \ShopifyTemplate\Tags\TagSchema::class,

            //section template
            'javascript' => \ShopifyTemplate\Tags\TagJavascript::class,
            'stylesheet' => \ShopifyTemplate\Tags\TagStylesheet::class,
        ];
    }
}
