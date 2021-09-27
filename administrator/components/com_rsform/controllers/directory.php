<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerDirectory extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}

	public function manage() {
        $app = JFactory::getApplication();

		$app->input->set('view', 'directory');
        $app->input->set('layout', 'default');
		
		parent::display();
	}
	
	public function edit() {
        $app = JFactory::getApplication();

		$app->input->set('view', 	'directory');
		$app->input->set('layout', 	'edit');
		
		parent::display();
	}
	
	public function saveOrdering()
	{
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $order)
		{
			$table = JTable::getInstance('RSForm_Directory_Fields', 'Table');

			if ($table->load(array('componentId' => $key, 'formId' => $formId), false))
			{
				$table->save(array(
					'ordering'    => $order
				));
			}
			else
			{
				$table->save(array(
					'componentId' => $key,
					'formId'      => $formId,
					'ordering'    => $order
				));
			}
		}
		
		echo 'Ok';
		exit();
	}
	
	public function saveDetails()
	{
		$cids	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$orders	= JFactory::getApplication()->input->get('order', array(), 'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $val)
		{
			$table = JTable::getInstance('RSForm_Directory_Fields', 'Table');

			if ($table->load(array('componentId' => $key, 'formId' => $formId), false))
			{
				$table->save(array(
					'indetails'   => $val
				));
			}
			else
			{
				$table->save(array(
					'componentId' => $key,
					'formId'      => $formId,
					'indetails'   => $val,
					'ordering'    => $orders[$key]
				));
			}
		}
		
		echo 'Ok';
		exit();
	}
	
	public function save() {
		$data = JFactory::getApplication()->input->get('jform',array(),'array');
		
		$model = $this->getModel('directory');
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('RSFP_SUBM_DIR_SAVED'));
		}
		
		$task = $this->getTask();
		switch ($task) {
			case 'save':
				$link = 'index.php?option=com_rsform&view=directory';
			break;
			
			case 'apply':
				$tab	= JFactory::getApplication()->input->getInt('tab', 0);
				$link	= 'index.php?option=com_rsform&view=directory&layout=edit&formId='.$data['formId'].'&tab='.$tab;
			break;
		}
		
		$this->setRedirect($link);
	}
	
	public function cancel() {
		$this->setRedirect('index.php?option=com_rsform&view=directory');
	}
	
	public function cancelform() {
		$app 	= JFactory::getApplication();
		$jform	= $app->input->get('jform',array(),'array');
		$formId = $jform['formId'];
		$app->redirect('index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId);
	}
	
	public function changeAutoGenerateLayout() {
        $app            = JFactory::getApplication();
		$formId 		= $app->input->getInt('formId');
		$name           = $app->input->get('ViewLayoutName');
		$status         = $app->input->getInt('status');

		$data = array(
		    'formId'                 => $formId,
            'ViewLayoutAutogenerate' => $status,
            'ViewLayoutName'         => $name
        );

		$table = JTable::getInstance('RSForm_Directory', 'Table');
		$table->save($data);

		$app->close();
	}
	
	public function saveName() {
        $app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$name   = $app->input->get('ViewLayoutName');

        $data = array(
            'formId'         => $formId,
            'ViewLayoutName' => $name
        );

		$table = JTable::getInstance('RSForm_Directory', 'Table');
		$table->save($data);

		$app->close();
	}

	public function saveSetting()
	{
		$app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$name   = $app->input->get('settingName');
		$value  = $app->input->getString('settingValue');

		$data = array(
			'formId'    => $formId,
			$name 		=> $value
		);

		$table = JTable::getInstance('RSForm_Directory', 'Table');
		$table->save($data);

		$app->close();
	}
	
	public function generate()
	{
		$app    = JFactory::getApplication();
		$formId = $app->input->getInt('formId');
		$layout = $app->input->getCmd('layoutName');
		$hideEmptyValues = $app->input->getInt('hideEmptyValues');
		$showGoogleMap = $app->input->getInt('showGoogleMap');

        $data = array(
            'formId' => $formId,
	        'ViewLayoutName' => $layout,
	        'HideEmptyValues' => $hideEmptyValues,
	        'ShowGoogleMap' => $showGoogleMap
        );

		$table = JTable::getInstance('RSForm_Directory', 'Table');
		$table->save($data);
		
		$model = $this->getModel('directory');
		$model->getDirectory();
		$model->_directory->formId = $formId;
		$model->_directory->ViewLayoutName = $layout;
		$model->_directory->HideEmptyValues = $hideEmptyValues;
		$model->_directory->ShowGoogleMap = $showGoogleMap;
		$model->autoGenerateLayout();
		
		echo $model->_directory->ViewLayout;

		$app->close();
	}
	
	public function remove() {
		$model	= $this->getModel('directory');
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		
		$model->remove($cids);
		
		$this->setRedirect('index.php?option=com_rsform&view=directory');
	}
}