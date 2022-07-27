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

use Ncf\ShopifyLiquid\ShopifyFileSystem;

class TagRender extends \Liquid\Tags\TagRender
{
	function parseFile($filename){
		parent::parseFile(ShopifyFileSystem::PATH_SNIPPET . '/' . $filename); 

		if($this->options['node'] && isset($this->options['node']->options['sections'])){
			$sections = $this->template->getRoot()->options['sections']??[];
			$this->template->getRoot()->options['sections'] = array_merge($sections, $this->options['node']->options['sections']);
		}
	}

	
}
