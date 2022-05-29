<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyLiquid\Tags;

use Liquid\AbstractTag;
use Liquid\Document;
use Liquid\Context;
use Liquid\Liquid;
use Liquid\LiquidException;
use Liquid\Regexp;
use Liquid\Template;
use Ncf\ShopifyLiquid\ShopifyFileSystem;

/**
 * Includes another, partial, template
 *
 * Example:
 *
 *     {% section 'foo' %}
 *
 */
class TagSection extends AbstractTag
{
	/**
	 * @var string The name of the template
	 */
	private $templateName;
	/**
	 * @var Document The Document that represents the included template
	 */
	private $document;

	/**
	 * @var string The Source Hash
	 */
	protected $hash;

	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param FileSystem $fileSystem
	 *
	 * @throws \Liquid\LiquidException
	 */
	public function __construct($markup, array &$tokens, ShopifyFileSystem $fileSystem = null) {
		$regex = new Regexp('/("[^"]+"|\'[^\']+\')/');

		if ($regex->match($markup)) {
			$this->templateName = substr($regex->matches[1], 1, strlen($regex->matches[1]) - 2);
		}else{
			throw new LiquidException("Error in tag 'section'");
		}

		parent::__construct($markup, $tokens, $fileSystem);
	}

	/**
	 * Parses the tokens
	 *
	 * @param array $tokens
	 *
	 * @throws \Liquid\LiquidException
	 */
	public function parse(array &$tokens) {

	}



	/**
	 * Renders the node
	 *
	 * @param Context $context
	 *
	 * @return string
	 */
	public function render(Context $context) {
		$result = '';

		if(!isset($context->registers['_app']) || !$context->registers['_app'] instanceof \Ncf\ShopifyLiquid\ShopifyTemplate){
			return '';
		}

		$context->push();
		$context->registers['_app']->renderSectionFile($this->templateName, $context);
		$context->pop();

		return $result;
	}
}
