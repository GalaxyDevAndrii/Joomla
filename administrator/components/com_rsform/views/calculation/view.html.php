<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformViewCalculation extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		$this->formId = $this->get('FormId');
		$this->form   = $this->get('Form');

		$displayPlaceholders    = RSFormProHelper::generateQuickAddGlobal('display', true);
		$quickfields            = $this->get('quickfields');
		foreach ($quickfields as $fields)
		{
			$displayPlaceholders = array_merge($displayPlaceholders, $fields['display']);
		}

		$this->document->addScriptDeclaration('RSFormPro.Placeholders = ' . json_encode(array_values($displayPlaceholders)) . ';');

		parent::display($tpl);
	}
}