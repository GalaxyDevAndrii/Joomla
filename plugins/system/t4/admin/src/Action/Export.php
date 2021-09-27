<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use \Joomla\CMS\Exception\ExceptionHandler as Exception;
use \Joomla\CMS\Language\Text as JText;
use T4Admin\Admin as Admin;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Archive\Zip;
use T4Admin\Action;
use T4\Helper\Path as T4Path;

class Export
{
    public static $params;
    public static $tpl;
    public static $tmp_path;
    public static $zip;
    public static $dest;
    protected static function init()
    {
        self::$tmp_path = JFactory::getConfig()->get('tmp_path');
        self::$tpl = Admin::getTemplate(true);
        self::$params = json_decode(self::$tpl->params, true);
        self::$dest = self::$tmp_path . '/t4_blank' . '_' . date('Ymdhms');
        if (!is_dir(self::$dest)) {
            Folder::create(self::$dest);
        }
    }
    public static function doExport()
    {
        self::init();
        $input = JFactory::getApplication()->input->post;
        $groups = $input->getVar('groups');
        if (!$groups) {
            $groups = 'typelist-site,typelist-navigation,typelist-theme,typelist-layout,system,other';
        }

        //Delete previously exported files before exporting
        $all_exported = array_values(array_diff(scandir(self::$tmp_path), array('..', '.')));
        if (count($all_exported)) {
            foreach ($all_exported as $item) {
                if (strstr($item, 't4_blank')) {
                    if (is_dir(self::$tmp_path . '/'. $item)) {
                        Folder::delete(self::$tmp_path . '/'. $item);
                    }
                    if (is_file(self::$tmp_path . '/'. $item)) {
                        File::delete(self::$tmp_path . '/'. $item);
                    }
                }
            }
        }

        $folder_ex = self::$dest;
        // export only selected groups
        $agroups = explode(',', $groups);
        foreach ($agroups as $group) {
            $name = str_replace('typelist-', '', $group);
            if (in_array($name, array('system','other'))) {
                continue;
            }
            $conf_file = T4Path::findInTheme('etc/'.$name.'/'.self::$params['typelist-'.$name] .'.json');
            if (isset($conf_file)) {

                $dir_json = dirname($folder_ex .'/etc/'.$name.'/'.self::$params['typelist-'.$name] .'.json');
                if (!is_dir($dir_json)) Folder::create($dir_json);
                File::copy($conf_file, $folder_ex .'/etc/'.$name.'/'.self::$params['typelist-'.$name] .'.json');
            }
        }
        if(in_array('Typelist-layout', $agroups)){
            //check block in layout
            $blocks = self::findBlock();
            $tplStyle = self::$tpl->id;
            if(count($blocks)){
                foreach ($blocks as $blCss => $blFile) {
                    $scss = T4Path::findInTheme('scss/layouts/'.$tplStyle.'-'.strtolower($blCss) . '.scss');
                    if($scss) {
                        $dir_css = dirname(self::$dest . '/scss/layouts/'.$tplStyle.'-'.strtolower($blCss) . '.scss');
                        if (!is_dir($dir_css)) Folder::create($dir_css);
                        File::copy($scss,self::$dest . '/scss/layouts/'.$tplStyle.'-'.strtolower($blCss) . '.scss' );
                    }
                     $dir_block = dirname(self::$dest . '/html/layouts/t4/block/'.basename($blFile) );
                    if (!is_dir($dir_block)) Folder::create($dir_block);
                    File::copy($blFile,self::$dest . '/html/layouts/t4/block/'.basename($blFile) );
                }
            }
        }
        // Force the download
        $filename = 'template-style-' . self::$tpl->id . '-' . date('Ymd') . '.json';
        $filenameZip = $folder_ex . '.zip';
        File::write($folder_ex . '/' . $filename, self::$tpl->params);
        $blank = array();
        $result = self::zipper($folder_ex, $blank, $filenameZip, true);
        if (file_exists($filenameZip)) {
            $fileDL = JUri::root(true) ."/tmp" . str_replace(self::$tmp_path, '', $filenameZip);
            return $fileDL;
            exit;
        } else {
            return [ 'error', 'Don\'t create file!' ];
        }
    }
    public static function zipper($directory, $ignore = array(), $the_file = '', $overwrite = false)
    {

        // Prevents overwriting an existing archive if overwrite is false.
        if (file_exists($the_file) && !$overwrite) {
            return false;
        }
            
        $files = array();
        
        // Loop through the directory and get the files to include in the zip.
        if (is_dir($directory)) {
            $files = Action::scanDirectories($directory, $files);
        } else {
            throw new Exception(JText::_('COM_T4PAGEBUILDER_EXPORT_ERROR_MESSAGE_NOT_FOUND'), 404);
            return false;
        }

        // If there are any files within the directory, we can create the zip.
        if (count($files) > 0) {
            require_once T4PATH . '/vendor/autoload.php';
            require_once T4PATH . '/vendor/nelexa/zip/src/ZipFile.php';

            $zipper =  new \PhpZip\ZipFile();
            // Add each file to the archive.
            foreach ($files as $file) {
                $zipper->addFile($file, str_replace($directory, "", $file));
            }
            $zipper->saveAsFile($the_file);
            $zipper->close();
            
            // Return confirmation that it now exists!
            return file_exists($the_file);
        } else {
            throw new Exception(JText::_('COM_T4PAGEBUILDER_EXPORT_ERROR_MESSAGE_NOT_FOUND'), 404);
            return false;
        }
    }
    public static function findCustomFont()
    {
        $name = 'site';
        $name_file = self::$params['typelist-'.$name];
        $path_site_config = Action::findInLocal('/etc/'.$name.'/'.$name_file .'.json');
        if (!$path_site_config) {
            return null;
        }
        $site_data = file_get_contents($path_site_config);
        var_dump($site_data);
        die;
        return $site_data;
    }
    public static function findBlock(){
        self::init();
        $file_layout = T4Path::findInTheme('etc/layout/'.self::$params['typelist-layout'] .'.json');
        $datas = json_decode(file_get_contents($file_layout),true);
        $sections = $datas['layout']['sections'];
        $data_block = array();
        foreach ($sections as $section) {
            foreach ($section['contents'] as $content) {
                if($content['type'] == 'block'){
                    $block_file = T4Path::findInTheme('html/layouts/t4/block/'.$content['name'].'.html');
                    if($block_file){
                        $data_block[$section['name']] = $block_file;
                    }
                }
            }
            
        }
        return $data_block;
    }
    public static function doTest(){

        self::init();
        $file_layout = T4Path::findInTheme('etc/theme/'.self::$params['typelist-theme'] .'.json');
        $datas = json_decode(file_get_contents($file_layout),true);
        // $sections = $datas['layout']['sections'];
        echo '<pre>: '. print_r( $datas, true ) .'</pre>';die;
        $data_block = array();
        foreach ($sections as $section) {
            foreach ($section['contents'] as $content) {
                if($content['type'] == 'block'){
                    $block_file = T4Path::findInTheme('html/layouts/t4/block/'.$content['name'].'.html');
                    if($block_file){
                        $data_block[$section['name']] = $block_file;
                    }
                }
            }
            
        }
        return $data_block;
    }
    public static function doTestcp(){

        self::init();
        $blocks = self::findBlock();
        $tplStyle = self::$tpl->id;
        foreach ($blocks as $blCss => $blFile) {
            $scss = T4Path::findInTheme('scss/layouts/'.$tplStyle.'-'.strtolower($blCss) . '.scss');
            if($scss) {
                $dir_css = dirname(self::$dest . '/scss/layouts/'.$tplStyle.'-'.strtolower($blCss) . '.scss');
                if (!is_dir($dir_css)) Folder::create($dir_css);
                File::copy($scss,self::$dest . '/scss/layouts/'.$tplStyle.'-'.strtolower($blCss) . '.scss' );
            }
            $dir_block = dirname(self::$dest . '/html/layouts/t4/block/'.basename($blFile) );
            if (!is_dir($dir_block)) Folder::create($dir_block);
            File::copy($blFile,self::$dest . '/html/layouts/t4/block/'.basename($blFile) );
        }
        echo "done";
        exit();
    }
}
