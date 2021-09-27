<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

define('RSFP_MAPPING_INSERT', 0);
define('RSFP_MAPPING_DELETE', 2);
define('RSFP_MAPPING_UPDATE', 1);
define('RSFP_MAPPING_REPLACE', 3);

class RsformModelMappings extends JModelAdmin
{	
	public function getMapping()
	{
		$row = JTable::getInstance('RSForm_Mappings', 'Table');
		$row->load(JFactory::getApplication()->input->getInt('cid'));
		
		return $row;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.mappings', 'mappings', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		if ($loadData)
		{
			$mapping = $this->getMapping();

			if ($mapping->id)
			{
				$config = $mapping->getProperties();
				$config['user'] = $config['username'];

				try
				{
					if ($tables = $this->getTables($config))
					{
						$tableField = $form->getField('table');
						$form->setFieldAttribute('table', 'disabled', 'false');
						foreach ($form->getFieldset('connection') as $field)
						{
							$form->setFieldAttribute($field->fieldname, 'disabled', 'true');
						}

						$form->setFieldAttribute('table', 'disabled', 'false');

						foreach ($tables as $table)
						{
							$tableField->addOption($table, array('value' => $table));
						}
					}
				}
				catch (Exception $e)
				{
					// Nothing for now
				}
			}
		}

		return $form;
	}

	protected function loadFormData()
	{
		return $this->getMapping();
	}
	
	public function save($post)
	{
		$row = JTable::getInstance('RSForm_Mappings', 'Table');

		if (!$row->bind($post))
		{
			return false;
		}

		if (!$row->check())
		{
			return false;
		}
		
		$data = $where = $extra = $andor = array();
		
		if (!empty($post))
		{
			foreach ($post as $key => $value)
			{
				if (!strlen($value))
				{
					continue;
				}
				
				if (substr($key,0,2) == 'f_')
				{
					$datakey 		= substr($key, 2);
					$data[$datakey] = $value;
				}
				elseif (substr($key,0,2) == 'w_')
				{
					$wherekey 			= substr($key, 2);
					$where[$wherekey] 	= $value;
					$extra[$wherekey] 	= isset($post['o_'.$wherekey]) ? $post['o_'.$wherekey] : '=';
					$andor[$wherekey] 	= isset($post['c_'.$wherekey]) ? $post['c_'.$wherekey] : 0;
				}
			}
		}
		
		if (in_array($row->method, array(RSFP_MAPPING_INSERT, RSFP_MAPPING_UPDATE, RSFP_MAPPING_REPLACE)) && empty($data))
		{
			return false;
		}
		
		if ($row->method == RSFP_MAPPING_DELETE && empty($where))
		{
			return false;
		}
		
		$row->data 		= serialize($data);
		$row->wheredata = serialize($where);
		$row->extra 	= serialize($extra);
		$row->andor 	= serialize($andor);
		
		return $row->store();
	}
	
	public function remove()
	{
		$id 	= JFactory::getApplication()->input->getInt('mid');
		$db		= JFactory::getDbo();
		$row 	= JTable::getInstance('RSForm_Mappings', 'Table');
		
		$row->load($id);
		$formId = $row->formId;
		
		$row->delete($id);
		$row->reorder($db->qn('formId').'='.$db->q($formId));
	}
	
	public function getQuickFields()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/quickfields.php';
		return RSFormProQuickFields::getFieldNames();
	}
	
	// Get columns from a specific table
	public function getColumns($config)
	{
		$db 	= $this->getMappingDbo($config);
		$tables = $db->getTableList();
		$table 	= isset($config['table']) ? $config['table'] : '';
		
		if (empty($table) || !in_array($table, $tables))
		{
			return false;
		}
		else
		{
			return $db->getTableColumns($table);
		}
	}
	
	// Get tables in database
	public function getTables($config)
	{
		$db = $this->getMappingDbo($config);
		
		return $db->getTableList();
	}
	
	// Get database connector object
	public function getMappingDbo($config)
	{
		if ($config['connection'])
		{
			if (!strlen($config['database']))
			{
				throw new Exception(JText::_('RSFP_PLEASE_SELECT_A_DATABASE_FIRST'));
			}
			
			if (empty($config['driver']))
			{
				throw new Exception(JText::_('RSFP_PLEASE_SELECT_A_DRIVER_FIRST'));
			}
			
			$config['user'] = $config['username'];

			$database = JDatabaseDriver::getInstance($config);
			$database->connect();
			
			return $database;
		}
		else
		{
			return JFactory::getDbo();
		}
	}
}