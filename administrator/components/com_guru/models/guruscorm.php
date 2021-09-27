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


class guruAdminModelguruScorm extends JModelLegacy {
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_total = 0;
    var $_pagination = null;
	protected $_context = 'com_guru.guruScorm';
	
	function __construct () {
		parent::__construct();
		global $option;
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		
		if(is_array($cids) && isset($cids["0"])){
			$cids = $cids["0"];
		}
		
		$mainframe = JFactory::getApplication("admin");
		// Get the pagination request variables
		$app = JFactory::getApplication('administrator');
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		
		if(JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")){
			JFactory::getApplication()->input->set("limitstart", "0");		
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}
		
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function getPagination(){
			// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function getItems(){
		$config = new JConfig(); 
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		
		$sql = $this->getListQuery();
		$limitstart=$this->getState('limitstart');
		$limit=$this->getState('limit');
			
		if($limit!=0){
			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
		} else {
			$limit_cond = NULL;
		}
		
		$task = JFactory::getApplication()->input->get("task","");

		$query = $this->getListQuery();
		$result = $this->_getList($query.$limit_cond);
		$this->_total = $this->_getListCount($query);
		return $result;
	}

	protected function getListQuery(){
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$search = JFactory::getApplication()->input->get("search", "", "raw");
		$and = "";
		
		$db = JFactory::getDbo();	
			
		$sql = "select a.*, b.name as course_name from #__guru_scorm_items a INNER JOIN #__guru_program b on a.course_id = b.id where 1=1";

		if(trim($search) != ""){
			$sql .= " and a.title like '%".$db->escape(trim($search))."%'";
		}

		return $sql;
	}


	function scormCourse($scorm_id){
		$db = JFactory::getDbo();

		$sql = "select c.`name` from #__guru_program c, #__guru_scorm_items p where c.`id`=p.`course_id` and p.`id`=".intval($scorm_id);
		$db->setQuery($sql);
		$db->execute();
		$course_name = $db->loadColumn();
		$course_name = @$course_name["0"];

		return $course_name;
	}

	function getScorm($id){
		$db = JFactory::getDbo();
		$sql = "select * from #__guru_scorm_items where `id`=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$scorm = $db->loadAssocList();

		if(isset($scorm["0"])){
			return $scorm["0"];
		}
	}

	function getTeachers(){
		$db = JFactory::getDbo();
		$sql = "select u.`id`, u.`name` from #__users u, #__guru_authors a where u.`id`=a.`userid` and a.`enabled`='1'";
		$db->setQuery($sql);
		$db->execute();
		$authors = $db->loadAssocList();

		return $authors;
	}

	function getCourses($author_id){
		if(intval($author_id) > 0){
			$db = JFactory::getDbo();
			$sql = "select p.`id`, p.`name` from #__guru_program p where (p.`author` = '".intval($author_id)."' OR p.`author` like '%|".intval($author_id)."|%') and p.`published`='1'";
			$db->setQuery($sql);
			$db->execute();
			$courses = $db->loadAssocList();

			return $courses;
		}
		else{
			return array();
		}
	}

	function getTeacherCoursesSelect(){
		$teacher_id = JFactory::getApplication()->input->get("teacher_id", "0", "raw");

		if(intval($teacher_id) > 0){
			$courses = $this->getCourses($teacher_id);
			
			if(isset($courses) && count($courses) > 0){
				$return = '
					<select id="course-id" name="course_id">
						<option value="0"> '.JText::_("GURU_SELECT_COURSE").' </option>
				';

				foreach($courses as $key=>$value){
					$return .= '
						<option value="'.$value["id"].'"> '.$value["name"].' </option>
					';
				}

				$return .= '
					</select>
				';
			}
			else{
				$return = '
					<select id="course-id" name="course_id">
						<option value="0"> '.JText::_("GURU_SELECT_COURSE").' </option>
					</select>
				';
			}

			die($return);
		}
		else{
			$return = '
				<div class="alert alert-info">'.JText::_("GURU_SELECT_TEACHER").'</div>
			';
			die($return);
		}
	}

	function save(){
		$return = array("error"=>"false", "id"=>"0");
		$db = JFactory::getDbo();
		$item = $this->getTable('guruScorm');
		$data = JFactory::getApplication()->input->post->getArray(array(), null, "raw");

		if(trim($data["id"]) == ""){
			$data["id"] = 0;
		}

		if(trim($data["end"]) == ""){
			$data["end"] = '0000-00-00 00:00:00';
		}

		if(!isset($data["updated"])){
			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$data["updated"] = $jnow->toSQL(true);
		}

		if(!isset($data["layout"])){
			$data["layout"] = "";
		}

		if(!isset($data["published"])){
			$data["published"] = 0;
		}
		
		if (!$item->bind($data)){
			$return["error"] = true;
		}

		// Make sure the news record is valid
		if (!$item->check()){
			$return["error"] = true;
		}		
		
		// Store the web link table to the database
		if (!$item->store()){
			var_dump($item->getError());
			die();
			$return["error"] = true;
		}

		$return["id"] = $item->id;
		$return["title"] = $item->title;

		return $return;
	}

	function remove(){
		$db = JFactory::getDbo();
		$cids = JFactory::getApplication()->input->post->get("cid", array(), "raw");

		if(isset($cids) && is_array($cids) && count($cids) > 0){
			foreach ($cids as $key => $value) {
				$sql = "delete from #__guru_scorm_items where `id`=".intval($value);
				$db->setQuery($sql);
				if(!$db->execute()){
					return false;
				}
			}
		}

		return true;
	}
};
?>