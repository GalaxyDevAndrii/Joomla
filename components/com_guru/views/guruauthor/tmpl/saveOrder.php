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
//require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'methods.php');
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'configuration.php' );
//require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'object.php');
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database.php');
//require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'mysql.php');
//require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

$config = new JConfig();

$options = array ("host" => $config->host, "user" => $config->user, "password" => $config->password, "database" => $config->db,"prefix" => $config->dbprefix);

$database =  JFactory::getDBO();
$data_get = JFactory::getApplication()->input->get->getArray();

/* the response looks like this: the_node_id-the_parent_node_id:type:real_id
the_node_id - not used, not necessary
the_parent_node_id - not used, not necessary (is 0 for ROOT and for a LEAF/SCREEN points to BRANCH/GROUP)
type: false if it's a GROUP
type: true if it's a SCREEN
real_id: the GROUP id or the SCREEN id
6-0:false:202:,1-0:false:73:,2-1:true:11:,3-1:true:13:,4-0:false:72:,5-4:true:13:,7-0:false:203: */
$i=1;
$items = explode(",",$data_get['saveString']);
foreach($items as $one_item)
	{
		//one_item looks like this ->  6-0:false:202:
		$one_item_array = explode(':', $one_item);
		if($one_item_array[1]=='false')
			{
				// saving the new order
				$day_id = $one_item_array[2];
			
				$sql = "UPDATE #__guru_days 
						SET ordering='".$i."'
						where id='".$day_id."'";
				$database->setQuery($sql);
				$database->execute();	
				
				// deleting the old day-task relation
				$sql = "DELETE FROM #__guru_mediarel 
						WHERE type='dtask' AND type_id='".$day_id."'";
				$database->setQuery($sql);	
				$database->execute();			
				
				$i++;
			}	
		else
			{	
				$task_id = $one_item_array[2];
				$sql = "INSERT INTO #__guru_mediarel (type,type_id,media_id) VALUES ('dtask','".$day_id."','".$task_id."')";
				$database->setQuery($sql);
				$database->execute();
			}
	}
?>