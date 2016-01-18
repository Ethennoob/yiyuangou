/**
 * 验证管理员登录状态
 * @param:
 * @return:
 * @author: 文婷
 * @create: 2015/12/23 21:49
 * @modify: 2016/01/15 11:26
 */
$.post('http://onebuy.ping-qu.com/Admin/Login/checkAdminLogin').done(function(data) {
    if(data.errcode == '70002'){
        window.location = 'login.html';
    }
}).fail(function() {
    alert("登录验证请求失败");
    window.location = 'login.html';
});

/**
 * 获取url中的参数
 * @param: {string} name 要获得的参数值的名字(user_id=56,则name为user_id)
 * @return: {string} 参数的值
 * @author: 文婷
 * @create: 2015/12/23 21:49
 * @modify: 2015/12/23 21:49
 */
function getParam(name) {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        var strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
        }
    }
    return theRequest[name];
}

/**
 * 自定义的vue过滤器，用于将时间戳转换成YYYY-MM-DD HH-MM的格式
 * @param:
 * @return: {string} 正确格式的时间字符串
 * @author: 文婷
 * @create: 2015/12/23 21:55
 * @modify: 2015/12/23 21:55
 */
Vue.filter('date', function (value) {
    var myDate = new Date(value * 1000);
    return myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' +  myDate.getDate() + ' '
        + myDate.getHours() + ':' + myDate.getMinutes();
});

/**
 * 侧边导航栏组件
 * @param:
 * @return:
 * @author: 文婷
 * @create: 2015/12/23 21:55
 * @modify: 2015/12/23 21:55
 */
Vue.component('sidebar', {
    template: '<div class="admin-sidebar am-offcanvas" id="admin-offcanvas"> ' +
    '<div class="am-offcanvas-bar admin-offcanvas-bar"> ' +
        '<ul class="am-list admin-sidebar-list"> ' +
            '<li><a href="index.html"><span class="am-icon-home"></span> 首页海报管理</a></li>' +
            '<li><a href="user.html"><span class="am-icon-users"></span> 会员管理</a></li>' +
            '<li class="admin-parent"><a class="am-cf" data-am-collapse="{target: \'#area-nav\'}">' +
                '<span class="am-icon-shopping-cart"></span> 专区管理' +
                '<span class="am-icon-angle-right am-fr am-margin-right"></span></a>' +
                '<ul class="am-list am-collapse admin-sidebar-sub" id="area-nav">' +
                    '<li><a href="add-area.html" class="am-cf"><span class="am-icon-plus"></span> 添加专区</a></li>' +
                    '<li><a href="area-list.html" class="am-cf"><span class="am-icon-list-alt"></span> 专区列表</a></li>' +
                '</ul>' +
            '</li>' +
            '<li class="admin-parent"><a class="am-cf" data-am-collapse="{target: \'#goods-nav\'}">' +
                '<span class="am-icon-shopping-cart"></span> 商品管理' +
                '<span class="am-icon-angle-right am-fr am-margin-right"></span></a>' +
                '<ul class="am-list am-collapse admin-sidebar-sub" id="goods-nav">' +
                    '<li><a href="add-goods.html" class="am-cf"><span class="am-icon-plus"></span> 添加商品</a></li>' +
                    '<li><a href="goods-list.html" class="am-cf"><span class="am-icon-list-alt"></span> 商品列表</a></li>' +
                '</ul>' +
            '</li>' +
            '<li class="admin-parent">' +
                '<a class="am-cf" data-am-collapse="{target: \'#thematic-nav\'}">' +
                '<span class="am-icon-bookmark"></span> 专题管理' +
                '<span class="am-icon-angle-right am-fr am-margin-right"></span></a>' +
                '<ul class="am-list am-collapse admin-sidebar-sub" id="thematic-nav">' +
                    '<li><a href="add-thematic.html" class="am-cf"><span class="am-icon-plus"></span> 添加专题</a></li>' +
                    '<li><a href="thematic-list.html" class="am-cf"><span class="am-icon-list-alt"></span> 专题列表</a></li>' +
                '</ul>' +
            '</li>' +
            '<li class="admin-parent">' +
                '<a class="am-cf" data-am-collapse="{target: \'#article-nav\'}">' +
                '<span class="am-icon-newspaper-o"></span> 文章管理' +
                '<span class="am-icon-angle-right am-fr am-margin-right"></span></a>' +
                '<ul class="am-list am-collapse admin-sidebar-sub" id="article-nav">' +
                    '<li><a href="add-article.html" class="am-cf"><span class="am-icon-plus"></span> 发表文章</a></li>' +
                    '<li><a href="article-list.html" class="am-cf"><span class="am-icon-list-alt"></span> 文章列表</a></li>' +
                '</ul>' +
            '</li>' +
            '<li><a href="sale-list.html"><span class="am-icon-money"></span> 销售明细</a></li>' +
            '<li><a href="prize-status.html"><span class="am-icon-gift"></span> 中奖情况</a></li>' +
            '<li><a href="order.html"><span class="am-icon-file-text"></span> 订单管理</a></li>' +
            '<li> <a href="logistics.html"><span class="am-icon-truck"></span> 物流管理</a></li>' +
            '<li><a href="administrator.html"><span class="am-icon-user"></span> 后台管理员管理</a></li>' +
            '<li class="admin-parent"><a class="am-cf" data-am-collapse="{target: \'#system-nav\'}">' +
                '<span class="am-icon-gears"></span> 系统设置' +
                '<span class="am-icon-angle-right am-fr am-margin-right"></span></a>' +
                '<ul class="am-list am-collapse admin-sidebar-sub" id="system-nav">' +
                    '<li><a href="limit-set.html" class="am-cf"><span class="am-icon-list-alt"></span> 认购上限设置</a></li>' +
                    '<li><a href="bvalue-set.html" class="am-cf"><span class="am-icon-list-alt"></span> B值设置</a></li>' +
                '</ul>' +
            '</li>' +
            '<li><a v-on:click="logout()" href="#"><span class="am-icon-sign-out"></span> 注销</a></li>' +
        '</ul>' +
    '</div>' +
    '</div>',
    methods: {
        logout:function() {
            window.location = 'http://onebuy.ping-qu.com/Admin/Login/logout';
        }
    }
});

