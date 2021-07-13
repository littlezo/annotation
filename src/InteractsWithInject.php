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
use PhpDocReader\PhpDocReader;
use ReflectionObject;
use think\App;
use think\event\AppInit;

/**
 * Trait InteractsWithInject.
 *
 * @property App $app
 * @property Reader $reader
 */
trait InteractsWithInject
{
	protected $docReader = null;

	protected function getDocReader()
	{
		if (empty($this->docReader)) {
			$this->docReader = new PhpDocReader();
		}

		return $this->docReader;
	}

	protected function autoInject()
	{
		// $this->app->event->listen(AppInit::class, function () {
		if ($this->app->config->get('annotation.inject.enable', true)) {
			$this->app->resolving(function ($object) {
				// dd($this->isInjectClass(get_class($object)));
				// if ($this->isInjectClass(get_class($object))) {
				$reader = $this->getDocReader();

				$refObject = new ReflectionObject($object);

				foreach ($refObject->getProperties() as $refProperty) {
					if ($refProperty->isDefault() && ! $refProperty->isStatic()) {
						$annotation = $this->reader->getPropertyAnnotation($refProperty, Inject::class);
						if ($annotation) {
							if ($annotation->value) {
								$value = $this->app->make($annotation->value);
							} else {
								//获取@var类名
								$propertyClass = $reader->getPropertyClass($refProperty);
								if ($propertyClass) {
									$value = $this->app->make($propertyClass);
								}
							}

							if (! empty($value)) {
								if (! $refProperty->isPublic()) {
									$refProperty->setAccessible(true);
								}
								$refProperty->setValue($object, $value);
							}
						}
					}
				}

				if ($refObject->hasMethod('__injected')) {
					$this->app->invokeMethod([$object, '__injected']);
				}
				// }
			});
		}
		// });
	}

	protected function isInjectClass($name)
	{
		$namespaces = ['app\\'] + $this->app->config->get('annotation.inject.namespaces', []);

		foreach ($namespaces as $namespace) {
			$namespace = rtrim($namespace, '\\') . '\\';

			if (stripos(rtrim($name, '\\') . '\\', $namespace) === 0) {
				return true;
			}
		}
	}
}
