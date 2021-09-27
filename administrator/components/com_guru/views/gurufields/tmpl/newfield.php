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
$field = $this->field;
$groups = $this->groups;

$type = "";
$name = "";
$field_code = "";
$published = "";
$required = "";
$options = "";
$group_id = "";

if(isset($field) && count($field) > 0){
	$type = $field["type"];
	$name = $field["name"];
	$field_code = $field["field_code"];
	$published = $field["published"];
	$required = $field["required"];
	$options = $field["options"];
	$group_id = $field["group_id"];
}

$display_options = "none";

if($type == "select" || $type == "radio" || $type == "checkbox"){
	$display_options = "table-row";
}

if(!isset($groups) || count($groups) == 0){
?>
	<div class="alert alert-error" style="text-align: center;">
		<?php echo JText::_("GURU_CREATE_FIRST_GROUPS"); ?>
	</div>
<?php
	return;
}

?>

<script type="text/javascript">
	function changeFieldType(type){
		if(type == "select" || type == "radio" || type == "checkbox"){
			document.getElementById("option").style.display = "";
		}
		else if(type == "text" || type == "textarea" || type == "url"){
			document.getElementById("option").style.display = "none";
		}
	}

	function validateForm(){
		var group_id = document.getElementById("group_id").value;
		var field_name = document.getElementById("field-name").value;
		var field_code = document.getElementById("field-code").value;
		var field_type = document.getElementById("field-type").value;
		var field_options = document.getElementById("field-options").value;

		if(group_id == 0){
			alert("<?php echo JText::_("GURU_ADD_FIELD_GROUP"); ?>");
			return false;
		}

		if(field_name == ""){
			alert("<?php echo JText::_("GURU_ADD_FIELD_NAME"); ?>");
			return false;
		}

		if(field_code == ""){
			alert("<?php echo JText::_("GURU_ADD_FIELD_CODE"); ?>");
			return false;
		}

		if(field_type == "select" || field_type == "radio" || field_type == "checkbox"){
			if(field_options == ""){
				alert("<?php echo JText::_("GURU_ADD_FIELD_OPTIONS"); ?>");
				return false;
			}
		}

		return true;
	}
</script>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post" onsubmit="return validateForm();">
	<table class="adminform">
		<tr>
			<td>
				<?php echo JText::_("GURU_TYPE"); ?>
			</td>
			<td>
				<select id="field-type" name="type" onchange="javascript:changeFieldType(this.value); return false">
					<option value="text" <?php if(trim($type) == "text"){echo 'selected="selected"';} ?> > Text </option>
					<option value="textarea" <?php if(trim($type) == "textarea"){echo 'selected="selected"';} ?> > Textarea </option>
					<option value="select" <?php if(trim($type) == "select"){echo 'selected="selected"';} ?> > Select </option>
					<option value="radio" <?php if(trim($type) == "radio"){echo 'selected="selected"';} ?> > Radio </option>
					<option value="checkbox" <?php if(trim($type) == "checkbox"){echo 'selected="selected"';} ?> > Checkbox </option>
					<option value="url" <?php if(trim($type) == "url"){echo 'selected="selected"';} ?> > URL </option>
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("GURU_FIELD_GROUP"); ?><font color="#ff0000">*</font>
			</td>
			<td>
				<select id="group_id" name="group_id">
					<option value="0"> <?php echo JText::_("GURU_SELECT_GROUP"); ?> </option>
					
					<?php
						if(isset($groups) && count($groups) > 0){
							foreach ($groups as $key => $value) {
								$selected = "";

								if($group_id == $value["id"]){
									$selected = 'selected="selected"';
								}

								echo '<option value="'.$value["id"].'" '.$selected.' >'.$value["name"].'</option>';
							}
						}
					?>
					
				</select>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("GURU_FIELD_NAME"); ?><font color="#ff0000">*</font>
			</td>
			<td>
				<input type="text" id="field-name" name="name" value="<?php echo trim($name); ?>" />
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("GURU_FIELD_CODE"); ?><font color="#ff0000">*</font>
			</td>
			<td>
				<input type="text" id="field-code" name="field_code" value="<?php echo trim($field_code); ?>" />
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("GURU_FIELD_PUBLISHED"); ?>
			</td>
			<td>
				<input type="hidden" name="published" value="0">
				<input type="checkbox" <?php if(intval($published) == 1){echo 'checked="checked"';} ?> value="1" class="ace-switch ace-switch-5" name="published"><span class="lbl"></span>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("GURU_FIELD_REQUIRED"); ?>
			</td>
			<td>
				<input type="hidden" name="required" value="0">
				<input type="checkbox" <?php if(intval($required) == 1){echo 'checked="checked"';} ?> value="1" class="ace-switch ace-switch-5" name="required"><span class="lbl"></span>
			</td>
		</tr>

		<tr id="option" style="display: <?php echo $display_options; ?>;">
			<td>
				<?php echo JText::_("GURU_FIELD_OPTIONS"); ?><font color="#ff0000">*</font>
			</td>
			<td>
				<textarea id="field-options" name="options"><?php echo $options; ?></textarea> <br />
				<?php echo JText::_("GURU_FIELD_OPTIONS_TIP"); ?>
			</td>
		</tr>
	</table>

	<hr />

	<input type="submit" class="btn btn-success" value="<?php echo JText::_("GURU_SAVE"); ?>" />

	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="save_field" />
	<input type="hidden" name="id" value="<?php echo intval($id); ?>" />
	<input type="hidden" name="controller" value="guruFields" />
</form>