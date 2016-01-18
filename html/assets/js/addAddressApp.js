/**
 * Created by xu on 2015/12/29.
 */
var addAddressApp = new Vue({
    el: '#addAddressApp',
    data: {
        companyId: getParam('company_id'),
        postData: {
            user_id: '',
            province: '',
            city: '',
            area: '',
            street: '',
            mobile: '',
            name: ''
        },
        //province: '',
        //city: '',
        //area: '',
        //linkageData: [],
        //cityArr: [],
        cityData: {}//省市区数据
    },
    created: function() {
        var that = this;
        var url = window.location.href;
        //检查是否是登录状态
        checkLogin(function(userid) {
            that.postData.user_id = userid;

            //初始化下拉菜单插件
            $('#province').mobiscroll().select({
                theme: 'ios',
                mode: 'scroller',
                display: 'bottom',
                lang: 'zh',
                inputClass: "mobiscroll-input",
                onSelect: function(val,inst){
                    var that = addAddressApp;
                    that.selUpdate(that.cityData, inst._value);
                    that.postData.province = inst._value;
                }
            });

            $('#city').mobiscroll().select({
                theme: 'ios', //插件样式风格
                mode: 'scroller',
                display: 'bottom',
                lang: 'zh',
                inputClass: "mobiscroll-input",
                onSelect: function(val,inst){
                    var that = addAddressApp;
                    that.selUpdate(that.cityData, false, inst._value);
                    that.postData.city = inst._value;
                }
            });

            $('#area').mobiscroll().select({
                theme: 'ios',
                mode: 'scroller',
                display: 'bottom',
                lang: 'zh',
                inputClass: "mobiscroll-input",
                onSelect: function(val,inst){
                    var that = addAddressApp;
                    that.postData.area = inst._value;
                }
            });
            //初始化下拉菜单插件--end

            if(getParam('address_id')){//修改地址
                $.post('http://onebuy.ping-qu.com/Api/Address/addressOneDetail',
                    {
                        address_id: getParam('address_id')
                    }
                ).done(function(res) {
                    var temp = res.data.address;
                    that.postData = {
                        user_id: userid,
                        address_id: getParam('address_id'),
                        province: temp.province,
                        city: temp.city,
                        area: temp.area,
                        street: temp.street,
                        mobile: temp.mobile,
                        name: temp.name,
                        is_default: temp.is_default + ''
                    };
                    $.getJSON('assets/json/city.json').done(function (cityData) {
                        that.cityData = cityData;
                        that.selUpdate(that.cityData, temp.province, temp.city, temp.area);

                    }).fail(function () {
                        alert("请求失败");
                    });

                }).fail(function() {
                    alert("请求失败");
                });
            } else {//添加地址
                that.postData.user_id = userid;

                $.getJSON('assets/json/city.json').done(function (res) {
                    that.cityData = res;
                    that.selUpdate(that.cityData);
                }).fail(function () {
                    alert("请求失败");
                });
            }
        });
    },
    methods: {
        addAdress: function () {
            var that = this;
            for(var key in this.postData){
                if(!this.postData[key]){
                    alert("请填写完整信息！" + key);
                    return false;
                }
            }
            if(this.postData.address_id){//修改地址
                $.post('http://onebuy.ping-qu.com/Api/Address/addressEdit', this.postData).done(function(res) {
                    if(res.errcode != '0'){
                        alert("修改失败");
                    } else {
                        history.back();
                    }
                }).fail(function() {
                    alert("请求失败");
                });
            } else {//添加地址
                $.post('http://onebuy.ping-qu.com/Api/Address/addressAdd', this.postData).done(function(res) {
                    if(res.errcode == '0'){
                        history.back();
                    } else if(res.errcode == '40018'){
                        alert("手机格式有误");
                    } else {
                        alert("添加失败");
                    }
                }).fail(function() {
                    alert("请求失败");
                });
            }
        },
        setDefault: function(event) {//设置默认地址
            if (this.postData.is_default == '0') {
                this.postData.is_default = '1';
                $(event.target).addClass('default-checked');
            } else if (this.postData.is_default == '1') {
                this.postData.is_default = '0';
                $(event.target).removeClass('default-checked');
            }

        },
        selUpdate: function(data, pro, city, area) {//省市区三级联动更新
            var proValue = pro ? pro : data.pro[0].value;//如果未选择省份则默认为省份数据中的第一个省
            var cityValue = city ? city : data.city[proValue][0].value;//如果未选择城市则默认为相应城市数据中的第一个城市
            var areaValue = area ? area : data.area[cityValue][0].value;

            //更新省份
            if ((pro && city && area) || (!pro && !city && !area)) {
                $('#province').empty();
                for (var i = 0; i < data.pro.length; i++) {
                    if (pro && data.pro[i].text == pro) {//如果已经选择了省份
                        $('#province').append('<option value="' + data.pro[i].value + '" selected>' + data.pro[i].text +'</option>');
                    } else {//未选择省份
                        $('#province').append('<option value="' + data.pro[i].value + '">' + data.pro[i].text +'</option>');
                    }
                }
                $('#province').mobiscroll('init');
            }

            if (pro || (!pro && !city && !area)) {
                //更新城市
                $('#city').empty();
                for (var i = 0; i < data.city[proValue].length; i++) {
                    if (city && data.city[proValue][i].text == city) {//如果已经选择了城市
                        $('#city').append('<option value="'
                            + data.city[proValue][i].value
                            + '" selected>'
                            + data.city[proValue][i].text
                            +'</option>');
                    } else {//未选择城市
                        $('#city').append('<option value="'
                            + data.city[proValue][i].value
                            + '">'
                            + data.city[proValue][i].text
                            +'</option>');
                    }
                }
                $('#city').mobiscroll('init');
            }


            $('#area').empty();
            for (var i = 0; i < data.area[cityValue].length; i++) {
                if (area && data.area[cityValue][i].text  == area) {//如果已经选择了区域
                    $('#area').append('<option value="'
                        + data.area[cityValue][i].value
                        + '" selected>'
                        + data.area[cityValue][i].text
                        +'</option>');
                } else {//未选择区域
                    $('#area').append('<option value="'
                        + data.area[cityValue][i].value
                        + '">'
                        + data.area[cityValue][i].text
                        +'</option>');
                }
            }
            $('#area').mobiscroll('init');
        }
    }
});

