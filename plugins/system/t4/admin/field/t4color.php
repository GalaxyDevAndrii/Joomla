<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;
use T4\Helper\T4Compatible as T4compatible;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\Form as JForm;
use T4Admin\T4form AS T4form;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldT4color extends JFormField
{
    /**
     * The field type.
     *
     * @var        string
     */
    protected $type = 't4color';
    protected $layout = 'field.colors';

    protected function getInput()
    {
        $data = [];

        $data['colors'] = $this->loadColors();
        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['value'] = $this->getValues();
        $data['class'] = $this->class;

        return \JLayoutHelper::render($this->layout, $data, T4PATH_ADMIN . '/layouts');
    }

    protected function loadColors()
    {
        $template = T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/default.json');
        }

        $datas = @json_decode($t4Theme);

        // $datas = T4compatible::t4Color();

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

    protected function getValues()
    {
        $template = T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/default.json');
        }

        $data = @json_decode($t4Theme);
        // $data = T4compatible::t4Color();

        return $data->{$this->fieldname};
    }
    
    protected function compareUserColor()
    {
        $customcolors = $this->getCustomColors();
        $template = T4Admin\Admin::getTemplate(true);
        $temp_Params = new Registry($template->params);
        $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/' . $temp_Params->get('typelist-theme') . '.json');
        if (!$t4Theme) {
            $t4Theme = \T4\Helper\Path::getFileContent('etc/theme/default.json');
        }

        $data = @json_decode($t4Theme);
       if(property_exists($data, 'custom_colors')){
             $themeCustomColors = (array)json_decode($data->custom_colors);
            //convert custom color to user color 
            $userColor = array_merge($customcolors, $themeCustomColors);
        }else{
             $userColor = $customcolors;
        }
       
        $convertUserColor = array();
        $i = 1;
        foreach ($userColor as $customName => $customColor) {
            $colorData = new \stdClass();
            $colorData->name = "user_color_".$i;
            $colorData->color = $customColor->color;
            $convertUserColor[$customName] = $colorData;
            $i++;

        }
        return $convertUserColor;
    }
    protected function getCustomColors(){
        // get base custom colors
        $baseUsercolors = (array) json_decode(T4\Helper\Path::getFileContent('etc/customcolors.json', false), true);
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
}
