<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformControllerWizard extends RsformController
{
	public function add()
	{
		$app = JFactory::getApplication();
		$app->input->set('view', 'wizard');
		$app->input->set('layout', 'default');

		parent::display();
	}

	public function stepFinal()
	{
		$app            = JFactory::getApplication();
		$config         = JFactory::getConfig();
		$rsformConfig   = RSFormProConfig::getInstance();
		$data           = $app->input->post->get('jform', array(), 'array');
		$row            = JTable::getInstance('RSForm_Forms', 'Table');
		$predefinedForm = !empty($data['PredefinedForm']) ? $data['PredefinedForm'] : false;

		// Default Language
		$data['Lang'] = JFactory::getLanguage()->getDefault();

		// Set a title if missing
		if (!isset($data['FormTitle']) || !strlen($data['FormTitle']))
		{
			$data['FormTitle'] = JText::_('RSFP_FORM_DEFAULT_TITLE');
		}
		$data['FormName'] = JFilterOutput::stringURLSafe($data['FormTitle']);

		// Layout
		if (empty($data['FormLayoutName']))
		{
			$data['FormLayoutName'] = $rsformConfig->get('global.default_layout', 'responsive');
		}
		$data['LoadFormLayoutFramework'] = $rsformConfig->get('global.default_load_layout_framework', 1);

		// Admin Email
		if (!empty($data['AdminEmail']))
		{
			$data['AdminEmailFrom'] = $config->get('mailfrom');
			$data['AdminEmailFromName'] = $config->get('fromname');
			$data['AdminEmailSubject'] = JText::sprintf('RSFP_ADMIN_EMAIL_DEFAULT_SUBJECT', $data['FormTitle']);
			$data['AdminEmailText'] = JText::_('RSFP_ADMIN_EMAIL_DEFAULT_MESSAGE');
		}

		// User Email
		if (!empty($data['UserEmail']))
		{
			$data['UserEmailFrom'] = $config->get('mailfrom');
			$data['UserEmailFromName'] = $config->get('fromname');
			$data['UserEmailSubject'] = JText::_('RSFP_USER_EMAIL_DEFAULT_SUBJECT');
			$data['UserEmailText'] = JText::_('RSFP_USER_EMAIL_DEFAULT_MESSAGE');
		}

		// Save so we can have a form ID
		try
		{
			if (!$row->save($data))
			{
				throw new Exception($row->getError());
			}

			if ($predefinedForm)
			{
				$model = $this->getModel('Wizard', 'RsformModel');
				$model->addFields($row, $predefinedForm, $data);

				// Store it again, some data has changed
				if (!$row->store())
				{
					throw new Exception($row->getError());
				}
			}

			$this->setRedirect('index.php?option=com_rsform&view=forms&layout=edit&formId=' . $row->FormId);
		}
		catch (Exception $e)
		{
			$this->setRedirect('index.php?option=com_rsform&view=forms', $e->getMessage(), 'error');
		}
	}
}