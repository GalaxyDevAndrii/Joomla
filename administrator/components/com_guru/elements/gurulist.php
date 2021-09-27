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
class JFormFieldGurulist extends JFormFieldList {
	function fetchElement($name, $value, &$node, $control_name){
		$res = array();
		$res[0] = new stdClass();
		$res[1] = new stdClass();
		$res[0]->display = "tree";
		$res[0]->name = "Tree";
		$res[1]->display = "listing";
		$res[1]->name = "Listing";
		$result = JHTML::_( 'select.genericlist', $res, 'params[display]', 'class="inputbox" size="1"', "display", "name", $value);
		return $result;
	}
}
?>
