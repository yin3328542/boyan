/**
author : zhupinglei
**/

(function($){
	//template 配置
	template.config('escape',false);

	$.WhiteMask = function(){
		$('body').append('<div id="white-mask"></div>');
	}
	$.WhiteMaskClose = function(){
		$('#white-mask').fadeOut('fast',function(){
			$(this).remove();
		});
	}
	$.jsonP = function(url,type,data,call,nomask){
		data = $.extend({
			app_key: _global.app_key,
			access_token: _global.access_token
		},data);
		if( nomask != 'nomask' ){
			$.WhiteMask();
		}
		$.when(
			$.ajax({
				url : url,
				type : type,
				dataType : 'jsonp',
				// callback : 'callback',
				data : data
			})
		).done(function(res){
			if( nomask != 'nomask' ){
				$.WhiteMaskClose();
			}
			call(res);
		}).fail(function(res){
			$.PopupTip('连接服务器失败',false,'center');
		})
	}
	$.DefaultBanner = function(){
		var mySwiper = new Swiper('.swiper-container',{
		    pagination: '.banner-ind',	//索引class
		    loop:true,	//loop模式,你能够无限滑动滑块，到最后一个之后会跳转回第一个
		    grabCursor: true,	//值为true时，光标在Swiper上时成手掌状
		    paginationClickable: true,	//索引小圆点是否可点
		    autoplay : 3000	//自动播放
		})
	}
	$.NewMsg = function(){
		//消息提醒
		$.jsonP(_global.url.api + 'message','get',{
			open_id : _global.member_info.open_id,
			act : 'get_count'
		},function(res){
			if( res.ret == 0 ){
				var str = '<u class="new-msg"></u>';
				if( res.data.tx_fc + res.data.tx_td + res.data.tx_sms > 0 ){
					if( $('.index-btm').size() ){
						$('.index-btm li.a1').append(str);
					}
				}else{
					$('.index-btm li.a1 u').remove();
				}
				if( $('.retailcenter .item-nav li.gain').size() && $('.retailcenter .item-nav li.team').size() ){
					if( res.data.tx_fc == 1 ){
						$('.retailcenter .item-nav li.gain').append(str);
					}
					if( res.data.tx_td == 1 ){
						$('.retailcenter .item-nav li.team').append(str);
					}
				}
				if( $('.retailcenter .extend .msg').size() ){
					if( res.data.tx_sms > 0 ){
						$('.retailcenter .extend .msg').append('<i></i>');
					}
				}
			}
		},'nomask')
	}
	$.BottomBtn = function(ind){
		var str = 	'<div class="index-btm">'+
						'<ul class="clearfix">'+
							'<li class="a1">';
		if( _global && _global.member_info && typeof _global.member_info.shop_status ){
			var type = parseInt(_global.member_info.shop_status);
			switch( type ){
				case -1: 	//未申请
				case 2: 	//审核拒绝
					str += '<a href="/retail/apply"><i></i>成为分销商</a>';
					break;
				case 0: 	//已申请,审核中
					str += '<a href="/retail/state"><i></i>成为分销商</a>';
					break;
				case 1: 	//审核通过
					str += '<a href="/retail/index_v2"><i></i>分销商中心</a>';
					break;
				default:
					break;
			}
		}else{
			str	+=	'<a href="javascript:void(0);"><i></i>分销商中心</a>';
		}
							
		str	+=			'</li>'+
						'<li class="a2"><a href="/"><i></i>小店首页</a></li>'+
						'<li class="a3"><a href="/order/list_v2"><i></i>我的订单</a></li>'+
					'</ul>'+
				'</div>';
		if( typeof ind != 'undefined' ){
			str = $(str).find('li').eq(ind).addClass('hover').parents('.index-btm');
		}
		$('<div class="footer-blank"></div>').appendTo('body');
		$(str).appendTo('body');
		$.NewMsg();
	}
	$.TellFavMask = function(){
		var gz = '';
		if( _global.site_info.weixin_account ){
			gz = _global.site_info.weixin_account;
		}
		if( !gz ){
			$('body').append('<div id="toFav"></div>');
		}else{
			$('body').append('<div id="toFav"><div class="gz-num">'+gz+'</div></div>');
		}
		$('#toFav').on('click',function(){
			$(this).fadeOut('fast',function(){
				$(this).remove();
			})
		})
	}
	$.Qrcode = function(){
		$('body').append('<div id="Qrcode"><img src="'+_global.url.qrcode+'"><h3>亲，马上保存二维码图片分享至朋友圈</h3><h3>让伙伴长按二维码即可进入小店哦</h3></div>');
		$('#Qrcode').on('click',function(){
			$(this).fadeOut(function(){
				$(this).remove();
			})
		})
	}
	$.AddFoot = function(btm){
		var str = '<footer>'+
					'<p><a style="color:#666;" href="/">小店首页</a><a style="color:#666;" class="focus-us" href="javascript:void(0);">关注我们</a></p>'+
					'<p><a href="http://mp.weixin.qq.com/s?__biz=MzAwMzA1NzU4Ng==&mid=201683746&idx=1&sn=473cef4ffdf69de5049c7e41d92babcd&scene=1&from=singlemessage&isappinstalled=0#rd"><span class="color-orange">121店</span> · 分享创造价值</a></p>'+
				  '</footer>';
		if( $('.btm-bar').size() || $('.sub-bar').size() ){
			str += '<div class="footer-blank"></div>';
		}
		$('#main').append(str);
		$('footer .focus-us').on('click',function(){
			$.TellFavMask();
		})
	}
	$.Share = function(data){
		// 所有功能必须包含在 WeixinApi.ready 中进行
		WeixinApi.ready(function(Api){

			// 微信分享的数据
			var wxData = data;
		    
		    // 分享的回调
		    var wxCallbacks = {
		        // 分享操作开始之前
		        ready:function () {
		            // 你可以在这里对分享的数据进行重组
		            $.WhiteMask();
		        },
		        // 分享被用户自动取消
		        cancel:function (resp) {
		            // 你可以在你的页面上给用户一个小Tip，为什么要取消呢？
		            $.WhiteMaskClose();
		        },
		        // 分享失败了
		        fail:function (resp) {
		            // 分享失败了，是不是可以告诉用户：不要紧，可能是网络问题，一会儿再试试？
		            $.WhiteMaskClose();
		        },
		        // 分享成功
		        confirm:function (resp) {
		            // 分享成功了，我们是不是可以做一些分享统计呢？
		            $.WhiteMaskClose();
		        },
		        // 整个分享过程结束
		        all:function (resp) {
		            // 如果你做的是一个鼓励用户进行分享的产品，在这里是不是可以给用户一些反馈了？
		            $.WhiteMaskClose();
		        }
		    };
		 
		    // 用户点开右上角popup菜单后，点击分享给好友，会执行下面这个代码
		    Api.shareToFriend(wxData, wxCallbacks);
		 
		    // 点击分享到朋友圈，会执行下面这个代码
		    Api.shareToTimeline(wxData, wxCallbacks);
		 
		    // 点击分享到腾讯微博，会执行下面这个代码
		    Api.shareToWeibo(wxData, wxCallbacks);
		});
	}

	WeixinApi.ready(function(Api){
		Api.showOptionMenu();
	})

	//share
	if( !$('#main').hasClass('goods-page') ){
		var share_url = _global.url.site_url;
		if( _global.source_info && _global.source_info.id ){
			share_url += 'shop_id/' + _global.source_info.id;
		}
		$.Share({
			"imgUrl" : _global.site_info.logo_file,
	        "link" : share_url,
	        "desc" : _global.site_info.intro,
	        "title" : _global.site_info.name
		})
	}

	$.getCityData = function(call){
		$.ajax({
			url : '/assets/js/common/cityData.min.json',
			dataType : 'json',
			success : function(res){
				call(res);
			}
		})
	}
	$.showCart = function(){
		$.ajax({
			url : '/api/cart_is_exist_goods',
			type : 'post',
			dataType : 'json',
			data : {
				open_id : _global.member_info.open_id
			}
		}).done(function(res){
			if( res.ret == 0 ){
				var str = '<a href="/cart" id="cart-icon"></a>';
				$('body').append(str);
				if( $('.index-btm').size() || $('.btm-bar').size() ){
					$('#cart-icon').css({'bottom':'55px'});
				}
			}
		})
	}
})(jQuery);