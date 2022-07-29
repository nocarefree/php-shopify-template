<?php

namespace Ncf\ShopifyTemplate\Nodes;

class Variable extends \Liquid\Nodes\Variable{

    function parse(){
        if($this->options['expression'] == 'content_for_layout'){
            $this->template->root->options['layout'][] = 'content.liquid';
        }
    }
}