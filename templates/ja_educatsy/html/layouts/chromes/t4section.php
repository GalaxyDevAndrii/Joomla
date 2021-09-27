<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * html5 (chosen html5 tag and font header tags)
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs  = $displayData['attribs'];


$badge          = preg_match ('/badge/', $params->get('moduleclass_sfx'))? '<span class="badge">&nbsp;</span>' : '';
$moduleTag      = htmlspecialchars($params->get('module_tag', 'div'));
$headerTag      = htmlspecialchars($params->get('header_tag', 'h4'));
$headerClass    = $params->get('header_class');
$bootstrapSize  = $params->get('bootstrap_size');
$moduleClass    = !empty($bootstrapSize) ? ' span' . (int) $bootstrapSize . '' : '';
$moduleClassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

// Sub Align
$moduleStyle = $params->get('title-style','1');

// Sub Align
$moduleAli = $params->get('sub-align','left');

// Sub Color
$subColor = $params->get('sub-color', 'normal');

// Title Space
$titleSpace = $params->get('title-space', 'large');

// Sub Heading
$moduleSub = '';

if($params->get('sub-heading')) {
	$moduleSub = '<span class="sub-heading mt-0 text-'.$subColor.' h2">'.$params->get('sub-heading').'</span>';
}

// Mod Title
$modTitle = '';

if($module->showtitle != 0) {
	$modTitle = '<'.$headerTag.' class="section-title h5 style-'.$moduleStyle.' '.$headerClass.'"><span>'.$module->title.'</span></'.$headerTag.'>';
}


if (!empty ($module->content)) {
	$html = "<{$moduleTag} class=\"t4-section-inner section{$moduleClassSfx} {$moduleClass}\" id=\"Mod{$module->id}\">" .
				"<div class=\"section-inner\">" . $badge;

	if ($module->showtitle != 0 || $params->get('sub-heading')) {
		$html .= "<div class=\"container\"><div class=\"section-title-wrap text-{$moduleAli} space-{$titleSpace}\">{$modTitle}{$moduleSub}</div></div>";
	}

	$html .= "<div class=\"section-ct\">{$module->content}</div></div></{$moduleTag}>";

	echo $html;
}