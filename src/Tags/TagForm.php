<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace ShopifyTemplate\Tags;

use Liquid\Nodes\Block;
use Liquid\Context;
use Liquid\Exceptions\SyntaxError;
use Liquid\Parser;
use Liquid\TokenStream;

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
	public function parse(TokenStream $stream)
	{

		if (preg_match(Parser::REGEX_STRING, $this->expression, $matches)) {
			$this->form_type = $matches[1] ?: $matches[2];
			$this->formArgs = preg_split('#\s?,\s?#', substr($this->expression, strlen($matches[0]), -1), -1, PREG_SPLIT_NO_EMPTY);
		} else {
			throw new SyntaxError("Invalid liquid syntax");
		}

		parent::parse($stream);
		return $this;
	}

	public function render(Context $context): string
	{
		if (!in_array(
			$this->form_type,
			[
				'activate_customer_password',
				'cart',
				'contact',
				'create_customer',
				'currency',
				'customer',
				'customer_address',
				'customer_login',
				'guest_login',
				'localization',
				'new_comment',
				'product',
				'recover_customer_password',
				'reset_customer_password',
				'storefront_password'
			]
		)) {
			return 'Invalid form type "' . $this->form_type . '", must be one of ["product", "storefront_password", "contact", "customer_login", "create_customer", "recover_customer_password", "reset_customer_password", "guest_login", "currency", "activate_customer_password", "customer_address", "new_comment", "customer", "localization", "cart"]';
		}

		return '<form>' . parent::render($context) . '</form>';
	}
}
