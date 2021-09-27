<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldCustomstylepreview extends JFormField
{
    /**
     * The field type.
     *
     * @var     string
     */
    protected $type = 'CustomStylePreview';
    protected function getInput()
    {
        // get first page assign to current template style
        $db = JFactory::getDbo();
        $id = JFactory::getApplication()->input->getInt('id');

        // get template style info
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from( $db->quoteName('#__template_styles') );
        $query->where( $db->quoteName('client_id') . ' = 0' );
        $query->where( $db->quoteName('id') . ' = ' . $db->quote($id) );
        $db->setQuery($query);
        $tpl = $db->loadObject();

        if ($tpl->home) {
            // get first default page
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from( $db->quoteName('#__menu') );
            $query->where( $db->quoteName('client_id') . ' = 0' );
            $query->where( $db->quoteName('template_style_id') . ' = 0' );
            $query->where( $db->quoteName('published') . ' = 1' );
            $query->where( $db->quoteName('type') . ' = ' . $db->quote('component') );
            if ($tpl->home == 1) {
                $query->where( $db->quoteName('language') . ' = ' . $db->quote('*') );
            } else {
                $query->where( $db->quoteName('language') . ' = ' . $db->quote($tpl->home) );
            }
            $db->setQuery($query);
            $item = $db->loadObject();
        } else {
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from( $db->quoteName('#__menu') );
            $query->where( $db->quoteName('client_id') . ' = 0' );
            $query->where( $db->quoteName('template_style_id') . ' = ' . $db->quote($id) );
            $query->where( $db->quoteName('published') . ' = 1' );
            $db->setQuery($query);
            $item = $db->loadObject();
        }

        $link = '';

        if ($item) {
            $link = $item->link . '&Itemid=' . $item->id;
            if ($item->language != '*') {
                $arr = explode('-', $item->language);
                $link .= '&lang=' . $arr[0];
            }
        }

        return '<div id="custom-style-preview"><iframe src="about:blank" data-link="' . $link . '"></iframe></div>';
    }

    /**
     * Method to get the field label markup for a spacer.
     * Use the label text or name from the XML element as the spacer or
     * Use a hr="true" to automatically generate plain hr markup
     *
     * @return  string  The field label markup.
     *
     * @since   11.1
     */
    protected function getLabel()
    {
        return '';
    }
}
