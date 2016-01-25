/**
 * Created by xu on 2015/12/29.
 */
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

        firstGoods: {},//记录最早一期商品
        newGoods: {},//记录最新一期商品
        countDownTime: [],//多个商品的倒计时时间记录
        goodsStatus: [],//商品状态记录
        companyId: getParam('company_id'),//专区id
        companyName: ''
    },
    created: function() {
        var that = this;
        var url = window.location.href;
        //检查是否是登录状态,登录状态加载数据的函数
        checkLogin(function(userid) {
            //老用户直接加载数据
            that.userId = userid;
            $.post('/Api/Goods/index',
                {
                    company_id: getParam('company_id')
                }
            ).done(function(goodsData) {
                //插入轮播图
                if (goodsData.data.poster) {//是否存在轮播图
                    for(var i = 0; i < goodsData.data.poster.length; i++){
                        $('.swiper-wrapper').append("<a href='" + goodsData.data.poster[i].adv_url + "' class='swiper-slide block'><img src='" + goodsData.data.poster[i].adv_img + "' style='width: 100%;' ></a>");
                    }
                    //初始化轮播
                    var swiper = new Swiper('.index-swiper', {
                        autoplay: 5000,//可选选项，自动滑动
                        pagination: '.swiper-pagination',
                        paginationClickable: true,
                        loop: true
                    });
                    //初始化轮播---end
                }

                //计算商品状态
                if (goodsData.data.index) {
                    that.goodsStatus = goodsStatus(goodsData.data.index);
                }
                that.ingThematic = goodsData.data.thematic.id;//当前专题id
                that.newestThematic = goodsData.data.newthematic;//最新一期专题id

                //判断是否切换默认专题
                if (!that.changeDefaultThematic(that.goodsStatus, that.ingThematic, that.newestThematic)) {
                    that.goods = goodsData.data.index;

                    that.thematic_name = goodsData.data.thematic.thematic_name;
                    that.currentThematic = goodsData.data.thematic.id;//当前专题id
                    that.firstThematic = goodsData.data.thematic.id;

                    that.poster = goodsData.data.poster;
                    that.companyName = goodsData.data.company_name;
                    that.firstGoods = that.goods;
                    that.newGoods = that.goods;
                }

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
                //用户切换专题时
                if (that.currentThematic != that.ingThematic) {
                    clearInterval(time);
                }
                var myDate = new Date();
                var millisecond = that.goods[index].lucky_time * 1000 - myDate.getTime();
                if (millisecond <= 0) {//倒计时结束
                    //重新加载商品获奖者数据
                    $.post('/Api/Goods/index',
                        {
                            company_id: getParam('company_id')
                        }
                    ).done(function(goodsData) {
                        //计算商品状态
                        that.goodsStatus = goodsStatus(goodsData.data.index);
                        that.ingThematic = goodsData.data.thematic.id;//正在进行的专题id
                        that.newestThematic = goodsData.data.newthematic;//最新一期专题id

                        //判断是否切换默认专题
                        if (!that.changeDefaultThematic(that.goodsStatus, that.ingThematic, that.newestThematic)) {
                            that.goods = goodsData.data.index;

                            that.thematic_name = goodsData.data.thematic.thematic_name;
                            that.currentThematic = goodsData.data.thematic.id;
                            that.firstThematic = goodsData.data.thematic.id;

                            that.poster = goodsData.data.poster;
                            that.companyName = goodsData.data.company_name;
                            that.firstGoods = that.goods;
                            that.newGoods = that.goods;
                        }
                        clearInterval(time);
                    }).fail(function() {
                        alert("商品数据请求失败");
                    });

                } else {
                    var temp = Math.floor(millisecond/60000) + '分' + Math.floor((millisecond%60000)/1000) + '秒';
                    that.countDownTime.$set(index, {time: temp});
                }
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
            $.post('/Api/Goods/index',
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
                        if (that.goods) {
                            that.goodsStatus = goodsStatus(that.goods);
                        }
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
            if(that.currentThematic < 1){
                that.goods = that.firstGoods;//设置商品数据为最早一期的数据
                that.currentThematic = that.firstThematic;//当前专题id为最老一期id
                alert("已经是第一期！");//提示用户没有上一期
                return;
            }

            //加载上一期商品列表
            $.post('/Api/Goods/index',
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
                        if (that.goods) {
                            that.goodsStatus = goodsStatus(that.goods);
                        }
                    }
                }

            }).fail(function() {
                alert("请求数据失败");
            });
        },
        changeDefaultThematic: function(status, ingThematic, newestThematic) {//切换默认专题
            var that = this;
            //判断是否有下一期
            if (ingThematic == newestThematic) {
                return false;
            }
            //判断该专题是否结束
            for (var i = 0; i < status.length; i++) {
                if (status[i] != 'end') {
                    return false;
                }
            }

            //如果有下一期且该专题已结束
            if (i == status.length) {
                $.post('Admin/Thematic/ChangeThematic',
                    {
                        thematic_id: ingThematic
                    }
                ).done(function(res) {
                    if (res.errcode == 0) {

                        //专题切换成功后重新载入首页数据
                        $.post('/Api/Goods/index',
                            {
                                company_id: getParam('company_id')
                            }
                        ).done(function(goodsData) {

                            //计算商品状态
                            if (goodsData.data.index) {
                                that.goodsStatus = goodsStatus(goodsData.data.index);
                            }
                            that.goods = goodsData.data.index;

                            that.thematic_name = goodsData.data.thematic.thematic_name;
                            that.ingThematic = goodsData.data.thematic.id;//正在进行的专题id
                            that.currentThematic = goodsData.data.thematic.id;//当前专题id
                            that.newestThematic = goodsData.data.newthematic;//最新一期专题id
                            that.firstThematic = goodsData.data.thematic.id;

                            that.companyName = goodsData.data.company_name;
                            that.firstGoods = that.goods;
                            that.newGoods = that.goods;

                        }).fail(function() {
                            alert("商品数据请求失败");
                        });

                    } else {
                        alert("专题切换失败");
                    }
                }).fail(function() {
                    alert("专题切换请求失败");
                });
            }
            return true;
        }
    }
});
