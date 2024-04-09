<?php

/**
 * This file is part of the Liquid package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Liquid
 */

namespace ShopifyTemplate;

use Liquid\LiquidException;
use Liquid\FileSystem;
use Liquid\Regexp;
use Liquid\Source;

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
	public function __construct($root)
	{
		$this->root = rtrim(trim($root), '/');
	}

	/**
	 * 读取liquid文件
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string template content
	 */
	public function readTemplateFile($path)
	{
		$fullPath = $this->fullPath($path) . '.liquid';
		$this->validPath($fullPath);
		return file_get_contents($fullPath);
	}

	public function readTemplateSource($path)
	{
		$fullPath = $this->fullPath($path) . '.liquid';
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
	public function readJsonFile($path)
	{
		$fullPath = $this->fullPath($path) . '.json';
		$this->validPath($fullPath);
		return @json_decode(file_get_contents($fullPath), true);
	}

	/**
	 * 主题下页面的模板文件路径
	 *
	 * @param string $templatePath
	 *
	 * @throws LiquidException
	 * @return string
	 */
	public function layoutPath($path)
	{
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
	public function templateType($path)
	{

		$path = ltrim($path, '/');
		$fullPath = $this->root . "/" . ShopifyTemplate::PATH_TEMPLATE . "/" . $path;

		if (file_exists($fullPath . '.json')) {
			return 'JSON';
		} else if (file_exists($fullPath . '.liquid')) {
			return 'LIQUID';
		}

		return null;
	}

	/**
	 * 加载Liquid文件
	 *
	 * @param [type] $templatePath
	 * @return string
	 */
	public function fullPath($path)
	{
		$path = ltrim($path, '/');
		$fullPath = $this->root . '/' . $path;
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
	public function validPath($fullPath)
	{
		if (!preg_match('/' . preg_quote(realpath($this->root), '/') . '/', realpath($fullPath)) || !file_exists($fullPath)) {
			throw new LiquidException("Illegal template path '" . $fullPath . "'");
		}
	}
}
