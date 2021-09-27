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


class guruAdminModelguruFields extends  JModelLegacy  {
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_total = 0;
    var $_pagination = null;
	protected $_context = 'com_guru.guruFields';

	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->setId((int)$cids[0]);
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
	
	function getPagination(){
			// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function getItems(){
		$config = new JConfig(); 
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
			
		if($limit!=0){
			$limit_cond = " LIMIT ".$limitstart.",".$limit." ";
		}
		else{
			$limit_cond = NULL;
		}
		
		$query = $this->getListQuery();

		$result = $this->_getList($query.$limit_cond);
		$this->_total = $this->_getListCount($query);
		
		return $result;
	}
	
	protected function getListQuery(){
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$and = "";
		
		$sql = "select f.* from #__guru_fields f where 1=1 ".$and." order by f.ordering asc";
		return $sql;
	}
	
	function orderUp(){	
		$db = JFactory::getDBO();
		$ids = JFactory::getApplication()->input->get("cid", "", "raw");
		$table = $this->getTable("guruFields");		
		$table->load($ids["0"]);		
		if(!$table->move(-1)){
			return false;
		}
		return true;
	}
	
	function orderDown(){
		$db = JFactory::getDBO();
		$ids = JFactory::getApplication()->input->get("cid", "", "raw");
		$table = $this->getTable("guruFields");		
		$table->load($ids["0"]);		
		if(!$table->move(1)){
			return false;
		}
		return true;
	}
	
	function setId($id) {
		$this->_id = $id;
		$this->_attribute = null;
	}

	function saveField(){
		$item = $this->getTable('guruFields');
		$data = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDbo();

		if(intval($data["id"]) == 0){
			$sql = "select max(`ordering`) as max_order from #__guru_fields";
			$db->SetQuery($sql);
			$db->execute();
			$max_ordering = $db->loadColumn();
			$max_ordering = @$max_ordering["0"];
			$new_ordering = intval($max_ordering) + 1;

			$data["ordering"] = $new_ordering;
		}

		if (!$item->bind($data)){
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		if (!$item->check()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		
		if (!$item->store()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		return true;
	}

	function saveGroup(){
		$item = $this->getTable('guruGroups');
		$data = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDbo();

		if(intval($data["id"]) == 0){
			$sql = "select max(`ordering`) as max_order from #__guru_groups";
			$db->SetQuery($sql);
			$db->execute();
			$max_ordering = $db->loadColumn();
			$max_ordering = @$max_ordering["0"];
			$new_ordering = intval($max_ordering) + 1;

			$data["ordering"] = $new_ordering;
		}

		if (!$item->bind($data)){
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		if (!$item->check()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		
		if (!$item->store()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		return true;
	}

	function saveOrder(){	
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");		
		$cid = array_values($cids);		
		$order = JFactory::getApplication()->input->get('order', array (0));
		$order = array_values($order);
		$total = count($cid);

		if(isset($order) && is_array($order) && count($order) > 0){
			$temp_order = array();
			$k = 1;
			
			foreach ($order as $key => $value) {
				$temp_order[] = $k;
				$k++;
			}

			$order = $temp_order;
		}
		
		for($i=0; $i<$total; $i++){
			$sql = "update #__guru_fields set `ordering`=".$order[$i]." where id=".$cid[$i];
			$db->setQuery($sql);

			if (!$db->execute()){
				return false;
			}
		}

		return true;
	}

	function saveOrderGroups(){	
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cidg', "", "raw");
		
		if(isset($cids) && is_array($cids) && count($cids) > 0){
			$k = 1;

			foreach($cids as $key=>$gid){
				$sql = "update #__guru_groups set `ordering`=".intval($k)." where id=".intval($gid);
				$db->setQuery($sql);

				if (!$db->execute()){
					return false;
				}

				$k++;
			}
		}
		
		return true;
	}

	function publish(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$sql = "update #__guru_fields set `published`='1' where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			return false;
		}
	
		return true;
	}

	function unpublish(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$sql = "update #__guru_fields set `published`='0' where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			return false;
		}
	
		return true;
	}

	function required(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$sql = "update #__guru_fields set `required`='1' where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			return false;
		}
	
		return true;
	}

	function unrequired(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$sql = "update #__guru_fields set `required`='0' where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			return false;
		}
	
		return true;
	}

	function publishGroup(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cidg', array(0), "raw");

		$sql = "update #__guru_groups set `published`='1' where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			return false;
		}
		else{
			$sql = "update #__guru_fields set `published`='1' where `group_id` in ('".implode("','", $cids)."')";
			$db->setQuery($sql);
			$db->execute();
		}
	
		return true;
	}

	function unpublishGroup(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cidg', array(0), "raw");

		$sql = "update #__guru_groups set `published`='0' where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->execute() ){
			return false;
		}
		else{
			$sql = "update #__guru_fields set `published`='0' where `group_id` in ('".implode("','", $cids)."')";
			$db->setQuery($sql);
			$db->execute();
		}
	
		return true;
	}

	function deleteGroup(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cidg', array(0), "raw");

		$sql = "delete from #__guru_groups where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		
		if (!$db->execute() ){
			return false;
		}
		else{
			$sql = "delete from #__guru_fields where `group_id` in ('".implode("','", $cids)."')";
			$db->setQuery($sql);
			$db->execute();
		}
	
		return true;
	}

	function deleteField(){
		$db = JFactory::getDbo();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$sql = "delete from #__guru_fields where `id` in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		
		if (!$db->execute() ){
			return false;
		}
	
		return true;
	}

	function getField(){
		$id = JFactory::getApplication()->input->get("id", '0', "raw");
		$db = JFactory::getDbo();
		$return = array();

		if(intval($id) > 0){
			$sql = "select * from #__guru_fields where `id`=".intval($id);
			$db->setQuery($sql);
			$db->execute();
			$return = $db->loadAssocList();
			$return = @$return["0"];
		}

		return $return;
	}

	function getFields($group_id){
		$db = JFactory::getDbo();

		$sql = "select * from #__guru_fields where `group_id`=".intval($group_id)." ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$fields = $db->loadObjectList();

		return $fields;
	}

	function getGroup(){
		$id = JFactory::getApplication()->input->get("id", '0', "raw");
		$db = JFactory::getDbo();
		$return = array();

		if(intval($id) > 0){
			$sql = "select * from #__guru_groups where `id`=".intval($id);
			$db->setQuery($sql);
			$db->execute();
			$return = $db->loadAssocList();
			$return = @$return["0"];
		}

		return $return;
	}

	function getGroups(){
		$db = JFactory::getDbo();
		
		$sql = "select * from #__guru_groups ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$groups = $db->loadAssocList();

		return $groups;
	}
	
};

?>