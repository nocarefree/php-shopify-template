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

class TagSections extends Node
{

	function parse(TokenStream $stream)
	{
		$this->section = null;
		if (preg_match(Parser::REGEX_STRING, $this->expression, $matches)) {
			$this->section = $matches[1] ?: $matches[2];
		} else {
			$stream->addSyntaxError("Syntax error in tag 'section' - Section name must be a quoted string");
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
		if (!$this->section) {
			return "<!-- Syntax error in tag 'section' - Section name must be a quoted string -->";
		}

		return call_user_func_array([$context->env(), 'renderSectionGroup'], [$this->section]);
	}
}
