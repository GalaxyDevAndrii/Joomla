<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
jimport('joomla.utilities.date');

$project = $this->project;
$teachers = $this->teachers;
$courses = $this->courses;
$format = "%Y-%m-%d %H:%M:%S";
//echo'<pre>';print_r($this);echo'</pre>';die;
$doc = JFactory::getDocument();
$doc->addScript(JURI::root().'components/com_guru/js/fileuploader.js');
$doc->addStyleSheet(JURI::root().'components/com_guru/css/fileuploader.css');

$tmpl = JFactory::getApplication()->input->get("tmpl", "", "raw");

	if($tmpl == "component"){
?>
		<div class="modal-save-area">
			<button onclick="Joomla.submitbutton('save');" class="btn btn-small button-save modal-save-button" style="background-color: #87B87F !important; border-color: #87B87F; height: 35px; border-radius: 5px !important; margin: 10px 20px;">
				<span class="icon-save" aria-hidden="true"></span>
				<?php echo JText::_("GURU_SV_AND_CL"); ?>
			</button>
		</div>
<?php
	}
?>

<style>
	#description_ifr{
		height: 300px !important;
	}

	.admintable td{
		padding: 10px 0px;
	}

	.modal-save-area{
		width: 100%;
		text-align: right;
	}
</style>

<script language="javascript" type="text/javascript">	
	Joomla.submitbutton = function(pressbutton){
		if (pressbutton == 'save' || pressbutton == 'apply'){
			if(document.getElementById('title').value == ""){
	            alert('<?php echo JText::_('GURU_ERR_FILL_TITLE')?>');
	            return false;
	        }

			if(document.getElementById('author-id').value == 0){
	            alert('<?php echo JText::_('GURU_ERR_SELECT_TEACHER')?>');
	            return false;
	        }

			if(document.getElementById('course-id').value == 0){
	            alert('<?php echo JText::_('GURU_ERR_SELECT_COURSE')?>');
	            return false;
	        }

	        if(document.getElementById('file').value == ""){
	            alert('<?php echo JText::_('GURU_ERR_UPLOAD_FILE')?>');
	            return false;
	        }

	        submitform(pressbutton);
		}
		else{ 
			submitform(pressbutton);
		}	
	}

	function changeTeacher(teacher_id){
		ajax_url = "index.php?option=com_guru&controller=guruProjects&task=getTeacherCoursesSelect&tmpl=component";
		var data = {
		   	'teacher_id': teacher_id
		};
		jQuery.post(ajax_url, data, function(response) {
			document.getElementById("courses-area").innerHTML = response;
		});
	}
</script>	

<form method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<tr>
				<td width="15%">
					<?php echo JText::_("GURU_TITLE"); ?><span class="error" style="color:#FF0000;">*</span>
				</td>
				<td>
					<input type="text" id="title" name="title" value="<?php echo $project["title"]; ?>" />
				</td>
			</tr>
			
			<tr>
				<td width="15%" valign="top">
					<?php echo JText::_("GURU_AUTHOR"); ?><span class="error" style="color:#FF0000;">*</span>
				</td>
				<td>
					<select id="author-id" name="author_id" onchange="javascript:changeTeacher(this.value);">
						<option value="0"> <?php echo JText::_("GURU_SELECT_TEACHER"); ?> </option>
					<?php
						if(isset($teachers) && count($teachers) > 0){
							foreach($teachers as $key=>$value){
								$selected = "";

								if($project["author_id"] == $value["id"]){
									$selected = 'selected="selected"';
								}
					?>
								<option value="<?php echo $value["id"]; ?>" <?php echo $selected; ?> > <?php echo $value["name"]; ?> </option>
					<?php
							}
						}
					?>
					</select>
				</td>
			</tr>

			<tr>
				<td width="15%" valign="top">
					<?php echo JText::_("GURU_COURSE"); ?><span class="error" style="color:#FF0000;">*</span>
				</td>
				<td id="courses-area">
					<?php
						if($project["id"] > 0){
					?>
							<select id="course-id" name="course_id">
								<option value="0"> <?php echo JText::_("GURU_SELECT_COURSE"); ?> </option>
							<?php
								if(isset($courses) && count($courses) > 0){
									foreach($courses as $key=>$value){
										$selected = "";

										if($project["course_id"] == $value["id"]){
											$selected = 'selected="selected"';
										}
							?>
										<option value="<?php echo $value["id"]; ?>" <?php echo $selected; ?> > <?php echo $value["name"]; ?> </option>
							<?php
									}
								}
							?>
							</select>
					<?php
						}
						else{
							echo '<div class="alert alert-info">'.JText::_("GURU_SELECT_TEACHER").'</div>';
						}
					?>
				</td>
			</tr>

			<tr>
				<td width="15%" valign="top">
					<?php echo JText::_("GURU_DESCRIPTION"); ?>
				</td>
				<td>
					<?php
						//$editor = JFactory::getEditor();
						$editor  = new JEditor(JFactory::getConfig()->get("editor"));
						echo $editor->display('description', $project["description"], '100%', '200px', '10', '50');
					?>
				</td>
			</tr>

			<tr>
				<td width="15%">
					<?php echo JText::_("GURU_FILE"); ?><span class="error" style="color:#FF0000;">*</span>
				</td>
				<td>
					<div style="float:left;">
                        <div id="fileUploader"></div>
                    </div>
                    <input type="hidden" name="file" id="file" value="<?php echo $project["file"]; ?>" />
				</td>
			</tr>

			<tr>
				<td width="15%">
					<?php echo JText::_("GURU_PUBL"); ?>
				</td>
				<td>
					<input type="checkbox" name="published" value="1" <?php if($project["published"] == '1'){ echo 'checked';} ?> /><span class="lbl"></span>
				</td>
			</tr>

			<tr>
				<td width="15%">
					<?php echo JText::_("GURU_PRODLSPUB"); ?>
				</td>
				<td>
					<?php echo JHTML::calendar($project["start"], 'start', 'start', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); ?>
				</td>
			</tr>

			<tr>
				<td width="15%">
					<?php echo JText::_("GURU_PRODLEPUB"); ?>
				</td>
				<td>
					<?php echo JHTML::calendar($project["end"], 'end', 'end', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); ?>
				</td>
			</tr>

		</table>
	</fieldset>

	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="id" value="<?php echo $project["id"]; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="guruProjects" />
	<?php
		if($tmpl == "component"){
			echo '<input type="hidden" name="save_from_lesson" value="1" />';
		}
	?>
</form>

<script>
	jQuery(function(){
        function createUploader(){
            var uploader = new qq.FileUploader({
                element: document.getElementById('fileUploader'),
                action: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image',
                params:{
                    folder:'courses',
                    mediaType:'image',
                    size: 10,
                    type: ''
                },
                onSubmit: function(id,fileName){
                    jQuery('.qq-upload-list li').css('display','none');
                },
                onComplete: function(id,fileName,responseJSON){
                    if(responseJSON.success == true){
                        jQuery('.qq-upload-success').append('- <span style="color:#387C44;"><?php echo JText::_('GURU_UPLOAD_SUCCESS')?></span>');
                        if(responseJSON.locate) {
                            //jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName);
                            jQuery('#view_imagelist23').attr("src", '<?php echo JURI::root()?>'+responseJSON.locate +'/'+ fileName+'?timestamp=' + new Date().getTime());
                            jQuery('#file').val(responseJSON.locate +'/'+ fileName);
                        }
                    }
                },
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF', 'xls', 'XLS'],
                sizeLimit: '10M',
                multiple: false,
                maxConnections: 1
            });
        }
        createUploader();
    });
</script>