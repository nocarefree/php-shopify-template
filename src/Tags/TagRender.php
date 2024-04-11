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

use Liquid\Tags\TagRender as BaseRender;
use Liquid\Context;
use Liquid\Nodes\Document;
use Liquid\Parser;
use Liquid\TokenStream;
use ShopifyTemplate\Theme;

class TagRender extends BaseRender
{
	public function parseDocument($stream)
	{
	}

	public function render(Context $context): string
	{
		$this->document = $context->env()->getCache($this->render);

		if ($this->document && $this->document instanceof Document) {
			return parent::render($context);
		} else {
			return "Liquid error: Could not find {" . $this->render . "}.liquid";
		}
	}
}
