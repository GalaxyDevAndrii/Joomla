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
defined ( '_JEXEC' ) or die ( 'Restricted access' );
if(!defined("DS")){
	define("DS", DIRECTORY_SEPARATOR);
}
define("_JAUPDATER_ROOT", realpath(dirname(__FILE__)));

// disable all notice and warning for product release
//error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);


// debug
//ini_set("xdebug.var_display_max_children", 500);
//ini_set("xdebug.var_display_max_depth", 100);
// -/-


require_once ("core/XmlParser.php");
require_once ("core/helper/VPackageHelper.php");
$VPackageHelper = new VPackageHelper();
$VPackageHelper->importRecursive(_JAUPDATER_ROOT."/../xlib");

$VPackageHelper->importAll(_JAUPDATER_ROOT."/core");
$VPackageHelper->importAll(_JAUPDATER_ROOT."/core/php5");
$VPackageHelper->import("core.bean.InfoObject");
$VPackageHelper->import("core.bean.Message");
$VPackageHelper->import("core.bean.Products");
$VPackageHelper->importAll(_JAUPDATER_ROOT."/core/checksum");
$VPackageHelper->importAll(_JAUPDATER_ROOT."/core/helper");
