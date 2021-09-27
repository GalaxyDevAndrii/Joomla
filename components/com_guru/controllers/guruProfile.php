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
jimport ('joomla.application.component.controller');

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

class guruControllerguruProfile extends guruController {
	var $model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "edit");
		$this->registerTask ("edit", "edit");
		$this->registerTask ("editform", "edit");
		$this->registerTask ("register", "edit");
		$this->registerTask ("saveCustomer", "save");
		$this->registerTask ("loginform", "loginform");
		$this->registerTask ("required_courses_message", "requiredCoursesMessage");
		$this->registerTask ("get_required_courses_message", "getRequiredCoursesMessage");
		$this->registerTask ("deleteStudentProfile", "deleteStudentProfile");
		$this->registerTask("deleteTeacherProfile", "deleteTeacherProfile");

		$this->_model = $this->getModel('guruCustomer');
	}

	function edit(){
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		if($user_id == "0"){		
			$helper = new guruHelper();
			$Itemid = $this->input->getInt('Itemid', 0);
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];
			
			if(intval($itemid_seo) > 0){
				$Itemid = intval($itemid_seo);
			}
		
			$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&Itemid=".$Itemid, false));
			return true;
		}

		$view = $this->getView("guruProfile", "html");
		$view->setLayout("editForm");
		$view->editForm();
	}
	
	function buy () {
		$view = $this->getView("guruProfile", "html");
		$view->setLayout("buy");
		$view->buy();
	}	

	function login(){
		$view = $this->getView("guruProfile", "html");
		$view->setLayout("editform");
		$view->login();
	}
	
	function loginform(){
		$view = $this->getView("guruProfile", "html");
		$view->setLayout("loginform");
		$view->loginform();
	}

	function logCustomerIn(){
		//global $mainframe;
		$app = JFactory::getApplication();
		if($return = JFactory::getApplication()->input->get('return', '')){
			$return = base64_decode($return);
		}
		$options = array();
		$options['remember'] = JFactory::getApplication()->input->get('remember', false);
		$options['return'] = $return;
		$username = JFactory::getApplication()->input->get("username", "");
		$password = JFactory::getApplication()->input->get("passwd", "");
		$credentials = array();
		$credentials['username'] = $username;
		$credentials['password'] = $password;
		$err = $app->login($credentials, $options);
		$graybox = JFactory::getApplication()->input->get("graybox", "");
		$course_id = intval(JFactory::getApplication()->input->get("course_id", ""));
		if($graybox == "true" || $graybox == "1"){
			if(isset($err) && $err === FALSE){
				$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruProfile&task=loginform&course_id=".intval($course_id)."-".$alias.$action."&returnpage=guruprograms&graybox=true&tmpl=component"), JText::_("GURU_LOGIN_FAILED"), "notice");
				return true;
			}
			else{
				$db = JFactory::getDBO();
				$user = JFactory::getUser();
				$user_id = $user->id;
				$courses = intval($course_id)."-0.0-1";
				$amount = 0;
				$buy_date = date("Y-m-d H:i:s");
				$plan_id = "1";
				$order_expiration = "0000-00-00 00:00:00";
				$jnow = new JDate('now');
				$current_date_string = $jnow->toSQL(true);
				
				$sql = "select count(*) from #__guru_buy_courses where userid=".intval($user_id)." and course_id=".intval($course_id)." and order_id='0' and expired_date < '".$current_date_string."'";
				$db->setQuery($sql);
				$db->execute();
				$result = $db->loadResult();
				if($result == 0){// add a new license
					$sql = "insert into #__guru_buy_courses (userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values (".$user_id.", 0 , ".$course_id.", '".$amount."', '".$buy_date."', '".$order_expiration."', '".$plan_id."', 0)";
					$db->setQuery($sql);
					$db->execute();	
					
					$sql = "select currency from #__guru_config where id=1" ;
					$db->setQuery($sql);
					$db->execute();
					$currency = $db->loadResult();
		
					$sql = "insert into #__guru_order (id, userid, order_date, courses, status, amount, amount_paid, processor, number_of_licenses, currency, promocodeid, published, form) values (0, ".intval($user_id).", '".$buy_date."', '".intval($course_id)."-0-1', 'Paid', '0', '-1','paypaypal','0','".$currency."','0','1', '')";
					$db->setQuery($sql);
					$db->execute();
					
					$msg = JText::_("GURU_ENROLL_SUCCESSFULLY");			
				}
				else{
					$msg = JText::_("GURU_ALREADY_ENROLLED");
				}
				
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('joomlamessage', $msg);
							
				echo '<script type="text/javascript">';
				echo 'window.parent.location.reload(true);';
				echo '</script>';	
				die();
				return true;
			}
		}
		$link = 'index.php?option=com_guru&view=guruPrograms&task=myprograms';
		$this->setRedirect(JRoute::_($link), $msg);
	}

	function registerCustomer(){
		jimport("joomla.database.table.user");
		$db = JFactory::getDBO();
		$user = new JUser();
		$currentuser = new JUser();
		$res = true;
		$item = $this->getTable('guruCustomer');
		$data = JFactory::getApplication()->input->post->getArray();
		$iduser = intval($data['user_id']);
		
		
		$sql = "select student_group from #__guru_config where id=1" ;
		$db->setQuery($sql);
		$db->execute();
		$student_group = $db->loadResult();
		
		$sql = "select title from #__usergroups where id='".$student_group."'" ;
		$db->setQuery($sql);
		$db->execute();
		$title = $db->loadResult();
		
		
		//update user
		if ($iduser!=0)	$currentuser->load($iduser);
		$oldpass = $currentuser->password;
		$user->bind($data);
		if (isset($data['password']) && $data['password']!="") $currentuser->password=$user->password;
		//update user
		
		if (!isset($user->registerDate)) $user->registerDate = date( 'Y-m-d H:i:s' );
		if (!isset($user->block)) $user->block = 0;
		$user->usertype = ''.$title.'';
		$sqls = "SELECT id FROM #__core_acl_aro_groups WHERE name='".$title."'";
		$db->setQuery($sqls);
		$reggroup = $db->loadResult();		
		$user->gid = $reggroup;
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		if ($currentuser->id>0) {
			$currentuser->bind($data); 
			if (strlen($data_post['password']) < 5) $currentuser->password=$oldpass;
			$currentuser->id = $iduser;
			$currentuser->name = $data['fullname'];
			if (!$currentuser->save()) {
				$error = $user->getError();
				echo $error;
				$res = false;
			}
		} else {
			if (!$user->save()) {
				$error = $user->getError();
				echo $error;
				$res = false;
			}
		}
		if ($res) {	
			if ($data['user_id']==0) {
				$ask = "SELECT id FROM #__users ORDER BY id DESC LIMIT 1 ";
				$db->setQuery( $ask );
				$where = $db->loadResult();
				$data['user_id'] = $where;
			}
			
			$data_post = JFactory::getApplication()->input->post->getArray();
			
			if (!isset($data['fullname'])) $data['fullname'] = $data_post['fullname'];			
			
			if (!$item->bind($data)){
			 	$res = false;
			}
			if (!$item->check()) {
				$res = false;
			}
			if (!$item->store()) {
				$res = false;
			}
		}		
				
		return $res;

	}	

	function save(){
		$link = $this->getLink();
		$model = $this->getModel("guruProfile");
		if($model->store()){
			$msg = JText::_('DSCUSTOMERSAVED');
		}
		else{
			$msg = JText::_('DSCUSTOMERSAVEERR');
			$link = "index.php?option=com_guru&view=guruProfile&task=edit";
		}
		
		$userr = JFactory::getUser();
		$logged_student = $userr->get("id");
		
		if($logged_student){
			$msg = JText::_('GURU_CUST_SAVED');
		}
		
		$this->setRedirect(JRoute::_($link, false), $msg);
	}


	function getLink() {
		$return = JFactory::getApplication()->input->get("returnpage", "");
		$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
	
		switch ($return) {
			case "myorders":
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruorders"];
					
					if(intval($itemid_seo) > 0){
						$itemid = intval($itemid_seo);
					}
			
			       	$link = "index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=".intval($itemid);
				break;
			case "checkout":
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["gurubuy"];
					
					if(intval($itemid_seo) > 0){
						$itemid = intval($itemid_seo);
					}
			
			       	$link = "index.php?option=com_guru&view=guruBuy&task=checkout&from=profile&Itemid=".intval($itemid);
				break;	
			default:
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprofile"];
					
					if(intval($itemid_seo) > 0){
						$Itemid = intval($itemid_seo);
					}
					else{
						$user = JFactory::getUser();
						$user_id = $user->id;

		            	$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

		            	if(intval($itemid_menu) > 0){
		                    $Itemid = intval($itemid_menu);
		                }
		            }
					
			       	$link = "index.php?option=com_guru&view=guruProfile&task=edit&Itemid=".intval($itemid);
				break;
		}

		$lang = JFactory::getApplication()->input->get('lang', '', "raw");

		if(isset($lang) && trim($lang) != ""){
			$lang = explode("-", $lang);
			$link .= "&lang=".$lang["0"];
		}

		return $link;
	}
	
	function requiredCoursesMessage(){
		echo '<div class="alert alert-info">' . JText::_("GURU_REQUIRED_COURSES_MESSAGE") . '</div>';
	}
	
	function getRequiredCoursesMessage(){
		$ret = array(
			"message" => '<div class="alert alert-info">' . JText::_("GURU_REQUIRED_COURSES_MESSAGE") . '</div>'
		);
	}

	function deleteStudentProfile(){
    	$user = JFactory::getUser();
    	$db = JFactory::getDbo();

		$sql = 'INSERT INTO #__jagdpr_activities (id, userid, username, email, name, task, status, plugin, date_add) VALUES
					(NULL, '.$db->quote($user->id).', 
					'.$db->quote($user->username).', 
					'.$db->quote($user->email).', 
					'.$db->quote($user->name).', 
					'.$db->quote('deleted').', 
					'.$db->quote('completed').',  
					'.$db->quote('guru student').',
					NOW())';
		$db->setQuery($sql);
		$db->execute();

		$sql = 'DELETE FROM #__guru_customer WHERE `id` = '.$user->id;
		$db->setQuery($sql);
		$db->execute();

		if ($db->getAffectedRows() && empty($redirect)) {
			JFactory::getApplication()->enqueueMessage(JText::_('GURU_DELETE_MESSAGE_SUCCESS'));
			JFactory::getApplication()->redirect(JUri::base());
	    	jexit();
		}
	}

	function deleteTeacherProfile(){
    	$user = JFactory::getUser();
    	$db = JFactory::getDbo();

		$sql = 'INSERT INTO #__jagdpr_activities (id, userid, username, email, name, task, status, plugin, date_add) VALUES
					(NULL, '.$db->quote($user->id).', 
					'.$db->quote($user->username).', 
					'.$db->quote($user->email).', 
					'.$db->quote($user->name).', 
					'.$db->quote('deleted').', 
					'.$db->quote('completed').',  
					'.$db->quote('guru teacher').',
					NOW())';
		$db->setQuery($sql);
		$db->execute();

		$sql = 'DELETE FROM #__guru_authors WHERE `userid` = '.$user->id;
		$db->setQuery($sql);
		$db->execute();

		if ($db->getAffectedRows() && empty($redirect)) {
			JFactory::getApplication()->enqueueMessage(JText::_('GURU_DELETE_MESSAGE_SUCCESS'));
			JFactory::getApplication()->redirect(JUri::base());
	    	jexit();
		}
	}
};

?>