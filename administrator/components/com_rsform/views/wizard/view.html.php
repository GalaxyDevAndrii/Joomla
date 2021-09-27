<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformViewWizard extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		JToolbarHelper::title('RSForm! Pro','rsform');
		JToolbarHelper::save('wizard.stepfinal', JText::_('RSFP_FINISH'));
		JToolbarHelper::cancel('forms.cancel');

		$this->form = $this->get('Form');

		parent::display($tpl);
	}
}