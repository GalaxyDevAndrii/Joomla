<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry as JRegistry;
use Joomla\CMS\Form\Form as JForm;
use T4Admin\T4form AS T4form;

class Css {
	public static function render($tplcss, $data) {

		$params = is_array($data) ? new JRegistry($data) : $data;
		
		// add padding and margin responsive to tplcss
		$tplcss .= self::renderPaddingMargin($params);

		$tplcss = str_replace('{', "{\n", $tplcss);
		$tplcss = str_replace('}', "\n}", $tplcss);
		$arr = explode("\n", $tplcss);
		$result = [];
		foreach ($arr as $i => $s) {
			if (!trim($s)) continue;

			$s = self::renderLine($s, $params);
			if ($s) $result[] = $s;
		}
		$css = implode("\n", $result);

		// remove empty rules
		$css = preg_replace('/[^{}]*\{\s*\}/mu', '', $css);

		return $css;
	}
	public static function renderRoot($data)
	{
		$params = is_array($data) ? new JRegistry($data) : $data;
		// new form
		$xmlfile = \T4\Helper\Path::findInTheme('params/typelist-theme.xml');
		$forms = JForm::getInstance('typelist-theme', $xmlfile);
		$form = new T4form($forms);
		if ($xmlfile) $form->loadFile($xmlfile);
		//load the template
		$tplXml = T4PATH_TPL . '/templateDetails.xml';
		//overwrite / extend with params of templateDetails
		$form->loadFile($tplXml, true, '//theme');
		$group_theme_colors = $form->getFieldsets();
		$root = array();
		$root[] = ":root{";
		foreach ($group_theme_colors as $group_name => $group_data) {
			$group_fields = $form->getFieldset($group_name);
			foreach ($group_fields as $fields) {
				if(in_array($fields->getAttribute('type'),array('t4color','t4brand'))){
					$root_val = $params->get($fields->getAttribute('name')) ? $params->get($fields->getAttribute('name')) : ($fields->getAttribute('default') ? $fields->getAttribute('default') : "__".$fields->getAttribute('name'));
					if($fields->getAttribute('data-variable')){
						$root[] = $fields->getAttribute('data-variable') .":" . $root_val .";";
					}else{
						$variable = "--".str_replace("_", "-", $fields->getAttribute('name'));
						$root[] = $variable .":" . $root_val .";";
					}
				}
			}
		}
		$root[] = "}";
		return implode("\n", $root);

	}
	public static function sass_lighten($hexcolor, $percent) {
		if(!$hexcolor || $hexcolor == 'inherit') return "";
		if(preg_match("/#/",  $hexcolor,$matches)){
			if ( strlen( $hexcolor ) < 6 ) {
				$hexcolor = $hexcolor[0] . $hexcolor[0] . $hexcolor[1] . $hexcolor[1] . $hexcolor[2] . $hexcolor[2];
			}
		  $hexcolor = array_map('hexdec', str_split( str_pad( str_replace('#', '', $hexcolor), 6, '0' ), 2 ) );
		}else{

			$hexcolor = explode(",",str_replace("rgb(", "",str_replace("rgba(", "", str_replace(")", "", $hexcolor))));
			$hexcolor = array_splice($hexcolor, 3);
		}
	  foreach ($hexcolor as $i => $color) {
	  	$color = (int)$color;
	    $from = $percent < 0 ? 0 : $color;
	    $to = $percent < 0 ? $color : 255;
	    $pvalue = ceil( ($to - $from) * $percent );
	    $hexcolor[$i] = str_pad( dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
	  }
	  return '#' . implode($hexcolor);
	}
	public static function sass_darken($hexcolor, $percent) {
		if(!$hexcolor || $hexcolor == 'inherit') return "";
		if(preg_match("/#/",  $hexcolor,$matches)){
			if ( strlen( $hexcolor ) < 6 ) {
				$hexcolor = $hexcolor[0] . $hexcolor[0] . $hexcolor[1] . $hexcolor[1] . $hexcolor[2] . $hexcolor[2];
			}
		  $hexcolor = array_map('hexdec', str_split( str_pad( str_replace('#', '', $hexcolor), 6, '0' ), 2 ) );
		}else{

			$hexcolor = explode(",",str_replace("rgb(", "",str_replace("rgba(", "", str_replace(")", "", $hexcolor))));
			$hexcolor = array_splice($hexcolor, 3);
		}

		foreach ($hexcolor as $i => $color) {
			$color = (int)$color;
			$from = $percent < 0 ? 0 : $color;
			$to = $percent < 0 ? $color : 0;
			$pvalue = ceil( ($to - $from) * $percent );
			$hexcolor[$i] = str_pad( dechex($color + $pvalue), 2, '0', STR_PAD_LEFT);
		}

		return '#' . implode($hexcolor);
	}

	public static function renderLighten($csstpl,$data)
	{
		$params = is_array($data) ? new JRegistry($data) : $data;
		$data = array();
		self::getVars($csstpl, $params, $data);
		$rootCss2 = array();
		$rootCss2[] = ":root{";
		foreach ($data as $variable => $value) {

			$rootCss2[] = $variable .":" .$value.";";
			$lightenArr = array('--color-blue','--color-indigo','--color-purple','--color-pink','--color-red','--color-orange','--color-yellow','--color-green','--color-teal','--color-cyan');
			if(in_array($variable,$lightenArr)){
				$rootCss2[] = $variable."-100" .":" .self::sass_lighten($value,0.8).";";
				$rootCss2[] = $variable."-200" .":" . self::sass_lighten($value,0.6).";";
				$rootCss2[] = $variable."-300" .":" . self::sass_lighten($value,0.4).";";
				$rootCss2[] = $variable."-400" .":" . self::sass_lighten($value,0.2).";";
				$rootCss2[] = $variable."-500" .":" . $value.";";
				$rootCss2[] = $variable."-600" .":" . self::sass_darken($value,0.2).";";
				$rootCss2[] = $variable."-700" .":" . self::sass_darken($value,0.4).";";
				$rootCss2[] = $variable."-800" .":" . self::sass_darken($value,0.6).";";
				$rootCss2[] = $variable."-900" .":" . self::sass_darken($value,0.8).";";
			}
		}
		$rootCss2[] = "}";
		return implode("\n", $rootCss2);
	}
	public static function renderTheme($tpl, $root, $data) {
		$params = is_array($data) ? new JRegistry($data) : $data;
		// parse to get variable map
		$vars = [];
		self::getVars($tpl, $params, $vars);
		self::getVars($root, $params, $vars);

		// parse tpl and replace value
		$replace = [];
		if (preg_match_all('/var\(([^\)]+)\)/mi', $tpl, $matches)) {

			foreach ($matches[1] as $i => $name) {
				if (!empty($vars[$name])) {
					$replace[$matches[0][$i]] = $vars[$name];
				} else {
					$replace[$matches[0][$i]] = Color::getInstance()->getColor(ltrim($name, '-'));
				}
				// $replace[$matches[0][$i]] = !empty($vars[$name]) ? $vars[$name] : '';
			}
			return str_replace(array_keys($replace), array_values($replace), $tpl);
		}

		return $tpl;
	}


	public static function getVars($tpl, $params, &$output) {

		if (preg_match_all('/^\s*(\-\-[a-z0-9][^\:]*)\:\s*([^;]+);/mi', $tpl, $matches)) {
			
			foreach ($matches[1] as $i => $name) {
				$val = self::renderLine($matches[2][$i], $params);
				$output[$name] = $val;
			}
		}
	}

	public static function renderLine($s, $params) {

		if (!preg_match_all('/__([0-9a-z_]+)/i', $s, $matches)) {
			return $s;
		}
		$search = [];
		$replace = [];
		foreach ($matches[1] as $j => $name) {
			$val = $params->get($name);
			if ($val) {
				if (preg_match('/url\(' . preg_quote($matches[0][$j]) . '\)/', $s)) $val = self::sefUrl($val);
				if(preg_match('/(\s*)color/',$val) || preg_match('/gray.+(.*)/',$val)){
					if($params->get('styles_'.(str_replace(' ','_',$val)))) $val = $params->get('styles_'.(str_replace(' ','_',$val)));
					if($params->get('styles_brand_'.(str_replace(' color','',$val)))) $val = $params->get('styles_brand_'.(str_replace(' color','',$val)));
				}
				$search[] = $matches[0][$j];
				$replace[] = $val;
			}
		}
		if (count($search)) {
			$s = str_replace($search, $replace, $s);
			return $s;
		}

		return '';
	}


	protected static function sefUrl($url) {
		if (preg_match('/^(https?\:|\/)/', $url)) return $url;
		return \JUri::root(true) . '/' . $url;
	}

	protected static function renderPaddingMargin($params)
	{
		$css = "";
		$Device = self::getDeviceWidth();
		foreach ($Device as $pdKey => $pdValue) {

			$screenValuve = "min-width: ".$pdValue;
			if(!isset($params['margin_'.$pdKey]) && !isset($params['padding_'.$pdKey])){
				continue;
			}
			if($pdKey !== 'xs'){
				$css .= "@media only screen and (".$screenValuve.") {\r\n";
				
			}
			$css .= "	#__id{"."\r\n";
			if(isset($params['margin_'.$pdKey])){
				$css .= "		margin:". $params['margin_'.$pdKey].";"."\r\n" ;
			} if(isset($params['padding_'.$pdKey])){
				$css .= "		padding:". $params['padding_'.$pdKey].";"."\r\n" ;
			} 
			$css .= "	}\r\n" ;
			if($pdKey !== 'xs'){
				$css .= "}\r\n";
			}
		}
		return $css;
	}
	protected static function getDeviceWidth()
	{
		$device = array(
			'xs'=>"0px",
			'sm'=>"576px",
			'md'=>"768px",
			'lg'=>"992px",
			'xl'=>"1200px",
		);
		return $device;
	}
}
