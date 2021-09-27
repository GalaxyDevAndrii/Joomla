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

$task = JFactory::getApplication()->input->get("task", "");
if($task == "user.login" || $task == "user.logout"){
	$username = JFactory::getApplication()->input->get("username", "");
    $password = JFactory::getApplication()->input->get("password", "");
	$return = JFactory::getApplication()->input->get("return", "");
	define(JPATH_COMPONENT, JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_users");
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_users".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."user.php");
	if($task == "user.login"){
		UsersControllerUser::login();
	}
	else{
		UsersControllerUser::logout();
	}
}
?>