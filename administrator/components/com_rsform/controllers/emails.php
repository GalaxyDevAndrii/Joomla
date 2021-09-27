<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerEmails extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}

	public function edit()
	{
		JFactory::getApplication()->input->set('view', 'email');
		JFactory::getApplication()->input->set('layout', 'default');

		parent::display();
	}
	
	public function save()
	{
		$this->checkToken();

	    $app                = JFactory::getApplication();
		$model	            = $this->getModel('email');
		$type	            = $model->getType();
		$data               = $app->input->post->get('jform', array(), 'array');
		$data['message']    = $data['message_' . $data['mode']];
		$data['replyto'] 	= str_replace(';', ',', $data['replyto']);
		$data['to'] 		= str_replace(';', ',', $data['to']);
		$data['cc'] 		= str_replace(';', ',', $data['cc']);
		$data['bcc'] 		= str_replace(';', ',', $data['bcc']);

		$row = $model->save($data);
		
		if ($this->getTask() == 'apply')
        {
            $this->setRedirect('index.php?option=com_rsform&task=emails.edit&type='.$type.'&cid='.$row->id.'&formId='.$row->formId.'&tmpl=component&update=1');
        }
		else
		{
			JFactory::getDocument()->addScriptDeclaration("window.opener.updateEmails('{$type}');window.close();");
		}
	}

	public function changeLanguage()
	{
		$input	  = JFactory::getApplication()->input;
		$model	  = $this->getModel('email');
		$data     = $input->post->get('jform', array(), 'array');
		$formId   = $data['formId'];
		$cid	  = $data['id'];
		$language = $data['language'];
		$type	  = $model->getType();

		JFactory::getSession()->set('com_rsform.emails.emailId' . $cid . '.lang', $language);

		$this->setRedirect('index.php?option=com_rsform&task=emails.edit&type=' . $type . '&tmpl=component&formId=' . $formId . '&cid=' . $cid);
	}
	
	public function remove()
	{
		$db		= JFactory::getDbo();
        $app    = JFactory::getApplication();
		$cid	= $app->input->getInt('cid');
		$formId = $app->input->getInt('formId');
		$type	= $app->input->getCmd('type','additional');
		$view	= $type == 'additional' ? 'forms' : 'directory';
		
		if ($cid)
		{
		    $query = $db->getQuery(true)
                ->delete($db->qn('#__rsform_emails'))
                ->where($db->qn('id') . ' = ' . $db->q($cid));
			$db->setQuery($query);
			$db->execute();

			$references = array(
                $cid . '.fromname',
                $cid . '.subject',
                $cid . '.message'
            );

			// Delete translations
            $query->clear()
                ->delete($db->qn('#__rsform_translations'))
                ->where($db->qn('reference') . ' = ' . $db->q('emails'))
                ->where($db->qn('reference_id') . ' IN (' . implode(',', $db->q($references)) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		
		$app->input->set('view', $view);
		$app->input->set('layout', 'edit_emails');
		$app->input->set('tmpl', 'component');
		$app->input->set('formId', $formId);
		$app->input->set('type', $type);
		
		parent::display();

		$app->close();
	}
	
	public function update()
	{
        $app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$view	= $app->input->getCmd('type', 'additional') == 'additional' ? 'forms' : 'directory';
		
		$app->input->set('view', $view);
		$app->input->set('layout', 'edit_emails');
		$app->input->set('tmpl', 'component');
		$app->input->set('formId', $formId);
		
		parent::display();

		$app->close();
	}
}