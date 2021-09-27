<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_rsform&amp;task=files.display" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div>
	<button type="button" class="btn btn-danger" onclick="window.close();"><?php echo JText::_('JCANCEL'); ?></button>
	<?php if ($this->canUpload) { ?>
		<table class="adminform">
		<tr>
			<th colspan="3"><?php echo JText::_('RSFP_UPLOAD_FILE'); ?></th>
		</tr>
		<tr>
			<td width="120"><label for="upload"><?php echo JText::_('RSFP_FILE'); ?>:</label></td>
			<td width="1%" nowrap="nowrap"><input class="input_box" id="upload" name="upload" type="file" size="57" /></td>
			<td><input class="btn btn-primary" type="button" value="<?php echo JText::_('RSFP_UPLOAD_FILE'); ?>" onclick="Joomla.submitbutton('files.upload')" /></td>
		</tr>
		</table>
	<?php } else { ?>
		<div class="alert alert-error">
			<?php echo JText::_('RSFP_CANT_UPLOAD'); ?>
		</div>
	<?php } ?>
		
	<table class="table table-striped">
		<thead>
		<tr>
			<th><strong><?php echo JText::_('RSFP_CURRENT_LOCATION'); ?></strong>
				<?php foreach ($this->elements as $folder) { ?>
					<a href="index.php?option=com_rsform&amp;task=files.files.display&amp;folder=<?php echo urlencode($folder->fullpath); ?>&amp;tmpl=component"><?php echo $this->escape($folder->name); ?></a> <?php echo DIRECTORY_SEPARATOR; ?>
				<?php } ?>
			</th>
		</tr>
		</thead>
		<tr>
			<td><a class="folder" href="index.php?option=com_rsform&amp;task=files.display&amp;folder=<?php echo urlencode($this->previous); ?>&amp;tmpl=component">..</a></td>
		</tr>
	<?php foreach ($this->folders as $folder) { ?>
		<tr>
			<td><a class="folder" href="index.php?option=com_rsform&amp;task=files.display&amp;folder=<?php echo urlencode($folder->fullpath); ?>&amp;tmpl=component"><?php echo $this->escape($folder->name); ?></a></td>
		</tr>
	<?php } ?>
	<?php foreach ($this->files as $file) { ?>
			<tr>
				<td><a class="file" href="javascript: void(0);" data-fullpath="<?php echo $this->escape($file->fullpath); ?>" onclick="window.opener.document.getElementById('UserEmailAttachFile').value = jQuery(this).data('fullpath'); window.close();"><?php echo $this->escape($file->name); ?></a></td>
			</tr>
	<?php } ?>
	</table>
		
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="folder" value="<?php echo $this->escape($this->current); ?>" />
	<input type="hidden" name="task" value="files.display" />
</div>
</form>