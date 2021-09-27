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
	define('JPATH_BASE', substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "/administra")) );
	include("../../../../../../configuration.php");
	
	$mosConfig_absolute_path =substr(JPATH_BASE, 0, strpos(JPATH_BASE, "/administra")); 
	$config = new JConfig();
	$dbhost = $config->host;
	$dbname = $config->db; 
	$dbuser = $config->user;
	$dbpass = $config->password;
	$dbprefix = $config->dbprefix;
	
	define( 'DS', DIRECTORY_SEPARATOR );
	
	require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
	require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
	$app = JFactory::getApplication('administrator');
	
	$app = JFactory::getApplication('site');
	
	$app->initialise();
	$db = JFactory::getDBO();
	$db_text=JFactory::getApplication()->input->get('dt', NULL);
	$db_med=JFactory::getApplication()->input->get('dm', NULL);
	$lay=JFactory::getApplication()->input->get('ldb',0);
	$scr=JFactory::getApplication()->input->get('scr',-1);
	
	$ip=ip2long($_SERVER['REMOTE_ADDR']);	
	$sql="SELECT ip FROM #__guru_media_templay WHERE ip='".$ip."'";
	$db->setQuery($sql);
	$exists=$db->loadResult();
	
	if($exists!=NULL) {
		$sql2="DELETE FROM #__guru_media_templay WHERE ip='".$ip."'";
		$db->setQuery($sql2);
		$db->execute($sql2);
	}
	$query="INSERT INTO #__guru_media_templay (ip , scr_id ,tmp_time ,db_lay ,db_med ,db_text)
			VALUES ('".$ip."', '".$scr."', NOW(), '".$lay."', '".$db_med."', '".$db_text."');";
	$db->setQuery($query);
	$db->execute();
?>