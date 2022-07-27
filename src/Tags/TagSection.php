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

use Liquid\Nodes\Block;
use Liquid\Context;
use Liquid\LiquidException;
use Liquid\Parser;

class TagSection extends Block
{
	
	function parse(){
		if(preg_match(Parser::REGEX_STRING, $this->options['expression'], $matches, 0, 0) ){
			$this->options['section'] = $matches[1]?:$matches[2];
			$this->template->getRoot()->options['sections'][] = $this->options['section'];
		}else{
			throw new LiquidException("Valid syntax: section '[type]'");
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
			$line = $this->getStream()->getLineno();
			return "Liquid error (sections/{$name}liquid line $line): Cannot render sections inside sections";
		}

		$name = $this->options['section'];

		try{
			$evn =  $this->context->registers['app'];
			return $evn->renderSection($name);
		}catch(\Exception $e){
			$result = "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
		}
		
		return $result;
	}
}
