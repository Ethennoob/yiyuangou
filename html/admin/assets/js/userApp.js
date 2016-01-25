/**
 * Created by xu on 2016/1/22.
 */
var userApp = new Vue({
    el: '#userApp',
    data: {
        user: [],
        deleteId: '',
        searchType: 'all',//记录搜索的字段
        searchText: '',//搜索的内容
        psize: 15,
        pageNum: '',
        page: [],
        currentPage: 1
    },
    created: function() {
        var that = this;
        if (location.href.indexOf('?') != -1) {
            that.searchText = decodeURI(getParam('searchText'));
            that.searchType = getParam('searchType');
            that.search(getParam('pn'));
        } else {
            that.searchType = 'all';
            that.search(1);
        }
    },
    methods: {
        search: function(page) {
            page = page > 0 ? page : 1;
            this.currentPage = page;
            var that = this;
            switch (this.searchType) {
                case 'all':
                    $.post('/Admin/User/userList',
                        {
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.user = res.data.userpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("无法获得用户数据");
                    });
                    break;
                case 'nickname':
                    $.post('/Admin/User/userListNickname',
                        {
                            nickname: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.user = res.data.userpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("搜索请求失败");
                    });
                    break;

                case 'mobile':
                    $.post('/Admin/User/userOnePhone',
                        {
                            mobile: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.user = res.data.userpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("搜索请求失败");
                    });
                    break;

                case 'is_follow':
                    $.post('/Admin/User/userListFollow',
                        {
                            is_follow: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.user = res.data.userpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("搜索请求失败");
                    });
                    break;

                case 'is_froze':
                    $.post('/Admin/User/userListFroze',
                        {
                            is_froze: that.searchText,
                            psize: that.psize,
                            pn: page
                        }
                    ).done(function(res) {
                        that.user = res.data.userpage;

                        //计算数据页数
                        that.pageNum = Math.ceil(res.data.pageInfo.dataSize / res.data.pageInfo.psize);
                        //初始化分页器数据
                        that.page = [];
                        for (var i = 0; i < that.pageNum; i++) {
                            that.page.$set(i, i + 1);
                        }

                    }).fail(function() {
                        alert("搜索请求失败");
                    });
                    break;

                default:
                    alert("缺少搜索字段");
            }

            //为分页器页码设置激活状态
            $('.am-pagination li').removeClass('am-active');
            $('.am-pagination li').eq(page - 1).addClass('am-active');
        },
        detailHref: function(userId) {
            var param = 'searchType=' + this.searchType
                + '&searchText=' + this.searchText
                + '&pn=' + this.currentPage;
            replaceState(param, "会员管理");
            window.location = 'user-detail.html?user_id=' + userId + '&searchType=' + this.searchType
                + '&searchText=' + encodeURI(this.searchText) + '&pn=' + this.currentPage;
        },
        deleteUser: function() {
            var that = this;
            $.post('/Admin/User/userOneDelete',
                {
                    user_id: that.user[that.deleteId].id
                }
            ).done(function(response) {
                if(response.errcode == '0'){
                    that.user.splice(that.deleteId, 1);
                    $('#deleteTip').modal('close');
                } else {
                    alert("删除失败");
                }
            }).fail(function() {
                alert("删除请求失败");
            });
        }
    }
});