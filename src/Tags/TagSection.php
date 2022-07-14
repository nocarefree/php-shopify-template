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
			return '';
		}

		$this->options['parameters'] = [
			'section'=>'settings.sections.'. $context->get($this->options['file']['name']),
		];

		return parent::render($context);
	}
}
