<?php



namespace ShopifyLiquid\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;
use ShopifyLiquid\Nodes\SectionAttributeNode;

class TagStylesheet extends SectionAttributeNode
{

    public function render(Context $context)
    {
        $context->registers['stylesheet'] = parent::render($context);
        return '';
    }
}
