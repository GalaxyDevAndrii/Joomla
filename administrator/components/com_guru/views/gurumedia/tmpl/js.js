function change_radio_code() {
	if(document.adminForm.type.value == 'video')
		adding_ext = '_v';
	if(document.adminForm.type.value == 'audio')
		adding_ext = '_a';
	if(document.adminForm.type.value == 'docs')
		adding_ext = '_d';		
	if(document.adminForm.type.value != 'docs')
	    document.getElementById('source_code'+adding_ext).checked = 'checked';
	document.getElementById('source_url'+adding_ext).checked = '';
	//document.getElementById('source_local'+adding_ext).checked = '';
	document.getElementById('source_local'+adding_ext+'2').checked = '';
}


function change_radio_url() {
	if(document.adminForm.type.value == 'video')
		adding_ext = '_v';
	if(document.adminForm.type.value == 'audio')
		adding_ext = '_a';
	if(document.adminForm.type.value == 'docs')
		adding_ext = '_d';	
	if(document.adminForm.type.value == 'file'){
		document.getElementById('filePreview').innerHTML="<a href='"+document.getElementById('url_f').value+"'>Download</a>";
		adding_ext = '_f';	
	}
	if(document.adminForm.type.value != 'docs' && document.adminForm.type.value != 'file')		
	    document.getElementById('source_code'+adding_ext).checked = '';
	document.getElementById('source_url'+adding_ext).checked = 'checked';
	//document.getElementById('source_local'+adding_ext).checked = '';
	document.getElementById('source_local'+adding_ext+'2').checked = '';
}	

 function doPreview(){
	if(document.getElementById('url_f').value!=""){
 		document.getElementById('filePreview').innerHTML="<a href='"+document.getElementById('url_f').value+"'>Download</a>";
	}
	else{
		document.getElementById('filePreview').innerHTML="";
	}
 }
 
 function change_radio_local() {
	if(document.adminForm.type.value == 'video')
		adding_ext = '_v';
	if(document.adminForm.type.value == 'audio')
		adding_ext = '_a';
	if(document.adminForm.type.value == 'docs')
		adding_ext = '_d';
	if(document.adminForm.type.value == 'file'){
		adding_ext = '_f';
		document.getElementById('filePreviewList').href=document.getElementById('filesFolder').innerHTML+"/"+document.getElementById('localfile_f').value;
		document.getElementById('filePreviewList').style.visibility="visible";
		
	}
	if(document.adminForm.type.value != 'docs' && document.adminForm.type.value != 'file')	
	    document.getElementById('source_code'+adding_ext).checked = '';
	document.getElementById('source_url'+adding_ext).checked = '';
	document.getElementById('source_local'+adding_ext+'2').checked = 'checked';
}

function show_hidden_row() {
	/*if(document.adminForm.type.value == 'video')
		adding_ext = '_v';
	if(document.adminForm.type.value == 'audio')
		adding_ext = '_a';
	if(document.adminForm.type.value == 'docs')
		adding_ext = '_d';
	if(document.adminForm.type.value == 'file')
		adding_ext = '_f';
	if(document.adminForm.type.value != 'docs' && document.adminForm.type.value != 'file')	
	    document.getElementById('source_code'+adding_ext).checked = '';
	document.getElementById('source_url'+adding_ext).checked = '';
	document.getElementById('source_local'+adding_ext+'2').checked = 'checked';	*/
}	
			
