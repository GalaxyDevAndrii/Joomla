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

class guruModelguruProjects extends JModelLegacy {
	protected $_context = 'com_guru.guruproject';
	var $_total;
	var $_pagination;
	

	function __construct () {
		parent::__construct();
	}

	/**
	* get list projects
	* @param $filter array
	*/
	function getListProjects($filter){
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('a.*,b.name as course_name');
		$query->from($db->quoteName('#__guru_projects','a'));
		$query->join('INNER', $db->quoteName('#__guru_program', 'b') . ' ON (' . $db->quoteName('a.course_id') . ' = ' . $db->quoteName('b.id') . ')');
		
		// filter by author
		if(!empty($filter['author_id'])){
			$query->where($db->quoteName('a.author_id')." = ".$db->quote($filter['author_id']));
		}
		// filter bu course
		if(!empty($filter['course_id'])){
			$query->where($db->quoteName('a.course_id')." = ".$db->quote($filter['course_id']));
		}
		// filter by keyword
		if($filter['keyword']){
			$query->where($db->quoteName('a.title')." LIKE ".$db->quote('%'.$filter['keyword'].'%'));
		}

		
		$query->order('id ASC');

		$db->setQuery($query,$filter['limitstart'],$filter['limit']);

		$results = $db->loadObjectList();

		// get total
		$this->_total = $filter['limit'];//$db->loadResult();
		return $results;
	}

	/**
	* get list projects
	* @param $filter array
	*/
	function getListProjectResults($filter){
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('a.*,b.name as student_name');
		$query->from($db->quoteName('#__guru_project_results','a'));
		$query->join('INNER', $db->quoteName('#__users', 'b') . ' ON (' . $db->quoteName('a.student_id') . ' = ' . $db->quoteName('b.id') . ')');
		
		if(!empty($filter['id'])){
			$query->where($db->quoteName('a.project_id')." = ".$db->quote($filter['id']));
		}
		
		$query->order('id DESC');

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	function getMyProjects($courses, $filter=''){
		if(empty($courses))
			return false;

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('a.*,b.name as course_name');
		$query->from($db->quoteName('#__guru_projects','a'));
		$query->join('INNER', $db->quoteName('#__guru_program', 'b') . ' ON (' . $db->quoteName('a.course_id') . ' = ' . $db->quoteName('b.id') . ')');
		$query->where($db->quoteName('a.course_id')." IN (".$courses.")");

		// filter bu course
		if(!empty($filter['course_id'])){
			$query->where($db->quoteName('a.course_id')." = ".$db->quote($filter['course_id']));
		}
		// filter by keyword
		if(!empty($filter['keyword'])){
			$query->where($db->quoteName('a.title')." LIKE ".$db->quote('%'.$filter['keyword'].'%'));
		}

		// show project active only
		$date = JFactory::getDate();
		$query->where( $db->quoteName('start') . ' <= ' . $db->Quote($date));
		$query->where( $db->quoteName('end') . ' >= ' . $db->Quote($date));

		$query->order('end ASC');
		
		$db->setQuery($query);
		$results = $db->loadObjectList();

		$this->_total = $filter['limit'];//$db->loadResult();

		return $results;

	}

	function getStudentProjects($courses, $filter=''){
		$db = JFactory::getDbo();

		$filter_course_id = JFactory::getApplication()->input->get("filter_course_id", "0", "raw");
		$filter_keyword = JFactory::getApplication()->input->get("filter_keyword", "", "raw");
		$user = JFactory::getUser();

		$sql = "select p.*, c.`name` as course_name, c.`alias` as course_alias, pr.`title` as project_name, t.`name` as lesson_name from #__guru_project_results p, #__guru_program c, #__guru_projects pr, #__guru_task t where p.`project_id`=pr.`id` and p.`course_id`=c.`id` and p.`lesson_id`=t.`id` and p.`student_id`=".intval($user->id);

		if(intval($filter_course_id) != 0){
			$sql .= " and c.`id`=".intval($filter_course_id);
		}

		if(trim($filter_keyword) != ""){
			$sql .= " and (p.`file` like '%".$db->escape(trim($filter_keyword))."%' OR pr.`title` like '%".$db->escape(trim($filter_keyword))."%' OR c.`name` like '%".$db->escape(trim($filter_keyword))."%' OR t.`name` like '%".$db->escape(trim($filter_keyword))."%')";
		}

		$db->setQuery($sql);
		$db->execute();
		$results = $db->loadAssocList();

		return $results;

	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}



};
?>