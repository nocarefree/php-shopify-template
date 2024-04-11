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
use Liquid\Nodes\Document;
use Liquid\Context;
use Liquid\TokenStream;
use Liquid\Parser;
use Liquid\Exceptions\SyntaxError;

class TagSection extends Node
{

	function parse(TokenStream $stream)
	{
		if (preg_match(Parser::REGEX_STRING, $this->expression, $matches)) {
			$this->section = $matches[1] ?: $matches[2];
		} else {
			throw new SyntaxError("<!-- Syntax error in tag 'section' - Section name must be a quoted string -->");
		}
		return $this;
	}

	/**
	 * Renders the node
	 *
	 * @param Context $context
	 *
	 * @return string
	 */
	public function render(Context $context): string
	{
		$document = $context->env()->getCache($this->section);

		if ($document && $document instanceof Document) {
			return $document->render($context);
		} else {
			return "Error in tag 'section' - '" . "' is not a valid section type";
		}
	}
}
