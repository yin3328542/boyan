/**
author : zhupinglei
**/

(function($){
	$.screenResize = function(fn){
		fn();
		$(window).resize(function(){ fn(); });
	}
	$.Banner = function(){
		var width = $(window).width();
		var $Banner = $('#banner'),
			$ul = $Banner.find('ul'),
			$li = $ul.find('li'),
			num = $li.length;
		$li.width(width);
		$ul.width(num*width);
		var ul = $ul.clone();
		$(ul).css({'left':width*num}).appendTo('.banner-list');
		var t = null;
		//自动轮循
		function auto(){
			t = setInterval(function(){
				$Banner.find('ul').animate({'left':'-='+width},500,function(){
					var left = parseFloat($(this).css('left'));
					if( left <= -width*num ){
						$(this).css({left : width*num });
					}
					if( left > -width*num && left <= 0 ){
						var ind = Math.floor(parseInt(-left)/width);
						$Banner.find('.banner-ind i').eq(ind).addClass('hover').siblings().removeClass('hover');
					}
				})
			},5000)
		}
		auto();
	}
})(jQuery);