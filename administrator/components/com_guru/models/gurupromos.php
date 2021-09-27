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



class guruAdminModelguruPromos extends  JModelLegacy {
	var $_packages;
	var $_package;
	var $_tid = null;
	var $_total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruPromos';

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

	function setId($id) {
		$this->_tid = $id;
		$this->_package = null;
	}
	
	function getcourses(){
		$db = JFactory::getDBO();
		$sql = "SELECT id, name FROM #__guru_program";	
		$db->setQuery($sql);
		$db->execute();
		$courses = $db->loadObjectList();
		return $courses;
	}
	function getCoursesPromo(){
		$db = JFactory::getDBO();
		$promo_id = JFactory::getApplication()->input->get("promo_id", "");
		$sql = "SELECT courses_ids FROM #__guru_promos where id=".intval($promo_id);	
		$db->setQuery($sql);
		$db->execute();
		$courses = $db->loadColumn();
		$courses_array = explode("|",$courses["0"]);
		$courses_array = array_values(array_filter($courses_array));
		if(count($courses_array) > 0){
			$courses_array = " where id IN(".implode(",",$courses_array ).")";
		}
		else{
			$courses_array = " where id IN(0)";
		}
		$sql = "SELECT id, name FROM #__guru_program".$courses_array;	
		$db->setQuery($sql);
		$db->execute();
		$courses = $db->loadObjectList();
		return $courses;
		
	
	}
	
	protected function getListQuery(){
        $db = JFactory::getDBO();
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$condition=NULL;$publ=NULL;
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$search_promos = $registry->get('search_promos', "");
			
			if(isset($data_post['search_promos'])){
				$cond= addslashes(($data_post['search_promos']));
				
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('search_promos', $cond);
				
				if($cond!=''){
					$condition="AND c.title LIKE '%".$cond."%' OR c.code LIKE '%".$cond."%' ";
				}
			}
			elseif(isset($search_promos) && trim($search_promos) != "") {
				$cond = $search_promos;
				
				if($cond!=''){
					$condition = "AND c.title LIKE '%".$cond."%' OR c.code LIKE '%".$cond."%' ";
				}
			}
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$promos_publ_status = $registry->get('promos_publ_status', "");
			
			if(isset($promos_publ_status) && trim($promos_publ_status) != ""){
				if($promos_publ_status == 'Y') {
					$publ = " AND c.published=1 ";
				}
				elseif($promos_publ_status == 'N') {
					$publ = " AND c.published=0 ";
				}
				else{
					$publ = NULL;
				}
			}
			
			if(isset($data_post['promos_publ_status'])){
				if($data_post['promos_publ_status']=='Y') {
					$publ=" AND c.published=1 ";
				} elseif ($data_post['promos_publ_status']=='N') {
					$publ=" AND c.published=0 ";
				} else {
					$publ=NULL;				
				}
			}
			
			$sql = "SELECT * FROM #__guru_promos AS c WHERE 1=1 ".$condition.$publ." ORDER BY id DESC";

		return $sql;
	}
	
	function getItems(){
		$config = new JConfig();	
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$sql = $this->getListQuery();
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
		$result = $this->_getList($sql.$limit_cond);
		$this->_total = $this->_getListCount($sql);
		return $result;
	}

