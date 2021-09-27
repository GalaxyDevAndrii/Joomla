<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007-2019 www.rsjoomla.com
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');

if (JFactory::getApplication()->input->getInt('update'))
{
	$this->document->addScriptDeclaration("window.opener.updateEmails('{$this->type}');");
}
?>
<form method="post" action="index.php?option=com_rsform" name="adminForm" id="adminForm">
<p>
	<button class="btn btn-success" type="button" onclick="Joomla.submitbutton('emails.apply');"><?php echo JText::_('JAPPLY'); ?></button>
	<button class="btn btn-success" type="button" onclick="Joomla.submitbutton('emails.save');"><?php echo JText::_('JSAVE'); ?></button>
	<button class="btn btn-secondary" type="button" onclick="window.close();"><?php echo JText::_('JCANCEL'); ?></button>
</p>

<fieldset class="form-horizontal">
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_FORM_EMAILS_NEW'); ?></legend>
	<?php
	if (!RSFormProHelper::getConfig('global.disable_multilanguage'))
	{
		if ($this->row->id)
		{
			echo $this->form->getField('language')->renderField();
		}
		else
		{
			?>
			<p><?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_IN', $this->lang, $this->translateIcon); ?></p>
			<input type="hidden" name="jform[language]" value="<?php echo $this->escape($this->lang); ?>" />
			<?php
		}
	}

	foreach ($this->form->getFieldsets() as $fieldset)
	{
		?>
		<legend class="rsfp-legend"><?php echo JText::_($fieldset->label); ?></legend>
		<?php
		if ($fields = $this->form->getFieldset($fieldset->name))
		{
			foreach ($fields as $field)
			{
				echo $field->renderField();
			}
		}
	}
	?>
</fieldset>

	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
</form>

<style type="text/css">
body {
	padding: 20px !important;
}
</style>