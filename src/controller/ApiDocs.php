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

namespace littler\annotation\controller;

use littler\annotation\Route;
use littler\BaseController;
use Nette\PhpGenerator\Dumper;
use Parsedown;
use think\facade\Cache;
use think\Request;

class ApiDocs extends BaseController
{
	/**
	 * API 文档.
	 * @Route("/docs", method="GET")
	 * @return \think\Response
	 */
	public function index()
	{
		echo file_get_contents(__DIR__ . '/preview.tpl');
		return;
	}

	/**
	 * API 文档.
	 * @Route("/docs/api", method="POST")
	 * @return \think\Response
	 */
	public function api(Request $request)
	{
		$host = config('little.domain') ?: $request->header('host');
		$param = $request->param();
		$dumper = new Dumper();
		$docs = Cache::get('apiDocs');
		$mark = 'API接口文档' . PHP_EOL;
		// $mark .= '> [字段映射](http://' . config('little.domain') . '/docs/api/fields)' . PHP_EOL;
		$mark .= '' . PHP_EOL;
		// dd($docs);
		foreach ($docs as $k => $v) {
			$mark .= '> 接口地址： http://' . $host . '  Or  ' . 'https://' . $host . PHP_EOL;
			$mark .= '> websocket ws://' . $host . '  Or  ' . 'wss://' . $host . PHP_EOL;
			$mark .= '> http 接口前缀: /' . $this->char($k) . PHP_EOL;
			$mark .= '' . PHP_EOL;
			if ($k !== 'admin') {
				// continue;
			}
			$mark .= '# ' . $this->char($k) . '  ' . PHP_EOL;
			foreach ($v as $key => $item) {
				$mark .= '##  ' . $this->char($key) . '  ' . PHP_EOL;
				foreach ($item as $class) {
					$mark .= '### ' . $this->char($class['title']) . PHP_EOL;
					foreach ($class['methods'] as $method) {
						$mark .= '#### ' . $this->char($method['title']) . PHP_EOL;
						$auth = $method['auth'] ===false ? '需要' : '不需要';
						$mark .= '>   版本： v ' . $class['version'] . PHP_EOL;
						$mark .= ' >  ' . PHP_EOL;
						$mark .= ' >   |    请求类型    |    路径    |   是否需要授权   |     授权方式     |' . PHP_EOL;
						$mark .= ' >   | :--------: | :--------: | :--------: | :--------: | :--------: |' . PHP_EOL;
						$mark .= ' >   | ' . $method['method'] . ' | ' . $method['path'] . ' | ' . $auth . ' | Header |' . PHP_EOL;
						$mark .= ' >  ' . PHP_EOL;
						$mark .= '  >   Headers ' . PHP_EOL;
						$mark .= '```json' . PHP_EOL;
						$mark .= json_encode($method['headers'], JSON_UNESCAPED_UNICODE) . PHP_EOL;
						$mark .= '```' . PHP_EOL;
						// $mark .= '```php' . PHP_EOL;
						// $mark .=  $dumper->dump($method['headers']) . PHP_EOL;
						// $mark .= '```' . PHP_EOL;
						$mark .= ' ' . PHP_EOL;
						$mark .= ' >  ' . PHP_EOL;
						$mark .= ' >   |    字段    |    数据类型    |   是否必填   |     释译    |    默认值   |' . PHP_EOL;
						$mark .= ' >   | :--------: | :--------: | :--------: | :--------: | :--------: |' . PHP_EOL;
						foreach ($method['param'] as $find => $desc) {
							$required=$desc['required'] ? '是' : '否';
							$type=$desc['type']??'';
							$param_desc=$desc['desc']??'';
							$default=$desc['default']??'';
							$mark .= ' >   |  ' . $find . '  |  ' . $type . '  |  ' . $required . '  |  ' . $param_desc . '  |  ' . $default . '  |' . PHP_EOL;
						}
						$mark .= '' . PHP_EOL;
						$mark .= ' >>  ' . $method['desc'] . PHP_EOL;
						$mark .= '' . PHP_EOL;
						$mark .= ' >  成功响应' . PHP_EOL;
						$mark .= '```json' . PHP_EOL;
						$mark .= json_encode($method['success'], JSON_UNESCAPED_UNICODE) . PHP_EOL;
						$mark .= '```' . PHP_EOL;
						// $mark .= '```php' . PHP_EOL;
						// $mark .= $dumper->dump($method['success']) . PHP_EOL;
						// $mark .= '```' . PHP_EOL;
						$mark .= '' . PHP_EOL;
						$mark .= ' >  失败响应' . PHP_EOL;
						$mark .= '```json' . PHP_EOL;
						$mark .= json_encode($method['error'], JSON_UNESCAPED_UNICODE) . PHP_EOL;
						$mark .= '```' . PHP_EOL;
						// $mark .= '```php' . PHP_EOL;
						// $mark .= $dumper->dump($method['error']) . PHP_EOL;
						// $mark .= '```' . PHP_EOL;
					}
				}
			}
		}
		$Parsedown = new Parsedown();
		$mark_markdown = $Parsedown->text($mark);
		echo $mark;
		return;
	}

	protected function char($data)
	{
		if (! empty($data)) {
			$fileType = mb_detect_encoding($data, ['UTF-8', 'GBK', 'LATIN1', 'BIG5'], true);
			if ($fileType != 'UTF-8') {
				$data = mb_convert_encoding($data, 'utf-8', $fileType);
			}
		}
		return $data;
	}
}
