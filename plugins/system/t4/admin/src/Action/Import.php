<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Language\Text as JText;
use T4Admin\Admin as Admin;
use Joomla\Filesystem\Folder as JFolder;
use Joomla\Filesystem\File as JFile;
use Joomla\CMS\Installer\InstallerHelper as JInstallerHelper;
use T4Admin\Action;
use T4Admin\Action\Blockcss;

class Import
{
    public static $params;
    public static $tpl;
    public static $tmp_path;

    protected static function init()
    {
        self::$tmp_path = JFactory::getConfig()->get('tmp_path');
        self::$tpl = Admin::getTemplate(true);
        self::$params = json_decode(self::$tpl->params, true);
    }
    public static function doImport()
    {
        self::init();
        $pk = self::_getPackageFromUpload();
        $dataImport = array();
        if (!isset($pk['dir'])) {
            return ["error" => "Package upload is error"];
        }
        $dir = $pk['dir'];
        
        $tpl_params = glob($dir."/*.json");

        if (!isset($tpl_params)) {
            return ["error" => "Package is invalid"];
        }

        $dataContent = json_decode(file_get_contents($tpl_params[0]), true);
        if (empty($dataContent)) {
            return ["error"=> "template param has empty!"];
        }
        
        $allFile = array();
        $allFile = Action::scanDirectories($dir, $allFile);
        $dataImport['setting'] = array_values(array_diff(array_merge(scandir($dir .'/etc/'), array('system','other')), array('..', '.',basename($tpl_params[0]))));
        ;

        $dataImport['params'] = $dataContent;
        $dataImport['dir'] = $dir;

        return $dataImport;
    }
    public static function _getPackageFromUpload()
    {
        $lang = JFactory::getLanguage();
        $extension = 'com_installer';
        $base_dir = JPATH_SITE;
        $language_tag = 'en-GB';
        $reload = true;
        $lang->load($extension, $base_dir, $language_tag, $reload);
        // Get the uploaded file information.
        $input    = JFactory::getApplication()->input;

        // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
        $userfile = $input->files->get('package', null, 'raw');

        // Make sure that file uploads are enabled in php.
        if (!(bool) ini_get('file_uploads')) {
            return ['error' => JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE')];
        }

        // Make sure that zlib is loaded so that the package can be unpacked.
        if (!extension_loaded('zlib')) {
            return ['error' => JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB')];
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile)) {
            return ['error' => JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED')];
        }

        // Is the PHP tmp directory missing?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR)) {
            return ['error' => JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSET')];
        }

        // Is the max upload size too small in php.ini?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE)) {
            return ['error' => JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZE')];
        }

        // Check if there was a different problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1) {
            return ['error' => JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR')];
        }

        // Build the appropriate paths.
        $config   = JFactory::getConfig();
        $tmp_dest = $config->get('tmp_path') . '/' . $userfile['name'];
        $tmp_src  = $userfile['tmp_name'];

        // Move uploaded file.
        JFile::upload($tmp_src, $tmp_dest, false, true);

        // Unpack the downloaded package file.
        $package = JInstallerHelper::unpack($tmp_dest, true);

        return $package;
    }
    public static function doImporting()
    {
        self::init();
        $dataError = array();
        $input = JFactory::getApplication()->input->post;
        $tplStyle = self::$tpl->id;
        $groups = $input->getVar('groups');
        $data = $input->getVar('data');
        $import_path = $data['dir'] . '/etc/';
        $block_path = $data['dir'];
        foreach ($groups as $group) {
            $folder_name = str_replace('typelist-', '', $group);
            if (is_dir($import_path . '/' .$folder_name)) {
                $files = glob($import_path . '/' .$folder_name . '/*.json');
                $file = $files[0];
                $dest_file = str_replace($block_path, T4PATH_LOCAL, $file);
                if (!is_dir(dirname($dest_file))) {
                    Action::mkdir_r(dirname($dest_file));
                }

                if (!JFile::copy($file, $dest_file)) {
                    $dataError[] = basename($file) . "has copy fail";
                }
            }
        }
        if (in_array('typelist-layout', $groups)) {
            if (is_dir($block_path . '/scss')) {
                $allCssFile = array();
                $allCssFile = Action::scanDirectories($block_path . '/scss', $allCssFile);
                foreach ($allCssFile as $cssFile) {
                    $dest_css = str_replace($block_path, T4PATH_LOCAL, $cssFile);
                    $dir_css = dirname($dest_css);
                    $basename_css_arr = explode('-', basename($dest_css));
                    $basename_css_arr[0] = $tplStyle;
                    $basename_css = implode('-', $basename_css_arr);
                    
                    if (!is_dir($dir_css)) {
                        Action::mkdir_r($dir_css);
                    }
                    if (!JFile::copy($cssFile, $dir_css . '/'.$basename_css)) {
                        $dataError[] = basename($dest_css) . " has copy fail";
                    }
                }
                $result_css = Blockcss::renderCss();
                if (isset($result_css['error'])) {
                    $dataError[] = $result_css['error'];
                }
            }
            if (is_dir($block_path . '/html')) {
                $allBlockFile = array();
                $allBlockFile = Action::scanDirectories($block_path . '/html', $allBlockFile);
                foreach ($allBlockFile as $BlockFile) {
                    $dest_block = str_replace($block_path, T4PATH_LOCAL, $BlockFile);
                    if (!is_dir(dirname($dest_block))) {
                        Action::mkdir_r(dirname($dest_block));
                    }
                    if (!JFile::copy($BlockFile, $dest_block)) {
                        $dataError[] = basename($dest_block) . " has copy fail";
                    }
                }
            }
        }
        return ['data' => $data,'groups'=> $groups, 'error'=>$dataError];
    }
    public static function doTest()
    {
        self::init();
        $dataError = array();
        $dir = "/Users/kienduong/works/joom/t4_dev/tmp/install_5f856cd069ed8";
        $groups = array( "typelist-layout", "typelist-navigation", "typelist-site", "typelist-theme", "system", "other");
        $data = json_decode(file_get_contents($dir.'/template-style-9-20201013.json'), true);
        $import_path = $dir . '/etc/';
        $block_path = $dir;
        foreach ($groups as $group) {
            $folder_name = str_replace('typelist-', '', $group);
            if (is_dir($import_path . '/' .$folder_name)) {
                $files = glob($import_path . '/' .$folder_name . '/*.json');
                $file = $files[0];
                if (!is_dir(dirname(T4PATH_LOCAL .'/etc/' .$folder_name))) {
                    Action::mkdir_r(dirname(T4PATH_LOCAL .'/etc/' .$folder_name));
                }
                if (!JFile::copy($file, str_replace($import_path, T4PATH_LOCAL . '/etc/', $file))) {
                    $dataError[] = basename($file) . "has copy fail";
                }
                if ($folder_name == 'layout') {
                    if (is_dir($block_path . '/scss')) {
                    }
                }
            }
        }
        if (in_array('typelist-layout', $groups)) {
            if (is_dir($block_path . '/scss')) {
                $allCssFile = array();
                $allCssFile = Action::scanDirectories($block_path . '/scss', $allCssFile);
                foreach ($allCssFile as $cssFile) {
                    $dest_css = str_replace($block_path, T4PATH_LOCAL, $cssFile);
                    if (!is_dir(dirname($dest_css))) {
                        Action::mkdir_r(dirname($dest_css));
                    }
                    if (!JFile::copy($cssFile, $dest_css)) {
                        $dataError[] = basename($dest_css) . " has copy fail";
                    }
                }
            }
            if (is_dir($block_path . '/html')) {
                $allBlockFile = array();
                $allBlockFile = Action::scanDirectories($block_path . '/html', $allBlockFile);
                foreach ($allBlockFile as $BlockFile) {
                    $dest_block = str_replace($block_path, T4PATH_LOCAL, $BlockFile);
                    if (!is_dir(dirname($dest_block))) {
                        Action::mkdir_r(dirname($dest_block));
                    }
                    if (!JFile::copy($BlockFile, $dest_block)) {
                        $dataError[] = basename($dest_block) . " has copy fail";
                    }
                }
            }
        }
    }
}
