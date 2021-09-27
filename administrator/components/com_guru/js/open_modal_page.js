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