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

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldT4brand extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.7.3
     */
    protected $type = 'T4brand';

    /**
     * The control.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $control = 'hue';

    /**
     * The format.
     *
     * @var    string
     * @since  3.6.0
     */
    protected $format = 'hex';

    /**
     * The keywords (transparent,initial,inherit).
     *
     * @var    string
     * @since  3.6.0
     */
    protected $keywords = '';

    /**
     * The position.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $position = 'default';

    /**
     * The colors.
     *
     * @var    mixed
     * @since  3.2
     */
    protected $colors;

    /**
     * The split.
     *
     * @var    integer
     * @since  3.2
     */
    protected $split = 3;

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  3.5
     */
    protected $layout = 'field.brandcolor';
    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param   string  $name   The property name for which to set the value.
     * @param   mixed   $value  The value of the property.
     *
     * @return  void
     *
     * @since   3.2
     */
    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'split':
                $value = (int) $value;
            case 'control':
            case 'format':
                $this->$name = (string) $value;
                break;
            case 'keywords':
                $this->$name = (string) $value;
                break;
            case 'exclude':
            case 'colors':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     JFormField::setup()
     * @since   3.2
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $this->control  = isset($this->element['control']) ? (string) $this->element['control'] : 'hue';
            $this->format   = isset($this->element['format']) ? (string) $this->element['format'] : 'hex';
            $this->keywords = isset($this->element['keywords']) ? (string) $this->element['keywords'] : '';
            $this->position = isset($this->element['position']) ? (string) $this->element['position'] : 'default';
            $this->colors   = (string) $this->element['colors'];
            $this->split    = isset($this->element['split']) ? (int) $this->element['split'] : 3;
        }

        return $return;
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   1.7.3
     */
    protected function getInput()
    {

        // Trim the trailing line in the layout file
        return rtrim(\JLayoutHelper::render($this->layout, $this->getLayoutData(), T4PATH_ADMIN . '/layouts'), PHP_EOL);
    }

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     *
     * @since 3.5
     */
    protected function getLayoutData()
    {
        $lang  = JFactory::getLanguage();
        $data  = parent::getLayoutData();
        $color = strtolower($this->value);
        $color = !$color ? $this->element['default'] : $color;

        // Position of the panel can be: right (default), left, top or bottom (default RTL is left)
        $position = ' data-position="' . (($lang->isRTL() && $this->position == 'default') ? 'left' : $this->position) . '"';

        if (!$color || in_array($color, array('none', 'transparent')))
        {
            $color = 'none';
        }
        elseif ($color['0'] != '#' && $this->format == 'hex')
        {
            $color = '#' . $color;
        }

        // Assign data for simple/advanced mode
        $controlModeData = $this->control === 'simple' ? $this->getSimpleModeLayoutData() : $this->getAdvancedModeLayoutData($lang);

        $extraData = array(
            'color'    => $color,
            'format'   => $this->format,
            'keywords' => $this->keywords,
            'position' => $position,
            'validate' => $this->validate
        );

        return array_merge($data, $extraData, $controlModeData);
    }

    /**
     * Method to get the data for the simple mode to be passed to the layout for rendering.
     *
     * @return  array
     *
     * @since 3.5
     */
    protected function getSimpleModeLayoutData()
    {
        $colors = strtolower($this->colors);

        if (empty($colors))
        {
            $colors = array(
                'none',
                '#049cdb',
                '#46a546',
                '#9d261d',
                '#ffc40d',
                '#f89406',
                '#c3325f',
                '#7a43b6',
                '#ffffff',
                '#999999',
                '#555555',
                '#000000',
            );
        }
        else
        {
            $colors = explode(',', $colors);
        }

        if (!$this->split)
        {
            $count = count($colors);
            if ($count % 5 == 0)
            {
                $split = 5;
            }
            else
            {
                if ($count % 4 == 0)
                {
                    $split = 4;
                }
            }
        }

        $split = $this->split ? $this->split : 3;

        return array(
            'colors' => $colors,
            'split'  => $split,
        );
    }

    /**
     * Method to get the data for the advanced mode to be passed to the layout for rendering.
     *
     * @param   object  $lang  The language object
     *
     * @return  array
     *
     * @since   3.5
     */
    protected function getAdvancedModeLayoutData($lang)
    {
        return array(
            'colors'  => $this->colors,
            'control' => $this->control,
            'lang'    => $lang,
        );
    }
}
