jQuery(document).ready(function($){

	// process dropdown hover event
	var navitem_selector = '.t4-megamenu .nav-item',
		navitem_selector_dropdown = '.t4-megamenu .nav-item.dropdown',
		$activeitem = null, timeout = 0;

	var hideDropdowns = function () {
		var $opens = $(navitem_selector_dropdown + '.show');
		$opens.each(function() {
			var $item = $(this);
			if ($activeitem && $activeitem.closest($item).length) return;
			$item.removeClass('show').find('.dropdown-menu').removeClass('show');
			endAnimating($item);
		})
	}


	var pos = function () {
		var $dropdown = $activeitem.addClass('show').children('.dropdown-menu').addClass('show');
		//$activeitem.addClass('show').find('.dropdown-menu').addClass('show');
		var rtl = $('html').attr('dir') == 'rtl',
			dw = $dropdown.outerWidth(),
			ww = $(window).width(),
			dl = $dropdown.offset().left,
			iw = $activeitem.width(),
			il = $activeitem.offset().left,
			ml = null,
			align = $activeitem.data('align');

		ml = align == 'center' ? (iw-dw)/2 : (align == 'right' ? iw-dw : 0);
		if (dw < ww) {
			if (il + ml < 20) ml = 20 - il;
			if (il + ml + dw > ww - 20) ml = ww - 20 - il - dw;
		} else {
			ml = (ww-dw)/2 - il;
		}

		$dropdown.css('margin-left', ml);

	}

	var posrtl = function () {
		var $dropdown = $activeitem.addClass('show').children('.dropdown-menu').addClass('show');
		//$activeitem.addClass('show').find('.dropdown-menu').addClass('show');
		var rtl = $('html').attr('dir') == 'rtl',
			dw = $dropdown.width(),
			ww = $(window).width(),
			dl = $dropdown.offset().left,
			iw = $activeitem.width(),
			il = $activeitem.offset().left,
			ml = null,
			align = $activeitem.data('align');

		ml = align == 'center' ? (dw-iw)/2 : (align == 'right' ? dw-iw : 0);
		if (dw < ww) {
			if (il + iw + ml > ww - 20) ml = ww - 20 - il - iw;
			if (il + iw + ml < dw + 20) ml = dw + 20 - il - iw;
		} else {
			ml = ww - il - iw + (dw - ww)/2;
		}
		$dropdown.css('margin-right', -ml);
	}

	var showDropdown = function () {

		if ($activeitem.is('.dropdown')) {
			var $dropdown = $activeitem.addClass('show').children('.dropdown-menu').addClass('show');

			// with animation, start animating after some ms
			startAnimating ($activeitem);

			if ($('html').attr('dir') == 'rtl') {
				posrtl();
			} else {
				pos();
			}
		}
		// hide other dropdown
		hideDropdowns();
	}

	var startAnimating = function ($item) {
		// get duration
		var $menu = $item.closest('.t4-megamenu');

		if (!$menu.hasClass('animate')) return;


		clearTimeout($item.data('animating-timer'));
		$item.data('animating-timer', setTimeout(function() {
			$item.addClass('animating');
		}, 10));
	}

	var endAnimating = function ($item) {

		// remove animating class to make sure the dropdown is totally hidden
		// get duration
		var $menu = $item.closest('.t4-megamenu');
		if (!$menu.hasClass('animate')) return;
		var duration = parseInt($menu.data('duration')) || 400;
		clearTimeout($item.data('animating-timer'));
		$item.data('animating-timer', setTimeout(function() {
			$item.removeClass('animating');
		}, duration + 10))
	}
	// $(document).find('t4')
	$('body').on('mouseenter', navitem_selector, function(e) {
		var $this = $(this);
		// prevent flict showing menu;
		if ($activeitem && $(e.target).is('div') && !$(e.target).closest($activeitem).length && $this.closest(navitem_selector_dropdown).length) {
			clearTimeout(timeout);
			return;
		}
		var $menu = $this.closest('.t4-megamenu'),
			id = $menu.attr('id'), $toggle = $('.navbar-toggler[data-target="#' + id + '"]');
			// fix dropdown menu offset right to let
		var rt = ($(window).width() - ($(this).offset().left + $(this).outerWidth())); 
		if($(window).width() > 991 && rt < 150 && ($(this).hasClass('dropright') || $(this).hasClass('dropend'))){
			$(this).removeClass('dropright dropend').addClass('dropleft dropstart');
		}
		if ($toggle.length && $toggle.is(':visible')) {
			// mobile, then remove animation and ignore
			if ($menu.hasClass('animate')) $menu.removeClass('animate').addClass('animate-bak');
			return;
		} else {
			if ($menu.hasClass('animate-bak')) $menu.removeClass('animate-bak').addClass('animate');
		}
		if ($this.closest(navitem_selector_dropdown).length) {
			if (timeout) {
				clearTimeout(timeout);
				timeout = 0;
			}

			var $_activeitem = $this.closest(navitem_selector_dropdown);

			if (!$_activeitem.hasClass('show') && !$(e.target).is($_activeitem) && !$(e.target).parent().is($_activeitem) && !$(e.target).is('.item-caret')) return;

			$activeitem = $_activeitem;
			showDropdown();
		} else {
			//timeout = setTimeout(function() {
				$activeitem = null;
				hideDropdowns();
			//}, 200)
		}
	}).on('mouseleave', navitem_selector, function(e) {
		var $this = $(this);
		timeout = setTimeout(function() {
			if ($activeitem && $activeitem.is($this)) {
				$activeitem = $this.parent().closest(navitem_selector);
				hideDropdowns();
			}
		}, 200)

	});

	// if menu open, just open the link
	var lastClickItem = null;
	$('.nav-item.dropdown a').on('click', function(e) {
		var $this = $(this);
		var parentDrpEl = $(this).closest('ul.dropdown-menu');
		if ($this.is(lastClickItem)) {
			var arr1 = this.href.split('#'),
				arr2 = location.href.split('#');
			if (arr1[0] == arr2[0])	{
				if (arr1.length > 1 && arr1[1]) location.hash =  '#' + arr1[1];
			} else {
				location.href = this.href;
			}
			e.preventDefault();
			e.stopPropagation();
			return false;
		} else {
			location.hash = "";
			var arr1 = this.href.split('#'),
				arr2 = location.href.split('#');
			if (arr1[0] == arr2[0])	{
				if (arr1.length > 1 && arr1[1]) location.hash =  '#' + arr1[1];
			}
			lastClickItem = $this;

			if(location.hash && !$this.is('.separator')){
				$('.js-offcanvas-close').trigger('click');
			}
		}

		return true;
	})

	// show toggler
	$('.t4-megamenu').each(function() {
		$toggle = $('.navbar-toggler[data-target="#' + this.id + '"]');
		if ($toggle.length == 1) $toggle.removeAttr('style');
	})
    

})
