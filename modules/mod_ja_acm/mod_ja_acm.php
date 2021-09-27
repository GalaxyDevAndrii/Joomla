<?php
/**
 * ------------------------------------------------------------------------
 * JA ACM Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/chrome.php';

$helper = new ModJAACMHelper ($params);


$class_sfx	= htmlspecialchars($params->get('class_sfx'));

$helper->addAssets();

$layout_path = $helper->getLayout();
$buffer = '';
if ($layout_path)
{
	ob_start();
	include $layout_path;
	$buffer = ob_get_contents();
	ob_end_clean();
}
if ($params->get('parse-jdoc', 0)) {
	$buffer = $helper->renderJDoc($module->id, $buffer);
}

echo $buffer;