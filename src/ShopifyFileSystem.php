<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace Ncf\ShopifyLiquid;

use Liquid\LiquidException;
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

	/**
	 * 读取liquid文件
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string template content
	 */
	public function readTemplateFile($path) {
		$fullPath = $this->fullPath($path).'.liquid';
		$this->validPath($fullPath);
		return file_get_contents($fullPath);
	}

	public function readTemplateSource($path) {
		$fullPath = $this->fullPath($path).'.liquid';
		$this->validPath($fullPath);
		return new Source(file_get_contents($fullPath), $fullPath);
	}


	/**
	 * 读取json文件
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string template content
	 */
	public function readJsonFile($path) {
		$fullPath = $this->fullPath($path).'.json';
		$this->validPath($fullPath);
		return @json_decode(file_get_contents($fullPath),true);
	}

	/**
	 * 主题下页面的模板文件路径
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string
	 */
	public function layoutPath($path) {
        $path = ltrim($path, '/');
        $fullPath = $this->root . '/layout/' . $path . '.liquid';
		$this->validPath($fullPath);
		return $fullPath;
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

    /**
	 * 主题下页面的模板文件路径
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string
	 */
	public function templateType($path) {

        $path = ltrim($path, '/');
        $fullPath = $this->root . "/" . static::PATH_TEMPLATE ."/" . $path;

        if(file_exists($fullPath . '.json')){
            return 'JSON';
        }else if(file_exists($fullPath . '.liquid')){
            return 'LIQUID';
        }

		throw new FileNoFound("Illegal template path '" .$fullPath . "'");
	}

    /**
     * 加载Liquid文件
     *
     * @param [type] $templatePath
     * @return string
     */
    public function fullPath($path) {
        $path = ltrim($path, '/');
        $fullPath = $this->root . '/'. $path;
		return $fullPath;
	}

    /**
	 * 验证文件路径
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string
	 */
	public function validPath($fullPath) {
		if (! preg_match('/' . preg_quote(realpath($this->root), '/') . '/', realpath($fullPath)) || !file_exists($fullPath)) {
			throw new FileNoFound("Illegal template path '" .$fullPath . "'");
		}
	}

}
