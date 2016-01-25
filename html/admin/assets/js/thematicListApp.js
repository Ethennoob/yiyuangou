/**
 * Created by xu on 2016/1/19.
 */
var thematicListApp = new Vue({
    el: '#thematicListApp',
    data: {
        companyId: '',
        companyList: [],
        thematicInfo: [],
        goodsModalData: {},
        searchType: '',//记录搜索的字段
        searchText: '',//搜索的内容
        psize: 15,
        pageNum: '',
        page: [],
        currentPage: 1
    },
    created: function() {
        var that = this;

        //获取专区数据
        $.post('/Admin/Company/companyOneList').done(function(res) {
            that.companyList = res.data.company;
            that.companyList.unshift({id: '', company_name: "全部"});
            that.companyId = that.companyList[0].id;

            if (location.href.indexOf('?') != -1) {//如果是修改完专题跳到这个页面要获取相应参数
                that.companyId = getParam('company_id');
                that.searchText = decodeURI(getParam('searchText'));
                that.searchType = getParam('searchType');
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

            switch (this.searchType) {
                case 'no': {
                    var postData = {
                        company_id: that.companyId,
                        psize: that.psize,
                        pn: page
                    };
                    if (postData.company_id == '') {
                        delete postData.company_id;
                    }
                    //加载专题列表
                    $.post('/Admin/Thematic/thematicList', postData).done(function(response) {
                        that.thematicInfo = response.data.thematicpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(response.data.pageInfo.dataSize / response.data.pageInfo.psize);
                        that.page = [];
                        //初始化分页器数据
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("专题列表请求失败");
                    });
                }
                    break;
                case 'thematic_name': {
                    $.post('/Admin/Thematic/thematicListName',
                        {
                            thematic_name: that.searchText,
                            company_id: that.companyId,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.thematicInfo = res.data.thematicpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        that.page = [];
                        //初始化分页器数据
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("专题列表请求失败");
                    });
                }
                    break;
                case 'status': {
                    $.post('/Admin/Thematic/thematicListStatus',
                        {
                            status: that.searchText,
                            company_id: that.companyId,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.thematicInfo = res.data.thematicpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        that.page = [];
                        //初始化分页器数据
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("专题列表请求失败");
                    });
                }
                    break;
                case 'add_time': {
                    $.post('/Admin/Thematic/thematicListTime',
                        {
                            add_time: that.searchText,
                            company_id: that.companyId,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.thematicInfo = res.data.thematicpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        that.page = [];
                        //初始化分页器数据
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("专题列表请求失败");
                    });
                }
                    break;
                default: alert("缺少搜索字段");
            }

            //为分页器页码设置激活状态
            $('.am-pagination li').removeClass('am-active');
            $('.am-pagination li').eq(page-1).addClass('am-active');
        },
        detailHref: function(thematicId) {

            var param = 'company_id=' + this.companyId
                + '&searchType=' + this.searchType
                + '&searchText=' + this.searchText
                + '&pn=' + this.currentPage;
            replaceState(param, "专题管理");
            window.location = 'thematic-detail.html?thematic_id=' + thematicId;
        },
        showGoods: function(index) {
            this.goodsModalData = this.thematicInfo[index];
            $('#goods-modal').modal({
                height: 400
            });
            $('#goods-modal').modal('open');
        }
    }
});