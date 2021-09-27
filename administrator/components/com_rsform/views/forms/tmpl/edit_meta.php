<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo RSFormProAdapterGrid::row(); ?>">
	<div class="<?php echo RSFormProAdapterGrid::column(12); ?>">
		<fieldset class="form-horizontal">
			<legend class="rsfp-legend"><?php echo JText::_('RSFP_FORM_META_TAGS'); ?></legend>
			<?php
				echo $this->jform->renderFieldset('meta');
			?>
		</fieldset>
	</div>
</div>