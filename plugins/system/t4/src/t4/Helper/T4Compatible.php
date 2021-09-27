<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry;
use Joomla\CMS\Form\Form as JForm;
use T4Admin\T4form AS T4form;
use Joomla\CMS\Language\Text as JText;
use Joomla\Filesystem\Folder as JFolder;
use Joomla\Filesystem\File as JFile;
class T4Compatible {

	// custom Font actions
    public static function t4Color() {
        $template = \T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $t4Theme = Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = Path::getFileContent('etc/theme/default.json');
        }
        $datas = @json_decode($t4Theme);
        $initThemelayout = false;
        $xmlfile = Path::findInTheme('params/typelist-theme.xml');
        $forms = JForm::getInstance('typelist-theme', $xmlfile);
        $form = new T4form($forms);
        if ($xmlfile) $form->loadFile($xmlfile);
        //load the template
        $tplXml = T4PATH_TPL . '/templateDetails.xml';
        //overwrite / extend with params of templateDetails
        $form->loadFile($tplXml, true, '//theme');
        $group_theme_colors = $form->getFieldsets();
        foreach ($group_theme_colors as $group_name => $group_data) {
            $group_fields = $form->getFieldset($group_name);
            foreach ($group_fields as $fields) {
                if(in_array($fields->getAttribute('type'),array('t4color','t4brand'))){
                    
                   if(empty($datas->{$fields->getAttribute('name')})){
                    $initThemelayout = true;
                    $datas->{$fields->getAttribute('name')} = $fields->getAttribute('default');
                   }
                }
            }
        }
        $customcolors =  self::getCustomColors();
        $oldCustomColor = array();

        if(property_exists($datas, 'custom_colors')){
             $themeCustomColors = (array)json_decode($datas->custom_colors);
            //convert custom color to user color 
            $userColor = array_merge($customcolors, $themeCustomColors);
            $i = 1;
            foreach ($userColor as $customName => $customColor) {
                $oldCustomColor[$customName] = "user_color_".$i;
                $datas->{"user_color_".$i} = $customColor->color;
                $i++;

            }
        }
        if($initThemelayout && !empty($datas->custom_colors)){
            self::saveThemeLayout($datas);
        }
        
        //migrate custom color to usercolor

        foreach ($datas as $data_name =>  $data_val) {
           if(preg_match("/_color/i", $data_name)){
            if(!empty($oldCustomColor[str_replace(" ", "_", $data_val)])){
                $datas->{$data_name} = str_replace("_", " ", $oldCustomColor[str_replace(" ", "_", $data_val)]);
            }
           }
        }

