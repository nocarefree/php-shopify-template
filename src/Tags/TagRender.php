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
 *     {% render 'foo' %}
 *
 *     Will include the template called 'foo'
 *
 *     {% render 'foo' with bar as product %}
 *
 *     Will include the template called 'foo', with a variable called foo that will have the value of 'bar'
 *
 *     {% render 'foo' for bars as bar %}
 *
 *     Will include the template called 'foo', with a variable called foo that will have the value of 'bar'
 *
 *     {% render 'foo' , width: 500, height: 800 %}
 *
 *
 *     Will loop over all the values of bar, including the template foo, passing a variable called foo
 *     with each value of bar
 */
class TagRender extends AbstractTag
{
	/**
	 * @var string The name of the template
	 */
	private $templateName;

	/**
	 * @var bool True if the variable is a collection
	 */
	private $collection;

	/**
	 * @var mixed The value to pass to the child template as the template name
	 */
	private $variable;

	/**
	 * @var mixed The value to pass to the child template as the template name
	 */
	private $args;

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
		$regex = new Regexp('/("[^"]+"|\'[^\']+\')\s+((with|for)\s+(' . Liquid::get('VARIABLE_NAME') . '+)\s+as\s+(' . Liquid::get('VARIABLE_NAME') . '))?\s*/s');
		$argumentRegexp = new Regexp('/("[^"]+"|\'[^\']+\')\s*' . Liquid::get('ARGUMENT_SEPARATOR') . '\s*(.*)$/s');

		if ($regex->match($markup)) {
			$this->templateName = substr($regex->matches[1], 1, strlen($regex->matches[1]) - 2);

			if (isset($regex->matches[1])) {
				$this->collection = (isset($regex->matches[3])) ? ($regex->matches[3] == "for") : null;
				$this->variable = (isset($regex->matches[4])) ? $regex->matches[4] : null;
				$this->variableName = (isset($regex->matches[5])) ? $regex->matches[5] : $this->templateName;
			}
		} else if($argumentRegexp->match($markup)){
			$this->templateName = substr($argumentRegexp->matches[1], 1, strlen($argumentRegexp->matches[1]) - 2);
			$this->args = \Liquid\Arguments::parse($argumentRegexp->matches[2]);
		} else if(trim($markup) == 'block'){
			$this->templateName = 'block';
		} else {
			throw new LiquidException("Error in tag 'render' - Valid syntax: render '$markup'");
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
	 * check for cached includes
	 *
	 * @return boolean
	 */
	public function checkIncludes() {
		$cache = Template::getCache();

		if ($this->document->checkIncludes() == true) {
			return true;
		}

		$source = $this->fileSystem->readTemplateFile($this->templateName);

		if ($cache->exists(md5($source)) && $this->hash == md5($source)) {
			return false;
		}

		return true;
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
		$variable = $context->get($this->variable);

		if(!$context->registers['_app'] instanceof \Ncf\ShopifyLiquid\ShopifyTemplate){
			return null;
		}

		if($this->templateName == 'block'){
			return '';
		}

		if ($this->collection) {
			$count = count($variable);
			foreach ($variable as $key=>$item) {
				$data = [];
				$forloop = [
					'first'=> $key==0,
					'index'=> $key+1,
					'index0'=> $key,
					'last'=> $key==($count-1),
					'length' => $count,
					'rindex' => $count-$key,
					'rindex0' => $count-1-$key,
				];
				$data[$this->variableName] = $item;
				

				$context->set($this->variableName, $item);
				$context->set('forloop', $forloop);
				
				$result .= $context->registers['_app']->renderSnippetLiquid($this->templateName);
			}
		} else if($this->args){
			$args = \Liquid\Arguments::render($context, $this->args);
			$context->push();
			foreach($args as $key=>$arg){
				$context->set($key, $arg);
			}
			$result .= $context->registers['_app']->renderSnippetLiquid($this->templateName);
		} else {
			$context->push();
			$data = [];
			if (!is_null($this->variable)) {
				$context->set($this->variableName, $variable);
			}
			$result .= $context->registers['_app']->renderSnippetLiquid($this->templateName);
		}

		return $result;
	}
}
