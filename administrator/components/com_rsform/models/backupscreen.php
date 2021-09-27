<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelBackupscreen extends JModelAdmin
{
	protected $_data;
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.backupscreen', 'backupscreen', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData()
	{
		$data = array(
			'name' => RSFormProHelper::getConfig('backup.mask')
		);
		
		return $data;
	}
	
	protected function _buildQuery()
	{
		$query 	=  $this->_db->getQuery(true);
		
		$query->select($this->_db->qn('FormId'))
			  ->select($this->_db->qn('FormTitle'))
			  ->select($this->_db->qn('FormName'))
			  ->select($this->_db->qn('Lang'))
			  ->from($this->_db->qn('#__rsform_forms'))
			  ->order($this->_db->qn($this->getSortColumn()).' '.$this->_db->escape($this->getSortOrder()));
		
		return $query;
	}
	
	public function getForms()
	{
		if (empty($this->_data))
		{
			$this->_data = $this->_getList($this->_buildQuery());

			// Count submissions
			$query = $this->_db->getQuery(true)
				->select('COUNT(' . $this->_db->qn('SubmissionId') . ') AS ' . $this->_db->qn('total'))
				->select($this->_db->qn('FormId'))
				->from($this->_db->qn('#__rsform_submissions'))
				->group($this->_db->qn('FormId'));
			$allSubmissions = $this->_db->setQuery($query)->loadObjectList('FormId');
			
			foreach ($this->_data as $i => $row)
			{
				$lang = RSFormProHelper::getCurrentLanguage($row->FormId);
				if ($lang != $row->Lang)
				{
					if ($translations = RSFormProHelper::getTranslations('forms', $row->FormId, $lang))
					{
						foreach ($translations as $field => $value)
						{
							if (isset($row->{$field}))
							{
								$row->{$field} = $value;
							}
						}
					}
				}

				$row->_allSubmissions = isset($allSubmissions[$row->FormId]) ? $allSubmissions[$row->FormId]->total : 0;
			}
		}
		
		return $this->_data;
	}
	
	public function getSortColumn()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.forms.filter_order', 'filter_order', 'FormId', 'word');
	}
	
	public function getSortOrder()
	{
		return JFactory::getApplication()->getUserStateFromRequest('com_rsform.forms.filter_order_Dir', 'filter_order_Dir', 'ASC', 'word');
	}
	
	public function getIsWritable()
	{
		return is_writable($this->getTempDir());
	}
	
	public function getTempDir()
	{
		return JFactory::getConfig()->get('tmp_path');
	}
}