<?php

namespace ShopifyTemplate\Envs;

use Liquid\Environment;

class BaseEnv extends Environment
{

    public function __construct($fileSystem = null)
    {
        parent::__construct($fileSystem);

        $this->registerTags([
            //Template
            'render' => \ShopifyTemplate\Tags\TagRender::class,
            'layout' => \ShopifyTemplate\Tags\TagLayout::class,
            'section' => \ShopifyTemplate\Tags\TagSection::class,

            //Iteration
            'paginate' => \ShopifyTemplate\Tags\TagPaginate::class,

            //Html
            'form' => \ShopifyTemplate\Tags\TagForm::class,
            'style' => \ShopifyTemplate\Tags\TagStyle::class,
        ]);
    }
}
