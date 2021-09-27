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

class guruAdminViewguruPromos extends JViewLegacy {

	function display ($tpl =  null ) {
		$database = JFactory::getDBO();
		JToolBarHelper::title(JText::_('GURU_PROMO_CODES_MANAGER'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNew('edit','New');
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_('GURU_CONFIRMDEL_PROMOCODE'));
		
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		if(isset($data_post['search_promos'])) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('search_promos', $data_post['search_promos']);
		}	
		
		$promos = $this->get('Items');
		$this->promos = $promos;
		
		$pagination = $this->get( 'Pagination' );
		$this->pagination = $pagination;
		parent::display($tpl);

	}
	
	function showCourses ($tpl =  null ) {
		$courses_promo = $this->get('CoursesPromo' );
		$this->courses_promo = $courses_promo;
		parent::display($tpl);

	}

	function editForm($tpl = null) {
		$data = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$promo = $this->get('promo');
		$courses = $this->get('courses');
		$this->courses = $courses;
		$isNew = ($promo->id < 1);
		$text = $isNew ? JText::_('GURU_NEW') : JText::_('GURU_EDIT');
		if (!isset($type) || ($type == '')){ 
			$type = 'cpm';
		}
		JToolBarHelper::title(JText::_('Promo Code').":<small>[".$text."]</small>");
		
		if($isNew){
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel('cancel', 'Cancel');
		} 
		else{
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel('cancel', 'Cancel');
		}	
		$this->promo = $promo;
		// Build type list
		$javascript = 'onchange="document.adminForm.submit();"';		
		$TypeOptions[] 	=  JHTML::_('select.option', 'cpm', JText::_('AGENCY_ORDERTYPE_CPM'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'pc', JText::_('AGENCY_ORDERTYPE_PC'), 'value', 'option' );
		$TypeOptions[] 	=  JHTML::_( 'select.option', 'fr',JText::_('AGENCY_ORDERTYPE_FR'), 'value', 'option' );
		$lists['type']  =  JHTML::_( 'select.genericlist', $TypeOptions, 'type', 'class="inputbox" size="1"'.$javascript,'value', 'option', $type);				
		$this->isNew = $isNew;
		$this->lists = $lists;
		parent::display($tpl);
	}
}
?>