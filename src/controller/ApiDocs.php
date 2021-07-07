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
		$markdown_tpl = file_get_contents(__DIR__ . '/markdown.tpl');
		echo $markdown_tpl;
		return;
	}

	/**
	 * API 文档.
	 * @Route("/docs/api", method="GET")
	 * @return \think\Response
	 */
	public function api(Request $request)
	{
		$param = $request->param();
		// return json($param);
		$dumper = new Dumper();

		$docs = Cache::get('apiDocs');
		// dd($docs);
		$title = 'API接口文档';
		// $header = '<div class="layui-logo layui-hide-xs layui-bg-black">API接口文档</div>' . PHP_EOL;
		// $header .= '<ul class="layui-nav layui-layout-left" lay-filter="header">' . PHP_EOL;
		// $header = '<li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">' . PHP_EOL;
		// $header .= ' <i class="layui-icon layui-icon-spread-left"></i>' . PHP_EOL;
		// $header .= '</li>' . PHP_EOL;
		$header = '' . PHP_EOL;
		$menu = '' . PHP_EOL;
		$mark = '' . PHP_EOL;

		//     <a class="" href="javascript:;" module="user">user</a>

		//   </li>
		// $mark .= '> [字段映射](http://' . config('little.domain') . '/docs/api/fields)' . PHP_EOL;
		// $mark .= '' . PHP_EOL;
		// $mark .= '> 接口地址： http://' . config('little.domain') . PHP_EOL;
		// $mark .= '>>> https://' . config('little.domain') . PHP_EOL;
		// $mark .= '' . PHP_EOL;
		foreach ($docs as $k => $v) {
			if ($k == 'api') {
				$header .= sprintf('<li class="layui-nav-item layui-hide-xs layui-this"><a href="javascript:;" data="%s">%s</a></li>', $k, $k) . PHP_EOL;
			} else {
				$header .= sprintf('<li class="layui-nav-item layui-hide-xs "><a href="javascript:;" data="%s">%s</a></li>', $k, $k) . PHP_EOL;
			}
			if ($k !== $param['layer']) {
				continue;
			}

			// $menu .= '# ' . $this->char($k) . '  ' . PHP_EOL;
			// $menu .= ' ---' . PHP_EOL;
			// $mark .= '' . PHP_EOL;
			foreach ($v as $key => $item) {
				// $menu .= '##  ' . $this->char($key) . '  ' . PHP_EOL;
				// dd($item);
				$menu .= '<li class="layui-nav-item" >' . PHP_EOL;

				$menu .=  sprintf('<a class="" href="javascript:;" module="%s">%s</a>', $this->char($key), $this->char($item[$key]['title']??$key)) . PHP_EOL;
				if ($key !== $param['module']) {
					// continue;
				}
				// dd($menu);
				$menu .=  '<dl class="layui-nav-child">' . PHP_EOL;
				foreach ($item as $class) {
					//
					//       <dd><a href="javascript:;" data="user">用户管理</a></dd>
					//       <dd><a href="javascript:;" data="group">用户组</a></dd>
					//       <dd><a href="javascript:;" data="access">用户权限</a></dd>
					//     </dl>
					$menu .=  sprintf(' <dd> <a class="" href="javascript:;" data="%s">%s</a>  </dd>', $this->char($class['group']), $this->char($class['title'])) . PHP_EOL;

					// $menu .= '### ' . $this->char($class['title']) . '  ' . ' v' . $class['version'] . PHP_EOL;
					// $menu .= '' . PHP_EOL;
					// $menu .= '' . PHP_EOL;

					// dd($class);
					if ($param['module']&&$key !== $param['module']) {
						continue;
					}
					if ($param['class']&&$class['group'] !== $param['class']) {
						continue;
					}
					$mark .= '> # ' . $this->char($class['title']) . '  版本： v ' . $class['version'] . PHP_EOL;
					foreach ($class['methods'] as $method) {
						$auth = $method['auth'] ===false ? '需要' : '不需要';
						$mark .= ' - ## ' . $this->char($method['title']) . PHP_EOL;
						$mark .= ' >  ' . PHP_EOL;
						$mark .= ' >   |    请求类型    |    路径    |   是否需要授权   |     授权方式     |' . PHP_EOL;
						$mark .= ' >   | :--------: | :--------: | :--------: | :--------: | :--------: |' . PHP_EOL;
						$mark .= ' >   | ' . $method['method'] . ' | ' . $method['path'] . ' | ' . $auth . ' | Header |' . PHP_EOL;
						$mark .= ' >  ' . PHP_EOL;
						$mark .= '  >   Headers ' . PHP_EOL;
						$mark .= '  >>   ```json' . PHP_EOL;
						// $dumper->dump($method['headers'])
						// $menu .= '  >>>   ' . json_encode($method['headers']) . PHP_EOL;
						$mark .= '  >>' . $dumper->dump($method['headers']) . PHP_EOL;
						// $menu .= '  >>>   ' ;
						$mark .= '  >>   ```' . PHP_EOL;
						$mark .= ' ' . PHP_EOL;
						$mark .= ' >  ' . PHP_EOL;
						$mark .= ' >   |    字段    |    数据类型    |   是否必填   |     释译    |    默认值   |' . PHP_EOL;
						$mark .= ' >   | :--------: | :--------: | :--------: | :--------: | :--------: |' . PHP_EOL;
						foreach ($method['param'] as $find => $desc) {
							$required=$desc['required'] ? '是' : '否';
							$mark .= ' >   |  ' . $find . '  |  ' . $desc['type'] . '  |  ' . $required . '  |  ' . $desc['desc'] . '  |  ' . $desc['default'] . '  |' . PHP_EOL;
							// dd($mark);
						}
						$mark .= '' . PHP_EOL;
						$mark .= ' >>  ' . $method['desc'] . PHP_EOL;
						$mark .= '' . PHP_EOL;
						$mark .= ' >  成功响应' . PHP_EOL;
						$mark .= '  >>   ```json' . PHP_EOL;
						$mark .= '  >>' . $dumper->dump($method['success']) . PHP_EOL;
						// $menu .= '{' . PHP_EOL;
						// foreach ($method['success'] as $success_key => $success_item) {
						// 	if (is_array($success_item)) {
						// 		foreach ($success_item as $new_success_key => $new_success_item) {
						// 			if (! is_array($success_item)) {
						// 				$menu .= '     ' . $new_success_key . ':' . $new_success_item . PHP_EOL;
						// 			}
						// 		}
						// 	} else {
						// 		$menu .= '     ' . $success_key . ':' . $success_item . PHP_EOL;
						// 	}
						// }
						// $menu .= '}' . PHP_EOL;
						$mark .= '  >>   ```' . PHP_EOL;
						$mark .= '' . PHP_EOL;
						$mark .= ' >  失败响应' . PHP_EOL;
						$mark .= '  >>   ```json' . PHP_EOL;
						// $error = json_encode($method['error']);
						$mark .= '  >>' . $dumper->dump($method['error']) . PHP_EOL;
						$mark .= '  >>   ```' . PHP_EOL;
						$mark .= '' . PHP_EOL;

						// $Parsedown = new Parsedown();
						// echo $Parsedown->text($menu);
						// dd($mark);
					}
				}
				$menu .=  '</dl>' . PHP_EOL;
				$menu .= '</li>' . PHP_EOL;
			}
		}
		// dd($menu);
		// $header .= '</ul>' . PHP_EOL;
		$header .= '' . PHP_EOL;
		$Parsedown = new Parsedown();
		$header_markdown = $Parsedown->text($header);
		$menu_markdown = $Parsedown->text($menu);
		$mark_markdown = $Parsedown->text($mark);
		echo $this->view($title, $header_markdown, $menu_markdown, $mark_markdown);
		dd();
		// dd($mark);
		// $Parsedown = new Parsedown();
		// $markdown = $Parsedown->text($menu);
		// $markdown_tpl = file_get_contents(__DIR__ . '/markdown.tpl');
		return json([
			'header'=>$header_markdown,
			'menu'=>$menu_markdown,
			'markdown'=>$mark_markdown,
		]);
		// exit();
		// // dd($html_tpl);

		// // file_put_contents('docs' . '.md', $md_tpl);
		// // file_put_contents('doc' . '.html', $html_tpl);
		// echo $this->view($title, '', $markdown); // $markdown_tpl;
		// // echo $Parsedown->text($menu);
		// dd();
		// dd($docs['admin']['user']);

		// return json($docs);
	}

	/**
	 * API 文档.
	 * @Route("/docs/view", method="GET")
	 * @return \think\Response
	 */
	public function view($title, $header, $menu, $markdown)
	{
		// $markdown_tpl = file_get_contents(__DIR__ . '/markdown.tpl');
		// echo $markdown_tpl;
		// exit();
		echo <<<HTML
			<!DOCTYPE html>
			<html lang="zh-CN">

			<head>
			  <meta charset="UTF-8">
			  <title>{$title} - Powered By @小小只</title>
			  <meta name="viewport"
			    content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
			  <style type="text/css">
			    body {
			      background: #222;
			      margin: auto;
			    }
			    .layout-fixed {
			      width: 100%;
			      margin: 0 auto;
			      float: left;
			    }
			    .header {
			      margin-bottom: 5px;
			    }
			    .sidebar {
			      width: 220px;
			      margin-right: 20px;
			      display: inline;
			      float: left;
			      margin-bottom: 5px;
			    }

			    .content {
			      width: auto;
			      /*主内容宽度*/
			      display: inline;
			      float: left;
			      color: #fff;
			      margin-bottom: 5px;
			    }
			    .header {
			      height: 75px;
			      background-color: #212836;
			      margin-bottom: 5px;
			      box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.2);
			    }

			    .sidebar {
			      /* height: 350px; */
			      background-color: #3c424b;
			      box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.2);
			    }
			    .layui-nav-item {
			      list-style: none;
			    }

			    .content {
			      background-color: #212836;
			      height: auto;
			      box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.2);
			    }

			    a {
			      color: #fff;
			      text-decoration:none
			    }
			  </style>
			  <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.0.1/build/styles/default.min.css">
			</head>

			<body class="layout-fixed">
			  <div class="header">
			    <!-- <div class="layui-header">
			      <div class="layui-logo layui-hide-xs layui-bg-black">接口文档</div>
			      <ul class="layui-nav layui-layout-left" lay-filter="header" id="header">
			        {$header}
			      </ul>
			    </div> -->

			  </div>
			  <div class="aside sidebar">
			    <div class="layui-side layui-bg-black">
			      <div class="layui-side-scroll">
			        <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
			        <ul class="layui-nav layui-nav-tree" lay-shrink="all" lay-unselect lay-filter="side" id="menu">
			          {$menu}
			        </ul>
			      </div>
			    </div>

			  </div>
			  <div class="content">
			    {$markdown}
			  </div>
			  <!-- <div class="footer">
						<h2>Footer Content</h2>
			  </div> -->
			  <script>hljs.highlightAll();</script>
			  <script src="//www.layuicdn.com/layui/layui.js" charset="utf-8"></script>
			  <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.0.1/build/highlight.min.js" charset="utf-8"></script>

			  <script>
			    //JS
			    layui.use(['element', 'layer', 'util'], function () {
			      var element = layui.element
			        , layer = layui.layer
			        , util = layui.util
			        , $ = layui.$;
			      var param = {
			        layer: 'admin',
			        module: '',
			        class: '',
			      };
			      //头部事件
			      util.event('lay-header-event', {
			        //左侧菜单事件
			        menuLeft: function (othis) {
			          layer.msg('展开左侧菜单的操作', { icon: 0 });
			        }
			      });
			      element.on('nav(header)', function (elem) {
			        var data = elem.attr('data');
			        param.layer = data
			        console.log(param);
			        ajax();
			      });
			      element.on('nav(side)', function (elem) {
			        var classer = elem.attr('data');
			        var module = elem.attr('module');
			        if (module) {
			          param.module = module
			        }
			        if (classer) {
			          param.class = classer
			        }
			        console.log(param);
			        ajax();
			      });
			      function ajax() {
			        window.location.href = window.location.origin + '/docs/api?layer=' + param.layer + '&module=' + param.module + '&class=' + param.class;
			      };
			    });
			  </script>
			</body>

			</html>
			HTML;
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
