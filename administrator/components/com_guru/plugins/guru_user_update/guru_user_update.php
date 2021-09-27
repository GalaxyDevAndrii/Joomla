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

class PlgUserGuru_user_update extends JPlugin{
	
	function encriptPassword($password){
	  $salt = "";
	  for($i=0; $i<=32; $i++) {
	   $d = rand(1,30)%2;
		 $salt .= $d ? chr(rand(65,90)) : chr(rand(48,57));
		 }  
	  $hashed = md5($password.$salt);
	  $encrypted = $hashed.':'.$salt;
	  return $encrypted;
	}
		
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		$guruses    = JFactory::getSession();
		$guru_data = $guruses->get("guru_data","");
		$guru_action = $guruses->get("guru_action","0");
		$option = JFactory::getApplication()->input->get("option","");
		$view = JFactory::getApplication()->input->get("view","");
		$task = JFactory::getApplication()->input->get("task","");
		
		
		if($guru_action == "1"&& $option == "com_community" && $view == "register" && $task == "registerUpdateProfile"){
			$db = JFactory::getDbo();
			$data = json_decode($guru_data, true);
			$userpassword = $this->encriptPassword($data['password']);
			if($data["guru_teacher"] == 1){
				$sql = "insert into #__guru_customer(id, company, firstname, lastname) values (".intval($user['id']).", '".addslashes(trim($data['company']))."', '".addslashes(trim($data['firstname']))."', '".addslashes(trim($data['lastname']))."')";
			}
			elseif($data["guru_teacher"] == 2){
				$data["full_bio"] = JFactory::getApplication()->input->get("full_bio","","raw");
				$sql = "INSERT INTO #__guru_authors (userid, gid, full_bio, images, emaillink, website, blog, facebook, twitter, show_email, show_website, show_blog, show_facebook, show_twitter, author_title, ordering, forum_kunena_generated) VALUES('".intval($user['id'])."', 2, '".$data["full_bio"]."', '".$data["images"]."', '".$data["emaillink"]."', '".$data["website"]."', '".$data["blog"]."', '".$data["facebook"]."','".$data["twitter"]."', '".$data["show_email"]."', '".$data["show_website"]."', '".$data["show_blog"]."', '".$data["show_facebook"]."', '".$data["show_twitter"]."',  '".$data["author_title"]."', '".$data["ordering"]."', '')";
			}
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "delete from  #__community_users where userid = 0";
			$db->setQuery($sql);
			$db->execute();
			
			$sql = "update #__users set password ='".$userpassword."' where id =".intval($user['id']);
			$db->setQuery($sql);
			$db->execute();
			
			$guruses->set("guru_data","");
			$guruses->set("guru_action","0");
		}
	}

}