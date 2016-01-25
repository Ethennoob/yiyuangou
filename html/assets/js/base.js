(function() {
    /**
     * 微信jssdk配置
     * @author: 文婷
     * @create: 2015/12/29 14:59
     * @modify: 2015/12/29 14:59
     */
    $.post('/Api/Wechatpay/wechatPayConfig',
        {
            url: window.location.href
        }
    ).done(function(res) {
        wx.config({
            debug: false,
            appId: res.data.wechatConfig.appid,
            timestamp: res.data.wechatConfig.timestamp,
            nonceStr: res.data.wechatConfig.noncestr,
            signature: res.data.wechatConfig.signature,
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ]
        });
    }).fail(function() {

    });

    if (window.location.href.indexOf('goods-detail.html') < 0) {
        shareIndex(getParam('company_id'));
    }
})();
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
    var year = myDate.getFullYear(),
        month = myDate.getMonth() + 1,
        date = myDate.getDate(),
        hour = myDate.getHours(),
        min = myDate.getMinutes();

    month = month < 10? '0' + String(month) : month;
    date = date < 10? '0' + String(date) : date;
    hour = hour < 10? '0' + String(hour) : hour;
    min = min < 10? '0' + String(min) : min;

    return year + '-' + month + '-' +  date + ' '
        + hour + ':' + min;
});

//Vue.component('error-modal', {
//    props: ['modalName',
//            'errorTip'],
//    data: function () {
//        return { isVisible: false }
//    },
//    template: '<button v-on:click="modalClose(modalName)">aa</button>',
//    //'<div v-on:click="modalClose(modalName)" v-bind:class="{\'is-visible\': isVisible, modalName: true}" role="alert">'
//    //+ '<div v-on:click.stop class="popup-container bg-white">'
//    //+ '<a v-on:click"modalClose(modalName)" href="javascript:;" class="popup-close"></a>'
//    //+ '<div class="error-tip">{{errorTip}}</div>'
//    //+ '<div v-on:click="modalClose(modalName)" class="error-confirm-btn"> 确定</div>'
//    //+ '</div>'
//    //+ '</div>',
//    methods: {
//        modalOpen: function(targetModal) {
//            alert("aa");
//            //this.isVisible = true;
//            //event.preventDefault();
//            //$(targetModal).addClass('is-visible');
//        },
//        modalClose: function(targetModal) {
//            alert("aa");
//            //event.preventDefault();
//            //$(targetModal).removeClass('is-visible');
//        }
//    }
//});

/**
 * 用于计算每个商品的状态
 * @param: {object} goods 商品数据，可能是数组或json对象
 * @return: {string} 'ing'正在进行中/ 'ending'正在揭晓/ 'end'已揭晓
 * @author: 文婷
 * @create: 2015/12/23 21:49
 * @modify: 2015/12/28 14:59
 */
function goodsStatus(goods) {
    //获取当前时间
    var mydate = new Date();
    var nowTime = mydate.getTime();
    if(goods.length){//多个商品
        var arr = [];
        for (var i = 0; i < goods.length; i++) {
            if (goods[i].last_num > 0) {
                arr[i] = 'ing';
            } else {
                arr[i] = (goods[i].lucky_time * 1000) >= nowTime ? 'ending' : 'end';
            }
        }
        return arr;
    } else {//单个商品
        if (goods.last_num > 0) {
            return 'ing';
        }  else {
            var rt = (goods.lucky_time * 1000) >= nowTime ? 'ending' : 'end';
            return rt;
        }
    }

}

/**
 * 检测登录状态
 * @param: {function} func 登录后执行的回调，用于加载数据
 * @return: void
 * @author: 文婷
 * @create: 2015/12/23 21:49
 * @modify: 2015/12/23 21:49
 */
function checkLogin(func) {
    var url = window.location.href;
    $.post('/Api/User/checklogin').done(function(res) {
        if (res.errcode == '90005') {
            //获取新用户信息
            window.location = '/Api/User/getOpenID/?refer=' + url;
        } else if (res.data.user_id) {

            func(res.data.user_id);//登录后的回调函数

        }
    });
}

/**
 * 微信分享自定义（分享首页链接）
 * @param: {int or String}companyId 专区id，判断要分享哪个专区的首页
 * @return: void
 * @author: 文婷
 * @create: 2015/12/29 17:21
 * @modify: 2016/01/15 22:54
 */
function shareIndex(companyId){
    wx.ready(function() {
        //分享到朋友圈内容自定义
        wx.onMenuShareTimeline({
            title: '一团云购', // 分享标题
            link: 'http://onebuy.91taoxue.cn?company_id=' + companyId + '&', // 分享链接
            imgUrl: 'http://onebuy.91taoxue.cn/assets/i/logo.jpg' // 分享图标
        });
        //分享给朋友内容自定义
        wx.onMenuShareAppMessage({
            title: '一团云购', // 分享标题
            desc: '一元购，购你所想，购你所爱，一元购天下', // 分享描述
            link: 'http://onebuy.91taoxue.cn?company_id=' + companyId + '&', // 分享链接
            imgUrl: 'http://onebuy.91taoxue.cn/assets/i/logo.jpg' // 分享图标
        });
    });
}