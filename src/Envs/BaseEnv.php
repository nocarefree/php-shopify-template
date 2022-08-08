<?php 

namespace Ncf\ShopifyTemplate\Envs;

use Liquid\Environment;

class BaseEnv extends Environment{

    public function __construct($fileSystem = null)
    {
        parent::__construct($fileSystem);

        $this->registerTags([
            //Template
            'render'=> Tags\TagRender::class,
            'layout'=> Tags\TagLayout::class,
            'section'=> Tags\TagSection::class,
    
            //Iteration
            'paginate'=> Tags\TagPaginate::class,
    
            //Html
            'form'=> Tags\TagForm::class,
            'style'=> Tags\TagStyle::class,
        ]);
    }

    
}