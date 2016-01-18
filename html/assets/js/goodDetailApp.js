/**
 * Created by xu on 2015/12/29.
 */
//初始化轮播
var swiper = new Swiper('.goods-detail-swiper', {
    pagination: '.swiper-pagination',
    paginationClickable: true,
    observer:true,
    observeParents:true,
    autoHeight: true,
    //updateOnImagesReady : true,
    autoplay : 3000,
    autoplayDisableOnInteraction : false
    //setWrapperSize :true
});
//初始化轮播---end

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
        time: {}
    },
    created: function() {
        var that = this;

        //检查登录状态,登录状态加载数据的函数
        checkLogin(function(userid) {
            that.userId = userid;//获取用户id

            //加载商品数据
            $.post('http://onebuy.ping-qu.com/Api/Goods/purchaseOneDetail',
                {
                    goods_id: getParam('goods_id'),
                    user_id: that.userId
                }
            ).done(function (res) {
                that.goods = res.data.goodinfo;//获取商品数据
                that.companyId = that.goods.company_id;

                //插入轮播图
                for(key in res.data.goodinfo.img){
                    swiper.appendSlide("<div class='swiper-slide'><img src='" + res.data.goodinfo.img[key] + "' width='100%' ></div>");
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
                            that.goodsStatus = 'end';
                        }
                        var min = Math.floor(millisecond/60000);
                        var sec = Math.floor((millisecond%60000)/1000);
                        that.time = {'min': min, 'sec': sec, 'msec': --msec};
                        if (msec == 0) {
                            msec = 100;
                        }
                    },0.1);
                }

                //微信jssdk API调用（设置分享内容）
                wx.ready(function() {
                    //分享到朋友圈内容自定义
                    wx.onMenuShareTimeline({
                        title: '一团云购', // 分享标题
                        link: 'http://onebuy.ping-qu.com/pay/goods-detail.html?goods_id=' + goodsDetailApp.goodsId
                            + '&company_id=' + that.companyId + '&', // 分享链接
                        imgUrl: 'http://onebuy.ping-qu.com/'
                            + (goodsDetailApp.goods.img ? goodsDetailApp.goods.img[0] : goodsDetailApp.goods.goods_thumb)
                            // 分享图标
                    });

                    //分享给朋友内容自定义
                    wx.onMenuShareAppMessage({
                        title: '一团云购', // 分享标题
                        desc: goodsDetailApp.goods.goods_name, // 分享描述
                        link: 'http://onebuy.ping-qu.com/pay/goods-detail.html?goods_id=' + goodsDetailApp.goodsId
                            + '&company_id=' + that.companyId + '&', // 分享链接
                        imgUrl: 'http://onebuy.ping-qu.com/'
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
        modalOpen: function() {
            event.preventDefault();
            if ($(event.target).hasClass('buy-modal-btn')) {
                $('.goods-popup').addClass('is-visible');
            } else if ($(event.target).hasClass('code-modal-btn')) {
                $('.code-modal').addClass('is-visible');
            }

        },
        modalClose: function() {
            if ($(event.target).hasClass('goods-popup-close') || $(event.target).hasClass('goods-popup')) {
                event.preventDefault();
                $('.goods-popup').removeClass('is-visible');
            } else if ($(event.target).hasClass('code-modal-close') || $(event.target).hasClass('code-popup')) {
                event.preventDefault();
                $('.code-modal').removeClass('is-visible');
            }
        }
    }
});
//初始化轮播---end

//立即购买按钮点击事件
document.querySelector('#chooseWXPay').onclick = function() {
    //请求支付数据
    $.post('http://onebuy.ping-qu.com/Api/Wechatpay/purchase',
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
                success: function (payRes) {
                    // 支付成功后的回调函数
                    window.location = '../pay-result.html?goods_id=' + goodsDetailApp.goodsId
                        + '&num=' + goodsDetailApp.num
                        + '&company_id=' + goodsDetailApp.goods.company_id;
                }
            });
            //调用微信支付API---end

        } else if(payInfo.errcode == '90001') {
            alert("超过限购数，无法购买！");
        } else if (payInfo.errcode == '90003') {
            alert("商品剩余数量不足！");
        } else {
            alert("购买失败，错误码：" + payInfo.errcode);
        }

    }).fail(function() {
        alert("获取支付数据失败");
    });
    //请求支付数据---end
};
//立即购买按钮点击事件---end