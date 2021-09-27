<?php
namespace T4\Document;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry as JRegistry;
use T4\Helper\Css;
use T4\Helper\Col;
use T4\Helper\Path;
use T4\Helper\Layout;
use T4\Helper\Color;
use T4\Helper\Rtl;
use T4\Helper\Asset;
use T4\Helper\Cache as T4Cache;
use Joomla\CMS\Filesystem\File as JFile;

class Template
{
    public $doc;
    public $styles = [];
    public $css_tpl;
    public $bodyClass = [];
    public $layout = '/t4/index';
    public $mode = 'live';

    public function __construct($doc)
    {
        $this->doc = $doc ? $doc : JFactory::getDocument();
        if (version_compare(JVERSION, '4', 'ge')) {
            $this->addAssets();
        } else {
            Asset::init();
            Asset::getWebAssetManager()->enableAsset('t4.bootstrap.js');
            Asset::getWebAssetManager()->enableAsset('jquery-noconflict');
            Asset::getWebAssetManager()->enableAsset('jquery-migrate');
        }
    }


    protected function addAssets()
    {
        // add assets
        if ($this->isHtml()) {
            $wam = JFactory::getDocument()->getWebAssetManager();
            $war = $wam->getRegistry();
            $assetfile = '/etc/assets.j4.json';
            if (is_file(T4PATH_BASE . $assetfile)) {
                $war->addRegistryFile(substr(T4PATH_BASE . $assetfile, strlen(JPATH_ROOT)));
            }
            if (is_file(T4PATH_TPL . $assetfile)) {
                $war->addRegistryFile(substr(T4PATH_TPL . $assetfile, strlen(JPATH_ROOT)));
            }
            if (is_file(T4PATH_LOCAL . $assetfile)) {
                $war->addRegistryFile(substr(T4PATH_LOCAL . $assetfile, strlen(JPATH_ROOT)));
            }
            // enable bootstrap by default
            $wam->useAsset('style', 'bootstrap.css');
            $wam->useAsset('style', 'font.awesome5');
        }
    }

    public static function getInstance($doc)
    {
        static $t4 = null;
        if (!$t4) {
            $app = JFactory::getApplication();
            $input = $app->input;
            $preview = $input->get('t4preview');
            $inedit = \T4\T4::inEdit();
            if ($inedit) {
                // check if error message exists, then using Template to display messae
                foreach ($app->getMessageQueue() as $message) {
                    if ($message['type'] == 'error') {
                        $inedit = false;
                        break;
                    }
                }
            }
            $previewlayout = $input->get('t4previewlayout');
            $class = 'Template';
            if ($preview) {
                $class = 'Preview';
            }
            if ($previewlayout) {
                $t4 = new PreviewLayout($doc);
            } elseif ($preview) {
                $t4 = new Preview($doc);
            } elseif ($inedit) {
                $t4 = new Edit($doc);
            } else {
                $t4 = new Template($doc);
            }
        }
        return $t4;
    }

    public function isHtml()
    {
        $input = JFactory::getApplication()->input;
        $type = $input->get('format', 'html', 'cmd');
        return $type == 'html';
    }

    public function render()
    {

        // Load typelist data
        $this->loadTypelistData();

        $cachekey = $this->getCachekey();
        if ($cachekey) {
            // Get from cache
            $layout = T4Cache::loadLayout($cachekey);
            if ($layout) {
                return $layout;
            }
        }

        // add page class
        // $this->initBodyClass();
        $tmpl = JFactory::getApplication()->input->get('tmpl');
        $tmpl = $tmpl ? '/t4/' . $tmpl : $this->layout;
        $layout = \JLayoutHelper::render($tmpl);

        $caller = $this;
        $doc = $this->doc;
        $params = $this->doc->params;

        if (preg_match_all('/\{t4:([^\}\:]*)\:?([^\}]*)\}/', $layout, $matches)) {
            $arr = [];
            foreach ($matches[0] as $i => $match) {
                $arr[] = [
                    'search' => $match,
                    'name' => $matches[1][$i],
                    'order' => (int)$matches[2][$i]
                ];
            }

            uasort($arr, function ($a, $b) {
                return $a['order'] == $b['order'] ? 0 : ($a['order'] < $b['order'] ? -1 : 1);
            });

            // replace template content
            $search = [];
            $replace = [];
            foreach ($arr as $match) {
                $search[] = $match['search'];
                $name = $match['name'];
                $func = 'get' . ucfirst($name);
                if (method_exists($caller, $func)) {
                    $replace[] = $caller->$func();
                } else {
                    $replace[] = property_exists($doc, $name) ? $doc->$name : $params->get($name, '');
                }
            }

            $layout = str_replace($search, $replace, $layout);
        }

        // render head
        if ($this->isHtml()) {
            $this->renderHead();
        }

        // store cache
        if ($cachekey) {
            T4Cache::storeLayout($cachekey, $layout);
        }

        return $layout;
    }


