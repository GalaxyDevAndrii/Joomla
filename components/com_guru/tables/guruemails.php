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

class TableguruEmails extends JTable {
	var $id = null;
	var $description = null;
	var $type = null;
	var $trigger = null;
	var $sendtime = null;
	var $sendday = null;
	var $reminder = null;
	var $published = null;
	var $subject = null;
	var $body = null;

	function __construct (&$db) {
		parent::__construct('#__guru_emails', 'id', $db);
	}

};


?>