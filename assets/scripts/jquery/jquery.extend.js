;(function($) {return;

	// Add show events

	var _oldShow = $.fn.show;

	$.fn.show = function() {
		return $(this).each(function() {
			var obj = $(this),
				newCallback = function() {
					if ($.isFunction(oldCallback)) 
						oldCallback.apply(obj);

					obj.trigger('afterShow');
				};

			obj.trigger('beforeShow');

			_oldShow.apply(obj, [speed, newCallback]);
		});
	};

})(jQuery);