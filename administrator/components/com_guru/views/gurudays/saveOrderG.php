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

define('_JEXEC',1);
defined( '_JEXEC' ) or die( 'Restricted access' );
define('JPATH_BASE' , 1);
include_once("../../../../../../configuration.php");
include_once("../../../../../../libraries/joomla/base/object.php");
include_once("../../../../../../libraries/joomla/database/database.php");
include_once("../../../../../../libraries/joomla/database/database/mysql.php");

$data_get = JFactory::getApplication()->input->get->getArray();
$items = explode(",", $data_get['saveString']);

$config = new JConfig();
$options = array ("host" => $config->host,"user" => $config->user,"password" => $config->password,"database" => $config->db,"prefix" => $config->dbprefix);
$database = new JDatabaseMySQL($options);

$sql = " SELECT id, days, tasks FROM #__guru_programstatus WHERE pid = 27 ";
$database->setQuery($sql);
if (!$database->execute()) {
	return;
}
$ids = $database->loadObject();
$the_old_day_order = $ids->days;
$the_old_day_order = explode(';', $the_old_day_order);
$the_old_task_order = $ids->tasks;
$the_old_task_order = explode(';', $the_old_task_order);

$new_daystatus_order = '';
$new_taskstatus_order = '';

foreach($items as $one_item)
	{
		//one_item looks like this ->  6-0:false:202:
		$one_item_array = explode(':', $one_item);
		if($one_item_array[1]=='false')
			{
				// saving the new order
				$day_id = $one_item_array[2];

				$sql = " SELECT ordering FROM #__guru_days WHERE id = ".$day_id;
				$database->setQuery($sql);		
				$database->execute();
				$tmp_ordering = $database->loadResult();		
				
				$old_day_status_array = $the_old_day_order[$tmp_ordering-1];
				$old_day_status_array_expl = explode (',',$old_day_status_array);
				
				$new_daystatus_order = $new_daystatus_order.$day_id.','.$old_day_status_array_expl[1].';';
						
				if(strlen($the_old_task_order[$tmp_ordering-1])>0)		
					$new_taskstatus_order = $new_taskstatus_order.$the_old_task_order[$tmp_ordering-1].';';
				else
					{
						$new_taskstatus_order = $new_taskstatus_order.';';	
					}	
			}	
	}

$sql = "UPDATE #__guru_programstatus 
						SET days='".$new_daystatus_order."',
							tasks='".$new_taskstatus_order."'
						where id='103'";
				$database->setQuery($sql);
				$database->execute();	

$i = 1;
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