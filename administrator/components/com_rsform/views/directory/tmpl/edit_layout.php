<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<fieldset>
	<?php foreach (array('dir-inline', 'dir-2lines', 'dir-inline-title', 'dir-2lines-title', 'dir-2cols') as $layout) { ?>
	<div class="rsform_layout_box">
		<label for="formLayout<?php echo ucfirst($layout); ?>" class="radio">
			<input type="radio" id="formLayout<?php echo ucfirst($layout); ?>" name="jform[ViewLayoutName]" value="<?php echo $layout; ?>" onclick="saveDirectoryLayoutName(this.value);" <?php if ($this->directory->ViewLayoutName == $layout) { ?>checked="checked"<?php } ?> /> <?php echo JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout));?>
		</label>
		<?php echo JHtml::_('image', 'com_rsform/admin/layouts/' . $layout . '.gif', JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)), array(), true); ?>
	</div>
	<?php } ?>
</fieldset>

<fieldset class="form-horizontal">
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_SUBM_DIR_DETAILS_LAYOUT'); ?></legend>
	<?php echo $this->form->renderFieldset('layout'); ?>

	<p>
		<button class="btn btn-warning" type="button" onclick="generateDirectoryLayout(true);"><?php echo JText::_('RSFP_GENERATE_LAYOUT'); ?></button>
	</p>
	<table width="100%">
		<tr>
			<td valign="top">
			   <table width="98%" style="clear:both;">
					<tr>
						<td>
							<?php echo RSFormProHelper::showEditor('jform[ViewLayout]', $this->directory->ViewLayout, array('classes' => 'rs_100', 'id' => 'ViewLayout', 'syntax' => 'html', 'readonly' => $this->directory->ViewLayoutAutogenerate)); ?>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top" width="1%" nowrap="nowrap">
				<button class="btn btn-secondary" type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
				<div class="QuickAdd">
					<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
					<?php
					echo RSFormProHelper::generateQuickAddGlobal();
					
					foreach ($this->quickfields as $field)
					{
						echo RSFormProHelper::generateQuickAdd($field, 'display');
					}
					?>
				</div>
			</td>
		</tr>
	</table>
</fieldset>