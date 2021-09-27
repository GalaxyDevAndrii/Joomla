<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerCalculations extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('apply', 'save');
	}

	public function save()
	{
		$this->checkToken();

		$app   = JFactory::getApplication();
		$model = $this->getModel('calculation');
		$data  = $app->input->post->get('jform', array(), 'array');

		$row = $model->save($data);

		if ($this->getTask() == 'apply')
		{
			$this->setRedirect('index.php?option=com_rsform&view=calculation&cid=' . $row->id . '&formId=' . $row->formId . '&tmpl=component&update=1');
		}
		else
		{
			JFactory::getDocument()->addScriptDeclaration("window.opener.showCalculations(); window.close();");
		}
	}

	public function show()
	{
		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');

		$app->input->set('view', 'forms');
		$app->input->set('layout', 'edit_calculations');
		$app->input->set('formId', $formId);

		parent::display();

		$app->close();
	}

	public function remove()
	{
		$app 	= JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$id		= $app->input->getInt('id');
		$model	= $this->getModel('calculation');

		$model->delete($id);

		$app->input->set('view', 'forms');
		$app->input->set('layout', 'edit_calculations');
		$app->input->set('formId', $formId);

		parent::display();

		$app->close();
	}

	public function saveOrdering()
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$cids	= $app->input->get('cid', array(), 'array');
		$formId	= $app->input->getInt('formId',0);

		foreach ($cids as $key => $order)
		{
			$query = $db->getQuery(true)
				->update($db->qn('#__rsform_calculations'))
				->set($db->qn('ordering') . ' = ' . $db->q($order))
				->where($db->qn('id') . ' = ' . $db->q($key))
				->where($db->qn('formId') . ' = ' . $db->q($formId));

			$db->setQuery($query);
			$db->execute();
		}

		echo 'Ok';

		$app->close();
	}
}