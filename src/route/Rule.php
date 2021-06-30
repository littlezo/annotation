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
namespace littler\annotation\route;

use Doctrine\Common\Annotations\Annotation;

abstract class Rule extends Annotation
{
	/**
	 * @var string|array
	 */
	public $middleware;

	/**
	 * 后缀
	 *
	 * @var string
	 */
	public $ext;

	/**
	 * @var string
	 */
	public $deny_ext;

	/**
	 * @var bool
	 */
	public $https;

	/**
	 * @var string
	 */
	public $domain;

	/**
	 * @var bool
	 */
	public $complete_match;

	/**
	 * @var string|array
	 */
	public $cache;

	/**
	 * @var bool
	 */
	public $ajax;

	/**
	 * @var bool
	 */
	public $pjax;

	/**
	 * @var bool
	 */
	public $json;

	/**
	 * @var array
	 */
	public $filter;

	/**
	 * @var array
	 */
	public $append;

	public function getOptions()
	{
		return array_intersect_key(get_object_vars($this), array_flip([
			'middleware', 'ext', 'deny_ext', 'https', 'domain', 'complete_match', 'cache', 'ajax', 'pjax', 'json', 'filter', 'append',
		]));
	}
}
