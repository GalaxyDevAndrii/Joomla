function set_modal(selector, w, h) {
	jQuery(selector).openDOMWindow({
		  height: h,
		  width: w,
		  positionTop: 50,
		  eventType: 'click',
		  positionLeft: 50,
		  windowSource: 'iframe',
		  windowPadding: 0,
		  loader: 1,
		  loaderImagePath: '',
		  loaderHeight: 31,
		  loaderWidth: 31		
	});
	jQuery('#close_gb').closeDOMWindow({
		eventType:'click'
	});
}

function close_modal() {
	jQuery('#close_gb').trigger('click');
}

/*jQuery(function() {
	var w = document.adminForm.page_width.value-50,
		h = document.adminForm.page_height.value-50;
	
	// #idul_meu
	set_modal('.modal2', w, h);
});*/