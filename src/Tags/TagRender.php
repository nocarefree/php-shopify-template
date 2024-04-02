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
use Ncf\ShopifyTemplate\Theme;

class TagRender extends Node
{
<<<<<<< HEAD
	function parse()
	{
		parent::parse();
		$this->options['file']['path'] = ShopifyTemplate::PATH_SNIPPET;
=======
	public $document;

	public function parse(Environment $env, TokenStream $stream)
	{
		$index = 0;
		$filename = '';

		if(preg_match(Parser::REGEX_STRING, $this->options['expression'], $matches, 0, $index) ){
			$filename = $matches[1]?:$matches[2];

			if(preg_match('/\G\s?(with|,|for)\s+(.*)/', $this->options['expression'], $matches, 0 , $index)){
				switch($matches[1]){
					case 'with':
						$this->options['parameters'] = Parser::parseAs($matches[2]);
						break;
					case ',':
						$this->options['parameters'] = Parser::parseParameters($matches[2]);
						break;
					case 'for':
						$this->options['collections'] = Parser::parseAs($matches[2]);
						break;
				}
			}

            $this->options['render'] = $filename;
		}else{
			$this->error = "<!-- Syntax error in tag 'render' - Template name must be a quoted string -->";
		}
	}


	public function render(Context $context) {
		if($this->error){
			return $this->error;
		}	
        
        if(!($context instanceof \Ncf\ShopifyTemplate\Context)){
            return '';
        }


        $filename = $this->options['render'];
        $file = $context->theme()->cache()->get(Theme::PATH_SNIPPET, $filename);
        if(!$file){
            return  "Liquid error: Could not find asset ".Theme::PATH_SNIPPET."/{$filename}.liquid";
        }

        $result = '';
        $document = $file['node'];
        

		if (isset($this->options['collections']) && !empty($this->options['collections'])) {
			$key = array_keys($this->options['collections'])[0];
			$collections = $context->get($this->options['collections'][$key]);	

			if($collections){
				foreach ($collections as $value) {
					$context->push();
					$context->set($key, $value);
					$result .= $document->render($context);
					$context->pop();
				}
			}
		}else{
			$context->push();
			if(isset($this->options['parameters'])){
				foreach($this->options['parameters'] as $key=>$value){
					$context->set($key, $context->get($value));	
				}
			}
			$result .= $document->render($context);
			$context->pop();
		}
		return $result;
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
	}
}
