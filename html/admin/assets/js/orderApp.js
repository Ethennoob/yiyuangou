/**
 * Created by xu on 2016/1/19.
 */
var orderApp = new Vue({
    el: '#orderApp',
    data: {
        order: [],
        companyList: [],
        companyId: '',
        deleteId: '',
        psize: 15,
        pageNum: '',
        page: [],
        currentPage: 1,
        searchType: 'no',
        searchText: ''
    },
    created: function() {
        var that = this;

        //获取专区数据
        $.post('/Admin/Company/companyOneList').done(function(res) {
            that.companyList = res.data.company;
            that.companyList.unshift({id: '', company_name: '全部'});

        }).fail(function() {
            alert("专区数据请求失败");
        });

        //判断url是否有查询参数
        if (location.href.indexOf('?') != -1) {
            this.companyId = getParam('company_id');
            this.searchText = decodeURI(getParam('searchText'));
            this.searchType = getParam('searchType');
            this.search(getParam('pn'));//搜索数据

        } else {
            this.companyId = '';
            this.searchType = 'no';
            that.search(1);
        }


    },
    methods: {
        search: function(page) {
            page = page > 0 ? page : 1;
            this.currentPage = page;

            var that = this;

            switch (this.searchType) {
                case 'no':{
                    var postUrl = '/Admin/Bill/billList';
                    var postData = {
                        company_id: that.companyId,
                        psize: that.psize,
                        pn: page
                    };
                    if (!postData.company_id) {
                        delete postData.company_id;
                    }
                }
                    break;
                case 'bill_sn':{
                    var postUrl = '/Admin/Bill/billListSn';
                    var postData = {
                        company_id: that.companyId,
                        bill_sn: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                }
                    break;
                case 'nickname':{
                    var postUrl = '/Admin/Bill/billListNickname';
                    var postData = {
                        company_id: that.companyId,
                        name: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                }
                    break;
                case 'goods_name':{
                    var postUrl = '/Admin/Bill/billListGoodsName';
                    var postData = {
                        company_id: that.companyId,
                        goods_name: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                }
                    break;
                case 'thematic_name':{
                    var postUrl = '/Admin/Bill/billListThematicName';
                    var postData = {
                        company_id: that.companyId,
                        thematic_name: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                }
                    break;
                case 'price':{
                    var postUrl = '/Admin/Bill/billListThematicPrice';
                    var postData = {
                        company_id: that.companyId,
                        price: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                }
                    break;
                case 'status':{
                    var postUrl = '/Admin/Bill/billListStatus';
                    var postUrl = {
                        company_id: that.companyId,
                        status: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                }
                    break;
                default: alert("缺少搜索字段");
            }

            $.post(postUrl, postData).done(function(response) {
                that.order = response.data.bill;

                //计算数据页数
                that.pageNum = Math.ceil(response.data.pageInfo.dataSize / response.data.pageInfo.psize);
                //初始化分页器数据
                that.page = [];
                for (var i = 0; i < that.pageNum; i++) {
                    that.page.$set(i, i + 1);
                }

            }).fail(function() {
                alert("订单数据请求失败");
            });

            //为分页器页码设置激活状态
            $('.am-pagination li').removeClass('am-active');
            $('.am-pagination li').eq(page-1).addClass('am-active');

        },
        detailHref: function(billId) {
            var param = 'company_id=' + this.companyId
                + '&searchType=' + this.searchType
                + '&searchText=' + this.searchText
                + '&pn=' + this.currentPage;

            replaceState(param, "订单管理");
            window.location = 'order-detail.html?bill_id=' + billId;
        },
        deleteOrder: function(){
            var that = this;
            $.post('/Admin/Bill/billDelete',
                {
                    bill_id: that.order[that.deleteId].id
                }
            ).done(function(res) {
                console.log(that.deleteId);
                if(res.errcode == '0'){

                    if(that.deleteId == 0){
                        that.order.shift();
                    } else {
                        that.order.splice(that.deleteId, 1);
                    }
                    $('#deleteTip').modal('close');

                } else {
                    alert("删除失败");
                }
            }).fail(function() {
                alert("删除请求失败");
            });
        },
        closeModal: function() {
            $('#deleteTip').modal('close');
        },
        exportExcel: function() {
            window.location = '/Admin/Bill/outputExcel';
        }
    }
});
