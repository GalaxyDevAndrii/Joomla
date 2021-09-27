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
use Joomla\Event\Dispatcher;


class guruAdminModelguruCustomers extends JModelLegacy{
	var $_customers;
	var $_customer;
	var $_id = null;
	var $_total = 0;
	var $total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruCustomers';

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
		$this->_id = $id;
		$this->_customer = null;
	}

	protected function getListQuery(){		
		$search = JFactory::getApplication()->getUserStateFromRequest($this->_context . '.search','search','');
		$and = "";
		
		if(trim($search) != ""){
			$and = " and (u.name like '%".addslashes(trim($search))."%' or u.username like '%".addslashes(trim($search))."%' or c.firstname like '%".addslashes(trim($search))."%' or c.lastname like '%".addslashes(trim($search))."%')";
		}
		
		$sql = "select u.id, u.username, u.name, c.firstname, c.lastname from #__users u, #__guru_customer c where c.id=u.id ".$and." order by c.id desc ";

		return $sql;
	}
	
	function getItems(){
		$config = new JConfig();	
		$app = JFactory::getApplication('administrator');
		
		$db = JFactory::getDbo();

		$db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $db->execute();
		
		$search = JFactory::getApplication()->getUserStateFromRequest($this->_context . '.search','search','');
		$and = "";
		
		if(trim($search) != ""){
			$and = " and (u.name like '%".$db->escape(trim($search))."%' or u.username like '%".$db->escape(trim($search))."%' or c.firstname like '%".$db->escape(trim($search))."%' or c.lastname like '%".$db->escape(trim($search))."%' or CONCAT(c.firstname, ' ', c.lastname) like '%".$db->escape(trim($search))."%' )";
		}
		
		$sql = "select u.id, u.username, u.name, c.firstname, c.lastname, ug.title usertype, u.block publish, u.lastvisitDate, u.id user_id, u.email, group_concat(ug.title) usertype
				from #__users u, #__guru_customer c, #__user_usergroup_map uugm, #__usergroups ug 
				where c.id=u.id AND uugm.user_id=u.id AND uugm.group_id=ug.id ".$and." 
				GROUP BY u.id
				order by c.id desc ";
		
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


	function getlistCourses () {
		$db = JFactory::getDBO();
		$sql = "SELECT id,name FROM #__guru_program";
		$db->setQuery($sql);
		$courses=$db->loadObjectList();
		return $courses;
	}

	function getFilters(){
		$filter_search = JFactory::getApplication()->getUserStateFromRequest($this->_context . '.search','search','');
		$filter = new stdClass;
		$filter->search = $filter_search;
		
		return $filter;
	}

	function getCustomer(){
		if(isset($_REQUEST['userid']) && $_REQUEST['userid'] > 0){
			$db = JFactory::getDBO();
			$q = "SELECT u.id as user_id,
						 u.name as name, 
						 u.email as email, 
						 u.username as username
						 FROM #__users u where id = '".$_REQUEST['userid']."'";
			$db->setQuery($q);
			$db->execute();
			$result = $db->loadObjectList();
			$this->_customer = $result[0];
			$this->_customer->id = 0;
		}
		elseif(empty($this->_customer)){ 
			$this->_customer = $this->getTable("guruCustomer"); 
			if($this->_id > 0){
				$this->_customer->load($this->_id);
			}
		}
		return $this->_customer;
	}
	
	function updateUserActivation($id){
		$db = JFactory::getDBO();
		$sql = 'UPDATE #__users set block=0, activation="" where id ='.intval($id);
		$db->setQuery($sql);
		$db->execute();
	}
	
	function encriptPassword($password){
		$salt = "";
		for($i=0; $i<=32; $i++){
			$d = rand(1,30)%2;
		  	$salt .= $d ? chr(rand(65,90)) : chr(rand(48,57));
	   	}
		$hashed = md5($password.$salt);
		$encrypted = $hashed.':'.$salt;
		return $encrypted;
	}
	
	function saveJoomlaUser(){
		$db = JFactory::getDBO();
		$user_id = "";
		$password = JFactory::getApplication()->input->get("password", "", "raw");
		$password = $this->encriptPassword($password);
		$name = JFactory::getApplication()->input->get("firstname", "", "raw");
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		
		$block = "0";
		$sendEmail = "0";
		$jnow = new JDate('now');
		$registerDate = $jnow->toSQL(); 
		$lastvisitDate = "0000-00-00 00:00:00";
		
		$sql = "insert into #__users(name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params) values ('".addslashes(trim($name))."', '".addslashes(trim($username))."', '".addslashes(trim($email))."', '".$password."', 0, 0, '".$registerDate."', '".$lastvisitDate."', '', '')";
		$db->setQuery($sql);
		
		if($db->execute()){
			$sql = "select id from #__users where name='".addslashes(trim($name))."' and username='".addslashes(trim($username))."' and email='".addslashes(trim($email))."'";
			$db->setQuery($sql);
			$db->execute();
			$user_id = $db->loadResult();			
		}
		
		if($user_id != ""){	
			$groups = JFactory::getApplication()->input->get("gid", array(), "raw");
			
			if(isset($groups) && count($groups) > 0){
				foreach($groups as $key=>$group_id){
					$query = "insert into #__user_usergroup_map(user_id, group_id) values('".intval($user_id)."', '".intval($group_id)."')";
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		
		return $user_id;
	}
	
	function updateJoomlaUser($id){
		$db = JFactory::getDbo();
		
		if(intval($id) > 0){
			$sql = "delete from #__user_usergroup_map where user_id=".intval($id);
			$db->setQuery($sql);
			$db->execute();
			
			$groups = JFactory::getApplication()->input->get("gid", array(), "raw");
				
			if(isset($groups) && count($groups) > 0){
				foreach($groups as $key=>$group_id){
					$query = "insert into #__user_usergroup_map(user_id, group_id) values('".intval($id)."', '".intval($group_id)."')";
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	function existCustomer($id){
		if(intval($id) == 0){
			return false;
		}
		
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_customer where id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function store(){ 
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "");		
		$company = JFactory::getApplication()->input->get("company", "", "raw");
		$firstname = JFactory::getApplication()->input->get("firstname", "", "raw");
		$lastname = JFactory::getApplication()->input->get("lastname", "", "raw");
		$sequential_courses = JFactory::getApplication()->input->get("sequential_courses", array(), "raw");	
		$return = array();
		
		$sql = "";
		
		if(!$this->existCustomer($id)){	
			$action = JFactory::getApplication()->input->get("action", "");
			
			if($action != "existing"){
				$id = $this->saveJoomlaUser();
			}
			
			$sql = "insert into #__guru_customer(`id`, `company`, `firstname`, `lastname`, `sequential_courses`) values (".intval($id).", '".$company."', '".addslashes(trim($firstname))."', '".$lastname."', '".json_encode($sequential_courses)."')";
		}
		else{
			$this->updateJoomlaUser($id);	
			$sql = "update #__guru_customer set company='".$company."', firstname='".addslashes(trim($firstname))."', lastname='".$lastname."', sequential_courses='".json_encode($sequential_courses)."' where id=".intval($id);
		}
		
		$db->setQuery($sql);
		if($db->execute()){
			$return["error"] = TRUE;
			$return["id"] = $id;
		}
		else{
			$return["error"] = false;
			$return["id"] = 0;
		}

		$this->updateUserActivation($id);

		$data = JFactory::getApplication()->input->post->getArray();

		if(intval($data["id"]) == 0){
			$data["id"] = $return["id"];
		}

		$saved_custom_fields = $this->saveUserCustomFields($data);
			
		if(!$saved_custom_fields){
			$return["error"] = false;
		}

		//---------------------------------------
		JEventDispatcher::getInstance()->trigger('onAfterStudentSaved', array(intval($id)));
		//---------------------------------------

		return $return;	
	}

	function saveUserCustomFields($data){
		if(isset($data["fields"]) && is_array($data["fields"]) && count($data["fields"]) > 0){
			$db = JFactory::getDbo();
			$fields = $data["fields"];

			$sql = "select * from #__guru_fields_results where `user_id`=".intval($data["id"]);
			$db->setQuery($sql);
			$db->execute();
			$saved_fields = $db->loadAssocList("field_id");

			foreach ($fields as $field_id => $field){
				if(isset($saved_fields[$field_id])){
					// field already was saved

					if(is_array($field)){
						$field = json_encode($field);
					}

					$sql = "update #__guru_fields_results set `value`='".$db->escape(trim($field))."' where `field_id`=".intval($field_id)." and `user_id`=".intval($data["id"]);
					echo $sql."<br /><br />";
					$db->setQuery($sql);
					if(!$db->execute()){
						return false;
					}

					unset($saved_fields[$field_id]);
				}
				else{
					// field not saved

					if(is_array($field)){
						$field = json_encode($field);
					}

					$sql = "insert into #__guru_fields_results (`user_id`, `field_id`, `value`) values ('".intval($data["id"])."', '".intval($field_id)."', '".$db->escape(trim($field))."')";
					$db->setQuery($sql);
					if(!$db->execute()){
						return false;
					}
				}
			}

			if(isset($saved_fields) && count($saved_fields) > 0){
				// there are custom fields that need to be deleted, user unset the value for that fields

				foreach($saved_fields as $field_id=>$value){
					$sql = "delete from #__guru_fields_results where `field_id`=".intval($field_id)." and `user_id`=".intval($data["id"]);
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		return true;
	}

	function remove(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(), "raw");
		foreach($cids as $key=>$value){
			if(trim($value) != ""){
				$sql = "SELECT count(*) from #__guru_order where userid=".intval($value);
				$db->setQuery($sql);
				$db->execute();
				$result_customer = $db->loadColumn();
				if(intval($result_customer[0]) <= 0){
					$sql = "delete from #__guru_customer where id=".intval($value);
					$db->setQuery($sql);
					if(!$db->execute()){
						return false;
					}
				}
				else{
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set('cust_is_enrolled', "1");
					return false;
				}
			} 
		}
		return true;
	}


	function block(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '', 'post');
		$item = $this->getTable('guruCustomer');
		if ($task == 'block'){
			$sql = "update #__users set block='1' where id in ('".implode("','", $cids)."')";
			$ret = -1;
		}
		else {
			$ret = 1;
			$sql = "update #__users set block='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	function getNoOrders($uid){
		$db = JFactory::getDBO();		
		$sql="SELECT COUNT(*) FROM jos_guru_order as o, 
		jos_guru_customer AS c, jos_guru_program AS p 
		WHERE o.userid=c.user_id AND
		o.programid=p.id AND c.user_id ='".$uid."'";		
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		return $result;
	}	
	
	function getNoTests($uid){
		$db = JFactory::getDBO();
		$sql="SELECT COUNT(*) FROM jos_guru_order as o, 
		jos_guru_customer AS c, jos_guru_program AS p 
		WHERE o.userid=c.user_id AND
		o.programid=p.id AND c.user_id ='".$uid."'";		
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		return $result;
	}		
	
	function existNewCustomer($username_value){
		$db = JFactory::getDBO();
		$sql = "select a.user_id as userid from #__guru_customer a where a.user_id=(select id from #__users u where u.username='".$username_value."')";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadObject();		
		if(isset($result->userid) && $result->userid==0){
			return false;
		} 
		return $result->userid;
	}
	
	function existUser($username_value){
		$db = JFactory::getDBO();
		$sql = "select count(*) as total from #__users where username='".$username_value."'";		
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadObject();		
		if($result->total==0){
			return false;
		} 
		return true;
	}
	
	function getUserId($username){
		$db = JFactory::getDBO();
		$sql = "select id from #__users where username='".addslashes(trim($username))."'";
	
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}
	
	function getCustomerDetails($id){
		$db = JFactory::getDBO();
		$sql = "select u.username, u.email, c.firstname, c.lastname, c.company, c.`sequential_courses` from #__users u left join #__guru_customer c on u.id=c.id where u.id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	function getStudentCourses($id){
		$db = JFactory::getDBO();
		$sql = "SELECT id ,name FROM #__guru_program where id in(SELECT distinct(course_id) from #__guru_buy_courses where userid=".intval($id).")";
		$db->setQuery($sql);
		$courses=$db->loadAssocList();
		return $courses;
	}
	
	function resetCourses(){
		$user_id = JFactory::getApplication()->input->get("id", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("course", "0", "raw");
		$db = JFactory::getDBO();
		
		$sql = "select `question_id`, `quiz_id` from #__guru_quiz_taken_v3 where pid=".intval($course_id)." and user_id=".intval($user_id);
		$db->setQuery($sql);
		$questions_details = $db->loadAssocList();

		$questions = array("0");
		$quizzes = array("0");

		if(isset($questions_details) && count($questions_details) > 0){
			foreach($questions_details as $key=>$value){
				if(!in_array($value["question_id"], $questions)){
					$questions[] = $value["question_id"];
				}

				if(!in_array($value["quiz_id"], $quizzes)){
					$quizzes[] = $value["quiz_id"];
				}
			}
		}

		if(isset($questions) && count($questions) > 0){
			$sql = "delete from #__guru_quiz_essay_mark where question_id in (".implode(",", $questions).") and user_id=".intval($user_id);
			$db->setQuery($sql);
			if(!$db->execute()){
				return "0";
			}
		}
		
		$sql = "delete from #__guru_mycertificates where course_id=".intval($course_id)." and user_id=".intval($user_id);
		$db->setQuery($sql);
		if(!$db->execute()){
			return "0";
		}
		
		$sql = "delete from #__guru_quiz_question_taken_v3 where user_id=".intval($user_id)." and `quiz_id` in (".implode(",", $quizzes).")";
		$db->setQuery($sql);
		if(!$db->execute()){
			return "0";
		}
		
		$sql = "delete from #__guru_quiz_taken_v3 where pid=".intval($course_id)." and user_id=".intval($user_id)." and `quiz_id` in (".implode(",", $quizzes).")";
		$db->setQuery($sql);
		if(!$db->execute()){
			return "0";
		}
		
		$sql = "delete from #__guru_viewed_lesson where pid=".intval($course_id)." and user_id=".intval($user_id);
		$db->setQuery($sql);
		if(!$db->execute()){
			return "0";
		}
		
		return $course_id;
	}
	
	function removeFromCourse(){
		$this->resetCourses();
		
		$user_id = JFactory::getApplication()->input->get("id", "0");
		$course_id = JFactory::getApplication()->input->get("course", "0");
		$db = JFactory::getDBO();
		
		$sql = "select order_id from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
		$db->setQuery($sql);
		$db->execute();
		$order_id = $db->loadColumn();
		$order_id = @$order_id["0"];
		
		$sql = "delete from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
		$db->setQuery($sql);
		if(!$db->execute()){
			return "0";
		}
		
		if(intval($order_id) > 0){
			$sql = "select courses from #__guru_order where id=".intval($order_id);
			$db->setQuery($sql);
			$db->execute();
			$courses = $db->loadColumn();
			$courses = @$courses["0"];
			$courses = explode("|", $courses);
			
			if(is_array($courses) && count($courses) > 0){
				$new_courses = array();
				
				foreach($courses as $key=>$value){
					$course = explode("-", $value);
					
					if($course["0"] != $course_id){
						$new_courses[] = $value;
					}
				}
				
				if(count($new_courses) == 0){
					$sql = "delete from #__guru_order where id=".intval($order_id);
					$db->setQuery($sql);
					$db->execute();
				}
				else{
					$sql = "update #__guru_order set courses='".implode("|", $new_courses)."' where id=".intval($order_id);
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
		
		return "1";
	}

	function getSequentialCourses($id){
		$db = JFactory::getDbo();

		$sql = "select p.`id`, p.`name` from #__guru_program p, #__guru_buy_courses bc where bc.`course_id`=p.`id` and p.`course_type`='1' and bc.`userid`=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$courses = $db->loadAssocList();

		return $courses;
	}
};
?>