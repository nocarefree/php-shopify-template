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
use Liquid\Exceptions\SyntaxError;
use Liquid\TokenStream;

class InSectionTopNode extends TagComment
{
    public function parse(TokenStream $stream)
    {
        $this->inside = false;
        if ($stream->depth() > 1) {
            $name = $this->name;
            $stream->addSyntaxError("'{$name}' tag must not be nested inside other tags");
            $this->inside = true;
        }

        parent::parse($stream);
        return $this;
    }

    public function __toString(): string
    {
        return $this->nodes ? implode("", $this->nodes) : '';
    }
}
