<?php

namespace Ncf\ShopifyLiquid;

class ContentForLayout{
    function __construct($context , $template)
    {
        $this->context = $context;
        $this->template = $template;
    }

    function __toString(){

        $content = $this->template['content'];
        foreach($this->template['sections'] as $section){
            $this->context->registers['sections'][] = $section;
            $this->context->registers['sectionsData'][] = $section;
            $this->context->registers['sectionsSchema'][] = $section;
        }
        return  '<!-- BEGIN template -->' .$content .'<!-- END template -->';
    }
}