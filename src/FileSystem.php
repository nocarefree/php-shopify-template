<?php


namespace Ncf\ShopifyTemplate;

use Liquid\FileNoFound;
use \Liquid\FileSystem as Im;
use Throwable;

class FileSystem implements Im
{

    /**
     * 主题路径
     *
     * @var [string]
     */
	private $root;

	/**
	 * Constructor
	 *
	 * @param string $root The root path for templates
	 */
	public function __construct($root) {
		$this->root = rtrim(trim($root),'/');
	}
	
	public function getAllFiles($path = '', $depth = 1){
		$fullPath = rtrim($this->root . '/'. $path, '/');
		$list = scandir($fullPath);
		$files = [];
		foreach($list as $file){
			if($file == '.' || $file == '..'){
				continue;
			}

			if(is_dir($fullPath . '/' . $file) && $depth > 1){
				$files = array_merge($files, $this->getAllFiles($path . '/'. $file, $depth++));
			}else{
				$files[] = ltrim($path . '/'. $file , '/');
			}
			
		}
		return $files;
	}

    public function get($path, $type = '') {
        $fullpath = $this->root . '/'. $path ;
		if (!file_exists($fullpath) ) {
			$this->throw("Illegal template path '{$path}'");
		}
		return file_get_contents($fullpath);
	}

	public function throw($message): Throwable{
		throw new FileNoFound($message);
	}

}
