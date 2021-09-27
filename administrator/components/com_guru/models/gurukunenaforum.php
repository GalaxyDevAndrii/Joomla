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


class guruAdminModelguruKunenaForum extends JModelLegacy {
	var $_packages;
	var $_package;
	var $_tid = null;
	var $_total = 0;
	var $_pagination = null;

	
	function savekunenadetails() {
		$post_value = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		
		$sql = "select count(*) from #__extensions where element='com_kunena'";
		$db->setQuery($sql);
		$db->execute();
		$count = $db->loadResult();
		if($count >0){
			$sql = "UPDATE #__guru_kunena_forum set forumboardcourse='".$post_value["autoforumk"]."', forumboardlesson= '".$post_value["autoforumk1"]."',	forumboardteacher ='". $post_value["autoforumk2"]."', deleted_boards='".$post_value["deleted_boards"]."', allow_stud='".$post_value["allow_stud"]."', allow_edit='".$post_value["allow_edit"]."',allow_delete='".$post_value["allow_delete"]."', `kunena_category`='".$post_value["kunena_category"]."' ";
			$db->setQuery($sql);
			$db->execute();
			return true;
		}
		else{
			return false;
		}
	}
	
	function getKunenaforumDetails(){
	  	$db = JFactory::getDBO();
		$sql = "SELECT * from #__guru_kunena_forum where id='1'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadObject();
		return $result;
	}

	function getKunenaCategories(){
		$result = array();
		$db = JFactory::getDbo();
		
		$sql ="select count(extension_id) from #__extensions WHERE name='com_kunena'";
		$db->setQuery($sql);
		$db->execute();
		$com_kunena = $db->loadResult();

		if(intval($com_kunena) > 0){
			include_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_kunena".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."categories.php");
			$kunena = new KunenaAdminModelCategories();
			$result = $kunena->getAdminCategories();
		}

		return $result;
	}
};

?>