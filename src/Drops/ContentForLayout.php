<?php

namespace ShopifyTemplate\Drops;

class ContentForLayout extends \Liquid\Models\Drop
{
    function __construct($content, $context)
    {
        $this->content = $content;
        $this->context = $context;

        $this->sections = $this->context->registers['sections'] ?? [];
        $this->context->registers['sections'] = [];
    }

    function __toString()
    {
        return  '<!-- BEGIN template -->' . $this->toHtml() . '<!-- END template -->';
    }

    function toHtml()
    {
        $this->context->registers['sections'] = array_merge($this->context->registers['sections'], $this->sections);
        return  $this->content;
    }
}