	function getPromo() {
		if (empty ($this->_package)) {
			$this->_package = $this->getTable("guruPromos");
			$this->_package->load($this->_tid);
			$data = JFactory::getApplication()->input->post->getArray();
			
			if (!$this->_package->bind($data)){
				JFactory::getApplication()->enqueueMessage($this->_package->getError(), 'error');
				return false;
	
			}
	
			if (!$this->_package->check()) {
				JFactory::getApplication()->enqueueMessage($this->_package->getError(), 'error');
				return false;
	
			}
		}
		return $this->_package;
		
	}
	
	
	function store () {
		$item = $this->getTable('guruPromos');
		$data = JFactory::getApplication()->input->post->getArray();
		$id = JFactory::getApplication()->input->get("id","0");
		
		if(isset($data["cid"]) && is_array($data["cid"]) && count($data["cid"]) > 0){
			$data["courses_ids"] = implode("||", $data["cid"]);
		}
		else{
			$data["courses_ids"] = "";
		}
		
		if(trim($data["id"]) == ""){
			$data["id"] = "0";
		}

		if(trim($data["codelimit"]) == ""){
			$data["codelimit"] = "0";
		}

		if(trim($data["codeend"]) == ""){
			$data["codeend"] = "0000-00-00 00:00:00";
		}

		$db = JFactory::getDBO();
		
		if($id !=0){
			$sql = "SELECT count(title)  FROM #__guru_promos
			WHERE title ='".$data['title']."' and id<> ".intval($id)."";
			$db->setQuery($sql);
			$db->execute();
			$count = $db->loadColumn();
			if($count[0] >0){
				$msg = JText::_('GURU_PROMO_TITLE_EXISTS');
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'warning');
				$app->redirect('index.php?option=com_guru&controller=guruPromos');
			 
			}
			
			$sql = "SELECT count(title)  FROM #__guru_promos
			WHERE code ='".$data['code']."' and id<> ".intval($id)."";
			$db->setQuery($sql);
			$db->execute();
			$countt = $db->loadColumn();
			
			if($countt[0] >0){
				$msg = JText::_('GURU_PROMO_CODE_EXISTS');
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'warning');
				$app->redirect('index.php?option=com_guru&controller=guruPromos');
			 
			}
		}
		else{
			$sql = "SELECT count(title)  FROM #__guru_promos
			WHERE title ='".$data['title']."'";
			$db->setQuery($sql);
			$db->execute();
			$count = $db->loadColumn();

			if($count[0] >0){
				$msg = JText::_('GURU_PROMO_TITLE_EXISTS');
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'warning');
				$app->redirect('index.php?option=com_guru&controller=guruPromos');
			}
			
			$sql = "SELECT count(title)  FROM #__guru_promos
			WHERE code ='".$data['code']."'";
			$db->setQuery($sql);
			$db->execute();
			$countt = $db->loadColumn();
			
			if($countt[0] >0){
				$msg = JText::_('GURU_PROMO_CODE_EXISTS');
				$app = JFactory::getApplication();
				$app->enqueueMessage($msg, 'warning');
				$app->redirect('index.php?option=com_guru&controller=guruPromos');
			}
		}
		
		$data['code'] = strtolower($data['code']);
		$db = JFactory::getDBO();
		if($data['codestart']==JText::_('GURU_TODAY') || $data['codestart'] == "" ){
			$data['codestart']=date('Y-m-d', time());
		}
		$data['codestart'] = date('Y-m-d H:i:s', strtotime($data['codestart']));
		
		if( $data['codeend'] !='Never' && $data['codeend'] != '' && $data['codeend'] != "0000-00-00 00:00:00"){ // calendar change
			$data['codeend'] = date('Y-m-d H:i:s', strtotime($data['codeend']));
		}

		require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
		$guruHelper = new guruHelper();

		$data["discount"] = $guruHelper->savePrice($data["discount"]);

		if (!$item->bind($data)){
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		if (!$item->check()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		
		if (!$item->store()) {
			die($item->getError());
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		
		$sql = "SELECT id  FROM #__guru_promos
		WHERE code ='".$data['code']."'";
		$db->setQuery($sql);
		$db->execute();
		$id = $db->loadColumn();
		if(isset($id[0]) && $id[0] !="" && $id[0] !=NULL){
					return $id[0];

		}
		else{
			return false;
		}
	}	

	function delete () {
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$item = $this->getTable('guruPromos');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getError());
				return false;

			}
		}

		return true;
	}


	function publish () {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		$item = $this->getTable('guruPromos');
		if ($task == 'publish')
			$sql = "update #__guru_promos set published='1' where id in ('".implode("','", $cids)."')";
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}
	
	function unpublish () {
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		$item = $this->getTable('guruPromos');
		if ($task == 'unpublish')
			$sql = "update #__guru_promos set published='0' where id in ('".implode("','", $cids)."')";		

		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return -1;
	}
	
	public static function getConfig(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$res = $db->loadObject();
		return $res;
	}

};
?>