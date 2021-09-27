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


class guruModelguruDays extends JModelLegacy {
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;


	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");

		$this->setId((int)$cids);
		global $option;
		$app = JFactory::getApplication();

		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart = $app->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);

	}


	function setId($id) {
		$this->_id = $id;

		$this->_license = null;
	}


	function getprogramname () {
		$data_get = JFactory::getApplication()->input->get->getArray();
		
		$sql = "SELECT * FROM #__guru_program WHERE id= (SELECT pid FROM #__guru_days WHERE id= ".$data_get['cid'].") ";
		$programname = $this->_getList($sql);
		return $programname;
	}
	
	function getlistTask ($dayid) {
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		$sql = "SELECT tasks FROM #__guru_programstatus WHERE pid = (SELECT pid FROM #__guru_days WHERE id= ".$dayid.") AND userid = ".$my->id;
		$database->setQuery($sql);
		$result = $database->loadResult();	
		return $result;
	}	
	
	function getTask ($taskid) {
		$database = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_task WHERE id = ".$taskid;
		$database->setQuery($sql);
		$result = $database->loadObject();	
		return $result;
	}		

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getlistDays();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}


	function getlistDays () {
		if (empty ($this->_licenses)) {
			$sql = "select * from #__guru_days";
			$this->_licenses = $this->_getList($sql);
		}		
		return $this->_licenses;

	}	

	function getday() {
		if (empty ($this->_license)) {
			$sql = "select * from #__guru_days where id=".$this->_id;
			$this->_total = $this->_getListCount($sql);
			
			$this->_license = $this->_getList($sql);
			if (count ($this->_license) > 0) $this->_license = $this->_license[0];
			else {
				$this->_license = $this->getTable("guruDays");
				$this->_license->username = "";
			}
		}
		return $this->_license;

	}
	function getConfigs() {
		$db = JFactory::getDBO();
		
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadObject();	
		return $result;

	}
	
	function find_if_rogram_was_bought($userid, $progid){
		// returns 1 if the program is already bought
		// return 0 if the program is TRIAL or wasn't bought
		$database = JFactory::getDBO();
		$sql = "SELECT payment FROM #__guru_order WHERE userid = '".$userid."' AND programid = ".$progid;
		$database->setQuery($sql);
		$result = $database->loadResult();
		if (strtolower($result) == 'trial' || !isset($result))	
			return 0;
		else return 1;	
	}	
	
	function find_status_line_for_program($userid, $progid){
		$database = JFactory::getDBO();
		$sql = "SELECT days,tasks FROM #__guru_programstatus WHERE userid = '".$userid."' AND pid = ".$progid;
		$database->setQuery($sql);
		$result = $database->loadObject();
		return $result;
	}
	
	function find_id_for_first_uncompleted_day($day_array){
		$search_id_for_first_day_uncompleted = -1;
		foreach($day_array as $day_array_key=>$day_array_value)
			{
				if (strpos($day_array_value, ',0')!==FALSE || strpos($day_array_value, ',1')!==FALSE)
					{
						$search_id_for_first_day_uncompleted = $day_array_key;
						break;
					}
			}
		
		$search_array_id_for_first_day_uncompleted = explode(',', $day_array[$search_id_for_first_day_uncompleted]);
		$id_for_first_day_uncompleted = $search_array_id_for_first_day_uncompleted[0];
	
		return $id_for_first_day_uncompleted.','.($search_id_for_first_day_uncompleted+1);
	}	


	function find_id_for_first_uncompleted_task($task_array){
		$search_id_for_first_task_uncompleted = 0;
		
		foreach($task_array as $task_array_key=>$task_array_value)
			{
				if (strpos($task_array_value, ',0')!==FALSE || strpos($task_array_value, ',1')!==FALSE)
					{
						$search_id_for_first_task_uncompleted = $task_array_key;
						break;
					}
			}

		$search_array_id_for_first_task_uncompleted = explode(',', $task_array[$search_id_for_first_task_uncompleted]);
		$id_for_first_task_uncompleted = $search_array_id_for_first_task_uncompleted[0];
		return $id_for_first_task_uncompleted.','.($search_id_for_first_task_uncompleted+1);
	}		
	
	function program_status($userid, $progid){
		$database = JFactory::getDBO();
		$sql = "SELECT status FROM #__guru_programstatus WHERE userid = '".$userid."' AND pid = ".$progid;
		$database->setQuery($sql);
		$result = $database->loadResult();
		return $result;
	}	
	
	function create_status_line_for_program ($progid){
		$db = JFactory::getDBO();
		$sql = "SELECT id FROM #__guru_days 
				WHERE pid = ".$progid." ORDER BY ordering ASC";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$day_status = '';
		$task_status = '';
		foreach($result as $day)
			{$day_status = $day_status.$day->id.',0;'; 
						
			$sqltasks = "SELECT media_id FROM #__guru_mediarel 
						WHERE type_id = ".$day->id." AND type = 'dtask' ";
			$db->setQuery($sqltasks);
			$resultt = $db->loadObjectList();
			foreach($resultt as $task)
					{$task_status = $task_status.$task->media_id.',0-';}
						$task_status = $task_status.';';		
					}
					
		return $day_status.'$$$$$'.$task_status;			
	}
	
	function find_link_text_for_day_resume_button($day_array, $task_array, $status){
		//$day_array = $status_line->days;
		$day_array = explode(';', $day_array);
		$how_many_days = count($day_array)-1;
		$day_id_to_get_started_array = explode(',', $day_array[0]); 
		
		//$task_array = $status_line->tasks;
		$task_array = explode(';', $task_array);
		$task_id_array = explode('-', $task_array[0]);
		$task_id_to_get_started_array = explode(',', $task_id_array[0]); 

		// we find the id for the first day who isn't completed

		
		if($status=='1')
			{	
				$first_day_uncompleted = guruModelguruDays::find_id_for_first_uncompleted_day($day_array);
				$first_day_uncompleted = explode(',', $first_day_uncompleted);
				$id_for_first_day_uncompleted = $first_day_uncompleted[0];
				$ordering_for_first_day_uncompleted = $first_day_uncompleted[1];

				$first_task_uncompleted = guruModelguruDays::find_id_for_first_uncompleted_task(explode('-',$task_array[($ordering_for_first_day_uncompleted-1)]));
				$first_task_uncompleted = explode(',', $first_task_uncompleted);
				$id_for_first_task_uncompleted = $first_task_uncompleted[0];
				$ordering_for_first_task_uncompleted = $first_task_uncompleted[1];
			}

if ($status=='0' && isset($task_id_to_get_started_array[0]) && $task_id_to_get_started_array[0]>0 && isset($day_id_to_get_started_array[0]) && $day_id_to_get_started_array[0]>0)
	{
		$link_for_resume = 	'index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid='.$day_id_to_get_started_array[0];
		$text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_GETSTARTED');
	}
	
if ($status=='1' && isset($id_for_first_task_uncompleted) && $id_for_first_task_uncompleted>0 && isset($id_for_first_day_uncompleted) && $id_for_first_day_uncompleted>0)
	{
		$link_for_resume = 	'index.php?option=com_guru&view=guruTasks&task=view&cid='.$ordering_for_first_task_uncompleted.'&pid='.$id_for_first_day_uncompleted;	
		$text_for_resume = JText::_('GURU_DAYS_RESUME_BUTTON');	
	}	
if ($status=='2' && isset($task_id_to_get_started_array[0]) && $task_id_to_get_started_array[0]>0 && isset($day_id_to_get_started_array[0]) && $day_id_to_get_started_array[0]>0) 
	{
		$link_for_resume = 'index.php?option=com_guru&view=guruTasks&task=view&cid=1&pid='.$day_id_to_get_started_array[0].'&s=0';	
		$text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_STARTAGAIN');	
	}
if ($status=='-1') 
	{
		$the_day_id = $day_id_to_get_started_array[0];
		
		$db = JFactory::getDBO();
		$sql = "SELECT pid FROM #__guru_days 
				WHERE id = ".$the_day_id;
		$db->setQuery($sql);
		$result = $db->loadResult();		
		
		$link_for_resume = 'index.php?option=com_guru&view=guruProfile&task=buy&cid='.$result;	
		$text_for_resume = JText::_('GURU_MYPROGRAMS_ACTION_BUYAGAIN');	
	}	
	
	return $link_for_resume.'$$$$$'.$text_for_resume;
	
	}
	
	function parse_day_finnish_content($dayid) {
		$tasks_to_parse = '';
		$time_to_parse = 0;
		$points_to_parse = 0;
		$db =  JFactory::getDBO();
		
		$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id = ".$dayid;
		$db->setQuery($sql);
		if (!$db->execute()) {
			echo $db->stderr();
			return;
			}
		$tasksarray = $db->loadResultArray();
		
		foreach($tasksarray as $task)
		{
			$sql = "SELECT * FROM #__guru_task WHERE id = ".$task;
			$db->setQuery($sql);
			if (!$db->execute()) {
				echo $db->stderr();
				return;
			}
			$returned_task = $db->loadObject();
			$tasks_to_parse = $tasks_to_parse.$returned_task->name.', ';
			$time_to_parse = $time_to_parse + $returned_task->time;
			$points_to_parse = $points_to_parse + $returned_task->points;
		} // foreach end	
		
		$tasks_to_parse = substr($tasks_to_parse, 0, strlen($tasks_to_parse)-2);
		$to_return = $tasks_to_parse.'$$$$$'.$time_to_parse.'$$$$$'.$points_to_parse;
		return $to_return;
	}	
	
	function find_intro_media($dayid) {
		$database = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id in (SELECT media_id FROM #__guru_mediarel WHERE type = 'dmed' AND type_id = ".$dayid." )";
		$database->setQuery($sql);
		$result = $database->loadObjectList();
		return $result;			
	}	
	
	function find_tasks_for_aday($dayid){
		$db = JFactory::getDBO();
						
			$sqltasks = "SELECT media_id FROM #__guru_mediarel 
						WHERE type_id = ".$dayid." AND type = 'dtask' ";
			$db->setQuery($sqltasks);
			$result = $db->loadResultArray();

		return $result;			
	}	
	
	function getMedia($mediaid) {
		$database = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id = ".$mediaid;
		$database->setQuery($sql);
		$result = $database->loadObject();
		return $result;		
	}

};
?>