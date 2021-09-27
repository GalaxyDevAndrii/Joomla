<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if (empty($this->emails))
{
	echo '<div class="alert alert-info">' . JText::_('COM_RSFORM_DIRECTORY_NO_EMAILS_HAVE_BEEN_CONFIGURED') . '</div>';

	return;
}
?>
<table class="table table-hover table-striped">
	<thead>
		<tr>
			<th><?php echo JText::_('RSFP_FORM_EMAILS_SUBJECT'); ?></th>
			<th width="55%"><?php echo JText::_('RSFP_FORM_EMAILS_TO'); ?></th>
			<th width="1%" nowrap="nowrap" class="title"><?php echo JText::_('RSFP_FORM_EMAILS_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($this->emails))
		{
			foreach ($this->emails as $row)
			{
				$onclick = "openRSModal('" . JRoute::_('index.php?option=com_rsform&task=emails.edit&type=directory&tmpl=component&formId=' . $row->formId . '&cid=' . $row->id) . "', 'Emails', '800x750'); return false;";
				?>
				<tr>
					<td>
						<a href="#" onclick="<?php echo $onclick; ?>"><?php echo strlen($row->subject) ? $this->escape($row->subject) : '<em>' . JText::_('RSFP_NO_SUBJECT_SPECIFIED') . '</em>'; ?></a>
					</td>
					<td><?php echo strlen($row->to) ? $this->escape($row->to) : '<em>' . JText::_('RSFP_NO_RECIPIENTS_SPECIFIED') . '</em>'; ?></td>
					<td width="20%" nowrap="nowrap">
						<button type="button" class="btn btn-primary" onclick="<?php echo $onclick; ?>"><?php echo JText::_('RSFP_EDIT'); ?></button>
						<button type="button" class="btn btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_ARE_YOU_SURE_DELETE'))) { removeEmail(<?php echo $row->id; ?>, 'directory'); }"><?php echo JText::_('RSFP_DELETE'); ?></button>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>