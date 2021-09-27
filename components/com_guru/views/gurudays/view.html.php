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

jimport ("joomla.application.component.view");

class guruViewguruDays extends JView {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('Day Manager'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNewX('edit','New');
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList();	
		$ads = $this->get('listDays');
		$this->ads = $ads;
		$pagination =  $this->get( 'Pagination' );
		$this->pagination = $pagination;

		parent::display($tpl);

	}
	
	function show ($tpl =  null ) {
		$db = JFactory::getDBO();		
		// we select from table the informations related to the PROGRAM
		$programname = $this->get('programname');
		$this->programname = $programname;	
		// we select from table the informations related to the DAY of PROGRAM
		$day = $this->get('day');
		$this->day = $day;
		parent::display($tpl);
	}	

	function preview ($tpl =  null ) { 
		parent::display($tpl);
	}		
}

?>