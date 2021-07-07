<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>API接口文档 - Powered By @小小只</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">
  <link rel="stylesheet" href="//www.layuicdn.com/layui/css/layui.css" media="all">
  <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.0.1/build/styles/default.min.css">
  <style>
    /* 移动端 */
    @media screen and (max-width: 768px) {

      .layui-layout-admin .layui-layout-left,
      .layui-layout-admin .layui-body,
      .layui-layout-admin .layui-footer {
        left: 0;
      }

      .layui-layout-admin .layui-side {
        left: -300px;
      }
    }

    .layui-layout-admin .layui-header,
    .layui-layout-admin .layui-footer {
      background-color: #212836;
      color: #fff;
    }

    .layui-body {
      background-color: #3c424b;
    }

    .main {
      background-color: #3c424b;
      width: 100%;
      /* height: 100%; */
      padding: 0 10px;
    }

    .content {
      width: 98%;
      background-color: #212836;
    }

    /* div {
      margin: auto;
      padding: unset !important;
    } */

    /* ul {
      padding-inline-start: 10px !important;
    } */
  </style>
</head>

<body>

  <div class="layui-layout layui-layout-admin">
    <div class="layui-header">
      <div class="layui-logo layui-hide-xs layui-bg-black">接口文档</div>
      <ul class="layui-nav layui-layout-left" lay-filter="header" id="header">
        <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
          <i class="layui-icon layui-icon-spread-left"></i>
        </li>
        <li class="layui-nav-item layui-hide-xs "><a href="javascript:;" data="other">other</a></li>
        <li class="layui-nav-item layui-hide-xs "><a href="javascript:;" data="admin">admin</a></li>
      </ul>
    </div>

    <div class="layui-side layui-bg-black">
      <div class="layui-side-scroll">
        <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
        <ul class="layui-nav layui-nav-tree" lay-shrink="all" lay-unselect lay-filter="side" id="menu">

        </ul>
      </div>
    </div>

    <div class="layui-body">
      <div class="main">
        <div class="content" id="markdown">
          {$markdown}
        </div>
      </div>
    </div>
    <div class="layui-footer">
      <!-- 底部固定区域 -->
      文档更新时间
    </div>
  </div>
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
        layer: 'api',
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
        console.log(window.location);
        // console.log(param)
        // $.ajax({
        // url: "docs/api",
        //   data: param,
        //     type: "get",
        //       dataType: "json",
        //         success: function (res) {
        //           // console.log(res);
        //           document.getElementById("header").innerHTML = res.header;
        //           document.getElementById("menu").innerHTML = res.menu;
        //           document.getElementById("markdown").innerHTML = res.markdown;

        //           // layer.alert(JSON.stringify(data), {
        //           //   title: data
        //           // });
        //         },
        // error: function (err) {
        //   console.log(err);

        // layer.alert(JSON.stringify(data), {
        //   title: data
        // });
        // }
        // });
      };
    });
  </script>
  <script>
    hljs.highlightAll();
  </script>
</body>

</html>
