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
use Liquid\Parser;


class SectionAttributeNode extends Block
{
    public function parse(){
        
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
		$this->assertMissingDelimitation();

        $name = $this->options['name'];
        if(!isset($this->template->root->options[$name])){
            $this->template->root->options[$name] = $content;
        }else{
            $line = $this->getStream()->getLineno();
            throw new \Liquid\LiquidException("Liquid syntax error (line {$line}): Duplicate entries for tag: {$name}");
        }
    }
    public function render(Context $context){
        return '';
    }
}
