/**
 * Created by xu on 2016/1/18.
 */

var goodsListApp = new Vue({
    el: '#goodsListApp',
    data: {
        goods: [],
        filterData: {
            company_id: '',
            thematic_id: ''
        },
        companyList: [],
        thematic: [],
        psize: 15,
        pageNum: '',
        page: [],
        currentPage: 1,
        searchType: 'no',
        searchText: '',
        goodsHref: ''
    },
    created: function() {
        var that = this;
        if (location.href.indexOf('?') == -1) {//url无查询参数
            //获取专区数据
            $.post('/Admin/Company/companyOneList').done(function(res) {
                that.companyList = res.data.company;
                that.companyList.unshift({id: '', company_name: "全部"});
                that.filterData.company_id = that.companyList[0].id;
                that.thematic = [{id: '', thematic_name: '无'}];
                that.filterData.thematic_id = that.thematic[0].id;

                that.searchType = 'no';
                that.search(1);
            }).fail(function() {
                alert("专区数据获取失败");
            });
        } else {//有查询参数时
            that.filterData.company_id = getParam('company_id');
            that.filterData.thematic_id = getParam('thematic_id');
            that.currentPage = getParam('pn');
            that.searchType = getParam('searchType');
            that.searchText = decodeURI(getParam('searchText'));

            //获取专区数据
            $.post('/Admin/Company/companyOneList').done(function(res) {
                that.companyList = res.data.company;
                that.companyList.unshift({id: '', company_name: "全部"});

                //获取专题名称列表
                $.post('/Admin/Thematic/thematicSelect',
                    {
                        company_id: that.filterData.company_id
                    }
                ).done(function(res2) {
                    that.thematic = res2.data.thematicSelect;
                    if (!that.thematic) {
                        that.thematic = [{id: '', thematic_name: '无'}];
                    } else {
                        that.thematic.unshift({id: '', thematic_name: '全部'});
                    }

                    that.search(that.currentPage);//获取商品数据

                }).fail(function() {
                    alert("请求失败");
                });
            }).fail(function() {
                alert("专区数据获取失败");
            });
            that.search();//获取商品数据
        }

    },
    methods: {
        thematicChange: function () {//当专区改变，重新获取专题选项
            var that = this;
            $.post('/Admin/Thematic/thematicSelect',
                {
                    company_id: that.filterData.company_id
                }
            ).done(function (res) {
                that.thematic = res.data.thematicSelect;
                if (!that.thematic) {
                    that.thematic = [{id: '', thematic_name: '无'}];
                } else {
                    that.thematic.unshift({id: '', thematic_name: '全部'});
                }
                that.filterData.thematic_id = that.thematic[0].id;
            }).fail(function () {
                alert("请求失败");
            });
        },
        search: function (page) {
            var that = this;

            page = page > 0 ? page : 1;
            that.currentPage = page;

            switch (this.searchType) {
                case 'no':
                {
                    var postData = {
                        thematic_id: that.filterData.thematic_id,
                        company_id: that.filterData.company_id,
                        psize: that.psize,
                        pn: page
                    };
                    if (postData.thematic_id == '') {
                        delete postData.thematic_id;
                    }
                    if (postData.company_id == '') {
                        delete postData.company_id;
                    }
                    //获取商品数据
                    $.post('/Admin/Goods/goodsList', postData).done(function (res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function () {
                        alert("获取商品数据失败");
                    });
                }
                    break;
                case 'goods_name':
                {
                    $.post('/Admin/Goods/goodsListName',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            goods_name: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function (res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function () {
                        alert("请求失败");
                    });
                }
                    break;
                case 'goods_sn':
                {
                    $.post('/Admin/Goods/goodsListSn',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            goods_sn: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function (res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function () {
                        alert("请求失败");
                    });
                }
                    break;
                case 'is_show':
                {
                    $.post('/Admin/Goods/goodsListShow',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            is_show: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function (res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function () {
                        alert("请求失败");
                    });
                }
                    break;
                case 'price':
                {
                    $.post('/Admin/Goods/goodsListPrice',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            price: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function (res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function () {
                        alert("请求失败");
                    });
                }
                    break;
                case 'nature':
                {
                    $.post('/Admin/Goods/goodsListNature',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            nature: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function (res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function () {
                        alert("请求失败");
                    });
                }
                    break;
                default:
                    alert("无搜索字段");
            }
            //为分页器页码设置激活状态
            $('.am-pagination li').removeClass('am-active');
            $('.am-pagination li').eq(page - 1).addClass('am-active');
        },
        detailHref: function (goodsId) {
            var param = 'company_id=' + this.filterData.company_id
                + '&thematic_id=' + this.filterData.thematic_id
                + '&searchType=' + this.searchType
                + '&searchText=' + this.searchText
                + '&pn=' + this.currentPage;
            replaceState(param, "商品管理");
            window.location = 'add-goods.html?goods_id=' + goodsId +'&company_id=' + this.filterData.company_id;
        },
        albumHref: function (goodsId) {
            var param = 'company_id=' + this.filterData.company_id
                + '&thematic_id=' + this.filterData.thematic_id
                + '&searchType=' + this.searchType
                + '&searchText=' + this.searchText
                + '&pn=' + this.currentPage;
            replaceState(param, "商品管理");
            window.location = 'add-album.html?goods_id=' + goodsId;
        },
        showGoodsHref: function(goodsId) {
            var url = window.location.href;
            var domain = url.substr(0, url.indexOf('admin'));
            this.goodsHref = domain + 'pay/goods-detail.html?goods_id=' + goodsId;
            $('#goods-href').modal('open');
        }
    }

});
