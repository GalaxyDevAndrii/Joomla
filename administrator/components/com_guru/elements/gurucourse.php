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
class JFormFieldGurucourse extends JFormFieldList {
	function fetchElement($name, $value, &$node, $control_name) {
		$db =  JFactory::getDBO();
		$q = "SELECT CONCAT_WS(':', id, alias) as cid, name
			 FROM #__guru_program";
		$db->setQuery($q);
		$db->execute();
		$res = $db->loadObjectList();
		$result = JHTML::_( 'select.genericlist', $res, 'urlparams[cid]', 'class="inputbox" size="1"', "cid", "name", $value);
		return $result;
	}
}
?>
