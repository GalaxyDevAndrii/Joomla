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

class TableguruSubremind extends JTable {
	var $id = null;
	var $name = null;
    var $term = null;
    var $recipient = null;
    var $custom_recipient = null;
    var $subject = null;
    var $body = null;
    var $published = null;
    var $ordering = null;
    
	function __construct(&$db) {
		parent::__construct('#__guru_subremind', 'id', $db);
	}
};

?>