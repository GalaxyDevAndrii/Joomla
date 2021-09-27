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
use Joomla\Event\Dispatcher;

class guruModelguruLogin extends JModelLegacy {
	
	function __construct(){
		parent::__construct();
	}
	
	function isNewUser(){
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		$firstname = JFactory::getApplication()->input->get("firstname", "", "raw");
		$lastname = JFactory::getApplication()->input->get("lastname", "", "raw");
    	$company = JFactory::getApplication()->input->get("company", "", "raw");
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$auid = JFactory::getApplication()->input->get("auid", "0", "raw");

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$registry->set('username', $username);
		$registry->set('email', $email);
		$registry->set('firstname', $firstname);
		$registry->set('lastname', $lastname);
		$registry->set('company', $company);
		
		$id_value = intval($id);
		
		if(intval($id_value) == 0){
			 $id_value = intval($auid);
		}
		
		if($id_value == 0){
			$db = JFactory::getDBO();
			$sql = "select count(*) from #__users where username='".trim(addslashes($username))."'";
			$db->setQuery($sql);
			$result = $db->loadResult();
			
			if($result != "0"){
				return false;
			}
			
			$sql = "select count(*) from #__users where email='".trim(addslashes($email))."'";
			$db->setQuery($sql);
			$result = $db->loadResult();
			if($result != "0"){
				return false;
			}
		}
		return true;
	}
	
