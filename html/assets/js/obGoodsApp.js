/**
 * Created by xu on 2015/12/29.
 */

//Vue实例化
var obtainedGoodsApp = new Vue({
    el: '#obtainedGoodsApp',
    data: {
        companyId: getParam('company_id'),
        obtainedGoods: {},
        userId: '',
        psize: 5,
        obScroller: {
            scroller: {},
            liHeight: 128,//获得记录中单条记录的高度
            loadTip: "上拉加载数据...",
            currentPn: 1,//当前页码
            pn: 1//要加载的页码
        }
    },
    created: function() {
        var that = this;
        checkLogin(function(userid) {
            that.userId = userid;
            $.post('http://onebuy.ping-qu.com/Api/User/luckyList',
                {
                    user_id: that.userId,
                    pn: that.obScroller.pn++,
                    psize: that.psize
                }
            ).done(function (luckyData) {
                obtainedGoodsApp.obtainedGoods = luckyData.data.obtained_goods;

                //初始化iScroll插件
                if(that.obtainedGoods.length == that.psize){//记录数量达到一页,上拉加载初始化
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
    }
});
//Vue实例化---end
