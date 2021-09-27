if (window.UIkit) {
	if ( UIkit.noConflict) {
		var myUIkit = UIkit.noConflict();
	}
} else {
	var fileref=document.createElement("link");
	fileref.setAttribute("rel", "stylesheet");
	fileref.setAttribute("type", "text/css");
	fileref.setAttribute("href", Joomla.getOptions('system.paths').root + 'components/com_guru/css/uikit.almost-flat.min.css');
	
	if (typeof fileref!="undefined"){
		if(document.getElementsByTagName("body")[0] === 'undefined'){
			// do nothing
		}
		else{
			document.getElementsByTagName("body")[0].appendChild(fileref);
		}
	}
}