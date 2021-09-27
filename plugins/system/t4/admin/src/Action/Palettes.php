<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry;
use T4\Helper\T4Compatible;
class Palettes {
	public static function doSave () {
		$input = JFactory::getApplication()->input->post;
		$value =  $input->getRaw('value');
		$all = ($input->getRaw('all') == 'true') ? true : false;
		if (empty($value)) {
			return ['error' => 'Missing params of palettes'];
		}
		$file = T4PATH_LOCAL . '/etc/palettes.json';
		$dir = dirname($file);
		if (!is_dir($dir)) \JFolder::create($dir);

		// local palettes
		if($all){
			if(!\JFile::write ($file, json_encode($value))){
				return ['error' => 'palettes saving error!'];
			}
		}else{
			$paletteName = key($value);
			$userpalettes = is_file($file) ? (array) json_decode(file_get_contents($file), true) : [];
			$userpalettes[$paletteName] = $value[$paletteName];
			$palettes = json_encode($userpalettes);
			if(!\JFile::write ($file, $palettes)){
				return ['error' => 'palette saving error!'];
			}
		}
		return ["ok" => 1,'palettes'=> $value ];
	}
	public static function doRemove()
	{
		$input = JFactory::getApplication()->input->post;
		$output = ['ok' => 1,'datacolor'=>""];
		$plName =  $input->getRaw('name');
		if (!$plName) {
			return ['error' => 'Missing params'];
		}
		$file = T4PATH_LOCAL . '/etc/palettes.json';
		$dir = dirname($file);
		if (!is_dir($dir)) \JFolder::create($dir);
		$allColor = T4Compatible::loadColors();

		// get base palettes
		$basepalettes = (array) json_decode(\T4\Helper\Path::getFileContent('etc/palettes.json', false), true);
		if(isset($basepalettes[$plName])){
			$plDefault = $basepalettes[$plName];
		}else{
			$plDefault = '';
		}
		// local palettes
		$userpalettes = is_file($file) ? (array) json_decode(file_get_contents($file), true) : [];

		if(!empty($plDefault)){
			$output = ['ok' => 1,'datacolor'=>$plDefault];
		}

		if(isset($userpalettes[$plName])){
			unset($userpalettes[$plName]);
	      	// write to file 
	      	if (!\JFile::write ($file, json_encode($userpalettes))) {
	          	$output = ['error' => \JText::_('T4_ADDONS_DELETE_ERROR')];
	      	} else {
	          	$output = ['ok' => 1, 'datacolor'=> $plDefault];
	      	}
	    }

		return $output;
	}
	public static function loadColors($color_name)
	{
		$template = \T4Admin\Admin::getTemplate(true);
		$customcolors = (array)json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json'));
		$temp_Params = new Registry($template->params);

		$t4Theme = \T4\Helper\Path::getFileContent('etc/theme/' . $temp_Params->get('t4-theme') . '.json');
		if (!$t4Theme) {
		  $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/default.json');
		}

		$datas = @json_decode($t4Theme);
		$Color_Params = ['brand_color' => [], 'user_color' => []];
		foreach ($datas as $key => $value) {
		  if (preg_match('/^color_([a-z]+)(_|$)/', $key, $match)) {
		      $type = 'brand_color';
	      $Color_Params[$type][$key] = $value;
	  	} elseif ($key == 'custom_colors') {
	      $type = 'user_color';
	      $data = json_decode($value);
	      if (empty($data)) {
	          $data = $customcolors;
	      } else {
	          $vals = @json_decode($datas->custom_colors);
	          if (empty($vals)) {
	              $vals = [];
	          }

	          // user color
	          foreach ($customcolors as $name => $color) {
	              $value = (!empty($vals->{$name}) && !empty($vals->{$name}->color)) ? $vals->{$name}->color : $color->color;
	              $color->color = $value;
	          }
	          $data = $customcolors;
	      }
	      foreach ($data as $clsColor => $color_custom) {
	          $Color_Params[$type][$clsColor] = $color_custom->color;
	      }
	  	}elseif (preg_match('/(.*)_color/', $key, $match)){
                $type = "template_color";
                $Color_Params[$type][$key] = $value;
	    }else{
	        $type = "";
	    }
	}
	$color = '';

	foreach ($Color_Params as $group) {
        if(array_key_exists($value,  $group)){
            $color = $group[$value];
        }elseif (array_key_exists("color_".$value,  $group)) {
            $color = $group["color_".$value];
        }
    }
	return $color;
  }


}