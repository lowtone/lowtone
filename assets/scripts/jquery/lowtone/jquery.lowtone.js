/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone
 */

;(function($) {

	$(function() {

		// Fix legends
		
		if (!$.browser.webkit) 
			$('fieldset.lowtone legend').attr('align', 'center');

	});

})(jQuery);