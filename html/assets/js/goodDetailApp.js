/**
 * Created by xu on 2015/12/29.
 */

//实例化vue对象
var goodsDetailApp = new Vue({
    el: '#goodsDetailApp',
    data: {
        companyId: '',
        userId: '',
        goodsId: getParam('goods_id'),
        goods: {},
        num: 1,
        goodsStatus: '',
        time: {},
        errorTip: ''
    },
    created: function() {
        var that = this;

        //检查登录状态,登录状态加载数据的函数
        checkLogin(function(userid) {
            that.userId = userid;//获取用户id

            //加载商品数据
            $.post('/Api/Goods/purchaseOneDetail',
                {
                    goods_id: getParam('goods_id'),
                    user_id: that.userId
                }
            ).done(function (res) {
                that.goods = res.data.goodinfo;//获取商品数据
                that.companyId = that.goods.company_id;

                //如果商品有相册，插入轮播图
                if (res.data.goodinfo.img) {
                    for(var i = 0; i < res.data.goodinfo.img.length; i++){
                        $('.swiper-wrapper').append("<div class='swiper-slide'><img src='" + res.data.goodinfo.img[i] + "' width='100%' ></div>");
                    }
                    //初始化轮播
                    var swiper = new Swiper('.goods-detail-swiper', {
                        pagination: '.swiper-pagination',
                        paginationClickable: true,
                        observer:true,
                        observeParents:true,
                        autoHeight: true,
                        //updateOnImagesReady : true,
                        autoplay : 3000,
                        autoplayDisableOnInteraction : false,
                        loop: true
                        //setWrapperSize :true
                    });
                    //初始化轮播---end
                }

                //计算轮播分页器宽度，并设置居中
                //var paginationWidth = 10 + res.data.goodinfo.img.length * 10 + res.data.goodinfo.img.length * 10;
                //$('.swiper-pagination').css('margin-left', -(paginationWidth / 2) + 'px');

                //计算商品的状态
                that.goodsStatus = goodsStatus(that.goods);

                if (that.goodsStatus == 'ending') {
                    var msec = 100;
                    var time = setInterval(function() {
                        var myDate = new Date();
                        var millisecond = that.goods.lucky_time * 1000 - myDate.getTime();
                        if(millisecond <= 0){//倒计时结束
                            //加载商品数据
                            $.post('/Api/Goods/purchaseOneDetail',
                                {
                                    goods_id: getParam('goods_id'),
                                    user_id: that.userId
                                }
                            ).done(function (res2) {
                                that.goods = res2.data.goodinfo;//获取商品数据
                                that.goodsStatus = goodsStatus(that.goods);
                            }).fail(function() {
                                alert("商品数据请求失败");
                            });
                            clearInterval(time);
                        } else {
                            var min = Math.floor(millisecond/60000);
                            var sec = Math.floor((millisecond%60000)/1000);
                            that.time = {'min': min, 'sec': sec, 'msec': --msec};
                            if (msec == 0) {
                                msec = 100;
                            }
                        }

                    },0.1);
                }

                //微信jssdk API调用（设置分享内容）
                wx.ready(function() {
                    //分享到朋友圈内容自定义
                    wx.onMenuShareTimeline({
                        title: '一团云购', // 分享标题
                        link: 'http://onebuy.91taoxue.cn/pay/goods-detail.html?goods_id=' + goodsDetailApp.goodsId
                            + '&company_id=' + that.companyId + '&', // 分享链接
                        imgUrl: 'http://onebuy.91taoxue.cn/'
                            + (goodsDetailApp.goods.img ? goodsDetailApp.goods.img[0] : goodsDetailApp.goods.goods_thumb)
                            // 分享图标
                    });

                    //分享给朋友内容自定义
                    wx.onMenuShareAppMessage({
                        title: '一团云购', // 分享标题
                        desc: goodsDetailApp.goods.goods_name, // 分享描述
                        link: 'http://onebuy.91taoxue.cn/pay/goods-detail.html?goods_id=' + goodsDetailApp.goodsId
                            + '&company_id=' + that.companyId + '&', // 分享链接
                        imgUrl: 'http://onebuy.91taoxue.cn/'
                            + (goodsDetailApp.goods.img ? goodsDetailApp.goods.img[0] : goodsDetailApp.goods.goods_thumb)
                            // 分享图标
                    });
                });
                //微信jssdk API调用（设置分享内容）---end
            }).fail(function() {
                alert("请求失败");
            });
            //加载商品数据---end

        });
        //检查登录状态---end
    },
    computed: {
        progressWidth: function() {//进度条计算
            var buyProgress = Math.floor((this.goods.purchase_num / this.goods.total_num) * 100);
            return buyProgress.toString() + '%';
        }
    },
    methods:{
        numAdd: function() {
            if(this.num < this.goods.limit_num){
                this.num ++;
            }
        },
        numCut: function() {
            if(this.num > 1){
                this.num --;
            }
        },
        modalOpen: function(targetModal) {
            event.preventDefault();
            $(targetModal).addClass('is-visible');

        },
        modalClose: function(targetModal) {
            event.preventDefault();
            $(targetModal).removeClass('is-visible');
        }
    }
});
//初始化轮播---end

//立即购买按钮点击事件
document.querySelector('#chooseWXPay').onclick = function() {
    //购买数量必须>=1
    if (!(goodsDetailApp.num >= 1)) {
        goodsDetailApp.errorTip = "至少购买一件商品";
        goodsDetailApp.modalOpen('.error-modal');
        return false;
    }
    //请求支付数据
    $.post('/Api/Wechatpay/purchase',
        {
            goods_sn: goodsDetailApp.goods.goods_sn,
            price: goodsDetailApp.num,
            goods_id: goodsDetailApp.goodsId,
            user_id: goodsDetailApp.userId
        }
    ).done(function(payInfo) {
        if(payInfo.errcode == '0'){
            var pay = JSON.parse(payInfo.data.wechatPay);//将支付数据解析为json格式

            //调用微信支付API
            wx.chooseWXPay({
                appId: pay.appId,
                timestamp: pay.timeStamp,
                nonceStr: pay.nonceStr,
                package: pay.package,
                signType: pay.signType,
                paySign: pay.paySign,
                cancel: function() {
                    $.post('/Api/Wechatpay/unPay',
                        {
                            goods_id: goodsDetailApp.goodsId,
                            user_id: goodsDetailApp.userId
                        }
                    );
                },
                success: function (payRes) {
                    // 支付成功后的回调函数
                    window.location = '../pay-result.html?goods_id=' + goodsDetailApp.goodsId
                        + '&num=' + goodsDetailApp.num
                        + '&company_id=' + goodsDetailApp.goods.company_id;
                }
            });
            //调用微信支付API---end

        } else if(payInfo.errcode == '90001') {
            goodsDetailApp.errorTip = "超过限购数，无法购买！";
            goodsDetailApp.modalOpen('.error-modal');
        } else if (payInfo.errcode == '90003') {
            goodsDetailApp.errorTip = "商品剩余数量不足！";
            goodsDetailApp.modalOpen('.error-modal');
        } else if (payInfo.errcode == '90008') {
            goodsDetailApp.errorTip = "您的账户被冻结，无法购买";
            goodsDetailApp.modalOpen('.error-modal');
        } else {
            goodsDetailApp.errorTip = "购买失败，错误码：" + payInfo.errcode;
            goodsDetailApp.modalOpen('.error-modal');
        }

    }).fail(function() {
        alert("获取支付数据失败");
    });
    //请求支付数据---end
};
//立即购买按钮点击事件---end