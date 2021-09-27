<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Registration extends JTable
{
	public $form_id 				= null;
	public $action					= 1;
	public $action_field			= '';
	public $groups 					= 2;
	public $vars 					= '';
	public $joomla_fields 			= '';
	public $profile_fields 			= '';
	public $activation 				= 1;
	public $cbactivation 			= 1;
	public $defer_admin_email 		= 0;
	public $user_activation_action 	= 0;
	public $admin_activation_action = 0;
	public $user_activation_url 	= '';
	public $admin_activation_url 	= '';
	public $user_activation_text 	= '';
	public $admin_activation_text 	= '';
	public $password_strength       = 0;
	public $itemid					= 0;
	public $published 				= 0;
	
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_registration', 'form_id', $db);
	}
	
	public function load($keys = null, $reset = true)
	{
		$result = parent::load($keys, $reset);
		
		if ($result)
		{
			$this->groups = explode(',', $this->groups);
			
			$this->vars = !empty($this->vars) ? @unserialize($this->vars) : array();
			if ($this->vars === false || !is_array($this->vars))
			{
				$this->vars = array();
			}
			
			if (!isset($this->vars['password']))
			{
				$this->vars['password'] = isset($this->vars['password1']) ? $this->vars['password1'] : '';
			}
			
			if (!isset($this->vars['email']))
			{
				$this->vars['email'] = isset($this->vars['email1']) ? $this->vars['email1'] : '';
			}

			$this->joomla_fields = !empty($this->joomla_fields) ? @unserialize($this->joomla_fields) : array();
			if ($this->joomla_fields === false || !is_array($this->joomla_fields))
			{
				$this->joomla_fields = array();
			}

			$this->profile_fields = !empty($this->profile_fields) ? @unserialize($this->profile_fields) : array();
			if ($this->profile_fields === false || !is_array($this->profile_fields))
			{
				$this->profile_fields = array();
			}
		}
		
		return $result;
	}
	
	// Validate data before save
	public function check()
	{
		if (is_array($this->groups))
		{
			$this->groups = implode(',', $this->groups);
		}
		
		if (is_array($this->vars))
		{
			$this->vars	= serialize($this->vars);
		}

		if (is_array($this->joomla_fields))
		{
			$this->joomla_fields = serialize($this->joomla_fields);
		}

		if (is_array($this->profile_fields))
		{
			$this->profile_fields = serialize($this->profile_fields);
		}
		
		return true;
	}

	public function hasPrimaryKey()
	{
		$db 	= $this->getDbo();
		$key 	= $this->getKeyName();
		$table	= $this->getTableName();

		$query = $db->getQuery(true)
			->select($db->qn($key))
			->from($db->qn($table))
			->where($db->qn($key) . ' = ' . $db->q($this->{$key}));

		return $db->setQuery($query)->loadResult() !== null;
	}
}