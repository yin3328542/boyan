$(document).ready(function(){
	var t = null;
	$('#user-name,#user-info-menu').on('mousemove',function(){
		clearTimeout(t);
		$("#user-info-menu").css("display","block");
	})
	$('#user-name,#user-info-menu').on('mouseout',function(){
		t = setTimeout(function(){
			$("#user-info-menu").css("display","none");
			clearTimeout(t);
		},200)
	})
})
