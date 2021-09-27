<?php
if(JFile::exists(JPATH_COMPONENT."/installer/update/2.5.3/jaextmanager.xml") && JFile::exists(JPATH_COMPONENT."/com_jaextmanager.xml")){ 
	$oldxmlfile = JPath::clean(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'com_jaextmanager.xml');
	$newxmlfile =  JPATH_COMPONENT.DIRECTORY_SEPARATOR.'installer'.DIRECTORY_SEPARATOR.'update'.DIRECTORY_SEPARATOR.'2.5.3'.DIRECTORY_SEPARATOR.'jaextmanager.xml';
	
	$newxmlfilecontent = JFile::read($newxmlfile);
	JFile::write($oldxmlfile, $newxmlfilecontent);
	rename($oldxmlfile, str_replace('com_jaextmanager.xml', 'jaextmanager.xml', $oldxmlfile));
	JFile::delete(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'/installer/update/update.php');
}
?>