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
//JHTML::_('behavior.tooltip');

class guruAdminControllerguruOrders extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listOrders");
		$this->registerTask ("checkcreateuser", "checkcreateuser");		
		$this->registerTask ("saveCustomer", "saveCustomer");
		$this->registerTask ("continue", "saveCustomer");
		$this->registerTask ("prepereNewOrder", "prepereNewOrder");
		$this->registerTask ("productitem", "productitem");
		$this->registerTask ("saveorder", "saveOrder");
		$this->registerTask ("cancel", "cancel");
		$this->registerTask ("cycleStatus", "cycleStatus");
		$this->registerTask ("approve_order", "approveOrder");
		$this->registerTask ("make_pending", "makePending");
		$this->registerTask ("newCustomerByUsername", "newCustomerByUsername");
		$this->registerTask ("show", "show");
		$this->_model = $this->getModel("guruOrder");
	}
	
	function approveOrder(){
		$model = $this->getModel("guruOrder");
		$res = $model->approveOrder();
		if($res == 1){
			$msg = JText::_('GURU_STATUS_ORDER_PE');
		}
		else{
        	$msg = JText::_('GURU_STATUS_ORDER_P');
		}
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg );				
	}
	
	function makePending(){
		$model = $this->getModel("guruOrder");
		$res = $model->makePending();
		if($res == 1){
			$msg = JText::_('GURU_STATUS_ORDER_PE');
		}
		else{
        	$msg = JText::_('GURU_STATUS_ORDER_P');
		}
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg );				
	}
	
	function newCustomerByUsername(){
		$model = $this->getModel("guruOrder");
		$return = $model->checkUsername();
		
		if($return){// if ok
			$username = JFactory::getApplication()->input->get("username", "", "raw");
			$username_id = $model->getUserId($username);
			$link = "";
			if($this->existCustomer($username_id)){
				$link = "index.php?option=com_guru&controller=guruOrders&task=prepereNewOrder&userid=".intval($username_id);
			}
			else{
				$link = "index.php?option=com_guru&controller=guruOrders&task=checkcreateuser&usertype=10&userid=".intval($username_id);
			}
			$this->setRedirect($link);
		}
		else{ //if not ok
			$username = JFactory::getApplication()->input->get("username", "");
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('NEW_ORDER_BY_USERNAME', $username);
			
			$msg = JText::_("GURU_USERNAME_DOESNT_EXIST");
			$link = "index.php?option=com_guru&controller=guruOrders&task=checkcreateuser&usertype=10";
			$this->setRedirect($link, $msg, 'notice');
		}
	}
	
	function existCustomer($id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_customer where id=".intval($id);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();
		if($result["0"] > 0){
			return true;
		}
		return false;
	}
	
	function cycleStatus(){
		$model = $this->getModel("guruOrder");
		$res = $model->cycleStatus();
		if($res == 1){
			$msg = JText::_('GURU_STATUS_ORDER_PE');
		}
		else{
        	$msg = JText::_('GURU_STATUS_ORDER_P');
		}
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg );				
	}
	
	function saveOrder(){
		$model = $this->getModel("guruOrder");
		$return = $model->saveOrder();
		if($return){
			$msg = JText::_('GURU_ORDSAVED');
		}
		else{
			$msg = JText::_('GURU_ORDFAILED');
		}
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg);
	}
	
	function productitem(){
		$userid = JFactory::getApplication()->input->get("userid", "0");
		$generate_number = $this->generateNumber();		
		$return  = '<div style="border-top:1px solid #CCCCCC;" id="course_item_'.$generate_number.'">';
		$return .= 		'<table>';
		$return .= 			'<tr>';
		$return .= 				'<td width="102px;" style="padding-top:5px;">'.JText::_("GURU_COURSE").'</td>';		
		$return .= 				'<td style="padding-top:5px;">';
		$return .= 					'<table style="width:30%"><tr><td><div style="float: left;">					
										<span style="border: 1px solid rgb(204, 204, 204); padding: 0.2em; overflow: visible; display: block; width: 230px;" id="course_name_text_'.$generate_number.'">'.JText::_("GURU_SELECT_A_COURSE").'</span>
										<input type="hidden" name="course_id['.$generate_number.']" id="course_id'.$generate_number.'" value="">
										
									</div></td><td>';
		$return .= 					'<div class="button2-left">
										<div style="padding: 0pt;" class="blank">
											<a onclick="document.getElementById(\'id_selected\').value=\''.$generate_number.'\'; showContentSelect(\'index.php?option=com_guru&controller=guruPrograms&task=selectCourse&id='.$generate_number.'&userid='.$userid.'&tmpl=component&format=row\');" data-toggle="modal" data-target="#myModal" class="btn" title="'.JText::_("GURU_SELECT_A_COURSE").'" href="#">Select</a></td></tr></table>
										</div>
									</div>';
		$return .= 				'</td>';
		$return .= 				'<td style="padding-top:5px; text-align:left;">';
		$return .= 					'<a onclick="remove_product(\''.$generate_number.'\');" id="course_item_remove_'.$generate_number.'" href="javascript:void(0)">'.JText::_("GURU_REMOVE").'</a>';
		$return .= 				'</td>';
		$return .= 			'</tr>';
		$return .= 			'<tr style="display:none;" id="subscr_type_'.$generate_number.'">';
		$return .= 				'<td>';
		$return .= 					JText::_("GURU_SUBSCRIPTION_TYPE");
		$return .= 				'</td>';
		$return .= 				'<td >';
		$return .= 					'<select onchange="show_licences_renew(\''.$generate_number.'\')" size="1" class="inputbox" id="subscr_type_select'.$generate_number.'" name="subscr_type_select['.$generate_number.']">
										<option value="new">New Subcription</option>
										<option value="renewal">Renewal</option>
									</select>
									<span class="editlinktip hasTip" title="'.JText::_("GURU_TIP_SUBSCRIPTION_TYPE").'" >
										<img border="0" src="components/com_guru/images/icons/tooltip.png">
									</span>';
		$return .= 				'</td>';
		$return .= 				'<td>';
		$return .= 				'</td>';
		$return .= 			'</tr>';
		$return .= 			'<tr style="display: none;" id="licences_'.$generate_number.'">';
		$return .= 				'<td >'.JText::_("GURU_SELECT_PLAN").'</td>';
		$return .= 				'<td>
									<div class="pull-left" id="div_licences_select_'.$generate_number.'">
									<select size="1" class="inputbox" id="licences_select'.$generate_number.'" name="licences_select['.$generate_number.']">
										<option value="none">none</option>
									</select></div>
										<span class="editlinktip hasTip pull-left" title="'.JText::_("GURU_TIP_SELECT_PLAN").'" >
											<img border="0" src="components/com_guru/images/icons/tooltip.png">
										</span>
									</td>';
		$return .= 				'<td></td>';
		$return .= 			'</tr>';
		$return .= 		'</table>';
		$return .= '<div id="course_item_'.$generate_number.'">';
		echo $return;
		die();		
		return true;
	}
	
	function generateNumber(){
		$chars_array = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$format_array = array("strtolower", "strtoupper");
		$number = "";
		//generate 6 charaters
		for($i=0; $i<3; $i++){
			$format = $format_array[rand(0,1)];
			$number .= rand(0,9);			 
			$number .= $format($chars_array[rand(0, 25)]);
		}
		return $number;		 
	}
	
	function saveCustomer(){
		$model = $this->getModel("guruOrder");
		$return = $model->saveCustomer();
		$link = "index.php?option=com_guru&controller=guruOrders&task=prepereNewOrder&userid=".$return;
		$this->setRedirect($link);
	}
	
	function prepereNewOrder(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("prepareorder");
		$view->setModel($this->_model, true);
		$view->prepareOrder();
	}
	
	function show(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("show");
		$view->setModel($this->_model, true);
		$view->show();
	}
	
	function listOrders() {
		$view = $this->getView("guruOrders", "html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("guruConfig");
		$view->setModel($model);
		$view->display();
	}

	function edit(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("addorder");
		$view->setModel($this->_model, true);
		$view->addNewOrder();
	}

	function checkcreateuser(){
		$view = $this->getView("guruOrders", "html");
		$view->setLayout("addtypeorder");
		$view->setModel($this->_model, true);
		$view->addTypeOrder();
	}

	function save(){
		$link = "index.php?option=com_guru&controller=guruOrders";
		$action = $this->_model->store();
		if (strlen($action)>10){
			$to_be_used = explode('$$$$$', $action);
			$link = $to_be_used[0];
			$msg = $to_be_used[1];
			$data = JFactory::getApplication()->input->post->getArray();
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('remember_order', $data);
		}
		elseif ($action == true ) {
			$msg = JText::_('GURU_ORDSAVED');
		}
		elseif ($action == false) {
			$msg = JText::_('GURU_ORDFAILED');
			$data=JFactory::getApplication()->input->post->getArray();
			
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('remember_order', $data);
		}
		$this->setRedirect($link, $msg);
	}
	
	function apply(){
		$id = JFactory::getApplication()->input->get("id","0");
		if($this->_model->store()){
			$msg = JText::_('GURU_ORDAPPLY');
		}
		else{
			$msg = JText::_('GURU_ORDAPPLYFAILED');
		}
		$link = "index.php?option=com_guru&controller=guruOrders&task=edit&cid[]=".$id;
		$this->setRedirect($link, $msg);
	}	

	function remove(){
		if(!$this->_model->delete()){
			$msg = JText::_('GURU_ORDDELERR');
		}
		else{
		 	$msg = JText::_('GURU_ORDDELSUCC');
		}		
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg);
	}
	
	function cancel () {
	 	$msg = JText::_('GURU_ORDCANCEL');
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg);
	}

	function publish(){
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('ORDBLOCKERR');
		}
		elseif ($res == -1) {
		 	$msg = JText::_('ORDUNPUB');
		}
		elseif ($res == 1) {
			$msg = JText::_('ORDPUB');
		}
		else{
            $msg = JText::_('ORDUNSPEC');
		}
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg);
	}

	function unpublish () {
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('ORDBLOCKERR');
		}
		elseif($res == -1){
		 	$msg = JText::_('ORDUNPUB');
		}
		elseif($res == 1){
			$msg = JText::_('ORDPUB');
		} 
		else{
            $msg = JText::_('ORDUNSPEC');
		}
		$link = "index.php?option=com_guru&controller=guruOrders";
		$this->setRedirect($link, $msg);
	}
};

?>