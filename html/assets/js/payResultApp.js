/**
 * Created by xu on 2015/12/29.
 */

var payResultApp = new Vue({
    el: '#payResultApp',
    data: {
        payData: {},
        payNum: getParam('num'),
        userId: '',
        goodsId: getParam('goods_id'),
        companyId: getParam('company_id'),
        sharePopupVisible: false
    },
    created: function() {
        var that = this;

        checkLogin(function(userid) {

            that.userId = userid;

            $.post('/Api/Wechatpay/record',
                {
                    user_id: that.userId,
                    goods_id: that.goodsId
                }
            ).done(function(res) {
                that.payData = res.data.record;

                if(that.payData.last_num == 0){//当商品剩余数量为0
                    //时间为晚上02:00到早上10:00
                    var myDate = new Date();
                    var h = myDate.getHours();
                    if (h >= 2 && h <= 10) {
                        $.post('/Admin/Roll/nightRoll',
                            {
                                goods_id: that.goodsId
                            }
                        );
                    } else {
                        //时间为早上10:00到晚上02:00
                        $.post('/Admin/Roll/saveRollGoodID',
                            {
                                goods_id: that.goodsId
                            }
                        );
                    }
                }
            }).fail(function() {
                alert("请求失败");
            });

        });

    },
    methods: {
        sharePopup: function () {
            this.sharePopupVisible = true;
        },
        sharePopupClose: function () {
            this.sharePopupVisible = false;
        }
    }
});