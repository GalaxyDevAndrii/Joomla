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

class CHTMLInput
{
	public static function checkbox($name, $class, $attribs = array(), $selected = null, $id = false)
	{
		$selectedHtml = '';
		$html = '';

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		if($selected) {
			$selectedHtml .= " checked=\"checked\"";
		}

		$html .= "\n<input type='hidden' value='0' name=\"$name\">"; // Self destruct
		$html .= "\n<input type=\"checkbox\" name=\"$name\" class=\"$class\" value=\"1\" $attribs $selectedHtml />";
		$html .= "\n<span class=\"lbl\"></span>";
		
		return $html;
	}
}