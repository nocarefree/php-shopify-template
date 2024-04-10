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

use Liquid\Nodes\Block;
use Liquid\Context;
use Liquid\Environment;
use Liquid\Parser;


class TagPaginate extends Block
{
	public function parse()
	{
		$index = 0;
		if (preg_match(Parser::REGEX_VAR, $this->expression, $matches, 0, $index)) {
			$this->options['arr'] =  $matches[0];
			$index = strlen($matches[0]);

			if (preg_match('/\s?by (.*)/', $this->expression, $matches)) {
				$this->options['by'] =  $matches[1];
			}
		}
	}
}
