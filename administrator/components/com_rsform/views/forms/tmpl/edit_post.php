<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('RSFP_POST_NAME_PLACEHOLDER');
JText::script('RSFP_POST_VALUE_PLACEHOLDER');
JText::script('RSFP_POST_HEADERS_NAME_PLACEHOLDER');
JText::script('RSFP_POST_HEADERS_VALUE_PLACEHOLDER');
JText::script('RSFP_POST_ARE_YOU_SURE_DELETE_THIS_FIELD');
JText::script('RSFP_POST_ARE_YOU_SURE_DELETE_THIS_HEADER');
?>
<fieldset class="form-horizontal">
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_POST_TO_LOCATION'); ?></legend>
	<?php echo $this->postJForm->renderFieldset('params'); ?>
</fieldset>
<fieldset>
	<legend class="rsfp-legend"><?php echo JText::_('RSFP_POST_TO_LOCATION_ADVANCED'); ?></legend>
	<?php echo $this->postJForm->renderFieldset('advanced'); ?>
</fieldset>