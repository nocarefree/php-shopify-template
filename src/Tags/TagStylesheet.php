<?php



namespace Ncf\ShopifyLiquid\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;

/**
 *
 * Example:
 *
 *     {% stylesheet %} This will be ignored {% stylesheet %}
 */
class TagStylesheet extends Block
{

    public function render(Context $context){
        $context->registers['stylesheet'] = parent::render($context);
        return '';
    }
    
}
