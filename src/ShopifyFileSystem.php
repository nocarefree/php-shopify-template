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

use Liquid\FileNoFound;
use Liquid\FileSystem;
use Liquid\Source;
use Illuminate\Support\Str;

class ShopifyFileSystem implements FileSystem
{
	const PATH_TEMPLATE  = 'templates';
    const PATH_LAYOUT = 'layout'; 
    const PATH_SECTION = 'sections'; 
    const PATH_SNIPPET = 'snippets'; 
    const PATH_LOCALE = 'locales'; 

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
	
	public function getLayout($path) {
		return $this->get(self::PATH_LAYOUT.'/'.$path);
	}

	public function getSection($path) {
		return $this->get(self::PATH_SECTION.'/'.$path);
	}

	public function getSnippet($path) {
		return $this->get(self::PATH_SNIPPET.'/'.$path);
	}

	public function getSections() {
        return $this->getLiquidFiles(static::PATH_SECTION);
	}

	public function getSnippets() {
        return $this->getLiquidFiles(static::PATH_SNIPPET);
	}

	public function getLiquidFiles($path){
		$fullPath = $this->root . '/'. $path;
		$list = scandir($fullPath);
		$files = [];
		foreach($list as $file){
			if(Str::endsWith($file, '.liquid')){
				$files[] = Str::before($file, '.liquid');
			}
		}
		return $files;
	}

    public function get($path, $type = 'liquid') {
        $path = ltrim($path, '/');
        $fullpath = $this->root . '/'. $path . '.' .$type;
		if (!file_exists($fullpath) ) {
			$this->throw("Illegal template path '{$path}.{$type}'");
		}
		return file_get_contents($fullpath);
	}

	public function throw($message): \Liquid\FileNoFound{
		return new \Liquid\FileNoFound($message);
	}

}
