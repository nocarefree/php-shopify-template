<?php

include(__DIR__ . "/vendor/autoload.php");

use Liquid\Liquid;
use Liquid\Parser;


// $str = "'2342\'sdf'";
// $reg = new Regexp('/"([^#"\\\\]*(?:\\\\.[^#"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As');
// $reg->match($str);
// var_dump($reg->matches);

// function encryptDecrypt($key, $string, $decrypt){  
//     if($decrypt){  
//         $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "12");  
//         return $decrypted;  
//     }else{  
//         $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));  
//         return $encrypted;  
//     }  
// }



$server = (new ShopifyTemplate\ThemeArchitecture());
$server->loadLocalFiles(__DIR__ . '/test/templates/crave');

exit;



$template = new Ncf\ShopifyTemplate\Theme(__DIR__ . '/test/templates/crave');
// $contents = $template->render('index',[
//     'request' => [
//         'locale' => [
//             'iso_code' => 'cn',
//         ]
//     ],
//     'site_name'=>'test.store',
//     'title'=>"首页",
//     'url'=> 'http://store.com',
//     'type'=>"index",
//     'description' => 'description',
//     'product' => [],
//     'all_products' => [],
//     'canonical_url' => 'http://store.com',
// ]);


// file_put_contents('2.txt',$contents);
