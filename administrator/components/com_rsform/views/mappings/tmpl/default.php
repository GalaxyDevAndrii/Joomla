<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('ERROR');
JText::script('RSFP_FORM_MAPPINGS_SELECT_TABLE');
JText::script('COM_RSFORM_PLEASE_ADD_SOME_DATA_TO_YOUR_COLUMNS_BEFORE_SAVING');

JHtml::_('script', 'com_rsform/admin/mappings.js', array('relative' => true, 'version' => 'auto'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsform'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <!-- this workaround is needed because browsers no longer honor autocomplete="off" -->
    <input type="text" style="display:none">
    <input type="password" style="display:none">

	<fieldset class="form-horizontal" id="connectionDetails">
	<?php
	foreach ($this->form->getFieldset('connection') as $field)
	{
		echo $field->renderField();
	}

	if (empty($this->mapping->id))
	{
		?>
		<div>
			<button class="btn btn-primary" type="button" id="connectBtn" onclick="mappingConnect();"><?php echo JText::_('RSFP_FORM_MAPPINGS_CONNECT'); ?></button>
			<?php echo JHtml::_('image', 'com_rsform/admin/loading.gif', '', array('id' => 'mappingloader', 'style' => 'vertical-align: middle; display: none;'), true); ?>
		</div>
		<?php
	}
	?>
	</fieldset>
	<fieldset class="form-horizontal">
	<?php
		echo $this->form->getField('table')->renderField();
	?>
	</fieldset>
	<div>
		<button class="btn btn-success" type="button" id="saveBtn" <?php if (empty($this->mapping->id)) { ?>style="display: none;"<?php } ?> onclick="Joomla.submitbutton('mappings.save')"><?php echo JText::_('JSAVE'); ?></button>
	</div>
	<?php echo JHtml::_('image', 'com_rsform/admin/loading.gif', '', array('id' => 'mappingloader2', 'style' => 'vertical-align: middle; display: none;'), true); ?>

	<div id="rsfpmappingColumns">
	<?php
	if (!empty($this->mapping->id) && $this->mapping->method != RSFP_MAPPING_DELETE)
	{
		try
		{
			echo RSFormProHelper::mappingsColumns($this->config, 'set', $this->mapping);
		}
		catch (Exception $e)
		{
			echo $this->escape(JText::sprintf('RSFP_DB_ERROR', $e->getMessage()));
		}
	}
	?>
	</div>

	<div id="rsfpmappingWhere">
	<?php
	if (!empty($this->mapping->id) && in_array($this->mapping->method, array(RSFP_MAPPING_UPDATE, RSFP_MAPPING_DELETE)))
	{
		try
		{
			echo RSFormProHelper::mappingsColumns($this->config, 'where', $this->mapping);
		}
		catch (Exception $e)
		{
			echo $this->escape(JText::sprintf('RSFP_DB_ERROR', $e->getMessage()));
		}
	}
	?>
	</div>

	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" id="mappingid" value="<?php echo $this->mapping->id; ?>" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
</form>

<style type="text/css">
body {
	padding: 20px !important;
}
</style>