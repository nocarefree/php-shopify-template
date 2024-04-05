<?php



namespace ShopifyLiquid\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;


class TagJavascript extends SectionAttributeNode
{

    public function render(Context $context)
    {
        $context->registers['javascript'] = parent::render($context);
        return '';
    }
}
