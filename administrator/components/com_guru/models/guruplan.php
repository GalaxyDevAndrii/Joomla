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


class guruAdminModelguruSubplan extends JModelLegacy {
	var $_modules;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;


	function __construct () {
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");

		$this->setId((int)$cids[0]);
		$app = JFactory::getApplication('administrator');

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
		$pid=JFactory::getApplication()->input->get("pid","0");
		if ($pid==0) {
			$sql = "SELECT * FROM #__guru_days WHERE 
					pid = ( SELECT id FROM #__guru_program WHERE id in 
					( SELECT pid FROM #__guru_days GROUP BY pid ) 
					ORDER BY name ASC LIMIT 1 ) ORDER BY ordering ASC";			
			$this->_total = $this->_getListCount($sql);
			$this->_modules = $this->_getList($sql);
		}
		else {				
			$sql = "SELECT * FROM #__guru_days WHERE pid =".$pid." ORDER BY ordering ASC ";
			
			$this->_modules = $this->_getList($sql);
			$this->_total = $this->_getListCount($sql);
		}		
		return $this->_modules;

	}	

	function getFilters(){
		$app = JFactory::getApplication('administrator');
		$db =JFactory::getDBO();
		$pid=$app->getUserStateFromRequest("pid","pid","0");
		
		$sql = "SELECT lp.id as pid,name as name 
				FROM #__guru_program lp
				LEFT JOIN #__guru_days ld
				on lp.id=ld.pid
				GROUP BY lp.id ORDER BY name ASC";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$allprg = $db->loadObjectList();
		
		$filters->pid  =  JHTML::_( 'select.genericlist', $allprg, 'pid', 'class="inputbox" size="1" onChange="document.adminForm.submit();"',"pid", "name", $pid);
		return $filters;
	}

	function getday() {
		$db =JFactory::getDBO();
		$data = JFactory::getApplication()->input->post->getArray();
		if (empty ($this->_attribute)) { 
			$this->_attribute =$this->getTable("guruDays");
			$this->_attribute->load($this->_id);
		}
			
		if (!$this->_attribute->bind($data)){
			$this->setError($item->getError());
			return false;
		}
	
		if (!$this->_attribute->check()) {
			$this->setError($item->getError());
			return false;
		}
		
		
		if ($this->_attribute->id>0) { 
			$this->_attribute->text=JText::_('New');
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_task as b WHERE a.type='dtask' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$this->_attribute->id);
			$this->_attribute->alltasks = $db->loadObjectList();
			
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.type='dmed' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=".$this->_attribute->id);
			$this->_attribute->daymmedia = $db->loadObjectList();
			$this->_attribute->nodegroup=$data['node'];
		} else {
			$this->_attribute->text=JText::_('Edit');
			$this->_attribute->alltasks = new stdClass();
			$this->_attribute->daymmedia = new stdClass();
			if(intval($data['pid'])>0){
				$this->_attribute->ordering=$this->order_for_last_day($data['pid'])+1;
				$this->_attribute->nodegroup=$this->id_for_last_day($data['pid'])+1;
			}
			else{
				$this->_attribute->ordering=1;
				$this->_attribute->nodegroup=1;
			}
			$jnow = new JDate('now');
			$this->_attribute->startpublish =  $jnow->toMySQL();
		}
		
		
		if(substr($this->_attribute->endpublish,0,4) =='0000' || $this->_attribute->id<1) 
			$this->_attribute->endpublish = JText::_('GURU_NEVER');  
		
		$javascript = 'onchange="document.adminForm.submit();"';
		$sql = "SELECT id as pid,name as name FROM #__guru_program WHERE 1=1 ORDER BY name ASC";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$allprg = $db->loadObjectList(); 
	    $this->_attribute->lists['pid']  =  JHTML::_( 'select.genericlist', $allprg, 'pid', 'class="inputbox" size="1"'.$javascript, "pid", "name", $this->_attribute->pid);
		
		if(!isset($this->_attribute->published))
			$this->_attribute->published=1;
		$this->_attribute->lists['published'] = JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', $this->_attribute->published );
		
		
		if(!isset($this->_attribute->locked))
			 $this->_attribute->locked=0;
		$locked =array();
		$locked[] = JHTML::_('select.option',"0",JText::_('GURU_DAY_LOCKED_N'));
		$locked[] = JHTML::_('select.option',"1",JText::_('GURU_DAY_LOCKED_Y'));
		$this->_attribute->lists['locked']  =  JHTML::_( 'select.genericlist', $locked, 'locked', 'class="inputbox" size="1"', "value", "text", $this->_attribute->locked);
		
		
		if(!isset($this->_attribute->afterfinish))
			 $this->_attribute->afterfinish=0;
		$afterfinish=array();
		$javascript='onChange="javascript: displayblock(this.value);"';
		$afterfinish[] = JHTML::_('select.option','0',JText::_('GURU_DAY_AFTER_FINNISH1'));
		$afterfinish[] = JHTML::_('select.option','1',JText::_('GURU_DAY_AFTER_FINNISH2'));
		$afterfinish[] = JHTML::_('select.option','2',JText::_('GURU_DAY_AFTER_FINNISH3'));
		$this->_attribute->lists['afterfinish']  =  JHTML::_( 'select.genericlist', $afterfinish, 'afterfinish', 'class="inputbox" size="1" '.$javascript, "value", "text", $this->_attribute->afterfinish);
		
		return $this->_attribute;
	}

