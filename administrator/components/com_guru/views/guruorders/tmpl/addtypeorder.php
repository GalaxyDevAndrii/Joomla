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

$username = "";
$email = "";
$first_name = "";
$last_name = "";
$id = 0;

$session = JFactory::getSession();
$registry = $session->get('registry');
$new_order_by_username = $registry->get('NEW_ORDER_BY_USERNAME', "");

if(isset($new_order_by_username) && trim($new_order_by_username) != ""){
	$user_id = JFactory::getApplication()->input->get("userid", 0);
	if($user_id != 0){
		$username = trim($new_order_by_username);
		$registry->set('NEW_ORDER_BY_USERNAME', "");
	}	
}
$user_id = JFactory::getApplication()->input->get("userid", 0);
$show_username = true;
$show_email = true;
$show_password = true;
if($user_id != 0){
	$joomla_user = guruAdminModelguruOrder::getJoomlaUser($user_id);
	$username = $joomla_user["0"]["username"];
	$email = $joomla_user["0"]["email"];
	$name = $joomla_user["0"]["name"];
	
	$temp = explode(" ", $name);
	if(isset($temp) && count($temp) > 1){		
		$last_name = $temp[count($temp) - 1];	
		unset($temp[count($temp) - 1]);
		$first_name = implode(" ", $temp); 
	}
	else{
		if(count($temp) == 1){
			$first_name = $name;
		}
	}
	
	$show_username = false;
	$show_email = false;
	$show_password = false;
}

$usertype = JFactory::getApplication()->input->get("usertype", "");
?>

<?php //WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function ?>
<script type="text/javascript" src="<?php //echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js'; ?>"></script>

<script language="javascript" type="text/javascript">
	function changeProvince(){
		var country;
		country = document.getElementById('country').value;
		document.adminForm.provice.options[document.adminForm.provice.options.length] = new Option(country, country);
		document.adminForm.provice.value = country;
	}
	
	function validateEmail(email){
		validRegExp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i;
		if(email.search(validRegExp) == -1){
			return false;
		}
		return true;
	}
	
	function validateForm(){
		if(document.adminForm.firstname.value == ""){
			alert("<?php echo JText::_("GURU_SET_CUSTOMER_FIRST_NAME"); ?>");
			return false;
		}
		else if(document.adminForm.lastname.value == ""){
			alert("<?php echo JText::_("GURU_SET_CUSTOMER_LAST_NAME"); ?>");
			return false;
		}
		else if(document.adminForm.email.value == ""){
			alert("<?php echo JText::_("GURU_SET_CUSTOMER_EMAIL"); ?>");
			return false;
		}
		else if(!validateEmail(document.adminForm.email.value)){
			alert("<?php echo JText::_("GURU_INSERT_VALIDMAIL"); ?>");
			return false;
		}
		else if(document.adminForm.username.value == ""){
			alert("<?php echo JText::_("GURU_SET_CUSTOMER_USERNAME"); ?>");
			return false;
		}
<?php
		if($show_password === TRUE){
			echo 'else if(document.adminForm.password.value == ""){
					alert("Set customer password!");
					return false;
				}
				else if(document.adminForm.password_confirm.value == ""){
					alert("Set customer password_confirm!");
					return false;
				}
				else if(document.adminForm.password_confirm.value != document.adminForm.password.value){
					alert("The password is not the same!");
					return false;
				}';
		}
		
