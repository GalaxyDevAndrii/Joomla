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

$programs = $this->programs;
$n = count($programs);

?>
<style>
table.adminlist {
background-color:#E7E7E7;
border-spacing:1px;
color:#666666;
width:100%;
font-family:Arial,Helvetica,sans-serif;
font-size:11px;
}
</style>

<br />
<div>
<form id="adminForm" action="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_guru&controller=guruDays" name="adminForm1" method="post">
<div id="editcell">
<table class="adminlist" width="75%" align="center">
<tbody>
	<tr class="camp0"> 
	   <td align="left" width="40%"><?php echo JText::_("GURU_SELECT_PROGRAM"); ?></td>
	   <td align="left" width="60%">
	   		<select name="program">
				<?php /*<option value="0"><?php echo JText::_( 'GURU_DAY_SELPR_OPT' );?></option> */ ?>
				<?php foreach($programs as $program) {?>
	   			<option value="<?php echo $program->id;?>"><?php echo $program->name;?></option>
				<?php }?>
			</select>
	   </td>		
	</tr>
	<tr>
		<td colspan="2">
			<input type="button" class="btn" onClick="submitbutton()" name="duplicate" value="<?php echo JText::_( 'GURU_DAY_DUPLICATE_DAYS' );?>" />
			<input type="hidden" name="option" value="com_guru" />
			<input type="hidden" name="task" value="make_duplicate" />
			<input type="hidden" name="the_days" id="the_days" value="" />
			<input type="hidden" name="controller" value="guruDays" />			
		</td>
	</tr>
</tbody>
</table>
</div>
</div>
</form> 

<script type="text/javascript" language="javascript">
function check_if_are_days_selected() {
the_days = top.document.getElementById('boxchecked').value;
the_days_array = top.document.getElementById('boxchecked_id').value;
if(the_days==0)
	{
		window.parent.document.getElementById("sbox-window").close();
		alert (<?php echo "'".JText::_( 'GURU_DAY_MAKE_SEL_FOR_DUPLICATE' )."'";?>);
	}	
else
	window.parent.document.DuplicateForm.the_days.value = the_days_array;
}

check_if_are_days_selected();

function submitbutton() {
	var form = document.adminForm1;
    var program_id = form['program'].value;
	if (form['program'].value == 0) 
		{
			alert( "<?php echo JText::_("GURU_DAY_SELPR_JAVA_MSG");?>" );
		}
	else
		{
			window.parent.document.getElementById("program_id").value=program_id;
			window.parent.document.DuplicateForm.submit();
			//window.parent.document.getElementById("sbox-window").close();
		}

}
</script>