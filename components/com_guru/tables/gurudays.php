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

class TableguruDays extends JTable {
	var $id = null;
	var $title = null;
	var $description = null;
	var $image = null;                       
	var $published = null;
	var $startpublish = null;
	var $endpublish = null;                       
	var $metatitle = null;
	var $metakwd = null;
	var $metadesc = null;
	var $afterfinish = null;
	var $url = null;
	var $pagetitle = null;
	var $pagecontent = null;
	var $ordering = null;
	
	function __construct (&$db) {
		parent::__construct('#__guru_days', 'id', $db);
		
	}

};


?>