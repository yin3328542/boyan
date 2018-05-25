/**
author : zhupinglei
**/

function index(){
	this.init();
}
index.prototype = {
	init : function(){
		this.img();
	},
	img : function(){
		var $wrap = $('.fencai-img-list'),
			$ul = $wrap.find('ul'),
			$li = $ul.find('li'),
			num = $li.length,
			maxw = (200+47)*num;
		$ul.width( maxw );
		var ulstr = $ul.clone();
		$(ulstr).css({'left':maxw}).appendTo('.fencai-img-list');

		$ul = $wrap.find('ul');

		var t = null;
		function auto(){
			$ul.each(function(){
				var left = parseFloat( $(this).css('left') );
				if( left <= -maxw ){
					$(this).css({'left':maxw});
				}
			})
			t = setInterval(function(){
				$wrap.addClass('moving');
				$ul.animate({'left':'-=247'},500,function(){
					var left = parseFloat( $(this).css('left') );
					if( left <= -maxw ){
						$(this).css({'left':maxw});
					}
					$wrap.removeClass('moving');
				})
			},3000)
		}
		auto();

		$('.arrs a').on('click',function(){
			if( !$wrap.hasClass('moving') ){
				$wrap.addClass('moving');
				clearInterval(t);
				var type = $(this).attr('class');
				if( type == 'arr-left' ){
					$ul.each(function(){
						var left = parseFloat( $(this).css('left') );
						if( left <= -maxw ){
							$(this).css({'left':maxw});
						}
					})
					$ul.animate({'left':'-=247'},500,function(){
						var left = parseFloat( $(this).css('left') );
						if( left <= -maxw ){
							$(this).css({'left':maxw});
						}
						$wrap.removeClass('moving');
					})
				}else if( type == 'arr-right' ){
					$ul.each(function(){
						var left = parseFloat( $(this).css('left') );
						if( left >= maxw ){
							$(this).css({'left':-maxw});
						}
					})
					$ul.animate({'left':'+=247'},500,function(){
						var left = parseFloat( $(this).css('left') );
						if( left >= maxw ){
							$(this).css({'left':-maxw});
						}
						$wrap.removeClass('moving');
					})
				}
				auto();
			}
		})
	}
}
$(document).ready(function(){
	new index();
})