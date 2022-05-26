<?php 

include(__DIR__."/vendor/autoload.php");



$template = new Ncf\ShopifyLiquid\ShopifyTemplate(__DIR__.'/tests/templates/crave');
$contents = $template->render('index',[]);


file_put_contents('1.txt',$contents, FILE_APPEND);