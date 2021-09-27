<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (empty($this->calculations))
{
	echo '<div class="alert alert-info">' . JText::_('COM_RSFORM_NO_CALCULATIONS_HAVE_BEEN_CONFIGURED') . '</div>';

	return;
}
?>
<table class="table table-hover table-striped" id="calculationsTable">
	<thead>
	<tr>
		<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_RSFORM_CALCULATION_TOTAL_FIELD'); ?></th>
		<th class="text-center center">&nbsp;</th>
		<th><?php echo JText::_('COM_RSFORM_CALCULATION_EXPRESSION'); ?></th>
		<th class="text-center center">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
		<?php
		if (!empty($this->calculations))
		{
			foreach ($this->calculations as $row)
			{
				?>
				<tr>
					<td>
						<?php echo $this->escape($row->total); ?>
					</td>
					<td class="text-center center">
						=
					</td>
					<td>
						<?php echo $this->escape($row->expression); ?>
					</td>
					<td>

						<button type="button" class="btn btn-secondary" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=calculation&cid='.$row->id.'&tmpl=component&formId='.$this->formId); ?>', 'Mappings', '1000x800')"><?php echo JText::_('RSFP_EDIT'); ?></button>
						<button type="button" class="btn btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_DELETE_SURE_CALCULATION'))) { removeCalculation(<?php echo $row->id; ?>); }"><?php echo JText::_('RSFP_DELETE'); ?></button>
						<input type="hidden" name="calcid[]" value="<?php echo $row->id; ?>" />
						<input type="hidden" name="calcorder[]" value="<?php echo $row->ordering; ?>" />
					</td>
				</tr>
			<?php
			}
		}
		?>
	</tbody>
</table>