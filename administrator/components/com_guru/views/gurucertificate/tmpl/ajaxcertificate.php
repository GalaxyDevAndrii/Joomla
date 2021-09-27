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

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

define('_JEXEC',1);
defined( '_JEXEC' ) or die( 'Restricted access' );
define('JPATH_BASE', substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "/administra")) );

if (!isset($_SERVER["HTTP_REFERER"])) exit("Direct access not allowed.");
include("../../../../../../configuration.php");

$mosConfig_absolute_path =substr(JPATH_BASE, 0, strpos(JPATH_BASE, "/administra")); 

define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.php');

$config = new JConfig();

$db = JFactory::getDBO();; 
$query="update #__guru_certificates set design_background ='' where id='1'";
$db->setQuery($query);
if($db->execute()){
	$image_selected = JFactory::getApplication()->input->get("image_selected", "");
	if(trim($image_selected) != ""){
		$image_selected_array = explode("/", $image_selected);
		$image_selected = $image_selected_array[count($image_selected_array) - 1];
		
		unlink(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."stories".DIRECTORY_SEPARATOR."guru".DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR.$image_selected);
		unlink(JPATH_SITE.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."stories".DIRECTORY_SEPARATOR."guru".DIRECTORY_SEPARATOR."certificates".DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$image_selected);
	}
	echo "2";
}
else{
	echo $query;
}
?>