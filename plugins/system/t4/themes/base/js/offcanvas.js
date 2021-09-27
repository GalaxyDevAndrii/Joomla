jQuery(document).ready(function($){
	var $offcanvas = $('.t4-offcanvas');
	setTimeout(function(){
		$offcanvas.show();
	},300);
	$('.t4-wrapper').addClass('c-offcanvas-content-wrap');
	if(!$('#triggerButton').length ) $offcanvas.remove();
	if($offcanvas.length && $('#triggerButton').length){


		$offcanvas.offcanvas({
			triggerButton: '#triggerButton' ,
			onOpen: function () {
				$('#triggerButton').addClass('active');
				bodyScrollLock.disableBodyScroll ($('.t4-off-canvas-body').get(0))
			},
			onClose: function () {
				$('#triggerButton').removeClass('active');
				setTimeout(function(){
					bodyScrollLock.enableBodyScroll ($('.t4-off-canvas-body').get(0))
				},300);
			}
		});

		if($('.t4-off-canvas-body').data('effect') == 'drill'){
			$('.t4-off-canvas-body nav.navbar').addClass('drilldown-effect');
		}
		$( '.t4-off-canvas-body ul.dropdown-menu' ).each(function( index ) {
			var label = $(this).prev().text();
			$(this).prepend("<span class='sub-menu-back'><i class='fas fa-chevron-left'></i>"+label+"</span>");
		 	$( this ).before('<span class="sub-menu-toggle btn-toggle"></span>');
		}); 
		$('.sub-menu-toggle').on('click', function(e) {
			e.preventDefault();
			$offc_level = $(this).parent('li').data('level') || 0;
			$offcanvas.removeClass(function (index, css) {
				return (css.match (/\oc-level-\S+/g) || []).join(' '); // removes anything that starts with "page-"
			})
			$offcanvas.addClass('oc-level-'+$offc_level);
			if($offc_level == 1){
				$('ul.dropdown-menu').not($(this).next()).hide(200);
				$('.sub-menu-toggle').not($(this)).removeClass('is-active');
			}else if ($offc_level == 2) {
				$(this).parents('ul.dropdown-menu').find('ul.dropdown-menu').not($(this).next()).hide(200);
				$(this).parents('ul.dropdown-menu').find('.sub-menu-toggle').not($(this)).removeClass('is-active');
			}
			$(this).next().slideToggle(200);
			$(this).toggleClass('is-active');
		});
		$('.sub-menu-back').on('click', function(e) {
			e.preventDefault();
			$offc_level = $(this).closest('li.nav-item').data('level') || 0;
			$offc_level = ($offc_level != 0) ? $offc_level - 1 : 0;
			$offcanvas.removeClass(function (index, css) {
				return (css.match (/\oc-level-\S+/g) || []).join(' '); // removes anything that starts with "page-"
			})
			$offcanvas.addClass('oc-level-'+$offc_level);
			$(this).parent('ul.dropdown-menu').slideToggle(200);
			$('.sub-menu-toggle').toggleClass('is-active');
		});
	}

});