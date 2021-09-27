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

class guruAdminModelguruadmin extends JModelLegacy {
	
	function __construct () {
		parent::__construct();
	}

	function getCoponentStatus(){
		$text = "";
		if($this->isCurlInstalled() == true){		
			$data = 'http://www.ijoomla.com/annoucements/guru_announcements_version_joomla3x.txt';
			$ch = @curl_init($data);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			@curl_setopt($ch, CURLOPT_TIMEOUT, 10); 							
			$text = @curl_exec($ch);				
		}
		return $text;
	}
	
	function isCurlInstalled() {
	    $array = get_loaded_extensions();
		if(in_array("curl", $array)){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getNotificationStatus(){
		$db = JFactory::getDBO();
		$sql = "SELECT notification FROM #__guru_config ";
		$db->setQuery($sql);
		$result = $db->loadColumn();		
		return $result["0"];
	}
};
?>