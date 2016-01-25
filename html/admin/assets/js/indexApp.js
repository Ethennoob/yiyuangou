/**
 * Created by xu on 2016/1/20.
 */
//实例化Vue对象
var adminIndexApp = new Vue({
    el: '#adminIndexApp',
    data: {
        poster: [],//海报
        uploader: {},//上传插件实例
        uploadedTip: '',//上传提示
        uploadedNum: 0,
        formData: {//添加海报时除图片外的额外数据
            advertisement_id: '',//海报id
            adv_url: '',
            adv_name:'',
            company_id: ''
        },
        deleteId: '',//即将删除的海报的索引
        companyList: [],//专区列表
        companyId: ''//用于筛选不同专区海报
    },
    created: function() {
        var that = this;

        //获取专区数据
        $.post('/Admin/Company/companyOneList').done(function(res) {
            that.companyList = res.data.company;
            that.companyId = that.companyList[0].id;
            that.formData.company_id = that.companyList[0].id;

            //获取所有海报数据
            $.post('/Admin/Advertisement/advertisementOneList',
                {
                    company_id: that.companyId
                }
            ).done(function(res) {
                that.poster = res.data.advertisement;
            }).fail(function() {
                alert("海报数据获取失败");
            });

        }).fail(function() {
            alert("专区数据获取失败");
        });

        //初始化上传插件
        that.uploader = uploaderInit(
            '/Admin/Advertisement/advertisementOneAdd',//数据接收接口
            '#filePicker',//上传图片按钮
            'adv_img[]',//文件上传的name
            {//除文件以外上传的额外数据
                advertisement_id: '',//海报id
                adv_name: '',
                adv_url: '',
                company_id: ''
            },
            '#fileList',
            1//限制上传数量
        );
    },
    methods: {
        uploadPoster: function() {//上传海报or修改
            var that = this;
            if (that.uploader.getFiles().length > 0) {//添加海报 or 修改海报（有图）

                if (that.formData.advertisement_id) {//修改海报有图时修改上传地址
                    that.uploader.options.server = '/Admin/Advertisement/advertisementOneEdit';
                } else {//添加海报时修改上传地址
                    that.uploader.options.server = '/Admin/Advertisement/advertisementOneAdd';
                }

                that.uploader.options.formData = that.formData;//更新上传的表单数据

                that.uploader.upload();//调用插件上传事件

                //开始上传流程时触发
                that.uploader.on('startUpload', function(file, response) {
                    that.uploadedTip = "正在上传...";
                });

                //上传完一个文件时
                that.uploader.on('uploadSuccess', function(file, response) {
                    that.uploadedTip = "已上传" + ++that.uploadedNum + "张图...";
                });

                //队列上传完成时
                that.uploader.on('uploadFinished', function() {
                    that.uploadedTip = "上传完毕！";
                    that.companyId = that.formData.company_id;//更改筛选数据的专区id，显示相应专区数据
                    //获取所有海报数据
                    $.post('/Admin/Advertisement/advertisementOneList',
                        {
                            company_id: that.companyId
                        }
                    ).done(function(res) {
                        that.poster = res.data.advertisement;
                    }).fail(function() {
                        alert("海报数据获取失败");
                    });

                    //清空表单数据
                    that.formData.adv_name = '';
                    that.formData.adv_url = '';
                    that.formData.advertisement_id = null;
                    //清空上传队列，清空缩略图容器
                    that.uploader.reset();
                    $('#fileList').empty();

                });
            } else {//修改海报（无图）
                $.post('/Admin/Advertisement/advertisementOneEdit', that.formData).done(function(res) {
                    alert("修改成功");
                    //获取所有海报数据
                    $.post('/Admin/Advertisement/advertisementOneList',
                        {
                            company_id: that.companyId
                        }
                    ).done(function(res) {
                        that.poster = res.data.advertisement;
                    }).fail(function() {
                        alert("海报数据获取失败");
                    });

                    //清空表单
                    that.formData.adv_name = '';
                    that.formData.adv_url = '';
                    that.formData.advertisement_id = null;

                }).fail(function() {
                    alert("修改海报请求失败");
                });
            }

        },
        edit: function(index) {//点击修改按钮后，将相应记录的数据添加到表单数据中
            this.formData.advertisement_id = this.poster[index].id;
            this.formData.adv_name = this.poster[index].adv_name;
            this.formData.adv_url = this.poster[index].adv_url;
            this.formData.company_id = this.companyId;
        },
        deletePoster: function() {
            var that = this;
            //删除海报数据
            $.post('/Admin/Advertisement/advertisementOneDelete',
                {
                    id: that.poster[that.deleteId].id
                }
            ).done(function(res) {
                that.poster.splice(that.deleteId, 1);
                $('#deleteTip').modal('close');
            }).fail(function() {
                alert("删除请求失败！");
            });

        },
        clearImg: function() {//清空已选择的图片
            this.uploader.reset();
            $('#fileList').empty();
        },
        loadPoster: function() {//更换专区后加载海报
            var that = this;
            //获取所有海报数据
            $.post('/Admin/Advertisement/advertisementOneList',
                {
                    company_id: that.companyId
                }
            ).done(function(res) {
                that.poster = res.data.advertisement;
            }).fail(function() {
                alert("海报数据获取失败");
            });
        }
    }
});