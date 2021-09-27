<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerMappings extends RsformController
{
	public function getTables()
	{
		$app    = JFactory::getApplication();
		$model	= $this->getModel('mappings');
		$config	= $app->input->get('jform', array(), 'array');
		
		try
		{
			$tables = $model->getTables($config);

			echo json_encode(array('tables' => $tables));
		}
		catch (Exception $e)
		{
			echo json_encode(array('message' => $e->getMessage()));
		}
		
		$app->close();
	}
	
	public function getColumns()
	{
		try
		{
			$app    = JFactory::getApplication();
			$cid    = $app->input->getInt('cid');
			$config	= $app->input->get('jform', array(), 'array');
			$type   = $app->input->get('type', 'set');
			$row    = null;
			
			if ($cid)
			{
				$row = JTable::getInstance('RSForm_Mappings', 'Table');
				$row->load($cid);
			}

			echo RSFormProHelper::mappingsColumns($config, $type, $row);
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
		
		$app->close();
	}
	
	public function save()
	{
		$app    = JFactory::getApplication();
		$data   = $app->input->post->getArray(array(), null, 'raw');
		$config	= $app->input->get('jform', array(), 'array');
		$data   = array_merge($data, $config);

		unset($data['jform']);

		$model = $this->getModel('mappings');
		$model->save($data);

		JFactory::getDocument()->addScriptDeclaration("window.opener.mappingsShow(); window.close();");
	}
	
	public function saveOrdering()
	{
		$db   = JFactory::getDbo();
		$data = JFactory::getApplication()->input->post->get('cid', array(), 'array');
		
		foreach ($data as $id => $val)
		{
			$query = $db->getQuery(true)
						->update($db->qn('#__rsform_mappings'))
						->set($db->qn('ordering') . '=' . $db->q($val))
						->where($db->qn('id') . '=' . $db->q($id));

			$db->setQuery($query)
			   ->execute();
		}
		
		JFactory::getApplication()->close();
	}
	
	public function remove()
	{
		$input  = JFactory::getApplication()->input;
		$model  = $this->getModel('mappings');
		$formId = $input->getInt('formId');
		
		$model->remove();
		
		$input->set('view', 	'forms');
		$input->set('layout', 	'edit_mappings');
		$input->set('tmpl', 	'component');
		$input->set('formId', 	$formId);
		
		parent::display();
		
		JFactory::getApplication()->close();
	}
	
	public function showMappings()
	{
		$input  = JFactory::getApplication()->input;
		$formId = $input->getInt('formId');
		
		$input->set('view', 	'forms');
		$input->set('layout', 	'edit_mappings');
		$input->set('tmpl', 	'component');
		$input->set('formId', 	$formId);
		
		parent::display();
		
		JFactory::getApplication()->close();
	}
}