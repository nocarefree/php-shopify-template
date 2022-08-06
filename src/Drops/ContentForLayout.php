<?php

namespace Ncf\ShopifyTemplate\Drops;

class ContentForLayout extends \Liquid\Models\Drop{
    function __construct($context)
    {
        $this->context = $context;
    }

    function setSections($sections){
        $this->sections = $sections;
    }

    function __toString(){
        foreach($this->sections as $section){
            $this->context->registers['sections'][] = $section;
        }
        return  '<!-- BEGIN template -->' .$this->content .'<!-- END template -->';
    }
}