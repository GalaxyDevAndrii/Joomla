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

jimport ('joomla.application.component.controller');

class guruControllerguruEditplans extends guruController {
	
	function __construct () {
		parent::__construct();
		$this->registerTask("","view");
		$this->registerTask("buy","buy");
		$this->registerTask("renew","renew");
		$this->registerTask("course","course");
		$this->_model = $this->getModel("guruEditplans");
	}
	
	function view(){
		JFactory::getApplication()->input->set('view', 'guruEditplans');	
		parent::display();
	}
	
	function buy(){
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");
		$plan = JFactory::getApplication()->input->get("course_plans", "", "raw");
		
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program WHERE id = ".intval($course_id);		
		$db->setQuery($sql);
		$db->execute();
		$name = $db->loadResult();

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$all_courses = $registry->get('courses_from_cart', "");

		if(!is_array($all_courses)){
			$all_courses = array();
		}

		$all_courses[$course_id]["course_id"] = $course_id;
		$all_courses[$course_id]["value"] = $plan;
		$all_courses[$course_id]["name"] = $name;
		$all_courses[$course_id]["plan"] = "buy";
		
		$registry->set('courses_from_cart', $all_courses);
		
		$document = JFactory::getDocument();
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
		
		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["gurubuy"];
		
		if(intval($itemid_seo) > 0){
			$Itemid = intval($itemid_seo);
		}
		
		echo '<script type="text/javascript" language="javascript">
				window.parent.window.location.href="'.JRoute::_("index.php?option=com_guru&view=guruBuy&Itemid=".intval($Itemid)).'";
			  </script>';
		
		//$document->addScriptDeclaration("window.parent.location.href='".JRoute::_("index.php?option=com_guru&view=guruBuy")."';");
	}
	
	function renew(){
		$course_id = JFactory::getApplication()->input->get("course_id", "0");
		$plan = JFactory::getApplication()->input->get("course_plans", "");
		
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program WHERE id = ".intval($course_id);		
		$db->setQuery($sql);
		$db->execute();
		$name = $db->loadResult();
		
		$session = JFactory::getSession();
		$registry = $session->get('registry');
		
		$all_courses = $registry->get('courses_from_cart', "");
		$all_courses[$course_id]["course_id"] = $course_id;
		$all_courses[$course_id]["value"] = $plan;
		$all_courses[$course_id]["name"] = $name;
		$all_courses[$course_id]["plan"] = "renew";
		$registry->set('courses_from_cart', $all_courses);
			
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("window.parent.location.href='".JRoute::_("index.php?option=com_guru&view=guruBuy")."';");
	}
	
	function course(){
		$course_id = JFactory::getApplication()->input->get("course_id", "0");
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("window.parent.location.href='index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($course_id)."';");
	}
};

?>