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
	JHtml::_('behavior.framework');
	JHtml::_('behavior.modal');
	
	$tab = JFactory::getApplication()->input->get("tab", "0");
	$configs = $this->configs;
	$lists = $configs->lists;
	
	//$editor  = JFactory::getEditor();
	$editor  = new JEditor(JFactory::getConfig()->get("editor"));

	$admins = $this->superadmins;
		
	$doc =JFactory::getDocument();

	//These scripts are already been included from the administrator\components\com_guru\guru.php file
	//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
	//$doc->addScript('components/com_guru/js/freecourse.js');
	
	$template_emails = json_decode($configs->template_emails);

	if(!isset($configs->thousands_separator)){
		$configs->thousands_separator = 0;
	}

	if(!isset($configs->decimals_separator)){
		$configs->decimals_separator = 1;
	}

	if(!isset($configs->show_decimals)){
		$configs->show_decimals = 0;
	}

	require_once JPATH_ADMINISTRATOR.'/components/com_users/helpers/users.php';
?>

<script language="JavaScript" type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/js/colorpicker.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/js/freecourse.js"></script>
<script language="javascript" type="text/javascript">        
	
	function validateColor(color){
		if(color == ''){
			return true
		}
		if(/^[0-9A-F]{6}$/i.test(color)){
			return true;
		}
		return false;
	}
	
	function makeVisibleProfileT(value){
		if(value == 1){
			document.getElementById('gurujomsocialregteachermprof').style.display = "block";	
		}
		else{
			document.getElementById('gurujomsocialregteachermprof').style.display = "none";
		}
	
	}
	
	function makeVisibleProfileS(value){
		if(value == 1){
			document.getElementById('gurujomsocialregstudentmprof').style.display = "block";	
		}
		else{
			document.getElementById('gurujomsocialregstudentmprof').style.display = "none";
		}
	}
	
	function IsValidNumeric(sText){
		var ValidChars = "0123456789.";
		var IsNumber=true;
		var Char;
		for (i = 0; i < sText.length && IsNumber == true; i++) { 
			Char = sText.charAt(i); 
			if (ValidChars.indexOf(Char) == -1)  { IsNumber = false; }
		}
		return IsNumber;
	}
	
	function emailTemplate(value){
		if(value == 1){
			showBlock('course-approve-email');
		}
		else if(value == 2){
			showBlock('course-unapprove-email');
		}
		else if(value == 3){
			showBlock('for-course-email');
		}
		else if(value == 4){
			showBlock('approved-teacher');
		}
		else if(value == 5){
			showBlock('pending-teacher');
		}
		else if(value == 6){
			showBlock('for-teacher-approve');
		}
		else if(value == 7){
			showBlock('for-teacher-registered');
		}
		else if(value == 8){
			showBlock('approved-order');
		}
		else if(value == 9){
			showBlock('checked-quiz-result');
		}
		else if(value == 10){
			showBlock('pending-order');
		}
		else if(value == 11){
			showBlock('feedback-result');
		}
		else if(value == 12){
			showBlock('review-quiz-result');
		}
		else if(value == 13){
			showBlock('new-student-enrolled');
		}
		else if (value == 14){
			showBlock('new-student-enrolled-admin');
		}
		else if(value == 15){
			showBlock('for-student-registered');
		}
		else if(value == 16){
			showBlock('admin-completed-course');
		}
		else if(value == 17){
			showBlock('teacher-completed-course');
		}
		else{
			showBlock();
		}
	}

	function showBlock(id) {
		document.getElementById("course-approve-email").style.display = "none";
		document.getElementById("course-unapprove-email").style.display = "none";
		document.getElementById("for-course-email").style.display = "none";
		document.getElementById("approved-teacher").style.display = "none";
		document.getElementById("pending-teacher").style.display = "none";
		document.getElementById("for-teacher-approve").style.display = "none";
		document.getElementById("for-teacher-registered").style.display = "none";
		document.getElementById("approved-order").style.display = "none";
		document.getElementById("pending-order").style.display = "none";
		document.getElementById("checked-quiz-result").style.display = "none";
		document.getElementById("feedback-result").style.display = "none";
		document.getElementById("review-quiz-result").style.display = "none";
		document.getElementById("new-student-enrolled").style.display = "none";
		document.getElementById("new-student-enrolled-admin").style.display = "none";
		document.getElementById("for-student-registered").style.display = "none";
		document.getElementById("teacher-completed-course").style.display = "none";
		document.getElementById("admin-completed-course").style.display = "none";

		if (id && document.getElementById(id)) {
			document.getElementById(id).style.display = "block";
		}
	}
	
	Joomla.submitbutton = function(pressbutton){
			var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,13}(?:\.[a-z]{2})?)$/i;
			console.log(filter);

		<?php
			$validation_script = "";
			if($tab == "0"){
				$validation_script = '/*if(document.adminForm.default_video_size_width.value != "" && !IsValidNumeric(document.adminForm.default_video_size_width.value)){
									  		alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
											return false;
									  }
									  else if(document.adminForm.default_video_size_width.value == 0){
									  		alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
											return false;
									  }
									  else */

									  if(document.adminForm.default_video_size_height.value != "" && !IsValidNumeric(document.adminForm.default_video_size_height.value)){
									  		alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
											return false;
									  }
									  else if(document.adminForm.default_video_size_height.value == 0){
									  		alert("'.JText::_("GURU_DEFAULT_VIDEO_SIZE_NUMBER").'");
											return false;
									  }

									  var thousands_separator = document.getElementById("thousands-separator").value;
									  var decimals_separator = document.getElementById("decimals-separator").value;

									  if(thousands_separator == decimals_separator){
									  	alert("'.JText::_("GURU_SELECT_DIFFERENT_SEPARATOR").'");
									  	return false;
									  }
									  ';
				echo $validation_script;
			}
			if($tab == "1"){
				$validation_script = 'if(document.adminForm.imagesin.value==""){
									  		alert("'.JText::_("GURU_IMAGE_PATH_MANDATORY").'");
											return false;
									  }
									  else if(document.adminForm.videoin.value==""){
									  		alert("'.JText::_("GURU_VIDEO_PATH_MANDATORY").'");
											return false;
									  }
									  else if(document.adminForm.audioin.value==""){
									  		alert("'.JText::_("GURU_AUDIO_PATH_MANDATORY").'");
											return false;
									  }
									  else if(document.adminForm.docsin.value==""){
									  		alert("'.JText::_("GURU_DOC_PATH_MANDATORY").'");
											return false;
									  }
									  else if(document.adminForm.filesin.value==""){
									  		alert("'.JText::_("GURU_FILE_PATH_MANDATORY").'");
											return false;
									  }
									  else if(document.adminForm.filesin1.value==""){
									  		alert("'.JText::_("GURU_CERT_PATH_MANDATORY").'");
											return false;
									  }';
				echo $validation_script;
			}
			if($tab == "4"){
				
				$validation_script = 'if(!validateColor(document.getElementById("pick_rdonecolorfield").value)){
									  		alert("'.JText::_("GURU_ALERT_INVALID_COLOR").'");
											return false;
									  }
									  else if(!validateColor(document.getElementById("pick_pnotdonecolorfield").value)){
									  		alert("'.JText::_("GURU_ALERT_INVALID_COLOR").'");
											return false;
									  }
									  else if(!validateColor(document.getElementById("pick_stxtcolorfield").value)){
									  		alert("'.JText::_("GURU_ALERT_INVALID_COLOR").'");
											return false;
									  }';
				echo $validation_script;
			}
			if($tab == "5"){
				
				$validation_script = 'if(document.adminForm.fromname.value==""){
									  		alert("'.JText::_("GURU_PROVIDE_NAME").'");
											return false;
									  }
									  else if(document.adminForm.fromemail.value==""){
									  		alert("'.JText::_("GURU_PROVIDE_EMAIL_ADDRESS").'");
											return false;
									  }
									  else if (!filter.test(document.adminForm.fromemail.value)){
											alert ("'.JText::_("GURU_PROVIDE_VALID_EMAIL").'");
											return false;
									  }';
			  echo $validation_script;
			}
		?>
		//submitform( pressbutton );
		document.adminForm.task.value = pressbutton;
		document.adminForm.submit();
	}

	function generateNewKey(){
		var numbers = 16;
		var string = "12ab34cde56fgh78ijklm9nopqr12stuv34wxyz569";
		var string_length = string.length;
		var new_key = "";
		var new_api_url = "";
		var site_url = document.getElementById("site-url").value;

		for(i=0; i<numbers; i++){
			poz = Math.floor(Math.random() * Math.floor(string_length));
			new_key_val = string.charAt(poz);
			new_key += new_key_val;
		}

		document.getElementById("secure-key").value = new_key;
		new_api_url = site_url+"index.php?option=com_guru&controller=guruBuy&task=apiurl&key="+new_key;
		document.getElementById("api-url").innerHTML = new_api_url;
	}
</script>

 <div id="myModal" class="modal-small modal hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>
<style>
	select {
  	 width: 180px!important;
	}

