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
defined( '_JEXEC' ) or die( 'Restricted access' );

$db = JFactory::getDBO();

if(isset($_REQUEST['id']) && $_REQUEST['id']>0){

	$sql4="UPDATE #__guru_task t SET forum_kunena_generatedt = 2 WHERE t.id=".intval($_REQUEST['id']);
	$db->setQuery($sql4);
	$db->execute($sql4);
	
	
	$sql1="select  catidkunena from #__guru_kunena_lessonslinkage where idlesson='".intval($_REQUEST['id'])."' order by id desc limit 0,1";
	$db->setQuery($sql1);
	$result = $db->loadResult();
	
	
	$sql2 = "delete from #__kunena_categories where id=".intval($result);
	$db->setQuery($sql2);
	$db->execute($sql2);
	
	$sql3 = "delete from #__kunena_aliases where item=".intval($result);
	$db->setQuery($sql3);
	$db->execute($sql3);
	
	$session = JFactory::getSession();
	$registry = $session->get('registry');
	$registry->set('lesson_removed', "yes");	
}