<?php

namespace Ncf\ShopifyTemplate\Drops;

class ContentForHeader extends \Liquid\Models\Drop{
    public $content = '';

    function __construct()
    {
        $this->id = '#'.md5(time()).'#'; 
    }

    function __toString(){
        return $this->id;
    }

    function toHtml(){
        return $this->content;
    }

}