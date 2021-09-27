<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.filesystem.folder' );

class com_guruInstallerScript{

    function install(){
    }
    
    function update($parent){
        $this->install();
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent){
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //echo '<p>' . JText::_('COM_ALTACOACH_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent){
        echo '<style>
                    .adminform{
                        width:97%;
                    }
              </style>';
        
        echo '<div class="alert alert-info">
                    <h4 class="alert-heading">Info</h4>
                    <p>Please wait while the installer is initiated...</p>
              </div>';
        if($type=='install' || $type=='update'){
            echo 
                '<script language="javascript" type="text/javascript">
                    window.location.href = "'.JURI::root().'administrator/index.php?option=com_guru&controller=guruInstall&step=start&tmpl=component";
                </script>';
        }
        if($type=='uninstall'){
            echo 
                '<script language="javascript" type="text/javascript">
                    window.location.href = "'.JURI::root().'administrator/index.php?option=com_installer&view=manage";
                </script>';
        }
        
        return true;
    }
}
?>