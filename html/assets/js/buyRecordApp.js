/**
 * Created by xu on 2015/12/29.
 */
var buyRecordApp = new Vue({
    el: '#buyRecordApp',
    data: {
        companyId: getParam('company_id'),
        goods: {},
        userId: '',
        countDownTime: [],//装多个商品的倒计时时间
        goodsStatus: [],//商品状态，是否揭晓
        liHeight: 169,//单条记录的高度
        currentPn: 1,//当前页面
        pn: 1,//要加载的页码
        psize: 15,//每页显示的记录数
        loadTip: '上拉加载数据...'
    },
    created: function() {
        var that = this;

        //检测登录状态
        checkLogin(function(userid) {

            that.userId = userid;//获取userid

            //请求用户购买数据
            $.post('http://onebuy.ping-qu.com/Api/User/purchaseList',
                {
                    user_id: that.userId,
                    pn: that.pn++,
                    psize: that.psize
                }
            ).done(function (purchaseData) {

                that.goods = purchaseData.data.detailpage;//获取用户购买数据

                //计算每个商品的状态
                that.goodsStatus = goodsStatus(that.goods);

                //初始化iScroll插件
                if(that.goods.length == that.psize){//记录数量达到一页,初始化下拉加载插件
                    $('.buy-goods-list').css('height', (that.liHeight * that.psize) + 10 + 'px');
                    var myScroll = new IScroll('#wrapper', {probeType: 2, click: true, scrollbars: true});
                    //滚动监听
                    myScroll.on('scroll', function() {
                        //滚到底部&&该页没被加载过->加载数据
                        if(-(this.y - parseInt($('#wrapper').css('height'))) >= this.scrollerHeight && that.pn == that.currentPn + 1){

                            $.post('http://onebuy.ping-qu.com/Api/User/purchaseList',
                                {
                                    user_id: that.userId,
                                    pn: that.pn++,
                                    psize: that.psize
                                }
                            ).done(function (purchaseData2) {

                                if (purchaseData2.data.detailpage) {//返回数据不为空时进行处理
                                    for (var i = 0; i < purchaseData2.data.detailpage.length; i++) {
                                        //插入商品
                                        that.goods.push(purchaseData2.data.detailpage[i]);
                                        //插入商品状态
                                        that.goodsStatus[that.goodsStatus.length] = goodsStatus(purchaseData2.data.detailpage[i]);
                                    }

                                    //更新scroller内容高度
                                    var scrollHeight =  parseInt($('#scroller ul').css('height')) + purchaseData2.data.detailpage.length * that.liHeight;
                                    $('#scroller ul').css('height', scrollHeight + 'px');

                                    //更新scroller
                                    setTimeout(function() {
                                        myScroll.refresh();
                                        that.currentPn++;//更新当前页数
                                    },200);
                                } else {
                                    that.loadTip = "没有数据啦...";
                                    return ;//返回数据为空时退出滚动监听
                                }

                            }).fail(function(){
                                alert("请求失败");
                            });
                        }
                    });
                } else {//记录数量未达一页，仅初始化iScroll
                    $('.buy-goods-list').css('height', (that.liHeight * that.goods.length) + 10 + 'px');
                    var myScroll = new IScroll('#wrapper', {probeType: 2, click: true, scrollbars: true});
                }

            }).fail(function () {
                alert("请求失败");
            });
        });

    },
    methods: {
        progressWidth: function(index) {
            var buyProgress = Math.floor((this.goods[index].purchase_num / this.goods[index].total_num) * 100);
            return buyProgress.toString() + '%';
        },
        countDown: function(index) {//正在揭晓商品倒计时
            var myDate = new Date();
            var millisecond = this.goods[index].lucky_time * 1000 - myDate.getTime();
            return Math.floor(millisecond/60000) + '分' + Math.floor((millisecond%60000)/1000) + '秒';
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
        }
    }
});
