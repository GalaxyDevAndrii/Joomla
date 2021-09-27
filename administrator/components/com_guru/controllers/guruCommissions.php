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

class guruAdminControllerguruCommissions extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("list", "commissionsList");
		$this->registerTask ("add", "addCommissions");
		$this->registerTask ("remove", "delete");
		$this->registerTask ("save", "save");
		$this->registerTask ("apply", "apply");
		$this->registerTask ("default", "default_comm");
		$this->registerTask ("edit", "edit");
		$this->registerTask ("history", "history");
		$this->registerTask ("pending", "pending");
		$this->registerTask ("paid", "paid");
		$this->registerTask ("details", "view_details");
		$this->registerTask ("make_paid", "make_Paid");
		$this->registerTask ("make_paid_top", "make_Paid_Top");
		$this->registerTask ("pending_ok", "pendingOk");
		$this->_model = $this->getModel("gurucommissions");
		
	}
	
	function edit(){
		JFactory::getApplication()->input->set("view", "gurucommissions");
		$view = $this->getView("gurucommissions", "html");
		$view->setLayout("editform");
		$view->setModel($this->_model, true);
		$view->editform();
	}
	
	function history(){
		JFactory::getApplication()->input->set("view", "gurucommissions");
		$view = $this->getView("gurucommissions", "html");
		$view->setLayout("historycommission");
		$view->setModel($this->_model, true);
		$view->historycommission();
	}	
	function pending(){
		JFactory::getApplication()->input->set("view", "gurucommissions");
		$p = JFactory::getApplication()->input->get("p", "0", "raw");
		$view = $this->getView("gurucommissions", "html");
		$view->setLayout("pendingcommission");
		$view->setModel($this->_model, true);
		$view->pendingcommission();
	}	
	function paid(){
		JFactory::getApplication()->input->set("view", "gurucommissions");
		$view = $this->getView("gurucommissions", "html");
		$view->setLayout("paidcommission");
		$view->setModel($this->_model, true);
		$view->paidcommission();
	}			
	function addCommissions(){
		JFactory::getApplication()->input->set("view", "gurucommissions");
		$view = $this->getView("gurucommissions", "html");
		$view->setLayout("editform");
		$view->setModel($this->_model, true);
		$view->editform();
	}
	function view_details(){
		JFactory::getApplication()->input->set("view", "gurucommissions");
		$view = $this->getView("gurucommissions", "html");
		$view->setLayout("view_details");
		$view->setModel($this->_model, true);
		$view->view_details();
	}
	
	function commissionsList() {
       	JFactory::getApplication()->input->set("view", "gurucommissions");
		$view = $this->getView("gurucommissions", "html");		
		$view->setModel($this->_model, true);
		parent::display();
	}
	function default_comm(){
		if($this->_model->setdefault()){
			$msg = JText::_( 'GURU_COMMISSIONS_DETAILS_SAVED' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruCommissions&task=list', $msg );
		}
		else{
			$msg = JText::_( 'GURU_COMMISSIONS_DETAILS_NOT_SAVED' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruCommissions&task=list', $msg, warning );
		}
	}
	
	function make_Paid(){
		$app = JFactory::getApplication('administrator');
		$result = $this->_model->make_paid();
		$app->enqueueMessage($msg, $type);
		$app->redirect('index.php?option=com_guru&controller=guruCommissions&task=pending');
	}
	function pendingOk(){
		$app = JFactory::getApplication('administrator');
		$result = $this->_model->make_paid_paypal();
		$app->enqueueMessage($msg, $type);
		$app->redirect('index.php?option=com_guru&controller=guruCommissions&task=pending');
	}
	function make_Paid_Top(){
		$app = JFactory::getApplication('administrator');
		$p = JFactory::getApplication()->input->get("p", "", "raw");
		$result = $this->_model->make_paid_top();
		if($result === true){
			$msg = JText::_('GURU_COMMISSIONS_DELETED');
			$type = "message";
		} 
		elseif($result === false){
			$msg = JText::_('GURU_COMMISSIONS_DELETED_ERROR');
			$type = "error";
		}

		$app->enqueueMessage($msg, $type);
		$app->redirect('index.php?option=com_guru&controller=guruCommissions&task=pending');
	}
	

	function delete(){
	 	$app = JFactory::getApplication('administrator');
		$result = $this->_model->delete();
		$msg = "";
		$type = "";
		$userlist =  array();
		
		if($result === true){
			$msg = JText::_('GURU_COMMISSIONS_DELETED');
			$type = "message";
		} 
		elseif($result === false){
			$msg = JText::_('GURU_COMMISSIONS_DELETED_ERROR');
			$type = "error";
		}
		elseif($result == "has teacher"){
			$db = JFactory::getDBO();
			
			$cids = JFactory::getApplication()->input->get('cid', array(), "raw");
			if(isset($cids) && is_array($cids) && count($cids) > 0){
				foreach($cids as $key=>$id){
					$sql = "select userid from #__guru_authors where commission_id=".intval($id);
					$db->setQuery($sql);
					$db->execute();
					$userlist12 = $db->loadColumn();
					$userlist = $userlist12;
				}
			}
			$sql = "select name from #__users where id in (".implode(",",$userlist).")";
			$db->setQuery($sql);
			$db->execute();
			$userlist_names = $db->loadColumn();
				
				
			$msg = JText::_("GURU_COMMISSIONS_EXIST_AUTHOR_ERROR1")."'".implode(", ", $userlist_names)."'".JText::_("GURU_COMMISSIONS_EXIST_AUTHOR_ERROR2");
			$type = "warning";
		}
		
		$app->enqueueMessage($msg, $type);
		$app->redirect('index.php?option=com_guru&controller=guruCommissions&task=list');
	}

	
	
	function display($cachable = false, $urlparams = Array()){
		JFactory::getApplication()->input->set("view", "guruauthor");
		$view = $this->getView("guruauthor", "html");		
		$view->setModel($this->_model, true);
		@parent::display();
    }

	
	
	function cancel(){
		$msg = JText::_('GURU_ORDCANCEL');
		$this->setRedirect( 'index.php?option=com_guru&controller=guruCommissions&task=list', $msg );		
	}	
	
	function save(){
		$app = JFactory::getApplication('administrator');
		$data = JFactory::getApplication()->input->post->getArray();
		if($this->_model->save()){
			$msg = JText::_( 'GURU_COMMISSIONS_DETAILS_SAVED' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruCommissions&task=list', $msg );
		}
	}
	function apply(){
		$app = JFactory::getApplication('administrator');
		$id = $this->_model->save();
		if(intval($id) > 0){
			$msg = JText::_( 'GURU_COMMISSIONS_DETAILS_SAVED' );
			$this->setRedirect( 'index.php?option=com_guru&controller=guruCommissions&task=edit&cid[]='.$id, $msg );
		}
	}
};
?>