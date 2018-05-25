/**
author : zhupinglei
**/

function index(){
	this.init();
}
index.prototype = {
	init : function(){
		this.event();
	},
	event : function(){
		$('.list-es li').on({
			'mouseenter' : function(){
				$(this).find('.qrcode').animate({'top':0},300);
			},
			'mouseleave' : function(){
				$(this).find('.qrcode').animate({'top':290},300);
			}
		})
	}
}
$(document).ready(function(){
	new index();
})