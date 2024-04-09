<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace ShopifyTemplate\Tags;

use Liquid\Nodes\Node;
use Liquid\Context;
use Liquid\Environment;
use Liquid\Parser;
use Liquid\TokenStream;
use ShopifyTemplate\Theme;

class TagRender extends Node
{
	function parse()
	{
		parent::parse();
		$this->options['file']['path'] = ShopifyTemplate::PATH_SNIPPET;
	}
}