    protected function postRender($buffer)
    {
        if(!$this->isHtml()){
            return;
        }
        if (preg_match_all('/\{t4post:([^\}\:]*)\}/', $buffer, $matches)) {
            // replace template content
            $search = [];
            $replace = [];
            $caller = $this;
            $doc = $this->doc;
            $params = $this->doc->params;
            foreach ($matches[0] as $i => $match) {
                $search[] = $match;
                $name = $matches[1][$i];
                $func = 'get' . ucfirst($name);

                if (method_exists($caller, $func)) {
                    $replace[] = $caller->$func();
                } else {
                    $replace[] = property_exists($doc, $name) ? $doc->$name : $params->get($name, '');
                }
            }

            $buffer = str_replace($search, $replace, $buffer);
        }
        return $buffer;
    }

    protected function renderHead()
    {
        $this->doc->addScript(Path::findInTheme('js/template.js', true));
        $this->doc->addScript(T4PATH_BASE_URI . '/js/base.js', ['version' => 'auto']);

        // load google fonts
        $this->loadGoogleFonts();

        // load addons
        $addons = (array)$this->doc->params->get('system_addons');

        if (!empty($addons)) {
            $wam = \T4\Helper\Asset::getWebAssetManager();
            foreach ($addons as $asset) {
                if (version_compare(JVERSION, '4', 'ge')) {
                    $wam->useStyle($asset);
                } else {
                    $wam->enableAsset($asset);
                }
            }
        }
    }

    protected function loadGoogleFonts()
    {
        $params = $this->doc->params;
        // Load google/custom font in field with suffix _font_family
        $googleFonts = [];

        // get google fonts name
        $file = T4PATH_ADMIN . "/etc/googlefonts/fonts.json";
        $datas = json_decode(file_get_contents($file), true);
        $themeData = self::getThemeData();

        foreach ($themeData->toArray() as $prop => $val) {
            if (preg_match('/_font_family$/', $prop)) {
                $name = substr($prop, 0, -7); // strip sufix _family
                $family = $themeData->get($prop);
                $weight = $themeData->get($name . '_weight');
                $style = $themeData->get($name . '_style');
                $load_weights = $themeData->get($name . '_load_weights');

                $checkFont = array_search($family, array_column((array)$datas, 'name'));
                if ($checkFont !== false) {
                    $weights = $load_weights ? explode(',', $load_weights) : [];
                    $weights[] = $weight . ($style == 'italic' ? 'i' : '');
                    if ($family) {
                        $googleFonts[$family] = empty($googleFonts[$family]) ? $weights : array_merge($googleFonts[$family], $weights);
                    }
                } else {
                    // try to load from custom font
                    $this->loadCustomFont($family);
                }
            }
        }

        $family = [];
        foreach ($googleFonts as $font => $weights) {
            $weight = implode(',', array_unique($weights));
            $family[] = $font . ($weight == '400' ? '' : ':' . $weight);
        }

        if (count($family)) {
            $checkload = $this->doc->params->get('theme-settings')->get('dont_use_google_font');
            if ($checkload != "1") {
                $this->doc->addStylesheet('https://fonts.googleapis.com/css?family=' . urlencode(implode('|', $family)));
            }
        }
    }

