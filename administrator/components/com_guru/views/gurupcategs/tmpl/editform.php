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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');

	$categ = $this->categ;
	$lists = $this->lists;
	$isNew = $this->isNew;
	$nullDate = 0;
	//$editorul  = JFactory::getEditor();
	$editorul  = new JEditor(JFactory::getConfig()->get("editor"));
	JHTML::_('behavior.combobox');
	$config = guruAdminModelguruPcateg::getConfigs();	

	$upload_mb = guruAdminHelper::getUploadMaxSize();
	$upload_mb*=1048576; //transform in bytes
	$doc =JFactory::getDocument();
	
	$cat_config = json_decode($config->ctgpage);
	$cat_t_prop = $cat_config->ctg_image_size_type == "0" ? "width" : "heigth";	
	
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){  
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'fileUploader\'),
					action: \''.JURI::root().'administrator/index.php?option=com_guru&controller=guruConfigs&tmpl=component&format=raw&task=guru_file_uploader\',
					params:{
						folder:\'categories\',
						mediaType:\'image\',
						size: \''.$cat_config->ctg_image_size.'\',
						type: \''.$cat_t_prop.'\'
					},
					onSubmit: function(id,fileName,folder){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						//alert(responseJSON.success);
						//alert(\'id: \'+ id + \'; filename:\' + fileName);
						if(responseJSON.success == true){
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							if(responseJSON.locate) {
								jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName);
								jQuery(\'#image\').val(responseJSON.locate +"/"+ fileName);
								jQuery(\'#is_image\').val("1");
							}
						}
					},
					allowedExtensions: [\'jpg\', \'jpeg\', \'png\', \'gif\', \'JPG\', \'JPEG\', \'PNG\', \'GIF\', \'xls\', \'XLS\'],
					sizeLimit: \''.$upload_mb.'\',
					multiple: false,
					maxConnections: 1
				});           
			}
			createUploader();
		});
	');
	$doc->addScript('components/com_guru/js/fileuploader.js');
	$doc->addStyleSheet('components/com_guru/css/fileuploader.css');
	
?>

	<script language="javascript" type="text/javascript">
		
	
	Joomla.submitbutton = function(pressbutton){
		var form = document.adminForm;
		
		if (pressbutton == 'save' || pressbutton == 'apply') {
			if(form.name.value == ""){
				alert( "<?php echo JText::_('GURU_INSERT_CATEG_NAME'); ?>" );
				return false;
			}			
			else{
				//submitform( pressbutton );
				form.task.value = pressbutton;
				form.submit();
			}
		}
		else {
			//submitform( pressbutton );
			form.task.value = pressbutton;
			form.submit();
		}
	}
	
	function deleteImage(id){
		var url = 'index.php?option=com_guru&controller=guruPcategs&tmpl=component&format=raw&task=delete_categ_image&id='+id;
		var myAjax = jQuery.ajax({
			method: 'get',
			asynchronous: 'true',
			url: url,
			data: { 'do' : '1' },
			success: function() {
					document.getElementById('view_imagelist23').src="components/com_guru/images/blank.png";
					document.getElementById('deletebtn').style.display="none";
					document.getElementById('img_name').value="";
					document.getElementById('image').value="";
			},
					
		});	
		return true;	
	}
	</script>
