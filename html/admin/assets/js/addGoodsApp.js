/**
 * Created by xu on 2016/1/19.
 */
var addGoodsApp = new Vue({
    el: '#addGoodsApp',
    data: {
        thematic: [],//专题选项
        companyList: [],//专区选项
        formData: {
            company_id: '',//选择的专区id
            thematic_id: '',//选择的专题id
            nature: '',//商品性质
            goods_name: '',
            goods_title: '',
            cost_price: '',//成本价
            price: '',//售价
            free_post: 1,//是否免邮
            goods_id: null,
            goods_desc:''
        },
        goodsImg: '',
        ue: {},//编辑器
        uploader: {}//上传插件实例
    },
    created: function() {
        var that = this;
        var server = '';
        if (getParam('goods_id')) {//修改商品时，获取数据

            server = '/Admin/Goods/goodsOneEdit';

            $.post('/Admin/Goods/goodsOneDetail',
                {
                    goods_id: getParam('goods_id')
                }
            ).done(function(res) {
                that.formData = {
                    company_id: res.data.goods.company_id,//选择的专区id
                    thematic_id: res.data.goods.thematic_id,//选择的专题id
                    nature: res.data.goods.nature,//商品性质
                    goods_name: res.data.goods.goods_name,
                    goods_title: res.data.goods.goods_title,
                    cost_price: res.data.goods.cost_price,//成本价
                    price: res.data.goods.price,//售价
                    free_post: 1,//是否免邮
                    goods_id: getParam('goods_id'),
                    goods_desc: res.data.goods.goods_desc
                };
                that.goodsImg = res.data.goods.goods_img;

                //获取专区数据
                $.post('/Admin/Company/companyOneList').done(function(res) {
                    that.companyList = res.data.company;
                }).fail(function() {
                    alert("专区数据获取失败");
                });

                //获取专题名称列表
                $.post('/Admin/Thematic/thematicSelect',
                    {
                        company_id: res.data.goods.company_id
                    }
                ).done(function(res) {
                    that.thematic = res.data.thematicSelect;
                }).fail(function() {
                    alert("请求失败");
                });

                //初始化编辑器
                UE.getEditor('editor').ready(function() {
                    that.ue = this;
                    this.setContent(that.formData.goods_desc);//为编辑器实例插入内容
                });
            }).fail(function() {
                alert("请求失败");
            });

        } else {//添加商品时，获取专区专题数据

            server = '/Admin/Goods/goodsAdd';

            //获取专区数据
            $.post('/Admin/Company/companyOneList').done(function(res) {
                that.companyList = res.data.company;
                that.formData.company_id = that.companyList[0].id;

                //获取专题名称列表
                $.post('/Admin/Thematic/thematicSelect',
                    {
                        company_id: that.formData.company_id
                    }
                ).done(function(res) {
                    that.thematic = res.data.thematicSelect;
                    that.formData.thematic_id = that.thematic[0].id;
                }).fail(function() {
                    alert("请求失败");
                });

            }).fail(function() {
                alert("专区数据获取失败");
            });

            //初始化编辑器
            UE.getEditor('editor').ready(function() {
                that.ue = this;
            });
        }

        //初始化上传插件
        that.uploader = uploaderInit(
            server,//数据接收接口
            '#filePicker',//上传图片按钮
            'goods_img',//文件上传的name
            {//额外表单数据
                company_id: '',//选择的专区id
                thematic_id: '',//选择的专题id
                nature: '',//商品性质
                goods_name: '',
                goods_title: '',
                cost_price: '',//成本价
                price: '',//售价
                free_post: 1,//是否免邮
                goods_id: null,
                goods_desc: ''
            },
            '#fileList',
            1//限制上传数量
        );
    },
    methods: {
        thematicChange:function() {//当专区改变，重新获取专题选项
            var that = this;
            $.post('/Admin/Thematic/thematicSelect',
                {
                    company_id: that.formData.company_id
                }
            ).done(function(res) {
                that.thematic = res.data.thematicSelect;
                that.formData.thematic_id = that.thematic[0].id;

            }).fail(function() {
                alert("请求失败");
            });
        },
        goodsSub: function() {//添加商品
            var that = this;
            that.formData.goods_desc = that.ue.getContent();

            //判断是否选择了商品图片
            if (that.uploader.getFiles().length > 0) {
                that.uploader.options.formData = that.formData;
                that.uploader.upload();//调用插件上传事件
                //队列上传完成时
                that.uploader.on('uploadAccept', function(obj, ret) {
                    if (ret.errcode == 0) {
                        alert("添加成功");
                        window.location = 'goods-list.html?company_id=' + that.formData.company_id
                            + '&thematic_id=' + that.formData.thematic_id
                            + '&searchType=no&pn=1&searchText=';
                    } else {
                        alert("添加失败，" + ret.errmsg);
                    }
                });
            } else {
                alert("请选择商品图片！");
            }
        },
        editSave: function() {//修改商品
            var that = this;
            that.formData.goods_desc = that.ue.getContent();

            //选择了图片则调用插件上传
            if (that.uploader.getFiles().length > 0) {
                that.uploader.options.formData = that.formData;
                that.uploader.upload();//调用插件上传事件
                //队列上传完成时
                that.uploader.on('uploadAccept', function(obj, ret) {
                    if (ret.errcode == 0) {
                        alert("修改成功");
                        history.back();
                    } else {
                        alert("修改失败，" + ret.errmsg);
                    }
                });
            } else {
                $.post('/Admin/Goods/goodsOneEdit', that.formData).done(function(res) {
                    if (res.errcode == 0) {
                        alert("修改成功");
                        history.back();
                    } else {
                        alert("修改失败");
                    }
                }).fail(function() {
                    alert("修改请求失败");
                });
            }

        },
        deleteGoods: function() {
            var that = this;
            $.post('/Admin/Goods/goodsOneDelete',
                {
                    goods_id: that.formData.goods_id
                }
            ).done(function(res) {
                if(res.errcode == 0){
                    alert("删除成功");
                    history.back();
                } else if(res.errcode == '70009') {
                    alert("无法删除")
                } else {
                    alert("删除失败");
                }
            }).fail(function() {
                alert("请求失败");
            });
        },
        clearImg: function() {//清空已选择的图片
            this.uploader.reset();
            $('#fileList').empty();
        }
    }
});