/**
 * 顶栏组件
 * @param:
 * @return:
 * @author: 文婷
 * @create: 2015/12/23 21:55
 * @modify: 2015/12/23 21:55
 */
Vue.component('topbar', {
    template: '<header class="am-topbar admin-header">' +
    '<div class="am-topbar-brand"><strong>一元购</strong><small>后台管理系统</small></div>' +
    '<button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-success am-show-sm-only" data-am-collapse="{target: \'#topbar-collapse\'}">' +
        '<span class="am-sr-only">导航切换</span> <span class="am-icon-bars"></span>' +
    '</button>' +
    '<div class="am-collapse am-topbar-collapse" id="topbar-collapse">' +
        '<ul class="am-nav am-nav-pills am-topbar-nav am-topbar-right admin-header-list">' +
            '<li class="am-dropdown" data-am-dropdown>' +
                '<a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">' +
                    '<span class="am-icon-users"></span> 管理员 <span class="am-icon-caret-down"></span>' +
                '</a>' +
                '<ul class="am-dropdown-content">' +
                    '<li><a href="#"><span class="am-icon-user"></span> 资料</a></li>' +
                    '<li><a href="#"><span class="am-icon-cog"></span> 设置</a></li>' +
                    '<li><a v-on:click="logout()" href="#"><span class="am-icon-power-off"></span> 退出</a></li>' +
                '</ul>' +
            '</li>' +
            '<li class="am-hide-sm-only"><a href="javascript:;" id="admin-fullscreen"><span class="am-icon-arrows-alt"></span> <span class="admin-fullText">开启全屏</span></a></li>' +
        '</ul>' +
    '</div>' +
    '</header>',
    methods: {
        logout:function() {
            window.location = 'http://onebuy.ping-qu.com/Admin/Login/logout';
        }
    }
});

/**
 * 初始化图片上传插件
 * @param: {String}server 接收数据的接口地址；
 *         {String}pick 上传图片按钮的id,例如'#filesPick'；
 *         {String}fileVal 图片上传域name；
 *         {JSON}formData 伴随着每次图片上传的额外数据,
 *         {String}thumbnail 显示缩略图的容器,例如'#list'；
 * @return: object(返回插件实例)
 * @author: 文婷
 * @create: 2016/01/11 23:20
 * @modify: 2015/01/11 23:20
 */
function uploaderInit(server, pick, fileVal, formData, thumbnail, fileNumLimit) {
    var up = WebUploader.create({
        auto: false,// 选完文件后，是否自动上传。

        swf: 'http://onebuy.ping-qu.com/admin/assets/swf/Uploader.swf',// swf文件路径

        server: server, // 文件接收服务端。

        pick: pick, // 选择文件的按钮。可选。// 内部根据当前运行是创建，可能是input元素，也可能是flash.

        accept: {// 只允许选择图片文件。
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        },

        fileVal: fileVal,

        formData: formData,//除文件以外上传的额外数据

        fileNumLimit: fileNumLimit
    });

    // 当有文件添加进来的时候
    up.on( 'fileQueued', function( file ) {
        var $list = $(thumbnail);//获取缩略图展示容器
        var thumbnailWidth = 100,
            thumbnailHeight = 100;//设置缩略图宽高

        var $li = $(
                '<div id="' + file.id + '" class="file-item thumbnail">' +
                '<img>' +
                '<div class="info">' + file.name + '</div>' +
                '</div>'
            ),
            $img = $li.find('img');

        // $list为容器jQuery实例
        $list.append( $li );//插入缩略图

        // 创建缩略图
        // 如果为非图片文件，可以不用调用此方法。
        // thumbnailWidth x thumbnailHeight 为 100 x 100
        up.makeThumb( file, function( error, src ) {
            if ( error ) {
                $img.replaceWith('<span>不能预览</span>');
                return;
            }

            $img.attr( 'src', src );
        }, thumbnailWidth, thumbnailHeight );

    });

    return up;//返回插件实例
}