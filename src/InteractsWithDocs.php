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

/**
 * Trait InteractsWithDocs.
 *
 * @property App $app
 * @property Reader $reader
 * @property
 */
trait InteractsWithDocs
{
	use InteractsWithCustom;

	/**
	 * 注册注解路由.
	 */
	protected function registerAnnotationDocs()
	{
		$this->app->event->listen(HttpRun::class, function () {
			$this->route = $this->app->route;
			$docs = [];
			foreach ($this->getAllClass() as $class_map) {
				$docs[]=$this->parseDocs($class_map);
			}
			// dd($docs);
		});
	}

	protected function parseDocs($class_map)
	{
		// dd($class_map);
		$docs = [];
		foreach ($class_map as $class => $path) {
			$refClass = new ReflectionClass($class);
			//类
			/** @var Group $group */
			if ($group = $this->reader->getClassAnnotation($refClass, Group::class)) {
				// 文档信息
				$docs[$group->value]= [];
			}

			/** @var ApiDocs $apiDocs */
			if ($apiDocs = $this->reader->getClassAnnotation($refClass, ApiDocs::class)) {
				//路由信息
				// dd($apiDocs);
				$docs[$group->value]['class'] = $apiDocs->getRule();
				$docs[$group->value]['class']['name'] =  $group->value;
				$docs[$group->value]['class']['group'] =  $group->value;
			}

			// dd($docs);

			//方法
			foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
				$methods =[];
				/* @var ApiDocs $apiMethodDocs */
				if ($apiMethodDocs = $this->reader->getMethodAnnotation($refMethod, ApiDocs::class)) {
					//文档信息
					$methods = $apiMethodDocs;
				}
				/* @var Route $route */
				if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {
					// 路由信息
					// $methods['path'] = $route->value;
				}
			}
			$docs['methods'][] = $methods;
		}
		return $docs;
	}
}
