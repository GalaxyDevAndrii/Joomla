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

defined( '_JEXEC' ) or die( 'Restricted access' );

$db = JFactory::getDBO();

$action = JFactory::getApplication()->input->get("action", "");
if($action == "check_values"){
	$name = JFactory::getApplication()->input->get("name", "");
	$alias = JFactory::getApplication()->input->get("alias", "");
	$id = JFactory::getApplication()->input->get("id", "0");
	$avatar = JFactory::getApplication()->input->get("avatar", "");
	
	if(trim($alias) != ""){
		$sql = "select count(*) from #__guru_program where alias='".addslashes(trim($alias))."' and id <> ".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		$count = @$count["0"];
		if($count > 0){
			echo "exist";
		}
		else{
			echo "not exist";
		}
	}
	else{
		$sql = "select count(*) from #__guru_program where name='".addslashes(trim($name))."' and id <> ".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadColumn();
		$count = @$count["0"];
		if($count > 0){
			echo "exist";
		}
		else{
			echo "not exist";
		}
	}
	die();
}

// start for remove image action
if(isset($_REQUEST['id']) && $_REQUEST['id']>0){ 

		
	if($_REQUEST['avatar'] == 0){
		$query = "update #__guru_program set image='' where id='".intval($_REQUEST['id'])."'";
	}
	elseif($_REQUEST['avatar'] == 1){
		$query = "update #__guru_program set image_avatar='' where id='".intval($_REQUEST['id'])."'";
	}
	$db->setQuery($query);
	
	if($db->execute()){
		echo "2";
	}
	else{
		echo $query;
	}
}
// end for remove image action
?>