</style>
<form action="<?php echo JUri::root() ?>administrator/index.php" class="form-horizontal" method="post" name="adminForm" id="adminForm">
<div class="">
	<?php
		if($tab == "0"){
	?>
   
    <div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=29999457&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_GENERAL_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
    <div class="well well-minimized">
		<?php echo JText::_("GURU_GENERAL_SETTINGS_DESCRIPTION"); ?>
	</div>
    <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_GENERAL')." ".JText::_('GURU_SETTINGS');?></h5></div>
    <div class="widget-body">
    	<div class="widget-main">	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_CURRENCY');?>:</label>
                                <div class="controls">
                                    <?php echo $lists['currency']; ?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CURRENCY"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_CURRENCY_POSITION');?>:		
                                  </label>
                                  <div class="controls">
                                    <select id="currencypos" name="currencypos">
                                        <option value="0" <?php if($configs->currencypos == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_POS_BEFORE"); ?></option>
                                        <option value="1" <?php if($configs->currencypos == "1"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_POS_AFTER"); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CURRENCY_POSITION"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_THOUSANDS_SEPARATOR');?>:		
                                  </label>
                                  <div class="controls">
                                    <select id="thousands-separator" name="thousands_separator">
                                        <option value="0" <?php if($configs->thousands_separator == "0"){echo 'selected="selected"';} ?>> . (<?php echo JText::_("GURU_POINT"); ?>) </option>
                                        <option value="1" <?php if($configs->thousands_separator == "1"){echo 'selected="selected"';} ?>> , (<?php echo JText::_("GURU_COMMA"); ?>) </option>
                                        <option value="2" <?php if($configs->thousands_separator == "2"){echo 'selected="selected"';} ?>>  &nbsp; (<?php echo JText::_("GURU_SPACE"); ?>) </option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_THOUSANDS_SEPARATOR"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_DECIMALS_SEPARATOR');?>:		
                                  </label>
                                  <div class="controls">
                                    <select id="decimals-separator" name="decimals_separator">
                                        <option value="0" <?php if($configs->decimals_separator == "0"){echo 'selected="selected"';} ?>> . (<?php echo JText::_("GURU_POINT"); ?>) </option>
                                        <option value="1" <?php if($configs->decimals_separator == "1"){echo 'selected="selected"';} ?>> , (<?php echo JText::_("GURU_COMMA"); ?>) </option>
                                        <option value="2" <?php if($configs->decimals_separator == "2"){echo 'selected="selected"';} ?>>  &nbsp; (<?php echo JText::_("GURU_SPACE"); ?>) </option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DECIMALS_SEPARATOR"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                	<label class="control-label" for="curency"><?php echo JText::_('GURU_SHOW_DECIMALS');?>:</label>
                                	<div class="controls">
										<input type="hidden" name="notification" value="1">
										<?php
											$checked = '';
											if($configs->show_decimals == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="show_decimals">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_DECIMALS"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_DATETIME');?>:		
                                  </label>
                                  <div class="controls">
                                    <?php echo $lists['date_format']; ?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DATE_TIME"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_HOUR_FORMAT');?>:		
                                  </label>
                                  <div class="controls">
                                    <?php echo $lists['hour_format']; ?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DATE_TIME"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            	
            <div class="row-fluid" style="display:none;">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_OPEN_STEP');?>:		
                                  </label>
                                  <div class="controls">
                                    <?php echo $lists['target']; ?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_OPEN_STEP"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_INVOICEISSUED_BY');?>	
                                  </label>
                                    <div class="controls">
                                        <textarea name="invoice_issued_by"><?php echo $configs->invoice_issued_by; ?></textarea>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_INVOICEISSUED_BY"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
			<div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_NOTIFICATION');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="notification" value="1">
										<?php
											$checked = '';
											if($configs->notification == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="notification">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_NOTIFICATION"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>		
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_SHOW_BREAD_CRUMBS');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="show_bradcrumbs" value="0">
										<?php
											$checked = '';
											if($configs->show_bradcrumbs == 1){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="show_bradcrumbs">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_GURU_SHOW_BREAD_CRUMBS"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                  			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>		
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_SHOW_POWERD_BY');?>:		
                                  </label>
                                  <div class="controls">
										<input type="hidden" name="show_powerd" value="0">
										<?php
											$checked = '';
											if($configs->show_powerd == 1){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="show_powerd">
										<span class="lbl"></span>
										
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_GURU_SHOW_POWERD"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_IGNORE_IJOOMLASEO');?>:		
                                  </label>
                                  <div class="controls">
										<input type="hidden" name="guru_ignore_ijseo" value="1">
										<?php
											$checked = '';
											if($configs->guru_ignore_ijseo == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="guru_ignore_ijseo">
										<span class="lbl"></span>
									
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IGNORE_IJOOMLASEO"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>               			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
           <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_TURN_OFF_JQUERY');?>:		
                                  </label>
                                  <div class="controls">
										<input type="hidden" name="guru_turnoffjq" value="1">
										<?php
											$checked = '';
											if($configs->guru_turnoffjq == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="guru_turnoffjq">
										<span class="lbl"></span>
									
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TURN_OFF_JQUERY1"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>               			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_TURN_OFF_UIKIT');?>:		
                                  </label>
                                  <div class="controls">
										<input type="hidden" name="guru_turnoffuikit" value="1">
										<?php
											$checked = '';
											if($configs->guru_turnoffuikit == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="guru_turnoffuikit">
										<span class="lbl"></span>
									
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TURN_OFF_UIKIT1"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>               			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_RTL_DIRECTION');?>:		
                                  </label>
                                  <div class="controls">
										<select name="rtl">
                                        	<option value="0" <?php if(intval($configs->rtl) == 0){ echo 'selected="selected"';} ?> > LTR </option>
                                            <option value="1" <?php if(intval($configs->rtl) == 1){ echo 'selected="selected"';} ?> > RTL </option>
                                        </select>
									
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_RTL_DIRECTION_TIP"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>               			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_STUDENTS_DEFAULT_GROUP');?>:		
                                  </label>
                                  <div class="controls">
                                    <?php 
                                    $db = JFactory::getDBO();
                                    $user = JFactory::getUser();
                                    $user_id = $user->id;
                                    
                                    $sql_u = "select group_id from #__user_usergroup_map where user_id=".intval($user_id);
                                    $db->setQuery($sql_u);
                                    $res_user_current = $db->loadResult();
                                    $listgroup = UsersHelper::getGroups();
                        
                                    ?>
                                        <select id="student_group" name="student_group"  class="inputbox" size="10">
	                                        <?php
		                                        /*if($res_user_current == 8){
		                                        	echo JHtml::_('select.options', $listgroup, 'value', 'text', $configs->student_group);
		                                        }
		                                        else{*/
		                                        	/*$groups_select = str_replace("- Super Users", "", JHtml::_('select.options', $listgroup, 'value', 'text', $configs->student_group));
		                                        	$groups_select = str_replace('<option value="8"></option>', 'value="0"', $groups_select);*/

		                                        	$groups_select = JHtml::_('select.options', $listgroup, 'value', 'text', $configs->student_group);
			                                        $groups_select = preg_replace('/option value="8"(.*)\/option/msU', "", $groups_select);
			                                        $groups_select = str_replace("<>", "", $groups_select);

		                                        	echo $groups_select;
		                                        //}
	                                        ?>
                                        </select>

                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENTS_DEFAULT_GROUP2"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>              			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span8">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_DEFAULT_VIDEO_SIZE');?>:		
                                  </label>
                                  <div class="controls">
                                    <?php echo $lists['lesson_default_video_size']; ?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DEFAULT_VIDEO_SIZE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>              			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_INDICATE_QUIZ');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="indicate_quiz" value="0">
						<?php
                            $checked = '';
                            if(isset($configs->indicate_quiz) && $configs->indicate_quiz == 1){
                                $checked = 'checked="checked"';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="indicate_quiz">
						<span class="lbl"></span>
									
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_INDICATE_QUIZ_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>						
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_SHOW_CAPTCHA');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="captcha" value="0">
						<?php
                            $checked = '';
                            if(isset($configs->captcha) && $configs->captcha == 1){
                                $checked = 'checked="checked"';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="captcha">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_SHOW_CAPTCHA_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_AUTO_APPROVE');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="auto_approve" value="0">
						<?php
                            $checked = '';
                            if(isset($configs->auto_approve) && $configs->auto_approve == 1){
                                $checked = 'checked="checked"';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="auto_approve">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_AUTO_APPROVE_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <br /><br />
            <h4><?php echo JText::_("GURU_CERTIFICATE_ICONS"); ?></h4>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_CERTIFICATE_HTML');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="certificate_html" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($configs->certificate_html) && $configs->certificate_html == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="certificate_html">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CERTIFICATE_HTML_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_CERTIFICATE_PDF');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="certificate_pdf" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($configs->certificate_pdf) && $configs->certificate_pdf == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="certificate_pdf">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CERTIFICATE_PDF_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_CERTIFICATE_SHARE');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="certificate_share" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($configs->certificate_share) && $configs->certificate_share == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="certificate_share">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CERTIFICATE_SHARE_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_CERTIFICATE_EMAIL');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="certificate_email" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($configs->certificate_email) && $configs->certificate_email == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="certificate_email">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CERTIFICATE_EMAIL_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <br /><br />
            <h4><?php echo JText::_("GURU_STUDENT_MENU_ITEMS"); ?></h4>

            <?php
            	$student_menus = json_decode($configs->student_menus, true);
            	$student_menu_profile = "1";
            	$student_menu_courses = "1";
            	$student_menu_orders = "1";
            	$student_menu_quizzes = "1";
            	$student_menu_projects = "1";
            	$student_menu_certificates = "1";
            	$student_menu_cart = "1";

            	if(isset($student_menus["student_menu_profile"])){
            		$student_menu_profile = $student_menus["student_menu_profile"];
            	}

            	if(isset($student_menus["student_menu_courses"])){
            		$student_menu_courses = $student_menus["student_menu_courses"];
            	}

            	if(isset($student_menus["student_menu_orders"])){
            		$student_menu_orders = $student_menus["student_menu_orders"];
            	}

            	if(isset($student_menus["student_menu_quizzes"])){
            		$student_menu_quizzes = $student_menus["student_menu_quizzes"];
            	}

            	if(isset($student_menus["student_menu_projects"])){
            		$student_menu_projects = $student_menus["student_menu_projects"];
            	}

            	if(isset($student_menus["student_menu_certificates"])){
            		$student_menu_certificates = $student_menus["student_menu_certificates"];
            	}

            	if(isset($student_menus["student_menu_cart"])){
            		$student_menu_cart = $student_menus["student_menu_cart"];
            	}
            ?>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_PROFILE');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_profile" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_profile) && $student_menu_profile == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_profile">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_PROFILE_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_COURSES');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_courses" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_courses) && $student_menu_courses == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_courses">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_COURSES_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_ORDERS');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_orders" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_orders) && $student_menu_orders == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_orders">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_ORDERS_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_QUIZZES');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_quizzes" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_quizzes) && $student_menu_quizzes == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_quizzes">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_QUIZZES_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_PROJECTS');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_projects" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_projects) && $student_menu_projects == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_projects">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_PROJECTS_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_CERTIFICATES');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_certificates" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_certificates) && $student_menu_certificates == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_certificates">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_CERTIFICATES_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_STUDENT_MENU_CART');?>:		
					</label>
					
                    <div class="controls">
						<input type="hidden" name="student_menu_cart" value="0">
						<?php
							$checked = 'checked="checked"';
                            
                            if(isset($student_menu_cart) && $student_menu_cart == 0){
                                $checked = '';
                            }
                        ?>
						<input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="student_menu_cart">
						<span class="lbl"></span>
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_MENU_CART_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>

            
            <br /><br />
            <h4><?php echo JText::_("GURU_YOUTUBE_INTEGRATION"); ?></h4>
            
            <div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label class="control-label" for="bootstrap_show"><?php echo JText::_('GURU_API_KEY');?>:
                        <br />
                        <a href="http://tiny.cc/youtube-api" target="_blank"> <?php echo JText::_("GURU_INSTRUCTIONS"); ?> </a>
					</label>
					
                    <div class="controls">
						<input type="text" name="youtube_key" value="<?php echo $configs->youtube_key; ?>" />
						<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_YOUTUBE_INTEGRATION_TIP"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>						
            </div>
            
        </div>
      </div>  
    <?php
	}
	elseif($tab == "1"){
    ?>

     <div class="container-fluid">
      <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=29999510&tmpl=component')" href="#">
                <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
            <?php echo JText::_("GURU_MEDIA_VIDEO"); ?>                  
      </a>
	</div>	
    <div class="clearfix"></div>
    <div class="well well-minimized">
		<?php echo JText::_("GURU_MEDIA_SETTINGS_DESCRIPTION"); ?>
	</div>
   <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_FILESTORAGE');?></h5></div>
    <div class="widget-body">
    	<div class="widget-main">	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_IMGIN');?>&nbsp;<span style="color:#FF0000">*</span></label>
                                <div class="controls">
                                    <input type="text" size="32" name="imagesin" value="<?php echo $configs->imagesin;?>" />
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IMGIN"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
             <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_VIDEOIN');?>&nbsp;<span style="color:#FF0000">*</span></label>
                                <div class="controls">
                                    <input type="text" size="32" name="videoin" value="<?php echo $configs->videoin;?>" />
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_VIDEOIN"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_AUDIOIN');?>&nbsp;<span style="color:#FF0000">*</span></label>
                                <div class="controls">
                                    <input type="text" size="32" name="audioin" value="<?php echo $configs->audioin;?>" />
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUDIOIN"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_DOCSIN');?>&nbsp;<span style="color:#FF0000">*</span></label>
                                <div class="controls">
                                    <input type="text" size="32" name="docsin" value="<?php echo $configs->docsin;?>" />
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DOCSIN"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_FILESIN');?>&nbsp;<span style="color:#FF0000">*</span></label>
                                <div class="controls">
                                   <input type="text" size="32" name="filesin" value="<?php echo $configs->filesin;?>" />
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FILESIN"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_BACKGROUND_STORAGE');?>&nbsp;<span style="color:#FF0000">*</span></label>
                                <div class="controls">
                                   <input type="text" size="32" name="filesin1" value="<?php echo $configs->imagesin."/certificates";?>" disabled />
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_BACKGROUND_STORAGE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>			
         </div>
       </div>     
    <?php 
	}
	elseif($tab == "2"){
	?>
	 <div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=29999504&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_LAYOUT_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
    <div class="well well-minimized">
		<?php echo JText::_("GURU_LAYOUT_SETTINGS_DESCRIPTION"); ?>
	</div>
	<div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_LAYOUT_TITLE');?></h5></div>
    	<div class="widget-body">
    		<div class="widget-main">
	
			<?php
                require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."guruconfigs".DIRECTORY_SEPARATOR."tmpl".DIRECTORY_SEPARATOR."layout.php"); 
            ?>
            </div>
         </div>   
        <?php
	}				
	elseif($tab == "4"){
		die();
	?>
    <script language="javascript" type="text/javascript">        
	
	function validateColor(color){
		if(color == ''){
			return true
		}
		if(/^[0-9A-F]{6}$/i.test(color)){
			return true;
		}
		return false;
	}
	
	Joomla.submitbutton = function(pressbutton){
		<?php
			$validation_script = "";
			if($tab == "4"){
				
				$validation_script = 'if(!validateColor(document.getElementById("pick_rdonecolorfield").value)){
									  		alert("'.JText::_("GURU_ALERT_INVALID_COLOR").'");
											return false;
									  }
									  else if(!validateColor(document.getElementById("pick_pnotdonecolorfield").value)){
									  		alert("'.JText::_("GURU_ALERT_INVALID_COLOR").'");
											return false;
									  }
									  else if(!validateColor(document.getElementById("pick_stxtcolorfield").value)){
									  		alert("'.JText::_("GURU_ALERT_INVALID_COLOR").'");
											return false;
									  }';
				echo $validation_script;
			}
		?>
		//submitform( pressbutton );
		document.adminForm.task.value = pressbutton;
		document.adminForm.submit();
	}
</script>
    <style>
    	input[type="text"]{
			width:135px;!important
		}
    
    </style>
     <div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=29999534&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_PROGRESS_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
    <div class="well well-minimized">
		<?php echo JText::_("GURU_PROGRESS_SETTINGS_DESCRIPTION"); ?>
	</div>
    <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_PROGRESS_BAR');?></h5></div>
   <div class="widget-body">
   	 <div style="padding-right: 375px;" class="pagination-right"><?php echo JText::_("GURU_PREVIEW"); ?></div>
   	<br/>
    	<div class="widget-main">	
         <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="curency"><?php echo JText::_('GURU_SHOW_PROGRS_BAR');?></label>
                            <div class="controls">
                                <input type="hidden" name="progress_bar" value="1">
								<?php
                                    $checked = '';
                                    if($configs->progress_bar == "0"){
                                        $checked = 'checked="checked"';
                                    }
                                ?>
                                <input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="progress_bar">
                                <span class="lbl"></span>                             
                                
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_PROGRS_BAR"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                                <div class="pull-right span5">
                                      <div id="progress" class="progress" style="width:<?php echo $configs->st_width; ?>px !important; height:<?php echo $configs->st_height; ?>px !important;">
                                        <div id="success" class="bar" style="width: 45%; background-color:<?php echo $configs->st_donecolor; ?>!important; background-image: none !important;"></div>
                                        <div id="warning" class="bar " style="width: 30%; background-color:<?php echo $configs->st_txtcolor; ?> !important; background-image: none !important;"></div>
                                        <div id="danger"  class="bar " style="width: 15%; background-color:<?php echo $configs->st_notdonecolor; ?>!important; background-image: none !important; "></div>
                                      </div>
                                    </div>  	
                                </div>
                            </div>
                        </div>
                    </div>							
                </div>
            </div>						
        </div>	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="curency"><?php echo JText::_('GURU_DONECOLOR');?></label>
                            <div class="controls">
                       			<div>
                                        <div style="float:left;">
                                            <input type="text" size="7" name="st_donecolor" ID="pick_rdonecolorfield" value="<?php echo substr($configs->st_donecolor, 1, strlen($configs->st_donecolor));?>" onchange="changeBcolor(); relateColor('pick_rdonecolor', this.value);" size="6" maxlength="6" onkeyup="if (this.value.length == 6) {relateColor('pick_rdonecolor', this.value); changeBcolor();}"/>
                                            &nbsp;
                                            <a href="javascript:pickColor('pick_rdonecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_rdonecolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">
                                            &nbsp;&nbsp;&nbsp;
                                            </a>
                                            <SCRIPT LANGUAGE="javascript">relateColor('pick_rdonecolor', getObj('pick_rdonecolorfield').value);</script>
                                            &nbsp;&nbsp;&nbsp;
                                        </div>
                                        <div style="float:left;">
                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DONECOLOR"); ?>" >
                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                            </span>
                                        </div>                    
                                    </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="curency"><?php echo JText::_('GURU_NOTDONECOL');?></label>
                            <div class="controls">
                       			<div>
                                    <div style="float:left;">
                                        <input type="text" size="7" name="st_notdonecolor" ID="pick_pnotdonecolorfield" value="<?php echo substr($configs->st_notdonecolor, 1, strlen($configs->st_notdonecolor));?>" onchange="changeBcolor(); relateColor('pick_pnotdonecolor', this.value);" size="6" maxlength="6" onkeyup="if (this.value.length == 6) {relateColor('pick_pnotdonecolor', this.value); changeBcolor();}"  />
                                        &nbsp;
                                        <a href="javascript:pickColor('pick_pnotdonecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_pnotdonecolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">
                                        &nbsp;&nbsp;&nbsp;
                                        </a>
                                        <SCRIPT LANGUAGE="javascript">relateColor('pick_pnotdonecolor', getObj('pick_pnotdonecolorfield').value);</script>
                                        <span id='show_hide_box'></span>
                                        &nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div style="float:left;">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_NOTDONECOL"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>	
                                    </div>                    
                                </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="curency"><?php echo JText::_('GURU_TEXTCOL');?></label>
                            <div class="controls">
                       			<div>
                                    <div style="float:left;">
                                        <input type="text" size="7" name="st_txtcolor" ID="pick_stxtcolorfield" value="<?php echo substr($configs->st_txtcolor, 1, strlen($configs->st_txtcolor));?>" onchange="changeBcolor(); relateColor('pick_stxtcolor', this.value);" size="6" maxlength="6" onkeyup="if (this.value.length == 6) {relateColor('pick_stxtcolor', this.value); changeBcolor();}"  />
                                        &nbsp;
                                        <a href="javascript:pickColor('pick_stxtcolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_stxtcolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">
                                        &nbsp;&nbsp;&nbsp;
                                        </a>
                                        <SCRIPT LANGUAGE="javascript">relateColor('pick_stxtcolor', getObj('pick_stxtcolorfield').value);</script>
                                        <span id='show_hide_box'></span>
                                        &nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div style="float:left;">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_TEXTCOL"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>	
                                    </div>                    
                                </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="curency"><?php echo JText::_('GURU_WIDTH');?></label>
                            <div class="controls">
                                 <div style="float:left;">
                                        <input  onchange="guruChangeWidth(this.value);" type="text" size="4" name="st_width" value="<?php echo $configs->st_width; ?>" />
                                    </div>
                                    <div style="float:left;">
                                     &nbsp;&nbsp;&nbsp;px &nbsp;
                                    </div>
                                    <div style="float:left;">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_BAR_WIDTH"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="curency"><?php echo JText::_('GURU_HEIGHT');?></label>
                            <div class="controls">
                                <div>
                                    <div style="float:left;">
                                        <input onchange="guruChangeHeight(this.value);" type="text" size="4" name="st_height" value="<?php echo $configs->st_height; ?>" />
                                    </div>
                                    <div style="float:left;">
                                     &nbsp;&nbsp;&nbsp;px &nbsp;
                                    </div>
                                    <div style="float:left;">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_BAR_HEIGHT"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>	
  		</div>		
</div> 
	<?php
	}
	elseif($tab == "5"){
    ?>
	<div class="container-fluid">
          <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=29999432&tmpl=component')" href="#">
                    <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                <?php echo JText::_("GURU_EMAIL_VIDEO"); ?>                  
          </a>
	</div>	
	<div class="clearfix"></div>
    
	<div class="well well-minimized">
		<?php echo JText::_("GURU_EMAIL_SETTINGS_DESCRIPTION"); ?>
	</div>

	<div class="row--fluid">
		<div class="span8">

		    <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_AUTHOR_EMAIL');?></h5></div>
			
			<div class="widget-body">
		    	<div class="widget-main">
		        	<div class="row-fluid">
		                <div class="span12"> 
		                    <div class="row-fluid">
		                        <div class="span6">
		                            <div class="row-fluid">
		                                <div class="control-group">
		                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_FROMNAME');?></label>
		                                    <div class="controls">
		                                     <div style="float:left;">
		                                         <input type="text" size="32" name="fromname" value="<?php echo $configs->fromname; ?>" />
		                                        </div>
		                                        <div style="float:left;">
		                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FROMNAME"); ?>" >
		                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
		                                            </span>
		                                        </div>
		                                    </div>
		                                    </div>
		                                </div>										
		                            </div>
		                        </div>							
		                    </div>
		            </div>	
		            <div class="row-fluid">
		                <div class="span12"> 
		                    <div class="row-fluid">
		                        <div class="span6">
		                            <div class="row-fluid">
		                                <div class="control-group">
		                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_FROMEMAIL');?></label>
		                                <div class="controls">
		                                     <div style="float:left;">
		                                        <input type="text" size="32" name="fromemail" value="<?php echo $configs->fromemail; ?>" />
		                                        </div>
		                                        <div style="float:left;">
		                                             <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FROMEMAIL"); ?>" >
		                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
		                                            </span>
		                                        </div>
		                                    </div>
		                                    </div>
		                                </div>										
		                            </div>
		                        </div>							
		                    </div>
		                </div>						
		          	 <div class="row-fluid">
		                <div class="span12"> 
		                    <div class="row-fluid">
		                        <div class="span6">
		                            <div class="row-fluid">
		                                <div class="control-group">
		                                    <label class="control-label" for="curency"><?php echo JText::_('GURU_EMAIL_SUPER_ADMIN');?></label>
		                                <div class="controls">
		                                     <div style="float:left;">
		                                     	<table class="table table-bordered">
		                                            <tbody>
		                                            <?php 
		                                            for($i=0; $i<count($admins); $i++){
		                                                $id = $admins[$i]->id;
		                                                $admins_array = explode(",",$configs->admin_email);
		                                            ?>
		                                            <tr>
		                                                <td>
		                                                    <input type="checkbox" <?php if(isset($configs->admin_email)&&(in_array($id,$admins_array))) { echo 'checked="checked"'; } ?> name="cid[]" value="<?php echo $id;?>" />
		                                                    <span class="lbl"></span>
		                                                </td>	
		                                                 <td>
		                                                    <?php echo $admins[$i]->name;?>
		                                                </td>		
		                                            </tr>
		                                            <?php 
		                                                }
		                                            ?>
		             
		                                            </tbody>
		                                    </table>
		                                     
		                                        </div>
		                                        <div style="float:left;">
		                                             <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_EMAIL_SUPER_ADMIN1"); ?>" >
		                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
		                                            </span>
		                                        </div>
		                                    </div>
		                                    </div>
		                                </div>										
		                            </div>
		                        </div>							
		                    </div>
		                </div>			
					<div class="row-fluid">
						<div class="control-group">
		                	<label class="control-label" for="curency"><?php echo JText::_('GURU_SELECT_EMAIL_TEMPLATE');?></label>
		                    <div class="controls">
		                    	<select onchange="javascript:emailTemplate(this.value);" name="email_template" id="email_template">
		                            <option>- Select -</option>
		                            <optgroup label="<?php echo JText::_("GURU_TO_TEACHER"); ?>:">
		                                <option id="1" value="1"><?php echo JText::_("GURU_COURSE_APPROVED"); ?></option>
		                                <option id="2" value="2"><?php echo JText::_("GURU_COURSE_UNAPPROVED"); ?></option>
		                                <option id="4" value="4"><?php echo JText::_("GURU_TEACHER_APPROVED"); ?></option>
		                                <option id="5" value="5"><?php echo JText::_("GURU_TEACHER_PENDING"); ?></option>
		                                <option id="12" value="12"><?php echo JText::_("GURU_REVIEW_QUIZ"); ?></option>
		                                <option id="13" value="13"><?php echo JText::_("GURU_NEW_STUDENT_ENROLLED"); ?></option>
		                                <option id="17" value="17"><?php echo JText::_("GURU_COURSE_FINISHED"); ?></option>
		                            </optgroup>
		                            <optgroup label="<?php echo JText::_("GURU_TO_ADMIN"); ?>:">
		                                <option id="3" value="3"><?php echo JText::_("GURU_FOR_COURSE_APPROVED"); ?></option>
		                                <option id="6" value="6"><?php echo JText::_("GURU_FOR_TEACHER_APPROVED"); ?></option>
		                                <option id="7" value="7"><?php echo JText::_("GURU_FOR_TEACHER_REGISTERED"); ?></option>
		                                <option id="15" value="15"><?php echo JText::_("GURU_FOR_STUDENT_REGISTERED"); ?></option>
		                                <option id="10" value="10"><?php echo JText::_("GURU_PENDING_ORDERS"); ?></option>
		                                <option id="14" value="14"><?php echo JText::_("GURU_NEW_STUDENT_ENROLLED"); ?></option>
		                                <option id="16" value="16"><?php echo JText::_("GURU_COURSE_FINISHED"); ?></option>
		                            </optgroup>
		                            <optgroup label="<?php echo JText::_("GURU_TO_STUDENT"); ?>:">
		                                <option id="8" value="8"><?php echo JText::_("GURU_FOR_COURSE_APPROVED_ORDER"); ?></option>
		                                <option id="9" value="9"><?php echo JText::_("GURU_RADED_AND_CHECKED_RESULT"); ?></option>
		                                <option id="11" value="11"><?php echo JText::_("GURU_TEACHER_MODIFIED_FEEDBACK"); ?></option>
		                            </optgroup>
		                        </select>
		                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SELECT_EMAIL_TEMPLATE"); ?>" >
									<img border="0" src="components/com_guru/images/icons/tooltip.png">
								</span>
		                    </div>
						</div>
					</div>
		            
		            <div id="course-approve-email" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="approve_subject" value="<?php echo $template_emails->approve_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('approve_body', $template_emails->approve_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		                
		            </div>
		            
		            <div id="course-unapprove-email" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="unapprove_subject" value="<?php echo $template_emails->unapprove_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('unapprove_body', $template_emails->unapprove_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		                
		            </div>
		            
		            <div id="for-course-email" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_APPROVE_URL] = <?php echo JText::_("GURU_APPROVE_URL"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="ask_approve_subject" value="<?php echo $template_emails->ask_approve_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('ask_approve_body', $template_emails->ask_approve_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="for-teacher-approve" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="ask_teacher_subject" value="<?php echo $template_emails->ask_teacher_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('ask_teacher_body', $template_emails->ask_teacher_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="for-teacher-registered" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="new_teacher_subject" value="<?php echo $template_emails->new_teacher_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('new_teacher_body', $template_emails->new_teacher_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>

		            <div id="for-student-registered" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_LAST_NAME] = <?php echo JText::_("GURU_STUDENT_LAST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_EMAIL] = <?php echo JText::_("GURU_STUDENT_EMAIL"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="new_student_subject" value="<?php echo $template_emails->new_student_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('new_student_body', $template_emails->new_student_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="approved-teacher" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="approved_teacher_subject" value="<?php echo $template_emails->approved_teacher_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('approved_teacher_body', $template_emails->approved_teacher_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		             <div id="approved-order" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="approve_order_subject" value="<?php echo $template_emails->approve_order_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('approve_order_body', $template_emails->approve_order_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="pending-order" style="display:none;">
		                <div class="pull-left">
		                    <?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_LAST_NAME] = <?php echo JText::_("GURU_STUDENT_LAST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[ORDER_NUMBER] = <?php echo JText::_("GURU_ORDER_NUMBER"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[ORDER_LIST_URL] = <?php echo JText::_("GURU_ORDER_LIST_URL"); ?></div>
		                <div class="clearfix"></div>
		               
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="pending_order_subject" value="<?php echo $template_emails->pending_order_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
		                            <?php
		                                //$editor = JFactory::getEditor();
		                                echo $editor->display('pending_order_body', $template_emails->pending_order_body, '100%', '350', '75', '20', false);
		                            ?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="pending-teacher" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[USERNAME] = <?php echo JText::_("GURU_USERNAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[PASSWORD] = <?php echo JText::_("GURU_PASSWORD"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="pending_teacher_subject" value="<?php echo $template_emails->pending_teacher_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('pending_teacher_body', $template_emails->pending_teacher_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="checked-quiz-result" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[QUIZ_NAME] = <?php echo JText::_("GURU_QUIZ_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[LINK_TO_QUIZ_RESULT] = <?php echo JText::_("GURU_LINK_TO_QUIZ_RESULT"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="chek_quiz_subject" value="<?php echo $template_emails->chek_quiz_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('chek_quiz_body', $template_emails->chek_quiz_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="feedback-result" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[QUIZ_NAME] = <?php echo JText::_("GURU_QUIZ_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[ESSAY_QUESTION_TITLE] = <?php echo JText::_("GURU_ESSAY_QUESTION_TITLE"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SCORE] = <?php echo JText::_("GURU_ESSAY_QUESTION_SCORE"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[TEACHER_FULL_NAME] = <?php echo JText::_("GURU_TEACHER_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[FEEDBACK_CONTENT] = <?php echo JText::_("GURU_FEEDBACK_CONTENT"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[TOTAL_QUIZ_SCORE] = <?php echo JText::_("GURU_TOTAL_QUIZ_SCORE"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="feedback_subject" value="<?php echo $template_emails->feedback_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('feedback_body', $template_emails->feedback_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="review-quiz-result" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[AUTHOR_NAME] = <?php echo JText::_("GURU_TEACH_NAME"); ?></div>
		                <div class="clearfix"></div>
		                 <div class="pull-left">[STUDENT_NAME] = <?php echo JText::_("GURU_STUDENT_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[QUIZ_NAME] = <?php echo JText::_("GURU_QUIZ_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[LINK_TO_QUIZ_RESULT] = <?php echo JText::_("GURU_LINK_TO_QUIZ_RESULT"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="review_quiz_subject" value="<?php echo $template_emails->review_quiz_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('review_quiz_body', $template_emails->review_quiz_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="new-student-enrolled" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_LAST_NAME] = <?php echo JText::_("GURU_STUDENT_LAST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_EMAIL] = <?php echo JText::_("GURU_STUDENT_EMAIL"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="new_student_enrolled_subject" value="<?php echo $template_emails->new_student_enrolled_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('new_student_enrolled_body', $template_emails->new_student_enrolled_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>

					<div id="new-student-enrolled-admin" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_FIRST_NAME] = <?php echo JText::_("GURU_STUDENT_FIRST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_LAST_NAME] = <?php echo JText::_("GURU_STUDENT_LAST_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_EMAIL] = <?php echo JText::_("GURU_STUDENT_EMAIL"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="new_student_enrolled_admin_subject" value="<?php echo $template_emails->new_student_enrolled_admin_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('new_student_enrolled_admin_body', $template_emails->new_student_enrolled_admin_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="teacher-completed-course" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_NAME] = <?php echo JText::_("GURU_STUDENT_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[TEACHER_FULL_NAME] = <?php echo JText::_("GURU_TEACHER_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="teacher_completed_course_subject" value="<?php echo $template_emails->teacher_completed_course_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('teacher_completed_course_body', $template_emails->teacher_completed_course_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
		            <div id="admin-completed-course" style="display:none;">
		            	<div class="pull-left">
		                	<?php echo JText::_("GURU_USE_EMAIL_VARIABLES"); ?>
		                </div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[STUDENT_NAME] = <?php echo JText::_("GURU_STUDENT_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[COURSE_NAME] = <?php echo JText::_("GURU_COURSE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[SITE_NAME] = <?php echo JText::_("GURU_SITE_NAME"); ?></div>
		                <div class="clearfix"></div>
		                <div class="pull-left">[ADMIN_NAME] = <?php echo JText::_("GURU_ADMIN_NAME"); ?></div>
		                <div class="clearfix"></div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_SUBJECT');?></label>
		                        <div class="controls">
		                            <input type="text" class="input-large" name="admin_completed_course_subject" value="<?php echo $template_emails->admin_completed_course_subject; ?>" />
		                        </div>
		                    </div>
		                </div>
		                
		                <div class="row-fluid">
		                    <div class="control-group">
		                        <label class="control-label" for="curency"><?php echo JText::_('GURU_BODY');?></label>
		                        <div class="controls">
									<?php
										//$editor = JFactory::getEditor();
										echo $editor->display('admin_completed_course_body', $template_emails->admin_completed_course_body, '100%', '350', '75', '20', false);
									?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            
				</div>
			</div>
    	
    	</div>

    	<div class="span4">
    		
    		<div class="widget-header widget-header-flat">
    			<h5><?php echo JText::_("GURU_EMAIL_NOTIFICATIONS"); ?></h5>
    		</div>
    		<div class="widget-body">
		    	<div class="widget-main">
		    		<table class="send-email-notifications">
		    			<tr>
		    				<td> <b><?php echo JText::_("GURU_TO_TEACHER"); ?></b> </td>
		    				<td> </td>
		    			</tr>
		    			<tr>
		    				<td> <?php echo JText::_("GURU_COURSE_APPROVED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_course_approved = "1";

				                            if(isset($template_emails->send_teacher_email_course_approved)){
				                            	$send_teacher_email_course_approved = $template_emails->send_teacher_email_course_approved;
				                            }
				                            
				                            if($send_teacher_email_course_approved == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_course_approved" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_course_approved" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_COURSE_UNAPPROVED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_course_unapproved = "1";

				                            if(isset($template_emails->send_teacher_email_course_unapproved)){
				                            	$send_teacher_email_course_unapproved = $template_emails->send_teacher_email_course_unapproved;
				                            }
				                            
				                            if($send_teacher_email_course_unapproved == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_course_unapproved" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_course_unapproved" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_TEACHER_APPROVED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_teacher_approved = "1";

				                            if(isset($template_emails->send_teacher_email_teacher_approved)){
				                            	$send_teacher_email_teacher_approved = $template_emails->send_teacher_email_teacher_approved;
				                            }
				                            
				                            if($send_teacher_email_teacher_approved == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_teacher_approved" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_teacher_approved" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_TEACHER_PENDING"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_teacher_pending = "1";

				                            if(isset($template_emails->send_teacher_email_teacher_pending)){
				                            	$send_teacher_email_teacher_pending = $template_emails->send_teacher_email_teacher_pending;
				                            }
				                            
				                            if($send_teacher_email_teacher_pending == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_teacher_pending" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_teacher_pending" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_REVIEW_QUIZ"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_review_quiz = "1";

				                            if(isset($template_emails->send_teacher_email_review_quiz)){
				                            	$send_teacher_email_review_quiz = $template_emails->send_teacher_email_review_quiz;
				                            }
				                            
				                            if($send_teacher_email_review_quiz == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_review_quiz" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_review_quiz" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_NEW_STUDENT_ENROLLED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_student_enrolled = "1";

				                            if(isset($template_emails->send_teacher_email_student_enrolled)){
				                            	$send_teacher_email_student_enrolled = $template_emails->send_teacher_email_student_enrolled;
				                            }
				                            
				                            if($send_teacher_email_student_enrolled == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_student_enrolled" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_student_enrolled" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_COURSE_FINISHED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_teacher_email_course_finished = "1";

				                            if(isset($template_emails->send_teacher_email_course_finished)){
				                            	$send_teacher_email_course_finished = $template_emails->send_teacher_email_course_finished;
				                            }
				                            
				                            if($send_teacher_email_course_finished == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_teacher_email_course_finished" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_teacher_email_course_finished" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td style="padding-top: 30px;"> <b><?php echo JText::_("GURU_TO_ADMIN"); ?></b> </td>
		    				<td> </td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_FOR_COURSE_APPROVED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_course_approved = "1";

				                            if(isset($template_emails->send_admin_email_course_approved)){
				                            	$send_admin_email_course_approved = $template_emails->send_admin_email_course_approved;
				                            }
				                            
				                            if($send_admin_email_course_approved == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_course_approved" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_course_approved" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_FOR_TEACHER_APPROVED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_teacher_approved = "1";

				                            if(isset($template_emails->send_admin_email_teacher_approved)){
				                            	$send_admin_email_teacher_approved = $template_emails->send_admin_email_teacher_approved;
				                            }
				                            
				                            if($send_admin_email_teacher_approved == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_teacher_approved" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_teacher_approved" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_FOR_TEACHER_REGISTERED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_teacher_registered = "1";

				                            if(isset($template_emails->send_admin_email_teacher_registered)){
				                            	$send_admin_email_teacher_registered = $template_emails->send_admin_email_teacher_registered;
				                            }
				                            
				                            if($send_admin_email_teacher_registered == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_teacher_registered" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_teacher_registered" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_FOR_STUDENT_REGISTERED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_student_registered = "1";

				                            if(isset($template_emails->send_admin_email_student_registered)){
				                            	$send_admin_email_student_registered = $template_emails->send_admin_email_student_registered;
				                            }
				                            
				                            if($send_admin_email_student_registered == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_student_registered" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_student_registered" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_PENDING_ORDERS"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_pending_order = "1";

				                            if(isset($template_emails->send_admin_email_pending_order)){
				                            	$send_admin_email_pending_order = $template_emails->send_admin_email_pending_order;
				                            }
				                            
				                            if($send_admin_email_pending_order == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_pending_order" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_pending_order" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_NEW_STUDENT_ENROLLED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_student_enrolled = "1";

				                            if(isset($template_emails->send_admin_email_student_enrolled)){
				                            	$send_admin_email_student_enrolled = $template_emails->send_admin_email_student_enrolled;
				                            }
				                            
				                            if($send_admin_email_student_enrolled == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_student_enrolled" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_student_enrolled" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_COURSE_FINISHED"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_admin_email_course_finished = "1";

				                            if(isset($template_emails->send_admin_email_course_finished)){
				                            	$send_admin_email_course_finished = $template_emails->send_admin_email_course_finished;
				                            }
				                            
				                            if($send_admin_email_course_finished == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_admin_email_course_finished" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_admin_email_course_finished" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td style="padding-top: 30px;"> <b><?php echo JText::_("GURU_TO_STUDENT"); ?></b> </td>
		    				<td> </td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_FOR_COURSE_APPROVED_ORDER"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_student_email_order_approved = "1";

				                            if(isset($template_emails->send_student_email_order_approved)){
				                            	$send_student_email_order_approved = $template_emails->send_student_email_order_approved;
				                            }
				                            
				                            if($send_student_email_order_approved == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_student_email_order_approved" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_student_email_order_approved" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_RADED_AND_CHECKED_RESULT"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_student_email_checked_results = "1";

				                            if(isset($template_emails->send_student_email_checked_results)){
				                            	$send_student_email_checked_results = $template_emails->send_student_email_checked_results;
				                            }
				                            
				                            if($send_student_email_checked_results == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_student_email_checked_results" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_student_email_checked_results" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    			<tr>
		    				<td> <?php echo JText::_("GURU_TEACHER_MODIFIED_FEEDBACK"); ?> </td>
		    				<td>
		    					<div class="controls">
				                    <fieldset class="radio btn-group">
				                        <?php
				                            $no_checked = "";
				                            $yes_cheched = "";

				                            $send_student_email_modified_feedback = "1";

				                            if(isset($template_emails->send_student_email_modified_feedback)){
				                            	$send_student_email_modified_feedback = $template_emails->send_student_email_modified_feedback;
				                            }
				                            
				                            if($send_student_email_modified_feedback == "0"){
				                                $no_checked = 'checked="checked"';
				                            }
				                            else{
				                                $yes_cheched = 'checked="checked"';
				                            }
				                        ?>
				                        <input type="hidden" name="send_student_email_modified_feedback" value="0">
				                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="send_student_email_modified_feedback" class="ace-switch ace-switch-5">
				                        <span class="lbl"></span>
				                    </fieldset>
				                </div>
		    				</td>
		    			</tr>

		    		</table>
		    	</div>
		    </div>

    	</div>
    </div>

    <?php
	}
	elseif($tab == "6"){       
	?>
    <div id="g_promotion_box">

        <div class="container-fluid">
              <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video"  onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=29999551&tmpl=component')" href="#">
                        <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                    <?php echo JText::_("GURU_PROMOTION_VIDEO"); ?>                  
              </a>
        </div>	
        <div class="clearfix"></div>
        <div class="well well-minimized">
            <?php echo JText::_("GURU_PROMOTION_SETTINGS_DESCRIPTION"); ?>
        </div>
        
            <?php
                echo $editor->display( 'content_selling', ''.stripslashes($configs->content_selling),'100%', '300px', '20', '60' );	
            ?>
           
            <?php	
		?>
   </div>
   
          <?php
   }
   elseif($tab == "8"){
  		$config_author = json_decode($configs->st_authorpage);
	 ?> 
    <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_GENERAL')." ".JText::_('GURU_SETTINGS');?></h5></div>
    <div class="widget-body">
        <div class="widget-main">
        	<div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_aprove"><?php echo JText::_('GURU_AUTHOR_APROVE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_aprove" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_aprove == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_aprove">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_GURU_AUTHOR_APROVE"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_redirect"><?php echo JText::_('GURU_AUTHOR_REDIRECT');?>
									</label>
									<div class="controls">
										<?php
											$teacher_redirect = "";

											if(isset($config_author->teacher_redirect)){
												$teacher_redirect = trim($config_author->teacher_redirect);
											}
										?>
										<input type="text" class="span12" value="<?php echo $teacher_redirect; ?>" name="teacher_redirect" style="width: 95% !important;" />
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_AUTHOR_REDIRECT_TIP"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_redirect_logout"><?php echo JText::_('GURU_AUTHOR_REDIRECT_LOGOUT');?>
									</label>
									<div class="controls">
										<?php
											$teacher_redirect_logout = "";

											if(isset($config_author->teacher_redirect_logout)){
												$teacher_redirect_logout = trim($config_author->teacher_redirect_logout);
											}
										?>
										<input type="text" class="span12" value="<?php echo $teacher_redirect_logout; ?>" name="teacher_redirect_logout" style="width: 95% !important;" />
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_AUTHOR_REDIRECT_LOGOUT_TIP"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_editor"><?php echo JText::_('GURU_AUTHOR_EDITOR'); ?>:		
									</label>
									<div class="controls">
										<select name="teacher_editor">
											<option value="0" <?php if(isset($config_author->teacher_editor) && intval($config_author->teacher_editor) == 0){ echo 'selected="selected"';} ?> ><?php echo JText::_('GURU_REDACTOR_WYSIWYG'); ?></option>
											<option value="1" <?php if(isset($config_author->teacher_editor) && intval($config_author->teacher_editor) == 1){ echo 'selected="selected"';} ?> ><?php echo JText::_('GURU_JOOMLA_EDITOR'); ?></option>
										</select>
										
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR_EDITOR"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_group"><?php echo JText::_('GURU_TEACHER_DEFAULT_GROUP');?>:		
                                  </label>
                                  <div class="controls">
                                    <?php 
                                    $db = JFactory::getDBO();
                                    $user = JFactory::getUser();
                                    $user_id = $user->id;
                                    
                                    $sql_u = "select group_id from #__user_usergroup_map where user_id=".intval($user_id);
                                    $db->setQuery($sql_u);
                                    $res_user_current = $db->loadResult();
                                    $listgroup = UsersHelper::getGroups();
                        
                                    ?>
                                        <select id="teacher_group" name="teacher_group"  class="inputbox" size="10">
                                        <?php
	                                        /*if($res_user_current == 8){
	                                        	echo JHtml::_('select.options', $listgroup, 'value', 'text', $config_author->teacher_group);
	                                        }
	                                        else{*/
	                                        	/*$groups_select = str_replace("- Super Users", "", JHtml::_('select.options', $listgroup, 'value', 'text', $config_author->teacher_group));
		                                        $groups_select = str_replace('value="8"', 'value="0"', $groups_select);*/

		                                        $groups_select = JHtml::_('select.options', $listgroup, 'value', 'text', $config_author->teacher_group);
		                                        $groups_select = preg_replace('/option value="8"(.*)\/option/msU', "", $groups_select);
		                                        $groups_select = str_replace("<>", "", $groups_select);

		                                        echo $groups_select;
	                                        //}
                                        ?>
                                        </select>
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_DEFAULT_GROUP2"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>              			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            
           	<?php
            	$terms_cond_teacher = $configs->terms_cond_teacher;
				$terms_cond_teacher_content = $configs->terms_cond_teacher_content;
				
				$mailchimp_teacher_api = $configs->mailchimp_teacher_api;
				$mailchimp_teacher_list_id = $configs->mailchimp_teacher_list_id;
				$mailchimp_teacher_auto = $configs->mailchimp_teacher_auto;
			?>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('GURU_ENABLE_TERMS');?>
                </label>
                <div class="controls">
                    <fieldset class="radio btn-group" id="single_video_p_show_length">
                        <?php
                            $no_checked = "";
                            $yes_cheched = "";
                            
                            if($terms_cond_teacher == "0"){
                                $no_checked = 'checked="checked"';
                            }
                            else{
                                $yes_cheched = 'checked="checked"';
                            }
                        ?>
                        <input type="hidden" name="terms_cond_teacher" value="0">
                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="terms_cond_teacher" class="ace-switch ace-switch-5">
                        <span class="lbl"></span>
                    </fieldset>
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ENABLE_TERMS_TEACHER_TOOLTIP"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('GURU_TERMS_AND_COND');?>
                </label>
                <div class="controls">
                    <?php
                        //$editor = JFactory::getEditor();
                        echo $editor->display('terms_cond_teacher_content', $terms_cond_teacher_content, '100%', '150', '75', '20', false);
                    ?>
                </div>
            </div>
            
            <h4><?php echo JText::_("GURU_MAILCHIMP_INTEGRATION"); ?></h4>
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('GURU_MAILCHIMP_API');?>
                </label>
                <div class="controls">
                    <input type="text" name="mailchimp_teacher_api" value="<?php echo $mailchimp_teacher_api; ?>" />
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MAILCHIMP_API_TIP"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('GURU_MAILCHIMP_LIST_ID');?>
                </label>
                <div class="controls">
                    <input type="text" name="mailchimp_teacher_list_id" value="<?php echo $mailchimp_teacher_list_id; ?>" />
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MAILCHIMP_LIST_ID_TIP"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('GURU_MAILCHIMP_AUTO_REGISTER');?>
                </label>
                <div class="controls">
                    <fieldset class="radio btn-group" id="single_video_p_show_length">
                        <?php
                            $yes_cheched = "";
                            $no_checked = "";
                            
                            if($mailchimp_teacher_auto == "0"){
                                $no_checked = 'checked="checked"';
                            }
                            else{
                                $yes_cheched = 'checked="checked"';
                            }
                        ?>
                        <input type="hidden" name="mailchimp_teacher_auto" value="0">
                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="mailchimp_teacher_auto" class="ace-switch ace-switch-5">
                        <span class="lbl"></span>
                    </fieldset>
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MAILCHIMP_AUTO_REGISTER_TIP"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
        </div>
    </div> 
    
    <br/>
    <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_ALLOW_TEACHERS_TO');?></h5></div>
    <div class="widget-body">
        <div class="widget-main">	
        	 <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="course_is_free_show"><?php echo JText::_('GURU_ALLOW_TECHERS_FREE');?>		
                                  </label>
                                  <div class="controls">
                                        <input type="hidden" name="course_is_free_show" value="0">
                                        <?php
                                            $checked = '';
                                            if($configs->course_is_free_show == 1){
                                                $checked = 'checked="checked"';
                                            }
                                        ?>
                                        <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="course_is_free_show">
                                        <span class="lbl"></span>
                                    
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_SHOW_HIDE_COURSEISFREE1"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>               			 
                                 </div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>		
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_add_media"><?php echo JText::_('GURU_TEACHER_ADD_MEDIA');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_add_media" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_add_media == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_add_media">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_ADD_MEDIA2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>	
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_edit_media"><?php echo JText::_('GURU_TEACHER_EDIT_OWN_MEDIA');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_edit_media" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_edit_media == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_edit_media">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_EDIT_OWN_MEDIA2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
             <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_add_courses"><?php echo JText::_('GURU_TEACHER_ADD_COURSE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_add_courses" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_add_courses == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_add_courses">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_ADD_COURSE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_add_courses"><?php echo JText::_('GURU_TEACHER_AUTO_APPROVE_COURSE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_approve_courses" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_approve_courses == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_approve_courses">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_AUTO_APPROVE_COURSE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_edit_courses"><?php echo JText::_('GURU_TEACHER_EDIT_OWN_COURSE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_edit_courses" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_edit_courses == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_edit_courses">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_EDIT_OWN_COURSE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_publish_courses"><?php echo JText::_('GURU_TEACHER_PUBLISH_OWN_COURSE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_publish_courses" value="1">
										<?php
											$checked = '';
											if(isset($config_author->teacher_publish_courses) && $config_author->teacher_publish_courses == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_publish_courses">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_PUBLISH_OWN_COURSE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_unpublish_courses"><?php echo JText::_('GURU_TEACHER_UNPUBLISH_OWN_COURSE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_unpublish_courses" value="1">
										<?php
											$checked = '';
											if(isset($config_author->teacher_unpublish_courses) && $config_author->teacher_unpublish_courses == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_unpublish_courses">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_UNPUBLISH_OWN_COURSE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>

            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_delete_courses"><?php echo JText::_('GURU_TEACHER_DELETE_OWN_COURSE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_delete_courses" value="1">
										<?php
											$checked = '';
											if(isset($config_author->teacher_delete_courses) && $config_author->teacher_delete_courses == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_delete_courses">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_DELETE_OWN_COURSE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            
            <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_add_quizzesfe"><?php echo JText::_('GURU_TEACHER_ADD_QUIZZES_FE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_add_quizzesfe" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_add_quizzesfe == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_add_quizzesfe">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_ADD_QUIZZES_FE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
             <div class="row-fluid">
                <div class="span12"> 
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="row-fluid">
                                <div class="control-group">
                                    <label class="control-label" for="teacher_edit_quizzesfe"><?php echo JText::_('GURU_TEACHER_EDIT_QWN_QUIZZES_FE');?>:		
									</label>
									<div class="controls">
										<input type="hidden" name="teacher_edit_quizzesfe" value="1">
										<?php
											$checked = '';
											if($config_author->teacher_edit_quizzesfe == 0){
												$checked = 'checked="checked"';
											}
										?>
										<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_edit_quizzesfe">
										<span class="lbl"></span>
										<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_EDIT_QWN_QUIZZES_FE2"); ?>" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>                   			 
									</div>
                                </div>										
                            </div>
                        </div>							
                    </div>
                </div>						
            </div>
            </div>
        </div>

        <?php
        	if(!isset($config_author->teacher_menu_courses)){
        		$config_author->teacher_menu_courses = 0;
        	}

        	if(!isset($config_author->teacher_menu_students)){
        		$config_author->teacher_menu_students = 0;
        	}

        	if(!isset($config_author->teacher_menu_projects)){
        		$config_author->teacher_menu_projects = 0;
        	}

        	if(!isset($config_author->teacher_menu_scorm)){
        		$config_author->teacher_menu_scorm = 0;
        	}

        	if(!isset($config_author->teacher_menu_quizzes)){
        		$config_author->teacher_menu_quizzes = 0;
        	}

        	if(!isset($config_author->teacher_menu_media)){
        		$config_author->teacher_menu_media = 0;
        	}

        	if(!isset($config_author->teacher_menu_commissions)){
        		$config_author->teacher_menu_commissions = 0;
        	}

        	if(!isset($config_author->teacher_menu_grade)){
        		$config_author->teacher_menu_grade = 0;
        	}
        ?>

        <br />
        <div class="widget-header widget-header-flat">
        	<h5><?php echo JText::_('GURU_TEACHER_MENU_ITEMS'); ?></h5>
        </div>
		<div class="widget-body">
    		<div class="widget-main">
				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_COURSES'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_courses" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_courses == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_courses">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_COURSES_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_STUDENTS'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_students" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_students == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_students">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_STUDENTS_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_PROJECTS'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_projects" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_projects == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_projects">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_PROJECTS_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_SCORM'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_scorm" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_scorm == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_scorm">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_SCORM_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_QUIZZES'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_quizzes" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_quizzes == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_quizzes">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_QUIZZES_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_MEDIA'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_media" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_media == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_media">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_MEDIA_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_COMMISSIONS'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_commissions" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_commissions == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_commissions">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_COMMISSIONS_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>

				<div class="row-fluid">
					<div class="span12">
						<div class="row-fluid">
							<div class="control-group">
								<label class="control-label" for="teacher_edit_quizzesfe">
									<?php echo JText::_('GURU_TEACHER_MENU_GRADE'); ?>:		
								</label>
								<div class="controls">
									<input type="hidden" name="teacher_menu_grade" value="1">
									<?php
										$checked = '';
										
										if($config_author->teacher_menu_grade == 0){
											$checked = 'checked="checked"';
										}
									?>
									<input type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="teacher_menu_grade">
									<span class="lbl"></span>
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TEACHER_MENU_GRADE_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
							</div>										
						</div>
					</div>							
				</div>
    		</div>
    	</div>

    	<br />
        <div class="widget-header widget-header-flat">
        	<h5><?php echo JText::_('GURU_CUSTOM_FIELDS_REGISTRATION'); ?></h5>
        </div>
		<div class="widget-body">
    		<div class="widget-main">
    			<div class="alert alert-info">
    				<?php echo JText::_("GURU_CUSTOM_FIELDS_INFO"); ?>
    			</div>

				<?php
					$fields_groups = $this->getFieldsGroups();
					$teacher_custom_fields = json_decode($configs->teacher_custom_fields, true);

					if(!isset($teacher_custom_fields)){
						$teacher_custom_fields = array();
					}

					if(isset($fields_groups) && count($fields_groups) > 0){
						foreach($fields_groups as $key=>$field_group){
				?>
                    		<h4><?php echo $field_group["name"]; ?></h4>
                <?php
							$group_fields = $this->getGroupFields($field_group["id"]);
							
							if(isset($group_fields) && count($group_fields) > 0){
				?>
								<table class="table table-bordered table-striped adminlist">
									<tr>
										<th width="5%"></th>
										<th><?php echo JText::_("GURU_FIELD_NAME"); ?></th>
										<th><?php echo JText::_("GURU_FIELD_CODE"); ?></th>
										<th><?php echo JText::_("GURU_TYPE"); ?></th>
									</tr>
				<?php
								foreach($group_fields as $key_field=>$field){
									$checked = "";

									if(in_array($field["id"], $teacher_custom_fields)){
										$checked = "checked";
									}
				?>
									<tr>
										<td style="text-align:center;">
											<input type="checkbox" <?php echo $checked; ?> name="fields[]" value="<?php echo $field["id"]; ?>" />
											<span class="lbl"></span>
										</td>
										<td>
											<?php echo $field["name"]; ?>
										</td>
										<td>
											<?php echo $field["field_code"]; ?>
										</td>
										<td>
											<?php echo strtoupper($field["type"]); ?>
										</td>
									</tr>
                <?php				
								}
				?>
								</table>
				<?php
							}
						}
					}
				?>
			</div>
		</div>

    </div> 
     <?php
    }
	elseif($tab == "9"){
		$student_params = json_decode($configs->student_params, true);

		$terms_cond_student = $configs->terms_cond_student;
		$terms_cond_student_content = $configs->terms_cond_student_content;
		
		$mailchimp_student_api = $configs->mailchimp_student_api;
		$mailchimp_student_list_id = $configs->mailchimp_student_list_id;
		$mailchimp_student_auto = $configs->mailchimp_student_auto;
	?>
		<div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="student_redirect"><?php echo JText::_('GURU_STUDENT_REDIRECT');?>
								</label>
								<div class="controls">
									<?php
										$student_redirect = "";

										if(isset($student_params["student_redirect"])){
											$student_redirect = trim($student_params["student_redirect"]);
										}
									?>
									<input type="text" class="span12" value="<?php echo $student_redirect; ?>" name="student_redirect" style="width: 95% !important;" />
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_REDIRECT_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>

        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label" for="student_redirect_logout"><?php echo JText::_('GURU_STUDENT_REDIRECT_LOGOUT');?>
								</label>
								<div class="controls">
									<?php
										$student_redirect_logout = "";

										if(isset($student_params["student_redirect_logout"])){
											$student_redirect_logout = trim($student_params["student_redirect_logout"]);
										}
									?>
									<input type="text" class="span12" value="<?php echo $student_redirect_logout; ?>" name="student_redirect_logout" style="width: 95% !important;" />
									<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_STUDENT_REDIRECT_LOGOUT_TIP"); ?>" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>
								</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>

    	<div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_ENABLE_TERMS');?>
            </label>
            <div class="controls">
                <fieldset class="radio btn-group" id="single_video_p_show_length">
                    <?php
                        $no_checked = "";
                        $yes_cheched = "";
                        
                        if($terms_cond_student == "0"){
                            $no_checked = 'checked="checked"';
                        }
                        else{
                            $yes_cheched = 'checked="checked"';
                        }
                    ?>
                    <input type="hidden" name="terms_cond_student" value="0">
                    <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="terms_cond_student" class="ace-switch ace-switch-5">
                    <span class="lbl"></span>
                </fieldset>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ENABLE_TERMS_STUDENT_TOOLTIP"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_TERMS_AND_COND');?>
            </label>
            <div class="controls">
                <?php
                    //$editor = JFactory::getEditor();
                    echo $editor->display('terms_cond_student_content', $terms_cond_student_content, '100%', '150', '75', '20', false);
                ?>
            </div>
        </div>
        
        <h4><?php echo JText::_("GURU_MAILCHIMP_INTEGRATION"); ?></h4>
        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_MAILCHIMP_API');?>
            </label>
            <div class="controls">
                <input type="text" name="mailchimp_student_api" value="<?php echo $mailchimp_student_api; ?>" />
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MAILCHIMP_API_TIP"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_MAILCHIMP_LIST_ID');?>
            </label>
            <div class="controls">
                <input type="text" name="mailchimp_student_list_id" value="<?php echo $mailchimp_student_list_id; ?>" />
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MAILCHIMP_LIST_ID_TIP"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_MAILCHIMP_AUTO_REGISTER');?>
            </label>
            <div class="controls">
                <fieldset class="radio btn-group" id="single_video_p_show_length">
                    <?php
                        $yes_cheched = "";
                        $no_checked = "";
                        
                        if($mailchimp_teacher_auto == "0"){
                            $no_checked = 'checked="checked"';
                        }
                        else{
                            $yes_cheched = 'checked="checked"';
                        }
                    ?>
                    <input type="hidden" name="mailchimp_student_auto" value="0">
                    <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="mailchimp_student_auto" class="ace-switch ace-switch-5">
                    <span class="lbl"></span>
                </fieldset>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MAILCHIMP_AUTO_REGISTER_TIP"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>

        <br />
        <div class="widget-header widget-header-flat">
        	<h5><?php echo JText::_('GURU_CUSTOM_FIELDS_REGISTRATION'); ?></h5>
        </div>
		<div class="widget-body">
    		<div class="widget-main">
    			<div class="alert alert-info">
    				<?php echo JText::_("GURU_CUSTOM_FIELDS_INFO2"); ?>
    			</div>

				<?php
					$fields_groups = $this->getFieldsGroups();
					$student_custom_fields = json_decode($configs->student_custom_fields, true);

					if(!isset($student_custom_fields)){
						$student_custom_fields = array();
					}

					if(isset($fields_groups) && count($fields_groups) > 0){
						foreach($fields_groups as $key=>$field_group){
				?>
                    		<h4><?php echo $field_group["name"]; ?></h4>
                <?php
							$group_fields = $this->getGroupFields($field_group["id"]);
							
							if(isset($group_fields) && count($group_fields) > 0){
				?>
								<table class="table table-bordered table-striped adminlist">
									<tr>
										<th width="5%"></th>
										<th><?php echo JText::_("GURU_FIELD_NAME"); ?></th>
										<th><?php echo JText::_("GURU_FIELD_CODE"); ?></th>
										<th><?php echo JText::_("GURU_TYPE"); ?></th>
									</tr>
				<?php
								foreach($group_fields as $key_field=>$field){
									$checked = "";

									if(in_array($field["id"], $student_custom_fields)){
										$checked = "checked";
									}
				?>
									<tr>
										<td style="text-align:center;">
											<input type="checkbox" <?php echo $checked; ?> name="fields2[]" value="<?php echo $field["id"]; ?>" />
											<span class="lbl"></span>
										</td>
										<td>
											<?php echo $field["name"]; ?>
										</td>
										<td>
											<?php echo $field["field_code"]; ?>
										</td>
										<td>
											<?php echo strtoupper($field["type"]); ?>
										</td>
									</tr>
                <?php				
								}
				?>
								</table>
				<?php
							}
						}
					}
				?>
			</div>
		</div>
    <?php
	}
	elseif($tab == "10"){
		$gurupcategs = "";
		$guruprograms = "";
		$gurutasks = "";
		$guruorders = "";
		$guruauthor = "";
		$guruprofile = "";
		$gurulogin = "";
		$gurueditplans = "";
		$gurubuy = "";

		$selected_gurupcategs = "-1";
		$selected_guruprograms = "-1";
		$selected_gurutasks = "-1";
		$selected_guruorders = "-1";
		$selected_guruauthor = "-1";
		$selected_guruprofile = "-1";
		$selected_gurulogin = "-1";
		$selected_gurueditplans = "-1";
		$selected_gurubuy = "-1";
		
		if(isset($configs->seo) && trim($configs->seo) != ""){
			$seo_array = json_decode(trim($configs->seo), true);
			$seo = $seo_array["seo"];
			$itemid = $seo_array["itemid"];
			
			$gurupcategs = $seo["gurupcategs"];
			$guruprograms = $seo["guruprograms"];
			$gurutasks = $seo["gurutasks"];
			$guruorders = $seo["guruorders"];
			$guruauthor = $seo["guruauthor"];
			$guruprofile = $seo["guruprofile"];
			$gurulogin = $seo["gurulogin"];
			$gurueditplans = $seo["gurueditplans"];
			$gurubuy = $seo["gurubuy"];
			
			$selected_gurupcategs = @$itemid["gurupcategs"];
			$selected_guruprograms = @$itemid["guruprograms"];
			$selected_gurutasks = @$itemid["gurutasks"];
			$selected_guruorders = @$itemid["guruorders"];
			$selected_guruauthor = @$itemid["guruauthor"];
			$selected_guruprofile = @$itemid["guruprofile"];
			$selected_gurulogin = @$itemid["gurulogin"];
			$selected_gurueditplans = @$itemid["gurueditplans"];
			$selected_gurubuy = @$itemid["gurubuy"];
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get a list of the menu items
		$query->select('m.id, m.parent_id, m.title, m.menutype');
		$query->from($db->quoteName('#__menu') . ' AS m');
		$query->where($db->quoteName('m.published') . ' = 1');
		$query->order('m.menutype, m.parent_id');
		$db->setQuery($query);

		$mitems = $db->loadObjectList();

		if (!$mitems)
		{
			$mitems = array();
		}

		// Establish the hierarchy of the menu
		$children = array();

		// First pass - collect children
		foreach ($mitems as $v)
		{
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		// Second pass - get an indent list of the items
		
		$mitems = array();
		$mitems[] = JHtml::_('select.option', -1, JText::_('JOPTION_UNASSIGNED'));

		$lastMenuType = null;
		$tmpMenuType = null;
		foreach ($list as $list_a)
		{
			if ($list_a->menutype != $lastMenuType)
			{
				if ($tmpMenuType)
				{
					$mitems[] = JHtml::_('select.option', '</OPTGROUP>');
				}
				$mitems[] = JHtml::_('select.option', '<OPTGROUP>', $list_a->menutype);
				$lastMenuType = $list_a->menutype;
				$tmpMenuType = $list_a->menutype;
			}

			$mitems[] = JHtml::_('select.option', $list_a->id, $list_a->title);
		}
		if ($lastMenuType !== null)
		{
			$mitems[] = JHtml::_('select.option', '</OPTGROUP>');
		}
		
		$selections = $mitems;
	?>
    	<table class="table">
        	<tr>
            	<th>
                	<?php echo JText::_("GURU_CONTROLLER_NAME"); ?>
                </th>
                <th>
                	<?php echo JText::_("GURU_CHANGE_WITH"); ?>
                </th>
                <th>
                	<?php echo JText::_("GURU_ASSIGN_ITEMID"); ?>
                </th>
            </tr>
            <tr>
            	<td>
                	guruPcategs / gurupcategs
                </td>
                <td>
                	<input type="text" name="seo[gurupcategs]" value="<?php echo $gurupcategs; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[gurupcategs]', 'class="inputbox"', 'value', 'text', $selected_gurupcategs, 'selections' );
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	guruPrograms / guruprograms
                </td>
                <td>
                	<input type="text" name="seo[guruprograms]" value="<?php echo $guruprograms; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[guruprograms]', 'class="inputbox"', 'value', 'text', $selected_guruprograms, 'selections' );
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	guruTasks / gurutasks
                </td>
                <td>
                	<input type="text" name="seo[gurutasks]" value="<?php echo $gurutasks; ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
            	<td>
                	guruOrders / guruorders
                </td>
                <td>
                	<input type="text" name="seo[guruorders]" value="<?php echo $guruorders; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[guruorders]', 'class="inputbox"', 'value', 'text', $selected_guruorders, 'selections' );
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	guruAuthor / guruauthor
                </td>
                <td>
                	<input type="text" name="seo[guruauthor]" value="<?php echo $guruauthor; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[guruauthor]', 'class="inputbox"', 'value', 'text', $selected_guruauthor, 'selections' );
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	guruProfile / guruprofile
                </td>
                <td>
                	<input type="text" name="seo[guruprofile]" value="<?php echo $guruprofile; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[guruprofile]', 'class="inputbox"', 'value', 'text', $selected_guruprofile, 'selections' );
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	guruLogin / gurulogin
                </td>
                <td>
                	<input type="text" name="seo[gurulogin]" value="<?php echo $gurulogin; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[gurulogin]', 'class="inputbox"', 'value', 'text', $selected_gurulogin, 'selections' );
					?>
                </td>
            </tr>
            <tr>
            	<td>
                	guruEditplans / gurueditplans
                </td>
                <td>
                	<input type="text" name="seo[gurueditplans]" value="<?php echo $gurueditplans; ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
            	<td>
                	guruBuy / gurubuy
                </td>
                <td>
                	<input type="text" name="seo[gurubuy]" value="<?php echo $gurubuy; ?>" />
                </td>
                <td>
                	<?php
						echo JHTML::_('select.genericlist', $selections, 'selections[gurubuy]', 'class="inputbox"', 'value', 'text', $selected_gurubuy, 'selections' );
					?>
                </td>
            </tr>
        </table>
    <?php
	}
	elseif($tab == "11"){
		$license_number = $configs->license_number;
		$license_details = $this->getLicenseDetails($license_number);
	?>
		<div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_ADD_LICENSE_NUMBER');?>
            </label>
            <div class="controls">
                <input type="text" name="license_number" value="<?php echo $license_number; ?>" />
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_LICENSE_NUMBER_TIP"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_LICENSE_DOMAIN');?>
            </label>
            <div class="controls">
                <?php echo $license_details["domain"]; ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_LICENSE_EXPIRATION');?>
            </label>
            <div class="controls">
                <?php echo $license_details["date"]; ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_LICENSE_STATUS');?>
            </label>
            <div class="controls">
                <?php echo $license_details["status"]; ?>
            </div>
        </div>
	<?php
	}
	elseif($tab == "12"){
		$secure_key = "";
		$payment_plugins = $this->payment_plugins;
		$payed_plugins = "";

		if(isset($configs->secure_key)){
			$secure_key = $configs->secure_key;
		}

		if(isset($configs->payed_plugins)){
			$payed_plugins = $configs->payed_plugins;
		}
	?>
		<div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_SECURE_KEY'); ?>
            </label>
            <div class="controls">
                <input type="text" id="secure-key" name="secure_key" value="<?php echo $secure_key; ?>" readonly />
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_SECURE_KEY_TIP"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
                <input type="button" class="btn btn-success" onclick="javascript:generateNewKey(); return false;" name="generate_key" value="<?php echo JText::_("GURU_GENERATE_KEY"); ?>" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_ORDERS_PAYMENT_PLUGINS'); ?>
            </label>
            <div class="controls">
                <select name="payed_plugins" style="width: 300px !important;">
                	<option value=""><?php echo JText::_("GURU_ALL"); ?></option>
                	<?php
                		if(isset($payment_plugins) && count($payment_plugins) > 0){
                			foreach ($payment_plugins as $key => $plugin) {
                				$selected = "";

                				if($plugin["element"] == $payed_plugins){
                					$selected = 'selected="selected"';
                				}

                				echo '<option value="'.$plugin["element"].'" '.$selected.'>'.JText::_($plugin["name"]).'</option>';
                			}
                		}
                	?>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">
                <?php echo JText::_('GURU_API_URL'); ?>
            </label>
            <div class="controls">
                <div id="api-url">
                	<?php
                		echo JURI::root()."index.php?option=com_guru&controller=guruBuy&task=apiurl&key=".$secure_key;
                	?>
                </div>
                <div class="alert alert-info">
                	<?php echo JText::_("GURU_API_URL_TIP"); ?>
                </div>
            </div>
        </div>
	<?php
	}
 	?>
 
    <input type="hidden" name="id" value="1" />
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="save" />	
	<input type="hidden" name="controller" value="guruConfigs" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="tab" value="<?php echo intval($tab); ?>" />
	<input type="hidden" name="site_url" id="site-url" value="<?php echo JURI::root(); ?>" />
</form>
<script language="javascript">
	var first = false;
	function showContentVideo(href){
	first = true;
	jQuery.ajax({
      url: href,
      success: function(response){
       jQuery( '#myModal .modal-body').html(response);
      }
    });
}

	jQuery('#myModal').on('hide', function () {
	 jQuery('div.modal-body').html('');
	});
	
	jQuery('#myModal').on('hide', function () {
	 jQuery('div.modal-body').html('');
	});
	jQuery('body').click(function () {
	if(!first){
		jQuery('#myModal .modal-body iframe').attr('src', '');
	}
	else{
		first = false;
	}
});
</script>