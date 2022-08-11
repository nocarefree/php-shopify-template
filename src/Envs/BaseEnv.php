<?php 

namespace Ncf\ShopifyTemplate\Envs;

use Liquid\Environment;

class BaseEnv extends Environment{

    public function __construct($fileSystem = null)
    {
        parent::__construct($fileSystem);

        $this->registerTags([
            //Template
            'render'=> \Ncf\ShopifyTemplate\Tags\TagRender::class,
            'layout'=> \Ncf\ShopifyTemplate\Tags\TagLayout::class,
            'section'=> \Ncf\ShopifyTemplate\Tags\TagSection::class,
    
            //Iteration
            'paginate'=> \Ncf\ShopifyTemplate\Tags\TagPaginate::class,
    
            //Html
            'form'=> \Ncf\ShopifyTemplate\Tags\TagForm::class,
            'style'=> \Ncf\ShopifyTemplate\Tags\TagStyle::class,
        ]);
    }

    
}