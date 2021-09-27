<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ("joomla.aplication.component.model");


class guruModelguruConfig extends JModelLegacy {
	var $_configs = null;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$this->_id = 1;

	}

	function getConfig() {
		$db=  JFactory::getDBO();
		
		$sql="SELECT * 
			  FROM #__guru_config
			  WHERE id=".$this->_id;
		$db->setQuery($sql);
		$db->execute();
		$this->_configs= $db->loadObjectList();
		
		return $this->_configs;

	}

	function store () {
		$item = $this->getTable('guruConfig');
		$data = JFactory::getApplication()->input->post->getArray();
		
		if (isset($data['btnback'])) $data['btnback']=1; else $data['btnback']=0;
		if (isset($data['btnhome'])) $data['btnhome']=1; else $data['btnhome']=0;
		if (isset($data['btnnext'])) $data['btnnext']=1; else $data['btnnext']=0;
		if (isset($data['dofirst'])) $data['dofirst']=1; else $data['dofirst']=0;
			
		$database =  JFactory::getDBO();
		
		if (!$item->bind($data)){
			return JFactory::getApplication()->enqueueMessage($database->getErrorMsg(), 'error');
			return false;

		} 
		if (!$item->check()) {
			return JFactory::getApplication()->enqueueMessage($database->getErrorMsg(), 'error');
			return false;

		}
		
       $item->taskpage = $data['taskpage'];
       $item->daypage = $data['daypage'];
       $item->ctgpage = $data['ctgpage'];
       $item->pggpage = $data['pggpage'];
       $item->pgpage = $data['pgpage'];
       $item->taskpage = $data['taskpage'];
      
		if (!$item->store()) {
			return JFactory::getApplication()->enqueueMessage($database->getErrorMsg(), 'error');
			return false;

		}
		
		return true;

	}	
};
?>