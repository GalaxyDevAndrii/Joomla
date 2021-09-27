jQuery(document).ready(function($) {
	var $toc = $('.table-of-contents'),
		arrowRight = Joomla.getOptions('system.paths').root + '/components/com_guru/images/arrow-right.gif',
		arrowDown = Joomla.getOptions('system.paths').root + '/components/com_guru/images/arrow-down.gif';

	$toc.on('click', '.show_sub', function() {
		$toc.find('.lessons_wrap').children().show();
		$toc.find('.guru-tab-title .day img').attr('src', arrowRight );
	});

	$toc.on('click', '.close_sub', function() {
		$toc.find('.lessons_wrap').children().hide();
		$toc.find('.guru-tab-title .day img').attr('src', arrowDown );
	});

	// override default function
	show_hidde = function(){ };

	$toc.on('click', '.guru-tab-title .day', function( e ) {
		var $el = $( this ),
			$lessons = $el.closest('.guru-tabs').find('.lessons_wrap').children();

		if ( $lessons.is(':visible') ) {
			$lessons.hide();
			$el.find('img').attr('src', arrowDown );
		} else {
			$lessons.show();
			$el.find('img').attr('src', arrowRight );
		}
	});
})