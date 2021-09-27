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
jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');

class guruAdminModelguruLogs extends JModelLegacy {
	var $_promos;
	var $_promo;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruLogs';
	
	function __construct () {
		parent::__construct();
		
		$mainframe =JFactory::getApplication();
		global $option;
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		
		if(JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")){
			JFactory::getApplication()->input->set("limitstart", "0");		
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}
		
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);	
	}
	
	function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function getItems(){
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$task = JFactory::getApplication()->input->get("task", "emails");
		$search = JFactory::getApplication()->input->get("search", "");
		$purchase_type = JFactory::getApplication()->input->get("purchase_type", "");
		$query = "";
		
		if($task == "emails"){
			$query = "SELECT l.*, u.name as user_name FROM #__guru_logs l, #__users u where u.email=l.to and l.buy_type='' and u.`id`=l.`userid`";
			
			if(trim($search) != ""){
				$query .= " and (l.to like '%".addslashes(trim($search))."%' OR l.subject like '%".addslashes(trim($search))."%' OR l.body like '%".addslashes(trim($search))."%' OR u.name like '%".addslashes(trim($search))."%')";
			}
			
			$query .= ' order by send_date desc';
		}
		elseif($task == "purchases"){
			$query = "SELECT l.*, u.name, u.username, p.name as course FROM #__guru_logs l, #__users u, #__guru_program p where u.email=l.to and l.buy_type <> '' and l.productid=p.id and u.`id`=l.`userid`";
			
			if(trim($search) != ""){
				$query .= " and (l.to like '%".addslashes(trim($search))."%' OR u.name like '%".addslashes(trim($search))."%' OR u.username like '%".addslashes(trim($search))."%' OR p.name like '%".addslashes(trim($search))."%')";
			}
			
			if(trim($purchase_type) != ""){
				$query .= " and l.buy_type='".trim($purchase_type)."'";
			}
			
			$query .= ' order by send_date desc';
		}
		
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		}
		else{
			$limit_cond = NULL;
		}

		$result = $this->_getList($query.$limit_cond);
		$this->_total = $this->_getListCount($query);
		
		return $result;
	}
	
	function getEmail(){
		$db = JFactory::getDbo();
		$id = JFactory::getApplication()->input->get("id", "0");
		
		$sql = "select * from #__guru_logs where id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$email = $db->loadAssocList();
		
		return $email["0"];
	}
};
?>