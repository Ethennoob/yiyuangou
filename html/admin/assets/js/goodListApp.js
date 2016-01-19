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
        searchType: '',
        searchText: ''
    },
    created: function() {
        var that = this;
        if (location.href.indexOf('?') == -1) {//url无查询参数
            //获取专区数据
            $.post('http://onebuy.ping-qu.com/Admin/Company/companyOneList').done(function(res) {
                that.companyList = res.data.company;
                that.companyList.unshift({id: '', company_name: "全部"});
                that.filterData.company_id = that.companyList[0].id;

                //获取专题名称列表
                $.post('http://onebuy.ping-qu.com/Admin/Thematic/thematicSelect',
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
                    that.filterData.thematic_id = that.thematic[0].id;

                    var url = location.href;
                    history.replaceState(null, "商品列表", url + '?searchType=no&psize='
                        + that.psize + '&pn=1&company_id=&thematic_id=&searchText=');

                    that.search();//获取商品数据

                }).fail(function() {
                    alert("请求失败");
                });
            }).fail(function() {
                alert("专区数据获取失败");
            });
        } else {//有查询参数时
            //获取专区数据
            $.post('http://onebuy.ping-qu.com/Admin/Company/companyOneList').done(function(res) {
                that.companyList = res.data.company;
                that.filterData.company_id = getParam('company_id');

                //获取专题名称列表
                $.post('http://onebuy.ping-qu.com/Admin/Thematic/thematicSelect',
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
                    that.filterData.thematic_id = that.thematic[0].id;

                    var url = location.href;
                    history.replaceState(null, "商品列表", url + '?searchType=no&psize='
                        + that.psize + '&pn=1');

                    that.search();//获取商品数据

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
        thematicChange:function() {//当专区改变，重新获取专题选项
            var that = this;
            $.post('http://onebuy.ping-qu.com/Admin/Thematic/thematicSelect',
                {
                    company_id: that.filterData.company_id
                }
            ).done(function(res) {
                that.thematic = res.data.thematicSelect;
                if (!that.thematic) {
                    that.thematic = [{id: '', thematic_name: '无'}];
                } else {
                    that.thematic.unshift({id: '', thematic_name: '全部'});
                }
                that.filterData.thematic_id = that.thematic[0].id;
            }).fail(function() {
                alert("请求失败");
            });
        },
        search: function(page) {
            this.searchType = this.searchType ? this.searchType : getParam('searchType');
            page = page > 0 ? page : getParam('pn');
            this.filterData.thematic_id = this.filterData.thematic_id ? this.filterData.thematic_id : getParam('thematic_id');
            this.filterData.company_id = this.filterData.company_id ? this.filterData.company_id : getParam('company_id');
            this.searchText = this.searchText ? this.searchText : decodeURI(getParam('searchText'));

            var that = this;

            switch (this.searchType) {
                case 'no': {
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
                    $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsList', postData).done(function(res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                        var param = 'searchType=no&searchText=&company_id=' + that.filterData.company_id
                            + '&thematic_id=' + that.filterData.thematic_id
                            + '&psize=' + that.psize
                            + '&pn=' + page;
                        pushState(param, '商品列表');

                    }).fail(function() {
                        alert("获取商品数据失败");
                    });
                }
                    break;
                case 'goods_name': {
                    $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsListName',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            goods_name: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                        var param = 'searchType=goods_name&searchText=' + that.searchText
                            + '&company_id=' + that.filterData.company_id
                            + '&thematic_id=' + that.filterData.thematic_id
                            + '&psize=' + that.psize
                            + '&pn=' + page;
                        pushState(param, '商品列表');

                    }).fail(function() {
                        alert("请求失败");
                    });
                }
                    break;
                case 'goods_sn': {
                    $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsListSn',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            goods_sn: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                        var param = 'searchType=goods_sn&searchText=' + that.searchText + '&company_id=' + that.filterData.company_id
                            + '&thematic_id=' + that.filterData.thematic_id
                            + '&psize=' + that.psize
                            + '&pn=' + page;
                        pushState(param, '商品列表');

                    }).fail(function() {
                        alert("请求失败");
                    });
                }
                    break;
                case 'is_show': {
                    $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsListShow',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            is_show: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                        var param = 'searchType=is_show&searchText=' + that.searchText + '&company_id=' + that.filterData.company_id
                            + '&thematic_id=' + that.filterData.thematic_id
                            + '&psize=' + that.psize
                            + '&pn=' + page;
                        pushState(param, '商品列表');

                    }).fail(function() {
                        alert("请求失败");
                    });
                }
                    break;
                case 'price': {
                    $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsListPrice',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            price: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                        var param = 'searchType=price&searchText=' + that.searchText + '&company_id=' + that.filterData.company_id
                            + '&thematic_id=' + that.filterData.thematic_id
                            + '&psize=' + that.psize
                            + '&pn=' + page;
                        pushState(param, '商品列表');

                    }).fail(function() {
                        alert("请求失败");
                    });
                }
                    break;
                case 'nature': {
                    $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsListNature',
                        {
                            company_id: that.filterData.company_id,
                            thematic_id: that.filterData.thematic_id,
                            nature: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.goods = res.data.goodspage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                        var param = 'searchType=nature&searchText=' + that.searchText
                            + '&company_id=' + that.filterData.company_id
                            + '&thematic_id=' + that.filterData.thematic_id
                            + '&psize=' + that.psize
                            + '&pn=' + page;
                        pushState(param, '商品列表');

                    }).fail(function() {
                        alert("请求失败");
                    });
                }
                    break;
                default: alert("无搜索字段");

            }
            //$.post('http://onebuy.ping-qu.com/Admin/Goods/goodsList', postData).done(function(res) {
            //    that.goods = res.data.goodspage;
            //
            //    //计算数据页数
            //    that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
            //    //初始化分页器数据
            //    that.page = [];
            //    for (var i = 0; i < that.pageNum; i++) {
            //        that.page.$set(i, i + 1);
            //    }
            //
            //}).fail(function() {
            //    alert("请求失败");
            //});
        },
        pageTurn: function(event, page) {
            var that = this;
            if (that.filterData.company_id == 0 && that.filterData.thematic_id == 0) {
                var postData = {
                    psize: that.psize,
                    pn: page
                };
            } else if(that.filterData.company_id != 0 && that.filterData.thematic_id == 0) {
                var postData = {
                    company_id: that.filterData.company_id,
                    psize: that.psize,
                    pn: page
                };
            } else if(that.filterData.company_id != 0 && that.filterData.thematic_id != 0) {
                var postData = {
                    company_id: that.filterData.company_id,
                    thematic_id: that.filterData.thematic_id,
                    psize: that.psize,
                    pn: page
                };
            }
            $.post('http://onebuy.ping-qu.com/Admin/Goods/goodsList', postData).done(function(response) {
                that.goods = response.data.goodspage;

                //为分页器页码设置激活状态
                $('.am-pagination li').removeClass('am-active');
                $(event.target.parentElement).addClass('am-active');

            }).fail(function() {
                alert("无法获得商品数据");
            });
        }
    }
});
