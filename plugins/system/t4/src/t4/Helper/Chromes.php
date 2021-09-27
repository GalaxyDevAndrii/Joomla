<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Layout\LayoutHelper;

class Chromes {
	public static function render($style, $module, $params, $attribs) {
		$displayData = array(
			'module'  => $module,
			'params'  => $params,
			'attribs' => $attribs,
		);
		echo LayoutHelper::render('chromes.' . $style, $displayData);
	}
}