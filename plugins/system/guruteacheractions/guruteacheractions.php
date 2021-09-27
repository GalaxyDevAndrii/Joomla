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
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class  plgSystemGuruTeacherActions extends JPlugin{

	public function __construct(&$subject, $config){
		parent::__construct($subject, $config);
	}
	
	public function onAfterRoute(){
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			return true;
		}
		
		$option = JFactory::getApplication()->input->get("option", "");
		$task = JFactory::getApplication()->input->get("task", "");
		$token = JFactory::getApplication()->input->get("token", "");
		$g = JFactory::getApplication()->input->get("g", "");
		
		if($option == "com_users" && $task == "registration.activate" && $g == 1){
			$db = JFactory::getDBO();
			
			$sql = "select u.* from #__users u, #__guru_authors a where u.activation='".trim($token)."' and u.id=a.userid";
			$db->setQuery($sql);
			$db->execute();
			$result = $db->loadAssocList();
			
			if(isset($result) && count($result) > 0){
				$sql = "select template_emails, fromname, fromemail, admin_email from #__guru_config";
				$db->setQuery($sql);
				$db->execute();
				$confic = $db->loadAssocList();
				$template_emails = $confic["0"]["template_emails"];
				$template_emails = json_decode($template_emails, true);
				$fromname = $confic["0"]["fromname"];
				$fromemail = $confic["0"]["fromemail"];
				
				$sql = "select u.email from #__users u, #__user_usergroup_map ugm where u.id=ugm.user_id and ugm.group_id='8' and u.id IN (".$confic["0"]["admin_email"].")";
				$db->setQuery($sql);
				$db->execute();
				$email = $db->loadColumn();
	
				
				$app = JFactory::getApplication();
				$site_name = $app->getCfg('sitename'); 
				
				$subject = $template_emails["ask_teacher_subject"];
				$body = $template_emails["ask_teacher_body"];
				
				$subject = str_replace("[AUTHOR_NAME]", $result["0"]["name"], $subject);
				
				$body = str_replace("[AUTHOR_NAME]", $result["0"]["name"], $body);
				
				for($i=0; $i< count($email); $i++){
					$send_admin_email_teacher_approved = isset($template_emails["send_admin_email_teacher_approved"]) ? $template_emails["send_admin_email_teacher_approved"] : 1;

					if($send_admin_email_teacher_approved){
						JFactory::getMailer()->sendMail($fromemail, $fromname, $email[$i], $subject, $body, 1);
					}
				}
			}
		}
	}
}

?>
