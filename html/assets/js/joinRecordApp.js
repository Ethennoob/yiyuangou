/**
 * Created by xu on 2015/12/29.
 */

var joinRecordApp = new Vue({
    el: '#joinRecordApp',
    data: {
        companyId: getParam('company_id'),
        recordList: {},
        goodsId: getParam('goods_id'),
        CodePopupVisible: false,
        buyCodeData: {},
        joinScroller: {
            scroller: {},
            liHeight: 65,//获得记录中单条记录的高度
            loadTip: "上拉加载数据...",
            currentPn: 1,//当前页码
            pn: 1,//要加载的页码
        },
        psize: 5
    },
    created: function() {
        var that = this;
        $.post('http://onebuy.ping-qu.com/Api/Record/buyGoodsRecordList',
            {
                goods_id: that.goodsId,
                pn: that.joinScroller.pn++,
                psize: that.psize,
            }
        ).done(function (joinData) {
            that.recordList = joinData.data.recordpage;


            //如果显示的记录数量达到一页则初始化下拉加载插件
            if(that.recordList.length == that.psize){

                //初始化iScroll插件
                $('#joinScroller ul').css('height', (that.joinScroller.liHeight * that.psize) + 'px');
                that.joinScroller.scroller = new IScroll('#joinWrapper', {probeType: 2, click: true, scrollbars: true});

                //滚动监听
                that.joinScroller.scroller.on('scroll', function() {

                    //滚到底部&&该页没被加载过->加载数据
                    if(-(this.y - parseInt($('#joinWrapper').css('height'))) >= this.scrollerHeight
                        && that.joinScroller.pn == that.joinScroller.currentPn + 1) {

                        $.post('http://onebuy.ping-qu.com/Api/Record/buyGoodsRecordList',
                            {
                                goods_id: that.goodsId,
                                pn: that.joinScroller.pn++,
                                psize: that.psize
                            }
                        ).done(function (joinData2) {

                            if (joinData2.data.recordpage) {//返回数据不为空时进行处理
                                for (var i = 0; i < joinData2.data.recordpage.length; i++) {
                                    //插入商品
                                    that.recordList.push(joinData2.data.recordpage[i]);
                                }

                                //更新scroller内容高度
                                var scrollHeight =  parseInt($('#joinScroller ul').css('height')) + (joinData2.data.recordpage.length * that.joinScroller.liHeight);
                                $('#joinScroller ul').css('height', scrollHeight + 'px');

                                //更新scroller
                                setTimeout(function() {
                                    that.joinScroller.scroller.refresh();
                                    that.joinScroller.currentPn++;//更新当前页数
                                },200);
                            } else {
                                that.joinScroller.loadTip = "没有数据啦...";
                                return ;//返回数据为空时退出滚动监听
                            }

                        }).fail(function(){
                            alert("请求失败");
                        });
                    }
                });
            }
        }).fail(function () {
            alert("请求失败");
        });
    },
    methods: {
        buyCodePopup: function (index) {
            this.buyCodeData = this.recordList[index];
            this.CodePopupVisible = true;
        },
        buyCodePopupClose: function () {
            this.CodePopupVisible = false;
        }
    }
});