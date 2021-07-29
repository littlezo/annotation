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
use littler\annotation\docs\ApiDocs;
use littler\annotation\route\Group;
use ReflectionClass;
use think\App;
use think\event\HttpRun;
use think\facade\Cache;

/**
 * Trait InteractsWithDocs.
 *
 * @property App $app
 * @property Reader $reader
 * @property
 */
/**
 * 合并两个多维数组.
 * @param array $a1
 * @param array $a2
 * @return array
 */
function array_merge_many($a1, $a2)
{
	$arr = array_merge($a1, $a2);
	foreach ($arr as $k => $v) {
		if (is_array($v) && isset($a1[$k]) && isset($a2[$k])) {
			$arr[$k] = array_merge_many($a1[$k], $a2[$k]);
		}
	}
	return $arr;
}
trait InteractsWithDocs
{
	use InteractsWithCustom;

	/**
	 * 注册注解文档.
	 */
	protected function registerAnnotationDocs()
	{
		// $this->app->event->listen(HttpRun::class, function () {
		Cache::set('apiDocs', $this->parseDocs($this->getClassMap()));
		// });
	}

	protected function parseDocs($class_map)
	{
		$apiDocs = [];
		foreach ($class_map as $class => $path) {
			$refClass = new ReflectionClass($class);
			/**
			 * 组.
			 * @var Group $group
			 */
			$group = $this->reader->getClassAnnotation($refClass, Group::class);
			/**
			 * 类文档.
			 * @var ApiDocs $apiClassDocs
			 */
			$item = [];
			if ($apiClassDocs = $this->reader->getClassAnnotation($refClass, ApiDocs::class)) {
				$item = array_merge_many($apiClassDocs->getRule(), $apiClassDocs->value);
				$apiDocs[$item['layer'] ?: 'other'][$item['module'] ?: 'default'][$item['group'] ?: $group->value] = array_merge_many($apiClassDocs->getRule(), $apiClassDocs->value);
				$apiDocs[$item['layer'] ?: 'other'][$item['module'] ?: 'default'][$item['group'] ?: $group->value]['group'] =  $apiClassDocs->value['group'] ?? 'default';
			}
			//方法
			foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
				/**
				 * 方法文档.
				 * @var ApiDocs $apiMethodDocs
				 */
				if ($apiMethodDocs = $this->reader->getMethodAnnotation($refMethod, ApiDocs::class)) {
					/**
					 * 路由信息.
					 * @var Route $route
					 */
					$method_docs = [];
					$method_docs = array_merge_many($apiMethodDocs->getRule(), $apiMethodDocs->value);
					if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {
						$path = '';
						if ($item['module']) {
							$path .='/' . $item['module'];
						}
						if ($item['group']) {
							$path .='/' . $item['group'];
						}
						if (strlen($route->value)>1) {
							$path .=$route->value;
						}
						$method_docs['path']= $path;
						$method_docs['auth']=$route->ignore_verify??false;
						$method_docs['method']=$route->method;
					}
					$apiDocs[$item['layer'] ?: 'other'][$item['module'] ?: 'default'][$item['group'] ?: $group->value]['methods'][] = $method_docs;
				}
			}
		}
		return $apiDocs;
	}
}
