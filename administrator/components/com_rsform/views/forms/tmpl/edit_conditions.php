<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (empty($this->conditions))
{
	echo '<div class="alert alert-info">' . JText::_('COM_RSFORM_NO_CONDITIONS_HAVE_BEEN_CONFIGURED') . '</div>';

	return;
}
?>
<table class="table table-hover table-striped">
	<thead>
		<tr>
			<th nowrap="nowrap"><?php echo JText::_('RSFP_CONDITION_FIELD_NAME'); ?></th>
			<th width="1%" class="title" nowrap="nowrap"><?php echo JText::_('RSFP_CONDITIONS_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->conditions as $row)
		{
			$onclick = "openRSModal('" . JRoute::_('index.php?option=com_rsform&view=conditions&layout=edit&tmpl=component&formId=' . $this->formId . '&cid=' . $row->id) . "', 'Conditions', '800x600'); return false;";
			?>
			<tr>
				<td>
					<a href="#" onclick="<?php echo $onclick; ?>">(<?php echo JText::_('RSFP_CONDITION_'.$row->action); ?>) <?php echo $this->escape(implode(', ', $row->ComponentNames)); ?></a>
					<?php if (!empty($row->details)) { ?>
						<ul>
							<li><strong><?php echo JText::_('RSFP_CONDITION_' . $row->condition); ?></strong></li>
							<?php foreach ($row->details as $detail) { ?>
								<li><small><?php echo $this->escape($detail->ComponentName) . ' ' . JText::_('RSFP_CONDITION_' . $detail->operator) . ' ' . $this->escape($detail->value); ?></small></li>
							<?php } ?>
						</ul>
					<?php } ?>
				</td>
				<td align="center" width="20%" nowrap="nowrap">
					<button type="button" class="btn btn-secondary" onclick="<?php echo $onclick; ?>"><?php echo JText::_('RSFP_EDIT'); ?></button>
					<button type="button" class="btn btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_CONDITION_DELETE_SURE'))) { conditionDelete(<?php echo $row->id; ?>); }"><?php echo JText::_('RSFP_DELETE'); ?></button>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>