    protected function loadCustomFont($name)
    {
        static $customFonts = null;
        static $loaded = [];
        if ($customFonts === null) {
            $customFonts = json_decode(Path::getFileContent('etc/customfonts.json'), true);
            if (empty($customFonts)) {
                $customFonts = [];
            }
        }

        if (empty($customFonts['fonts'])) {
            return;
        }
        if (!empty($loaded[$name])) {
            return;
        }

        // mark as loaded
        $loaded[$name] = 1;

        if (empty($customFonts['fonts'][$name])) {
            return;
        }

        $font = $customFonts['fonts'][$name];

        if (!empty($font['type']) && $font['type'] == 'css') {
            if (!preg_match("/[^\/.\s+]/", $font['url'])) {
                $font['url'] = '/'.$font['url'];
            }
            // load css file
            $this->doc->addStylesheet($font['url']);
        } else {
            // add css declaration
            $css = '@font-face {';
            $css .= "font-family: '$name';";
            $css .= "src: url('{$font['url']}');";
            $css .= "}";

            $this->doc->addStyleDeclaration($css);
        }
    }


    public function getBody()
    {
        $layout = $this->getLayoutData();
        if (!$layout) {
            return '';
        }

        // render layout
        $sections = [];
        $i = 1;
        foreach ($layout['sections'] as $section) {
            if (!empty($section['name']) && strtolower($section['name']) == 'sections') {
                $section['name'] = 'sections-'.$i;
                $i++;
            }
            $sections[] = $this->renderSection($section);
        }
        $this->renderCustomCss();

        $body = implode("\n", $sections);
        $backToTop = filter_var($this->doc->params->get('site-settings')->get('other_backToTop', ''), FILTER_VALIDATE_BOOLEAN);
        if ($backToTop) {
            $body .= \JText::_('T4_BACK_TO_TOP');
        }

        return $body;
    }

    public function getOffcanvas()
    {
        return \JLayoutHelper::render('t4.layout.offcanvas', $this, T4PATH_BASE . '/html/layouts');
    }
    public function getLogoedit()
    {
        // Load typelist data
        $this->loadTypelistData();
        $doc = $this->doc;
        $conf = JFactory::getConfig();

        $site_settings = $doc->params->get('site-settings');
        $site_name = $site_settings->get('site_name', $conf->get('sitename'));
        $site_slogan = $site_settings->get('site_slogan', '');
        $logo = $site_settings->get('site_logo');
        $logo_small = $site_settings->get('site_logo_small');
        $logo_cls = $logo ? 'logo-image' : 'logo-text';
        $logo_sm_cls = '';
        if ($logo_small){
          $logo_cls .= ' logo-control';
          $logo_sm_cls = ' d-none d-sm-block';
        }
        $return = "";
        if($logo){
            $return = '<img class="logo-img'.$logo_sm_cls.'" src="'. $logo.'" alt="'.strip_tags($site_name).'" />';
        }elseif(!$logo && $site_name || $site_slogan){
            if($site_name) $return = '<span class="site-name'.$logo_sm_cls.'">'.$site_name.'</span>';
            if($site_slogan) $return .= '<span class="site-slogan'.$logo_sm_cls.'">'.$site_slogan.'</span>';
        }else{
            $return = '<div class="brand">T4 Framework</div>';
        }
        return $return;
    }
    public function getBodyclass()
    {
        $this->initBodyClass();
        return implode(' ', $this->bodyClass);
    }
    public function getEditCss(){
        $app = JFactory::getApplication();
        $template = $app->getTemplate();
        if(Path::findInTheme('css/frontend-edit.css',true)){
            $path = Path::findInTheme('css/frontend-edit.css',true);
            return "<link rel='stylesheet' href=". $path ." />";
        }
        return '';
       
    }
    public function addBodyclass($classes)
    {
        foreach (explode(' ', $classes) as $class) {
            if (!in_array($class, $this->bodyClass)) {
                $this->bodyClass[] = $class;
            }
        }
    }

    public function getHead()
    {
        return $this->doc->getBuffer('head');
    }

