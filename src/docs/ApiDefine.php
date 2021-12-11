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

namespace littler\docs;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * api 定义.
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class ApiDefine extends ApiItem
{
    /**
     * 请求方式.
     *
     * @Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"})
     *
     * @var string
     */
    public $method = '*';
    /**
     * 返回值
     *
     * @var string
     */
    public $value = null;
}
