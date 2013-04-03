;(function($) {

	var table = function(elm) {
		var $table = $(elm);

		if ($table.hasClass('scroll')) 
			makeScrollable();

		function makeScrollable() {
			var cellWidth = [];

			$('<th>').css({
				width: 16,
				padding: 0
			}).appendTo($table.find('thead tr')); // Extra cell to compensate scrollbar

			var $headCells = $table.find('thead tr:first').children();

			$table
				.find('tbody tr:first')
				.children()
				.each(function(index) {
					cellWidth[index] = $(this).outerWidth();

					$(this).css('width', cellWidth[index]);
					$headCells.eq(index).css('width', cellWidth[index]);
				});

			$table
				.find('thead, tbody')
				.css({
					display: 'block'
				})
				.filter('tbody')
				.css({
					'overflow-y': 'scroll',
					width: '100%'
				});
		}

		return {

		};
	}

	$.fn.table = function() {
		return this.each(function() {
			if ('table' != this.tagName.toLowerCase())
				return;

			$(this).data('lowtone-table', table(this));
		});
	}

	$(function() {
		$('table.lowtone').table();
	});

})(jQuery);