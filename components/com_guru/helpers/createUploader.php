<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
	$upload_mb 		= guruHelper::getUploadMaxSize();
	$upload_mb*=1048576; //transform in bytes
	$doc = JFactory::getDocument();
	
	//start images
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){            
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'imageUploader\'),
					action: \''.JURI::root().'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
					params:{
						folder:\'media\',
						mediaType:\'image\',
						size: jQuery(\'#media_fullpx\').value,
						type: jQuery(\'#media_prop\').value
					},
					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						//alert(responseJSON.success);
						//alert(\'id: \'+ id + \'; filename:\' + fileName);
						//if(responseJSON.success == true){
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							//if(responseJSON.locate) {
								//jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName);
								jQuery(\'#view_imagelist23\').attr("src", "'.JURI::root().'"+responseJSON.locate +"/"+ fileName);
								jQuery(\'#image\').val("/"+ fileName);
								jQuery(\'#is_image\').val("1");
							//}
						//}
					},
					allowedExtensions: [\'jpg\', \'jpeg\', \'png\', \'gif\', \'JPG\', \'JPEG\', \'PNG\', \'GIF\', \'xls\', \'XLS\'],
					sizeLimit: '.$upload_mb.',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');
	//end images
	
	//start video
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){            
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'videoUploader\'),
					action: \''.JURI::root().'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
					params:{
						mediaType:\'video\'
					},
					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');							
						jQuery(\'#localfile\').append("<option value=\'"+responseJSON.locate +"/"+ fileName+"\' selected=\'selected\'>"+ fileName+"</option>");
						jQuery(\'#source_local_v2\').val(\'local\');
						jQuery(\'#source_local_v2\').attr("checked", "checked");
						jQuery(\'#source_code_v\').attr("checked", "");
						jQuery(\'#source_url_v\').attr("checked", "");
						
						document.getElementById("source_local_v2").checked = true;
					},
					allowedExtensions: [\'ax\', \'amv\', \'asf\', \'gif\', \'avi\', \'mp4\', \'fla\', \'swf\', \'mov\' , \'mpg\', \'mpeg\', \'rm\', \'xls\', \'XLS\'],
					sizeLimit: '.$upload_mb.',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');
	//end video
	
	//start audio
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){            
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'audioUploader\'),
					action: \''.JURI::root().'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
					params:{
						mediaType:\'audio\'
					},
					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						//alert(responseJSON.success);
						//alert(\'id: \'+ id + \'; filename:\' + fileName);
						//if(responseJSON.success == true){
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							//if(responseJSON.locate) {
								jQuery(\'#localfile_a\').append("<option value=\'"+ fileName+"\' selected=\'selected\'>"+ fileName+"</option>");
								jQuery(\'#source_local_a2\').val(\'local\');
								jQuery(\'#source_local_a2\').attr("checked", "checked");
							//}
						//}
					},
					allowedExtensions: [\'aac\', \'aob\', \'ada\', \'au\', \'mid\', \'midi\', \'mp3\', \'ogg\', \'wav\', \'m4a\', \'xls\', \'XLS\'],
					sizeLimit: '.$upload_mb.',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');
	//end audio
	
	//start docs
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){            
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'docUploader\'),
					action: \''.JURI::root().'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
					params:{
						mediaType:\'doc\'
					},
					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						//alert(responseJSON.success);
						//alert(\'id: \'+ id + \'; filename:\' + fileName);
						//if(responseJSON.success == true){
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							//if(responseJSON.locate) {
								jQuery(\'#localfile_d\').append("<option value=\'"+ fileName+"\' selected=\'selected\'>"+ fileName+"</option>");
								jQuery(\'#source_local_d2\').val(\'local\');
								jQuery(\'#source_local_d2\').attr("checked", "checked");
							//}
						//}
					},
					allowedExtensions: [\'doc\', \'docx\', \'txt\', \'pdf\', \'csv\', \'htm\', \'html\', \'xhtml\', \'xml\', \'sxw\', \'rtf\', \'odt\', \'css\', \'odp\', \'pps\', \'ppt\', \'pptx\', \'sxi\', \'xls\', \'XLS\'],
					sizeLimit: '.$upload_mb.',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');
	//end docs
	
	//start file
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){            
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'fileUploader\'),
					action: \''.JURI::root().'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image\',
					params:{
						mediaType:\'file\'
					},
					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						//alert(responseJSON.success);
						//alert(\'id: \'+ id + \'; filename:\' + fileName);
						//if(responseJSON.success == true){
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							//if(responseJSON.locate) {
								jQuery(\'#localfile_f\').append("<option value=\'"+ fileName+"\' selected=\'selected\'>"+ fileName+"</option>");
								jQuery(\'#source_local_f2\').val(\'local\');
								jQuery(\'#source_local_f2\').attr("checked", "checked");
							//}
						//}
					},
					allowedExtensions: [\'exe\', \'zip\', \'xls\', \'XLS\'],
					sizeLimit: '.$upload_mb.',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');
	//end file
	
	
	
	//$doc->addScript('components/com_guru/js/fileuploader.js');
	JHtml::stylesheet('components/com_guru/css/fileuploader.css');
	
	?>

