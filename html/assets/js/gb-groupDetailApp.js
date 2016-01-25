/**
 * Created by xu on 2016/1/20.
 */
var groupDetailApp = new Vue({
    el: '#groupDetailApp',
    data: {

    },
    created: function() {
        //初始化轮播
        var swiper = new Swiper('.gb-group-detail-swiper', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            observer:true,
            observeParents:true,
            autoHeight: true,
            autoplay : 3000,
            autoplayDisableOnInteraction : false,
            loop: true
        });
        //初始化轮播---end
    }
});