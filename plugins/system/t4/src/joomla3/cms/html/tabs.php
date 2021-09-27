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
 * Utility class for Tabs elements.
 *
 * @since       1.6
 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
 */
abstract class JHtmlTabs
{
	static $group = 'tabs';
	/**
	 * Creates a panes and creates the JavaScript object for it.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  An array of option.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
	 */
	public static function start($group = 'tabs', $params = array())
	{
		self::$group = $group;

		$html = JHtmlBootstrap::startTabSet($group, $params);
		$html .= '<div style="display:none;">';
		return $html;
	}

	/**
	 * Close the current pane
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   1.6
	 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
	 */
	public static function end()
	{
		return JHtmlBootstrap::endTab() . JHtmlBootstrap::endTabSet();
	}

	/**
	 * Begins the display of a new panel.
	 *
	 * @param   string  $text  Text to display.
	 * @param   string  $id    Identifier of the panel.
	 *
	 * @return  string  HTML to start a new panel
	 *
	 * @since   1.6
	 * @deprecated  3.7.0 These helpers are dependent on the deprecated MooTools support
	 */
	public static function panel($text, $id)
	{
		$html = JHtmlBootstrap::endTab();
		$html .= JHtmlBootstrap::addTab(self::$group, $id, $text);
		return $html;
	}

}
