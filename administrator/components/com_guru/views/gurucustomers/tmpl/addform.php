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

$document=JFactory::getDocument();
$document->addScript(JURI::root()."administrator/components/com_guru/js/new_student.js");

?>

<script language="javascript" type="text/javascript">
	
	Joomla.submitbutton = function(pressbutton){
	//function submitbutton(pressbutton){
		action = "new_existing_student";
		username = document.adminForm.username.value;
		if(action == "new_existing_student" && username == "" && pressbutton !='cancel' ){
			alert("<?php echo JText::_("GURU_INSERT_USERNAME"); ?>");
			return false;
		}
		else{
			submitform(pressbutton);
		}
	}
</script>
<style type="text/css">
	#js-cpanel input[type="checkbox"], #js-cpanel input[type="radio"] {
    	height: 18px;
    	opacity: inherit !important;
    	position: inherit !important;
	}
</style>

<form name="adminForm" id="adminForm">
	<table> 
        <tr>
            <td style="padding: 7px" width="15%">
            	<?php echo JText::_("GURU_USERNAME"); ?>&nbsp;&nbsp;<input type="text" name="username" value=""> 
            </td> 
        </tr>
    </table>
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="action" value="new_existing_student" />
	<input type="hidden" name="controller" value="guruCustomers" />
    <input type="hidden" name="author_type" value="1" />
</form>