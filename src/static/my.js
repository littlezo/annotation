
function openTo(code) {
    let chainList = fileChainList[code];
    for (let i=0; i<chainList.length; i++) {
        document.getElementById(chainList[i]).classList.remove('hide');
    }
    document.getElementById(code).style.color = '#FF9800';
}

function redirect(obj) {

    let id = obj.getAttribute('id');
    if (id !== null) {
        let url = location.origin + '/docs/' + id + '.html?code=' + id;
        location.href = url;
    }
}

function parseQuery(query) {
    let pos = query.indexOf('?');
    if (pos === -1) {
        return {};
    }

    query = query.substring(pos + 1);
    query = decodeURIComponent(query);

    if (query.length === 0) {
        return {};
    }

    let items = null, item = null, name = null, value = null;

    if (query.indexOf('&') === -1) {
        items = query.split("=");
        name = items[0];
        value = items[1];
        let tmp = {};
        tmp[name] = value;
        return tmp;
    }

    let args = {};
    items = query.split("&");
    for(let i=0; i < items.length; i++){
        item = items[i].split("=");
        if(item[0]){
            name = item[0];
            value = item[1] ? item[1] : "";
            args[name] = value;
        }
    }

    return args;
}

function showSider() {

    // console.log('window.screen.width ',window.screen.width);
    // console.log('document.body.clientWidth ',document.body.clientWidth);
    // console.log('document.body.clientHeight ',document.body.clientHeight);
    // console.log('document.body.scrollWidth ',document.body.scrollWidth);
    // console.log('document.body.scrollHeight ',document.body.scrollHeight);
    // console.log('document.body.scrollTop ',document.body.scrollTop);
    // console.log('document.body.scrollLeft ',document.body.scrollLeft);
    // console.log('document.body.offsetHeight ',document.body.offsetHeight);

    let sidebar = document.getElementById('sidebar');
    let windowWidth = window.screen.width;

    // sidebar.style.width = windowWidth;

    if (sidebar.style['margin-left'] === '0px') {
        sidebar.style['margin-left'] = '-'+windowWidth+'px';
    } else {
        sidebar.style['margin-left'] = '0px';
    }
}

function togglePanel(obj) {

    let currentDisplayStatus = obj.getAttribute('show');
    let is_show = true;
    if (currentDisplayStatus === null || currentDisplayStatus === '0') {
        is_show = true;
        obj.setAttribute('show', '1');
    } else {
        is_show = false;
        obj.setAttribute('show', '0');
    }

    let childs = obj.children;
    console.log(childs);
    for (let i=0; i<childs.length; i++) {
        if (childs[i].classList.contains('panel-body')) {

            if (is_show) {
                childs[i].classList.remove('hide');

            } else {
                childs[i].classList.remove('hide');
                childs[i].classList.add('hide');
            }
        }
    }

    obj.onclick = function (e) {
        e.stopPropagation();
    }
    return false;
}