    protected function initBodyclass()
    {
        // page input
        $input = JFactory::getApplication()->input;
        $this->addBodyclass($input->getCmd('option'));
        $this->addBodyclass('view-'.$input->getCmd('view'));
        $itemId = $input->getCmd('Itemid');
        if ($itemId) {
            $this->addBodyclass('item-' . $itemId);
        }
        $active = JFactory::getApplication()->getMenu()->getActive();
        if (version_compare(JVERSION, '4', 'ge')) {
            $params = JFactory::getApplication()->getMenu()->getParams($active->id);
        } else {
            $params = $active ? $active->params : null;
        }
        if ($active && $params) {
            $page_cls = $params->get('pageclass_sfx');
            if ($page_cls) {
                $this->addBodyclass($page_cls);
            }
        }
    }


    //
    public function beforeRender()
    {
        if (!$this->isHtml()) {
            return;
        }
        // add favicon if set
        $favicon = (!empty($this->doc->params->get('site-settings'))) ? $this->doc->params->get('site-settings')->get('other_faviconFile') : "";
        if ($favicon) {
            // remove current favicon
            foreach ($this->doc->_links as $url => $options) {
                if ($options['relation'] == 'shortcut icon') {
                    unset($this->doc->_links[$url]);
                }
            }
            $this->doc->addFavicon($favicon, 'image/x-icon', 'icon');
        }
    }

    public function afterRender()
    {
        if(!$this->isHtml()) return;
        $app = JFactory::getApplication();
        $buffer = $app->getBody();

        $buffer = $this->postRender($buffer);

        // Remove empty t4 layout container
        $count = 0;
        do {
            $buffer = preg_replace('/<div[^>]*class="t4-[^>]*>\s*<\/div>/mui', '', $buffer, -1, $count);
        } while ($count > 0);

        $app->setBody($buffer);
    }


    public function compileHead()
    {
        if (!$this->isHtml()) {
            return;
        }

        if(!\T4\T4::inEdit()){
            // Load template CSS file
            if ($this->doc->direction == 'rtl') {
                $cssurl = Rtl::render();
                $this->doc->addStylesheet($cssurl);
                $rtl = Path::findInTheme('css/rtl.css', true);
                if ($rtl) {
                    $this->doc->addStylesheet($rtl);
                }
            } else {
                $this->doc->addStylesheet(Path::findInTheme('css/template.css', true));
            }
            // Load custom CSS file
            $template_style_custom_css = T4PATH_MEDIA_URI . '/css/' . $this->getCustomCssFilename();
            $this->doc->addStylesheet($template_style_custom_css);
            if (Path::findInTheme('css/custom.css', true)) {
                $this->doc->addStylesheet(Path::findInTheme('css/custom.css', true)."?r=".rand(0, 1000));
            }
            //load custom layout css
            $tplStyle = self::getStyleId();
            if (Path::findInTheme('css/'.$tplStyle.'-layouts.css', true)) {
                $this->doc->addStylesheet(Path::findInTheme('css/'.$tplStyle.'-layouts.css', true)."?r=".rand(0, 1000));
            }
        }

        $sources = $this->doc->_scripts;

        // only one jquery
        $jquery = false;

        foreach ($sources as $url => $options) {
            if (strpos($url, 'media/jui/js/bootstrap') !== false) {
                unset($sources[$url]);
            }
            if (preg_match('#/jquery(.min)?\.js#', $url)) {
                if ($jquery) {
                    unset($sources[$url]);
                } else {
                    $jquery = true;
                }
            }
            //if (strpos($url, 'media/system/js/mootools') !== false) unset($sources[$url]);
        }
        $this->doc->_scripts = $sources;
    }




    public function renderSection($data)
    {
        static $counter = 1;
        // make id
        $data['id'] = 't4-' . (!empty($data['name']) ? preg_replace('/\s/', '-', strtolower($data['name'])) : 'section-' . $counter++);


        // render custom css for this section
        $style = $this->sectionCustomStyles($data);
        if (!empty($style)) {
            $this->styles[] = "\n/* Section: {$data['name']} */\n" . $style;
        } else {
            // reset id if not set
            if (empty($data['name'])) {
                $data['id'] = '';
            }
        }

        // render row content
        $content = $this->renderRow($data);

        return LayoutHelper::render('t4.layout.section', ['data' => $data, 'content' => $content], T4PATH_BASE . '/html/layouts');
    }

