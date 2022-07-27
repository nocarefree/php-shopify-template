<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyLiquid;

use Liquid\Nodes\Block;
use Liquid\Context;
use Liquid\LiquidException;
use Liquid\Parser;


class SectionAttributeNode extends Block
{
    public function parse(){
        $this->options['content'] = [];
        if($this->level >1){
            $name = $this->options['name'];
			$line = $this->getStream()->getLineno();
            throw new LiquidException("Liquid syntax error (line {$line}): '{$name}' tag must not be nested inside other tags");
        }
        
        $this->startBlock();
        $content = '';
		$stream = $this->getStream();

		while (!$stream->empty()) {
			$token = $stream->current();
			if(($m = Parser::testTagTemplate($token)) !==false && $this->isBlockDelimiter($m['name'])){
                break;
			}else{
                $content .= $token;
            }
			$stream->next();
		}
        $this->options['content'] = $content;
		$this->assertMissingDelimitation();
    }

    public function render(Context $context){
        return '';  
    }
}
