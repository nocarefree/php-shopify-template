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

use Liquid\AbstractBlock;
use Liquid\Liquid;
use Liquid\Context;
use Liquid\LiquidException;
use Liquid\FileSystem;
use Liquid\Regexp;

/**
 * Loops over an array, assigning the current value to a given variable
 *
 * Example:
 *
 *     {%for item in array%} {{item}} {%endfor%}
 *
 *     With an array of 1, 2, 3, 4, will return 1 2 3 4
 * 		
 *	   or
 *
 *	   {%for i in (1..10)%} {{i}} {%endfor%}
 *	   {%for i in (1..variable)%} {{i}} {%endfor%}
 *
 */
class TagForm extends AbstractBlock
{
	/**
	 * @var array The collection to loop over
	 */
	private $collectionName;

	/**
	 * @var string The variable name to assign collection elements to
	 */
	private $variableName;

	/**
	 * @var string The name of the loop, which is a compound of the collection and variable names
	 */
	private $name;
	
	/**
	 * @var string The type of the loop (collection or digit)
	 */
	private $type = 'collection';

	/**
	 * Array holding the nodes to render for each logical block
	 *
	 * @var array
	 */
	private $nodelistHolders = array();

	/**
	 * Array holding the block type, block markup (conditions) and block nodelist
	 *
	 * @var array
	 */
	protected $blocks = array();

	/**
	 * Constructor
	 *
	 * @param string $markup
	 * @param array $tokens
	 * @param FileSystem $fileSystem
	 *
	 * @throws \Liquid\LiquidException
	 */
	public function __construct($markup, array &$tokens, FileSystem $fileSystem = null) {
		$this->nodelist = & $this->nodelistHolders[count($this->blocks)];
		array_push($this->blocks, array('for', $markup, &$this->nodelist));

		parent::__construct($markup, $tokens, $fileSystem);

		$syntaxRegexp = new Regexp(Liquid::get('TAG_ATTRIBUTES'));

		if ($syntaxRegexp->match($markup)) {
			$this->extractAttributes($markup);
		} else {
			throw new LiquidException("Syntax Error in 'form'");
		}
	}

	/**
	 * Handler for unknown tags, handle else tags
	 *
	 * @param string $tag
	 * @param array $params
	 * @param array $tokens
	 */
	public function unknownTag($tag, $params, array $tokens) {
		if ($tag == 'else') {
			// Update reference to nodelistHolder for this block
			$this->nodelist = & $this->nodelistHolders[count($this->blocks) + 1];
			$this->nodelistHolders[count($this->blocks) + 1] = array();

			array_push($this->blocks, array($tag, $params, &$this->nodelist));
		} else {
			parent::unknownTag($tag, $params, $tokens);
		}
	}

	/**
	 * Renders the tag
	 *
	 * @param Context $context
	 *
	 * @return null|string
	 */
	public function render(Context $context) {
        return '';
	}

}
