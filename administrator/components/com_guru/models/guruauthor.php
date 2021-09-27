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

require_once JPATH_ADMINISTRATOR.'/components/com_users/helpers/users.php';

class guruAdminModelguruauthor extends JModelLegacy{
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_total = 0;
	var $total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruAuthor';

	function __construct () {
		global $option;
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
	
	function getItems(){
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$search = JFactory::getApplication()->input->get("search", "", "raw");
		$filter = JFactory::getApplication()->input->get("filter_status", "-");
		$filter2 = JFactory::getApplication()->input->get("filter_alert", "");
		$and = "";
		if(trim($search) != ""){
			$and = " and (u.username like '%".addslashes(trim($search))."%' or u.name like '%".addslashes(trim($search))."%')";
		}
		if($filter !="-"){
			$and .=" and a.enabled=".intval($filter);
		}
		if($filter2 !="" && $filter2 == 1){
			$and .=" and a.enabled=2 and u.registerDate >= now() and u.block=0";
		}
		else if($filter2 !="" && ($filter2 == 1 || $filter2 == 2)){
			$and .=" and a.enabled=2 and u.registerDate >= now() and u.block=1";
		}
		else if($filter2 !="" && $filter2 == 4){
			$and .=" and a.enabled=1 and u.registerDate >= now() and u.block=1";
		}
		
		$active = JFactory::getApplication()->input->get("active", "0");
		if($active == 1){
			$and .= " and u.block='0' and u.activation=''";
		}

		$sql =  "SELECT a.id author_id, a.ordering, a.enabled, 
				 u.name, u.username, group_concat(distinct(ug.title)) usertype, u.block publish, 
				 u.lastvisitDate, u.id user_id, u.email
				 FROM #__users u, #__guru_authors a, #__user_usergroup_map uugm, #__usergroups ug
				 WHERE a.userid=u.id AND uugm.user_id=u.id AND uugm.group_id=ug.id ".$and." 
				 GROUP BY u.id, a.id, a.ordering, a.enabled, u.name, u.username, u.block, u.lastvisitDate, u.email
				 ORDER by a.ordering ASC";
		
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

	
	function setId($id) {
		$this->_id = $id;
		$this->_attribute = null;
	}

	function getConfig(){
		$db = JFactory::getDBO();
		$sql="select * from #__guru_config limit 1";
		$db->setQuery($sql);
		$db->execute();
		$result=$db->loadObject();
		return $result;
	}

	function delete(){
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(), "raw");
		$return = TRUE;
		
		if(isset($cids) && is_array($cids) && count($cids) > 0){
			foreach($cids as $key=>$id){
				$sql = "select count(0) from #__guru_program where author=".intval($id);
				$db->setQuery($sql);
				$db->execute();
				$count = $db->loadColumn();
				if($count["0"] == 0){
					$sql = "delete from #__guru_authors where userid=".intval($id);
					$db->setQuery($sql);
					if(!$db->execute()){
						return false;
					}
				}
				else{
					$return = "has courses";
				}
			}
		}
		return $return;
	}

	function getAuthorList(){	
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
	
		$db = JFactory::getDBO();
		$sql =  "SELECT count(*) FROM #__users u
				 RIGHT JOIN #__guru_authors a 
				 ON a.userid=u.id
				 ORDER by a.ordering ASC ";
		$db->setQuery($sql);
		$db->execute();
		$this->_total = $db->loadColumn();
		$this->_total = $this->_total["0"];
	    
		$sql =  "SELECT a.id author_id, a.ordering, 
				 u.name, u.username, ug.title usertype, u.block publish, 
				 u.lastvisitDate, u.id user_id, u.email
				 FROM #__users u, #__guru_authors a, #__user_usergroup_map uugm, #__usergroups ug
				 WHERE a.userid=u.id AND uugm.user_id=u.id AND uugm.group_id=ug.id
				 ORDER by a.ordering ASC ".$limit_cond;
					
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		
		$sql = "select u.id from #__users u, #__session s where s.userid = u.id order by u.name";
		$db->setQuery($sql);
		$logged_in_users = $db->loadColumn();		

		if(isset($result)){
			foreach($result as &$element){
				if(in_array($element['user_id'], $logged_in_users)){
					$element['logged'] = true;
				} else{ 
					$element['logged'] = false; 
				}
			}
		} else { return NULL; }
		
		return $result;
	}	
	
	function block(){
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		$db = JFactory::getDBO();
		$task = JFactory::getApplication()->input->get("task");
		
		if(isset($cids) && count($cids) > 0){
			foreach($cids as $key=>$user_id){
				$var = "";
				if($task == "block"){
					$var = 0;
				}
				else{
					$var = 1;
				}
				
				$sql = "select enabled from #__guru_authors where userid=".intval($user_id);	
				$db->setQuery($sql);
				$db->execute();
				$old_enabled = $db->loadColumn();
				$old_enabled = $old_enabled["0"];
				
				$sql = "update #__guru_authors set enabled=".$var." where userid=".intval($user_id);	
				$db->setQuery($sql);
				if(!$db->execute()){
					return false;
				}
				else{
					if($var == 1){// unblock
						$sql = "update #__users set block='0', activation='' where id=".intval($user_id);
						$db->setQuery($sql);
						$db->execute();
					
						if($old_enabled != 1){// before was blocked
							$configs = $this->getConfig();
							$template_emails = $configs->template_emails;
							$template_emails = json_decode($template_emails, true);
							
							$fromname = $configs->fromname;
							$fromemail = $configs->fromemail;
							
							$approved_teacher_subject = $template_emails["approved_teacher_subject"];
							$approved_teacher_body = $template_emails["approved_teacher_body"];
							
							$sql = "select name, email from #__users where id=".intval($user_id);
							$db->setQuery($sql);
							$db->execute();
							$result = $db->loadAssocList();
							$user_name = $result["0"]["name"];
							$email = $result["0"]["email"];
							
							$app = JFactory::getApplication();
							$site_name = $app->getCfg('sitename');
							
							$approved_teacher_subject = str_replace("[SITE_NAME]", $site_name, $approved_teacher_subject);
							$approved_teacher_subject = str_replace("[AUTHOR_NAME]", $user_name, $approved_teacher_subject);
							$approved_teacher_body = str_replace("[SITE_NAME]", $site_name, $approved_teacher_body);
							$approved_teacher_body = str_replace("[AUTHOR_NAME]", $user_name, $approved_teacher_body);
							
							$send_teacher_email_teacher_approved = isset($template_emails["send_teacher_email_teacher_approved"]) ? $template_emails["send_teacher_email_teacher_approved"] : 1;

							if($send_teacher_email_teacher_approved){
								JFactory::getMailer()->sendMail($fromemail, $fromname, $email, $approved_teacher_subject, $approved_teacher_body, 1);
							}
						}
					}
					else{ // block
						$sql = "update #__users set block='1' where id=".intval($user_id);
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}
		return true;
	}
	
	function saveOrder(){	
		$db = JFactory::getDBO();	
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");		
		$cid = array_values($cids);		
		$order = JFactory::getApplication()->input->get('order', array (0));
		$order = array_values($order);		
		$total = count($cid);
		
		for($i=0; $i<$total; $i++){
			$sql = "update #__guru_authors set ordering=".$order[$i]." where userid=".$cid[$i];
			$db->setQuery($sql);
			if (!$db->execute()){
				return false;
			}
		}
		return true;
	}

	//for author details
	function existNewAuthor($username_value){
		$db = JFactory::getDBO();
		
		$sql = "select id from #__users u where u.username='".addslashes(trim($username_value))."'";
		$db->setQuery($sql);
		$db->execute();
		$id = $db->loadResult();
		
		$sql = "select count(*) as total from #__guru_authors a where a.userid=".intval($id);
		$db->setQuery($sql);
		$db->execute();		
		$result = $db->loadObject();		
		if($result->total==0){
			return false;
		} 
		return true;
	}
	
	function getAuthorDetails(){
		$db = JFactory::getDBO();
		$author_id = "";
		$id_val = JFactory::getApplication()->input->get("cid", "", "raw");
		if(is_array($id_val) && count($id_val) > 0 && $id_val["0"] != ""){
			$cids = JFactory::getApplication()->input->get("cid", "", "raw");
			$author_id = $cids[0];
		}
		else{
			$author_id = JFactory::getApplication()->input->get("id", "0");		
		}
		
		$type = JFactory::getApplication()->input->get("author_type", "1");
		$result = new StdClass();
		
		if($author_id > 0){
			if($this->existAuthor($author_id)){
				$sql = "select u.*,a.id as lmsid,a.* from #__users u, #__guru_authors a where u.id=a.userid and a.userid=".$author_id;
			}
			else{
				$sql = "select * from #__users u where id=".$author_id;
			}
			$db->setQuery($sql);
			$db->execute();
			$result = $db->loadObject();
			
			$result->userid = $author_id;
			
			if(!isset($result->lmsid)){
				$result->id = 0;
			}
			else{
				$result->id = $result->lmsid;
			}			
		}
		else{
			$get = JFactory::getApplication()->input->post->getArray();
			foreach($get as $key => $val){
				$result->$key = $val;
			}
		}
		
		$result->type = $type;
		
		if(!isset($result->id)){
			$result->id = 0;
		}
		$result->userid = $author_id;
		
		if(!isset($result->username)){
			$result->usernam = "";
		}
		
		if(!isset($result->name)){
			$result->name = "";
		}
		
		if(!isset($result->email)){
			$result->email = "";
		}
		
		if(!isset($result->author_title)){
			$result->author_title = "";
		}
		
		if(!isset($result->website) || $result->website == ""){
			$result->website = "http://";
		}
		
		if(!isset($result->blog) || $result->blog == ""){
			$result->blog = "http://";
		}
		
		if(!isset($result->facebook) || $result->facebook == ""){
			$result->facebook = "http://";
		}
		
		if(!isset($result->twitter) || $result->twitter == ""){
			$result->twitter = "";
		}
		
		//show/hide drop-down options
		$show_options = array();
		$show_options[] = JHTML::_('select.option', '1', JText::_('GURU_SHOW'));
		$show_options[] = JHTML::_('select.option', '0', JText::_('GURU_HIDE'));
			
		if(!isset($result->show_email)){ 
			$result->show_email = 1;
		}
		$result->lists['show_email'] = JHTML::_('select.genericlist', $show_options, 'show_email', null, 'value', 'text', $result->show_email);
		
		if(!isset($result->show_website)){
			$result->show_website = 1;
		}
		$result->lists['show_website'] = JHTML::_('select.genericlist', $show_options, 'show_website', null, 'value', 'text', $result->show_website);
		
		if(!isset($result->show_blog)){
			$result->show_blog = 1;
		}
		$result->lists['show_blog']	= JHTML::_('select.genericlist', $show_options, 'show_blog', null, 'value', 'text', $result->show_blog);
		
		if(!isset($result->show_facebook)){
			$result->show_facebook = 1;
		}
		$result->lists['show_facebook']	= JHTML::_('select.genericlist', $show_options, 'show_facebook', null, 'value', 'text', $result->show_facebook);
		
		if(!isset($result->show_twitter)){
			$result->show_twitter = 1;
		}
		$result->lists['show_twitter'] = JHTML::_('select.genericlist', $show_options, 'show_twitter', null, 'value', 'text', $result->show_twitter);	
		
		$id_a = JFactory::getApplication()->input->get("id", "0");	
		
		if((!isset($result->gid)) && (isset($id_a) && ($id_a != ""))){ 
			$query = "select group_id from #__user_usergroup_map where user_id='".$id_a."'";
			$db->setQuery($query);
			$res = $db->loadResult();
			
			if(isset($res) && $res != ""){			
				$result->gid = $res;
			}			
			else{
				$result->gid = "";
			}
		}
		elseif(!isset($result->gid)){
			$result->gid = "";
		}
		
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$sql_u = "select group_id from #__user_usergroup_map where user_id=".intval($id_a);
		$db->setQuery($sql_u);
		$db->execute();
		$res_user_current = $db->loadColumn();
		
		$result->lists['gid'] = JHTML::_('select.genericlist', UsersHelper::getGroups(), 'gid[]', 'size="10" multiple="multiple"', 'value', 'text', $res_user_current);
		
		if(!isset($result->images) || $result->images == ""){
			$result->images = "";
		}
		
		if(!isset($result->usertype)){
			$result->usertype = 0;
		}
		
		if(!isset($result->full_bio)){
			$result->full_bio = "";
		}
			
		return $result;
	}
	
	function existUser($username_value){
		$db = JFactory::getDBO();
		$sql = "select count(*) as total from #__users where username='".$username_value."'";		
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadObject();		
		if($result->total == 0){
			return false;
		}
		return true;
	}
	
	function existAuthor($userid){
		$db = JFactory::getDBO();
		$sql = "select count(*) as total from #__guru_authors where userid=".$userid;		
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadObject();		
		if($result->total==0){
			return false;
		} 
		return true;
	}
	
	function save(){
		$db = JFactory::getDBO();
		$item = $this->getTable('guruAuthor');
		$user = JFactory::getUser(JFactory::getApplication()->input->get('id', 0));
		$data = JFactory::getApplication()->input->post->getArray();
		$data["full_bio"] = JFactory::getApplication()->input->get("full_bio", "", "raw");
		$task = JFactory::getApplication()->input->get("task", "");
		
		if($task == 'save' || $task == 'apply'){
			if(intval($data['userid']) != 0){
				$sql = "update #__users set name='".$data['name']."' where id='".$data['id']."'";
				$db->setQuery($sql);
				$db->execute();
				
				$sql = "select username, email from #__users where id='".intval($data['id'])."'";
				$db->setQuery($sql);
				$db->execute();
				$result = $db->loadAssocList();
				
				$data["username"] = @$result["0"]["username"];
				$data["email"] = @$result["0"]["email"];
			}
		}
		
		$data["commission_id"] = $data["commission_plan"];
		$data["paypal_email"] = $data["paypal_email"];
		$data["paypal_option"] = $data["payment_option"];
		$data['images'] = str_replace('..','',$data['images']);

		if($data["author_type"] == 0){// new user
			//check username and email if already exist
			$username = $data["username"];
			$email = $data["email"];
			
			$sql = "select count(*) from #__users where username='".$username."'";
			$db->setQuery($sql);
			$db->execute();
			$result = $db->loadResult();
			
			if($result > 0){
				return false;
			}
			
			$sql = "select count(*) from #__users where email='".$email."'";
			$db->setQuery($sql);
			$db->execute();
			$result = $db->loadResult();
			
			if($result > 0){
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('email_already', "1");
				
				return false;
			}
		}
		$data["groups"] = $data['gid'];
		
		$user = JFactory::getUser(JFactory::getApplication()->input->get('id', 0));
		
		if(!$user->bind($data)){
			//nothing
		}
		
		if(!$user->save()){
			//nothing
		}
		
		// change id and userid
		$temp = $data["userid"];
		$data["userid"] = $data["id"];
		$data["id"] = $temp;
		
		if(!isset($data['userid']) || ($data['userid'] == '') || ($data['userid'] == 0)){
			$sql = 'SELECT id FROM #__users ORDER BY id DESC LIMIT 1';
			$db->setQuery($sql);
			$data['userid'] = $db->loadColumn();
			$data['userid'] = $data['userid'][0];
		}
		
		if(intval($data['userid']) != 0){
			$sql = "select count(*) from #__user_usergroup_map where user_id=".intval($data['userid'])." and group_id=".intval($data['gid']);
			$db->setQuery($sql);
			$db->execute();
			$count_already = $db->loadColumn();
		}
		
		$sql = "select count(*) from #__extensions where element='com_kunena' and name='com_kunena'";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		
		if($count[0] > 0){
			if(JComponentHelper::isEnabled( 'com_kunena', true) ){
				$sql = "SELECT name  FROM #__guru_program WHERE author =".intval($data['userid']);
				$db->setQuery($sql);
				$db->execute();
				$coursename = $db->loadAssocList();
				
				$sql = 'SELECT count(id) FROM #__guru_authors where user_id='.intval($data['userid']);
				$db->setQuery($sql);
				$nbaut = $db->loadColumn;
		
				if($nbaut[0] >0){
					$sql = "update #__guru_authors set forum_kunena_generated='1' where user_id='".intval($data['userid'])."'";
					$db->setQuery($sql);
					$db->execute();
				}
				
				$db->setQuery("SELECT `kunena_category` FROM #__guru_kunena_forum WHERE id=1");
				$db->execute();	
				$kunena_category = $db->loadColumn();
				$kunena_category = @$kunena_category["0"];

				if(intval($kunena_category) == 0){
					$nameofmainforum = JText::_('GURU_TREECOURSE');
				}
				else{
					$sql = "SELECT `name` FROM #__kunena_categories WHERE id='".intval($kunena_category)."'";
					$db->setQuery($sql);
					$db->execute();
					$nameofmainforum = $db->loadResult();
				}
			
				$sql = "SELECT id FROM #__kunena_categories WHERE name='".$db->escape($nameofmainforum)."'";
				$db->setQuery($sql);
				$db->execute();
				$idmainforum= $db->loadResult();
	
				foreach($coursename as $key=>$value){	
					$sql = "SELECT id FROM #__kunena_categories WHERE parent_id='".intval($idmainforum)."' and name='".addslashes($value['name'])."'";
		
					$db->setQuery($sql);
					$db->execute();
					$idcourses= $db->loadColumn();
					$idcourses = implode(",", $idcourses);
					if($idcourses == ""){
						$idcourses = "0";
					}
					if(isset($idcourses)){
						$sql = "SELECT id FROM #__kunena_categories WHERE parent_id IN (".$idcourses.")";
						$db->setQuery($sql);
						$db->execute();
						$idlessons= $db->loadAssocList();
						
						foreach($idlessons as $key=>$value){
							$sql = "SELECT count(*) FROM #__kunena_user_categories WHERE category_id ='".$value['id']."' and user_id=".$data['userid'];
							$db->setQuery($sql);
							$db->execute();
							$already= $db->loadResult();
							if($already == 0){
								$sql = "INSERT INTO #__kunena_user_categories (user_id, category_id, role, allreadtime, subscribed, params) VALUES ('".$data['userid']."', '".$value['id']."', 1, '', 0, '')";
								$db->setQuery($sql);
								$db->execute();
							}
						}
					}
				}
			}	
		}			
		if (!$item->bind($data)){
			echo $item->getError().' - 1';
			return false;
		}
		if (!$item->check()) {
			echo $item->getError().' - 2';
			return false;
		}			
		if (!$item->store()) {
			echo $item->getError().' - 3';
			return false;
		}

		$data["id"] = $data['userid'];
		$saved_custom_fields = $this->saveUserCustomFields($data);
			
		if(!$saved_custom_fields){
			$return["error"] = false;
		}

		return $data['userid'];
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
	
	function getUserId($username){
		$db = JFactory::getDBO();
		$sql = "select id from #__users where username='".$username."'";
	
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}
	function getFilters(){
		$app = JFactory::getApplication('administrator');
		@$filter_search = $app->getUserStateFromRequest('search','search','');
		@$filter->search = $filter_search;
		
		return $filter;
	}
	
	function getAroId($author_id){
		$db = JFactory::getDBO();
		
		if($author_id == "exist"){
			$username = JFactory::getApplication()->input->get("username");
			$sql = "select id from #__core_acl_aro where name='".$username."'";	
		}
		else{
			$sql = "select id from #__core_acl_aro where value='".$author_id."'";
		}	
		
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}
	
	function updateUser($user_id){
		$db = JFactory::getDBO();
		$gid = JFactory::getApplication()->input->get("gid");
		$usertype = $this->getUserType($gid);
		$block = JFactory::getApplication()->input->get("block");
		$name = JFactory::getApplication()->input->get("name");
		$username = JFactory::getApplication()->input->get("username");
		$email = JFactory::getApplication()->input->get("email");
		$password = JFactory::getApplication()->input->get("password");
		$sql = "";
		
		if($user_id == "exist"){
			if($password == ""){
				$sql = "update #__users set email='".$email."', block=".$block.", gid=".$gid.", usertype='".$usertype."' where username='".$username."'";
			}
			else{
				$sql = "update #__users set email='".$email."', block=".$block.", gid=".$gid.", usertype='".$usertype."',  password='".trim(md5($password))."' where username='".$username."'";
			}
		}
		else{
			if($password == ""){
				$sql = "update #__users set name='".$name."', username='".$username."', email='".$email."', block=".$block.", gid=".$gid.", usertype='".$usertype."' where id=".$user_id;
			}
			else{
				$sql = "update #__users set name='".$name."', username='".$username."', email='".$email."', block=".$block.", gid=".$gid.", usertype='".$usertype."',  password='".trim(md5($password))."' where id=".$user_id;
			}				
		}
		
		$db->setQuery($sql);
		$db->execute();
	}
	
	function saveAuthor($user_id){
		$db = JFactory::getDBO();
		$email = JFactory::getApplication()->input->get("email");
		$sql = "insert into #__guru_authors(userid, emaillink, ordering) values".
				"(".$user_id.", '".$email."', 0)";
				
		$db->setQuery($sql);
		if($db->execute()){
			return true;			
		}
		else{
			return false;
		}	
	}
	
	function saveAroMap($aro_id){
		$db = JFactory::getDBO();
		$gid = JFactory::getApplication()->input->get("gid");
		$sql = "insert into #__core_acl_groups_aro_map(group_id, section_value, aro_id) values".
				"(".$gid.", '', ".$aro_id.")";		
		$db->setQuery($sql);
		if($db->execute()){
			return true;			
		}
		else{
			return false;
		}		
	}
	
	function getUserType($id){
		$db = JFactory::getDBO();
		$sql = "select name from #__core_acl_aro_groups where id=".$id;
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		return $result;
	}
	
	function saveAro($user_id){
		$db = JFactory::getDBO();
		$name = JFactory::getApplication()->input->get("name");
		$sql = "insert into #__core_acl_aro(section_value, value, order_value, name, hidden) values".
				"('users', '".$user_id."', 0, '".$name."', 0)";
		$db->setQuery($sql);
		if($db->execute()){
			if(intval($id) == 0){
				$sql = "select id from #__core_acl_aro where value='".$user_id."' and name='".$name."'";
				$db->setQuery($sql);
				$db->execute();
				$id = $db->loadResult();
			}
			return $id;
		}	
	}
	
	function saveUsers(){
		$db = JFactory::getDBO();
		$gid = JFactory::getApplication()->input->get("gid");
		$usertype = $this->getUserType($gid);
		$block = JFactory::getApplication()->input->get("block");
		$registerDate = date("Y-m-d G:i:s");
		$name = JFactory::getApplication()->input->get("name");
		$username = JFactory::getApplication()->input->get("username");
		$email = JFactory::getApplication()->input->get("email");
		$password = JFactory::getApplication()->input->get("password");
		
		$sql = "insert into #__users(name, username, email, password, usertype, block, sendEmail, gid, registerDate, lastvisitDate) values ".
				"('".$name."', '".$username."', '".$email."', '".md5(trim($password))."', '".$usertype."', ".$block.", 0, ".$gid.", '".$registerDate."', '0000-00-00 00:00:00')";
		$db->setQuery($sql);
		if($db->execute()){
			if(intval($id) == 0){
				$sql = "select id from #__users where usertype='".$usertype."' and name='".$name."' and email='".$email."'";
				$db->setQuery($sql);
				$db->execute();
				$id = $db->loadResult();
			}			
			return $id;
		}		
	}
};	
?>