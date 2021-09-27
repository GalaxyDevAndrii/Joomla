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
$div_menu = $this->authorGuruMenuBar();
$id = 0;


$student = $this->student;

$username = "";
$firstname = "";
$lastname = "";
$company = "";
$email = "";
$disabled = "";

if(isset($student) && count($student) > 0){
	$id = $student["0"]["user_id"];
	$username = $student["0"]["username"];
	$firstname = $student["0"]["firstname"];
	$lastname = $student["0"]["lastname"];
	$company = $student["0"]["company"];
	$email = $student["0"]["email"];
	$disabled = 'disabled="disabled"';
}
$doc = JFactory::getDocument();
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_STUDENTS')));
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script type="text/javascript" language="javascript">
	function saveUser(pressbutton){
		var form = document.adminForm;		
		id = <?php echo $id; ?>;	
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		
		if (pressbutton=='save' || pressbutton=='apply'){
			if(id == 0){
				if (form['password'].value == "") {
					alert( "<?php echo JText::_("GURU_INSERT_PASS");?>" );
					return false;
				}
				else if(form['password'].value != form['password_confirm'].value){
					alert( "<?php echo JText::_("GURU_MATCH_PASS");?>" );
					return false;
				}
			}	
			
			if (form['username'].value == "") {
				alert("<?php echo JText::_("GURU_INSERT_USERNAME");?>");
				return false;
			}
			else if (form['firstname'].value == "") {
				alert( "<?php echo JText::_("GURU_INSERT_FIRSTNAME");?>" );
				return false;
			}
			else if (form['lastname'].value == "") {
				alert( "<?php echo JText::_("GURU_INSERT_LASTNAME");?>" );
				return false;
			}
			else if (form['email'].value == "") {
				alert( "<?php echo JText::_("GURU_INSERT_EMAIL");?>" );				
				return false;
			}
			else if(reg.test(form['email'].value) == false){
				alert('<?php echo JText::_("GURU_INSERT_VALIDMAIL"); ?>');
				return false;
			}
			checkGuruExistingUser();
			setTimeout(submitIFOk(pressbutton), 1000);
		}
		else{ 
			//submitform(pressbutton);
			form.task.value = pressbutton;
            form.submit();
		}	
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
			var req = jQuery.ajax({
				method: 'get',
				url: '<?php echo JURI::root()."index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=checkExistingUserU";?>&username='+username+'&email='+email+'&id=<?php echo $id; ?>',
				data: { 'do' : '1' },
				submit: function(response){
					jQuery("#ajax_response").empty().append(response);
				}
			});
		}

		if(email != ""){
			htmlvalue = "0";
			var req = jQuery.ajax({
				method: 'get',
				url: '<?php echo JURI::root()."index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=checkExistingUserE";?>&username='+username+'&email='+email+'&id=<?php echo $id; ?>',
				data: { 'do' : '1' },
				success: function(response){
					jQuery("#ajax_response").empty().append(response);
				}
			});
		}
		
		check_return = document.getElementById("ajax_response").innerHTML;
	}
	
	function trimString(str){
		str = str.toString();
		var begin = 0;
		var end = str.length - 1;
		while (begin <= end && str.charCodeAt(begin) < 33) { ++begin; }
		while (end > begin && str.charCodeAt(end) < 33) { --end; }
		return str.substr(begin, end - begin + 1);
	}
</script>
<div class="g_row clearfix">
	<div class="g_cell span12">
		<div>
			<div>
            	<div id="g_newstudentauthor" class="clearfix com-cont-wrap">
                	<?php echo $div_menu; //MENU TOP OF AUTHORS?>	
                    <h2><?php echo JText::_("GURU_NEW_STUDENT"); ?></h2>
                    <div class="row-fluid">
                        <div class="span12">
                            <h3><?php echo JText::_("GURU_STUDENT_ACCOUNT_DETAILS"); ?></h3>
                        </div>
                        <div class="span12 pagination-right  g_margin_bottom">
                            <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveUser('apply');" />
                            <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:saveUser('save');" />
                            <input type="button" class="btn btn-inverse" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents"); ?>';" />
                        </div>
                    </div>
                    
                    <form class="form-horizontal" id="adminForm" method="post" name="adminForm" action="<?php echo JUri::root() ?>index.php">
                        
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo JText::_('GURU_USERNAME'); ?>
                                <span class="star">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" value="<?php echo $username; ?>" <?php echo $disabled; ?> id="username" name="username" />
                            </div>
                        </div>
                        
                        <?php if($id == "0"){ ?>
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('GURU_PASSWORD'); ?>
                                    <span class="star">*</span>
                                </label>
                                <div class="controls">
                                    <input type="password" id="password" name="password" />
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label class="control-label">
                                    <?php echo JText::_('GURU_CONFIRM_PASSWORD'); ?>
                                    <span class="star">*</span>
                                </label>
                                <div class="controls">
                                    <input type="password" id="password_confirm" name="password_confirm" />
                                </div>
                            </div>
                        <?php
                            }
                        ?>
                        
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo JText::_('GURU_FIRS_NAME'); ?>
                                <span class="star">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" value="<?php echo $firstname; ?>" id="firstname" name="firstname" />
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo JText::_('GURU_LAST_NAME'); ?>
                                <span class="star">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" value="<?php echo $lastname; ?>" id="lastname" name="lastname" />
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo JText::_('GURU_COMPANY'); ?>
                                <span class="star">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" value="<?php echo $company; ?>" id="company" name="company" />
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo JText::_('GURU_PLUG_EMAIL'); ?>
                                <span class="star">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" value="<?php echo $email; ?>" <?php echo $disabled; ?> id="email" name="email" />
                            </div>
                        </div>
                        
                        <div id="ajax_response" style="display:none;"></div>
                        
                        <input type="hidden" name="option" value="com_guru" />
                        <input type="hidden" name="controller" value="guruAuthor" />
                        <input type="hidden" name="view" value="guruauthor" />
                        <input type="hidden" name="task" value="newStudent" />
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="action" value="<?php if(intval($id) != 0){echo "existing";}else{ echo "";} ?>" />
                    </form>
                  </div>
              </div>    
		</div>
	</div>	
</div>               