/**
author : zhupinglei
**/

function index(){
	this.init();
}
index.prototype = {
	init : function(){
		$.Banner();
		this.GoodList();
		this.GiftList();
		this.Factories();
		this.News();
	},
	GoodList : function(){
		$('.goods-list .lists').each(function(){
			var $ul = $(this).find('ul'),
				num = $ul.length;
			if( num > 1 ){
				$ul.each(function(index){
					$(this).css({'left':index*290});
				})
				var t = null;
				function auto(){
					t = setInterval(function(){
						$ul.parent().addClass('moving');
						$ul.animate({'left':'-='+290},500,function(){
							var left = parseFloat($(this).css('left'));
							if( left < 0 ){
								$(this).css({'left':(num-1)*290});
							}
							$ul.parent().removeClass('moving');
						})
					},3000);
				}
				auto();

				$(this).parent().find('.arrs a').on('click',function(){
					if( !$ul.parent().hasClass('moving') ){
						clearInterval(t);
						$ul.parent().addClass('moving');
						var type = $(this).attr('class');
						if( type == 'icon-arr-left' ){
							$ul.each(function(){
								var left = parseFloat($(this).css('left'));
								if( left == (num-1)*290 ){
									$(this).css({'left':'-290px'});
								}
							})
							$ul.animate({'left':'+='+290},500,function(){
								$ul.parent().removeClass('moving');
							})
						}else if( type == 'icon-arr-right' ){
							$ul.animate({'left':'-='+290},500,function(){
								var left = parseFloat($(this).css('left'));
								if( left < 0 ){
									$(this).css({'left':(num-1)*290});
								}
								$ul.parent().removeClass('moving');
							})
						}
						auto();
					}
				})
			}
		})
	},
	GiftList : function(){
		var $list = $('.gift-list'),
			$li = $list.find('li'),
			$ind = $list.find('.gift-list-ind'),
			num = $li.length;
		$li.hide().eq(0).show().addClass('hover');
		var str = '';
		for(var i = 0; i < num; i++){
			str += '<i></i>';
		}
		$(str).appendTo('.gift-list-ind');
		$ind.find('i').width(570/num).eq(0).addClass('hover');

		function next(ind){
			$('.gift-list li').eq(ind).fadeIn().addClass('hover').siblings().fadeOut().removeClass('hover');
			$('.gift-list-ind i').eq(ind).addClass('hover').siblings().removeClass('hover');
		}
		var t = null;
		function auto(){
			t = setInterval(function(){
				var ind = $('.gift-list li.hover').index();
				if( (ind+1) == num ){
					ind = 0;
				}else{
					ind++;
				}
				next(ind);
			},3000)
		}
		auto();

		$ind.find('i').hover(function(){
			clearInterval(t);
			var ind = $(this).index();
			next(ind);
			auto();
		})
	},
	Factories : function(){
		$('.factories-main ul').hide().eq(0).show().addClass('hover').siblings().removeClass('hover');
		$('.factories-list li').eq(0).addClass('hover').siblings().removeClass('hover');
		$('.factories-list li').hover(function(){
			var ind = $(this).index();
			$(this).addClass('hover').siblings().removeClass('hover');
			$('.factories-main ul').eq(ind).fadeIn().addClass('hover').siblings().fadeOut().removeClass('hover');
		})
	},
	News : function(){
		$('#news .news-nav').each(function(){
			$(this).find('li').on('mouseenter',function(){
				if( !$(this).hasClass('hover') ){
					var ind = $(this).index();
					console.log(ind);
					$(this).addClass('hover').siblings().removeClass('hover');
					$(this).parents('.news-nav').next().find('.news-table').eq(ind).addClass('hover').siblings().removeClass('hover');
				}
			})
		})
	}
}
$(document).ready(function(){
	new index();
})