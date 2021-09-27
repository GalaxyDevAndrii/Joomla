<?php
/**
 * @version     $Id: model.php 1679 2012-10-03 13:04:52Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ;

jimport('joomla.application.component.model');

if (version_compare(JVERSION, '3.0', 'ge'))
{
    class JAEMModel extends JModelLegacy
    {
        public static function addIncludePath($path = '', $prefix = '')
        {
            return parent::addIncludePath($path, $prefix);
        }

    }

}
else if (version_compare(JVERSION, '2.5', 'ge'))
{
    class JAEMModel extends JModel
    {
        public static function addIncludePath($path = '', $prefix = '')
        {
            return parent::addIncludePath($path, $prefix);
        }

    }

}
else
{
    class JAEMModel extends JModel
    {
        public function addIncludePath($path = '', $prefix = '')
        {
            return parent::addIncludePath($path);
        }

    }

}
