<?php
namespace T4Admin;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Uri\Uri as JUri;
use T4\Helper\Path;
use T4\Helper\Css;
class Admin
{
    public static $template = null;

    public static function init($form, $data)
    {
        if (!self::isT4Template()) {
            return;
        }

        // Define T4 const
        $template = self::getTemplate();

        // upgrade compare new version
        T4Compatible::run($data);
        // Load T4 template params
        Params::load($form, $data);

        // set back template params
        if (!empty($data->params)) {
            self::$template->params = json_encode($data->params);
        }

        // clean draft data
        Draft::clear();

        $doc = JFactory::getDocument();
        //create jsLangs
        $langs = array(
            't4save'				=> JText::_("JAPPLY"),
            'customCssSaved'				=> JText::_("T4_CUSTOM_CSS_HAS_SAVED"),
            'patternDelConfirm' => JText::_('TPL_T4_PATTERN_CONFIRM'),
            'OverRideConfirm' => JText::_('T4_OVERRIDE_CONFIRM'),
            'RemoveColConfirm' => JText::_('TPL_T4_COL_REMOVE_CONFIRM'),
            'logoPresent' => JText::_('T4_LAYOUT_LOGO_TEXT'),
            'emptyLayoutPosition' => JText::_('T4_LAYOUT_EMPTY_POSITION'),
            'defaultLayoutPosition' => JText::_('T4_LAYOUT_DEFAULT_POSITION'),

            'layoutConfig' => JText::_('T4_LAYOUT_CONFIG_TITLE'),
            'layoutConfigDesc' => JText::_('T4_LAYOUT_CONFIG_DESC'),
            'layoutUnknownWidth' => JText::_('T4_LAYOUT_UNKN_WIDTH'),
            'layoutPosWidth' => JText::_('T4_LAYOUT_POS_WIDTH'),
            'layoutPosName' => JText::_('T4_LAYOUT_POS_NAME'),

            'layoutCanNotLoad' => JText::_('T4_LAYOUT_LOAD_ERROR'),

            'askCloneLayout' => JText::_('T4_LAYOUT_ASK_ADD_LAYOUT'),
            'correctLayoutName' => JText::_('T4_LAYOUT_ASK_CORRECT_NAME'),
            'askDeleteLayout' => JText::_('T4_LAYOUT_ASK_DEL_LAYOUT'),
            'askDeleteLayoutDesc' => JText::_('T4_LAYOUT_ASK_DEL_LAYOUT_DESC'),
            'askPurgeLayout' => JText::_('T4_LAYOUT_ASK_DEL_LAYOUT'),
            'askPurgeLayoutDesc' => JText::_('T4_LAYOUT_ASK_PURGE_LAYOUT_DESC'),

            'lblDeleteIt' => JText::_('T4_LAYOUT_LABEL_DELETEIT'),
            'lblCloneIt' => JText::_('T4_LAYOUT_LABEL_CLONEIT'),

            'layoutEditPosition' => JText::_('T4_LAYOUT_EDIT_POSITION'),
            'layoutShowPosition' => JText::_('T4_LAYOUT_SHOW_POSITION'),
            'layoutHidePosition' => JText::_('T4_LAYOUT_HIDE_POSITION'),
            'layoutChangeNumpos' => JText::_('T4_LAYOUT_CHANGE_NUMPOS'),
            'layoutDragResize' => JText::_('T4_LAYOUT_DRAG_RESIZE'),
            'layoutHiddenposDesc' => JText::_('T4_LAYOUT_HIDDEN_POS_DESC'),

            'updateFailedGetList' => JText::_('T4_OVERVIEW_FAILED_GETLIST'),
            'updateDownLatest' => JText::_('T4_OVERVIEW_GO_DOWNLOAD'),
            'updateCheckUpdate' => JText::_('T4_OVERVIEW_CHECK_UPDATE'),
            'updateChkComplete' => JText::_('T4_OVERVIEW_CHK_UPDATE_OK'),
            'updateHasNew' => JText::_('T4_OVERVIEW_TPL_NEW'),
            'updateCompare' => JText::_('T4_OVERVIEW_TPL_COMPARE'),
            'switchResponsiveMode' => JText::_('T4_MSG_SWITCH_RESPONSIVE_MODE'),

            'toolImportDataDone' => JText::_('T4_TOOL_IMPORT_DONE'),
            'toolExportNoSelectedGroupsWarning' => JText::_('T4_TOOL_EXPORT_NO_SELECTED_GROUPS_WARNING'),
            'toolImportDataFileError' => JText::_('T4_TOOL_IMPORT_DATA_FILE_ERROR'),
            'toolImportDataFileEmptyWarning' => JText::_('T4_TOOL_IMPORT_DATA_FILE_EMPTY_WARNING'),

            'addonEmptyFieldWaring' => JText::_('T4_ADDONS_EMPTY_FIELD_WARNING'),
            'addonEmptyFieldCssOrJSWaring' => JText::_('T4_ADDONS_EMPTY_CSS_OR_JS_FIELD_WARNING'),
            'addonRemoveConfirm' => JText::_('T4_ADDONS_REMOVE_CONFIRM'),
            'addonRemoveDeleted' => JText::_('T4_ADDONS_DELETED'),
            'addonNameDuplicated' => JText::_('T4_ADDONS_SAVE_DUPLICATED_ERROR'),
            'fontsEmptyFieldCssWaring' => JText::_('T4_CUSTOM_FONT_CSS_MISSED'),
            'fontEmptyFieldFontFileWaring' => JText::_('T4_CUSTOM_FONT_FILE_MISSED'),
            'customColorRemoveConfirm' => JText::_('T4_CUSTOM_COLOR_CONFIRM'),
            'customColordaplicateWaring' => JText::_('T4_CUSTOM_COLOR_DUPLICATED_ERROR'),
            'colorNameNoneWarning' => JText::_('T4_CUSTOM_COLOR_NAME_NONE_ERROR'),
            'colorEmptyFieldWaring' => JText::_('T4_CUSTOM_COLOR_COLOR_FIELD_ERROR'),
            'colorNameEmptyFieldWaring' => JText::_('T4_CUSTOM_COLOR_NAME_FIELD_ERROR'),
            'customColorHasSaved' => JText::_('T4_CUSTOM_COLOR_HAS_SAVED'),
            'customColorRemoveConfirm' => JText::_('T4_CUSTOM_COLOR_CONFIRM_REMOVE'),
            'customColorDeleted' => JText::_('T4_CUSTOM_COLOR_HAS_DELETED'),
            'userColorConfirmEditLabel' => JText::_('T4_CUSTOM_COLOR_CONFIRM_EDIT'),
            'palettesUpdated' => JText::_('T4_COLOR_PALETTES_UPDATED'),
            'typelistConfirmEditlayout' => JText::_('T4_TYPELIST_CONFIRM_EDIT_LAYOUT'),
            'typelistConfirmEdittheme' => JText::_('T4_TYPELIST_CONFIRM_EDIT_THEME'),
            'typelistConfirmEditnavigation' => JText::_('T4_TYPELIST_CONFIRM_EDIT_NAVIGATION'),
            'typelistConfirmEditsite' => JText::_('T4_TYPELIST_CONFIRM_EDIT_SITE'),
            'typelistconfirmlayoutDelete' => JText::_('T4_TYPELIST_CONFIRM_DELETE_LAYOUT'),
            'typelistconfirmlayoutRestore' => JText::_('T4_TYPELIST_CONFIRM_RESTORE_LAYOUT'),
            'typelistconfirmthemeDelete' => JText::_('T4_TYPELIST_CONFIRM_DELETE_THEME'),
            'typelistconfirmthemeRestore' => JText::_('T4_TYPELIST_CONFIRM_RESTORE_THEME'),
            'typelistconfirmnavigationDelete' => JText::_('T4_TYPELIST_CONFIRM_DELETE_NAVIGATION'),
            'typelistconfirmnavigationRestore' => JText::_('T4_TYPELIST_CONFIRM_RESTORE_NAVIGATION'),
            'typelistconfirmsiteDelete' => JText::_('T4_TYPELIST_CONFIRM_DELETE_SITE'),
            'typelistconfirmsiteRestore' => JText::_('T4_TYPELIST_CONFIRM_RESTORE_SITE'),
            'typelistconfirmlayoutDeleted' => JText::_('T4_TYPELIST_CONFIRM_DELETED_LAYOUT'),
            'typelistconfirmlayoutRestored' => JText::_('T4_TYPELIST_CONFIRM_RESTORED_LAYOUT'),
            'typelistconfirmthemeDeleted' => JText::_('T4_TYPELIST_CONFIRM_DELETED_THEME'),
            'typelistconfirmthemeRestored' => JText::_('T4_TYPELIST_CONFIRM_RESTORED_THEME'),
            'typelistconfirmnavigationDeleted' => JText::_('T4_TYPELIST_CONFIRM_DELETED_NAVIGATION'),
            'typelistconfirmnavigationRestored' => JText::_('T4_TYPELIST_CONFIRM_RESTORED_NAVIGATION'),
            'typelistconfirmsiteDeleted' => JText::_('T4_TYPELIST_CONFIRM_DELETED_SITE'),
            'typelistconfirmsiteRestored' => JText::_('T4_TYPELIST_CONFIRM_RESTORED_SITE'),
            'megamenuExtraClass' => JText::_('T4_NAVIGATION_MEGA_EXTRA_CLASS'),
            'megamenuSubmenuWidth' => JText::_('T4_NAVIGATION_SUB_MENU_WIDTH'),
            'megamenuAlignment' => JText::_('T4_NAVIGATION_ALIGNMENT'),
            'megamenuSectionSelectItems' => JText::_('T4_NAVIGATION_MEGA_BUILD_SELECT_ITEMS'),
            'megamenuSectionAllItems' => JText::_('T4_NAVIGATION_MEGA_BUILD_ALL_ITEMS'),
            'colorPalettesConfirmRestore' => JText::_('T4_LAYOUT_PALETTES_CONFIRM_RESTORE'),
            'colorPalettesRestore' => JText::_('T4_LAYOUT_PALETTES_RESTORE'),
            'colorPalettesConfirmDelete' => JText::_('T4_LAYOUT_PALETTES_CONFIRM_DEL'),
            'colorPalettesDelete' => JText::_('T4_LAYOUT_PALETTES_DEL'),
            'butonCloseConfirm' => JText::_('T4_BTN_CLOSE_CONFIRM'),
            't4LayoutRowConfirmDel' => JText::_('T4_LAYOUT_CONFIRM_ROW_DEL'),
            'typelistItemDeleted' => JText::_('T4_TYPE_LIST_DELETED'),
            'typelistCloneSaved' => JText::_('T4_TYPE_LIST_CLONE_SAVE'),
            'palettesRemnoveClone' => JText::_('T4_PALETTES_REMOVE_CLONE'),
            't4LayoutRowDeleted' => JText::_('T4_LAYOUT_ROW_DELETED'),
            'T4BlockNameNone' => JText::_('T4_LAYOUT_BLOCK_NAME_NONE'),
            'T4LayoutSaveBlock' => JText::_('T4_LAYOUT_BLOCK_HAS_SAVED'),
            'T4AddonsHasUpdated' => JText::_('T4_ADDONS_HAS_UPDATED'),
            'T4AddonsHasAdded' => JText::_('T4_ADDONS_HAS_ADDED'),
            'T4fontCustomAdded' => JText::_('T4_CUSTOM_FONT_HAS_ADDED'),
            'T4fontCustomRemoveConfirm' => JText::_('T4_CUSTOM_FONT_CONFIRM_REMOVE'),
            'T4fontCustomRemoved' => JText::_('T4_CUSTOM_FONT_HAS_REMOVED'),
            'T4TypeListSaved' => JText::_('T4_TYPE_LIST_SAVED'),
            'T4loadGoogleFontConfirm' => JText::_('T4_DONT_LOAD_GOOGLE_FONT_CONFIRM'),
            'ExportDataSuccessfuly' => JText::_('T4_TOOL_EXPORT_SUCCESS'),
        );
        // Add loading class when rendering admin layout
        $script = "document.documentElement.classList.add('t4admin-loading');\n";
        $script .= "window.addEventListener('load', function() {setTimeout(function(){document.documentElement.classList.remove('t4admin-loading')}, 1000)})";
        $script .= "; var T4Admin = window.T4Admin || {}; ";
        $script .= " T4Admin.langs = ". json_encode($langs) . "; ";
        $script .= " T4Admin.t4devmode = " . JFactory::getConfig()->get('devmode') ? 1 : 0 . "; ";
        $doc->addScriptDeclaration($script);

        // Init js
        $assets_uri = T4PATH_ADMIN_URI . '/assets';
        $doc->addStyleSheet($assets_uri . '/css/dark_theme.css');
        $doc->addStyleSheet($assets_uri . '/css/t4-code.css');
        $doc->addStyleSheet($assets_uri . '/css/t4-ie.css', array('version' => 'auto', 'relative' => true));//, 'conditional' => 'IE'
        //$doc->addStyleSheet($assets_uri . '/css/animate.css');
        if (\T4\Helper\J3J4::major() < 4) {
            $pathCodeMirrorCore = "editors";
        } else {
            $pathCodeMirrorCore = "vendor";
        }
        $doc->addStyleSheet(JUri::root(true) . '/media/'.$pathCodeMirrorCore.'/codemirror/lib/codemirror.css');
        $doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/lib/codemirror.js');
        $doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/mode/css/css.js');
        $doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/mode/xml/xml.js');
        $doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/mode/htmlmixed/htmlmixed.js');
        if (version_compare(JVERSION, '4', 'ge')) {
            $wam = JFactory::getDocument()->getWebAssetManager();
            $wam->useStyle('chosen')
                ->useScript('chosen')
                ->useStyle('minicolors')
                ->useScript('minicolors')
                ->useScript('jquery-migrate');
        } else {
            // enable jquery.ui
            $wam = \T4\Helper\Asset::getWebAssetManager();
            $wam->enableAsset('minicolors');
            $wam->enableAsset('chosen');
            $wam->enableAsset('jquery-migrate');
        }
        $doc->addScript($assets_uri . '/js/jquery-ui.min.js');
        $doc->addScript($assets_uri . '/js/overwrite-settings.js');
        // Preview
        $doc->addScript($assets_uri . '/js/preview.js', ['version' => 'auto']);
        $cssRoot = Css::renderRoot($data) . Path::getFileContent('css/tpl/theme.tpl.css');
        $previewjs = "var cssTplStyle = " . json_encode($cssRoot) . ";";
        $previewjs .= "var cssTplPalette = " . json_encode(Path::getFileContent('css/tpl/pattern.tpl.css')) . ";";
        $doc->addScriptDeclaration($previewjs);
    }

