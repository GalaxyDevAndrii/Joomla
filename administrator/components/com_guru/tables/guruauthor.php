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

class TableguruAuthor extends JTable{
	var $id = null;
	var $userid = null;
	var $gid = null;
	var $full_bio = null;
	var $emaillink = null;
	var $website = null;
	var $blog = null;
	var $facebook = null;
	var $twitter = null;
	var $show_email = null;
	var $show_website = null;
	var $show_blog = null;
	var $show_facebook = null;
	var $show_twitter = null;
	var $author_title = null;
	var $ordering = null;
	var $images = null;
	
	function __construct(&$db){
		parent::__construct('#__guru_authors', 'id', $db);
	}
};
?>