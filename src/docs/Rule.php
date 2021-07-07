<?php

declare(strict_types=1);

/*
 * #logic 做事不讲究逻辑，再努力也只是重复犯错
 * ## 何为相思：不删不聊不打扰，可否具体点：曾爱过。何为遗憾：你来我往皆过客，可否具体点：再无你。
 * ## 只要思想不滑稽，方法总比苦难多！
 * @version 1.0.0
 * @author @小小只^v^ <littlezov@qq.com>  littlezov@qq.com
 * @contact  littlezov@qq.com
 * @link     https://github.com/littlezo
 * @document https://github.com/littlezo/wiki
 * @license  https://github.com/littlezo/MozillaPublicLicense/blob/main/LICENSE
 *
 */

namespace littler\annotation\docs;

use Doctrine\Common\Annotations\Annotation;

abstract class Rule extends Annotation
{
	/**
	 * 标题.
	 * @var string
	 */
	public $title;

	/**
	 * 版本.
	 *
	 * @var string|float
	 */
	public $version;

	/**
	 * 名称.
	 * @var string
	 */
	public $name;

	/**
	 * 应用层.
	 * @var string
	 */
	public $layer;

	/**
	 * 模块.
	 * @var string
	 */
	public $module;

	/**
	 * 组.
	 * @var string
	 */
	public $group;

	/**
	 * 路径.
	 * @var string
	 */
	public $path;

	/**
	 * 方法.
	 * @var string
	 */
	public $method;

	/**
	 * 请求头.
	 * @var string
	 */
	public $headers;

	/**
	 * 授权.
	 * @var string
	 */
	public $auth;

	/**
	 * 描述.
	 * @var string
	 */
	public $desc;

	/**
	 * 成功
	 * @var array
	 */
	public $success = [
		'code' => 200,
		'type' => 'success',
		'message' => '成功消息||success',
		'timestamp' => 1234567890,
		'result' => [
			'encryptData' => '加密数据自行解密',
		],
	];

	/**
	 * 失败.
	 * @var array
	 */
	public $error = [
		'code' => 500,
		'message' => '错误消息',
		'type' => 'error',
		'result' => '',
		'timestamp' => 1234567890,
	];

	/**
	 * 参数.
	 * @var array
	 */
	public $param = [];

	/**
	 * 方法合集.
	 * @var array
	 */
	public $methods = [];

	public function getRule()
	{
		return array_intersect_key(get_object_vars($this), array_flip([
			'title', 'version', 'name', 'path', 'method', 'headers', 'auth', 'layer',
			'group', 'module', 'desc', 'success', 'error', 'param', 'methods',
		]));
	}
}
