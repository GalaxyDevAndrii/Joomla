function alertNotification(){
	document.getElementById("guru-not-content").className = "open";
}

jQuery(document).click(function(e){
	if (jQuery(e.target).attr('id') != 'guru-dropdown-toggle' && jQuery(e.target).attr('id') != 'icon-bell' && jQuery(e.target).attr('id') != 'badge-important' && jQuery(e.target).attr('id') != 'new-options-button'){
		if(eval(document.getElementById("guru-not-content"))){
			document.getElementById("guru-not-content").className = "";
		}
		
		if(eval(document.getElementById("button-options"))){
			document.getElementById("button-options").style.display = "none";
		}
	}
})