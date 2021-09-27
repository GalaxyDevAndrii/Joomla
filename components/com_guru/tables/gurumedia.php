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

class TableguruMedia extends JTable {
	var $id = null;
	var $name = null;
	var $instructions = null;
	var $type = null;
	var $source = null;
	var $code = null;
	var $url = null;
	var $local = null;
	var $width = null;
	var $height = null;
	var $published = null;

	function __construct (&$db) {
		parent::__construct('#__guru_media', 'id', $db);
	}

};


?>