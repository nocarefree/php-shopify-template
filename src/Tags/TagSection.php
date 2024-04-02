<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

<<<<<<< HEAD
namespace ShopifyLiquid\Tags;
=======
namespace Ncf\ShopifyTemplate\Tags;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe

use Liquid\Nodes\Node;
use Liquid\Context;
use Liquid\Environment;
use Liquid\Parser;
use Liquid\TokenStream;

class TagSection extends Node
{
<<<<<<< HEAD

	function parse()
	{
		parent::parse();
		$this->options['file']['path'] = ShopifyTemplate::PATH_SECTION;
=======
	function parse(Environment $env, TokenStream $stream)
	{
		if(preg_match(Parser::REGEX_STRING, $this->options['expression'], $matches, 0, 0) ){
			$section = $matches[1]?:$matches[2];
			$this->options['section'] = $section;

		}else{
			$this->error = "Valid syntax: section '[type]'";
			$env->addSyntaxError($this->error);
		}
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
	}

	/**
	 * Renders the node
	 *
	 * @param Context $context
	 *
	 * @return string
	 */
<<<<<<< HEAD
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
=======
	public function render(Context $context) {
		if(isset($context->registers['in_section']) && $context->registers['in_section']){
			$name = $context->registers['in_section'];
			$line = $this->lineno;
			return "Liquid error (sections/{$name}liquid line $line): Cannot render sections inside sections";
		}

		$name = $this->options['section'];

		if($context instanceof \Ncf\ShopifyTemplate\Context){
			return $context->renderSection(['id'=>$name,'type'=>$name]);
		}else{
			return "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
		}
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
	}
}
