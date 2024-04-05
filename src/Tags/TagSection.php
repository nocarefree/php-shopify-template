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

use Liquid\Nodes\Node;
use Liquid\Context;
use Liquid\Environment;
use Liquid\Parser;
use Liquid\TokenStream;

class TagSection extends Node
{

	function parse()
	{
		parent::parse();
		$this->options['file']['path'] = ShopifyTemplate::PATH_SECTION;
	}

	/**
	 * Renders the node
	 *
	 * @param Context $context
	 *
	 * @return string
	 */
	public function render(Context $context)
	{
		if (isset($context->registers['in_section']) && $context->registers['in_section']) {
			return '';
		}

		$name = $context->get($this->options['file']['name']);

		$this->options['parameters'] = [
			'section' => 'settings.sections.' . $name,
		];

		$result = parent::render($context);

		$context->registers['headers'][$name] = [
			'stylesheet' => $context->registers['stylesheet'] ?? '',
			'javascript' => $context->registers['javascript'] ?? ''
		];

		unset($context->registers['stylesheet']);
		unset($context->registers['javascript']);

		return $result;
	}
}
