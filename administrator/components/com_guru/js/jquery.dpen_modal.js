function set_modal(selector, w, h) {
		jQuery(selector).openDOMWindow({ 
		eventType:'click', 
		loader:1, 
		loaderImagePath:'animationProcessing.gif', 
		loaderHeight:16, 
		loaderWidth:17 
		});
	
}

function close_modal() {
	jQuery('#close_gb').trigger('click');
}
