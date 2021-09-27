 function doPreview(){
	if(document.getElementById('url_f').value!=""){
 		document.getElementById('filePreview').innerHTML="<a href='"+document.getElementById('url_f').value+"'>Download</a>";
	}
	else{
		document.getElementById('filePreview').innerHTML="";
	}
 }

function on_over_size(id){
	document.getElementById(id).style.background = '#D8E6FB';			
}
		
function on_out_size(id){
	if((id=='ysize1')&&(document.getElementById('width_v').value==480)){
		document.getElementById(id).style.background = '#D8E6FB';
		return true;
	}
	if((id=='ysize2')&&(document.getElementById('width_v').value==560)){
		document.getElementById(id).style.background = '#D8E6FB';
		return true;
	}
	if((id=='ysize3')&&(document.getElementById('width_v').value==630)){
		document.getElementById(id).style.background = '#D8E6FB';
		return true;
	}
	if((id=='ysize4')&&(document.getElementById('width_v').value==853)){
		document.getElementById(id).style.background = '#D8E6FB';
		return true;
	}
	if((id=='ysize5')&&(document.getElementById('width_v').value==212)){
		document.getElementById(id).style.background = '#D8E6FB';
		return true;
	}
	if((id=='ysize6')&&(document.getElementById('width_v').value==320)){
		document.getElementById(id).style.background = '#D8E6FB';
		return true;
	}
	document.getElementById(id).style.background = '#CCCCCC';			
}
		
function changeType(value){
	switch(value){
		case 'video':
			document.getElementById('videoblock').style.display="block";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('auto_play').style.display="block";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'audio':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="block";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('auto_play').style.display="block";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'docs':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="block";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('display_docs').style.display="block";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'url':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="block";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'article':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="block";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'image':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="block";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'text':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="block";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'file':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="block";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;

		case 'zoom':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('display_docs').style.display="none";
			document.getElementById('zoomblock').style.display="block";
			break;
		}
}

function changeVideo(){
	document.getElementById("url_v").value = "";
	document.getElementById("video_details").empty();
	document.getElementById("name").value = "";
}

function addVideoFromUrl(site){
	video_url = document.getElementById("url_v").value;
	if(video_url == "" || video_url == "http://www.youtube.com/watch?v="){
		// do nothing
	}
	else{
		document.getElementById("progress-video-upload").style.display = "block";
		
		jQuery.ajax({
			async: false,
			url: site+'index.php?option=com_guru&controller=guruMedia&task=ajax_add_video&url='+encodeURI(video_url),
			success: function(response) {
	            document.getElementById("progress-video-upload").style.display = "none";
				document.getElementById("video_details").innerHTML = response;
				
				//new_description = document.getElementById("video-description").value;
				//document.getElementById("description").value = new_description;
				
				new_name = document.getElementById("video-name").value;
				
				if(new_name != "" && document.getElementById("name").value == ""){
					document.getElementById("name").value = new_name;
				}
				
				//document.getElementById('media-name').style.display = "block";
			}
	    });
	}
}