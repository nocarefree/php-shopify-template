<?php 

namespace Ncf\ShopifyTemplate\Drops;

use Ncf\ShopifyTemplate\Nodes\SectionAttributeNode;

class SectionsDrop extends \Liquid\Models\Drop{
    
    function __construct($files)
    {
        foreach($files as $file){
            if($file['node']){
                $name = $file['name'];
                $node = $file['node'];
                foreach($node->getNodelist() as $sub){
                    if($sub instanceof SectionAttributeNode){
                        $this->attributes[$name][$sub->options['name']] = $sub->getContent();
                    }
                }
            }
        }
    }

}