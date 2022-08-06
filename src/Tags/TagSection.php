<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyTemplate\Tags;

use Liquid\Nodes\Node;
use Liquid\Context;
use Liquid\Environment;
use Liquid\Parser;
use Liquid\TokenStream;

class TagSection extends Node
{
	function parse(Environment $env, TokenStream $stream)
	{
		if(preg_match(Parser::REGEX_STRING, $this->options['expression'], $matches, 0, 0) ){
			$section = $matches[1]?:$matches[2];
			$this->options['section'] = $section;

		}else{
			$this->error = "Valid syntax: section '[type]'";
			$env->addSyntaxError($this->error);
		}
	}

	/**
	 * Renders the node
	 *
	 * @param Context $context
	 *
	 * @return string
	 */
	public function render(Context $context) {
		if(isset($context->registers['in_section']) && $context->registers['in_section']){
			$name = $context->registers['in_section'];
			$line = $this->lineno;
			return "Liquid error (sections/{$name}liquid line $line): Cannot render sections inside sections";
		}

		$name = $this->options['section'];
		try{
			$app = $context->registers['app'];
			$result = $app->renderSection($app);
		}catch(\Exception $e){
			$result = "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
		}
		
		return $result;
	}
}
