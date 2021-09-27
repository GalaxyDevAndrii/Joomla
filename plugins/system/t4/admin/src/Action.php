<?php
namespace T4Admin;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\Filesystem\Folder as JFolder;
use Joomla\Filesystem\File as JFile;

class Action
{
    public static function run()
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_templates')) {
            throw new \JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        if (!Admin::isT4Template()) {
            throw new \JAccessExceptionNotallowed(JText::_('T4_ERROR_NOT_T4_TEMPLATE'), 500);
        }

        $action = JFactory::getApplication()->input->getCmd('t4do');
        $action = trim(ucfirst($action));
        if ($action) {
            $class = '\\T4Admin\\Action\\' . $action;
            if (!class_exists($class)) {
                $class = '\\T4Admin\\Action';
                $func = 'do' . $action;
            } else {
                $task = JFactory::getApplication()->input->getCmd('task');
                $func = 'do' . trim(ucfirst($task));
            }
            
            if (method_exists($class, $func)) {
                self::doExit($class::$func());
            }
        }
        self::doExit(['error' => "Action not found [$action]!"]);
    }

    public static function doExit($result)
    {
        if (!is_array($result)) {
            $result = ['data' => $result];
        }
        // return as json
        header('Content-type: application/json');
        echo json_encode($result);
        exit();
    }

    // ACTIONS
    public static function doSaveLayout()
    {
        $key = Draft::store('t4layout');
        return ["ok" => 1, "key" => $key];
    }
    // ACTIONS
    public static function doSaveMegamenu()
    {
        $key = Draft::store('navigation_mega_settings');
        return ["ok" => 1, "key" => $key];
    }

    // Export current template style configuration
    //
    public static function doExport()
    {
        $groups = JFactory::getApplication()->input->getString('groups');

        $tpl = Admin::getTemplate(true);

        // export only selected groups
        $output = $tpl->params;
        if ($groups) {
            $params = json_decode($tpl->params, true);

            $agroups = explode(',', $groups);
            $selparams = [];
            foreach ($agroups as $group) {
                foreach ($params as $name => $value) {
                    if ($name == $group || strpos($name, $group . '_') === 0) {
                        $selparams[$name] = $value;
                    }
                }
            }
            $output = json_encode($selparams);
        }

        // Force the download
        $filename = 'template-style-' . $tpl->id . ($groups ? '-' .$groups : '') . '-' . date('Ymd') . '.json';

        header('Content-Description: Export Template Configuration');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        //header("Content-Length: " . strlen($tpl->params));
        //header("Content-Type: application/octet-stream;");
        header("Content-Type: application/json;");
        echo $output;
        exit;
    }

    // Get current template custom css
    public static function doGetcss()
    {
        $file = \T4\Helper\Path::findInTheme('css/custom.css');
        $css = $file ? file_get_contents($file) : '';
        echo $css;
        exit;
    }

    // Save custom css to current template
    public static function doSavecss()
    {
        $css = JFactory::getApplication()->input->post->getRaw('css');
        
        if ($css !== null) {
            $path = T4PATH_LOCAL . '/css/';
            if (!is_dir($path)) {
                JFolder::create($path);
            }
            JFile::write($path . 'custom.css', $css);
            echo 'ok';
        } else {
            echo 'error';
        }

        exit;
    }

    // Get current template custom block
    public static function doGetblock()
    {
        $blName = JFactory::getApplication()->input->post->getRaw('block');
        $file = \T4\Helper\Path::findInTheme('block/'.$blName.'.html');
        if (!$file) {
            $file = \T4\Helper\Path::findInTheme('html/layouts/t4/block/'.$blName.'.html');
        }
        $fileLocal = \T4\Helper\Path::getLocalContent('html/layouts/t4/block/'.$blName.'.html');
        $fileBase = \T4\Helper\Path::getBaseContent('html/layouts/t4/block/'.$blName.'.html');
        if (!empty($fileBase) && !empty($fileLocal)) {
            $status = 'ovr';
        } elseif (!empty($fileLocal)) {
            $status = 'loc';
        } elseif (!empty($fileBase)) {
            $status = 'org';
        }
        $block = $file ? file_get_contents($file) : '';
        return ['pos'=>$status,'data'=>$block];
    }
    // Save custom block to current template
    public static function doSaveblock()
    {
        $name = JFactory::getApplication()->input->post->getVar('name');
        $blockData = JFactory::getApplication()->input->post->getRaw('data');
        
        if ($blockData !== null) {
            $path = T4PATH_LOCAL . '/html/layouts/t4/block/';
            if (!is_dir($path)) {
                JFolder::create($path);
            }
            JFile::write($path . $name.'.html', $blockData);
            $output = ['ok'=> 1];
        } else {
            $output = ['error'=> 'T4_SAVE_BLOCK_ERROR'];
        }
        return $output;
    }

    // Save custom block to current template
    public static function doRemoveblock()
    {
        $name = JFactory::getApplication()->input->post->getVar('block');
        if ($name !== null) {
            $file = T4PATH_LOCAL . '/html/layouts/t4/block/' . $name .'.html';
            if (is_file($file)) {
                unlink($file);
            }
            $file = \T4\Helper\Path::findInTheme('html/layouts/t4/block/'.$name.'.html');
            $block = $file ? file_get_contents($file) : '';
            return ['ok'=>1,'data'=>$block];
        } else {
            echo 'error';
        }
    }

    // get preset
    public static function doGetPreset()
    {
        $name = JFactory::getApplication()->input->get('name');
        $file = \T4\Helper\Path::findInTheme('etc/presets/' . $name . '.json');
        header("Content-Type: application/json;");

        echo $file ? file_get_contents($file) : '{}';
        exit;
    }

    // ADDONS actions
    public static function doAddAddon()
    {
        $data = JFactory::getApplication()->input->post->getVar('asset');
        $asset = $data['asset'];
        $name = $asset['name'];
        $action = $data['action'];
        $output = [
            'ok' => 1,
            'action' => $action,
            'asset' => $asset
        ];
        if ($action == 'update') {
            return self::doUpdateAddon($data);
        }
        // current addon
        $assetsfile = T4PATH_LOCAL . '/etc/assets.json';
        $assets = is_file($assetsfile) ? json_decode(file_get_contents($assetsfile), true) : ['name' => 'Local Assets'];

        if (empty($assets['assets'])) {
            $assets['assets'] = [];
        }
        
        if (!isset($assets['assets'][$name])) {
            $assets['assets'][$name] = $asset;

            // write to file
            if (!is_dir(dirname($assetsfile))) {
                JFolder::create(dirname($assetsfile));
            }
            if (!JFile::write($assetsfile, json_encode($assets))) {
                $output = ['error' => JText::_('T4_ADDONS_SAVE_ERROR')];
            }
        } else {
            $output = ['error' => JText::_('T4_ADDONS_SAVE_DUPLICATED_ERROR')];
        }


        header("Content-Type: application/json;");
        echo json_encode($output);
        exit;
    }
    // ADDONS actions update
    public static function doUpdateAddon($data)
    {
        $oldname = $data['oldname'];
        $action = $data['action'];
        $asset = $data['asset'];
        $name = $asset['name'];
        $output = [
            'ok' => 1,
            'action' => $action,
            'asset' => $asset
        ];
        
        // current addon
        $assetsfile = T4PATH_LOCAL . '/etc/assets.json';
        $assets = is_file($assetsfile) ? json_decode(file_get_contents($assetsfile), true) : ['name' => 'Local Assets'];
        if (empty($assets['assets'])) {
            $assets['assets'] = [];
        }
        if (isset($assets['assets'][$oldname])) {
            unset($assets['assets'][$oldname]);
        }
        $assets['assets'][$name] = $asset;
        // write to file
        if (!is_dir(dirname($assetsfile))) {
            JFolder::create(dirname($assetsfile));
        }
        if (!JFile::write($assetsfile, json_encode($assets))) {
            $output = ['error' => JText::_('T4_ADDONS_SAVE_ERROR')];
        }
        return $output;
    }

    public static function doRemoveAddon()
    {
        $name = JFactory::getApplication()->input->post->getVar('name');
        $assetsfile = T4PATH_LOCAL . '/etc/assets.json';
        $assets = is_file($assetsfile) ? json_decode(file_get_contents($assetsfile), true) : [];
        if (isset($assets['assets'][$name])) {
            unset($assets['assets'][$name]);
            // write to file
            if (!JFile::write($assetsfile, json_encode($assets))) {
                $output = ['error' => JText::_('T4_ADDONS_DELETE_ERROR')];
            } else {
                $output = ['ok' => 1];
            }
        } else {
            $output = ['error' => JText::_('T4_ADDONS_DELETE_NOTFOUND_ERROR')];
        }

        header("Content-Type: application/json;");
        echo json_encode($output);
        exit;
    }

    public static function scanDirectories($rootDir, $allData=array())
    {
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
        // run through content of root directory
        $dirContent = scandir($rootDir);
        foreach ($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir.'/'.$content;
            if (!in_array($content, $invisibleFileNames)) {
                // if content is file & readable, add to array
                if (is_file($path) && is_readable($path)) {
                    // save file name with path
                    $allData[] = $path;
                // if content is a directory and readable, add path and name
                } elseif (is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    $allData = self::scanDirectories($path, $allData);
                }
            }
        }
        return $allData;
    }

    public static function findInLocal($file)
    {
        $path = T4PATH_LOCAL . '/' . $file;
        if (is_file($path)) {
            return $path;
        }

        return null;
    }
    public static function mkdir_r($dirName, $rights=0777){
        $dirs = explode('/', $dirName);
        $dir='';
        foreach ($dirs as $part) {
            $dir.=$part.'/';
            if (!is_dir($dir) && strlen($dir)>0)
                mkdir($dir, $rights);
        }
    }
}
