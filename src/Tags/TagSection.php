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

use Liquid\Tags\TagRender;
use Liquid\Context;
use Ncf\ShopifyLiquid\ShopifyTemplate;

class TagSection extends TagRender
{
	
	function parse(){
		parent::parse();
		$this->options['file']['path'] = ShopifyTemplate::PATH_SECTION ;
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

		$name = $context->get($this->options['file']['name']);

		$this->options['parameters'] = [
			'section'=>'settings.sections.'. $name,
		];

		try{
			$result = parent::render($context);
		}catch(\Exception $e){
			$result = "Liquid error: Error in tag 'section' - {$name} is not a valid section type";
		}
		
		return $result;
	}
}
