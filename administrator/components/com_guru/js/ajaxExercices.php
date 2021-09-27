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

$db = JFactory::getDBO();		
$id = $_REQUEST["id"];
$task = $_REQUEST["task"];

if($task == 'addcourse_ajax'){
	$sql = "select published from #__guru_program where id =".$id;
	$db->setQuery($sql);
	$db->execute();
	$published = $db->loadColumn();
	$published = $published["0"];
	
	if($published){
		$sql = "update #__guru_program set published='0' where id =".$id;
		$ret = 'unpublish';
	}
	else{
		$ret = 'publish';
		$sql = "update #__guru_program set published='1' where id =".$id;
	}	
}
else{
	$sql = "select published from #__guru_media where id =".$id;
	$db->setQuery($sql);
	$db->execute();
	$published = $db->loadColumn();
	$published = $published["0"];
	
	if($published){
		$sql = "update #__guru_media set published='0' where id =".$id;
		$ret = 'unpublish';
	}
	else{
		$ret = 'publish';
		$sql = "update #__guru_media set published='1' where id =".$id;
	}
}
$db->setQuery($sql);
if (!$db->execute() ){
	//$this->setError($db->getErrorMsg());
	return false;
}

echo $ret;
die();
?>	