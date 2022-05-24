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

use Liquid\AbstractBlock;
use Liquid\Context;
use Liquid\Liquid;
use Liquid\Regexp;

/**
 * Example:
 *
 *     {% stylesheet %} This will be ignored {% endstylesheet %}
 */
class TagStylesheet extends AbstractBlock
{


	public function parse(array &$tokens) {

		$tagRegexp = new Regexp('/^' . Liquid::get('TAG_START') . '-?\s*(\w+)\s*(.*)?\s-?' . Liquid::get('TAG_END') . '$/s');

		while (count($tokens)) {
			$token = array_shift($tokens);
			if ($tagRegexp->match($token) && $tagRegexp->matches[1] == $this->blockDelimiter()) {
				$this->endTag();
				return;
			}
		}
	}

	public function render(Context $context) {
		return '';
	}
}
