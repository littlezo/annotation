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
namespace littler\annotation\handler;

use Doctrine\Common\Annotations\Annotation;

abstract class Handler implements HandleInterface
{
	public function cls(\ReflectionClass $refClass, Annotation $annotation, \think\Route\RuleGroup &$route)
	{
		// TODO: Implement cls() method.
	}

	public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\route\RuleItem &$rule)
	{
		// TODO: Implement func() method.
	}

	/**
	 * 判断是否是当前请求
	 *
	 * @return bool
	 */
	public function isCurrentMethod(\ReflectionMethod $refMethod, \think\route\RuleItem $rule)
	{
		if (strtolower(PHP_SAPI) != 'cli') {
			if (strtolower(request()->method()) !== strtolower($rule->getMethod())) {
				return false;
			}
			$routeRule = $rule->parseUrlPath($rule->getRule());
			$requestRule = $rule->parseUrlPath(explode('?', request()->url())[0]);
			if (count($requestRule) !== count($routeRule)) {
				return false;
			}
			foreach ($requestRule as $k => $v) {
				if ($requestRule[$k] !== $routeRule[$k]) {
					if (! strstr($routeRule[$k], '<')) {
						return false;
					}
				}
				continue;
			}

			return true;
		}

		return false;
	}
}
