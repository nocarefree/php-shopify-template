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
use Liquid\Regexp;
use Liquid\Liquid;

class ShopifyFileSystem implements FileSystem
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

	/**
	 * 解析文件
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string template content
	 */
	public function readTemplateFile($path, $type = '') {
		if (!($fullPath = $this->fullPath($path, $type))) {
			throw new LiquidException("No such template '$type : $path'");
		}
		return file_get_contents($fullPath);
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

    /**
	 * 主题下页面的模板文件路径
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string
	 */
	public function templatePath($path) {

        $path = ltrim($path, '/');
        $fullPath = $this->root . '/templates/' . $path;

        if(file_exists($fullPath . '.json')){
            $fullPath = $fullPath .'.json';
        }else if(file_exists($fullPath . '.liquid')){
            $fullPath = $fullPath .'.liquid';
        }

        $this->validPath($fullPath);
		return $fullPath;
	}

    /**
     * 加载Liquid文件
     *
     * @param [type] $templatePath
     * @return string
     */
    public function fullPath($path, $type) {
        $path = ltrim($path, '/');
        $type = trim($type, '/');
        $fullPath = $this->root . '/'. trim($type).'/' . $path . '.liquid';
		$this->validPath($fullPath);
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
		$rootRegex = new Regexp('/' . preg_quote(realpath($this->root.'/'), '/') . '/');
		if (!$rootRegex->match(realpath($fullPath))) {
			throw new LiquidException("Illegal template path '" .$fullPath . "'");
		}
	}
    


}
