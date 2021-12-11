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

abstract class ResultItem extends Annotation
{
    /**
     * 字段.
     *
     * @var string
     */
    public $field;

    /**
     * 名称.
     *
     * @var string
     */
    public $name;

    /**
     * 类型.
     *
     * @var string
     */
    public $type;

    /**
     * 描述.
     *
     * @var string
     */
    public $desc;

    public function getResult()
    {
        return array_intersect_key(get_object_vars($this), array_flip([
            'field', 'name', 'type', 'desc',
        ]));
    }
}
