<?php

<<<<<<< HEAD


namespace ShopifyLiquid\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;

=======
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyTemplate\Tags;

use Ncf\ShopifyTemplate\Nodes\SectionAttributeNode;

class TagJavascript extends SectionAttributeNode
{

<<<<<<< HEAD
    public function render(Context $context)
    {
        $context->registers['javascript'] = parent::render($context);
        return '';
    }
=======
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
}
