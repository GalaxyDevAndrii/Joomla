<?php
/*------------------------------------------------------------------------
# com_publisher
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modGuruMenusHelper {

	function getGuruCategories($params){
		$db = JFactory::getDbo();
		$sql = "select c.*, A.total from #__guru_category c left outer join (select count(*) as total, catid from #__guru_program where published='1' and startpublish <= now() and (endpublish >= now() OR endpublish = '0000-00-00 00:00:00') group by catid) as A on c.id=A.catid where c.published='1' order by c.ordering asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		return $result;
	}
}

?>