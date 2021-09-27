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
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

$config = new JConfig();

$options = array ("host" => $config->host, "user" => $config->user, "password" => $config->password, "database" => $config->db,"prefix" => $config->dbprefix);

$database = JFactory::getDBO();
		
		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			echo $database->stderr();
			return;
		}
		$imagesin = $database->loadResult();			
			$data_get = JFactory::getApplication()->input->get->getArray();
			
			$group_id = $data_get['group'];
			$cid = $group_id;
			
			$sql = " SELECT locked, ordering FROM #__guru_days WHERE id = ".$cid;
			$database->setQuery($sql);
			if (!$database->execute()) {
				echo $database->stderr();
				return;
			}
			$locked_and_ordering = $database->loadObject();
			
			$locked = $locked_and_ordering->locked;
			$ordering = $locked_and_ordering->ordering;
			
			if ($locked==0) { // if locked=0
			// we delete also the DAY from program status - start
				$sql = " SELECT id, days, tasks FROM #__guru_programstatus WHERE pid = (SELECT pid FROM #__guru_days WHERE id = '".$cid."')";
				$database->setQuery($sql);
				if (!$database->execute()) {
					echo $database->stderr();
					return;
				}
				$ids = $database->loadObjectList();
				
				foreach($ids as $one_id){
					$day_array = explode(';', $one_id->days);
					$task_array = explode(';', $one_id->tasks);
					
					$the_key_to_be_removed=0;
					foreach ($day_array as $key=>$day_item)
						{
							$day_item_expld = explode(',',$day_item);
							if($day_item_expld[0]==$cid)
								{
									unset($day_array[$key]);
									$day_array = array_values($day_array);
									unset($task_array[$key]);
									$task_array = array_values($task_array);
								}
						}
				$new_day_array = implode(';', $day_array);
				//$task_array[$ordering-1] = '';
				$new_task_array = implode(';', $task_array);
				$sql = "update #__guru_programstatus set tasks='".$new_task_array."', days='".$new_day_array."' where id =".$one_id->id;
				$database->setQuery($sql);
				$database->execute();
				}
			// we delete also the DAY from program status - stop
			
			
			// we delete the relations with the media - start
			$sql = "delete from #__guru_mediarel where type='dmed' and type_id=".$cid;
			$database->setQuery($sql);
			if (!$database->execute() ){
				$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the media - stop
			
			// we delete the relations with the tasks - start
			$sql = "delete from #__guru_mediarel where type='dtask' and type_id=".$cid;
			$database->setQuery($sql);
			if (!$database->execute() ){
				$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the tasks - stop		
						
			
			$sql = "SELECT pid FROM #__guru_days WHERE id = ".$cid;
			$database->setQuery($sql);
			$database->execute();	
			$prog_id = $database->loadColumn();				
			
			$sql = "update #__guru_days set ordering=(ordering-1) where pid = '".$prog_id[0]."' AND ordering > ".$ordering;
			$database->setQuery($sql);
			$database->execute();
				
			$sql = "DELETE FROM #__guru_days WHERE id=".$cid;
			$database->setQuery($sql);	
			$database->execute();		
			
				
		
			} // end if locked=0
?>