    public static function initj3()
    {
        if (\T4\Helper\J3J4::major() < 4) {
            \JLoader::registerNamespace('Joomla\CMS', T4PATH . '/src/joomla3/src', false, true, 'psr4');
        }
    }


    public static function isT4Template($template = null)
    {
        if (!$template) {
            $template = self::getTemplate();
        }
        if ($template) {
            // parse xml
            $filePath = JPATH_ROOT . '/templates/' . $template . '/templateDetails.xml';

            if (!is_file($filePath)) {
                return false;
            }
            $xml = simplexml_load_file($filePath);
            // check t4
            $base = isset($xml->t4) && isset($xml->t4->basetheme) ? trim(strtolower($xml->t4->basetheme)) : null;

            // not an T4 template, ignore
            if (!$base) {
                return false;
            }

            // validate base
            $path = T4PATH_THEMES . '/' . $base;

            if (!is_dir($path)) {
                return false;
            }

            // define const
            if (!defined('T4PATH_BASE')) {
                define('T4PATH_BASE', $path);
                define('T4PATH_BASE_URI', T4PATH_THEMES_URI . '/' . $base);
            }

            return true;
        }

        return false;
    }

    public static function getTemplate($params = false)
    {
        if (self::$template === '') {
            return null;
        }

        if (self::$template === null) {
            $id = JFactory::getApplication()->input->getInt('id');
            $db = JFactory::getDbo();

            $query = $db->getQuery(true);
            $query->select(array('*'));
            $query->from($db->quoteName('#__template_styles'));
            $query->where($db->quoteName('client_id') . ' = 0');
            $query->where($db->quoteName('id') . ' = ' . $db->quote($id));

            $db->setQuery($query);

            $tpl = $db->loadObject();
            if (!$tpl || !self::isT4Template($tpl->template)) {
                self::$template = '';
                return null;
            }

            self::$template = $tpl;

            // define template const
            $tpl_path = '/templates/' . $tpl->template;
            define('T4PATH_TPL', JPATH_ROOT . $tpl_path);
            define('T4PATH_TPL_URI', JUri::root(true) . $tpl_path);
            // define local const
            define('T4PATH_LOCAL', T4PATH_TPL . '/local');
            define('T4PATH_LOCAL_URI', T4PATH_TPL_URI . '/local');
        }
        return $params ? self::$template : self::$template->template;
    }
    protected static function initT4AdminJs($path)
    {
        $doc = JFactory::getDocument();
    }
}
