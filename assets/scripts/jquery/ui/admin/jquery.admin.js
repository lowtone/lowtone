;(function($) {

	var methods = {
		init: function() {
			return this;
		},
		open: function(options) {
			return this.each(function() {
				var $subject = $(this),
					$wrap = $('#wpbody-content .wrap');

				if ($wrap.length < 1)
					return;

				var $openWrap = $('<div class="wrap lowtone admin open" />').insertAfter($wrap);

				$wrap.hide(); // Detaching breaks TinyMCE

				var $icon = $('<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>')
						.appendTo($openWrap),
					$title = $('<h2></h2>')
						.appendTo($openWrap),
					$iframe = $('<iframe frameborder="0" scrolling="no">')
						.appendTo($openWrap);

				var options = $.extend(null, {
					title: $subject.attr('title'),
					back: jquery_ui_admin.open.back,
					url: $subject.attr('href'),
					back_link: window.location
				}, options);

				$title.html(options.title + ' <a href="' + options.back_link + '" class="add-new-h2"></a>');

				var updateIframe = function() {
					$iframe.each(function() {
						if (null === this.contentWindow.document.body) 
							return;

						if ($.browser.mozilla)
							$(this.contentWindow.document.body).css('height', 'auto'); // Fixed scrollheight

						this.style.height = this.contentWindow.document.body.scrollHeight + 'px';
					});
				};

				$iframe
					.css({
						width: '100%',
						border: 'none'
					})
					.load(updateIframe)
					.attr('src', options.url);

				var interval = window.setInterval(updateIframe, 100);
				
				$title
					.find('a')
					.html(options.back)
					.click(function() {
						clearInterval(interval);

						$wrap.show();
						$openWrap.remove();

						return false;
					});
			});
		}
	}

	$.fn.admin = function(method) {
		if (methods[method]) 
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		else if (typeof method === 'object' || !method) 
			return methods.init.apply( this, arguments);
		else 
			$.error('Method ' +  method + ' does not exist on jQuery.admin');
	};

})(jQuery);