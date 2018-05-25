
(function($){
	$.screenResize = function(fn){
		fn();
		$(window).resize(function(){ fn(); });
	}
})(jQuery);