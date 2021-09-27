<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformViewRichtext extends JViewLegacy
{
	public function display($tpl = null)
	{
        if (!JFactory::getUser()->authorise('forms.manage', 'com_rsform'))
        {
            throw new Exception(JText::_('COM_RSFORM_NOT_AUTHORISED_TO_USE_THIS_SECTION'));
        }

		$this->noEditor = $this->get('NoEditor');
		$this->lang 	= $this->get('Lang');
		$this->formId	= $this->get('FormId');

        if ($this->noEditor)
		{
			$this->textarea = $this->get('Textarea');
		}
        else
		{
			$this->editor = $this->get('Editor');
		}

		$this->editorText = $this->get('EditorText');
		$this->editorName = $this->get('EditorName');

		parent::display($tpl);
	}
}