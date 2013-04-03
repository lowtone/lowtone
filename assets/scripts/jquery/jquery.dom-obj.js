;(function($) {

	$.extend({
		dom2obj: function(dom) {
			var obj = {};

			$(dom).children().each(function(i, child) {
				var $child = $(child);
				obj[child.nodeName] = $child.children().length ? $child.dom2obj() : $child.text();
			});

			return obj;
		}
	});
	
	$.fn.extend({
		dom2obj: function() {
			return $.dom2obj(this);
		}
	});

})(jQuery);