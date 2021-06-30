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
use littler\annotation\route\Group;
use littler\annotation\route\Middleware;
use littler\annotation\route\Model;
use littler\annotation\route\Resource;
use littler\annotation\route\Validate;
use littler\facade\FileSystem;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder;
use think\App;
use think\event\RouteLoaded;

/**
 * Trait InteractsWithRoute.
 *
 * @property App $app
 * @property Reader $reader
 */
trait InteractsWithRoute
{
	use InteractsWithCustom;

	/**
	 * @var \think\Route
	 */
	protected $route;

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

	/**
	 * 注册注解路由.
	 */
	protected function registerAnnotationRoute()
	{
		if ($this->app->config->get('annotation.route.enable', true)) {
			$this->app->event->listen(RouteLoaded::class, function () {
				$this->route = $this->app->route;
				foreach ($this->getAllClass() as $class_map) {
					$this->parse($class_map);
				}
				// $directories = [];
				// dd($this->getAllClass());
				// foreach (Finder::create()->in($this->app->getRootPath())->directories()->ignoreUnreadableDirs()->sortByName() as $dir) {
				// 	if (strripos($dir->getPathname(), $this->app->config->get('route.controller_layer'))) {
				// 		$directories[] = $dir->getPathname();
				// 		if (is_dir($dir->getPathname())) {
				// 			$this->scanDir($dir->getPathname());
				// 		}
				// 	}
				// }
			});
		}
	}

	protected function parse($class_map)
	{
		// dd($dir);
		// $class_file = FileSystem::allFiles(root_path());
		// dd($class_map);
		foreach ($class_map as $class => $path) {
			# code...
			// dd($class);
			$refClass = new ReflectionClass($class);

			// dd($refClass);
			$routeGroup = false;
			$routeMiddleware = [];
			$callback = null;

			//类
			/** @var resource $resource */
			if ($resource = $this->reader->getClassAnnotation($refClass, Resource::class)) {
				//资源路由
				$callback = function () use ($class, $resource) {
					$this->route->resource($resource->value, $class)
						->option($resource->getOptions());
				};
			}

			if ($middleware = $this->reader->getClassAnnotation($refClass, Middleware::class)) {
				$routeGroup = '';
				$routeMiddleware = $middleware->value;
			}

			/** @var Group $group */
			if ($group = $this->reader->getClassAnnotation($refClass, Group::class)) {
				$routeGroup = $group->value;
			}

			if ($routeGroup !== false) {
				$routeGroup = $this->route->group($routeGroup, $callback);
				if ($group) {
					$routeGroup->option($group->getOptions());
				}

				$routeGroup->middleware($routeMiddleware);
			} else {
				if ($callback) {
					$callback();
				}
				$routeGroup = $this->route->getGroup();
			}

			$this->registerCustomClassAnnotations($refClass, $routeGroup);

			//方法
			foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
				/** @var Route $route */
				if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {
					// dd($route);
					//注册路由
					$rule = $routeGroup->addRule($route->value, "{$class}@{$refMethod->getName()}", $route->method);

					$rule->option($route->getOptions());

					//中间件
					if ($middleware = $this->reader->getMethodAnnotation($refMethod, Middleware::class)) {
						$rule->middleware($middleware->value);
					}
					//设置分组别名
					if ($group = $this->reader->getMethodAnnotation($refMethod, Group::class)) {
						$rule->group($group->value);
					}

					//绑定模型,支持多个
					if (! empty($models = $this->getMethodAnnotations($refMethod, Model::class))) {
						/** @var Model $model */
						foreach ($models as $model) {
							$rule->model($model->var, $model->value, $model->exception);
						}
					}

					//验证
					/** @var Validate $validate */
					if ($validate = $this->reader->getMethodAnnotation($refMethod, Validate::class)) {
						$rule->validate($validate->value, $validate->scene, $validate->message, $validate->batch);
					}

					$this->registerCustomMethodAnnotations($refMethod, $rule);
				}
			}
		}
	}

	protected function getMethodAnnotations(ReflectionMethod $method, $annotationName)
	{
		$annotations = $this->reader->getMethodAnnotations($method);

		return array_filter($annotations, function ($annotation) use ($annotationName) {
			return $annotation instanceof $annotationName;
		});
	}
}