    protected function sectionCustomStyles($data)
    {
        //if (!empty($data['overlay_type']) && $data['overlay_type'] != 'image') return '';

        if (!$this->css_tpl) {
            $this->css_tpl = Path::getFileContent('css/tpl/section.tpl.css');
        }
        // fix data for stiky
        if (!empty($data['sticky'])) {
            $data['sticky_position'] = 'sticky';
            $data['webkit_sticky_position'] = '-webkit-sticky !important';
            $data['sticky_zindex'] = 10;
        }
        // fix data for opacity
        if (!empty($data['opacity'])) {
            $op = floatval($data['opacity']);
            if ($op > 1) {
                $op = $op / 100;
            }
            $data['overlay_opacity'] = round($op, 2);
        }
        $css = Css::render($this->css_tpl, $data);
        return $css;
    }

    public function renderRow(&$data)
    {
        if (empty($data['contents']) || !is_array($data['contents'])) {
            return '';
        }
        $cols = $data['contents'];

        // render cols
        $contents = [];

        // hidden class to row
        $hiddencls = Col::getHiddenRowCls($cols);
        if ($hiddencls) {
            $data['extra_class'] = empty($data['extra_class']) ? $hiddencls : $data['extra_class'] . ' ' . $hiddencls;
        }

        foreach ($cols as $col) {
            if (empty($col) || empty($col['type']) || $col['type'] == 'none') {
                unset($cols[$idx]);
                continue;
            }

            // render column content
            $content = $this->renderContent($col);

            // col class
            $cls = [];
            if (!empty($col['name']) && $col['name'] != 'none') {
                $cls[] = preg_replace('/\s/', '-', strtolower($col['name']));
            }

            // class for col width

            $defaultscreen = 'md';
            $_cls = !empty($col['xs']) && Col::addCls('xs', $col['xs'], $cls);
            $_cls = !empty($col['sm']) && Col::addCls('sm', $col['sm'], $cls);
            //$_cls = Col::addCls('sm', $col, $cls);
            if (!$_cls) {
                // auto col start from sm screen
                if ($col['type'] != 'component') {
                    Col::addCls('sm', 'auto', $cls);
                }
            }
            $_cls = !empty($col['md']) && Col::addCls('md', $col['md'], $cls);
            if ($_cls) {
                $defaultscreen = 'lg';
            }
            $_cls = !empty($col['lg']) && Col::addCls('lg', $col['lg'], $cls);
            if ($_cls && $defaultscreen == 'lg') {
                $defaultscreen = 'xl';
            }
            $_cls = !empty($col['xl']) && Col::addCls('xl', $col['xl'], $cls);
            if ($_cls && $defaultscreen == 'xl') {
                $defaultscreen = null;
            }

            if ($defaultscreen) {
                if ($col['type'] == 'component' || empty($col['col']) || $col['col'] == 'auto') {
                    Col::addCls($defaultscreen, 'auto', $cls);
                } elseif (is_numeric($col['col'])) {
                    Col::addCls($defaultscreen, $col['col'], $cls);
                }
            }

            // hidden class
            Col::addHiddenCls($col, $cls);

            if (!empty($col['extra_class'])) {
                $cls[] = $col['extra_class'];
            }

            // order component first
            $content = ['content' => $content, 'cls' => implode(' ', $cls), 'data' => $col];
            if ($col['type'] == 'component') {
                foreach ($contents as $i => $c) {
                    $contents[$i]['cls'] .= ' order-md-first';
                }
                array_unshift($contents, $content);
            } else {
                $contents[] = $content;
            }
        }

        $html = '';
        
        if (count($contents) == 1 && (!is_numeric($contents[0]['data']['col']) || $contents[0]['data']['col'] == '12')) {
            $html = $this->renderSingleCol($contents[0]);
        } else {
            foreach ($contents as $col) {
                $html .= $this->renderCol($col) . "\n";
            }
            $html = "<div class=\"t4-row row\">\n$html</div>\n";
        }


        // if (count($contents) > 1) {
        //  foreach ($contents as $col) $html .= $this->renderCol($col) . "\n";
        //  $html = "<div class=\"t4-row row\">\n$html</div>\n";
        // } else if (count($contents) == 1) {
        //  $html = $this->renderSingleCol($contents[0]);
        // }
        return trim($html);
    }


