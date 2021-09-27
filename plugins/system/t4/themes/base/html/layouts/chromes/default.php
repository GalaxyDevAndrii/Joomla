<?php

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

if ($module->content) {
	$html = "<{$moduleTag} class=\"t4-module module{$moduleClassSfx} {$moduleClass}\" id=\"Mod{$module->id}\">" .
				"<div class=\"module-inner\">" . $badge;

	if ($module->showtitle != 0) {
		$html .= "<{$headerTag} class=\"module-title {$headerClass}\"><span>{$module->title}</span></{$headerTag}>";
	}

	$html .= "<div class=\"module-ct\">{$module->content}</div></div></{$moduleTag}>";

	echo $html;
}