<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyTemplate\Nodes;

use Liquid\Tags\TagComment;
use Liquid\Environment;

class SectionAttributeNode extends TagComment
{
    public function parseExpression(Environment $env)
    {
        if($this->depth > 1){
            $name = $this->options['name'];
			$line = $this->lineno;
            $this->error = "Liquid syntax error (line {$line}): '{$name}' tag must not be nested inside other tags";
            $env->addSyntaxError("Liquid syntax error (line {$line}): '{$name}' tag must not be nested inside other tags");
        }
    }

    public function getContent(){
        $content = '';
        foreach($this->nodelist as $node){
            $content .= $node;
        } 
        return $node;
    }
}
