<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry as JRegistry;

class Layout {
	static public $styles = [];
	static public $css_tpl = null;

	public static function j3($file) {
		$j = 'j' . explode('.', JVERSION)[0];
		$jfile = preg_replace('/(\.[^\.]+)$/', '.' . $j . '\1', $file);
		return ($j == 'j3' && is_file($jfile) && $jfile != $file) ? $jfile : false;
	}

	public static function isSubpage() {
		static $isSubpage = null;
		if ($isSubpage !== null) return $isSubpage;

		$app = JFactory::getApplication();
		$active = $app->getMenu()->getActive();
		if (!$active) return false;
		$input = $app->input;
		$query = new JRegistry($active->query);

		$isSubpage = $input->get('option') != $query->get('option')
			|| $input->get('view') != $query->get('view')
			//|| $input->get('layout') != $query->get('layout')
			|| $input->get('id') != $query->get('id');
		return $isSubpage;
	}
}
