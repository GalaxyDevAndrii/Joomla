<?php
/**
T4 Overide
 */


defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * three arguments.
 */

// looking for all chrome style in template and base, then create function for it
$chromes = [];
$chromepath = '/html/layouts/chromes';

$path = T4PATH_LOCAL . $chromepath;
if (is_dir($path)) {
	$files = \JFolder::files($path, '\.php$');
	if ($files) $chromes = array_merge($chromes, $files);
}

$path = T4PATH_TPL . $chromepath;
if (is_dir($path)) {
	$files = \JFolder::files($path, '\.php$');
	if ($files) $chromes = array_merge($chromes, $files);
}

$path = T4PATH_BASE . $chromepath;
if (is_dir($path)) {
	$files = \JFolder::files($path, '\.php$');
	if ($files) $chromes = array_merge($chromes, $files);
}

foreach ($chromes as $chrome) {
	$style = substr($chrome, 0, -4);
	$func = 'modChrome_' . $style;
	if (!function_exists($func)) {
		eval('function ' . $func . '($module, $params, $attribs) {\T4\Helper\Chromes::render(\'' . $style . '\', $module, $params, $attribs);}');
	}
}
