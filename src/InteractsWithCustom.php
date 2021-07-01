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
use Symfony\Component\Finder\Finder;
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

	/**
	 * packages psr4.
	 *
	 * @return mixed
	 */
	protected function psr4()
	{
		$autoload_psr4 = include app()->getRootPath() . ('vendor/composer/autoload_psr4.php');
		$root_path = app()->getRootPath();

		foreach ($autoload_psr4 as &$item) {
			if (is_array($item)) {
				foreach ($item as &$value) {
					$value = str_replace($root_path, '', $value);
				}
			} else {
				$item = str_replace($root_path, '', $item);
			}
		}
		return $autoload_psr4;
	}

	/**
	 * 获取根命名空间.
	 * @param $module
	 * @param mixed $path
	 */
	protected function getRootNamespace($path): ?string
	{
		$psr4 = $this->psr4();
		$namespace = null;
		foreach ($psr4 as $_namespace => $item) {
			foreach ($item as $_path) {
				if (! is_bool(stripos($path, $_path))) {
					$namespace = $_namespace;
					continue;
				}
			}
		}
		return $namespace;
	}

	/**
	 * 获取所有class.
	 *
	 * @param string $layer 层名 controller model ...
	 * @throws \ReflectionException
	 * @return \ReflectionClass
	 */
	protected function getAllClass()
	{
		$class_file = iterator_to_array(
			Finder::create()->files()->ignoreUnreadableDirs(true)->ignoreDotFiles(true)->in($this->app->getRootPath())->sortByName(),
			false
		);
		$class_list = [];
		foreach ($class_file as $item) {
			$path = $item->getPath();
			$relative_path = str_replace(root_path(), '', $path);
			$pos = stripos($relative_path, $this->app->config->get('route.controller_layer'));
			$file_name = $item->getFilename();
			if (! $pos) {
				continue;
			}
			if ($item->getExtension() !== 'php') {
				continue;
			}
			$namespace = $this->getRootNamespace($relative_path) ?? false;
			if (! $namespace) {
				continue;
			}
			$module_namespace = substr(str_replace(['/', '\\\\'], '\\', $relative_path), (int) strpos(str_replace(['/', '\\\\'], '\\', $relative_path), $namespace));
			$is_package = stripos($relative_path, 'src');
			if ($is_package) {
				$module_namespace = $namespace . substr(str_replace(['/', '\\\\'], '\\', $relative_path), $is_package + 4);
			}
			$is_test = stripos($relative_path, 'test');
			$is_tests = stripos($relative_path, 'tests');
			if ($is_test || $is_tests) {
				continue;
			}
			if (stripos($relative_path, 'laravel')) {
				continue;
			}
			$class_name = str_replace('.php', '', $file_name);
			$class = $module_namespace . '\\' . $class_name;
			try {
				if (class_exists($class)) {
					$class_list[] = [
						$class => $relative_path . DIRECTORY_SEPARATOR . $file_name,
					];
				}
			} catch (\Throwable $t) {
				continue;
			}
		}
		// dd($class_list);
		return $class_list;
	}

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
