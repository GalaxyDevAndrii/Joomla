<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;
?>
<div class="container-fluid">
	<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
		<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
			<label for="checkColumns" class="checkbox"><input type="checkbox" onclick="toggleCheckColumns();" id="checkColumns" /> <strong><?php echo JText::_('RSFP_CHECK_ALL'); ?></strong></label>
			<?php $i = 0; ?>
			<?php foreach ($this->staticHeaders as $header) { ?>
				<label for="column<?php echo $i; ?>" class="checkbox">
					<input type="checkbox" <?php if ($header->enabled) { ?>checked="checked"<?php } ?> name="staticcolumns[]" value="<?php echo $this->escape($header); ?>" id="column<?php echo $i; ?>" />
					<?php echo $header->label; ?>
				</label>
				<?php $i++; ?>
			<?php } ?>
			<?php foreach ($this->headers as $header) { ?>
				<label for="column<?php echo $i; ?>" class="checkbox">
					<input type="checkbox" <?php if ($header->enabled) { ?>checked="checked"<?php } ?> name="columns[]" value="<?php echo $this->escape($header); ?>" id="column<?php echo $i; ?>" />
					<?php echo $header->label; ?>
				</label>
				<?php $i++; ?>
			<?php } ?>
		</div>
	</div>
</div>
