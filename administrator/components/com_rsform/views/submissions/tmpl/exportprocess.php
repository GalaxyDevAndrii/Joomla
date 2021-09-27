<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('script', 'com_rsform/admin/export.js', array('relative' => true, 'version' => 'auto'));

JText::script('ERROR');
JText::script('COM_RSFORM_AN_ERROR_HAS_OCCURRED');

$this->document->addScriptDeclaration('jQuery(document).ready(function(){ exportProcess(0, ' . $this->limit . ', ' . $this->total . '); });');
?>
<div class="progressWrapper"><div class="progressBar" id="progressBar">0%</div></div>

<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" value="<?php echo $this->file; ?>" id="ExportFile" />
	<input type="hidden" value="<?php echo $this->exportType; ?>" id="ExportType" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rsform" />
</form>