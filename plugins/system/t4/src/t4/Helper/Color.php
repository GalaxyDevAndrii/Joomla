<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;

class Color {
	var $params;
	var $userColors;
	var $palettes = [];

	public function __construct($params) {

		$this->params = $params;
		// get user color
		$this->userColors = $params->get('custom_colors');

		if(!empty($params->get('styles_palettes'))){
			$palettes = json_decode($params->get('styles_palettes'),true);
		}else{
			// color palettes
			$palettes = (array)json_decode(Path::getBaseContent('etc/palettes.json'), true);
			$userpalettes = (array)json_decode(Path::getLocalContent('etc/palettes.json'), true);
			foreach ($userpalettes as $key => $palette) {
				$palettes[$key] = $palette;
			}
		}

		if (!empty($palettes)) {
			foreach ($palettes as $pname => $palette) {
				// update color alias
				foreach ($palette as $name => $val) {
					if (preg_match('/_color$/', $name)) {
						$palette[$name] = $this->getColor($val);
					}
				}
				$palette['class'] = 't4-palette-' . $pname;
				$palettes[$pname] = $palette;
			}
			$this->palettes = $palettes;
		}


		// update color for params
		$props = $params->toArray();
		foreach ($props as $name => $val) {
			if (preg_match('/_color$/', $name)) {
				$this->params->set($name, $this->getColor($val));
			}
			//set custom color to variable
			// elseif(preg_match('/_colors$/', $name))
			// {
			// 	foreach ($val as $color_name => $color) {
			// 		$this->params->set($color_name, $this->getColor($color));
			// 	}
			// }
		}

		// update user color
		if(!empty($this->getUserColors())){
			foreach ($this->getUserColors() as $name => $val) {
				$this->params->set('user_color_' . $name, $val);
			}
		}
	}

	public static function getInstance ($params = null) {
		static $me = null;
		if ($me === null) {
			$me = new Color ($params);
		}
		return $me;
	}

	public function getUserColors() {
		return !empty($this->userColors) ? $this->userColors : array();
	}

	public function getPalettes() {
		return $this->palettes;
	}

	public function getParams() {
		return $this->params;
	}


	public function getColor($alias) {
		$val = $alias;
		if (!$val) return '';
		if (!preg_match('/^(#|rgb)/', $val)) {
			$val = str_replace(" ","_",strtolower($val));
			if (!empty($this->userColors[$val])) {

				$val = $this->userColors[$val];
			} else {
				if (!preg_match('/color_/', $val)) $val = 'color_' . $val;
				$val = $this->params->get($val);
			}
		}
		return $val;
	}
}
