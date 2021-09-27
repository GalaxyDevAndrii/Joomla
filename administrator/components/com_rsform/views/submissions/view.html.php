<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewSubmissions extends JViewLegacy
{
    protected $previewArray = array();
    protected $staticHeaders = array();
    protected $headers = array();

	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('submissions.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		JFactory::getApplication()->input->set('hidemainmenu', true);
		
		$this->tooltipClass = RSFormProHelper::getTooltipClass();
		$this->formId = $this->get('formId');
		
		$layout = strtolower($this->getLayout());
		if ($layout == 'export')
		{
			$this->headers = $this->get('headers');
			$this->staticHeaders = $this->get('staticHeaders');

			for ($i = 0; $i < count($this->staticHeaders) + count($this->headers); $i++)
			{
				$this->previewArray[] = 'Value '.$i;
			}

			$this->formTitle = $this->get('formTitle');
			$this->exportSelected = $this->get('exportSelected');
			$this->exportSelectedCount = count($this->exportSelected);
			$this->exportFilteredCount = $this->get('Total');
			$this->exportAll = $this->exportSelectedCount == 0;
			$this->exportType = $this->get('exportType');
			$this->exportFile = $this->get('exportFile');
			$this->tabs = new RSFormProAdapterTabs('exportTabs');

			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $this->formTitle).']</small>','rsform');

			JToolbarHelper::custom('submissions.exporttask', 'archive', 'archive', JText::_('RSFP_EXPORT'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.manage');
		}
        elseif ($layout == 'import')
        {
            $this->headers = $this->get('headers');
            $this->staticHeaders = $this->get('staticHeaders');
            $this->formTitle = $this->get('formTitle');
            $this->previewData = $this->get('previewImportData');
            $this->countHeaders = $this->previewData ? count(reset($this->previewData)) : 0;

            $options = array(
                JHtml::_('select.option', '', JText::_('COM_RSFORM_IMPORT_IGNORE'))
            );
            foreach ($this->staticHeaders as $header)
            {
                $options[] = JHtml::_('select.option', $header->value, $header->label);
            }
            foreach ($this->headers as $header)
            {
                $options[] = JHtml::_('select.option', $header->value, $header->label);
            }
            $this->options = $options;
			$this->selected = $this->get('previewSelectedData');

            JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('COM_RSFORM_IMPORTING', $this->formTitle).']</small>','rsform');

	        JToolbarHelper::custom('submissions.importtask', 'archive', 'archive', JText::_('COM_RSFORM_IMPORT_SUBMISSIONS'), false);
	        JToolbarHelper::spacer();
	        JToolbarHelper::cancel('submissions.manage');
        }
		elseif ($layout == 'exportprocess')
		{
			$this->limit        = RSFormProHelper::getConfig('export.limit');
			$this->total        = $this->get('exportTotal');
			$this->file         = JFactory::getApplication()->input->getCmd('ExportFile');
			$this->exportType   = JFactory::getApplication()->input->getCmd('ExportType');
			$this->formId	    = $this->get('FormId');

			JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('RSFP_EXPORTING', $this->exportType, $this->get('formTitle')).']</small>','rsform');

			JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
			JToolbarHelper::custom('submissions.back', 'database', 'database', JText::_('RSFP_SUBMISSIONS'), false);
        }
        elseif ($layout == 'importprocess')
        {
            $this->limit    = 500;
            $this->total    = $this->get('importTotal');
            $this->formId	= $this->get('FormId');

            JToolbarHelper::title('RSForm! Pro <small>['.JText::sprintf('COM_RSFORM_IMPORTING', $this->get('formTitle')).']</small>','rsform');

            JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
            JToolbarHelper::custom('submissions.back', 'database', 'database', JText::_('RSFP_SUBMISSIONS'), false);
        }
		elseif ($layout == 'edit')
		{
			$this->formId = $this->get('submissionFormId');
			$this->submissionId = $this->get('submissionId');
			$this->submission = $this->get('submission');
			$this->staticHeaders = $this->get('staticHeaders');
			$this->staticFields = $this->get('staticFields');
			$this->fields = $this->get('editFields');

			JToolbarHelper::title('RSForm! Pro','rsform');

			JToolbarHelper::custom('submissions.exportpdf', 'archive', 'archive', JText::_('RSFP_EXPORT_PDF'), false);
			JToolbarHelper::spacer();
			JToolbarHelper::apply('submissions.apply');
			JToolbarHelper::save('submissions.save');
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.manage');
		}
		else
		{
		    $this->user = JFactory::getUser();
			$this->form = $this->get('FormProperties');
			$this->headers = $this->get('headers');
			$this->unescapedFields = $this->get('unescapedFields');
			$this->staticHeaders = $this->get('staticHeaders');
			$this->submissions = $this->get('submissions');
			$this->pagination = $this->get('pagination');
			$this->sortColumn = $this->get('sortColumn');
			$this->sortOrder = $this->get('sortOrder');
			$this->specialFields = $this->get('specialFields');
			$this->filter = $this->get('filter');

			$this->state         = $this->get('State');
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');

            if ($this->user->authorise('forms.manage', 'com_rsform'))
            {
                JToolbarHelper::custom('submissions.cancelform', 'previous', 'previous', JText::_('RSFP_BACK_TO_FORM'), false);
                JToolbarHelper::spacer();
            }

            // Choose columns
			JToolbarHelper::modal('columnsModal', 'icon icon-checkmark', 'RSFP_CUSTOMIZE_COLUMNS');
			JToolbarHelper::spacer();
			JToolbarHelper::custom('submissions.resend', 'mail', 'mail', JText::_('RSFP_RESEND_EMAILS'), true);

			if ($this->form->ConfirmSubmission)
			{
				JToolbarHelper::custom('submissions.confirm', 'checkmark-2', 'checkmark-2', JText::_('COM_RSFORM_CONFIRM_SUBMISSIONS'), true);
			}

            JToolbarHelper::modal('exportModal', 'icon-archive icon white', 'RSFP_EXPORT');
            JToolbarHelper::modal('importModal', 'icon-upload icon white', 'COM_RSFORM_IMPORT_SUBMISSIONS');
            JToolbarHelper::spacer();
			JToolbarHelper::editList('submissions.edit', JText::_('JTOOLBAR_EDIT'));
			JToolbarHelper::deleteList(JText::_('RSFP_ARE_YOU_SURE_DELETE'), 'submissions.delete', JText::_('JTOOLBAR_DELETE'));
			JToolbarHelper::spacer();
			JToolbarHelper::cancel('submissions.cancel', JText::_('JTOOLBAR_CLOSE'));

			JToolbarHelper::title('RSForm! Pro <small>['.$this->get('formTitle').']</small>','rsform');
		}
		
		parent::display($tpl);
	}
}