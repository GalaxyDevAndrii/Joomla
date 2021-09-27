<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformController extends JControllerLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		JHtml::_('jquery.framework');
		JHtml::_('behavior.core');

        JHtml::_('script', 'com_rsform/admin/placeholders.js', array('relative' => true, 'version' => 'auto'));
        JHtml::_('script', 'com_rsform/admin/script.js', array('relative' => true, 'version' => 'auto'));
        JHtml::_('script', 'com_rsform/admin/jquery.tag-editor.js', array('relative' => true, 'version' => 'auto'));
        JHtml::_('script', 'com_rsform/admin/jquery.caret.min.js', array('relative' => true, 'version' => 'auto'));
        JHtml::_('script', 'com_rsform/admin/validation.js', array('relative' => true, 'version' => 'auto'));
        JHtml::_('script', 'com_rsform/admin/tablednd.js', array('relative' => true, 'version' => 'auto'));

        JHtml::_('stylesheet', 'com_rsform/admin/style.css', array('relative' => true, 'version' => 'auto'));
        JHtml::_('stylesheet', 'com_rsform/admin/jquery.tag-editor.css', array('relative' => true, 'version' => 'auto'));
        JHtml::_('stylesheet', 'com_rsform/rsicons.css', array('relative' => true, 'version' => 'auto'));

		if (version_compare(JVERSION, '4.0', '>='))
		{
			JHtml::_('stylesheet', 'com_rsform/admin/style40.css', array('relative' => true, 'version' => 'auto'));
			JHtml::_('script', 'com_rsform/admin/script40.js', array('relative' => true, 'version' => 'auto'));
		}
		else
		{
			JHtml::_('stylesheet', 'com_rsform/admin/style30.css', array('relative' => true, 'version' => 'auto'));
		}

		if (RSFormProHelper::getConfig('global.disable_multilanguage'))
		{
			JFactory::getDocument()->addStyleDeclaration(".rsfp-translate-icon:before { content: ''; }");
		}
	}

	public function layoutsGenerate()
	{
		/* @var $model RsformModelForms */

		$model = $this->getModel('forms');
		$model->getForm();
		$model->_form->FormLayoutName = JFactory::getApplication()->input->getCmd('layoutName');
		$model->autoGenerateLayout();

		echo $model->_form->FormLayout;
		exit();
	}

	public function layoutsSaveName()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$name 	= JFactory::getApplication()->input->getCmd('formLayoutName');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_forms'))
			->set($db->qn('FormLayoutName') . ' = ' . $db->q($name))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));
		$db->setQuery($query)->execute();

		exit();
	}

	public function plugin()
	{
		JFactory::getApplication()->triggerEvent('onRsformBackendSwitchTasks');
	}
}