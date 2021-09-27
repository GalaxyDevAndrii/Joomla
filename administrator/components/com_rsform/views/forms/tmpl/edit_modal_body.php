<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

$tabs = new RSFormProAdapterTabs('editModalTabs');
$tabs->addTitle('RSFP_COMPONENTS_GENERAL_TAB', 'rsfptab0');
$tabs->addContent('');
$tabs->addTitle('RSFP_COMPONENTS_VALIDATIONS_TAB', 'rsfptab1');
$tabs->addContent('');
$tabs->addTitle('RSFP_COMPONENTS_ATTRIBUTES_TAB', 'rsfptab2');
$tabs->addContent('');
$tabs->addTitle('RSFP_COMPONENTS_FREETEXT_TAB', 'rsfptab3');
$tabs->addContent('<div id="rsfp-editor-container" class="rsfp-hidden">' . RSFormProHelper::getEditor()->display('param[TEXT]', '', '100%', '120px', 40, 12, false, 'TEXT', null, null) . '</div>');
?>
	<div id="rsfp-tabs">
		<?php $tabs->render(); ?>
	</div>