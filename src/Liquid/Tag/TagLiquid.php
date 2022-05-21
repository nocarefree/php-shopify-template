<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Liquid\Tag;

use Liquid\AbstractBlock;
use Liquid\Document;
use Liquid\Liquid;
use Liquid\Context;
use Liquid\LiquidException;
use Liquid\FileSystem;
use Liquid\Regexp;
use Liquid\Template;

class TagLiquid extends AbstractBlock
{
	
    private $innenTokens = [];

	public function __construct($markup, array &$tokens, FileSystem $fileSystem = null, $whitespace = [0,0]) {

        foreach(preg_split("#[\r\n]#", trim($markup),-1, PREG_SPLIT_NO_EMPTY) as $row){
            $this->innenTokens[] = "{% " .trim($row) ." %}";
        }

        parent::__construct($markup, $this->innenTokens, $fileSystem);
	}


	public function checkIncludes() {

	}

    protected function assertMissingDelimitation() {
		// throw new LiquidException($this->blockName() . " tag was never closed");
	}


	public function render(Context $context) {

	}
}
