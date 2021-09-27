<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewDirectory extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('directory.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		// set title
		JToolbarHelper::title('RSForm! Pro', 'rsform');
		
		$layout = strtolower($this->getLayout());
		
		if ($layout == 'edit')
		{
			JFactory::getApplication()->input->set('hidemainmenu', true);

			JToolbarHelper::apply('directory.apply');
			JToolbarHelper::save('directory.save');
			JToolbarHelper::cancel('directory.cancel');

			JText::script('RSFP_AUTOGENERATE_LAYOUT_WARNING_SURE');

            $this->user = JFactory::getUser();

            if ($this->user->authorise('forms.manage', 'com_rsform'))
            {
                JToolbarHelper::spacer();
                JToolbarHelper::custom('directory.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
            }

            $this->form         = $this->get('Form');
			$this->directory	= $this->get('Directory');
			$this->formId		= JFactory::getApplication()->input->getInt('formId',0);
			$this->tab			= JFactory::getApplication()->input->getInt('tab', 0);
			$this->emails		= $this->get('emails');
			$this->fields		= RSFormProHelper::getDirectoryFields($this->formId);
			$this->quickfields	= $this->get('QuickFields');

			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EDITING_DIRECTORY', $this->get('formTitle')).']</small>','rsform');
		}
		elseif ($layout == 'edit_emails')
		{
			$this->emails = $this->get('emails');
		}
		else
		{
			$this->addToolbar();
			JToolbarHelper::title(JText::_('RSFP_SUBM_DIR'),'rsform');
			JToolbarHelper::deleteList('','directory.remove');

			$this->items		= $this->get('forms');
			$this->pagination	= $this->get('pagination');
			$this->sortColumn 	= $this->get('sortColumn');
			$this->sortOrder 	= $this->get('sortOrder');

			$this->state         = $this->get('State');
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');
		}
		
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		static $called;
		
		// this is a workaround so if called multiple times it will not duplicate the buttons
		if (!$called) {			
			require_once JPATH_COMPONENT.'/helpers/toolbar.php';
			RSFormProToolbarHelper::addToolbar('directory');
			
			$called = true;
		}
	}

	public function getHeaderLabel($field)
    {
        JFactory::getApplication()->triggerEvent('onRsformBackendGetHeaderLabel', array(&$field->FieldName, $this->formId));

        $staticHeaders = RSFormProHelper::getDirectoryStaticHeaders();

        if ($field->componentId < 0 && isset($staticHeaders[$field->componentId]))
        {
            return JText::sprintf('RSFP_DIRECTORY_SUBMISSION_HEADER', $field->FieldName);
        }

        return $field->FieldName;
    }
}