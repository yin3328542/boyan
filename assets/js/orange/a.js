(function($){
	$.extend({
		 MeChat: function() {
                var str = '<style> .mc_button { display: none; }</style><style type="text/css">' +
                    '#MECHAT-BTN-2{z-index: 99;}' +
                    '.MECHAT_FLOAT_CHAT img{background: none;}' +
                    '</style>' +
                    '<script src="https://meiqia.com/js/mechat.js?unitid=10575&btn=hide" charset="UTF-8"></script>';
                $('html').append(str);
        },
        doMeChat: function() {
            $('.mc_button').hide();
			if (typeof mechatClick != 'undefined') {
				mechatClick();
			} else {
				alert('客服繁忙，请稍后重试');
			}
        }
	});
})(jQuery);