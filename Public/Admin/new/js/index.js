jQuery(document).ready(function($) {

	// If firefox
	if(navigator.userAgent.toLowerCase().match(/firefox/)) {
		$('.browser-warning').removeClass('hidden');
		setTimeout(function() {
			$('.browser-warning').addClass('hidden');
		}, 6*1000);
	}

	$('#window').attr('style', '');


	function resetAnimation() {
		var win = $('#window');

		win.stop().fadeOut(500, function() {

			// Reset things
			win.attr('style', '');
			win.find('input[type=text], input[type=password]').val('');
			win.find('.load-btn.loading').removeClass('loading done');

			// Clone and re-create window element to trigger animation restart
			win.removeClass('flip');
			win.before(win.clone(true)).remove();

			// Restart animation
			initAnimation();
		});
	}

});