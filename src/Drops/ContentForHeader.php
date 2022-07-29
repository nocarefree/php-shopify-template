<?php

namespace Ncf\ShopifyTemplate\Drops;

class ContentForHeader extends \Liquid\Models\Drop{
    function __construct($app)
    {
        $this->context = $context;
        $this->content = $content;

        $this->sections = $this->context->registers['sections']??[];
        unset($this->context->registers['sections']);
    }

    function __toString(){
        foreach($this->sections as  $section){
            $this->context->registers['sections'][] = $section;
        }
        return  '<!-- BEGIN template -->' .$this->content .'<!-- END template -->';
    }
}