<?php



namespace Ncf\ShopifyLiquid\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;

/**
 *
 * Example:
 *
 *     {% javascript %} This will be ignored {% endjavascript %}
 */
class TagJavascript extends Block
{

    public function render(Context $context){
        $context->registers['javascript'] = parent::render($context) ;
        return '';
    }
    
}
