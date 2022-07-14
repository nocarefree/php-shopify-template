<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyLiquid\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;



class TagStyle extends Block
{

	public function render(Context $context){
		return "<style>" . parent::render($context) . "<style>";
	}

}