<div>
 <form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<fieldset class="adminform">
	<table class="adminform" border="0">
	<tr>
		<td width="100" align="left">
		<?php echo JText::_('GURU_CATEGNAME');?>:<font color="#ff0000">*</font>
		</td>
		<td>
		<input class="inputbox" type="text" name="name" size="35" value="<?php echo $categ->name;?>" />
		<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGNAME"); ?>" >
			<img border="0" src="components/com_guru/images/icons/tooltip.png">
		</span>
		</td>
	</tr>
	<tr>
		<td width="100" align="left">
			<?php echo JText::_('GURU_ALIAS');?>:
		</td>
		<td>
			<input class="inputbox" type="text" name="alias" size="35" value="<?php echo $categ->alias;?>" />
			<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEG_ALIAS"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
		</td>
	</tr>
	<tr>
		<td width="100" align="left">
		<?php echo JText::_('GURU_CATEGPARENT');?>:
		</td>
		<td>
		<?php $lists['treecateg']=$this->list_all("parentcategory_id", $categ->id, $categ->id); ?>
		<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_CATEGPARENT"); ?>" >
			<img border="0" src="components/com_guru/images/icons/tooltip.png">
		</span>
		</td>
	</tr>

	<tr>
		<td width="100" align="left">
			<?php echo JText::_('GURU_LANGUAGE');?>:
		</td>
		<td>
			<?php
				$languages = JLanguageHelper::getLanguages();
			?>
				<select name="language">
					<option value="*"> <?php echo JText::_("GURU_ALL"); ?> </option>
			<?php
				if(isset($languages) && count($languages) > 0){
					foreach ($languages as $key => $lang) {
						$selected = "";

						if($categ->language == $lang->sef){
							$selected = 'selected="selected"';
						}

						echo '<option value="'.$lang->sef.'" '.$selected.'> '.$lang->title.' </option>';
					}
				}
			?>
				</select>

			<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COURSE_CATEGPARENT"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
		</td>
	</tr>

	<tr>
		<td width="100" align="left">
			<?php echo JText::_('GURU_ACL');?>:
		</td>
		<td id="members-list">
			<style>
                #members-list input[type="checkbox"], #members-list input[type="radio"]{
                	opacity: 1;
                }
            </style>
			<?php
	            $groups = array();

	            if(isset($categ->groups) && trim($categ->groups) != ""){
	            	$groups = json_decode(trim($categ->groups), true);
	            }

	            echo JHtml::_('access.usergroups', 'groups', $groups, true);
            ?>

            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ACL_TIP"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
		</td>
	</tr>

	<tr>
		<td width="100" align="left"><?php echo JText::_("Icon"); ?>:</td>
		<td>
			<!-- icon picker -->
			<input class="form-control icp icp-auto" value="<?php echo $categ->icon; ?>" type="text" name="icon" /> &nbsp;
			<span style="display:inline-block;position:relative"><i class="picker-target fa-2x fa <?php echo $categ->icon; ?>" style="position:absolute;left:0;bottom:-9px"></i></span>

			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
			<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.css">

			<style>.bg-primary{ background-color: #337ab7; color: #ffffff; }</style>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/js/fontawesome-iconpicker.js"></script>
			<script>
				jQuery(function( $ ) {
					jQuery('.icp-auto').iconpicker().on('iconpickerSelected', function(e) {
						jQuery('.picker-target').get(0).className = 'picker-target fa-2x ' +
							e.iconpickerInstance.options.iconBaseClass + ' ' +
							e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue);
					});
				});
			</script>
		</td>
	</tr>
	<tr>
		<td width="100" align="left">
		<?php echo JText::_('GURU_CATEGDESC');?>:
		</td>
		<td>
		<?php 
		echo $editorul->display( 'description', ''.stripslashes($categ->description),'100%', '300px', '20', '60' );
		?>
		</td>
		<td>
			<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGDESC"); ?>" >
				<img border="0" src="components/com_guru/images/icons/tooltip.png">
			</span>
		</td>
	</tr>
	<tr>
		<td width="100" align="left">
		<?php echo JText::_('GURU_CATEGIMG');?>:
		</td>
		<td>
        	<div class="span12">
            	<div class="span2">
                	<div id="fileUploader"></div>
                </div>
                <div class="span10">
                	&nbsp;&nbsp;
					<?php echo JText::_("GURU_RECOMMENDED_SIZE"); ?>
                    <div>
                        &nbsp;&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CATEGIMG"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="image" id="image" value="<?php echo $categ->image; ?>" />&nbsp;
		</td>
	</tr>
	<tr>
		<td width="100" align="left">
		<?php echo JText::_('GURU_CATEG_CURIMG');?>:
		</td>
		<td>
			<?php
				if(isset($categ->image) && $categ->image!=""){ 	?>
					<img id="view_imagelist23" name="view_imagelist" src='../<?php echo $categ->image;?>'/>
				<br />
				<input type="button" class="btn"  style="margin-top:20px;" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="deleteImage('<?php echo $categ->id; ?>');" id="deletebtn"/>
				<input type="hidden" value="<?php echo $categ->image; ?>" name="img_name" id="img_name" />		
			<?php 
			} else {
					echo "<img id='view_imagelist23' name='view_imagelist' src='components/com_guru/images/blank.png'/>";
			}?>
		</td>
	</tr>
	</table>
		<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="" />
		
		<input type="hidden" name="id" value="<?php echo $categ->id; ?>" />
		<input type="hidden" name="controller" value="guruPcategs" />
</form>		
</fieldset>