    public function renderCol($col)
    {
        return "<div class=\"t4-col {$col['cls']}\">\n{$col['content']}\n</div>";
    }

    public function renderSingleCol($col)
    {
        return !empty($col['data']['extra_class']) && !empty($col['content']) ? "<div class=\"t4-col {$col['data']['extra_class']}\">{$col['content']}</div>" : $col['content'];
        ;
    }

    public function renderContent($data)
    {
        if (empty($data['type'])) {
            return false;
        }
        switch ($data['type']) {
            case 'row':
                return $this->renderRow($data);
            case 'component':
                return $this->renderComponent($data);
            case 'module':
                $data['jdoc'] = 'module';
                break;
            case 'positions':
                $data['jdoc'] = 'modules';
                break;
            case 'block':
                return $this->renderBlock($data);
            case 'element':
                return $this->renderElement($data);
            case 'spacer':
                return $this->renderSpacer($data);
        }
        return $this->renderJdoc($data);
    }

    public function renderJdoc($data)
    {
        if (empty($data['jdoc'])) {
            return '';
        }
        $name = ($data['jdoc'] == 'module') ? (isset($data['modname']) ? $data['modname'] : $data['name']) : $data['name'] ;
        // if ($data['jdoc'] == 'modules' && JFactory::getDocument()->countModules($data['name']) == 0) {
        //     return "";
        // }

        $html = "<jdoc:include type=\"{$data['jdoc']}\" name=\"{$name}\" ";
        if (!empty($data['style'])) {
            $html .= 'style="' . $data['style'] . '" ';
        }
        if (!empty($data['title'])) {
            $html .= 'title="' . $data['title'] . '" ';
        }
        if (!empty($data['block'])) {
            $html .= 'block="' . $data['block'] . '" ';
        }
        $html .= '/>';
        return $html;
    }

    public function renderComponent($data)
    {
        return '<jdoc:include type="message" /><jdoc:include type="component" />';
    }

    public function renderSpacer($data)
    {
        return '<meta name="spacer"/>';
    }

    public function renderElement($data)
    {
        // extra params
        $extra_params = '';
        if (!empty($data['extra_params']) && preg_match('/^([\w-]*\s*=\s*"[^"]*"\s*)+$/', $data['extra_params'])) {
            $extra_params = $data['extra_params'];
            if (!preg_match('/title\s*=/', $extra_params)) {
                $extra_params .= ' title="' . md5($extra_params) . '"';
            }
            $extra_params = ' ' . trim($extra_params);
        }
        return "<jdoc:include type=\"element\" name=\"{$data['name']}\"<?php echo $extra_params ?> />";
    }

    public function renderBlock($data)
    {
        $file = Path::findInTheme('block/' . $data['name'] . '.html');
        if (!$file) {
            $file = Path::findT4Layout('block/' . $data['name'] . '.html');
        }
        if ($file) {
            return file_get_contents($file);
        }

        return '';
    }

