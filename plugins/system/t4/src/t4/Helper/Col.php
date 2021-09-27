<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;

class Col {
	static $screens = ['xs', 'sm', 'md', 'lg', 'xl'];

	public static function getCls($screen, $val) {
		if (empty($val) || $val == 'none') return '';
		$cls = 'col' . ($screen == 'xs' ? '' : '-' . $screen);
		if (is_numeric($val)) $cls = $cls . '-' . $val;
		return $cls;
	}

	public static function addCls($screen, $val, &$coll) {

		$idx = array_search($screen, self::$screens);
		if ($idx === false) return '';

		$toaddcls = self::getCls($screen, $val);
		if (!$toaddcls) return '';

		if ($idx > 0 && count($coll)) {
			$lastcls = end($coll);
			// detect similar class in smaller screen, if found, no need to add current class
			for ($i = $idx-1; $i >= 0; $i--) {
				$s = self::$screens[$i];
				$cls = self::getCls($s, $val);
				if ($cls == $lastcls) return $toaddcls;
				// if (in_array($cls, $coll)) return $toaddcls;
				// check if there's this screen class
				$cls = self::getCls($s, 'auto');
				if (strpos($lastcls, $cls) === 0) break; // need add cls
			}
		}

		$coll[] = $toaddcls;
		return $toaddcls;
	}



	public static function addHiddenCls($col, &$coll) {
		for ( $i = 0;$i < count(self::$screens); $i++) {
			$s = self::$screens[$i];
			$name = 'hidden_' . $s;

			if (isset($col[$name]) && $col[$name]) {
				// add hide class, for all small screen to the current check
				// $coll[] = 'd-none';
				// add display screen larger screen than the current check
				$coll[] = ($s != 'xs') ? 'd' . '-' . $s  . '-none' : 'd-none';
			}else{
				if(in_array('d-none', $coll)){
					$coll[] = 'd-'.$s.'-block';
				}
			}
		}
		return;

	}

	public static function getHiddenRowCls ($cols) {
		$cls = array();
		for ( $i = 0;$i < count(self::$screens); $i++) {
			$s = self::$screens[$i];

			// check if all column is hidden in this screen
			$hidden = true;
			$name = 'hidden_' . $s;
			foreach ($cols as $col) {
				if (empty($col[$name])) {
					$hidden = false;
					break;
				}
			}

			if ($hidden) {
				$cls[] = ($s != 'xs') ? 'd-'.$s.'-none' : 'd-none';/*
				if($s != 'xl') $d = self::$screens[$i+1];
				$cls[] = ($d == "undefined") ? 'd-block' : 'd' . '-' . $d  . '-block';*/
				// return implode(' ', $cls);
			}else{
				if(in_array('d-none', $cls)){
					$cls[] = 'd-'.$s.'-block';
				}
			}
		}
		return implode(' ', $cls);
	}
}
