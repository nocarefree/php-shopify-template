<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

<<<<<<< HEAD
namespace ShopifyLiquid\Tags;
=======
namespace Ncf\ShopifyTemplate\Tags;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe

use Liquid\Nodes\Block;
use Liquid\Context;



class TagStyle extends Block
{

<<<<<<< HEAD
	public function render(Context $context)
	{
		return "<style>" . parent::render($context) . "<style>";
=======
	public function render(Context $context){
		return "<style>" . parent::render($context) . "</style>";
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
	}
}
