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

class TableguruPrograms extends JTable {
	var $id = null;
	var $catid = null;
	var $name = null;
	var $alias = null;
	var $description = null;
	var $introtext = null;
	var $image = null;
	var $emails = null;
	var $published = null;
	var $startpublish = null;
	var $endpublish = null;
	var $metatitle = null;
	var $metakwd = null;
	var $metadesc = null;
	var $ordering = null;
	var $pre_req = null;
	var $pre_req_books = null;
	var $reqmts = null;
	var $author = null;
	var $level = null;
    var $priceformat = null;
	var $skip_module = null;

	function __construct (&$db) {
		parent::__construct('#__guru_program', 'id', $db);
	}

};


?>