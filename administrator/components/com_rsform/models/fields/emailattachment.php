<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldType('text');

class JFormFieldEmailattachment extends JFormFieldText
{
	protected function getInput()
	{
		$html 	= parent::getInput();
		$file 	= $this->value;
		$folder = $file && file_exists($file) ? '&folder=' . urlencode(dirname($file)) : '';
		$url 	= JRoute::_('index.php?option=com_rsform&controller=files&task=display&tmpl=component' . $folder);
		$html  .= '<a href="' . $url . '" onclick="openRSModal(this.href); return false;" class="btn btn-secondary"><span class="rsficon rsficon-file-text-o"></span> ' . JText::_('RSFP_SELECT_FILE') . '</a>';

		if ($file && !file_exists($file))
		{
			$html .= '<div class="alert alert-danger">' . JText::_('RSFP_EMAILS_ATTACH_FILE_WARNING') . '</div>';
		}

		return $html;
	}
}
