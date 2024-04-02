<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace ShopifyLiquid\Tags;

use Ncf\ShopifyLiquid\ShopifyTemplate;


class TagRender extends \Liquid\Tags\TagRender
{
	function parse()
	{
		parent::parse();
		$this->options['file']['path'] = ShopifyTemplate::PATH_SNIPPET;
	}
}