    public function renderCustomCss()
    {
        $css = '';

        $color = Color::getInstance($this->getThemeData());

        // add theme customize
        $params = $color->getParams();
        
        $rootcss = Css::renderRoot($params);
        //render lighten color
        $cssLighten = Css::renderLighten($rootcss,$params);
        $css .= "\n" . $cssLighten;
        
        // render color pattern styles
        $css_tpl = Path::getFileContent('css/tpl/pattern.tpl.css');
        foreach ($color->getPalettes() as $palette) {
            $css .= "\n/* Pattern: {$palette['title']} */\n" . Css::render($css_tpl, $palette) . "\n";
        }
        // Section specific
        $css .= implode("\n", $this->styles);

        // fix for megamenu duration
        $navigation_settings = $this->doc->params->get('navigation-settings');

        $params->set('animation_duration', $navigation_settings->get('mega_duration', 400) . 'ms');
        $css_tpl = Path::getFileContent('css/tpl/theme.tpl.css');
        $siteCss = Css::render($css_tpl, $params);
        $css .= "\n" . $siteCss;

        //$css_tpl = Path::getFileContent('css/tpl/style.tpl.css');
        //$rootcss = Css::render($css_tpl, $params);
        //$css .= "\n" . $rootcss;

        // theme
        // $themecss = Path::getFileContent('css/theme.css');
        // if ($this->mode == 'live') $themecss = Css::renderTheme($themecss, $rootcss, $params);

        // $css .= "\n\n" . $themecss;

        // to css file
        $file = T4PATH_MEDIA . '/css/' . $this->getCustomCssFilename();
        \JFile::write($file, $css);

        return;
    }


    protected function loadTypelistData()
    {
        $groups = ['site', 'navigation', 'theme', 'layout'];
        // end check;
        foreach ($groups as $group) {
            $profile = $this->doc->params->get('typelist-' . $group);
            // check overwrite profile for sub layout
            if ($group == 'layout' && Layout::isSubpage()) {
                $_profile = $this->doc->params->get('sub-layout');
                if ($_profile) {
                    $profile = $_profile;
                }
            }

            $content = Path::getFileContent('etc/' . $group . '/' . $profile . '.json');
            if (!$content) {
                $profile = 'default';
                $content = Path::getFileContent('etc/' . $group . '/' . $profile . '.json');
            }
            $this->doc->params->set($group . '-settings', new JRegistry(json_decode($content, true)));

            // add body class
            $this->addBodyclass($group . '-' . str_replace(' ', '-', strtolower($profile)));
        }
        // Load global params
        \T4\Helper\TemplateStyle::loadGlobalParams($this->doc);
    }

    protected function getLayoutData()
    {
        $layout = (array)$this->doc->params->get('layout-settings')->get('layout');
        if (empty($layout) || empty($layout['sections'])) {
            return null;
        }
        return $layout;
    }


    protected function getThemeData()
    {
        $theme = $this->doc->params->get('theme-settings');
        $site = $this->doc->params->get('site-settings');
        // merge site and theme data
        $data = new JRegistry(array_merge($theme->toArray(),$site->toArray()));
        // custom color
        $cc = $data->get('custom_colors');

        if (Path::getBaseContent('etc/customcolors.json') && is_string(Path::getBaseContent('etc/customcolors.json'))) {
            // user color
            $colors = [];
            $basecolors = (array)json_decode(Path::getBaseContent('etc/customcolors.json'), true);
            $customcolors = (array)json_decode(Path::getLocalContent('etc/customcolors.json'), true);
            foreach ($basecolors as $name => $color) {
                if (empty($customcolors[$name])) {
                    $customcolors[$name] = $color;
                }
            }

            $vals = @json_decode($cc, true);
            foreach ($customcolors as $name => $color) {
                $value = (!empty($vals[$name]) && !empty($vals[$name]['color'])) ? $vals[$name]['color'] : $color['color'];
                $colors[strtolower($name)] = $value;
            }

            $data->set('custom_colors', $colors);
        }

        return $data;
    }


    protected function getCustomCssFilename()
    {
        return $this->getStyleId() . (Layout::isSubpage() ? '-sub' : '') . '.css';
    }

    protected function getCachekey()
    {
        $tmpl = JFactory::getApplication()->input->get('tmpl');
        return 'style-' . $this->getStyleId() . (Layout::isSubpage() ? '-sub' : '') . ($tmpl ? '-' . $tmpl : '');
    }

    // return template style id if found, otherwise return Itemid
    protected function getStyleId()
    {
        $app = \JFactory::getApplication('site');
        // get from template object
        $template = $app->getTemplate(true);
        if (!empty($template->id)) {
            return $template->id;
        }

        // get template style id from menu
        $active = $app->getMenu()->getActive();
        if ($active && $active->template_style_id != '') {
            return $active->template_style_id;
        }

        // failback, get itemid
        return $app->input->getInt('Itemid');
    }
}
