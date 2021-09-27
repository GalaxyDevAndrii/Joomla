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

class guruModelguruEditplans extends JModelLegacy {
	
	function __construct () {
		parent::__construct();
	}
	
	function getListPlans(){
		$course_id = JFactory::getApplication()->input->get("course_id", "raw");
		$db = JFactory::getDbo();
		$sql = "select pp.price, pp.default, s.name from #__guru_program_plans pp, #__guru_subplan s where s.id = pp.plan_id and pp.product_id=".intval($course_id)." order by s.ordering asc";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config where id=1";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadAssocList();
		return $result;
	}
};

?>