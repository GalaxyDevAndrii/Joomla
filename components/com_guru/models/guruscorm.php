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

class guruModelguruScorm extends JModelList {
	protected $_context = 'com_guru.guruscorm';
	var $_total;
	var $_pagination;
	

	function __construct () {
		parent::__construct();
	}

	protected function populateState($ordering = null, $direction = null){
		parent::populateState();
		// Add archive properties
		$app = JFactory::getApplication("site");
		$config = JFactory::getConfig();
		$itemid = JFactory::getApplication()->input->get('Itemid', 0, "raw");
		$limit = $app->getUserStateFromRequest('com_guru.guruscorm' . $itemid . '.limit', 'limit', $config->get('list_limit', 20));
		$this->setState('list.limit', $limit);
	}

	/**
	* get list scorm
	* @param $filter array
	*/
	function getListScorm($filter){
		$db = JFactory::getDbo();
		$app = JFactory::getApplication("site");

		$query = $db->getQuery(true);

		$query->select('a.*,b.name as course_name');
		$query->from($db->quoteName('#__guru_scorm_items','a'));
		$query->join('INNER', $db->quoteName('#__guru_program', 'b') . ' ON (' . $db->quoteName('a.course_id') . ' = ' . $db->quoteName('b.id') . ')');

		$limit		= $app->getUserStateFromRequest( 'limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int' );
		
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
		$db->setQuery($query, $limitstart, $limit);

		$results = $db->loadObjectList();

		// get total
		$this->_total = $this->_getListCount($query);

		$pagination = $this->getPagination();
		$pagination->limitstart = $limitstart;

		if(intval($limit) > 0){
			$pagination->limit = $limit;
		}

		$pagination->total = $this->_total;
		@$pagination->pagesTotal = ceil($this->_total / $pagination->limit);
		@$pagination->pagesStop = ceil($this->_total / $pagination->limit);
		@$pagination->pagesCurrent = ($limitstart / $limit + 1);
		$this->set("Pagination", $pagination);

		return $results;
	}

	function getMyScorm($courses, $filter=''){
		if(empty($courses))
			return false;

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('a.*,b.name as course_name');
		$query->from($db->quoteName('#__guru_scorm_items','a'));
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

		// show scorm active only
		$date = JFactory::getDate();
		$query->where( $db->quoteName('start') . ' <= ' . $db->Quote($date));
		$query->where( $db->quoteName('end') . ' >= ' . $db->Quote($date));

		$query->order('end ASC');
		
		$db->setQuery($query);
		$results = $db->loadObjectList();

		$this->_total = $filter['limit'];//$db->loadResult();

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