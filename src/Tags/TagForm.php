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

use Liquid\Nodes\Block;
use Liquid\Context;
use Liquid\Environment;
use Liquid\Parser;

/**
 * Loops over an array, assigning the current value to a given variable
 *
 * Example:
 *
 *     {%for item in array%} {{item}} {%endfor%}
 *
 *     With an array of 1, 2, 3, 4, will return 1 2 3 4
 * 		
 *	   or
 *
 * activate_customer_password:{ action: '', input：'',}
 * cart
 * contact
 * create_customer
 * currency
 * customer
 * customer_address
 * customer_login
 * guest_login
 * localization
 * new_comment :{ article: ture, }
 * product :{ product: ture, }
 * recover_customer_password
 * reset_customer_password
 * storefront_password
 *
 */
class TagForm extends Block
{
<<<<<<< HEAD
	public function parse()
	{

		if (preg_match(Parser::REGEX_STRING, $this->options['expression'], $matches)) {
=======
	public function parseExpression(Environment $env)
	{
		if(preg_match(Parser::REGEX_STRING, $this->options['expression'], $matches)){
>>>>>>> 7cd1322d617f0c921f627129d76e9edb3559ccfe
			$this->options['form_name'] =  $matches[0];
			$index = strlen($this->options['form_name']);

			$this->opitons['args'] = [];
			if (preg_match('/\G\s?,/', $this->options['expression'], $matches, 0, $index)) {
				$index += strlen($matches[0]);
				$this->opitons['args'] = Parser::parseParameters($this->options['expression']);
			}
		}
	}

	public function render(Context $context)
	{
		return '<form>' . parent::render($context) . '</form>';
	}
}
