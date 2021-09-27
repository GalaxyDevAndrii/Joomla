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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ("joomla.application.component.view");

require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
JHTML::_( 'behavior.modal' );

class guruAdminViewguruOrders extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('GURU_ORDER_MANAGER_TITLE'), 'generic.png');
		JToolBarHelper::custom('approve_order','publish','publish',JText::_('GURU_APROVE'),false);
		JToolBarHelper::custom('make_pending','forward.png','forward_f2.png',JText::_('GURU_MAKE_PENDING'),false);
		JToolBarHelper::addNew();
		JToolBarHelper::deleteList(JText::_('GURU_CONFIRM_DEL'));

		$orders = $this->get('Items');
		$pagination = $this->get( 'Pagination' );
		
				
		$this->orders = $orders;
		$this->pagination = $pagination;
			
		@$configs->time_format="Y-m-d";
		
        $now = mktime(0,0,0,date("m"),date("d"),date("Y"));
        $tomorrow = $now + 3600*24;
	    $start_month = mktime(0,0,0,date("m"),1,date("Y"));
        $end_month = mktime(0,0,0,date("m")+1,1,date("Y")) - 3600*24;
        $start_month1 = mktime(0,0,0,date("m")-1,1,date("Y"));
	    $end_month1 = mktime(0,0,0,date("m"),1,date("Y")) - 3600*24;
	   	
		$dates['start_date']['today'] = date($configs->time_format);
	    $dates['end_date']['today'] = date($configs->time_format);
      	$dates['start_date']['yesterday'] = date($configs->time_format, $now-3600*24);
	    $dates['end_date']['yesterday'] = date($configs->time_format, $now-3600*24);
     	$dates['start_date']['lastweek'] = date($configs->time_format, $now-3600*24*7);
	    $dates['end_date']['lastweek'] = date($configs->time_format, $now);
     	$dates['start_date']['thismonth'] = date($configs->time_format, $start_month);
		$dates['end_date']['thismonth'] = date($configs->time_format, $end_month);
        $dates['start_date']['lastmonth'] = date($configs->time_format, $start_month1);
	    $dates['end_date']['lastmonth'] = date($configs->time_format, $end_month1);

		$this->dates = $dates;
		
		$datetype = $this->get( 'DateType' );
		$this->datetype = $datetype;
		
	    parent::display($tpl);

	}

	function addNewOrder($tpl = null) {	
		$id = JFactory::getApplication()->input->get("id", "0");
		if($id == "0" || $id ==NULL){
			JToolBarHelper::title(JText::_('GURU_ORDER').":<small>[".trim(JText::_("GURU_NEW"))."]</small>");
		}
		else{
			JToolBarHelper::title(JText::_('GURU_ORDER').":<small>[".trim(JText::_("GURU_EDIT"))."]</small>");
		}
		JToolBarHelper::cancel('cancel', 'Cancel');
		parent::display($tpl);		
	}
	
	function addTypeOrder($tpl = null) {	
		$id = JFactory::getApplication()->input->get("id", "0");
		if($id == "0" || $id ==NULL){
			JToolBarHelper::title(JText::_('GURU_ORDER').":<small>[".trim(JText::_("GURU_NEW"))."]</small>");
		}
		else{
			JToolBarHelper::title(JText::_('GURU_ORDER').":<small>[".trim(JText::_("GURU_EDIT"))."]</small>");
		}	
		JToolBarHelper::cancel('cancel', 'Cancel');
		parent::display($tpl);		
	}
	
	function prepareorder($tpl = null) {	
		$id = JFactory::getApplication()->input->get("id", "0");
		if($id == "0" || $id ==NULL){
			JToolBarHelper::title(JText::_('GURU_ORDER').":<small>[".trim(JText::_("GURU_NEW"))."]</small>");
		}
		else{
			JToolBarHelper::title(JText::_('GURU_ORDER').":<small>[".trim(JText::_("GURU_EDIT"))."]</small>");
		}	
		JToolBarHelper::cancel('cancel', 'Cancel');
		parent::display($tpl);		
	}
	
	function getOrderDetails(){
		$details = $this->get("OrderDetails");
		return $details;
	}
	
	function show($tpl = null){
		JToolBarHelper::cancel('cancel', JText::_("GURU_BACK"));
		$order = $this->get("OrderFromOrders");
		$this->order = $order;
		parent::display($tpl);
	}	
	
	function getAllTeachers(){
		$teacher = $this->get("AllTeachers");
		return $teacher;
	}
	
	function getTotalSum(){
		$sum = $this->get("TotalSum");
		return $sum;
	}
	
	function getPromoName($id){
		if(intval($id) == 0){
			return "";
		}
		else{
			$db = JFactory::getDBO();
			$sql = "select code from #__guru_promos where id=".intval($id);
			$db->setQuery($sql);
			$db->execute();
			$code = $db->loadColumn();
			$code = @$code["0"];
			return '<a href="index.php?option=com_guru&controller=guruPromos&task=edit&cid[]='.intval($id).'">'.$code."</a>";
		}
	}
}

?>