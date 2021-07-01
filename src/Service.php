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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use think\App;
use think\Cache;
use think\Config;

class Service extends \think\Service
{
	use InteractsWithRoute;
	use InteractsWithInject;
	use InteractsWithModel;
	use InteractsWithDocs;

	/** @var Reader */
	protected $reader;

	public function boot(Reader $reader)
	{
		$this->reader = $reader;

		//注解路由
		$this->registerAnnotationRoute();

		//自动注入
		$this->autoInject();

		//模型注解方法提示
		$this->detectModelAnnotations();
		// dd($this);
        // 注册 api文档
		$this->registerAnnotationDocs();
	}

	public function register()
	{
		AnnotationReader::addGlobalIgnoredName('mixin');

		// TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
		AnnotationRegistry::registerLoader('class_exists');

		$this->app->bind(Reader::class, function (App $app, Config $config, Cache $cache) {
			$store = $config->get('annotation.store');

			return new CachedReader(new AnnotationReader(), $cache->store($store), $app->isDebug());
		});
	}
}
