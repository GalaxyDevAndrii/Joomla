<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php foreach ($this->layouts as $layoutGroup => $layouts) { ?>
<fieldset>
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_' . $layoutGroup); ?></legend>
	<?php foreach ($layouts as $layout) { ?>
		<div class="rsform_layout_box">
			<label for="formLayout<?php echo ucfirst($layout); ?>" class="radio">
				<input type="radio" id="formLayout<?php echo ucfirst($layout); ?>" name="FormLayoutName" value="<?php echo $layout; ?>" onclick="saveLayoutName(this.value);" <?php if ($this->form->FormLayoutName == $layout) { ?>checked="checked"<?php } ?> /><?php echo JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout));?><br/>
			</label>
			<?php echo JHtml::_('image', 'com_rsform/admin/layouts/' . $layout . '.gif', JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)), array('width' => 175), true); ?>
		</div>
	<?php } ?>
</fieldset>
<?php } ?>

<fieldset class="form-horizontal">
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_FORM_HTML_LAYOUT_OPTIONS'); ?></legend>
	<?php echo $this->jform->renderFieldset('layout_options'); ?>
</fieldset>

<fieldset>
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_FORM_HTML_LAYOUT'); ?></legend>
	<p>
		<button class="btn btn-warning" type="button" onclick="generateLayout(true);"><?php echo JText::_('RSFP_GENERATE_LAYOUT'); ?></button>
	</p>
	<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
		<div class="<?php echo RSFormProAdapterGrid::column(10); ?>">
			<?php echo RSFormProHelper::showEditor('FormLayout', $this->form->FormLayout, array('classes' => 'rs_100', 'id' => 'formLayout', 'syntax' => 'html', 'readonly' => $this->form->FormLayoutAutogenerate)); ?>
		</div>
		<div class="<?php echo RSFormProAdapterGrid::column(2); ?>">
			<button class="btn btn-secondary" type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div class="QuickAdd">
				<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
				<?php
				echo RSFormProHelper::generateQuickAddGlobal('generate');

				foreach ($this->quickfields as $field)
				{
					echo RSFormProHelper::generateQuickAdd($field, 'generate');
				}
				?>
			</div>
		</div>
	</div>
</fieldset>