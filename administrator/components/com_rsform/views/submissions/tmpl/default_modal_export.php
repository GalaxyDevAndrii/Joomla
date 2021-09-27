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
			<p class="text-center">
				<button class="btn btn-large btn-lg btn-primary" onclick="Joomla.submitbutton('submissions.export.csv');" type="button"><?php echo JText::_('RSFP_EXPORT_CSV'); ?></button>
				<button class="btn btn-large btn-lg btn-secondary" onclick="Joomla.submitbutton('submissions.export.ods');" type="button"><?php echo JText::_('RSFP_EXPORT_ODS'); ?></button>
				<button class="btn btn-large btn-lg btn-secondary" onclick="Joomla.submitbutton('submissions.export.excelxml');" type="button"><?php echo JText::_('RSFP_EXPORT_EXCEL_XML'); ?></button>
				<button class="btn btn-large btn-lg btn-secondary" onclick="Joomla.submitbutton('submissions.export.excel');" type="button"><?php echo JText::_('RSFP_EXPORT_EXCEL'); ?></button>
				<button class="btn btn-large btn-lg btn-secondary" onclick="Joomla.submitbutton('submissions.export.xml');" type="button"><?php echo JText::_('RSFP_EXPORT_XML'); ?></button>
			</p>
		</div>
	</div>
</div>
