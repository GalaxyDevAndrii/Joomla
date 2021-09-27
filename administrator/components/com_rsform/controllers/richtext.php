<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerRichtext extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}
	
	public function show()
	{
        $app = JFactory::getApplication();

		$app->input->set('view', 'richtext');
        $app->input->set('layout', 'default');
		
		parent::display();
	}
	
	public function save()
	{
		$db 		= JFactory::getDbo();
		$app    	= JFactory::getApplication();
		$formsModel = $this->getModel('forms');
		$model		= $this->getModel('richtext');
		$lang   	= $formsModel->getLang();
		$formId 	= $model->getFormId();
		$opener 	= $model->getEditorName();
		$value  	= $app->input->post->get($opener, '', 'raw');
		$noEditor	= $model->getNoEditor();

		if ($formsModel->_form->Lang != $lang || (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') != 'en-GB'))
		{
			$model->saveTranslation($value);
		}
		else
		{
		    $query = $db->getQuery(true)
                ->update($db->qn('#__rsform_forms'))
                ->set($db->qn($opener) . ' = ' . $db->q($value))
                ->where($db->qn('FormId') . ' = ' . $db->q($formId));
			$db->setQuery($query);
			$db->execute();
		}

		/**
		 * Add feedback in the modal window
		 */
        $app->enqueueMessage(JText::_('RSFP_CHANGES_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->setRedirect('index.php?option=com_rsform&task=richtext.show&opener='.$opener.'&formId='.$formId.'&tmpl=component' . ($noEditor ? '&noEditor=1' : ''));
		}

        JFactory::getDocument()->addScriptDeclaration('window.close();');
	}
	
	public function preview()
	{
		echo $this->getModel('richtext')->getEditorText();
	}
}