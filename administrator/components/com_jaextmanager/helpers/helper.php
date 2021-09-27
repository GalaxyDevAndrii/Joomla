<?php
/**
 * ------------------------------------------------------------------------
 * JA Extension Manager Component for J3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

function isJAProduct($extname)
{	
	//Eg: jat3
	if (preg_match("/^ja/i", $extname)) {
		return true;
	}
	
	//Eg: com_jaextmanager
	if (preg_match("/[_\-\s]ja/i", $extname)) {
		return true;
	}
	
	return false;
}


function jaGetCoreVersion($jVersion, $extname = '')
{
	$isJAProduct = isJAProduct($extname);
	
	if($isJAProduct) {
		$coreVersion = (preg_match("/^1\.5/", $jVersion)) ? 'j15' : 'j16';
	} else {
		$versions = explode('.', $jVersion);
		array_splice($versions, 2);
		$coreVersion = 'j'.implode('', $versions);
	}
	
	return $coreVersion;
}


function jaGetListServices()
{
	$db = JFactory::getDbo();
	
	$sql = "SELECT * FROM #__jaem_services AS t WHERE 1 ORDER BY t.ws_name";
	$db->setQuery($sql);
	return $db->loadObjectList();
}


function jaGetDefaultService()
{
	$services = jaGetListServices();
	$default = new stdClass();
	foreach ($services as $id => $sv) {
		if ($id == 0 || $sv->ws_default) {
			$default = $sv;
		}
	}
	//set default values
	if (!isset($default->ws_mode)) {
		$default->ws_mode = 'local';
	}
	if (!isset($default->ws_uri)) {
		$default->ws_uri = 'http://update.joomlart.com/service/';
	}
	if (!isset($default->ws_user)) {
		$default->ws_user = 'joomlart';
	}
	if (!isset($default->ws_pass)) {
		$default->ws_pass = '';
	}
	
	return $default;
}


function jaEMTooltips($tipid, $title)
{
	return '';
}


/**
 * Create file with unique file name
 *
 */
function jaTempnam($dir, $prefix)
{
	$dir = JPath::clean($dir . '/');
	if (!JFolder::exists($dir)) {
		$dir = JPath::clean(ja_sys_get_temp_dir() . '/');
	}
	
	$sand = md5(microtime());
	$fileName = $prefix . date("YmdHis") . $sand;
	$i = 0;
	$fileNameTest = $fileName . ".tmp";
	while (JFile::exists($dir . $fileNameTest)) {
		$i++;
		$fileNameTest = $fileName . "_{$i}.tmp";
	}
	$file = $dir . $fileNameTest;
	//$content = '';
	//JFile::write($file, $content);
	//chmod
	//@chmod($file, '0755');
	return $file;
}

function jaIsJoomla3x() {
	return version_compare(JVERSION, '3.0', 'ge');
}

function jaIsJoomla4x() {
	return version_compare(JVERSION, '4', 'ge');
}