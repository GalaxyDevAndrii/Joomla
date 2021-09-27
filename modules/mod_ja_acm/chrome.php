<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/*
 * Default Module Chrome that has semantic markup and has best SEO support
 */
function modChrome_ACMContainerItems($module, &$params, &$attribs)
{
	static $indexes = array();
	$position = $module->position;
	$class = isset($attribs['class']) ? $attribs['class'] : '';
	$active = isset($attribs['active']) ? (int) $attribs['active'] : -1;
	$tag = isset($attribs['tag']) ? $attribs['tag'] : 'div';
	$tag_attribs= isset($attribs['tag-attribs']) ? $attribs['tag-attribs'] : '';
	$id_prefix = isset($attribs['id-prefix']) ? $attribs['id-prefix'] : 'mod-';

	$indexes[$position] = isset($indexes[$position]) ? $indexes[$position] + 1 : 0;
	$class .= ($indexes[$position] == $active) ? ' in active' : '';
	$class .= ' mod-' . $module->id;

	$html = '<' . $tag . ' class="' . trim($class) . '" ' . $tag_attribs . 'id="mod-' . $module->id . '">';
	$html .= $module->content;
	$html .= '</' .$tag . '>';

	echo $html;
}
