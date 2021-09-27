<?php
namespace T4Admin;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Session\Session;
use T4\Helper\Path;
use Joomla\CMS\Filesystem\Folder as JFolder;
class T4menutype {

	public static function onAfterGetMenuTypeOptions(&$list,$model) 
	{
        $lang = JFactory::getLanguage();
        $components = self::getAllComponents();
        
        if(empty($components)) return;

        foreach ($components as $component)
        {
            $options = self::getTypeOptionsFromMvc($component->option);

            if ($options)
            {
                $list[$component->name] = array_merge($list[$component->name],$options);

                // Create the reverse lookup for link-to-name.
                foreach ($options as $option)
                {
                    if (isset($option->request))
                    {
                        $model->addReverseLookupUrl($option);

                        if (isset($option->request['option']))
                        {
                            $componentLanguageFolder = JPATH_ADMINISTRATOR . 'components/' . $option->request['option'];
                            $lang->load($option->request['option'] . '.sys', JPATH_ADMINISTRATOR, null, false, true)
                                ||  $lang->load($option->request['option'] . '.sys', $componentLanguageFolder, null, false, true);
                        }
                    }
                }
            }
        }
	}
    public static function getAllComponents(){
        $template = self::getTemplate();

        if (!Admin::isT4Template($template->template)) {
            return;
        }
        // Get the list of components.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('name, element AS ' . $db->quoteName('option'))
            ->from('#__extensions')
            ->where('type = ' . $db->quote('component'))
            ->where('enabled = 1')
            ->order('name ASC');
        $db->setQuery($query);
        $components = $db->loadObjectList();

        if(!defined('T4PATH_BASE_HTML')){
            define('T4PATH_BASE_HTML',T4PATH_BASE . '/html/');
        }
        foreach ($components as $component) {
            if(is_dir(T4PATH_BASE_HTML . $component->option)){
                $options[] = $component;
            }
        }
        return $options;
    }

    /**
     * Get menu types from MVC
     *
     * @param   string  $component  Component option like in URLs
     *
     * @return  array|boolean
     *
     * @since   1.6
     */
    public static function getTypeOptionsFromMvc($component)
    {
        $options = array();

        // Get the views for this component.
        if (is_dir(T4PATH_BASE_HTML . $component))
        {
            $views = JFolder::folders(T4PATH_BASE_HTML . $component);
        }
        $path = '';
        foreach ($views as $view)
        {
            $options = array_merge($options, (array) self::getTypeOptionsFromLayouts($component, $view));
        }
        return $options;
    }
    /**
     * Get the menu types from component layouts
     *
     * @param   string  $component  Component option as in URLs
     * @param   string  $view       Name of the view
     *
     * @return  array
     *
     * @since   1.6
     */
    public static function getTypeOptionsFromLayouts($component, $view)
    {
        $options     = array();
        $layouts     = array();
        $layoutNames = array();
        $lang        = JFactory::getLanguage();
        $path        = T4PATH_BASE_HTML . $component.'/'.$view;

        // Get the views for this component.
        if (is_dir($path))
        {
            $layouts = array_merge($layouts, JFolder::files($path, '.xml$', false, true));
        }
        if (empty($layouts))
        {
            return $options;
        }

        // Process the found layouts.
        foreach ($layouts as $layout)
        {
            // Ignore private layouts.
            if (strpos(basename($layout), '_') === false)
            {
                $file = $layout;

                // Get the layout name.
                $layout = basename($layout, '.xml');

                // Create the menu option for the layout.
                $o = new \JObject;
                $o->title       = ucfirst($layout);
                $o->description = '';
                $o->request     = array('option' => $component, 'view' => $view);

                // Only add the layout request argument if not the default layout.
                if ($layout != 'default')
                {
                    $o->request['layout'] = $layout;
                }

                // Load layout metadata if it exists.
                if (is_file($file))
                {
                    // Attempt to load the xml file.
                    if ($xml = simplexml_load_file($file))
                    {
                        // Look for the first view node off of the root node.
                        if ($menu = $xml->xpath('layout[1]'))
                        {
                            $menu = $menu[0];

                            // If the view is hidden from the menu, discard it and move on to the next view.
                            if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
                            {
                                unset($xml);
                                unset($o);
                                continue;
                            }

                            // Populate the title and description if they exist.
                            if (!empty($menu['title']))
                            {
                                $o->title = trim((string) $menu['title']);
                            }

                            if (!empty($menu->message[0]))
                            {
                                $o->description = trim((string) $menu->message[0]);
                            }
                        }
                    }
                }

                // Add the layout to the options array.
                $options[] = $o;
            }
        }
        return $options;
    }
    public static function getTemplate()
    {
        
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from($db->quoteName('#__template_styles'));
        $query->where($db->quoteName('client_id') . ' = 0');
        $query->where($db->quoteName('home') . ' = ' . $db->quote('1'));

        $db->setQuery($query);

        $tpl = $db->loadObject();

        return $tpl;
    }
}