var modalWindow = {
	parent:"body",
	windowId:null,
	content:null,
	width:null,
	height:null,
	left:null,
	right:null,
	top:null,
	bigmodal:null,
	close:function()
	{
		jQuery(".guru-modal").remove();
		jQuery(".modal-overlay").remove();
	},
	open:function()
	{
		modal_style = "";
		
		if(this.width != null && this.width != 0){
			screen_width = window.innerWidth;
			screen_height = window.innerHeight;
			modal_style = 'style="display:block; height:'+(screen_height-100)+'px;"';
		}
		else{
			modal_style = 'style="display:block; height:90%;"';
		}
		
		var modal = "";
		modal += "<div class=\"modal-overlay\"></div>";
		modal += "<div class=\"guru-modal p_modal\" id=\"" + this.windowId + "\">";
		modal += "<div class=\"guru-modal-dialog\" "+modal_style+">";
		modal += "<div class=\"guru-modal-content\">";
		modal += this.content;
		modal += "</div>";
		modal += "</div>";
		modal += "</div>";
		jQuery(this.parent).append(modal);


		jQuery(".guru-modal-dialog").append("<a class=\"close-window\" id=\"close-window\"></a>");
		jQuery(".close-window").click(function(){modalWindow.close();});
		jQuery(".modal-overlay").click(function(){modalWindow.close();});
		jQuery(".guru-modal").click(function(){modalWindow.close();});
	}
};

var openMyModal = function(width, height, source){
	modalWindow.windowId = "myModal";
	iframe_style = "";
	if(width != 0 && height != 0){
		screen_width = window.innerWidth;
		screen_height = window.innerHeight;
		
		modalWindow.width = width;
		modalWindow.height = height;
		
		iframe_style = 'style="width:100%; height:'+(screen_height-100)+'px;"';
	}
	else{
		modalWindow.width = 0;
		modalWindow.height = 0;
		iframe_style = 'style=""';
	}
	
	modalWindow.content = "<iframe "+iframe_style+" id='g_preview' class='pub_modal_frame' src='" + source + "'>content</iframe>";
	modalWindow.open();
};