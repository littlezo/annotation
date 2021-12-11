<?php

declare(strict_types=1);

/**
 * #logic 做事不讲究逻辑，再努力也只是重复犯错
 * ## 何为相思：不删不聊不打扰，可否具体点：曾爱过。何为遗憾：你来我往皆过客，可否具体点：再无你。
 * ## 只要思想不滑稽，方法总比苦难多！
 *
 * @version 1.0.0
 *
 * @author @小小只^v^ <littlezov@qq.com>  littlezov@qq.com
 * @contact  littlezov@qq.com
 *
 * @see     https://github.com/littlezo
 * @document https://github.com/littlezo/wiki
 *
 * @license  https://github.com/littlezo/MozillaPublicLicense/blob/main/LICENSE
 */

namespace littler\annotation;

use Doctrine\Common\Annotations\Reader;
use littler\annotation\docs\ApiDefine;
use littler\annotation\docs\ApiError;
use littler\annotation\docs\ApiGroup;
use littler\annotation\docs\ApiHeader;
use littler\annotation\docs\ApiParam;
use littler\annotation\docs\ApiSuccess;
use ReflectionClass;
use think\App;
use think\facade\Cache;

/**
 * Trait InteractsWithDocs.
 *
 * @property App    $app
 * @property Reader $reader
 * @property
 */

/**
 * 合并两个多维数组.
 *
 * @param array $a1
 * @param array $a2
 *
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

function object_to_array($array)
{
    if (is_object($array)) {
        $array = (array) $array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_to_array($value);
        }
    }

    return $array;
}

trait InteractsWithDocs
{
    use InteractsWithCustom;

    /**
     * 注册注解文档.
     */
    protected function registerAnnotationDocs()
    {
        Cache::clear();
        Cache::set('apiList', $this->parses($this->getClassMap()));
    }

    protected function parses($class_map)
    {
        // dd($class_map);
        $apiList = [];
        foreach ($class_map as $class => $path) {
            $refClass = new ReflectionClass($class);
            /**
             * 类接口定义.
             *
             * @var ApiDocs $apiClassDocs
             */
            $item = [
                'group' => [],
                'methods' => [],
            ];

            $group = $this->reader->getClassAnnotation($refClass, ApiGroup::class);
            if (!$group) {
                continue;
            }
            $item['group'] = object_to_array($group ?? []);
            /***
             *
             * 方法文档
             */
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
                $define = $this->reader->getMethodAnnotation($refMethod, ApiDefine::class);
                $header = $this->reader->getMethodAnnotation($refMethod, ApiHeader::class);
                $param = $this->reader->getMethodAnnotation($refMethod, ApiParam::class);
                $success = $this->reader->getMethodAnnotation($refMethod, ApiSuccess::class);
                // dd();
                // $success->getValue();
                $error = $this->reader->getMethodAnnotation($refMethod, ApiError::class);
                if (!$define || !$header || !$param || !$success || !$error) {
                    // dd($refMethod, $refClass, $success);
                    continue;
                }
                $item['methods'][] =
                    // 'group' => object_to_array($group->value),
                    [
                        'define' => object_to_array($define ?: []),
                        'header' => object_to_array($header?->value ?: []),
                        'param' => object_to_array($param?->value ?: []),
                        'success' => object_to_array($success ?: []),
                        'error' => object_to_array($error ?: []),
                    ];
            }

            if (!is_null($item)) {
                $apiList[] = $item;
            }
        }
        // dd($apiList);

        return $apiList;
    }
}
