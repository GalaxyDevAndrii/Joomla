<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('textarea');

class JFormFieldSyntaxhighlight extends JFormFieldTextarea
{
	protected function getInput()
	{
		$useEditor 	= RSFormProHelper::getConfig('global.codemirror');
		$editor 	= 'codemirror';

		if ($useEditor)
		{
			$plugin = JPluginHelper::getPlugin('editors', $editor);

			if (empty($plugin))
			{
				$useEditor = false;
			}
			elseif (!is_string($plugin->params) && is_callable(array($plugin->params, 'toString')))
			{
				$plugin->params = $plugin->params->toString();
			}
		}

		if ($useEditor)
		{
			$syntax 	= !empty($this->element['syntax']) ? (string) $this->element['syntax'] : 'html';
			$readonly 	= $this->readonly;
			$instance 	= new \Joomla\CMS\Editor\Editor($editor);

			// Inline PHP
			if ($syntax === 'php')
			{
				JFactory::getDocument()->addScriptDeclaration("jQuery(function(){Joomla.editors.instances[" . json_encode($this->id) . "].setOption('mode', 'text/x-php');});");
			}

			return $instance->display($this->name, $this->escape($this->value), '100%', 300, 75, 20, $buttons = false, $this->id, $asset = null, $author = null, array('syntax' => $syntax, 'readonly' => $readonly));
		}
		else
		{
			return parent::getInput();
		}
	}

	protected function escape($string)
	{
		return htmlspecialchars($string, ENT_COMPAT, 'utf-8');
	}
}
