$(document).ready(function(){
	var page=$('body').attr('page');
	if (page != 'index'){
		var t = null;
		$('#nav-category,.nav-category-item').on('mousemove',function(){
			clearTimeout(t);
			$("#nav-category-section").show();
			$(".type-icon").html('&#xe606;');
		})
		$('#nav-category').on('mouseout',function(){
			t = setTimeout(function(){
				$("#nav-category-section").hide();
				$(".type-icon").html('&#xe608;');
				clearTimeout(t);
			},200)
		})
		$('.site-header').height(140);
	}
})
