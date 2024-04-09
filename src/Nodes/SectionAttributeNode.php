<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace ShopifyTemplate\Nodes;

use Liquid\Tags\TagComment;
use Liquid\Environment;

class SectionAttributeNode extends TagComment
{
    public function parse()
    {
        if ($this->stream->depth() > 1) {
            $name = $this->getName();
            $line = $this->stream->lineNo();
            $this->env->addSyntaxError("Liquid syntax error (line {$line}): '{$name}' tag must not be nested inside other tags");
        }

        parent::parse();
    }
}
