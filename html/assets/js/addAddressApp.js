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
                    console.log(inst);
                    var that = addAddressApp;
                    that.cityUpdate(that.cityData, inst._value);
                    that.areaUpdate(that.cityData, that.cityData.city[inst._value][0].text);
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
                    console.log(inst);
                    var that = addAddressApp;
                    that.areaUpdate(that.cityData, inst._value);
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
                    console.log(inst);
                    var that = addAddressApp;
                    that.postData.area = inst._value;
                }
            });
            //初始化下拉菜单插件--end

            if(getParam('address_id')){//修改地址
                $.post('/Api/Address/addressOneDetail',
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
                $.post('/Api/Address/addressEdit', this.postData).done(function(res) {
                    if(res.errcode != '0'){
                        alert("修改失败");
                    } else {
                        window.location = 'address-manage.html?company_id=' + that.companyId;
                    }
                }).fail(function() {
                    alert("请求失败");
                });
            } else {//添加地址
                $.post('/Api/Address/addressAdd', this.postData).done(function(res) {
                    if(res.errcode == '0'){
                        window.location = 'address-manage.html?company_id=' + that.companyId;
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
        cityUpdate: function(data, pro) {
            //更新城市
            this.postData.city = data.city[pro][0].text;//用户还未选择城市时默认为第一个
            $('#city').empty();
            for (var i = 0; i < data.city[pro].length; i++) {
                $('#city').append('<option value="'
                    + data.city[pro][i].text
                    + '">'
                    + data.city[pro][i].text
                    +'</option>');
            }
            $('#city').mobiscroll('init');
        },
        areaUpdate: function(data, city) {
            //更新地区
            this.postData.area = data.area[city][0].text;//用户还未选择区域时默认为第一个
            $('#area').empty();
            for (var i = 0; i < data.area[city].length; i++) {
                $('#area').append('<option value="'
                    + data.area[city][i].text
                    + '">'
                    + data.area[city][i].text
                    +'</option>');
            }
            $('#area').mobiscroll('init');
        },
        selUpdate: function(data, pro, city, area) {
            //省市区三级联动数据初始化
            var proValue = pro ? pro : data.pro[0].text;
            var cityValue = city ? city : data.city[proValue][0].text;
            var areaValue = area ? area : data.area[cityValue][0].text;

            this.postData.province = proValue;
            this.postData.city = cityValue;
            this.postData.area = areaValue;

            for (var i = 0; i < data.pro.length; i++) {
                if (pro && data.pro[i].text == pro) {//如果已经选择了省份
                    $('#province').prepend('<option value="'
                        + data.pro[i].text
                        + '" selected>'
                        + data.pro[i].text
                        +'</option>');
                } else {//未选择省份
                    $('#province').append('<option value="'
                        + data.pro[i].text
                        + '">'
                        + data.pro[i].text
                        +'</option>');
                }
            }
            $('#province').mobiscroll('init');

            for (var i = 0; i < data.city[proValue].length; i++) {
                if (city && data.city[proValue][i].text == city) {//如果已经选择了城市
                    $('#city').prepend('<option value="'
                        + data.city[proValue][i].text
                        + '" selected>'
                        + data.city[proValue][i].text
                        +'</option>');
                } else {//未选择城市
                    $('#city').append('<option value="'
                        + data.city[proValue][i].text
                        + '">'
                        + data.city[proValue][i].text
                        +'</option>');
                }
            }
            $('#city').mobiscroll('init');

            for (var i = 0; i < data.area[cityValue].length; i++) {
                if (area && data.area[cityValue][i].text  == area) {//如果已经选择了区域
                    $('#area').append('<option value="'
                        + data.area[cityValue][i].text
                        + '" selected>'
                        + data.area[cityValue][i].text
                        +'</option>');
                } else {//未选择区域
                    $('#area').append('<option value="'
                        + data.area[cityValue][i].text
                        + '">'
                        + data.area[cityValue][i].text
                        +'</option>');
                }
            }
            $('#area').mobiscroll('init');
        }
    }
});

