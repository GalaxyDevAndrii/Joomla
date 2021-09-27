<?php

namespace T4Admin;

defined('_JEXEC') or die();
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\Form as JForm;
use Joomla\Registry\Registry as JRegistry;
use T4\Helper\Path;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class T4Compatible
{
    public static $temp_data = null;
    public static $temp_Params = null;
    public static $palettes = null;
    public static function run($temp_data)
    {
        self::$temp_data = $temp_data;
        self::$temp_Params = new JRegistry($temp_data->params);
        if(self::check()){
         		$backup = T4PATH_TPL . "/backup";
            if(!is_dir($backup)){
            	self::backupFile();
    		}
            self::T4CompatibleVersions();
        }
    }
    public static function check()
    {
		$temp_Params = self::$temp_Params;
 		$tempDatas = self::loadTypelistData($temp_Params);
 		$siteData = $tempDatas['site'];
		$themeData = $tempDatas['theme'];
        if(!array_key_exists("body_font_family", $siteData) || array_key_exists('custom_colors',$themeData)){
            return true;
        }
        return false;
    }
    public static function T4CompatibleVersions()
    {
        $temp_Params = self::$temp_Params;

        // compare palettes
        self::$palettes = self::comparePalettes();
        // save palettes
        self::saveLayoutPalettes('palettes', self::$palettes);

        //save theme and site setting to file
        $dataTypelists = self::loadTypelistData($temp_Params);
        $typelistSettings = array('site','theme');

        foreach ($typelistSettings as $typelist) {
                self::saveSetting($typelist,$dataTypelists);
        }

        return true;
    }
    
    public static  function loadTypelistData($TempData)
    {
        $groups = ['site', 'theme'];
        $dataTypelist = array();
        // end check;
        foreach ($groups as $group) {
            $profile = $TempData->get('typelist-' . $group);
            $content = Path::getFileContent('etc/' . $group . '/' . $profile . '.json');
            if (!$content) {
                $profile = 'default';
                $content = Path::getFileContent('etc/' . $group . '/' . $profile . '.json');
            }
            $dataTypelist[$group] = json_decode($content, true);
        }

        return $dataTypelist;
    }
    public static function getAllTemplateParams($template)
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from( $db->quoteName('#__template_styles') );
        $query->where( $db->quoteName('client_id') . ' = 0' );
        $query->where( $db->quoteName('template') . ' = ' . $db->quote($template) );
        $query->order('id');

        $db->setQuery($query);

        $tpl = $db->loadObjectList();

        return $tpl;
    }
    public static function saveSetting($type,$data)
    {
        $initCompare = false;
        $temp_Params = self::$temp_Params;
        $t4Data = $data[$type];
        if($type == 'site'){
            $t4ThemeData = $data['theme'];
        }
        $allColors = self::loadAllColors();
        $xmlfile = Path::findInTheme('params/typelist-'.$type.'.xml');
        $forms = JForm::getInstance('typelist-'.$type, $xmlfile);
        $form = new T4form($forms);
        if ($xmlfile) $form->loadFile($xmlfile);
        //load the template
        $tplXml = T4PATH_TPL . '/templateDetails.xml';
        //overwrite / extend with params of templateDetails
        $form->loadFile($tplXml, true, '//'.$type);
        $group_sites = $form->getFieldsets();
        foreach ($group_sites as $group_name => $group_data) {
            $group_fields = $form->getFieldset($group_name);
            foreach ($group_fields as $fields) {
                if(!array_key_exists($fields->getAttribute('name'), $t4Data) && $fields->getAttribute('type') != 'spacer'){
                    $initCompare = true;
                    $t4Data[$fields->getAttribute('name')] = "";
                    if($type == 'site' && !empty($t4ThemeData[$fields->getAttribute('name')])){
                        if (strpos($fields->getAttribute('name'), 'font_family') !== false) {
                            $font_style = str_replace("_family", "_load_weights", $fields->getAttribute('name'));
                            $t4Data[$font_style] = $t4ThemeData[$font_style];
                        }
                        $t4Data[$fields->getAttribute('name')] = $t4ThemeData[$fields->getAttribute('name')];
                    }elseif($type == 'theme'){
                        $t4Data[$fields->getAttribute('name')] = $fields->getAttribute('default');
                    }
                }
            }
        }
        if($initCompare){
            if($type == 'theme'){
                $t4Data['styles_palettes'] = json_encode(self::$palettes);

                //convert page color setting 
               if(!empty($allColors[str_replace(" ", "_", $t4Data['body_bg_color'])])) $t4Data['body_bg_color'] = $allColors[str_replace(" ", "_", $t4Data['body_bg_color'])];
               if(!empty($allColors[str_replace(" ", "_", $t4Data['body_text_color'])])) $t4Data['body_text_color'] = $allColors[str_replace(" ", "_", $t4Data['body_text_color'])];
               if(!empty($allColors[str_replace(" ", "_", $t4Data['link_color'])])) $t4Data['body_link_color'] = $allColors[str_replace(" ", "_", $t4Data['link_color'])];
               if(!empty($allColors[str_replace(" ", "_", $t4Data['link_hover_color'])])) $t4Data['body_link_hover_color'] = $allColors[str_replace(" ", "_", $t4Data['link_hover_color'])];
               // unset custom_colors
               if(!empty($t4Data['custom_colors'])){
                	unset($t4Data['custom_colors']);
               }
            }
            self::saveLayout($type,$temp_Params->get('typelist-'.$type),json_encode($t4Data),true);
        }
    }
    public static function saveLayout($type,$name,$data, $local = false)
    {
        //save to base
        if($name == 'palettes'){
            $fileBase = T4PATH_TPL . '/etc/' .$name . '.json';
        }else{
            $fileBase = T4PATH_TPL . '/etc/'.$type.'/' .$name . '.json';
        }
        $dirBase = dirname($fileBase);
        if (!is_dir($dirBase)) \JFolder::create($dirBase);
        \JFile::write($fileBase, $data);

        // update file local
        if($local){
             $fileLocal = str_replace(T4PATH_TPL, T4PATH_LOCAL, $fileBase);
             $dirLocal = dirname($fileLocal);
             if (!is_dir($dirLocal)) \JFolder::create($dirLocal);
            \JFile::write($fileLocal, $data);
        }
    }
    public static function saveLayoutPalettes($name,$data)
    {
    		$paletteBase = $paletteLocal = array();
    		foreach ($data as $pl_k =>  $pl) {
    			if($pl['status'] == 'org'){
    				$paletteBase[$pl_k] = $pl;
    			}else{
    				$paletteLocal[$pl_k] = $pl;
    			}
    		}

        if(!empty($paletteBase)){
          $fileBase = T4PATH_TPL . '/etc/' .$name . '.json';
	        $dirBase = dirname($fileBase);
	        if (!is_dir($dirBase)) \JFolder::create($dirBase);
	        \JFile::write($fileBase, json_encode($paletteBase));
        }

        // update file local
        if(!empty($paletteLocal)){
             $fileLocal = str_replace(T4PATH_TPL, T4PATH_LOCAL, $fileBase);
             $dirLocal = dirname($fileLocal);
             if (!is_dir($dirLocal)) \JFolder::create($dirLocal);
            \JFile::write($fileLocal, json_encode($paletteLocal));
        }
    }
    public static function comparePalettes()
    {

        // get base palettes
        $basepalettes = (array) json_decode(Path::getFileContent('etc/palettes.json', false), true);
        // local palettes
        $file = T4PATH_LOCAL . '/etc/palettes.json';
        $userpalettes = is_file($file) ? (array) json_decode(file_get_contents($file), true) : [];
        $allPalettes = array_merge($userpalettes,$basepalettes);
        $keys = array_unique(array_merge(array_keys($basepalettes), array_keys($userpalettes)));
        $palettes = [];
        foreach ($keys as $key) {
            $base = !empty($basepalettes[$key]) ? $basepalettes[$key] : [];
            $local = !empty($userpalettes[$key]) ? $userpalettes[$key] : [];
            $status = '';
            $ovr = $loc = $org = false;
            if (!empty($base) && !empty($local)) {
                $palette = $local;
                $ovr = true;
            } else if (!empty($base)) {
                $palette = $base;
                $org = true;
            } else if (!empty($local)) {
                $palette = $local;
                $loc = true;
            }
            $status = $ovr || ($loc && $org) ? 'ovr' : ($loc ? 'loc' : 'org');
            $palette['status'] = $status;
            $palette['class'] = $key;

            $palettes[$key] = $palette;
        }
        $palettes = self::loadColorToPalettes($palettes);

        return $palettes;

    }
    public static function loadColorToPalettes($palettes)
    {
        $allColor = self::loadAllColors();
        $new_pls = array();
        foreach ($palettes as $key_pl =>  $palette) {
            $new_pl = array();
            foreach ($palette as $key => $value) {
                if(!in_array($key, array('title','class','status'))){
                    if(!empty($allColor[$value])){
                        $new_value = $allColor[$value];
                    }elseif(!empty($allColor["color_".$value])){
                         $new_value = $allColor["color_".$value];
                    }else{
                        $new_value = $value;
                    }
                     $new_pl[$key] = $new_value;
                }else{
                    $new_pl[$key] = $value;
                }
            }
            $new_pls[$key_pl] = $new_pl;

        }
        return $new_pls;
    }

    public static function loadAllColors()
    {
        $data_tmp = self::loadTypelistData(self::$temp_Params);
        $customcolors = (array)json_decode(\T4\Helper\Path::getFileContent('etc/customcolors.json'));
        $theme = $data_tmp['theme'];
         $Color_Params = [];
        foreach ($theme as $key => $value) {
            if (preg_match('/^color_([a-z]+)(_|$)/', $key, $match)) {
              $type = 'brand_color';
              $Color_Params[$type][$key] = $value;
            } elseif ($key == 'custom_colors') {
              $type = 'user_color';
              $data = json_decode($value);
              if (empty($data)) {
                  $data = $customcolors;
              }
              foreach ($data as $clsColor => $color_custom) {
                  $Color_Params[$type][$clsColor] = $color_custom->color;
              }
            }
        }
        if(!empty($Color_Params['user_color'])){
            return array_merge($Color_Params['brand_color'],$Color_Params['user_color']);
        }
        return $Color_Params['brand_color'];
    }
    public static function backupFile()
    {
        // backup file data
        $dst = T4PATH_TPL . "/backup";
        if(!is_dir($dst)) \JFolder::create($dst);
        $localFolder = T4PATH_LOCAL . '/etc';
        $allFile = array();
        if(is_dir($localFolder)){
            $localFile = array();
            $allFile['local'] = self::scanDirectories($localFolder,$localFile);
        }
        $baseFolder = T4PATH_TPL . '/etc';
        $basefile = array();
        $allFile['base'] = self::scanDirectories($baseFolder,$basefile);
        foreach ($allFile as $nameFolder => $folders) {
            foreach ($folders as $file) {
                if($nameFolder == 'local'){
                    $file_dst = str_replace(T4PATH_LOCAL, T4PATH_TPL . "/backup/".$nameFolder, $file);
                }else{
                    $file_dst = str_replace(T4PATH_TPL, T4PATH_TPL . "/backup/".$nameFolder, $file);
                }
                if(!is_dir(dirname($file_dst))) \JFolder::create(dirname($file_dst));
                copy($file , $file_dst);
            }
        }
    }
    public static function scanDirectories($rootDir, $allData=array()) {
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..",".DS_Store", ".htaccess", ".htpasswd");
        // run through content of root directory
        $dirContent = scandir($rootDir);
        foreach($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir.'/'.$content;
            if(!in_array($content, $invisibleFileNames)) {
                // if content is file & readable, add to array
                if(is_file($path) && is_readable($path)) {
                    // save file name with path
                    $allData[] = $path;
                // if content is a directory and readable, add path and name
                }elseif(is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    $allData = self::scanDirectories($path, $allData);
                }
            }
        }
        return $allData;
    }
    
}
