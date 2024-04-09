<?php



namespace ShopifyTemplate\Tags;

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
    public function render(Context $context)
    {
        $context->registers['layout'] = $this->options['expression'];
        return '';
    }
}
