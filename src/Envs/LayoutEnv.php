<?php

namespace ShopifyTemplate\Envs;

use Liquid\LiquidException;

class LayoutEnv extends BaseEnv
{


    public function parse($content)
    {
        if (!preg_match('/{{\s?content_for_header\s?}}/', $content)) {
            throw new LiquidException('Missing {{content_for_header}} in the body section of the template');
        }

        if (!preg_match('/{{\s?content_for_layout\s?}}/', $content)) {
            throw new LiquidException('Missing {{content_for_layout}} in the body section of the template');
        }

        return parent::parse($content);
    }
}
