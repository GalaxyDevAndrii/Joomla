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

require_once (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'custom_fields.php');

?>
<style>.alert-info { height:auto!important;}</style>
<script language="javascript" type="text/javascript">		

	Joomla.submitbutton = function(pressbutton){
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			//submitform( pressbutton );
			//return;

			form.task.value = pressbutton;
			form.submit();
		}
		
		if(eval(form.this_form) && form.this_form.value == "true"){
			//var isNew = getUrlVars()["author_type"];
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
			var x = new RegExp("[\<|\>|\"]","i");
			var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,13}(?:\.[a-z]{2})?)$/i
			
			can_change = checkIfCanChnageCommission();
			
			if(can_change == '000'){
				alert("<?php echo JText::_("GURU_CAN_NOT_CHANGE_COMMISSION"); ?>");
				return false;
			}
			
			if (form.name.value == "") {
				alert("<?php echo JText::_("GURU_PROVIDE_NAME"); ?>");
				return false;
			} 
		
			else if (x.exec(form.name.value)){
				alert ("<?php echo JText::_("GURU_INVALID_CHARACTERS_AUTHOR_NAME"); ?>");
				return false;
			}
			else if (form.username.value == "") {
				alert("<?php echo JText::_("GURU_PROVIDE_USER_LOGIN_NAME"); ?>");
				return false;
			} 
			else if (form.username.value.length < 3) {
				alert("<?php echo JText::_("GURU_INVALID_CHARACTERS_LOG_NAME"); ?>");
				return false;
			} 
			else if (form.email.value == "") {
				alert("<?php echo JText::_("GURU_PROVIDE_EMAIL_ADDRESS"); ?>");
				return false;
			} 
			else if (!filter.test(form.email.value)){
				alert ("<?php echo JText::_("GURU_PROVIDE_VALID_EMAIL"); ?>");
				return false;
			}
			else if (form.gid.value == "") {
				alert("<?php echo JText::_("GURU_ASSIGN_USER_TO_GROUP"); ?>");
				return false;
			} 
			else if (form.gid.value == "29") {		
				alert("<?php echo JText::_("GURU_NOT_PUBLIC_FRONTEND"); ?>");
				return false;
			} 
			else if (form.gid.value == "30") {
				alert("<?php echo JText::_("GURU_NOT_PUBLIC_BACKEND"); ?>");
				return false;
			}
			if(form.website.value.substring(0, 7)!="http://" && form.website.value.substring(0, 8)!="https://"){
				alert("Website must begin with http:// or https://"); 
				return false;
			} 
			if(form.blog.value.substring(0, 7)!="http://" && form.blog.value.substring(0, 8)!="https://"){
				alert("Blog URL must begin with http:// or https://"); 
				return false;
			} 
			if (form.facebook.value.substring(0, 7)!="http://" && form.facebook.value.substring(0, 8)!="https://"){
				alert("Facebook page URL must begin with http:// or https://"); 
				return false;
			}
			else {
				if (document.getElementById("password") != null){
					if (form.password.value != "" && form.password2.value != "" && form.password.value != form.password2.value){
						alert("<?php echo JText::_("GURU_PASSWORD_NOT_MATCH"); ?>");
						return false;
					}
					if(form.id.value == 0 || form.id.value == ""){
						if(form.password.value == "" || form.password2.value == ""){
							alert("<?php echo JText::_("GURU_PASSWORD_REQUIRED"); ?>");
							return false;
						}
						else{ 
							checkGuruExistingUser();
							setTimeout(submitIFOk(pressbutton), 1000);
						}
					} 
				} 
				else {
					//submitform( pressbutton );
					if(!validateCustomFields()){
						return false;
					}

					form.task.value = pressbutton;
					form.submit();
				}
			}			
		}
		else{
			//submitform( pressbutton );
			if(!validateCustomFields()){
				return false;
			}

			form.task.value = pressbutton;
			form.submit();
		}
	}

	function validateCustomFields(){
		var is_ok = true;

		jQuery(".custom_fields_required").each(function(){
			required_text = "<?php echo JText::_("GURU_REQUIRED_CUSTOM_FIELD"); ?>";
			not_valid_url_text = "<?php echo JText::_("GURU_NOT_VALID_URL"); ?>";

			field_type = jQuery(this).attr("data-type");
			field_id = jQuery(this).attr("data-id");
			field_name = jQuery(this).attr("data-name");

			if(field_type == "text"){
				element_val = jQuery('[name="fields['+field_id+']"]').val();
				
				if(element_val == ""){
					alert(field_name + " " + required_text);
					is_ok = false;
					return false;
				}
			}
			else if(field_type == "textarea"){
				element_val = jQuery('[name="fields['+field_id+']"]').val();
				
				if(element_val == ""){
					alert(field_name + " " + required_text);
					is_ok = false;
					return false;
				}
			}
			else if(field_type == "radio"){
				element_val = jQuery('[name="fields['+field_id+'][]"]:checked').val();
				
				if(typeof element_val == 'undefined'){
					alert(field_name + " " + required_text);
					is_ok = false;
					return false;
				}
			}
			else if(field_type == "checkbox"){
				element_val = jQuery('[name="fields['+field_id+'][]"]:checked').val();
				
				if(typeof element_val == 'undefined'){
					alert(field_name + " " + required_text);
					is_ok = false;
					return false;
				}
			}
			else if(field_type == "url"){
				element_val = jQuery('[name="fields['+field_id+'][url]"]').val();
				
				if(element_val == ""){
					alert(field_name + " " + required_text);
					is_ok = false;
					return false;
				}
				else{
					prot = jQuery('[name="fields['+field_id+'][prot]"]').val();
					url_validate = /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i;
					
					if(!url_validate.test(prot+element_val)){
   						alert(field_name + " " + not_valid_url_text);
   						is_ok = false;
   						return false;
					}
				}
			}
		});

		return is_ok;
	}
	
	function submitIFOk(pressbutton){
		check_return = document.getElementById("ajax_response").innerHTML;
		if(check_return != 0){
			if(trimString(check_return) == '111'){// not validate email
				alert("<?php echo JText::_("GURU_EMAIL_IN_USE"); ?>");
				return false;
			}
			else if(trimString(check_return) == '222'){// not validate username
				alert("<?php echo JText::_("GURU_USERNAME_IN_USE"); ?>");
				return false;
			}
		}
		else{
			document.adminForm.task.value = pressbutton;
			document.adminForm.submit();
		}
	}
	
	function checkGuruExistingUser(){
		username = document.getElementById("username").value;
		email = document.getElementById("email").value;
		
		if(username != ""){
			htmlvalue = "0";
			/*var req = jQuery.ajax({
				async: false,
				method: 'get',
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo @$user->id; ?>',
				data: { 'do' : '1' },
				onComplete: function(response){
					document.getElementById("ajax_response").empty().adopt(response);
				}
			})*/

			jQuery.ajax({
				async: false,
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo @$user->id; ?>',
				success: function(response) {
		            jQuery("#ajax_response").empty().html(response);
				}
		    });
		}

		if(email != ""){
			htmlvalue = "0";
			/*var req = jQuery.ajax({
				async: false,
				method: 'get',
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo @$user->id; ?>',
				data: { 'do' : '1' },
				onComplete: function(response){
					document.getElementById("ajax_response").empty().adopt(response);
				}
			})*/

			jQuery.ajax({
				async: false,
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo @$user->id; ?>',
				success: function(response) {
		            jQuery("#ajax_response").empty().html(response);
				}
		    });
		}
		
		check_return = document.getElementById("ajax_response").innerHTML;
	}
	
	function checkIfCanChnageCommission(){
		id = document.adminForm.id.value;
		original_commission = document.getElementById("original_commission").value;
		selected_commission = document.getElementById("commission_plan").value;
		
		if(original_commission != selected_commission && original_commission != 0){
			/*var req = jQuery.ajax({
				async: false,
				method: 'get',
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkCommissionPlan&id='+id+'&new_plan='+selected_commission+'&old_plan='+original_commission,
				data: { 'do' : '1' },
				onComplete: function(response){
					document.getElementById("ajax_response_commission").empty().adopt(response);
				}
			})*/

			jQuery.ajax({
				async: false,
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkCommissionPlan&id='+id+'&new_plan='+selected_commission+'&old_plan='+original_commission,
				success: function(response) {
		            jQuery("#ajax_response_commission").empty().html(response);
				}
		    });

			return trimString(document.getElementById("ajax_response_commission").innerHTML);
		}
		else{
			return true;
		}
	}
	
	function trimString(str){
		str = str.toString();
		var begin = 0;
		var end = str.length - 1;
		while (begin <= end && str.charCodeAt(begin) < 33) { ++begin; }
		while (end > begin && str.charCodeAt(end) < 33) { --end; }
		return str.substr(begin, end - begin + 1);
	}
		
	function deleteImage(){
		document.getElementById("view_imagelist23").src = "";
		document.getElementById("images").value = "";
	}
	function setModertorg(){
		document.getElementById('forummoderator1').style.display = 'none';
		document.getElementById('forummoderator2').style.display = 'table-row';
	}
	
