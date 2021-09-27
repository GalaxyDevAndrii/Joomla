<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
?>
<form method="post" action="index.php" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<fieldset>
		<legend class="rsfp-legend"><?php echo JText::_('RSFP_NEW_FORM_STEP_2_1'); ?></legend>
		<?php
		echo $this->form->getField('FormTitle')->renderField();
		echo $this->form->getField('FormLayoutName')->renderField();
		?>
	</fieldset>
	<fieldset>
		<legend class="rsfp-legend"><?php echo JText::_('RSFP_NEW_FORM_STEP_2_2'); ?></legend>
		<?php
		echo $this->form->getField('AdminEmail')->renderField();
		echo $this->form->getField('AdminEmailTo')->renderField();
		echo $this->form->getField('UserEmail')->renderField();
		?>
	</fieldset>
	<fieldset>
		<legend class="rsfp-legend"><?php echo JText::_('RSFP_NEW_FORM_STEP_2_3'); ?></legend>
		<?php
		echo $this->form->getField('SubmissionAction')->renderField();
		echo $this->form->getField('ReturnUrl')->renderField();
		echo $this->form->getField('Thankyou')->renderField();
		echo $this->form->getField('ScrollToThankYou')->renderField();
		echo $this->form->getField('ThankYouMessagePopUp')->renderField();
		?>
	</fieldset>
	<fieldset>
		<legend class="rsfp-legend"><?php echo JText::_('RSFP_NEW_FORM_STEP_3'); ?></legend>
		<?php echo $this->form->getField('PredefinedForm')->renderField(); ?>
	</fieldset>

	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="wizard.stepthree" />
</form>