?>		
			
		return true;
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
			//document.adminForm.task.value = pressbutton;
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
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo $id; ?>',
				data: { 'do' : '1' },
				onComplete: function(response){
					document.getElementById("ajax_response").empty().adopt(response);
				}
			})*/

			url = 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo $id; ?>';

			jQuery.ajax({ url: url,
				method: 'get',
				asynchronous: 'false',
				success: function(response) {
					document.getElementById("ajax_response").empty().adopt(response);
				}
			});
		}

		if(email != ""){
			htmlvalue = "0";
			
			/*var req = jQuery.ajax({
				async: false,
				method: 'get',
				url: 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo $id; ?>',
				data: { 'do' : '1' },
				onComplete: function(response){
					document.getElementById("ajax_response").empty().adopt(response);
				}
			})*/

			url = 'index.php?option=com_guru&controller=guruPrograms&tmpl=component&format=raw&task=checkExistingUser&username='+username+'&email='+email+'&id=<?php echo $id; ?>';

			jQuery.ajax({ url: url,
				method: 'get',
				asynchronous: 'false',
				onComplete: function(response) {
					document.getElementById("ajax_response").empty().adopt(response);
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
	
	function submitbutton(task){
		if(task == "cancel"){
			location.href = "index.php?option=com_guru&controller=guruOrders";
			return true;			
		}
		else if(task == "continue"){
			<?php
			if($usertype == 1){
			?>
				checkGuruExistingUser();
				setTimeout(submitIFOk(task), 1000);
			<?php
			}
			else{
			?>
				document.adminForm.submit();
			<?php
			}
			?>
		}
	}	
</script>

<form action="" method="post" name="adminForm" id="adminForm">	
	<?php
		if($usertype == "" || $usertype == "1" || $usertype == "10"){
	?>
			<fieldset class="adminform">
				<div class="well"><?php echo JText::_("GURU_CREATE_CUSTOMER_PROFILE"); ?></div>
				<table class="admintable">
					<tbody>
						<tr>
							<td width="50%"><?php echo JText::_("GURU_FIRS_NAME"); ?><span style="color:#FF0000">*</span></td>
							<td><input type="text" value="<?php echo $first_name; ?>" size="30" id="firstname" name="firstname"><b>&nbsp;</b></td>
						</tr>
						<tr>
							<td><?php echo JText::_("GURU_LAST_NAME"); ?><span style="color:#FF0000">*</span></td>
							<td><input type="text" value="<?php echo $last_name; ?>" size="30" id="lastname" name="lastname"><b>&nbsp;</b></td>
						</tr>
						<tr>
							<td><?php echo JText::_("GURU_COMPANY"); ?></td>
							<td><input type="text" value="" size="30" id="company" name="company"></td>
						</tr>
						<tr>
							<td><?php echo JText::_("GURU_EMAIL"); ?><span style="color:#FF0000">*</span></td>
							<td><input type="text" value="<?php echo $email; ?>" <?php if($show_email === FALSE){echo 'disabled="disabled"';} ?> size="30" id="email" name="email"><b>&nbsp;</b></td>
						</tr>
			
						<tr>
							<td colspan="2"><h3><?php echo JText::_("GURU_LOGIN_INFORMATIONS"); ?></h3></td>
						</tr>
						<tr>
							<td><?php echo JText::_("GURU_USERNAME"); ?><span style="color:#FF0000">*</span></td>
							<td><input type="text" value="<?php echo $username; ?>" <?php if($show_username === FALSE){echo 'disabled="disabled"';} ?> size="30" id="username" name="username"><b>&nbsp;</b></td>
						</tr>
						<?php
						if($show_password === TRUE){
						?>
						<tr>
							<td><?php echo JText::_("GURU_PASSWORD"); ?><span style="color:#FF0000">*</span></td>
							<td><input type="password" size="30" id="password" name="password" value=""><b>&nbsp;</b></td>
						</tr>
						<tr>
							<td><?php echo JText::_("GURU_CONFIRM_PASSWORD"); ?><span style="color:#FF0000">*</span></td>
							<td><input type="password" size="30" id="password_confirm" name="password_confirm" value=""><b>&nbsp;</b></td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td colspan="2">
								<input type="button" class="btn" onclick="return submitbutton('continue');" value="<?php echo JText::_("GURU_CONTINUE"); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<input type="hidden" name="task" value="saveCustomer" />
            <div id="ajax_response" style="display:none;"></div>

	<?php
		}
		elseif($usertype == "2" || $usertype == "3"){
	?>
			<fieldset class="adminform">
				<legend><?php echo JText::_("GURU_NEW_CUSTOMER_LEGEND"); ?></legend>
					<label for="username">Username</label>
					<input type="text" value="" name="username" id="username">
					<input class="btn" type="submit" value="<?php echo JText::_("GURU_CONTINUE"); ?>">
					<input type="hidden" name="usertype" value="<?php echo $usertype; ?>"/>
					<input type="hidden" name="task" value="newCustomerByUsername"/>
			</fieldset>
	<?php
		}
	?>
	<input type="hidden" name="controller" value="guruOrders" />
	<input type="hidden" name="images" value="" />
	<input type="hidden" name="option" value="com_guru" />	
</form>
