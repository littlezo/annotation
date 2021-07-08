<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <title>文档中心</title>
    <meta name="robots" content="index,follow,archive">
    <meta name="description" content="文档中心"/>
    <meta name="copyright" content="文档中心"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Window-target" content="_top"/>
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/gh/vanessa219/b3log-index@7582df6ba7d52434a3e229cdbd56a06ae62b45c6/src/css/base.css" charset="utf-8"/>
    <!-- <script src="/static/vditor.js" defer></script> -->
    <style>
        .header {
            /* background-color: #fff; */
            box-shadow: rgba(0, 0, 0, 0.05) 0 1px 7px;
            /* border-bottom: 1px solid #e1e4e8; */
        }

        .wrapper {
            margin: 0 auto;
            max-width: 768px;
        }

        #outline {
            display: none;
            position: fixed;
            width: 200px;
            top: 10px;
            left: 0px;
            bottom: 86px;
            overflow: auto;
            font-size: 14px;
            color: #d1d5da;
            /* background-color: #fff; */
            line-height: 20px;
        }

        #outline ul {
            margin-left: 16px;
            list-style: none;
        }

        #outline > ul {
            margin-left: 0;
        }

        #outline li > span {
            cursor: pointer;
            border-left: 1px solid transparent;
            display: block;
            padding-left: 8px;
        }

        #outline li > span.vditor-outline__item--current {
            border-left: 1px solid #4285f4;
            color: #4285f4;
            /* background-color: #f6f8fa; */
        }

        #outline li > span:hover {
            color: #4285f4;
            /* background-color: #f6f8fa; */
        }

        @media (max-width: 768px) {
            #outline {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="fn-50"></div>
    <div id="preview"></div>
    <div class="fn-100"></div>
    <div id="vditorComments"></div>
    <div class="fn-100"></div>
</div>
<div id="outline"></div>
<!-- end main -->

<div class="footer">
    <div class="wrapper fn-clear">
        <span>本文档更新时间：</span>
        <div class="fn-right">
            © 2021 @小小只^v^
        </div>
    </div>
</div>
<script>
    const addScript = (path, callback) => {
  const scriptElement = document.createElement("script");
  scriptElement.src = path;
  scriptElement.async = true;
  document.head.appendChild(scriptElement);
  scriptElement.onload = () => {
    callback();
  };
};

const addStyle = (url) => {
  const styleElement = document.createElement("link");
  styleElement.rel = "stylesheet";
  styleElement.type = "text/css";
  styleElement.href = url;
  document.getElementsByTagName("head")[0].appendChild(styleElement);
};

const updateCode = (btnElement, code) => {
  if (btnElement.classList.contains("btn--red")) {
    return;
  } else {
    const redBtnElement = document.querySelector(".btn--red");
    if (redBtnElement) {
      redBtnElement.classList.remove("btn--red");
    }
    btnElement.classList.add("btn--red");
  }
};

const autoType = () => {
  const typeElement = document.getElementById("autoType");
  if (!typeElement) {
    return;
  }
  const texts = [];
  let textLength = 0;
  let time = 0;
  texts.forEach((text, i) => {
    if (i > 0) {
      textLength += text[i - 1].length + 20;
    }
    for (let j = 0; j < text.length; j++) {
      time += 200;
      setTimeout(() => {
        typeElement.innerHTML =
          text.substr(0, j + 1) +
          `<span class="caret" style="${
            j === text.length - 1 ? "animation-name:flash" : ""
          }"></span>`;
      }, time);
    }
    if (i !== texts.length - 1) {
      time += 2000;
      for (let k = 0; k < text.length; k++) {
        time += 50;
        setTimeout(() => {
          typeElement.innerHTML =
            typeElement.textContent.substr(
              0,
              typeElement.textContent.length - 1
            ) + '<span class="caret"></span>';
        }, time);
      }
    } else {
      setTimeout(() => {
        document.querySelector(".caret").style.animationName = "flash";
      }, time + 1);
    }
  });
};

addStyle("https://cdn.jsdelivr.net/npm/vditor@3.8.4/dist/index.css");
document.addEventListener("DOMContentLoaded", function () {
  autoType();

  if (document.getElementById("vditorComments")) {
    addScript(
      "https://cdn.jsdelivr.net/npm/vditor@3.8.4/dist/index.min.js",
      () => {
        const demoCodeElement = document.getElementById("vditorDemoCode");
        if (demoCodeElement) {
          Vditor.highlightRender(
            { lineNumber: true, enable: true },
            demoCodeElement
          );
          Vditor.codeRender(demoCodeElement);
        }
        if (typeof vditorScript !== "undefined") {
          vditorScript();
        }
      }
    );
  }
});

</script>
<script>
  const initRender = () => {
      fetch('docs/api',{method: 'POST'}).
      then(response => response.text()).
      then(markdown => {
        Vditor.preview(document.getElementById('preview'),
          markdown, {
            mode:"dark",
            markdown: {
                fixTermTypo: true,
                toc: true,
            },
            hljs:{
               enable:true,
               style:"dracula",
               lineNumber:true,
            },
            theme:{
                current:"dark",
                path:"https://cdn.jsdelivr.net/npm/vditor@3.8.6/dist/css/content-theme"
            },
            speech: {
              enable: true,
            },
            anchor: 1,
            after () {
              if (window.innerWidth <= 768) {
                return
              }
              const outlineElement = document.getElementById('outline')
              Vditor.outlineRender(document.getElementById('preview'), outlineElement)
              if (outlineElement.innerText.trim() !== '') {
                outlineElement.style.display = 'block'
                initOutline()
              }
            },
          })
      })
  }
  const initOutline = () => {
    const headingElements = []
    Array.from(document.getElementById('preview').children).forEach((item) => {
      if (item.tagName.length === 2 && item.tagName !== 'HR' && item.tagName.indexOf('H') === 0) {
        headingElements.push(item)
      }
    })

    let toc = []
    window.addEventListener('scroll', () => {
      const scrollTop = window.scrollY
      toc = []
      headingElements.forEach((item) => {
        toc.push({
          id: item.id,
          offsetTop: item.offsetTop,
        })
      })

      const currentElement = document.querySelector('.vditor-outline__item--current')
      for (let i = 0, iMax = toc.length; i < iMax; i++) {
        if (scrollTop < toc[i].offsetTop - 30) {
          if (currentElement) {
            currentElement.classList.remove('vditor-outline__item--current')
          }
          let index = i > 0 ? i - 1 : 0
          document.querySelector('span[data-target-id="' + toc[index].id + '"]').classList.add('vditor-outline__item--current')
          break
        }
      }
    })
  }
  vditorScript = initRender
</script>
</body>
</html>
