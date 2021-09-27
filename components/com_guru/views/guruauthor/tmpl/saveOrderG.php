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

$database =  JFactory::getDBO();
$saveString = $_SERVER['QUERY_STRING'];
$saveString = str_replace("saveString=", "", $saveString);
// we get one day_id to find the program_id

$temp_items = array();
for($i=1; $i<=100; $i++){
	$result = JFactory::getApplication()->input->get("saveString".$i, "null", "raw");
	if($result == "null"){
		break;
	}
	else{
		$temp_items[] = $result;
	}
}
$temp_items = implode(",", $temp_items);

$items = explode(",", $temp_items);

$day_id_array = explode(':',$items[0]);
$day_id = $day_id_array[2];

/* the response looks like this: the_node_id-the_parent_node_id:type:real_id
the_node_id - not used, not necessary
the_parent_node_id - not used, not necessary (is 0 for ROOT and for a LEAF/SCREEN points to BRANCH/GROUP)
type: false if it's a GROUP
type: true if it's a SCREEN
real_id: the GROUP id or the SCREEN id
6-0:false:202:,1-0:false:73:,2-1:true:11:,3-1:true:13:,4-0:false:72:,5-4:true:13:,7-0:false:203: */

$sql = " SELECT id, days, tasks FROM #__guru_programstatus WHERE pid = (SELECT pid FROM #__guru_days WHERE id = '".$day_id."') ";
$database->setQuery($sql);
if (!$database->execute()) {
	echo $database->stderr();
	return;
}
$ids = $database->loadObject();
$the_old_day_order = @$ids->days;
$the_old_day_order = explode(';', $the_old_day_order);
$the_old_task_order = @$ids->tasks;
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
						where id='".$ids->id."'";
				$database->setQuery($sql);
				$database->execute();	

$i = 1;
foreach($items as $one_item){
	//one_item looks like this ->  6-0:false:202:
	$one_item_array = explode(':', $one_item);
	
	if(@$one_item_array["1"] == 'false'){
		$module_id = $one_item_array["2"];
		
		$sql = "UPDATE #__guru_days 
				SET ordering='".$i."'
				where id='".$module_id."'";
		$database->setQuery($sql);
		$database->execute();
		$i++;
	}
}
?>