function hide_hidden_row() {
	/*document.getElementById('was_uploaded').value = 0;	*/	
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
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('auto_play').style.display="block";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'audio':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="block";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('auto_play').style.display="block";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'docs':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="block";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'url':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="block";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'Article':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="block";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'image':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="block";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'text':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="block";
			document.getElementById('fileblock').style.display="none";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;
		case 'file':
			document.getElementById('videoblock').style.display="none";
			document.getElementById('defaultblock').style.display="none";
			document.getElementById('audioblock').style.display="none";
			document.getElementById('docsblock').style.display="none";
			document.getElementById('urlblock').style.display="none";
			document.getElementById('imageblock').style.display="none";
			document.getElementById('textblock').style.display="none";
			document.getElementById('fileblock').style.display="block";
			document.getElementById('auto_play').style.display="none";
			document.getElementById('artblock').style.display="none";
			document.getElementById('zoomblock').style.display="none";
			break;

			case 'zoom':
				document.getElementById('videoblock').style.display="none";
				document.getElementById('defaultblock').style.display="none";
				document.getElementById('audioblock').style.display="none";
				document.getElementById('docsblock').style.display="none";
				document.getElementById('urlblock').style.display="none";
				document.getElementById('imageblock').style.display="none";
				document.getElementById('textblock').style.display="none";
				document.getElementById('fileblock').style.display="none";
				document.getElementById('auto_play').style.display="none";
				document.getElementById('artblock').style.display="none";
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
		/*var req = jQuery.ajax({
			method: 'get',
			url: site+'index.php?option=com_guru&controller=guruMedia&task=ajax_add_video&url='+encodeURI(video_url),
			data: { 'do' : '1' },
			onComplete: function(response){
				document.getElementById("progress-video-upload").style.display = "none";
				document.getElementById("video_details").empty().adopt(response);
				new_name = document.getElementById("video-name").value;
				
				if(new_name != "" && document.getElementById("name").value == ""){
					document.getElementById("name").value = new_name;
				}
			}
		})*/

		url = site+'index.php?option=com_guru&controller=guruMedia&task=ajax_add_video&url='+encodeURI(video_url);

		jQuery.ajax({url: url,
			method: 'get',
			success: function(response){
				document.getElementById("progress-video-upload").style.display = "none";
				document.getElementById("video_details").innerHTML = response + "";

				new_name = document.getElementById("video-name").value;
				
				if(new_name != "" && document.getElementById("name").value == ""){
					document.getElementById("name").value = new_name;
				}
			}
		});
	}
}

function changeHost(host){
	if(host == "0"){
		document.getElementById("div-playlist").style.display = "none";
		document.getElementById("div-album").style.display = "none";
		document.getElementById("youtube-pagination").style.display = "none";
		document.getElementById("vimeo-pagination").style.display = "none";
		document.getElementById("all-row").style.display = "none";
	}
	else if(host == "1"){ // YouTube
		document.getElementById("div-playlist").style.display = "block";
		document.getElementById("div-album").style.display = "none";
		document.getElementById("youtube-pagination").style.display = "block";
		document.getElementById("vimeo-pagination").style.display = "none";
		document.getElementById("all-row").style.display = "";
	}
	else if(host == "2"){ // Vimeo
		document.getElementById("div-playlist").style.display = "none";
		document.getElementById("div-album").style.display = "block";
		document.getElementById("youtube-pagination").style.display = "none";
		document.getElementById("vimeo-pagination").style.display = "block";
		document.getElementById("all-row").style.display = "";
	}
}

function listMassVideos(site){
	list = "";
	api = "";
	host = document.getElementById("host").value;
	page = "1";
	start_with = "1";
	per_page = "25";
	
	if(host == 1){ // YouTube
		list = document.getElementById("playlist").value;
		api = document.getElementById("apikey").value;
		start_with = document.getElementById("start_with").value;
		per_page = document.getElementById("per_page").value;
	}
	else if(host == 2){// Vimeo
		list = document.getElementById("album").value;
		page = document.getElementById("page").value;
	}
	
	document.getElementById("bowlG").style.display = '';
	
	/*var req = jQuery.ajax({
		method: 'get',
		url: site+'index.php?option=com_guru&controller=guruMedia&task=ajax_add_mass_video&list='+list+'&api='+api+'&host='+host+'&page='+page+'&start_with='+start_with+'&per_page='+per_page+'&tmpl=component',
		data: { 'do' : '1' },
		onComplete: function(response){
			document.getElementById("list-of-videos").empty().adopt(response);
			document.getElementById("bowlG").style.display = 'none';
		}
	})*/

	url = site+'index.php?option=com_guru&controller=guruMedia&task=ajax_add_mass_video&list='+list+'&api='+api+'&host='+host+'&page='+page+'&start_with='+start_with+'&per_page='+per_page+'&tmpl=component';

	jQuery.ajax({
		url: url,
		method: 'get',
		asynchronous: 'true',
		success: function(response){
			/*document.getElementById("list-of-videos").empty().adopt(response);*/
			jQuery("#list-of-videos").html(response);
			document.getElementById("bowlG").style.display = 'none';
		}
	});
}

function changeMassCourse(course_id){
	/*var req = jQuery.ajax({
		method: 'get',
		url: 'index.php?option=com_guru&controller=guruMedia&task=list_of_modules&course_id='+course_id+'&tmpl=component',
		data: { 'do' : '1' },
		onComplete: function(response){
			document.getElementById("div-modules").empty().adopt(response);
		}
	})*/

	url = 'index.php?option=com_guru&controller=guruMedia&task=list_of_modules&course_id='+course_id+'&tmpl=component';

	jQuery.ajax({url: url,
		method: 'get',
		asynchronous: 'false',
		success: function(response){
			//document.getElementById("div-modules").empty().adopt(response);
			jQuery("#div-modules").html(response);
		}
	});
}