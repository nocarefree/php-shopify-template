<?php 

include(__DIR__."/vendor/autoload.php");



$template = new Ncf\ShopifyLiquid\ShopifyTemplate(__DIR__.'/tests/templates/crave');
$template->parseTemplate('index');

$nodes = $template->getRoot()->getNodelist();

foreach($nodes as $node){
    if($node instanceof Ncf\ShopifyLiquid\Tags\TagSection){
        $sections = $node;
    }
}

var_dump($sections);

