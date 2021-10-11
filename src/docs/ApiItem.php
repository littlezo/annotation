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

abstract class ApiItem extends Annotation
{
    /**
     * 名字.
     *
     * @var string
     */
    public $name;

    /**
     * 版本. x.y.z.
     *
     * @var string
     */
    public $version;

    /**
     * 请求方式.
     *
     * @Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"})
     *
     * @var string
     */
    public $method;

    /**
     * 是否验证.
     *
     * @var bool
     */
    public $verify;

    /**
     * 权限标识.
     *
     * @var string|int|array
     */
    public $permission;

    /**
     * 接口描述.
     *
     * @var string
     */
    public $desc;

    public function getResult()
    {
        return array_intersect_key(get_object_vars($this), array_flip([
            'code', 'type', 'message', 'timestamp', 'result',
        ]));
    }
}
