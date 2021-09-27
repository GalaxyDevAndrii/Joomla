<?php

/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ("joomla.application.component.view");

class guruAdminViewguruabout extends JViewLegacy {

	function display ($tpl =  null ) {		
		JToolBarHelper::title(JText::_('AD_ABOUTTITLE'), 'generic.png');
		JToolBarHelper::Cancel();
		
		$component = array();
		$component['installed'] = 0;
		$component['name'] = '.Guru';
		$component['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."administrator". DIRECTORY_SEPARATOR ."components". DIRECTORY_SEPARATOR ."com_guru". DIRECTORY_SEPARATOR ."install.xml"; 		
		
		if(file_exists($component['file'])){
			$component['installed'] = 1;
			$data = implode("", file($component['file']));
	        $pos1 = strpos($data, "<version>");
	        $pos2 = strpos($data, "</version>");
	        $component['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}		
		$this->component = $component;
		
		$module_cart = array();
		$module_cart['installed'] = 0;
		$module_cart['name'] = 'Guru Cart';
		$module_cart['file'] = JPATH_SITE. DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR ."mod_guru_cart". DIRECTORY_SEPARATOR."mod_guru_cart.xml"; 		
		
		if(file_exists($module_cart['file'])){
			$module_cart['installed'] = 1;
			$data = implode("", file($module_cart['file']));
	        $pos1 = strpos($data, "<version>");
	        $pos2 = strpos($data, "</version>");
	        $module_cart['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
		}		
		
		$this->module_cart = $module_cart;
		
		$db = JFactory::getDBO();		
		$sql = "select element, folder from #__extensions where folder like '%guru%'";
		$db->setQuery($sql);
		$db->execute();
		$all_plugins = $db->loadAssocList();
		$plugins = array();

		if(isset($all_plugins) && count($all_plugins) > 0){			
			foreach($all_plugins as $key=>$value){
				$plugins[$key]['installed'] = 0;
				$plugins[$key]['name'] = $value["element"];
				$file = JPATH_SITE.DIRECTORY_SEPARATOR ."plugins". DIRECTORY_SEPARATOR .$value["folder"]. DIRECTORY_SEPARATOR .'paypaypal'.DIRECTORY_SEPARATOR ."paypaypal.xml"; 		
			
				if(file_exists($file)){
					$plugins[$key]['installed'] = 1;
					$data = implode("", file($file));
					$pos1 = strpos($data, "<version>");
					$pos2 = strpos($data, "</version>");
					$plugins[$key]['version'] = 'version '.substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
				}
			}	
		}		
				
		$this->plugins = $plugins;
		parent::display($tpl);
	}
	
	function vimeo($tpl = null) {
        $id = JFactory::getApplication()->input->get('id', '0');
        $this->id = $id;
        parent::display($tpl);
    }
}

?>