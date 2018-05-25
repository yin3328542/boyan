$(document).ready(function(){
	var li_len = $('.imglist_w li').length,
		scr = Math.floor(li_len/4) + (li_len % 4 ? 1 : 0),	//屏数
		li_w = 310,
		max_w = 310*li_len;
	$('.imglist_w ul').width(max_w);
	
	var t1 = null;
	var t2 = null;
	var way = 1240;
	
	function auto(way){
		t1 = setInterval(function(){
			$('.imglist_w').addClass('moving');
			$('.imglist_w ul').animate({'left':'-='+way},500,function(){
				var left = parseInt($(this).css('left'));
				if( left == -((scr-1)*1240+1) ){
					way = -1240;
					$('.aright').removeClass('hover');
				}else if( left == -1 ){
					way = 1240;
					$('.aleft').removeClass('hover');
				}
				if( left < -1 ){
					$('.aleft').addClass('hover');
				}
				if( left > -((scr-1)*1240+1) ){
					$('.aright').addClass('hover');
				}
				$('.imglist_w').removeClass('moving');
			})
		},4000);
	}
	
	auto(way);
	
	function move(way){
		$('.imglist_w').addClass('moving');
		$('.imglist_w ul').animate({'left':'-='+way},500,function(){
			var left = parseInt($(this).css('left'));
			if( left == -((scr-1)*1240+1) ){
				way = -1240;
				$('.aright').removeClass('hover');
			}else if( left == -1 ){
				way = 1240;
				$('.aleft').removeClass('hover');
			}
			if( left < -1 ){
				$('.aleft').addClass('hover');
			}
			if( left > -((scr-1)*1240+1) ){
				$('.aright').addClass('hover');
			}
			$('.imglist_w').removeClass('moving');
			t2 = setTimeout(function(){
				auto(way);
			},4000);
		})
	}
	$('a.abtn').on('click',function(){
		if( $(this).hasClass('hover') && !$('.imglist_w').hasClass('moving') ){
			clearInterval(t1);
			clearTimeout(t2)
			if( $(this).hasClass('aleft') ){
				var arr = -1240;
			}else if( $(this).hasClass('aright') ){
				var arr = 1240;
			}
			move(arr);
		}
	})
})
