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


class guruAdminModelguruMediacategs extends JModelLegacy {
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;


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

	function getPagination(){
		if(empty($this->_pagination)){
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function getAllRows($parent, $level){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_media_categories where parent_id=".intval($parent);
		$db->setquery($sql);
		$db->execute();
		$result = $db->loadAssocList();
				
		if(isset($result) && is_array($result) && count($result) > 0){
			$level ++;			
			foreach($result as $key=>$value){
				$sql = "select count(id) from #__guru_media where category_id=".intval($value["id"]);
				$db->setquery($sql);
				$db->execute();
				$result = $db->loadResult();
				$value["nb_medias"] = $result;
				$value["level"] = $level;				
				$this->return_array[] = $value;				
				$this->getAllRows($value["id"], $level);
			}
		}		
		return $this->return_array;
	}
	
	function getItems(){
		$config = new JConfig();	
		$app = JFactory::getApplication('administrator');
		$limit		= $app->getUserStateFromRequest( 'limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int' );		
		$return = $this->getAllRows(0, 0);

		$filter_state = JFactory::getApplication()->input->get("filter_state");
		$filter_search = JFactory::getApplication()->input->get("filter_search", "", "raw");
		
		if($filter_state != "-1" || trim($filter_search) != ""){
			if(isset($return) && count($return) > 0){
				foreach($return as $key=>$value){
					if($filter_state == "1"){
						if($value["published"] == 0){
							unset($return[$key]);
						}
					}
					elseif($filter_state == "0"){
						if($value["published"] == 1){
							unset($return[$key]);
						}
					}
					
					if(trim($filter_search) != ""){
						$name = strtolower($value["name"]);
						$search = strtolower(trim($filter_search));
						if(strpos($name, $search) === FALSE){
							unset($return[$key]);
						}
					}
				}
			}	
		}

		$this->_total = count($return);
		if(isset($return) && count($return) > 0 && $limit!=0){
			$return = array_slice($return, (int)($limitstart), (int)($limit));
		}

		return $return;
	}
	
	function getCategories(){
		$db = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get('cid', 0, "raw");
		$ids = JFactory::getApplication()->input->get('id', '0');
		
		if(isset($cid["0"]) && $cid["0"] != 0){
			$id = $cid["0"];
		}
		else{
			$id = $ids;
		}
		$sql = "SELECT * FROM #__guru_media_categories WHERE id =".$id;
		$db->setQuery($sql);
		$db->execute();
		$categories = $db->loadObjectList();
		
		return $categories;
	}
	
	function getParent(){
		$db	=JFactory::getDBO();		
		$sql = "select parent_id from #__guru_media_categories";
		$db->setQuery($sql);
		$db->execute();
		$res = $db->loadObjectList();
	
		return $res;
	}
	
	function store(){
		$database = JFactory::getDBO();
		$item = $this->getTable('guruMediacategs');
		$data = JFactory::getApplication()->input->post->getArray();

		if(trim($data["id"]) == ""){
			$data["id"] = 0;
		}

		if(trim($data["parent_id"]) == ""){
			$data["parent_id"] = 0;
		}

		if(!isset($data["child_id"])){
			$data["child_id"] = 0;
		}

		if (!$item->bind($data)){
			echo $item->getError(); exit;
			$this->setError($item->getError());
			$return["0"] = false;
		}
		// Make sure the news record is valid
		if (!$item->check()){
			echo $item->getError(); exit;		
			$this->setError($item->getError());
			$return["0"] = false;
		}		
		// Store the web link table to the database
		if (!$item->store()){
			echo $item->getError(); exit;
			$this->setError( $item->getError() );
			$return["0"] = false;
		}
		$return["0"] = true;
		$return["1"] = $item->id;		
		return $return;	
	}
	
	function unpublish(){
		$db	=JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get('cid', "", "raw");		
		foreach($cid as $key=>$id){			
			$sql = "update #__guru_media_categories set published=0 where id=".$id;
			$db->setQuery($sql);
			if(!$db->execute()){
				return false;
			}
		}
		return true;
	}
	
	function publish(){
		$db	=JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get('cid', "", "raw");		
		foreach($cid as $key=>$id){			
			$sql = "update #__guru_media_categories set published=1 where id=".$id;
			$db->setQuery($sql);
			if(!$db->execute()){
				return false;
			}
		}
		return true;
	}
	
	public function remove(){
		$db = JFactory::getDBO();
		$data = JFactory::getApplication()->input->post->getArray();
		$ids = $data['cid'];	 
		if(!empty($ids)){
			foreach($ids as $key => $id){
				$sql = "select count(id) from #__guru_media where category_id=".$id;
				$db->setQuery($sql);
				$db->execute();
				$res = $db->loadColumn();
				$res = $res[0];
				
				$sql = "select count(*) from #__guru_media_categories where parent_id=".$id;
				$db->setQuery($sql);
				$db->execute();
				$res1 = $db->loadColumn();
				$res1 = $res1[0];
				$app = JFactory::getApplication('administrator');

				if($res >0 || $res1>0){
					$msg = JText::_('GURU_NO_DELETE_MEDIACAT');
					$app->enqueueMessage($msg, 'error');
            		$app->redirect('index.php?option=com_guru&controller=guruMediacategs');
				}
				
				$sql = "delete from #__guru_media_categories where id=".$id;
				$db->setQuery($sql);
				$db->execute();
			}
		}
		return true;	
	}
};
?>