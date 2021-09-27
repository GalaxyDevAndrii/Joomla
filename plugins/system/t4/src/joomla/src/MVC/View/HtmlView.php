<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\MVC\View;

use Joomla\CMS\Filesystem\Path;

defined('JPATH_PLATFORM') or die;


// Make alias of original FileLayout
\T4\Helper\Joomla::makeAlias(JPATH_LIBRARIES . '/src/MVC/View/HtmlView.php', 'HtmlView', '_JHtmlView');


/**
 * Base class for a Joomla View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  2.5.5
 */
class HtmlView extends _JHtmlView
{

	// public function display($tpl = null)
	// {
	// 	$result = \T4\Helper\J3J4::isJ3() ? $this->loadTemplateJ3($tpl) : $this->loadTemplate($tpl);

	// 	if ($result instanceof \Exception)
	// 	{
	// 		return $result;
	// 	}

	// 	echo $result;
	// }

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 *
	 * @return  string  The output of the the template script.
	 *
	 * @since   3.0
	 * @throws  \Exception
	 */
	public function loadTemplate($tpl = null)
	{
		if (\T4\Helper\J3J4::isJ4()) return parent::loadTemplate($tpl);

		// Clear prior output
		$this->_output = null;

		$template = \JFactory::getApplication()->getTemplate();
		$layout = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang = \JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate !== '_' && $layoutTemplate != $template)
		{
			$this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
		}

		// Load the template script
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $file));

		// find for j3 specific first
		$filetofindj3 = preg_replace('/\.php$/', '.j3.php', $filetofind);
		$this->_template = \JPath::find($this->_path['template'], $filetofindj3);

		if (!$this->_template)
			$this->_template = \JPath::find($this->_path['template'], $filetofind);

		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false)
		{
			$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = \JPath::find($this->_path['template'], $filetofind);
		}

		if ($this->_template != false)
		{
			// Unset so as not to introduce into template scope
			unset($tpl, $file);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else
		{
			throw new \Exception(\JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
		}
	}

	protected function _addPath($type, $path)
	{
		//Trigger event, then can alter include paths
		$path = (array)$path;

		// revert order - the addPath add priority in a revert order, the we need make it unnormal order
		$path = array_reverse($path);
		\Joomla\CMS\Factory::getApplication()->triggerEvent('onHtmlViewAddPath', array ($type, &$path));
		$path = array_reverse($path);

		return parent::_addPath($type, $path);
	}

}
