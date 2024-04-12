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
use Liquid\Nodes\Document;
use Liquid\Parser;
use Liquid\TokenStream;
use ShopifyTemplate\Theme;

class TagRender extends Node
{
	public function parse(TokenStream $stream)
	{

		if (preg_match(Parser::REGEX_STRING, $this->expression, $matches)) {
			$this->render = $matches[1] ?: $matches[2];

			$index = strlen($matches[0]);

			if (preg_match('/\G\s?(with|,|for)\s+(.*)/', $this->expression, $matches, 0, $index)) {
				switch ($matches[1]) {
					case 'with':
						$this->with = Parser::parseAs($matches[2]);
						break;
					case ',':
						$this->parameters = Parser::parseParameters($matches[2]);
						break;
					case 'for':
						$this->for = Parser::parseAs($matches[2]);
						break;
				}
			}
		} else if (preg_match(Parser::REGEX_VAR, $this->expression, $matches)) {
			$this->render = $matches[0];
		} else {
			$this->throwEmptyExpression("<!-- Syntax error in tag 'render' - Template name must be a quoted string -->");
		}
		return $this;
	}

	public function render(Context $context): string
	{


		$result = '';
		if (isset($this->for) && !empty($this->for)) {
			$collections = $context->get($this->for[1]);

			if ($collections) {
				foreach ($collections as $value) {
					$result .= call_user_func_array([$context->env(), 'renderSnippet'], [$this->render, [$this->for[0] => $value]]);
				}
			}
		} else {
			$context->push();
			$data = [];
			if (isset($this->parameters)) {
				foreach ($this->options['parameters'] as $value) {
					$data[$value[0]] = $value[1];
				}
			}

			$result .= call_user_func_array([$context->env(), 'renderSnippet'], [$this->render, $data]);
		}

		return $result;

		// if ($this->document && $this->document instanceof Document) {
		// 	return parent::render($context);
		// } else {
		// 	return "Liquid error: Could not find {" . $this->render . "}.liquid";
		// }
	}
}
