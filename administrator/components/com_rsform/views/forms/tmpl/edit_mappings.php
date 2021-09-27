<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (empty($this->mappings))
{
	echo '<div class="alert alert-info">' . JText::_('COM_RSFORM_NO_MAPPINGS_HAVE_BEEN_CONFIGURED') . '</div>';

	return;
}
?>
<table class="table table-hover table-striped" id="mappingTable">
	<thead>
		<tr>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('RSFP_FORM_MAPPINGS_DATABASE_TABLE'); ?></th>
			<th align="center"><?php echo JText::_('RSFP_FORM_MAPPINGS_QUERY'); ?></th>
			<th width="1%" class="title" nowrap="nowrap"><?php echo JText::_('RSFP_FORM_MAPPINGS_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->mappings as $row)
		{
			?>
			<tr style="cursor: move;">
				<td width="1%" nowrap="nowrap">
					<input type="hidden" name="mpid[]" value="<?php echo $row->id; ?>" />
					<input type="hidden" name="mporder[]" value="<?php echo $row->ordering; ?>" />
					<?php echo !empty($row->database) ? $this->escape($row->database).'.' : ''; ?>`<?php echo $this->escape($row->table); ?>` (<?php echo $row->connection ? JText::_('RSFP_FORM_MAPPINGS_CONNECTION_REMOTE') : JText::_('RSFP_FORM_MAPPINGS_CONNECTION_LOCAL'); ?>)
				</td>
				<td>
					<?php
					try
					{
						echo wordwrap($this->escape(RSFormProHelper::getMappingQuery($row)), 150, '<br />', true);
					}
					catch (Exception $e)
					{
						echo $this->escape(JText::sprintf('RSFP_DB_ERROR', $e->getMessage()));
					}
					?>
				</td>
				<td align="center" width="20%" nowrap="nowrap">
					<button type="button" class="btn btn-secondary" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=mappings&cid='.$row->id.'&tmpl=component&formId='.$this->formId); ?>', 'Mappings', '1000x800')"><?php echo JText::_('RSFP_EDIT'); ?></button>
					<button type="button" class="btn btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_ARE_YOU_SURE_DELETE'))) mappingDelete(<?php echo $row->id; ?>);"><?php echo JText::_('RSFP_DELETE'); ?></button>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>