</script>
<?php
	$user = $this->user;
	$lists = $user->lists;
	
	$config = $this->config;

	$upload_mb = guruAdminHelper::getUploadMaxSize();
	$upload_mb *= 1048576; //transform in bytes
	$doc =JFactory::getDocument();
	
	$config_author = json_decode($config->authorpage);
	$author_t_prop = $config_author->author_image_size_type == "0" ? "width" : "heigth";
	
	$doc->addScriptDeclaration('
		jQuery.noConflict();
		jQuery(function(){
			function createUploader(){
				var uploader = new qq.FileUploader({
					element: document.getElementById(\'fileUploader\'),
					action: \''.JURI::root().'administrator/index.php?option=com_guru&controller=guruConfigs&tmpl=component&format=raw&task=guru_file_uploader\',
					params:{
						folder:\'authors\',
						mediaType:\'image\',
						size: '.$config_author->author_image_size.',
						type: \''.$author_t_prop.'\'
					},
					onSubmit: function(id,fileName){
						jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
					},
					onComplete: function(id,fileName,responseJSON){
						//alert(\'id: \'+ id + \'; filename:\' + fileName);
						if(responseJSON.success == true){						
							jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
							if(responseJSON.locate) {
								jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName+"?timestamp=" + new Date().getTime());
								jQuery(\'#images\').val("/"+responseJSON.locate +"/"+ fileName);
							}
						}
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
	$doc->addScript('components/com_guru/js/fileuploader.js');
	$doc->addStyleSheet('components/com_guru/css/fileuploader.css');
	
	$db = JFactory::getDBO();
	$sql = "SELECT `name` FROM #__guru_program WHERE `author` like '%|".intval(JFactory::getApplication()->input->get("id","0"))."|%' and id IN (SELECT distinct `idcourse` from #__guru_kunena_courseslinkage)";
	
	$db->setQuery($sql);
	$db->execute();
	$coursename = $db->loadAssocList();
	
	$db = JFactory::getDBO();
	$sql = "select count(*) from #__extensions where element='com_kunena'";
	$db->setQuery($sql);
	$db->execute();
	$count_k = $db->loadResult();
	
	$sql = "select `id`, `commission_plan`, `teacher_earnings` from #__guru_commissions ORDER BY `default_commission` DESC";
	$db->setQuery($sql);
	$db->execute();
	$result_commission = $db->loadAssocList();

?>
<div id="g_teacher_details">
<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
     <div class="row-fluid">
         <ul class="nav nav-tabs">
                <li class="nav-item active"><a class="nav-link" href="#general" data-toggle="tab">General</a></li>
                <li class="nav-item"><a class="nav-link" href="#photo" data-toggle="tab">Photo</a></li>
                <li class="nav-item"><a class="nav-link" href="#bio" data-toggle="tab">Bio</a></li>
                <?php if($count_k >0){?><li><a href="#kunena" data-toggle="tab"><?php echo JText::_('GURU_KUNENA_FORUM'); ?></a></li><?php } ?>
         </ul>
		<div class="tab-content" style="overflow: auto;">
            <div class="tab-pane active" id="general">
                <table class="adminform">
                    <tr>
                        <td width="15%">
                            <?php echo JText::_('GURU_AU_AUTHOR_NAME'); ?>:
                            <span style="color:#FF0000">*</span>
                        </td>
                        <td>
                            <input type="text" name="name" value="<?php echo $user->name; ?>" class="inputbox" size="40" />
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_NAME"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_USERNAME'); ?>:
                            <span style="color:#FF0000">*</span>
                        </td>
                        <td>
                            <input type="text" id="username" name="username" <?php if(isset($user->username) && (trim($user->username != ""))){echo 'disabled="disabled"';} ?> value="<?php if(isset($user->username)){echo $user->username;}?>" class="inputbox" size="40" />
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_USERNAME"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_TITLE'); ?>:
                        </td>
                        <td>
                            <input type="text" name="author_title" value="<?php echo $user->author_title; ?>" class="inputbox" size="40" />
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_TITLE"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr> 
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_COMMISSION_PLAN_TH'); ?>:
                        </td>
                        <td>
                             <select id="commission_plan" name="commission_plan" style="float:left !important;" >
								<?php
									if(isset($result_commission) && count($result_commission) > 0){
										foreach($result_commission as $key=>$commission){
											$selected = '';
											if(@$user->commission_id == $commission["id"]){
												$selected = 'selected="selected"';
											}
											echo '<option value="'.$commission["id"].'" '.$selected.'>'.$commission["commission_plan"].' ('.$commission["teacher_earnings"].'%)</option>';
										}
									}
									else{
                                ?>
                                    <option value="commission1"><?php echo "Please add commission plans first";?></option>
                                
									<?php
                                    }
                                    ?>
                            </select>
                            <input type="hidden" id="original_commission" value="<?php echo intval(@$user->commission_id); ?>" />
                            
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_COMMISSION_PLAN"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr> 
                     <tr>
                        <td>
                        	<input type="radio" class="pull-left g_margin_top" name="payment_option" value="0" <?php if(@$user->paypal_option == "0"){echo 'checked="checked"';} ?>/> 
                            <span class="lbl"></span>
                            <?php echo JText::_('GURU_AU_AUTHOR_PAYPAL_EMAIL'); ?>:
                        </td>
                        <td>
                            <input type="text" id="paypal_email" name="paypal_email" value="<?php echo @$user->paypal_email; ?>" class="inputbox" size="40" />
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_EMAIL2"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                        	<input type="radio" class="pull-left g_margin_top" name="payment_option" value="1" <?php if(@$user->paypal_option == "1"){echo 'checked="checked"';} ?>/> 
                            <span class="lbl"></span>
                            <?php echo JText::_('GURU_AU_AUTHOR_PAYPAL_OTHER_INFORMATIONS'); ?>:
                        </td>
                        <td>
                        	<textarea name="paypal_other_information"><?php echo @$user->paypal_other_information; ?></textarea>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_OTHER_INFORMATION"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                                         
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_EMAIL'); ?>:
                            <span style="color:#FF0000">*</span>
                        </td>
                        <td>
                            <input type="text" id="email" name="email" <?php if(trim($user->email != "")){echo 'disabled="disabled"';} ?> value="<?php echo $user->email; ?>" class="inputbox" size="40" />
                            <?php echo $lists['show_email']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_EMAIL"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>           
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_WEBSITE'); ?>:
                        </td>
                        <td>
                            <input type="text" name="website" value="<?php echo $user->website; ?>" class="inputbox" size="40" />
                            <?php echo $lists['show_website']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_WEBSITE"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>           
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_BLOG'); ?>:
                        </td>
                        <td>
                            <input type="text" name="blog" value="<?php echo $user->blog; ?>" class="inputbox" size="40" />
                            <?php echo $lists['show_blog']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_BLOG"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>            
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_FACEBOOK'); ?>:
                        </td>
                        <td>
                            <input type="text" name="facebook" value="<?php echo $user->facebook; ?>" class="inputbox" size="40" />
                            <?php echo $lists['show_facebook']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_FACEBOOK"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>           
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_TWITTER'); ?>:
                        </td>
                        <td>
                            <input type="text" name="twitter" value="<?php echo $user->twitter; ?>" class="inputbox" size="40" />
                            <?php echo $lists['show_twitter']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_TWITTER"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    <?php
						if($user->type == 0){
					?>
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_PASSWORD'); ?>:
                            <span style="color:#FF0000">*</span>
                            <br /> <?php if (intval($user->id)>0  || $user->type!=0) echo '<i> Fill blank if no changes </i>'; ?>							
                        </td>
                        <td>
                            <input id="password" type="password" name="password" size="40" value="" autocomplete="off" class="inputbox" size="40" />
                        </td>
                    </tr> 
                    <tr>
                        <td>
                            <?php echo JText::_('GURU_AU_AUTHOR_VERIFY_PASSWORD'); ?>:
                            <span style="color:#FF0000">*</span>
                        </td>
                        <td>
                            <input id="password2" type="password" name="password2" size="40" value="" autocomplete="off" class="inputbox" size="40" />					
                        </td>
                    </tr>	
                    <?php } ?>
                    <tr>
                        <td valign="top">
                            <?php echo JText::_('GURU_AU_AUTHOR_GROUP');?>:
                            <span style="color:#FF0000">*</span>
                        </td>
                        <td>
                            <?php
                                echo $lists['gid'];
                            ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_GROUP"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>								
                        </td>
                    </tr>									
                </table>

                <div style="display: block; clear: both; width: 100%;">
					<style type="text/css">
						label{
							width: 200px;
							margin-top: 5px;
							float: left;
						}

						.uk-form-controls{
							margin-left: 215px;
							margin-top: 5px;
						}

						.uk-text-danger {
						    color: #d85030 !important;
						}

						.custom-label {
						    width: auto;
						    display: inline-block;
						    margin: 0px 15px 0px 5px;
						    vertical-align: middle;
						}

						.uk-form-row{
							display: block;
							width: 100%;
							float: left;
							margin-bottom: 10px;
						}
					</style>

					<?php
						$custom_fields_groups = $this->getCustomFieldsGroups();

						if(isset($custom_fields_groups) && count($custom_fields_groups) > 0){
							$custom_class = new guruCustomFields();

							foreach($custom_fields_groups as $key=>$custom_group){
								$custom_fields = $this->getCustomFields(intval($custom_group["id"]));

								if(isset($custom_fields) && count($custom_fields) > 0){
					?>
									<legend><?php echo trim($custom_group["name"]); ?></legend>
					<?php
									foreach($custom_fields as $key2=>$custom_field){
										echo $custom_class->renderCustomField($custom_field);
									}
								}
							}
						}
					?>
				</div>

               </div>

			   <div class="tab-pane" id="photo">
                    <table width="100%" class="adminform" cellpadding="3" cellspacing="3">
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 25%;" valign="top">
                                <?php echo JText::_('GURU_UPLOAD_IMAGE'); ?>
                            </td>
                            <td>
                                <div id="fileUploader"></div>
                                <input type="hidden" name="images" id="images" value="<?php echo $user->images; ?>" />
                                &nbsp;
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_UPLOAD_IMAGE"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                            </td>
                         </tr>	
                    <?php
                        if(isset($user->images) && $user->images!=""){ 	?>
                        
                            <tr>
                                <td valign='top'>
                                    <?php echo JText::_('GURU_SEL_IMAGE'); ?>
                                </td>
                                <td>
                                    <div id='authorImageSelected'>
                                        <img id="view_imagelist23" name="view_imagelist" src='../<?php echo $user->images;?>'/><br />
                                    </div>
                                    <br />
                                    <input type="button" class="btn" value="<?php echo JText::_('GURU_REMOVE'); ?>" onclick="return deleteImage();"/>
                                    <input type="hidden" value="<?php echo $user->images; ?>" name="img_name" id="img_name" />
                            </td></tr>
                            
                    <?php 
                        } else {
                            echo "<tr><td></td><td><div id='authorImageSelected'><img id='view_imagelist23' name='view_imagelist' src='components/com_guru/images/blank.png'/></div></td></tr>";
                        }
                    ?>
                        </td>
                        </tr>
                    </table>		
       		</div>
          <div class="tab-pane" id="bio">   
			<?php
                //$editor1  = JFactory::getEditor();
				$editor1  = new JEditor(JFactory::getConfig()->get("editor"));
            ?>
            
            <div class="well well-minimized"> <?php echo JText::_('GURU_AUTHOR_BIO_DET');?></div>

            <table class="adminform">
                    <tr>					
                        <td width="100%">
                        <?php
                        // parameters : areaname, content, hidden field, width, height, rows, cols'full_bio',
                        echo $editor1->display( 'full_bio',  $user->full_bio ,  '100%', '300px', '20', '60' ) ; ?>
                        </td>
                        <td valign="top">
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_BIO_DET"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
           </table>
         </div>
		<?php			
			if($count_k >0){
		?>
        	<div class="tab-pane" id="kunena">   	
		<?php		
				$kunenayn = "SELECT forum_kunena_generated FROM #__guru_authors where userid=".intval(JFactory::getApplication()->input->get("id","0"));
				$db->setQuery($kunenayn);
				$db->execute($kunenayn);
				$kunenayn = $db->loadResult();
				
				if($kunenayn == 1){
					$s1 = 'style="display:none;"';
					$s2 = 'style="display:block;"';
				}
				else{
					$s1 = 'style="display:block;"';
					$s2 = 'style="display:none;"';
				}
		?>
            <table class="adminform">
            <tr>
                <td width="15%">
                    <?php echo JText::_('GURU_MODERATOR'); ?>
                </td>
                <td>
                     <div id="forummoderator1" <?php echo $s2; ?>>
                        <input style="background-color:#F7F7F7; height:30px" type="button" name="forumboard" onclick="javascript:setModertorg();"  value="<?php echo JText::_("GURU_MODERATOR2"); ?>" />
                     </div>
                <div id="forummoderator2" <?php echo $s1; ?>>
                <?php 
                if(count($coursename) > 0){
                    for($i=0; $i <= count($coursename); $i++){
						if(isset($coursename[$i])){
                        	echo $coursename[$i]["name"].'<br/>';
						}
                    }
                }
                else{
                    echo  JText::_('GURU_COURSETH_NOT_ASSIGNED'); 
                }
                ?>
                </div>
                </td>
            </tr>
            </table>
          </div>  
		<?php			
		}	
		?>	
      </div>  
      	 <div id="ajax_response" style="display:none;"></div>
         <div id="ajax_response_commission" style="display:none;"></div>

		<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="id" value="<?php echo $user->userid;?>" />
        <input type="hidden" name="userid" value="<?php echo $user->id;?>" />
		<input type="hidden" name="author_type" value="<?php echo $user->type; ?>" />
        <input type="hidden" name="this_form" value="true" />
</form>
</div>