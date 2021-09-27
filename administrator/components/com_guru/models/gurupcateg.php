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

class guruAdminModelguruPcateg extends JModelLegacy {
	var $_promos;
	var $_promo;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;
	protected $context = 'com_guru.guruPcategs';
	
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
	
	
	
   function getPagination() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function getAllRows($parent, $level){
		$db = JFactory::getDbo();
		$sql = "SELECT id, description, name, child_id as cid, parent_id as pid, ordering, published FROM #__guru_category, #__guru_categoryrel WHERE #__guru_category.id = #__guru_categoryrel.child_id and #__guru_categoryrel.parent_id=".intval($parent)." ORDER BY `ordering` ASC";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		
		if(isset($result) && is_array($result) && count($result) > 0){
			$level ++;
			foreach($result as $key=>$value){
				$value["level"] = $level;
				$this->return_array[] = $value;
				$this->getAllRows($value["id"], $level);
			}
		}
		return @$this->return_array;
	}
	
	function getItems(){
		$config = new JConfig();	
		$app = JFactory::getApplication('administrator');
		$limistart = $app->getUserStateFromRequest($this->context.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', $config->list_limit);
		
		$return = $this->getAllRows(0, 0);

		$this->_total = count($return);
		
		if(isset($return) && count($return) > 0 && $limit!=0){
			$return = array_slice($return, (int)($limistart), (int)($limit));
		}
		
		return $return;
	}
	
	function setId($id) {
		$this->_id = $id;
		$this->_promo = null;
	}
	
	function getCategoryCount(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_category";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}
	
	function getParentId($id){
		$db = JFactory::getDBO();
		$sql = "select parent_id from #__guru_categoryrel where child_id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}	
	function getCateg() {
		if (empty ($this->_promo)) { 
			$this->_promo = $this->getTable("guruPcateg");
			
			if($this->_id > 0){
			} 
			else{
				$id=JFactory::getApplication()->input->get("cid","0");
				if ($id>0) $this->_id=intval($id);
			}
			$this->_promo->load($this->_id);
			
			$pid = $this->getParentId($this->_id);
			$this->_promo->pid = $pid;
			JFactory::getApplication()->input->set("pid",$pid);
		}
		return $this->_promo;
	}
	
	function store () {			
		$db = JFactory::getDBO();
		$item = $this->getTable('guruPcateg');
		
		$data = JFactory::getApplication()->input->post->getArray();
		$data['description'] = JFactory::getApplication()->input->get("description","","raw");
		
		if($data['alias']==''){
			$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['name']);
		} else {
			$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['alias']);
		}
		
		$id = JFactory::getApplication()->input->get("id", "0");
		if(intval($id) == 0){
			$sql = "SELECT max(c.ordering) as maximum
					from #__guru_category c, #__guru_categoryrel cr 
					where c.id = cr.child_id and cr.parent_id = ".intval($data["parentcategory_id"])."
					group by cr.parent_id";
							
			$db->setQuery($sql);
			$db->execute();
			$max_order = $db->loadResult();		
			$data["ordering"] = intval($max_order)+1;
		}

		if(trim($data["id"]) == ""){
			$data["id"] = 0;
		}

		if(!isset($data["groups"])){
			$data["groups"] = "";
		}
		else{
			$data["groups"] = json_encode($data["groups"]);
		}

		if (!$item->bind($data)){
			return false;
		}
		if (!$item->check()) {
			return false;
		}
		if (!$item->store()) {
			return false;
		}
		
		if (intval($data['id']) > 0) {
			//we need to delete the old parent and create new relationship
			$delid = intval($data['id']);
			$ask = "DELETE FROM #__guru_categoryrel WHERE child_id = ".$delid;
			$db->setQuery($ask);
			$db->execute();
			//now we do the new insert
			$theparent = intval($data['parentcategory_id']);
			$ask = "INSERT INTO #__guru_categoryrel (parent_id, child_id) VALUES ({$theparent},{$delid})";
			$db->setQuery($ask);
			$db->execute();
			$newid = intval($data['id']);
		} else {
			//inseram in tabela de relatii cu categoriile
			if (intval($newid) == 0) {
				$ask = "SELECT id FROM #__guru_category ORDER BY id DESC LIMIT 1 ";
				$db->setQuery( $ask );
				$newid = $db->loadResult();				
			}
			//let's do the insert into the relationship table
			$theparent = intval($data['parentcategory_id']);
			$sql = "INSERT INTO #__guru_categoryrel (parent_id, child_id) VALUES ({$theparent},{$newid})";
			$db->setQuery($sql);
			$db->execute();
			
			// start create menu item to this category
			$sql = "select count(*) from #__menu_types where menutype='guru-categories'";
			$db->setQuery($sql);
			$db->execute();
			$count = $db->loadColumn();
			$count = @$count["0"];
			
			if(intval($count) == 0){
				$sql = "insert into #__menu_types (menutype, title, description) values ('guru-categories', 'Guru Categories', 'Guru Categories')";
				$db->setQuery($sql);
				$db->execute();
			}
			
			$sql = "select extension_id from #__extensions where type='component' and element='com_guru'";
			$db->setQuery($sql);
			$db->execute();
			$extension_id = $db->loadColumn();
			$extension_id = @$extension_id["0"];
			
			$sql = "insert into #__menu (menutype, title, alias, path, link, type, published, component_id, access, params, level, img) values ('guru-categories', '".addslashes(trim($data["name"]))."', '".addslashes(trim($data["alias"]))."', '".addslashes(trim($data["alias"]))."', 'index.php?option=com_guru&view=gurupcategs&layout=view', 'component', '1', '".intval($extension_id)."', '1', '{\"cid\":\"".intval($newid)."\",\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":\"\",\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', '1', '')";
			$db->setQuery($sql);
			$db->execute();
			// stop create menu item to this category
		}
		
		return $newid;
	}	
	
	function delete () {
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$database = JFactory::getDBO();
		$item = $this->getTable('guruPcateg');
		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			return;
		}
		$imagesin = $database->loadResult();
		
		$not_deleted = '';
		$menus_itemid = array();
		
		$sql = "select id, params from #__menu where menutype='guru-categories'";
		$database->setQuery($sql);
		$database->execute();
		$menus = $database->loadAssocList();
		
		if(isset($menus) && count($menus) > 0){
			foreach($menus as $key=>$value){
				$params = $value["params"];
				$params = json_decode($params, true);
				$menus_itemid[$params["cid"]] = $value["id"];
			}
		}
		
		foreach ($cids as $cid) {
			$q = "SELECT count(child_id) FROM #__guru_categoryrel WHERE parent_id = ".$cid;
			$database->setQuery($q);
			$how_many_subcats = $database->loadResult();	
			
			$q = "SELECT count(id) FROM #__guru_program WHERE catid = ".$cid;
			$database->setQuery($q);
			$how_many_programs = $database->loadResult();	
			
			if(($how_many_subcats==0 || !isset($how_many_subcats)) && ($how_many_programs==0 || !isset($how_many_programs))) 
			{// if the category doesn't have subcategories or programs - we delete it - begin			
				// we delete the image asociated to this program - begin
				$sql = "SELECT image FROM #__guru_category WHERE id =".$cid;
				$database->setQuery($sql);
				if (!$database->execute()) {
					return;
				}
				$image = $database->loadResult();	
				$targetPath = JPATH_SITE.'/'.$imagesin.'/';		
				unlink($targetPath.$image);
				// we delete the image asociated to this program - end				
			
				$delrel = "DELETE FROM #__guru_categoryrel WHERE child_id = ".$cid;
				$database->setQuery($delrel);
				$database->execute();
			
				if (!$item->delete($cid)) {
					$this->setError($item->getError());
					return false;
				}
				
				// start delete menu item for that category
				if(isset($menus_itemid[$cid])){
					$sql = "delete from #__menu where id=".intval($menus_itemid[$cid]);
					$database->setQuery($sql);
					$database->execute();
				}
				// stop delete menu item for that category
				
			}// if the category doesn't have subcategories or programs - we delete it - end
			else
			{// if the category cannot be deleted we pass the ID - for the message - begin
				$not_deleted = $not_deleted . $cid . ',';
			}// if the category cannot be deleted we pass the ID - for the message - end
			
		}		
		$not_deleted = substr($not_deleted, 0, strlen($not_deleted)-1);
		//return true;
		return '1$$$$$'.$not_deleted;
	}
	
