<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('RSFP_BACKUP_SELECT');
JText::script('RSFP_ERROR');
JText::script('RSFP_STATUS');
JText::script('RSFP_STATUS_BACKING_UP_FORM_STRUCTURE_LEFT');
JText::script('RSFP_STATUS_BACKING_UP_FORM_SUBMISSIONS_LEFT');
JText::script('RSFP_STATUS_FINISHING_UP_SUBMISSIONS_FOR_FORM');
JText::script('RSFP_STATUS_COMPRESSING_FILES');
JText::script('RSFP_JSON_DECODING_ERROR');

JHtml::_('script', 'com_rsform/admin/backup.js', array('relative' => true, 'version' => 'auto'));

$script = 'RSFormPro.Backup.requestTimeOut.Seconds = ' . (float) $this->config->get('request_timeout') . ';';
?>
<form enctype="multipart/form-data" action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<?php
	echo RSFormProAdapterGrid::sidebar();
	?>
	<div id="formsList">
		<table class="table table-striped">
			<thead>
			<tr>
				<th style="width:1%" class="text-center">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th class="title"><?php echo JText::_('RSFP_FORM_TITLE'); ?></th>
				<th class="title"><?php echo JText::_('RSFP_FORM_NAME'); ?></th>
				<th class="title" nowrap="nowrap" width="1%"><?php echo JText::_('RSFP_FORM_ID'); ?></th>
				<th class="title" nowrap="nowrap" width="1%"><?php echo JText::_('RSFP_SUBMISSIONS'); ?></th>
			</tr>
			</thead>
			<?php
			foreach ($this->forms as $i => $row)
			{
				$script .= 'RSFormPro.Backup.submissionsCount[' . $row->FormId . '] = ' . $row->_allSubmissions . ';';

				?>
				<tr>
					<td><?php echo JHtml::_('grid.id', $i, $row->FormId); ?></td>
					<td><label for="cb<?php echo $i; ?>"><?php echo !empty($row->FormTitle) ? strip_tags($row->FormTitle) : '<em>' . JText::_('RSFP_FORM_DEFAULT_TITLE') . '</em>'; ?></label></td>
					<td><?php echo $this->escape($row->FormName); ?></td>
					<td width="1%"><?php echo $row->FormId; ?></td>
					<td width="1%"><?php echo $row->_allSubmissions; ?></td>
				</tr>
				<?php
			}
			?>
		</table>
		<fieldset class="form-horizontal">
			<?php
			echo $this->form->getField('submissions')->renderField();
			echo $this->form->getField('name')->renderField();
			?>
		</fieldset>
	</div>

	<div class="progressWrapper" style="display: none;"><div class="progressBar" id="progressBar">0%</div></div>
	</div>
	
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="option" value="com_rsform"/>
		<input type="hidden" name="boxchecked" value="0"/>
		
		<input type="hidden" name="key" id="backupKey" value="" />
	</div>
</form>

<?php
$this->document->addScriptDeclaration($script);