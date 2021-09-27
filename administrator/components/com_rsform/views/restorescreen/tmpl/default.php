<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('RSFP_OVERWRITE_WARNING');

$script = 'Joomla.submitbutton = function(task) {
	if (task === \'restore.start\') {
		if (jQuery(\'[name="jform[overwrite]"]:checked\').val() === \'1\' && !confirm(Joomla.JText._(\'RSFP_OVERWRITE_WARNING\'))) {
			return;
		}
	}
	
	Joomla.submitform(task);
}';

$this->document->addScriptDeclaration($script);
?>
<form enctype="multipart/form-data" action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm">
	<?php
	echo RSFormProAdapterGrid::sidebar();
	?>
	<fieldset class="form-horizontal">
		<?php
		echo $this->form->getField('backup')->renderField();
		echo $this->form->getField('overwrite')->renderField();
		echo $this->form->getField('keepid')->renderField();
		?>
	</fieldset>
	</div>
	
	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="option" value="com_rsform"/>
	</div>
</form>