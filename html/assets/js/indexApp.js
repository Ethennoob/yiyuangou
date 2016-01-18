/**
 * Created by xu on 2015/12/29.
 */

//初始化轮播
var indexSwiper = new Swiper('.index-swiper', {
    pagination: '.swiper-pagination',
    paginationClickable: true,
    observer:true,
    observeParents:true,
    autoHeight: true,
    updateOnImagesReady : true,
    autoplay : 3000,
    autoplayDisableOnInteraction : false
});
//初始化轮播---end

var indexApp = new Vue({
    el: '#index-app',
    data: {
        //userId: '',
        goods: {},

        thematic_name: '',
        ingThematic: '',//正在进行专题id
        currentThematic: '',//当前专题id
        newestThematic: '',//最新一期专题id
        firstThematic: '',//第一期专题id
        //thematicStatus: true,//专题状态（默认为true，只有专题处于未开始时才会是false）

        firstGoods: {},//记录最早一期商品
        newGoods: {},//记录最新一期商品
        poster: [],//海报图
        countDownTime: [],//多个商品的倒计时时间记录
        goodsStatus: [],//商品状态记录
        companyId: getParam('company_id'),//专区id
        companyName: '',
        newestThematic: ''//最新一期专题id
    },
    created: function() {
        var that = this;
        var url = window.location.href;
        //检查是否是登录状态,登录状态加载数据的函数
        checkLogin(function(userid) {
            //老用户直接加载数据
            that.userId = userid;
            $.post('http://onebuy.ping-qu.com/Api/Goods/index',
                {
                    company_id: getParam('company_id')
                }
            ).done(function(goodsData) {
                that.goods = goodsData.data.index;

                that.thematic_name = goodsData.data.thematic.thematic_name;
                that.ingThematic = goodsData.data.thematic.id;//正在进行的专题id
                that.currentThematic = goodsData.data.thematic.id;//当前专题id
                that.newestThematic = goodsData.data.newthematic;//最新一期专题id
                that.firstThematic = goodsData.data.thematic.id;
                //that.thematicStatus = true;

                that.poster = goodsData.data.poster;
                that.companyName = goodsData.data.company_name;
                that.firstGoods = that.goods;
                that.newGoods = that.goods;

                //插入轮播图
                for(key in that.poster){
                    indexSwiper.appendSlide("<a href='" + that.poster[key].adv_url + "' class='swiper-slide'><img src='" + that.poster[key].adv_img + "' width='100%' ></a>");
                }

                //计算商品状态
                that.goodsStatus = goodsStatus(that.goods);

            }).fail(function() {
                alert("请求数据失败");
            });
        });


    },
    methods: {
        progressWidth: function(index) {
            var buyProgress = Math.floor((this.goods[index].purchase_num / this.goods[index].total_num) * 100);
            return buyProgress.toString() + '%';
        },
        countDown:function(index) {//正在揭晓商品倒计时
            var that = this;
            var time = setInterval(function() {
                var myDate = new Date();
                var millisecond = that.goods[index].lucky_time * 1000 - myDate.getTime();
                if(millisecond <= 0){//倒计时结束
                    that.goodsStatus.$set(index, 'end');
                    clearInterval(time);
                }
                var temp = Math.floor(millisecond/60000) + '分' + Math.floor((millisecond%60000)/1000) + '秒';
                that.countDownTime.$set(index, {time: temp});
            },1000);

        },
        goodsHref: function(index) {
            var that = this;
            if (!that.goodsStatus) {
                return 'javascript: ;';
            } else {
                return 'pay/goods-detail.html?goods_id=' + that.goods[index].id
                    + '&company_id=' + that.companyId;
            }
        },
        nextPeriod: function() {
            var that = this;

            //清空商品数据
            that.goods = null;

            //判断是否为最新一期
            if(that.currentThematic == that.newestThematic){
                that.goods = that.newGoods;
                alert("已是最新一期！");
                return;
            }

            //加载下一期数据
            $.post('http://onebuy.ping-qu.com/Api/Goods/index',
                {
                    company_id: that.companyId,
                    thematic_id: ++that.currentThematic
                }
            ).done(function(goodsData) {
                if (!goodsData.data.index) {//下一个id没数据则继续查找
                    that.nextPeriod();
                } else {//查找到数据后显示数据
                    that.goods = goodsData.data.index;
                    that.newGoods = that.goods;
                    that.thematic_name = goodsData.data.thematic.thematic_name;
                    that.bgSrc = goodsData.data.thematic.poster;

                    //设置专题状态
                    if(that.currentThematic > that.ingThematic){//当前专题未开始时
                        that.goodsStatus = null;
                    } else {
                        //计算商品状态
                        that.goodsStatus = goodsStatus(that.goods);
                    }
                }

            }).fail(function() {
                alert("请求数据失败");
            });
        },
        prevPeriod: function() {
            var that = this;

            //清空商品数据
            that.goods = null;

            //判断是否到达第一期
            if(that.currentThematic == 1){
                that.goods = that.firstGoods;//设置商品数据为最早一期的数据
                that.currentThematic = that.firstThematic;//当前专题id为最老一期id
                alert("已经是第一期！");//提示用户没有上一期
                return;
            }

            //加载上一期商品列表
            $.post('http://onebuy.ping-qu.com/Api/Goods/index',
                {
                    company_id: that.companyId,
                    thematic_id: --that.currentThematic
                }
            ).done(function(goodsData) {
                if (!goodsData.data.index) {
                    that.prevPeriod();//如果没有数据继续寻找上一期
                } else {//有数据则提取数据
                    that.goods = goodsData.data.index;
                    that.firstGoods = that.goods;//一查询到商品，将当前商品存储为第一期商品
                    that.firstThematic = that.currentThematic;//一查询到商品，将当前id存储为第一期id
                    that.thematic_name = goodsData.data.thematic.thematic_name;
                    that.bgSrc = goodsData.data.thematic.poster;

                    //设置专题状态
                    if(that.currentThematic > that.ingThematic){//专题id<=当前正在进行的专题id
                        that.goodsStatus = null;
                    } else {
                        //计算商品状态
                        that.goodsStatus = goodsStatus(that.goods);
                    }
                }

            }).fail(function() {
                alert("请求数据失败");
            });
        }
    }
});
