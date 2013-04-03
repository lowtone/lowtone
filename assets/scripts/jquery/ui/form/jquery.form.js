;(function($) {

	$(function() {

		var dateOptions = {
				beforeShow : function() {
					$('#ui-datepicker-div').css('z-index', 500); // @todo Fix this
				},
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'yy-mm-dd'
			},
			dateTimeOptions = $.extend(dateTimeOptions, dateOptions, {
				timeFormat: 'hh:mm:ss',
				hourGrid: 4,
				minuteGrid: 10
			});

		$('form .date').each(function() {
			var $input = $(this);

			if ($input.hasClass('time'))
				$input.datetimepicker(dateTimeOptions);
			else
				$input.datepicker(dateOptions);
		});

	});

})(jQuery);