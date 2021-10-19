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

namespace littler\annotation\docs;

use Doctrine\Common\Annotations\Annotation;

abstract class GroupItem extends Annotation
{
    /**
     * 应用名称.
     *
     * @var string
     */
    public $app_name;

    /**
     * 应用标题.
     *
     * @var string
     */
    public $app_title;

    /**
     * 模块名称.
     *
     * @var string
     */
    public $module_name;

    /**
     * 模块标题.
     *
     * @var string
     */
    public $module_title;

    /**
     * 组名称.
     *
     * @var string
     */
    public $name;
    /**
     * 组标题.
     *
     * @var string
     */
    public $title;

    /**
     * 组描述.
     *
     * @var string
     */
    public $desc;

    public function getResult()
    {
        return array_intersect_key(get_object_vars($this), array_flip([
           'app_name', 'app_title',  'module_name', 'module_title', 'name', 'title', 'desc',
        ]));
    }
}
