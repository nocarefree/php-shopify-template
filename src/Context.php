<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyTemplate;

use \Liquid\Context as BaseContext;

class Context extends BaseContext
{

	public function __construct(Theme $theme) {
        $this->theme = $theme;

		parent::__construct();

        foreach([
            Filters\FilterArray::class,
            Filters\FilterColor::class,
            Filters\FilterFont::class,
            Filters\FilterHtml::class,
            Filters\FilterMath::class,
            Filters\FilterMedia::class,
            Filters\FilterMetafield::class,
            Filters\FilterMoney::class,
            Filters\FilterString::class,
            Filters\FilterUrl::class
        ] as $filter){
            $this->registerFilterClass($filter);
        }
	}

    public function theme(){
        return $this->theme;
    }


    public function renderSection($config = []){
        return $this->theme->renderSection($config);
    }




}