<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007-2019 www.rsjoomla.com
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
	<div class="<?php echo RSFormProAdapterGrid::column(10); ?>">
		<fieldset class="form-horizontal">
			<legend class="rsfp-legend"><?php echo JText::_('RSFP_USER_EMAILS'); ?></legend>
			<div class="alert alert-info"><?php echo JText::_('RSFP_EMAILS_DESC'); ?></div>

			<legend class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_SENDER'); ?></legend>
			<?php
				echo $this->jform->renderFieldset('user_email_sender');
			?>
			<legend class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_RECIPIENT'); ?></legend>
			<?php
				echo $this->jform->renderFieldset('user_email_recipient');
			?>
			<legend class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_CONTENTS'); ?></legend>
			<?php
				echo $this->jform->renderFieldset('user_email_contents');
			?>
			<legend class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_ATTACHMENTS'); ?></legend>
			<?php
				echo $this->jform->renderFieldset('user_email_attachments');
			?>
			<?php $this->triggerEvent('onRsformBackendAfterShowUserEmail'); ?>
		</fieldset>
	</div>
	<div class="<?php echo RSFormProAdapterGrid::column(2); ?>">
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
	</div>
</div>