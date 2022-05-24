<?php

namespace Ncf\ShopifyLiquid;

class OnlineStoreEditorData{

    protected $attributes;

    public function __construct($attributes = []){
        $this->$attributes = $attributes;
    }

    public function set($key , $value){
 
    }

    public function __toString()
    {
        return json_encode($this->attributes);
    }
    
}