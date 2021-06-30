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
namespace littler\annotation;

use Doctrine\Common\Annotations\Reader;
use think\App;

/**
 * Trait InteractsWithRoute.
 *
 * @property App $app
 * @property Reader $reader
 */
trait InteractsWithCustom
{
	protected $custom = [];

	protected function registerCustomClassAnnotations(\ReflectionClass $refClass, &$routeGroup)
	{
		if ($this->checkCustomIsEnable()) {
			foreach ($this->custom as $decorator => $hander) {
				if ($this->reader->getClassAnnotation($refClass, $decorator)) {
					$annotation = $this->reader->getClassAnnotation($refClass, $decorator);
					(new $hander())->cls($refClass, $annotation, $routeGroup);
				} else {
					continue;
				}
			}
		}
	}

	protected function registerCustomMethodAnnotations(\ReflectionMethod $refMethod, &$rule)
	{
		if ($this->checkCustomIsEnable()) {
			foreach ($this->custom as $decorator => $hander) {
				if ($this->reader->getMethodAnnotation($refMethod, $decorator)) {
					$annotation = $this->reader->getMethodAnnotation($refMethod, $decorator);
					(new $hander())->func($refMethod, $annotation, $rule);
				} else {
					continue;
				}
			}
		}
	}

	private function checkCustomIsEnable()
	{
		if ($this->app->config->get('annotation.route.enable', true)) {
			$this->custom = config('annotation.custom.namespaces', []);
			if (! empty($this->custom)) {
				return true;
			}
		}

		return false;
	}
}
