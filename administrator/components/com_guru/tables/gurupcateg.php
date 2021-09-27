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

class TableguruPcateg extends JTable {
	var $id = null;
	var $name = null;
	var $alias = null;
	var $published = null;
	var $description = null;
	var $image = null;
	var $ordering = null;
	
	function __construct (&$db) {
		parent::__construct('#__guru_category', 'id', $db);
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__guru_category set ordering=".intval($lft_array[$key])." where id=".intval($id);
				$db->setQuery($sql);
				$db->execute();
			}
		}
	}
};
?>
