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

use Liquid\Context;
use Liquid\Nodes\Block;
use Liquid\Exceptions\SyntaxError;
use Liquid\Parser;
use Liquid\TokenStream;

class TagPaginate extends Block
{
	public function parse(TokenStream $stream)
	{

		$this->drop = null;
		if (preg_match(Parser::REGEX_VAR, $this->expression, $matches)) {
			$this->drop = $matches[0];

			if (preg_match('#[\s,]+window_size[\s]?:[\s]?(\d)[\s,]#', $this->expression, $matches)) {
				$this->window_size = $matches[0];
			}
		} else {
			$stream->addSyntaxError("in tag 'paginate' - Valid syntax: paginate [collection] by number");
		}
		parent::parse($stream);
		return $this;
	}

	public function render(Context $context): string
	{
		if (empty($this->drop)) {
			return "in tag 'paginate' - Valid syntax: paginate [collection] by number";
		}


		if (!in_array($this->drop, [
			'all_products',
			'article.comments',
			'blog.articles',
			'collections',
			'collection.products',
			'customer.addresses',
			'customer.orders',
			'pages',
			'search.results',
			'collection_list settings',
			'product_list settings'
		])) {
			return "Array '" . $this->drop . "' is not paginateable.";
		}

		return parent::render($context);
	}
}
