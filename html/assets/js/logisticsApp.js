/**
 * Created by xu on 2015/12/29.
 */
//Vue实例化
var logisticsApp = new Vue({
    el: '#logisticsApp',
    data: {
        companyId: getParam('company_id'),
        logistics: {},
        logisticNum: null,
        logisticsState: '',
        goods: {},
        sharePopupVisible: false,
        userId: '',
        goodsId: ''
    },
    created: function() {
        var that = this;
        //检测登录状态
        checkLogin(function(userid) {
            that.userId = userid;
            //获取物流信息
            if(getParam('logistics_number')){
                that.logisticNum = getParam('logistics_number');
                if (that.logisticNum == "null") {
                    that.logistics = null;
                    that.logisticNum = null;
                } else {
                    $.post('/Api/User/getExpress',
                        {
                            logistics_number: that.logisticNum
                        }
                    ).done(function (res) {
                        that.logistics = res.data.expressdetail.lastResult.data;
                        that.logisticsState = res.data.expressdetail.lastResult.state;
                    }).fail(function () {
                        alert("请求失败");
                    });
                }
            } else {
                that.logistics = null;
            }
            //获取商品信息
            $.post('/Api/Record/luckyOneDetail',
                {
                    goods_id: getParam('goods_id')
                }
            ).done(function (res) {
                that.goods = res.data.detail[0];
                that.goodsId = res.data.detail[0].goods_id;
            }).fail(function () {
                alert("请求失败");
            });
        });

    },
    methods: {
        isEnd: function (index) {
            return index == 0 && this.logisticsState == 3;
        },
        sharePopup: function () {
            this.sharePopupVisible = true;
        },
        sharePopupClose: function () {
            this.sharePopupVisible = false;
        }

    }
});
//Vue实例化---end