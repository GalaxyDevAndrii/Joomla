<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

$mainframe = JFactory::getApplication();
?>

<table width="100%" class="table table-bordered" id="dirSubmissionsTable">
	<thead>
		<tr>
			<th></th>
			<th class="center text-center">
				<label class="checkbox inline" for="dirviewablecheck"><input type="checkbox" id="dirviewablecheck" onclick="dirSelectAll('dirviewable')" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" /> <strong><?php echo JText::_('RSFP_SUBM_DIR_LISTING'); ?></strong></label>
			</th>
			<th class="center text-center">
				<label class="checkbox inline" for="dirsearchablecheck"><input type="checkbox" id="dirsearchablecheck" onclick="dirSelectAll('dirsearchable')" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" /> <strong><?php echo JText::_('RSFP_SUBM_DIR_SEARCHABLE'); ?></strong></label>
			</th>
			<th class="center text-center">
				<label class="checkbox inline" for="direditablecheck"><input type="checkbox" id="direditablecheck" onclick="dirSelectAll('direditable')" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" /> <strong><?php echo JText::_('RSFP_SUBM_DIR_EDITABLE'); ?></strong></label>
			</th>
			<th class="center text-center">
				<label class="checkbox inline" for="dirindetailscheck"><input type="checkbox" id="dirindetailscheck" onclick="dirSelectAll('dirindetails')" onchange="dirAutoGenerate();" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" /> <strong><?php echo JText::_('RSFP_SUBM_DIR_IN_DETAILS'); ?></strong></label>
			</th>
			<th class="center text-center">
				<label class="checkbox inline" for="dirincsvcheck"><input type="checkbox" id="dirincsvcheck" onclick="dirSelectAll('dirincsv')" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" /> <strong><?php echo JText::_('RSFP_SUBM_DIR_IN_CSV'); ?></strong></label>
			</th>
		</tr>
	</thead>
	<?php
	foreach ($this->fields as $field) {
	    $mainframe->triggerEvent('onRsformBackendManageDirectoriesDecideEditable', array(&$field));
	?>
	<tr>
		<td width="15%" nowrap="nowrap" align="right">
			<?php echo $this->escape($this->getHeaderLabel($field)); ?>
			<input type="hidden" name="dirorder[]" value="<?php echo $field->ordering; ?>" />
			<input type="hidden" name="dircid[]" value="<?php echo $field->FieldId; ?>" />
		</td>
		<td class="center text-center">
			<input type="checkbox" name="dirviewable[]" <?php echo $field->viewable ? 'checked="checked"' : ''; ?> value="<?php echo $field->FieldId; ?>" />
		</td>
		<td class="center text-center">
			<input type="checkbox" name="dirsearchable[]" <?php echo $field->searchable ? 'checked="checked"' : ''; ?> value="<?php echo $field->FieldId; ?>" />
		</td>
		<td class="center text-center">
		<?php
		if ($field->allowEdit)
		{
		?>
			<input type="checkbox" name="direditable[]" <?php echo $field->editable ? 'checked="checked"' : ''; ?> value="<?php echo $field->FieldId; ?>" />
		<?php
		}
		?>
		</td>
		<td class="center text-center">
			<input type="checkbox" name="dirindetails[]" onchange="dirAutoGenerate();" <?php echo $field->indetails ? 'checked="checked"' : ''; ?> value="<?php echo $field->FieldId; ?>" />
		</td>
		<td class="center text-center">
			<input type="checkbox" name="dirincsv[]" <?php echo $field->incsv ? 'checked="checked"' : ''; ?> value="<?php echo $field->FieldId; ?>" />
		</td>
	</tr>
	<?php } ?>
</table>