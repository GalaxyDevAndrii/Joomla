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

class guruAdminViewguruLanguages extends JViewLegacy {

	function display ($tpl =  null ) {
	}


	function editForm($tpl = null) {

		$db = JFactory::getDBO();
				
		$isNew = 0;
		$text = $isNew ? JText::_('GURU_NEW') : JText::_('GURU_EDIT');

		JToolBarHelper::title(JText::_('GURU_LANGUAGE_MANAGER').":<small>[".trim($text)."]</small>");
		//JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel();
		} else {
			//JToolBarHelper::apply();
			JToolBarHelper::cancel ('cancel', 'Close');
		}
		
		parent::display($tpl);
	}
}

?>