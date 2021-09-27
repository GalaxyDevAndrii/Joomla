<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;

class Customcolors {
	public static function doSave () {
		$input = JFactory::getApplication()->input->post;
		$customColor =  $input->getRaw('color');
		if (empty($customColor)) {
			return ['error' => 'Missing params'];
		}
		$output = ["ok" => 1];
		$userColorFile = T4PATH_LOCAL . '/etc/spectrumColor.json';
		$dir = dirname($userColorFile);
		if (!is_dir($dir)) \JFolder::create($dir);
		$colors = is_file($userColorFile) ? explode(";",file_get_contents($userColorFile)) : explode(";",\T4\Helper\Path::getFileContent('etc/spectrumColor.json', false));
		if(empty($colors[0])) $colors = array();
		if(!in_array($customColor, $colors )){
			$colors[] = $customColor;
		}
		\JFile::write ($userColorFile, implode(";", $colors));
		return $colors;
	}
	public static function doLoad () {
		$userColorFile = T4PATH_LOCAL . '/etc/spectrumColor.json';
		$colors = is_file($userColorFile) ? explode(";",file_get_contents($userColorFile)) : explode(";",\T4\Helper\Path::getFileContent('etc/spectrumColor.json', false));
		return $colors;
	}
	public static function doFirstsave(){
		$input = JFactory::getApplication()->input->post;
		$customColor =  $input->getVar('data');
		if (empty($customColor)) {
			return ['error' => 'Missing params'];
		}
		$userColorFile = T4PATH_LOCAL . '/etc/spectrumColor.json';
		$dir = dirname($userColorFile);
		if (!is_dir($dir)) \JFolder::create($dir);
		$colors = json_decode($customColor);
		\JFile::write ($userColorFile, implode(";", $colors));
		return $colors;
	}
	public static function doRemove () {
		$name = JFactory::getApplication()->input->post->getVar('name');
		$name = str_replace(" ", "_", $name);
		if (!$name) {
			return ['error' => \JText::_('T4_CUSTOM_COLOR_MISSING_PARAMS')];
		}
	    $userColorFile = T4PATH_LOCAL . '/etc/customcolors.json';
	    $colors = is_file($userColorFile) ? explode(file_get_contents($userColorFile), true) : [];
	    if (isset($colors[$name])) {
	        unset($colors[$name]);
	        // write to file 
	        if (!\JFile::write ($userColorFile, json_encode($colors))) {
	            $output = ['error' => \JText::_('T4_CUSTOM_COLOR_DELETE_ERROR')];
	        } else {
	        	$output['status'] = 'loc';
	        	$colors = self::getBaseUserColors($name);
	       		if(isset($colors['color'])){
	       			$output['status'] = 'org';
	            $output['color'] = $colors['color'];
	       		}
	          $output['ok'] = 1;
	        }
	    }else {
	        $output = ['error' => \JText::_('T4_CUSTOM_COLOR_DELETE_NOTFOUND_ERROR')];
	    }

	    return $output;
	}
	public static function getBaseUserColors($name){
		// get base custom colors
		$baseUsercolors = (array) json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json', false), true);
		$colors = (isset($baseUsercolors[$name])) ? $baseUsercolors[$name] : "";
		return $colors;
	}
	
}