/**
 * Created by xu on 2015/12/29.
 */
var personalDetailApp = new Vue({
    el: '#personalDetailApp',
    data: {
        companyId: getParam('company_id'),
        tabActive: 1,
        userImg: '',
        userPhone: '',
        userId: '',
        buyRecord: [],
        obtainedGoods: [],
        countDownTime: [],//多个商品的倒计时时间记录
        goodsStatus: [],//商品状态记录
        buyScroller: {
            scroller: {},
            liHeight: 169,//购买记录中单条记录的高度
            loadTip: "上拉加载数据...",
            currentPn: 1,//当前页码
            pn: 1//要加载的页码
        },
        obScroller: {
            scroller: {},
            liHeight: 128,//获得记录中单条记录的高度
            loadTip: "上拉加载数据...",
            currentPn: 1,//当前页码
            pn: 1//要加载的页码
        },
        psize: 5
    },
    created: function() {
        var that = this;
        checkLogin(function(userid) {
            that.userId = userid;
            //获取用户头像与手机号
            $.post('http://onebuy.ping-qu.com/Api/User/userOneAllDetail',
                {
                    user_id: that.userId
                }
            ).done(function (userData) {
                that.userImg = userData.data.user.user_img;
                that.userPhone = userData.data.user.phone;
            }).fail(function () {
                alert("请求失败");
            });

            //购买记录
            $.post('http://onebuy.ping-qu.com/Api/User/purchaseList',
                {
                    user_id: that.userId,
                    pn: that.buyScroller.pn++,
                    psize: that.psize
                }
            ).done(function (buyData) {
                that.buyRecord = buyData.data.detailpage;

                //计算每个商品的状态
                that.goodsStatus = goodsStatus(that.buyRecord);

                //初始化iScroll插件
                if(that.buyRecord.length == that.psize){//记录数量达到一页,初始化下拉加载插件

                    $('#buyScroller ul').css('height', (that.buyScroller.liHeight * that.psize) + 10 + 'px');
                    that.buyScroller.scroller = new IScroll('#buyWrapper', {probeType: 2, click: true});

                    //滚动监听
                    that.buyScroller.scroller.on('scroll', function() {
                        //滚到底部&&该页没被加载过时加载数据
                        if(-(this.y - parseInt($('#buyWrapper').css('height'))) >= this.scrollerHeight
                            && that.buyScroller.pn == that.buyScroller.currentPn + 1) {

                            $.post('http://onebuy.ping-qu.com/Api/User/purchaseList',
                                {
                                    user_id: that.userId,
                                    pn: that.buyScroller.pn++,
                                    psize: that.psize
                                }
                            ).done(function (purchaseData2) {

                                if (purchaseData2.data.detailpage) {//返回数据不为空时进行处理
                                    for (var i = 0; i < purchaseData2.data.detailpage.length; i++) {
                                        //插入商品
                                        that.buyRecord.push(purchaseData2.data.detailpage[i]);
                                        //插入商品状态
                                        that.goodsStatus[that.goodsStatus.length] = goodsStatus(purchaseData2.data.detailpage[i]);
                                    }

                                    //更新scroller内容高度
                                    var scrollHeight =  parseInt($('#buyScroller ul').css('height')) + purchaseData2.data.detailpage.length * that.buyScroller.liHeight;
                                    $('#buyScroller ul').css('height', scrollHeight + 'px');

                                    //更新scroller
                                    setTimeout(function() {
                                        that.buyScroller.scroller.refresh();
                                        that.buyScroller.currentPn++;//更新当前页数
                                    },200);
                                } else {
                                    that.buyScroller.loadTip = "没有数据啦...";
                                    return ;//返回数据为空时退出滚动监听
                                }

                            }).fail(function(){
                                alert("请求失败");
                            });
                        }
                    });
                } else {//记录数量未达一页，仅初始化iScroll
                    $('#buyScroller ul').css('height', (that.buyScroller.liHeight * that.buyRecord.length) + 10 + 'px');
                    that.buyScroller.scroller = new IScroll('#buyWrapper', {probeType: 2, click: true});
                }
            }).fail(function () {
                alert("请求失败");
            });

            //获得商品数据
            $.post('http://onebuy.ping-qu.com/Api/User/luckyList',
                {
                    user_id: that.userId,
                    pn: that.obScroller.pn++,
                    psize: that.psize
                }
            ).done(function (luckyData) {
                that.obtainedGoods = luckyData.data.obtained_goods;

                //初始化iScroll插件
                if(that.obtainedGoods.length == that.psize){//记录数量达到一页,初始化下拉加载

                    $('#obScroller ul').css('height', (that.obScroller.liHeight * that.psize) + 10 + 'px');
                    that.obScroller.scroller = new IScroll('#obWrapper', {probeType: 2, click: true, scrollbars: true});

                    //滚动监听
                    that.obScroller.scroller.on('scroll', function() {

                        //滚到底部&&该页没被加载过->加载数据
                        if(-(this.y - parseInt($('#obWrapper').css('height'))) >= this.scrollerHeight
                            && that.obScroller.pn == that.obScroller.currentPn + 1) {

                            $.post('http://onebuy.ping-qu.com/Api/User/luckyList',
                                {
                                    user_id: that.userId,
                                    pn: that.obScroller.pn++,
                                    psize: that.psize
                                }
                            ).done(function (luckyData2) {

                                if (luckyData2.data.obtained_goods) {//返回数据不为空时进行处理
                                    for (var i = 0; i < luckyData2.data.obtained_goods.length; i++) {
                                        //插入商品
                                        that.obtainedGoods.push(luckyData2.data.obtained_goods[i]);
                                    }

                                    //更新scroller内容高度
                                    var scrollHeight =  parseInt($('#obScroller ul').css('height')) + luckyData2.data.obtained_goods.length * that.obScroller.liHeight;
                                    $('#obScroller ul').css('height', scrollHeight + 'px');

                                    //更新scroller
                                    setTimeout(function() {
                                        that.obScroller.scroller.refresh();
                                        that.obScroller.currentPn++;//更新当前页数
                                    },200);
                                } else {
                                    that.obScroller.loadTip = "没有数据啦...";
                                    return ;//返回数据为空时退出滚动监听
                                }

                            }).fail(function(){
                                alert("请求失败");
                            });
                        }
                    });
                } else {//记录数量未达一页，仅初始化iScroll
                    $('#obScroller ul').css('height', (that.obScroller.liHeight * that.obtainedGoods.length) + 10 + 'px');
                    that.obScroller.scroller = new IScroll('#obWrapper', {probeType: 2, click: true, scrollbars: true});
                }
            }).fail(function () {
                alert("请求失败");
            });
        });
    },
    methods: {
        progressWidth: function(index) {
            var buyProgress = Math.floor((this.buyRecord[index].purchase_num / this.buyRecord[index].total_num) * 100);
            return buyProgress.toString() + '%';
        },
        countDown:function(index) {//正在揭晓商品倒计时
            var that = this;
            var time = setInterval(function() {
                var myDate = new Date();
                var millisecond = that.buyRecord[index].lucky_time * 1000 - myDate.getTime();
                if(millisecond <= 0){//倒计时结束
                    that.goodsStatus.$set(index, 'end');
                    clearInterval(time);
                }
                var temp = Math.floor(millisecond/60000) + '分' + Math.floor((millisecond%60000)/1000) + '秒';
                that.countDownTime.$set(index, {time: temp});
            },1000);

        }
    }
});
