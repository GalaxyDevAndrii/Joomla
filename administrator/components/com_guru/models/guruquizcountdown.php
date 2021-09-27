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


class guruAdminModelguruQuizCountdown extends JModelLegacy {
	var $_packages;
	var $_package;
	var $_tid = null;
	var $_total = 0;
	var $_pagination = null;

	
	
	function store () {
		$post_value = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$sql = "UPDATE #__guru_config set qct_alignment='".$post_value["timer_alignement"]."', qct_border_color= '".$post_value["st_donecolor"]."',	qct_minsec ='". $post_value["st_notdonecolor"]."', qct_title_color='".$post_value["st_txtcolor"]."',	qct_bg_color='".$post_value["st_xdonecolor"]."', qct_font='".$post_value["font"]."', qct_width='".$post_value["st_width"]."', qct_height='".$post_value["st_height"]."', qct_font_nb='".$post_value["fontnb"]."', qct_font_words='".$post_value["fontwords"]."' ";
		$db->setQuery($sql);
		$db->execute();
		return true;
	}	



};
?>