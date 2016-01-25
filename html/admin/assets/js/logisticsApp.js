/**
 * Created by xu on 2016/1/19.
 */
var logisticsListApp = new Vue({
    el: '#logisticsListApp',
    data: {
        logistics: [],
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
            that.companyId = that.companyList[0].id;

            if (location.href.indexOf('?') != -1) {//有查询参数
                that.searchType = getParam('searchType');
                that.searchText = decodeURI(getParam('searchText'));
                that.companyId = getParam('company_id');
                that.search(getParam('pn'));
            } else {
                that.searchType = 'no';
                that.search(1);
            }
        }).fail(function() {
            alert("专区数据请求失败");
        });

    },
    methods: {
        search: function(page) {
            var that = this;
            page = page > 0 ? page : 1;
            that.currentPage = page;

            switch (that.searchType) {
                case 'no': {
                    var postData = {
                        company_id: that.companyId,
                        psize: that.psize,
                        pn: page
                    };
                    if (!postData.company_id) {
                        delete postData.company_id;
                    }
                    var postUrl = '/Admin/Logistics/logisticsList';
                }
                    break;
                case 'logistics_number': {
                    //加载物流数据
                    var postData = {
                        company_id: that.companyId,
                        logistics_number: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                    var postUrl = '/Admin/Logistics/logisticsListNumber';
                }
                    break;
                case 'bill_sn': {
                    var postData = {
                        company_id: that.companyId,
                        bill_sn: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                    var postUrl = '/Admin/Logistics/logisticsListSn';
                }
                    break;
                case 'logistics_name': {
                    var postData = {
                        company_id: that.companyId,
                        logistics_name: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                    var postUrl = '/Admin/Logistics/logisticsListName';
                }
                    break;
                case 'logistics_status': {
                    var postData = {
                        company_id: that.companyId,
                        logistics_status: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                    var postUrl = '/Admin/Logistics/logisticsListName';
                }
                    break;
                case 'name': {
                    var postData = {
                        company_id: that.companyId,
                        name: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                    var postUrl = '/Admin/Logistics/logisticsListNickname';
                }
                    break;
                case 'mobile': {
                    var postData = {
                        company_id: that.companyId,
                        mobile: that.searchText,
                        psize: that.psize,
                        pn: page
                    };
                    var postUrl = '/Admin/Logistics/logisticsListMobile';
                }
                    break;
                default: alert("缺少搜索字段");
            }

            //加载物流数据
            $.post(postUrl, postData).done(function(res) {
                that.logistics = res.data.logisticslist;

                //计算数据页数
                that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                //初始化分页器数据
                that.page = [];
                for (var i = 0; i < that.pageNum; i++) {
                    that.page.$set(i, i + 1);
                }

            }).fail(function() {
                alert("物流数据请求失败");
            });

            //为分页器页码设置激活状态
            $('.am-pagination li').removeClass('am-active');
            $('.am-pagination li').eq(page-1).addClass('am-active');
        },
        detailHref: function(logisticsId) {
            var param = 'company_id=' + this.companyId
                + '&searchType=' + this.searchType
                + '&searchText=' + this.searchText
                + '&pn=' + this.currentPage;
            replaceState(param, "物流管理");

            window.location = 'logistics-detail.html?logistics_id=' + logisticsId;
        },
        deleteLogistics: function(){
            var that = this;
            $.post('/Admin/Logistics/logisticsDelete',
                {
                    logistics_id: that.logistics[that.deleteId].id
                }
            ).done(function(res) {
                if(res.errcode == '0'){
                    that.logistics.splice(that.deleteId, 1);
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
        }
    }
});