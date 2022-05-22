<?php 

namespace Ncf\Liquid;

use Liquid\Tag\TagDecrement;
use Liquid\Template;
use Ncf\Liquid\Filters\FilterAdditional;

class ShopifyTemplate extends Template{

    private $innerTags = [
        'decrement'=> Tags\TagDecrement::class,
        'increment'=> Tags\TagIncrement::class,
        'layout'=> Tags\TagLayout::class,
        'paginate'=> Tags\TagPaginate::class,
        'style'=> Tags\TagStyle::class,
        'tablerow'=> Tags\TagTablerow::class,
    ];
    
    private $innerFilters = [
        Filters\FilterAdditional::class,
        Filters\FilterArray::class,
        Filters\FilterColor::class,
        Filters\FilterFont::class,
        Filters\FilterHtml::class,
        Filters\FilterMath::class,
        Filters\FilterMedia::class,
        Filters\FilterMetafield::class,
        Filters\FilterMoney::class,
        Filters\FilterString::class,
        Filters\FilterU::class,
    ];

    function __construct($path = null, $cache = null)
    {

        parent::__construct($path, $cache);

        foreach($this->innerTags as $name => $tag){
            $this->registerTag($name, $tag);
        }

        foreach($this->innerFilters as $filter){
            $this->registerFilter($filter);
        }
    }

}