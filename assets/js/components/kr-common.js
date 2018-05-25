/**
 * Created by river on 14-10-29.
 */
/**
 * 通用方法
 */
define([
    'jquery'
], function( $){
    var KR = {
        alert: function(options) {
            if(!options) {
                return false;
            }
            if(typeof(options.type) === 'undefined') {
                options.type = 'success';
            }
            if($("#kr_alert")) { $('#kr_alert').remove();}

            $('body').prepend('<div id="kr_alert" class="kr_alert kr_alert_'+options.type+'">'+options.msg+'</div>');
            //位置
            $('#kr_alert').css({left: ((document.documentElement.clientWidth / 2) - ($('#kr_alert').width() / 2)) + 'px'});
            if(typeof(options.time) === 'undefined') {
                options.time = 3;
            }
            setTimeout(function() {
                $('#kr_alert').slideUp(function() {
                    $(this).remove();
                });
            }, options.time * 1000);
        },
        getUrlPara : function(key){
            var para = window.location.search;
            if( para ){
                var arr = para.split('?')[1].split('&'),
                    len = arr.length,
                    obj = {};
                for(var i = 0; i < len; i++){
                    var v = arr[i].split('='),
                        k = v[0],
                        value = v[1] ? v[1] : '';
                    obj[k] = value;
                }
                if( !key ){	//无key值，取全部
                    return obj;
                }else{	//有key值，取key值对应值
                    return obj[key];
                }
            }else{
                return '';
            }
        },

        getUrlPath : function(key) {
            var path = window.location.pathname;
            if(path) {
                var arr = path.split('/');
                if(arr[key]) {
                    return arr[key];
                } else {
                    return '';
                }
            } else {
                return '';
            }
        }
    };


    return KR;
});