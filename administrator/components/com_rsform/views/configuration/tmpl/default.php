<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
?>
<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<?php
	echo RSFormProAdapterGrid::sidebar();
	?>
	<fieldset class="form-horizontal">
	<?php
	foreach ($this->fieldsets as $name => $fieldset)
	{
		// add the tab title
		$this->tabs->addTitle($fieldset->label, $fieldset->name);
		
		// prepare the content
		$this->fieldset =& $fieldset;
		$this->fields 	= $this->form->getFieldset($fieldset->name);

		$content = '';

        // set description if required
        if (isset($this->fieldset->description) && !empty($this->fieldset->description))
        {
            $content .= '<p>' . JText::_($this->fieldset->description) . '</p>';
        }

        foreach ($this->fields as $field)
        {
			// This is a workaround because our fields are named "global." and Joomla! uses the dot as a separator and transforms the JSON into [global][disable_multilanguage] instead of [global.disable_multilanguage].
			$content .= str_replace('"rsformConfig[global][', '"rsformConfig[global.', $this->form->renderField($field->fieldname));
        }
		
		// add the tab content
		$this->tabs->addContent($content);
	}
	
	$this->triggerEvent('onRsformBackendAfterShowConfigurationTabs', array($this->tabs));
	
	// render tabs
	$this->tabs->render();
	?>
	</fieldset>
	</div>
	
	<div>
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="option" value="com_rsform" />
		<input type="hidden" name="task" value="" />
	</div>
</form>