	function publish () {
		$db = JFactory::getDBO();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		if ($task == 'publish'){
			$sql = "update #__guru_category set published='1' where id in ('".implode("','", $cids)."')";
			$ret = 1;
			
		} else {
			$ret = -1;
			$sql = "update #__guru_category set published='0' where id in ('".implode("','", $cids)."')";
		}
		$db->setQuery($sql);
		$db->execute();
		
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return $ret;
	}
	
	public static function getConfigs() {
		$db = JFactory::getDBO();
		
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadObject();	
		return $result;
	}
	
	function get_undeleted_categs($ids){
		$db = JFactory::getDBO();
		$cat_name = '';
		$sql = "SELECT name FROM #__guru_category WHERE id in (".$ids.") GROUP BY id";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadColumn();	
		foreach($result as $res)
			$cat_name = $cat_name.$res.', ';
		$cat_name = substr($cat_name,0,strlen($cat_name)-2);
		
		return $cat_name;	
	}
	
	function orderdown(){
		$db = JFactory::getDBO();

		$cid = JFactory::getApplication()->input->get("cid", array());
		$id = $cid["0"];
		$ordering = JFactory::getApplication()->input->get("order", array());
		$order_value = $ordering[$id];
		$sql = "SELECT c.id, c.ordering 
				FROM #__guru_category c, #__guru_categoryrel cr 
				WHERE c.id = cr.child_id and cr.parent_id = (select parent_id from #__guru_categoryrel where child_id=".intval($id).") and c.ordering >= ".intval($order_value)." and c.id <> ".intval($id)."
				GROUP BY cr.parent_id, c.ordering
				ORDER BY c.ordering asc";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		if(isset($result) && is_array($result) && count($result)>0){
			$new_id = $result["0"]["id"];
			$new_value = $result["0"]["ordering"];
			$sql = "update #__guru_category set ordering=".intval($new_value)." where id=".intval($id);
			$db->setQuery($sql);
			if($db->execute()){			
				$sql = "update #__guru_category set ordering=".intval($order_value)." where id=".intval($new_id);
				$db->setQuery($sql);
				if($db->execute()){
					return true;
				}
			}
		}
		return false;
	}
	