	function getHowManyTasksForDay ($dayid) {	
		
			$database = JFactory::getDBO();
			$sql = "SELECT count(media_id) FROM #__guru_mediarel WHERE type='dtask' AND type_id = ".$dayid." "; 
			$database->setQuery($sql);
			$result = $database->loadResult();
			return $result;
	}
	
	function getIDTasksForDay ($dayid){			
			$database = JFactory::getDBO();
			$sql = "SELECT media_id 
					FROM #__guru_mediarel lm
					LEFT JOIN #__guru_task lt
					ON lm.media_id=lt.id
					WHERE type='dtask' AND type_id = ".$dayid." 
					ORDER BY ordering ASC "; 
			$database->setQuery($sql);
			$result = $database->loadColumn();
			return $result;	
	}
	
	function getTask ($taskid){
			$database = JFactory::getDBO();
			$sql = " SELECT * FROM #__guru_task WHERE id = ".$taskid; 
			$database->setQuery($sql);
			$result = $database->loadObject();
			return $result;	
	}	
	
	function select_layout ($pid){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$pid." AND type='scr_l' ");
		$db->execute();
		$layout_id = $db->loadResult();
		return $layout_id;
	}	
	
	function getProgram($id){
			$database = JFactory::getDBO();
			$sql = " SELECT * FROM #__guru_program WHERE id = ".$id." "; 
			$database->setQuery($sql);
			$result = $database->loadAssocList();
			return $result[0];	
	}
	
	function getProgramName ($dayid){
			$database = JFactory::getDBO();

			$sql = " SELECT name FROM #__guru_program WHERE id = (SELECT pid FROM #__guru_days WHERE id = ".$dayid." )"; 
			$database->setQuery($sql);
			$result = $database->loadResult();
			return $result;	
	}	
	
	function getTaskType ($taskid) {
			$database = JFactory::getDBO();
			$sql = " SELECT type FROM #__guru_media WHERE id = (SELECT media_id FROM #__guru_mediarel WHERE type='task' AND type_id = ".$taskid." )"; 
			$database->setQuery($sql);
			$result = $database->loadResult();
			return $result;		
	}	
	
	function getCampaignCount($banner_id) {
		$database = JFactory::getDBO();
		$sql	= "SELECT count(c.id) FROM #__ad_agency_campaign c INNER JOIN #__ad_agency_campaign_banner cb on c.id = cb.campaign_id WHERE banner_id = {$banner_id}";
		$database->setQuery($sql);
		$result = $database->loadResult();
		return $result;
	}
	
	function more_media_files_t ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_task WHERE id in (".$ids.") GROUP BY media_id");
		$db->execute();
		$more_task_files = $db->loadObjectList();
		$this->more_task_files = $more_task_files;
		return true;
	}
	
	function existing_ids_t ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$ids." AND type='dtask' ");
		$db->execute();
		$existing_ids = $db->loadObjectList();
		
		return $existing_ids;
	}		
	
	function more_media_files_m ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_media WHERE id in (".$ids.") GROUP BY media_id");
		$db->execute();
		$more_media_files = $db->loadObjectList();
		$this->more_media_files = $more_media_files;
		return true;
	}
	
	function existing_ids_m ($ids){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = ".$ids." AND type='dmed' ");
		$db->execute();
		$existing_ids = $db->loadObjectList();
		
		return $existing_ids;
	}	
	
	function id_for_last_day($ids){
		$db = JFactory::getDBO();
		$sql = "SELECT max(id) FROM #__guru_days WHERE pid = ".$ids;
			$db->setQuery($sql);
			if (!$db->execute()) {
				return;
			}
			$id = $db->loadResult();
		return $id;	
	}		
	
	function order_for_last_day($ids){
		$db = JFactory::getDBO();
		$sql = "SELECT max(ordering) FROM #__guru_days WHERE pid = ".$ids;
			$db->setQuery($sql);
			if (!$db->execute()) {
				return;
			}
			$id = $db->loadResult();
		return $id;	
	}
	
	function find_max_dayid(){
		$db = JFactory::getDBO();
		$x = new JConfig();
		$tablename         = $x->dbprefix.'guru_days';
		$next_increment     = 0;
		$sql         = "SHOW TABLE STATUS LIKE '".$tablename."'";

		$db->setQuery($sql);
		$id = $db->loadObject();

		return $id->Auto_increment;
	}			


	function store () {
		$db = JFactory::getDBO();
		$item = $this->getTable('guruDays');
		$data = JFactory::getApplication()->input->post->getArray();

		$data['pagecontent'] = JFactory::getApplication()->input->get('pagecontent','','raw');
		$data['description'] = JFactory::getApplication()->input->get('description','','raw');
		
		$res = true;
		if (!$item->bind($data)){
			$res = false;
		}
		if (!$item->check()) {
			$res = false;
		}
		if (!$item->store()) {
			$res = false;
		}
		
		$data['id'] = JFactory::getApplication()->input->get('newdayid',"0");
		
		$data['mediafilesday']=substr($data['mediafilesday'],1,-1);
		$mediafilesday=explode(",,",$data['mediafilesday']);
		for($i=0;$i<count($mediafilesday);$i++)
			$this->addtask ($mediafilesday[$i], $data['id'], "0");
		
		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
			}
		$influence = $db->loadResult(); // we have selected the INFLUENCE 
		
		$sql = "SELECT locked FROM #__guru_days WHERE id = ".$data['id'];
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
		}
		$locked = $db->loadResult(); // we have selected the LOCKED property for a day 		

		if($influence==1 && $locked==0){			
			
			$sql = "SELECT lp.id,days,tasks FROM #__guru_programstatus  lp
					LEFT JOIN #__guru_days ld
					ON lp.pid=ld.id
					WHERE lp.id = ".$data['id']."
					GROUP BY lp.id";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return;
			}
			$days_array = $db->loadObjectList();				
		
			foreach($days_array as $one_day_array){
				$one_day_array_days = $one_day_array->days;
				$one_day_array_id = $one_day_array->id;
				$one_day_array_tasks = $one_day_array->tasks;
				
				$pos = strpos($one_day_array_days, $data['id'].',');
				if($pos === false)
					{ // if the day is not in the programstatus_table (NEW DAY) we add it at "the end" - begin
						$new_day_array = $one_day_array_days.$data['id'].'-0;';

						$task_status = $one_day_array_tasks.';';	
						
						$sql = "UPDATE #__guru_programstatus SET 
								days='".$new_day_array."', tasks = '".$task_status.
								"'	where id = '".$one_day_array_id."' ";
						$db->setQuery($sql);
						$db->execute();
					}	
			}//endforeach
		}
		return true;
	}//endfunction	
	
	
	function delete () {
		$database = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		
		$item = $this->getTable('guruDays');
		
		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			return;
		}
		$imagesin = $database->loadResult();			
		
		foreach ($cids as $cid) {
			
			$sql = " SELECT locked, ordering FROM #__guru_days WHERE id = ".$cid;
			$database->setQuery($sql);
			if (!$database->execute()) {
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
				//$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the media - stop
			
			// we delete the relations with the tasks - start
			$sql = "delete from #__guru_mediarel where type='dtask' and type_id=".$cid;
			$database->setQuery($sql);
			if (!$database->execute() ){
				//$this->setError($database->getErrorMsg());
				return false;
			}
			// we delete the relations with the tasks - stop		
			
			// we delete the image - begin
			$sql = "SELECT image FROM #__guru_days WHERE id =".$cid;
			$database->setQuery($sql);
			if (!$database->execute()) {
				return;
			}
			$image = $database->loadResult();	
			$targetPath = JPATH_SITE.'/'.$imagesin.'/';		
			unlink($targetPath.$image);
			// we delete the image - end
			
			$sql = "update #__guru_days set ordering=(ordering-1) where pid = (SELECT pid FROM #__guru_days WHERE id = '".$cid."') AND ordering > ".$ordering;
			$database->setQuery($sql);
			$database->execute();
				
			if (!$item->delete($cid)) {
				$this->setError($item->getError());
				return false;
			}			
			
			} // end if locked=0
			
		} // end foreach

		return true;
	}

	function publish () {

		$db = JFactory::getDBO();		
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		if ($task == 'publish'){
			$sql = "update #__guru_days set published='1' where id in ('".implode("','", $cids)."')";
			$ret = 1;
		} else {
			$ret = -1;
			$sql = "update #__guru_days set published='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
	
		return $ret;
	}

	function addtask ($toinsert, $taskid, $mainmedia) {
		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__guru_mediarel ( id , type , type_id , media_id , mainmedia ) VALUES ('', 'dtask', '".$taskid."' , '".$toinsert."', '".$mainmedia."');";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
	return true;
	}
	
	function addmainmedia ($toinsert, $taskid, $mainmedia) {
		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__guru_mediarel ( id , type , type_id , media_id , mainmedia ) VALUES ('', 'dmed', '".$taskid."' , '".$toinsert."', '".$mainmedia."');";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
	return true;
	}	

	function getMediaList(){
		$app = JFactory::getApplication('administrator');
		$db 		 = JFactory::getDBO();
		$search_text = $app->getUserStateFromRequest('search_text','search_text','');
		$media_select= $app->getUserStateFromRequest('media_select','media_select','all');
		
		$sql = "SELECT *,
				CASE type
					WHEN 'audio' THEN '".JText::_('GURU_AUDIO')."'
					WHEN 'video' THEN '".JText::_('GURU_VIDEO')."'
					WHEN 'docs' THEN '".JText::_('GURU_DOCS')."'
					WHEN 'url' THEN '".JText::_('GURU_URL')."'
				END as type
				FROM #__guru_media WHERE 1=1 ";
				
		if($search_text!="")
			$sql = $sql." AND name LIKE '%".$search_text."%' " ;
		if($media_select!='all')
			$sql = $sql." AND type='".$media_select."' ";			
		$db->setQuery($sql);
		$medias = $db->loadObjectList();
		return $medias;
	}
	
	function getMediaFilters(){
		$app = JFactory::getApplication('administrator');
		$db =JFactory::getDBO();
		$search_text = $app->getUserStateFromRequest("search_text","search_text","");
		$media_select= $app->getUserStateFromRequest("media_select","media_select","");
		
		$media_type = array();
		$media_type[] = JHTML::_('select.option',"all",JText::_('GURU_ALL'));
		$media_type[] = JHTML::_('select.option',"audio",JText::_('GURU_AUDIO'));
		$media_type[] = JHTML::_('select.option',"video",JText::_('GURU_VIDEO'));
		$media_type[] = JHTML::_('select.option',"docs",JText::_('GURU_DOCS'));
		$media_type[] = JHTML::_('select.option',"url",JText::_('GURU_URL'));
		
		$filters->media_select  =  JHTML::_( 'select.genericlist', $media_type, 'media_select', 'class="inputbox" size="1" onChange="document.form1.submit();"',"value", "text", $media_select);
		
		$filters->search_text=$search_text;
		return $filters;
	}

	function deltask($tid,$cid) {
		// $tid = DAY ID
		// $cid = TASK ID
		$db = JFactory::getDBO();

		$sql = "delete from #__guru_mediarel where type='dtask' and type_id=".$tid." and media_id = ".$cid;
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	function delmedia($tid,$cid) {
		$db = JFactory::getDBO();

		$sql = "delete from #__guru_mediarel where type='dmed' and type_id=".$tid." and media_id=".$cid;
		
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}	
	
	function getConfigs() {
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$result = $db->loadObject();	
		return $result;
	}	
	
	function checkbox_construct( $rowNum, $recId, $name='cid' )
	{
		$db = JFactory::getDBO();
		
		$sql = " SELECT programid FROM #__guru_order GROUP BY programid";
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$sold_programs = $db->loadColumn();	
		
		$sql = " SELECT pid FROM #__guru_days WHERE id = ".$recId;
		$db->setQuery($sql);
		if (!$db->execute() ){
			//$this->setError($db->getErrorMsg());
			return false;
		}	
		$program_of_this_day = $db->loadResult();		
		
		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return;
			}
		$influence = $db->loadResult(); // we have selected the INFLUENCE 
		
		if($influence==0 && in_array($program_of_this_day, $sold_programs))
			{
				$not = 'not';
				$disabled = 'disabled="disabled"';	
			}	
		else 
			{
				$disabled = '';
				$not = '';
			}	
		
		return '<input type="checkbox" id="'.$not.'cb'.$rowNum.'" '.$disabled.'" name="'.$name.'[]" value="'.$recId.'" onclick="isChecked(this.checked);add_day_to_duplicate(this.value, this.checked)" />';
	}		
	
	function checkbox_construct_add_task( $rowNum, $recId )
	{
		return '<input type="checkbox" id="cb'.$rowNum.'" name="cb'.$rowNum.'" value="'.$recId.'" onclick="isChecked(this.value, this.checked);" />';
	}		

};
?>