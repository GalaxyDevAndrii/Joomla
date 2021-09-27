<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;

class Asset {


    public static function getWebAssetManager() {
        static $wam = null;
        if ($wam === null) {
            $doc = JFactory::getApplication()->getDocument();
            if (!$doc) {
                $doc = JFactory::getDocument();
            }

            if (method_exists($doc, 'getWebAssetManager')) {
                $wam = $doc->getWebAssetManager();
            } else {
                // joomla 3, register WebAsset
                \JLoader::registerNamespace('Joomla\CMS\WebAsset', T4PATH . '/src/joomla3/src/WebAsset', false, false, 'psr4');
                $wam = new \Joomla\CMS\WebAsset\WebAssetManager();

                // add core registry
                $coreasset = '/etc/assets.core.json';
                \T4\Helper\Asset::addAssets(T4PATH_BASE . $coreasset);
                \T4\Helper\Asset::addAssets(T4PATH_TPL . $coreasset);

            }
        }
        return $wam;
    }

    public static function init() {
        $assetfile = '/etc/assets.json';
        self::addAssets(T4PATH_BASE . $assetfile);
        self::addAssets(T4PATH_TPL . $assetfile);
        self::addAssets(T4PATH_LOCAL . $assetfile);
    }

    public static function addAssets($file) {

        //static $i = 1;
        //if ($i++ == 10) {debug_print_backtrace(-1);die;}
        if (!is_file($file)) return;
        $assets = json_decode(file_get_contents($file), true);

        $wam = self::getWebAssetManager();
        $war = $wam->getRegistry();

        foreach ($assets['assets'] as $name => $asset) {
            // update url
            if (!empty($asset['js'])) {
                foreach ($asset['js'] as $i => $uri) {
                    $_uri = Path::findInTheme($uri, true, true);
                    if ($_uri) $asset['js'][$i] = $_uri;
                }
            }
            if (!empty($asset['css'])) {
                foreach ($asset['css'] as $i => $uri) {
                    $_uri = Path::findInTheme($uri, true, true);
                    if ($_uri) $asset['css'][$i] = $_uri;
                }
            }
            if(version_compare(JVERSION, '4', "ge")){
                $asset = new \Joomla\CMS\WebAsset\WebAssetItem($asset['name'], $asset['uri'], ['type' => $asset['type']]);
                // add
                $war->add($asset['name'],$asset);
            }else{
                 // add
                $war->add($war->createAsset($name,$asset));
            }
           
        }
    }
}