	function orderup(){
		$db = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get("cid", array());
		$id = $cid["0"];
		$ordering = JFactory::getApplication()->input->get("order", array());
		$order_value = $ordering[$id];
		$sql = "SELECT c.id, c.ordering 
				FROM #__guru_category c, #__guru_categoryrel cr 
				WHERE c.id = cr.child_id and cr.parent_id = (select parent_id from #__guru_categoryrel where child_id=".intval($id).") and c.ordering <= ".intval($order_value)." and c.id <> ".intval($id)."
				GROUP BY cr.parent_id, c.ordering
				ORDER BY c.ordering desc";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		if(isset($result) && is_array($result) && count($result)>0){
			$new_id = $result["0"]["id"];
			$new_value = $result["0"]["ordering"];
			$sql = "update #__guru_category set ordering=".intval($new_value)." where id=".intval($id);
			$db->setQuery($sql);
			if($db->execute()){			
				$sql = "update #__guru_category set ordering=".intval($order_value)." where id=".intval($new_id);
				$db->setQuery($sql);
				if($db->execute()){
					return true;
				}
			}
		}
		return false;
	}
	
	function saveorder($idArray = null, $lft_array = null){
		// Get an instance of the table object.
		$table = $this->getTable("guruPcateg");
		if(!$table->saveorder($idArray, $lft_array)){
			$this->setError($table->getError());
			return false;
		}
		// Clean the cache
		$this->cleanCache();
		return true;
	}
};
?>