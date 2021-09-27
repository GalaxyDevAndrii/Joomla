<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/
define( '_JEXEC', 1 );
defined( '_JEXEC' ) or die( 'Restricted access' );
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "components")),0,-1));
define( 'DS', DIRECTORY_SEPARATOR );

$parts = explode(DS, JPATH_BASE);
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'configuration.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.php');

$db = JFactory::getDBO();

$course_id = JFactory::getApplication()->input->get("course_id", "0");
$sql = "update #__guru_program set id_final_exam='0' where id=".intval($course_id);
$db->setQuery($sql);
if($db->execute()){
	return true;
}
else{
	return false;
}
?>