        return $datas;
    }
    public static function loadColors()
    {
        $template = \T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $t4Theme = Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = Path::getFileContent('etc/theme/default.json');
        }

        $datas = @json_decode($t4Theme);
        $datas = self::t4Color();
        $Color_Params = [];
        foreach ($datas as $key => $value) {
            if (preg_match('/^color_([a-z]+)(_|$)/', $key, $match)) {
                $type = 'brand_color';
            } elseif (preg_match('/^user_color_(.*)(_|$)/', $key, $match)) {
                $type = 'user_color';
            }elseif (preg_match('/(.*)_color/', $key, $match)){
                $type = "template_color";
            }else{
                $type = "";
            }

            if($type) {
                if(empty($Color_Params[$type])) $Color_Params[$type] = array();
                $Color_Params[$type][$key] = $value;
            }
        }
        return $Color_Params;
    }
    public static function paletteColor($fields)
    {
        $allColor = self::loadColors();
        // get base palettes
        $basepalettes = (array) json_decode(Path::getFileContent('etc/palettes.json', false), true);
        // local palettes
        $file = T4PATH_LOCAL . '/etc/palettes.json';
        $userpalettes = is_file($file) ? (array) json_decode(file_get_contents($file), true) : [];
        if(!empty($userpalettes)){
            $dataPalettes = array_merge(array_keys($userpalettes),array_keys($basepalettes));
        }else{
            $dataPalettes = array_keys($basepalettes);
        }
        $keys = array_unique($dataPalettes);
        $palettes = [];
        $fields[] = ['name' => 'title'];
        foreach ($keys as $key) {
            $base = !empty($basepalettes[$key]) ? $basepalettes[$key] : [];
            $local = !empty($userpalettes[$key]) ? $userpalettes[$key] : [];
            $status = '';
            $palette = [];
            $ovr = $loc = $org = false;
            foreach ($fields as $field) {
                $value = '';
                $name = $field['name'];
				if (!empty($base[$name]) && !empty($local[$name]) && $base[$name] != $local[$name]) {
					$value = $local[$name];
					$ovr = true;
				} else if (!empty($base[$name])) {
					$value = $base[$name];
					$org = true;
				} else if (!empty($local[$name])) {
					$value = $local[$name];
					$loc = true;
				}
                foreach ($allColor as $group) {
                    if(array_key_exists($value,  $group)){
                        $value = $group[$value];
                    }elseif (array_key_exists("color_".$value,  $group)) {
                        $value = $group["color_".$value];
                    }
                }

                if(!$value) $value = $field['value'];

                $palette[$name] = $value;
            }
            $status = $ovr || ($loc && $org) ? 'ovr' : ($loc ? 'loc' : 'org');
            $palette['status'] = $status;
            $palette['class'] = $key;

            $palettes[$key] = $palette;
        }
        return $palettes;
    }

    public static function saveThemeLayout($data)
    {
        $template = \T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $typeName = $temp_Params->get('typelist-theme') ? $temp_Params->get('typelist-theme') : "default";
        $data = json_encode($data);
        self::backupFile($temp_Params);

        //save to local
        $file = T4PATH_LOCAL . '/etc/theme/' .$typeName . '.json';
        $dir = dirname($file);
        if (!is_dir($dir)) \JFolder::create($dir);
        \JFile::write($file, $data);
        //save to base
        $fileBase = T4PATH_TPL . '/etc/theme/' .$typeName . '.json';
        $dir = dirname($fileBase);
        if (!is_dir($dir)) \JFolder::create($dir);
        \JFile::write($fileBase, $data);
    }
    public static function getCustomColors(){
        // get base custom colors
        $baseUsercolors = (array) json_decode(Path::getFileContent('etc/customcolors.json', false), true);
        // local custom colors
        $file = T4PATH_LOCAL . '/etc/customcolors.json';
        $customColors = is_file($file) ? (array) json_decode(file_get_contents($file), true) : [];

        $keys = array_unique(array_merge(array_keys($customColors), array_keys($baseUsercolors)));
        $userColors = [];
        foreach ($keys as $key) {
            $base = !empty($baseUsercolors[$key]) ? $baseUsercolors[$key] : [];
            $local = !empty($customColors[$key]) ? $customColors[$key] : [];
            $status = '';
            $ovr = $loc = $org = false;
            if (!empty($base) && !empty($local) && ($base['color'] != $local['color'] || $base['name'] != $local['name'])) {
                $value = $local;
                $ovr = true;
            } else if (!empty($base)) {
                $value = $base;
                $org = true;
            } else if (!empty($local)) {
                $value = $local;
                $loc = true;
            }
            $status = $ovr || ($loc && $org) ? 'ovr' : ($loc ? 'loc' : 'org');
            $value['status'] = $status;
            $userColors[$key] = (object)$value;
        }
        return $userColors;
    }
    public static function backupFile($temp_Params)
    {
    	// backup file data
        $dst = T4PATH_TPL . "/etc/backup";
        if(!is_dir($dst)) \JFolder::create($dst);
        $fielArr = array('site','theme');
        foreach ($fielArr as $file) {
            $FileData = T4PATH_LOCAL . 'etc/'.$file.'/' . $temp_Params->get('typelist-'.$file) . '.json';
            if(file_exists($FileData)){
                if(!is_dir($dst .'/local/'.$file)) \JFolder::create($dst .'/local/'.$file);
                $FileData_cp = $dst .'/local/'.$file . '/'.basename($FileData,'.json') . '.json';
                copy($FileData,$FileData_cp);
            }
            $FileDataBase = Path::findInBase('etc/'.$file.'/' . $temp_Params->get('typelist-'.$file) . '.json');
            if(!is_dir($dst .'/base/'.$file)) \JFolder::create($dst .'/base/'.$file);
            $FileDataBase_cp = $dst .'/base/'.$file .'/'. basename($FileDataBase,'.json') . '.json';
            // echo '<pre>: '. print_r( $FileDataBase_cp, true ) .'</pre>';die;
            copy($FileDataBase,$FileDataBase_cp);
        }
    }
}