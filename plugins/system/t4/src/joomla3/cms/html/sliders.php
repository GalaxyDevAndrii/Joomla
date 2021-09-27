<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Sliders elements
 *
 * @since       1.6
 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
 */
abstract class JHtmlSliders
{
	static $group = 'sliders';
	/**
	 * Creates a panes and loads the javascript behavior for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of options.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
	 */
	public static function start($group = 'sliders', $params = array())
	{
		self::$group = $group;

		$html = JHtmlBootstrap::startAccordion($group, $params);
		$html .= '<div style="display:none;"><div><div>';
		return $html;
	}

	/**
	 * Close the current pane.
	 *
	 * @return  string  hTML to close the pane
	 *
	 * @since   1.6
	 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
	 */
	public static function end()
	{
		return JHtmlBootstrap::endSlide() . JHtmlBootstrap::endAccordion();
	}

	/**
	 * Begins the display of a new panel.
	 *
	 * @param   string  $text  Text to display.
	 * @param   string  $id    Identifier of the panel.
	 *
	 * @return  string  HTML to start a panel
	 *
	 * @since   1.6
	 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
	 */
	public static function panel($text, $id)
	{
		$html = JHtmlBootstrap::endSlide();
		$html .= JHtmlBootstrap::addSlide(self::$group, $text, $id);
		return $html;
	}
}
