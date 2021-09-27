<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
?>
<form method="post" action="index.php?option=com_rsform" name="adminForm" id="adminForm">
	<p>
		<button class="btn btn-success" type="button" onclick="Joomla.submitbutton('richtext.apply');"><?php echo JText::_('JAPPLY'); ?></button>
		<button class="btn btn-success" type="button" onclick="Joomla.submitbutton('richtext.save');"><?php echo JText::_('JSAVE'); ?></button>
		<button class="btn btn-secondary" type="button" onclick="window.close();"><?php echo JText::_('JCANCEL'); ?></button>
	</p>

	<fieldset>
		<legend class="rsfp-legend"><?php echo JText::_('RSFP_EDITING_TEXT'); ?><?php if (!RSFormProHelper::getConfig('global.disable_multilanguage')) { ?> <small><?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_IN_SHORT', $this->lang); ?></small><?php } ?></legend>
	<?php
	if ($this->noEditor)
	{
		echo $this->textarea->input;
	}
	else
	{
		echo $this->editor->display($this->editorName, $this->escape($this->editorText), 500, 320, 70, 10);
	}
	?>
	</fieldset>

	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="opener" value="<?php echo $this->escape($this->editorName); ?>" />
	<?php
	if ($this->noEditor)
	{
		?>
		<input type="hidden" name="noEditor" value="1" />
		<?php
	}
	?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
</form>

<style type="text/css">
body {
	padding: 20px !important;
}
</style>