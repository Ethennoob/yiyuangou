//window.addEventListener( "load", function() {
//    FastClick.attach( document.body );
//}, false );

function getParam(name) {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        var strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
        }
    }
    return theRequest[name];
}

Vue.filter('date', function (value) {
    var myDate = new Date(value * 1000);
    return myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' +  myDate.getDate() + ' '
        + myDate.getHours() + ':' + myDate.getMinutes();
});