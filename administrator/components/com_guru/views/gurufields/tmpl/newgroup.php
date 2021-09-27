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

/*JHtml::_('behavior.framework');*/

defined ('_JEXEC') or die ("Go away.");
$id = JFactory::getApplication()->input->get("id", '0', "raw");
$group = $this->group;

$name = "";
$published = "";

if(isset($group) && count($group) > 0){
	$name = $group["name"];
	$published = $group["published"];
}

?>

<script type="text/javascript">
	function validateForm(){
		var group_name = document.getElementById("group-name").value;
		
		if(group_name == ""){
			alert("<?php echo JText::_("GURU_ADD_GROUP_NAME"); ?>");
			return false;
		}

		return true;
	}
</script>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post" onsubmit="return validateForm();">
	<table class="adminform">
		<tr>
			<td>
				<?php echo JText::_("GURU_NAME"); ?><font color="#ff0000">*</font>
			</td>
			<td>
				<input type="text" id="group-name" name="name" value="<?php echo trim($name); ?>" />
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("GURU_PUBLISHED"); ?>
			</td>
			<td>
				<input type="hidden" name="published" value="0">
				<input type="checkbox" <?php if(intval($published) == 1){echo 'checked="checked"';} ?> value="1" class="ace-switch ace-switch-5" name="published"><span class="lbl"></span>
			</td>
		</tr>
	</table>

	<hr />

	<input type="submit" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE"); ?>" />

	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="save_group" />
	<input type="hidden" name="id" value="<?php echo intval($id); ?>" />
	<input type="hidden" name="controller" value="guruFields" />
</form>