	function store(){
		jimport("joomla.database.table.user");
		$db = JFactory::getDBO();
		$my = JFactory::getUser();
		$course_id = JFactory::getApplication()->input->get("course_id","0", "raw");

		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$configs = $db->loadAssocList();
		
		$allow_teacher_action = json_decode($configs["0"]["st_authorpage"]);//take all the allowed action from administator settings
		
		$teacher_aprove = @$allow_teacher_action->teacher_aprove; //allow or not aprove teacher
		$params = JComponentHelper::getParams('com_users');
		
		$nowDate = new JDate('now');
		$nowDate = $nowDate->toSql(true);
		$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$authKey = md5(uniqid(rand(), true));
		
		
		if(!$my->id){
			$new_user = 1;
		}
		else{
			$new_user = 0;
		}

		$table = $this->getTable('guruCustomer');
		$data = JFactory::getApplication()->input->post->getArray();
		$data['password2'] = $data['password_confirm'];
		
		if($data['guru_teacher'] == 1){
			$data['name'] = $data['firstname']." ".$data['lastname'];
		}
		
		$firstname = $data['firstname'];
		$lastname = $data['lastname'];
		
		$data["enabled"] = 1;
		$res = true;
		$reg = JSession::getInstance("none", array());
		$user = new JUser();
		$useractivation = $params->get('useractivation');
		
		if(intval($data["id"]) == 0){
			$data["id"] = intval($my->id);
			\JFactory::getConfig()->set('root_user', 0);
		}
		else{
			if(intval($data["id"]) != intval($my->id)){
				return false;
			}
		}
		
		$user = new JUser($data["id"]);

		if($data['guru_teacher'] == 1){
			$session = JFactory::getSession();
			$token = $session->getToken();
			
			if($useractivation == 1 || $useractivation == 2){
				$data["block"] = 1;
				$data["activation"] = $token;
			}
			else{
				$data["block"] = 0;
				$data["activation"] = "";
			}

			// ignore Joomla registration settings if auto approve students
			if(isset($configs["0"]["auto_approve"]) && $configs["0"]["auto_approve"] == "1"){
				$data["block"] = 0;
				$data["activation"] = "";
			}
			// ignore Joomla registration settings if auto approve students
			
			$user->bind($data);

			if(!$user->save()) {
				$reg->set("tmp_profile", $data);
				$error = $user->getError();
				$res = false;
			}
		}
		
		if($data['guru_teacher'] == 2){
			$session = JFactory::getSession();
			$token = $session->getToken();
				
			if($teacher_aprove == 1){
				$data["enabled"] = 2;
				$auid = JFactory::getApplication()->input->get("auid", "0", "raw");

				if(($useractivation == 1 || $useractivation == 2) && intval($auid) == 0){
					$data["block"] = 1;
					$data["activation"] = $token;
				}
				else{
					$data["block"] = 0;
					$data["activation"] = "";
				}
			}
			elseif($teacher_aprove == 0){
				$data["block"] = 0;
				$data["activation"] = "";
			}
			
			$user->bind($data);

			if(!$user->save()) {
				$reg->set("tmp_profile", $data);
				$error = $user->getError();
				$res = false;
			}
		}
		
		if(intval($data["id"]) == 0){
			$this->addToGroup($user->id);
		}

		if($data['guru_teacher'] == 1){
			$data['id'] = $user->id;
			
			if(!$this->existCustomer($data['id'])){
				if(trim($firstname) == "" && trim($lastname) == ""){
					$user_name = $user->name;
					$user_name = explode(" ", $user_name);
					
					if(count($user_name) > 1){
						$lastname = $user_name[count($user_name) - 1];
						unset($user_name[count($user_name) - 1]);
						$firstname = implode(" ", $user_name);
					}
					else{
						$firstname = $user->name;
					}
				}
				
				$sql = "insert into #__guru_customer (id, company, firstname, lastname, image) values (".intval($data['id']).", '".$db->escape(trim($data['company']))."', '".$db->escape(trim($firstname))."', '".$db->escape(trim($lastname))."', '".$db->escape(trim($data['image']))."')";
				$db->setQuery($sql);

				
				if(!$db->execute()){
					$res = false;
				}

				//---------------------------------------
				JFactory::getApplication()->triggerEvent('onAfterStudentSaved', array(intval($data['id'])));
				//---------------------------------------
				
				$params = JComponentHelper::getParams('com_users');
				$useractivation = $params->get('useractivation');
				
				if($useractivation == 2){
					// admin
					$sql = "select template_emails, fromname, fromemail, admin_email from #__guru_config";
					$db->setQuery($sql);
					$confic = $db->loadAssocList();
					$template_emails = $confic["0"]["template_emails"];
					$template_emails = json_decode($template_emails, true);
					$fromname = $confic["0"]["fromname"];
					$fromemail = $confic["0"]["fromemail"];
					
					
					$sql = "select u.email from #__users u, #__user_usergroup_map ugm where u.id=ugm.user_id and ugm.group_id='8' and u.id IN (".$confic["0"]["admin_email"].")";
					$db->setQuery($sql);
					$email = $db->loadColumn();
					
					$app = JFactory::getApplication();
					$site_name = $app->getCfg('sitename'); 
					
					$subject = $template_emails["new_student_subject"];
					$body = $template_emails["new_student_body"];
					
					$subject = str_replace("[STUDENT_FIRST_NAME]", $data['firstname'], $subject);
					$subject = str_replace("[STUDENT_LAST_NAME]", $data['lastname'], $subject);
					$subject = str_replace("[STUDENT_EMAIL]", $data['email'], $subject);
					
					$body = str_replace("[STUDENT_FIRST_NAME]", $data['firstname'], $body);
					$body = str_replace("[STUDENT_LAST_NAME]", $data['lastname'], $body);
					$body = str_replace("[STUDENT_EMAIL]", $data['email'], $body);

					for($i=0; $i< count($email); $i++){
						$send_admin_email_student_registered = isset($template_emails["send_admin_email_student_registered"]) ? $template_emails["send_admin_email_student_registered"] : 1;

						if($send_admin_email_student_registered){
							JFactory::getMailer()->sendMail($fromemail, $fromname, $email[$i], $subject, $body, 1);
						}
						
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->clear();
						$query->insert('#__guru_logs');
						$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
						$query->values(intval($data["id"]) . ',' . $db->quote('student-registration') . ',' . '0' . ',' . $db->quote(trim($email[$i])) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
						$db->setQuery($query);
						$db->execute();
					}
				}
				
				// add user to mailchimp
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MCAPI.class.php");
				
				$sql = "select mailchimp_student_api, mailchimp_student_list_id, mailchimp_student_auto from #__guru_config";
				$db->setQuery($sql);
				$mailchimp_details = $db->loadAssocList();
				
				$mailchimp_student_api = $mailchimp_details["0"]["mailchimp_student_api"];
				$mailchimp_student_list_id = $mailchimp_details["0"]["mailchimp_student_list_id"];
				$mailchimp_student_auto = $mailchimp_details["0"]["mailchimp_student_auto"];
				
				if(trim($mailchimp_student_api) != "" && trim($mailchimp_student_list_id) != ""){
					$api = new MCAPI($mailchimp_student_api);
					$mergeVars = array('FNAME'=>$data['firstname'], 'LNAME'=>$data['lastname']);
					$mc_autoregister = false;
					
					if($mailchimp_student_auto == 1){
						$mc_autoregister = true;
					}
					
					$api->listSubscribe($mailchimp_student_list_id, $data["email"], $mergeVars, 'html', $mc_autoregister, true);
				}
			}

			$saved_custom_fields = $this->saveUserCustomFields($data);
			
			if(!$saved_custom_fields){
				$res = false;
			}
		}
		
		if($data['guru_teacher'] == 2){
			$sql = "select id from #__guru_commissions where default_commission=1";
			$db->setQuery($sql);
			$id_commission = $db->loadColumn();
			$id_commission = @$id_commission["0"];
		
			$data['id'] = $user->id;	
			$data["full_bio"] = JFactory::getApplication()->input->get("full_bio","","raw");
			$data["show_email"] = JFactory::getApplication()->input->get("show_email","1","raw");
			$data["forum_kunena_generated"] = JFactory::getApplication()->input->get("forum_kunena_generated","0","raw");
			
			if(!$this->existAuthor($data['id'])){
				$sql = "INSERT INTO #__guru_authors (userid, gid, full_bio, images, emaillink, website, blog, facebook, twitter, show_email, show_website, show_blog, show_facebook, show_twitter, author_title, ordering, forum_kunena_generated,enabled, commission_id) VALUES('".intval($data['id'])."', 2, '".$db->escape($data["full_bio"])."','".$db->escape($data["images"])."', '".intval($db->escape($data["emaillink"]))."', '".$db->escape($data["website"])."', '".$db->escape($data["blog"])."', '".$db->escape($data["facebook"])."', '".$db->escape($data["twitter"])."', '".$data["show_email"]."', '".$data["show_website"]."', '".$data["show_blog"]."', '".$data["show_facebook"]."', '".$data["show_twitter"]."',  '".$db->escape($data["author_title"])."', '".intval($data["ordering"])."', '".$data["forum_kunena_generated"]."', '".$data["enabled"]."', '".$id_commission."' )";
				$db->setQuery($sql);
				if(!$db->execute()){
					$res = false;
				}

				if($teacher_aprove == 0){ // YES
					$sql = "select template_emails, fromname, fromemail, admin_email from #__guru_config";
					$db->setQuery($sql);
					$confic = $db->loadAssocList();
					$template_emails = $confic["0"]["template_emails"];
					$template_emails = json_decode($template_emails, true);
					$fromname = $confic["0"]["fromname"];
					$fromemail = $confic["0"]["fromemail"];
					
					
					$sql = "select u.email from #__users u, #__user_usergroup_map ugm where u.id=ugm.user_id and ugm.group_id='8' and u.id IN (".$confic["0"]["admin_email"].")";
					$db->setQuery($sql);
					$email = $db->loadColumn();
					
					$app = JFactory::getApplication();
					$site_name = $app->getCfg('sitename'); 
					
					$subject = $template_emails["new_teacher_subject"];
					$body = $template_emails["new_teacher_body"];
					
					$subject = str_replace("[AUTHOR_NAME]", $user->name, $subject);
					
					$body = str_replace("[AUTHOR_NAME]", $user->name, $body);
			
					for($i=0; $i< count($email); $i++){
						$send_admin_email_teacher_registered = isset($template_emails["send_admin_email_teacher_registered"]) ? $template_emails["send_admin_email_teacher_registered"] : 1;

						if($send_admin_email_teacher_registered){
							JFactory::getMailer()->sendMail($fromemail, $fromname, $email[$i], $subject, $body, 1);
						}
						
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->clear();
						$query->insert('#__guru_logs');
						$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type') ));
						$query->values(intval($data["id"]) . ',' . $db->quote('teacher-registration') . ',' . '0' . ',' . $db->quote(trim($email[$i])) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''" );
						$db->setQuery($query);
						$db->execute();
					}
				}
				
				// add teacher to mailchimp
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."MCAPI.class.php");
				
				$sql = "select mailchimp_teacher_api, mailchimp_teacher_list_id, mailchimp_teacher_auto from #__guru_config";
				$db->setQuery($sql);
				$mailchimp_details = $db->loadAssocList();
				
				$mailchimp_teacher_api = $mailchimp_details["0"]["mailchimp_teacher_api"];
				$mailchimp_teacher_list_id = $mailchimp_details["0"]["mailchimp_teacher_list_id"];
				$mailchimp_teacher_auto = $mailchimp_details["0"]["mailchimp_teacher_auto"];
				
				if(trim($mailchimp_teacher_api) != "" && trim($mailchimp_teacher_list_id) != ""){
					$name = $data["name"];
					$name_array = explode(" ", $name);
					$FNAME = "";
					$LNAME = "";
					
					if(count($name_array) > 1){
						$LNAME = $name_array[count($name_array) - 1];
						unset($name_array[count($name_array) - 1]);
						$FNAME = implode(" ", $name_array);
					}
					else{
						$FNAME = $data["name"];
					}
					
					$api = new MCAPI($mailchimp_teacher_api);
					$mergeVars = array('FNAME'=>$FNAME, 'LNAME'=>$LNAME);
					$mc_autoregister = false;
					
					if($mailchimp_teacher_auto == 1){
						$mc_autoregister = true;
					}
					
					$api->listSubscribe($mailchimp_teacher_list_id, $data["email"], $mergeVars, 'html', $mc_autoregister, true);
				}
			}
			else{
				$sql = "update #__guru_authors set full_bio='".addslashes(trim($data["full_bio"]))."', images='".addslashes(trim($data["images"]))."', website='".addslashes(trim($data["website"]))."', blog='".addslashes(trim($data["blog"]))."', facebook='".addslashes(trim($data["facebook"]))."', twitter='".addslashes(trim($data["twitter"]))."', show_website='".addslashes(trim($data["show_website"]))."', show_blog='".addslashes(trim($data["show_blog"]))."', show_facebook='".addslashes(trim($data["show_facebook"]))."', show_twitter='".addslashes(trim($data["show_twitter"]))."', author_title='".addslashes(trim($data["author_title"]))."' where userid=".intval($user->id);
				$db->setQuery($sql);
				if(!$db->execute()){
					$res = false;
				}
			}

			$saved_custom_fields = $this->saveUserCustomFields($data);
			
			if(!$saved_custom_fields){
				$res = false;
			}

		}

		//global $mainframe;
		$app = JFactory::getApplication();
		
		if($return = JFactory::getApplication()->input->get('return', '', "raw")) {
			$return = base64_decode($return);
		}

		if($res){
			$reg->clear("tmp_profile");
		}
		
		return array("0"=>$res, "1"=>$user);
	}

	function saveUserCustomFields($data){
		if(isset($data["fields"]) && is_array($data["fields"]) && count($data["fields"]) > 0){
			$db = JFactory::getDbo();
			$fields = $data["fields"];

			$sql = "select * from #__guru_fields_results where `user_id`=".intval($data["id"]);
			$db->setQuery($sql);
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
	
	function update($id){
		$db = JFactory::getDBO();
		$data = JFactory::getApplication()->input->post->getArray();
		$data["full_bio"] = JFactory::getApplication()->input->get("full_bio","","raw");
		
		$sql1 = "UPDATE #__users set name= '".$data["name"]."' WHERE id=".intval($id);
		$db->setQuery($sql1);
		$db->execute();
		
		$sql = "UPDATE #__guru_authors set full_bio= '".addslashes($data["full_bio"])."', images= '".$data["images"]."', emaillink='".$data["emaillink"]."', website='".$data["website"]."', blog='".$data["blog"]."', facebook='".$data["facebook"]."', twitter='".$data["twitter"]."', show_email= '".$data["show_email"]."', show_website='".$data["show_website"]."' , show_blog='".$data["show_blog"]."', show_facebook='".$data["show_facebook"]."', show_twitter='".$data["show_twitter"]."', author_title='".$data["author_title"]."', ordering= '".$data["ordering"]."' WHERE userid=".intval($id);
		$db->setQuery($sql);
		if(!$db->execute()){
			$res = false;
		}
		else{
			$res = true;
		}
		return $res;
	}
	
	function existCustomer($id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_customer where id=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		else{
			return false;
		}
	}
	function existAuthor($id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_authors where userid=".intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if($result[0] > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function addToGroup($user_id){
		$db = JFactory::getDBO();
		$group_id = "";
		
		$studentpage = JFactory::getApplication()->input->get("studentpage", "", "raw");
		
		if($studentpage == "studentpage"){
			$sql = "select `student_group` from #__guru_config where `id`=1";
			$db->setQuery($sql);
			$group_id = $db->loadResult();
		}
		else{
			$sql = "select `st_authorpage` from #__guru_config where `id`=1";
			$db->setQuery($sql);
			$st_authorpage = $db->loadColumn();
			$st_authorpage = json_decode($st_authorpage["0"], true);
			$group_id = $st_authorpage["teacher_group"];
		}

		if(intval($group_id) == 0 || intval($group_id) == 8){
			$group_id = 2;
		}
		
		$sql = "insert into #__user_usergroup_map(user_id, group_id) values('".$user_id."', '".$group_id."')";
		$db->setQuery($sql);
		$db->execute();
		
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function wasBuy($course_id, $user_id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if($result == "0"){
			return false;
		}
		return true;
	}
	
};
?>