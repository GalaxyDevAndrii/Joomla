<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;

class Draft {
	public static function doSave () {
		$key = \T4Admin\Draft::store();
		return ["ok" => 1, "key" => $key];
	}
}