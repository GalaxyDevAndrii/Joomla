<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

define('_JEXEC',1);
defined( '_JEXEC' ) or die( 'Restricted access' );
define('JPATH_BASE' , 1);
include_once("../../../../../../configuration.php");
include_once("../../../../../../libraries/joomla/base/object.php");
include_once("../../../../../../libraries/joomla/database/database.php");
include_once("../../../../../../libraries/joomla/database/database/mysql.php");

$config = new JConfig();
$options = array ("host" => $config->host,"user" => $config->user,"password" => $config->password,"database" => $config->db,"prefix" => $config->dbprefix);
$database = new JDatabaseMySQL($options);
?>