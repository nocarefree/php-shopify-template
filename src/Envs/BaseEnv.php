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

        foreach([
            Filters\FilterArray::class,
            Filters\FilterColor::class,
            Filters\FilterFont::class,
            Filters\FilterHtml::class,
            Filters\FilterMath::class,
            Filters\FilterMedia::class,
            Filters\FilterMetafield::class,
            Filters\FilterMoney::class,
            Filters\FilterString::class,
            Filters\FilterUrl::class
        ] as $filter){
            $this->registerFilters($filter);
        }
    }

    
}