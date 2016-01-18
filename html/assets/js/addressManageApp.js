/**
 * Created by xu on 2015/12/29.
 */
var addressManageApp = new Vue({
    el: '#addressManageApp',
    data: {
        companyId: getParam('company_id'),
        userId: '',
        address: {},
        showCheckBox: false,//是否显示删除选择框
        deleteId: [],//存储删除地址选项
        isAllSelect: false,//是否全选
        oneSelectId: null//选择收货地址选项框是否选中
    },
    created: function() {
        var that = this;
        //检查是否是登录状态
        checkLogin(function(userid) {
            that.userId = userid;
            $.post('http://onebuy.ping-qu.com/Api/Address/addressList',
                {
                    user_id: that.userId
                }
            ).done(function (addressRes) {
                that.address = addressRes.data.addresslist;
            }).fail(function () {
                alert("请求失败");
            });
        });
    },
    methods: {
        deleteBtn: function () {
            if(!this.showCheckBox && this.address.length > 0){
                this.showCheckBox = true;
            } else {
                this.showCheckBox = false;
            }
        },
        select: function (index) {

            if(this.deleteId[index]){
                //取消选中，将deleteId中相应的元素值设为false
                this.deleteId.$set(index, false);
            } else {
                //选中,将地址id放入deleteId数组
                this.deleteId.$set(index, this.address[index].id);
            }
        },
        allSelect: function () {
            if(!this.isAllSelect){
                //全选
                for(var i = 0; i < this.address.length; i++){
                    this.deleteId.$set(i, this.address[i].id);
                }
                this.isAllSelect = true;
            } else {
                //全不选
                this.deleteId = [];
                this.isAllSelect = false;
            }
        },
        delete: function () {
            var that = this;
            var deleteData = [];//储存要删除的地址的ID
            var len = 0;

            //将deleteID中的地址ID筛选出来
            for(var i = 0; i < that.deleteId.length; i++){
                if(that.deleteId[i]){
                    deleteData[len++] = that.deleteId[i];
                }
            }
            $.post('http://onebuy.ping-qu.com/Api/Address/addressDeleteconfirm',
                {
                    address_id: deleteData
                }
            ).done(function (res) {
                if(res.errcode == '0'){

                    //更新实例中的地址数据
                    for(var i = 0; i < that.deleteId.length; i++){
                        if(that.deleteId[i]){
                            that.address.splice(i, 1);
                            that.deleteId.splice(i, 1);
                        }
                    }

                }
            }).fail(function () {
                alert("请求失败");
            });
        },
        oneSelect: function(event, index) {//选择收货地址
            $('.round-checkBox').removeClass('checked');
            $(event.target).addClass('checked');
            this.oneSelectId = this.address[index].id;//存入选中的地址的id
        },
        selectAddress: function() {
            var that = this;
            $.post('http://onebuy.ping-qu.com/Api/Address/chooseAddress',
                {
                    address_id: that.oneSelectId,
                    bill_id: getParam('bill_id')
                }
            ).done(function() {
                history.back();
            }).fail(function() {
                alert("保存地址失败");
            });
        }
    }
});