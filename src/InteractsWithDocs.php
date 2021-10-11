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
trait InteractsWithDocs
{
    use InteractsWithCustom;

    /**
     * 注册注解文档.
     */
    protected function registerAnnotationDocs()
    {
        Cache::set('apiDocs', $this->parses($this->getClassMap()));
    }

    protected function parses($class_map)
    {
        $apiDocs = [];
        foreach ($class_map as $class => $path) {
            $refClass = new ReflectionClass($class);
            /**
             * 类接口定义.
             *
             * @var ApiDocs $apiClassDocs
             */
            $item = [];
            if ($apiClassDefine = $this->reader->getClassAnnotation($refClass, ApiDefine::class)) {
                dd($apiClassDefine);
            }
            /***
             *
             * 方法文档
             */
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
                $define = $this->reader->getMethodAnnotation($refMethod, ApiDefine::class);
                $header = $this->reader->getMethodAnnotation($refMethod, ApiHeader::class);
                $param = $this->reader->getMethodAnnotation($refMethod, ApiParam::class);
                $success = $this->reader->getMethodAnnotation($refMethod, ApiSuccess::class);
                $error = $this->reader->getMethodAnnotation($refMethod, ApiError::class);
                dd($define, $header, $param, $success, $error);
            }
        }
        // dd($apiDocs);

        return $apiDocs;
    }
}
