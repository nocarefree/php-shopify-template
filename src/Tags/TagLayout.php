<?php



<<<<<<< HEAD
namespace ShopifyLiquid\Tags;
=======
namespace Ncf\ShopifyTemplate\Tags;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe

use Liquid\Nodes\Node;
use Liquid\Context;

/**
 *
 * Example:
 *
 *     {% javascript %} This will be ignored {% endjavascript %}
 */
class TagLayout extends Node
{
<<<<<<< HEAD
    public function render(Context $context)
    {
        $context->registers['layout'] = $this->options['expression'];
=======
    public function render(Context $context){
        $context->registers['layout'] = $context->get($this->options['expression']);
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
        return '';
    }
}
