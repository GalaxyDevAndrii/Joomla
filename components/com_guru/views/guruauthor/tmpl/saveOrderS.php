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

// we get one day_id to find the program_id
$day_id_array = explode(':',$items[0]);
$day_id = $day_id_array["2"];

/* the response looks like this: the_node_id-the_parent_node_id:type:real_id
the_node_id - not used, not necessary
the_parent_node_id - not used, not necessary (is 0 for ROOT and for a LEAF/SCREEN points to BRANCH/GROUP)
type: false if it's a GROUP
type: true if it's a SCREEN
real_id: the GROUP id or the SCREEN id
6-0:false:202:,1-0:false:73:,2-1:true:11:,3-1:true:13:,4-0:false:72:,5-4:true:13:,7-0:false:203: */

$sql = " SELECT id, days, tasks 
		FROM #__guru_programstatus 
		WHERE pid = (SELECT pid FROM #__guru_days WHERE id = '".$day_id."') ";
$database->setQuery($sql);
if (!$database->execute()) {
	echo $database->stderr();
	return;
}

$ids = $database->loadObject();

$the_old_task_order = $ids->tasks;
$the_old_task_order = substr($the_old_task_order, 0, strlen($the_old_task_order)-1);
$the_old_task_order = explode(';', $the_old_task_order);

$how_many_days = count($the_old_task_order);

$new_taskstatus_order = '';

$skip = 0;
foreach($items as $one_item){
	//one_item looks like this ->  6-0:false:202:
	$one_item_array = explode(':', $one_item);
	$the_id = $one_item_array[2];

	if($one_item_array[1]=='false' && $skip > 0)
		$new_taskstatus_order = $new_taskstatus_order.';';
	elseif($one_item_array[1]=='true')	
		$new_taskstatus_order = $new_taskstatus_order.$the_id.',';	
	$skip++;	
}
	
$new_taskstatus_tmp_order = explode(';', $new_taskstatus_order);

$extracted_from = 0;
$added_to = 0;

for($i = 0; $i < $how_many_days; $i++){
	$tmp_1 = $the_old_task_order[$i];
	//$tmp_1 = substr($tmp_1, 0, strlen($tmp_1)-1);
	$tmp_1 = explode('-', $tmp_1);
	$tmp_1 = count($tmp_1);
		
	$tmp_2 = $new_taskstatus_tmp_order[$i];
	//$tmp_2 = substr($tmp_2, 0, strlen($tmp_2)-1);
	$tmp_2 = explode(',', $tmp_2);
	$tmp_2 = count($tmp_2);		

	if($tmp_1 > $tmp_2)
		$extracted_from = $i;
			
	if($tmp_1 < $tmp_2)
		$added_to = $i;
}

$tmp_old_ = explode('-',$the_old_task_order[$extracted_from]);
$tmp_old_just_ids = '';
foreach($tmp_old_ as $one_tmp_old){
	$just_the_id = explode(',', $one_tmp_old);
	$tmp_old_just_ids = $tmp_old_just_ids.$just_the_id[0].',';
}


$tmp_old_just_ids = substr($tmp_old_just_ids, 0,(strlen($tmp_old_just_ids)-1));
//$tmp_old_just_ids = explode(',', $tmp_old_just_ids);
$tmp_new_ = explode(',',$new_taskstatus_tmp_order[$extracted_from]);

//$the_element_moved = array_diff_assoc($tmp_old_just_ids, $tmp_new_);

foreach($tmp_new_ as $to_erase){
	$tmp_old_just_ids = substr($tmp_old_just_ids, 0, strpos($tmp_old_just_ids, $to_erase.',')).substr($tmp_old_just_ids, (strpos($tmp_old_just_ids, $to_erase.',') + strlen($to_erase) + 1), strlen($tmp_old_just_ids));
}

$the_element_moved = $tmp_old_just_ids;

$the_element_moved_status = substr($the_old_task_order[$extracted_from], (strpos($the_old_task_order[$extracted_from],$the_element_moved.'-') + strlen($the_element_moved) + 1), 1);

// we have found out one of the elements we need
$the_new_day_after_removing = substr($the_old_task_order[$extracted_from], 0, strpos($the_old_task_order[$extracted_from], $the_element_moved.','.$the_element_moved_status.'-')).substr($the_old_task_order[$extracted_from], (strpos($the_old_task_order[$extracted_from], $the_element_moved.','.$the_element_moved_status.'-') + strlen($the_element_moved.','.$the_element_moved_status.'-')), strlen($the_old_task_order[$extracted_from]));
//$the_new_day_after_removing = str_replace($the_element_moved.','.$the_element_moved_status.'-','',$the_old_task_order[$extracted_from]);


$tmp_new_ = explode(',',$new_taskstatus_tmp_order[$added_to]);
$tmp_old_ = explode('-',$the_old_task_order[$added_to]);
$was_inserted = 0;
$the_new_day_after_adding = '';
$the_order = 0;

foreach($tmp_old_ as $one_tmp_old){
	$just_the_id = explode(',', $one_tmp_old);
	$tmp_old_just_ids = $just_the_id[0];

	if($was_inserted == 0){
		if($tmp_old_just_ids == $tmp_new_[$the_order]){
			$the_new_day_after_adding = $the_new_day_after_adding.$one_tmp_old.'-';
		}
		else{
			$the_new_day_after_adding = $the_new_day_after_adding.$the_element_moved.','.$the_element_moved_status.'-'.$one_tmp_old.'-';
			$was_inserted = 1;
		}	
	}else{
		$the_new_day_after_adding = $the_new_day_after_adding.$one_tmp_old.'-';
	}
	$the_order++;	
}


$the_new_day_after_adding = str_replace('--', '-', $the_new_day_after_adding);

$the_new_task_order = $the_old_task_order;
$the_new_task_order [$extracted_from] = $the_new_day_after_removing;
$the_new_task_order [$added_to] = $the_new_day_after_adding;

$the_new_task_order = implode(';',$the_new_task_order);

$the_new_task_order = $the_new_task_order.';';

$sql = "UPDATE #__guru_programstatus 
		SET tasks='".$the_new_task_order."'
		where id='".$ids->id."'";
$database->setQuery($sql);
$database->execute();		

$i = 0;

$id_modules = array();
foreach($items as $one_item){
	$one_item_array = explode(':', $one_item);
	if(@$one_item_array["1"] == 'false'){
		$temp = explode("-", $one_item_array["0"]);
		@$id_modules[$temp["0"]] = $one_item_array["2"];
	}
}

foreach($items as $one_item){
	//one_item looks like this ->  6-0:false:202:
	$one_item_array = explode(':', $one_item);
	
	if(@$one_item_array["1"] == 'true'){
		$temp = $one_item_array["0"];
		$temp = explode("-", $temp);
		$module_id = $id_modules[$temp["1"]];
		$lesson_id = $one_item_array["2"];
		
		$sql = "UPDATE #__guru_task 
				SET ordering='".$i."'
				where id='".$lesson_id."'";
		$database->setQuery($sql);
		$database->execute();	

		$sql = "update #__guru_mediarel set type_id=".$module_id."
				WHERE type='dtask' AND media_id='".$lesson_id."'";
		$database->setQuery($sql);	
		$database->execute();
		
		$i++;
	}
}
?>