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
use littler\annotation\route\Resource as Resources;
use littler\annotation\route\Validate;
use ReflectionClass;
use ReflectionMethod;
use think\App;
use think\event\RouteLoaded;

/**
 * Trait InteractsWithRoute.
 *
 * @property App    $app
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
     * 注册注解路由.
     */
    protected function registerAnnotationRoute()
    {
        if ($this->app->config->get('annotation.route.enable', true)) {
            // $this->app->event->listen(RouteLoaded::class, function () {
            $this->route = $this->app->route;
            $this->parse($this->getClassMap());
            // });
        }
    }

    protected function parse($class_map)
    {
        foreach ($class_map as $class => $path) {
            $refClass = new ReflectionClass($class);
            $routeGroup = false;
            $routeMiddleware = [];
            $callback = null;

            //类
            /** @var Resources $resource */
            if ($resource = $this->reader->getClassAnnotation($refClass, Resources::class)) {
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

            if (false !== $routeGroup) {
                $routeGroup = $this->route->group($routeGroup, $callback);
                if ($group) {
                    $routeGroup->option($group->getOptions());
                }

                $routeGroup->middleware($routeMiddleware[0] ?? null, $routeMiddleware[1] ?? null);
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
                    //注册路由
                    if (false !== strpos($route->value, '^')) {
                        $rule = $this->route->rule(substr($route->value, 1), "{$class}@{$refMethod->getName()}", $route->method);
                    } else {
                        $rule = $routeGroup->addRule($route->value, "{$class}@{$refMethod->getName()}", $route->method);
                    }

                    $rule->option($route->getOptions());
                    $rule->middleware($routeMiddleware[0] ?? null, $routeMiddleware[1] ?? null);
                    //中间件
                    if ($middleware = $this->reader->getMethodAnnotation($refMethod, Middleware::class)) {
                        $rule->middleware($middleware->value[0] ?? null, $middleware->value[1] ?? null);
                    }
                    //设置分组别名
                    if ($group = $this->reader->getMethodAnnotation($refMethod, Group::class)) {
                        $rule->group($group->value);
                    }

                    //绑定模型,支持多个
                    if (!empty($models = $this->getMethodAnnotations($refMethod, Model::class))) {
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
