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

namespace littler\annotation\docs;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

if (!defined('TIME')) {
    define('TIME', time());
}

/**
 * api 异常返回.
 *
 * @Annotation
 * @Target({"METHOD"})
 */
final class ApiError extends ReturnItem
{
    /**
     * 状态码.
     *
     * @var int
     */
    public $code = -1;

    /**
     * 返回类型.
     *
     * @Enum({"success","error","fail","notice","tips"})
     *
     * @var string
     */
    public $type = 'fail';
    /**
     * 返回提示.
     *
     * @var string
     */
    public $message = '操作失败';

    /**
     * 时间戳.
     *
     * @var int
     */
    public $timestamp = TIME;

    /**
     * 返回值
     *
     * @var object|array|string|int|float|bool|null
     */
    public $value = [];
}
