<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<select name="ExportRows">
	<option value="0" <?php echo $this->exportAll ? 'selected="selected"' : ''; ?>><?php echo JText::_('RSFP_EXPORT_ALL_ROWS'); ?></option>
	<option value="<?php echo implode(',', $this->exportSelected); ?>" <?php echo !$this->exportAll ? 'selected="selected"' : ''; ?>><?php echo JText::_('RSFP_EXPORT_SELECTED_ROWS'); ?> (<?php echo $this->exportSelectedCount; ?>)</option>
	<option value="-1"><?php echo JText::_('RSFP_EXPORT_FILTERED_ROWS'); ?> (<?php echo $this->exportFilteredCount; ?>)</option>
</select>

<table class="table table-striped">
	<tr>
		<td><input type="checkbox" onclick="toggleExportCheckboxes();" id="checkColumns" checked /></td>
		<td colspan="2"><label for="checkColumns"><strong><?php echo JText::_('RSFP_CHECK_ALL'); ?></strong></label></td>
	</tr>
	<thead>
	<tr>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT'); ?></th>
		<th class="title"><?php echo JText::_('RSFP_EXPORT_SUBMISSION_INFO'); ?></th>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT_COLUMN_ORDER'); ?></th>
	</tr>
	</thead>
	<?php $i = 1; ?>
	<?php foreach ($this->staticHeaders as $header) { ?>
		<tr>
			<td><input type="checkbox" onchange="updateCSVPreview();" class="exportCheckbox" name="ExportSubmission[<?php echo $header->value; ?>]" id="header<?php echo $i; ?>" value="<?php echo $header->value; ?>" <?php if ($header->enabled) { ?>checked="checked"<?php } ?> /></td>
			<td><label for="header<?php echo $i; ?>"><?php echo $header->label; ?></label></td>
			<td><input type="text" onkeyup="updateCSVPreview();" style="text-align: center" name="ExportOrder[<?php echo $header->value; ?>]" value="<?php echo $i; ?>" size="3"/></td>
		</tr>
		<?php $i++; ?>
	<?php } ?>
	<thead>
	<tr>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT'); ?></th>
		<th class="title"><?php echo JText::_('RSFP_EXPORT_COMPONENTS'); ?></th>
		<th class="title" width="5" nowrap="nowrap"><?php echo JText::_('RSFP_EXPORT_COLUMN_ORDER'); ?></th>
	</tr>
	</thead>
	<?php foreach ($this->headers as $header) { ?>
		<tr>
			<td><input type="checkbox" onchange="updateCSVPreview();" class="exportCheckbox" name="ExportComponent[<?php echo $header->value; ?>]" id="header<?php echo $i; ?>" value="<?php echo $header->value; ?>" <?php if ($header->enabled) { ?>checked="checked"<?php } ?> /></td>
			<td><label for="header<?php echo $i; ?>">
					<?php echo $header->label; ?>
				</label></td>
			<td><input type="text" onkeyup="updateCSVPreview();" style="text-align: center" name="ExportOrder[<?php echo $header->value; ?>]" value="<?php echo $i; ?>" size="3" /></td>
		</tr>
		<?php $i++; ?>
	<?php } ?>
</table>

<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('submissions.exporttask');" name="Export"><?php echo JText::_('RSFP_EXPORT');?></button>