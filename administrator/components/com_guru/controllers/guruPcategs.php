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

class guruAdminControllerguruPcategs extends guruAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listZones");
		$this->_model = $this->getModel("guruPcateg");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("orderdown", "orderdown");
		$this->registerTask ("orderup", "orderup");
		$this->registerTask ("saveorder", "saveorder");
		$this->registerTask ("delete_categ_image", "delete_categ_image");
	}

	function listZones(){
		$task = JFactory::getApplication()->input->get("task", "");
		if($task == "saveOrderAjax"){
			$this->saveOrderAjax();
			return true;
		}
		$view = $this->getView("guruPcategs", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit(){
		$view = $this->getView("guruPcategs", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);	
		$view->editForm();
	}

	function checkNameAlias($cat_id){
		if(intval($cat_id) == 0){
			$data = JFactory::getApplication()->input->post->getArray();

			if($data['alias'] == ''){
				$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['name']);
			}
			else{
				$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['alias']);
			}

			$db = JFactory::getDbo();
			$sql = "select count(*) from #__guru_category where `name`='".$db->escape(trim($data['name']))."' OR `alias`='".$db->escape(trim($data['alias']))."'";
			$db->setQuery($sql);
			$db->execute();
			$count_categ = $db->loadColumn();
			$count_categ = @$count_categ["0"];

			if(intval($count_categ) > 0){
				$app = JFactory::getApplication();
				$link = "index.php?option=com_guru&controller=guruPcategs";
				$app->redirect($link, JText::_("GURU_CATEG_SAME_NAME_ALIAS"), "error");
				die();
			}
		}
	}

	function save(){
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$this->checkNameAlias($id);

		if($this->_model->store()){
			$msg = JText::_('GURU_PRCAT_SAVED');
		}
		else{
			$msg = JText::_('GURU_PRCAT_FAILED');
		}
		
		$link = "index.php?option=com_guru&controller=guruPcategs";
		$this->setRedirect($link, $msg);
	}

	function apply(){
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$this->checkNameAlias($id);

		$id = $this->_model->store();
		
		if($id){
			$msg = JText::_('GURU_PRCAT_APPLY');
		}
		else{
			$msg = JText::_('GURU_PR_APPLY_FAILED');
		}

		$pid = $this->_model->getParentId($id);
		$link = "index.php?option=com_guru&controller=guruPcategs&task=edit&cid[]=".intval($id)."&pid=".intval($pid);
		$this->setRedirect($link, $msg);
	}

	function remove(){
		$delete_action = $this->_model->delete();
		if(substr($delete_action,0,1)!='1'){
			$msg = JText::_('GURU_PRCAT_REMERR');
		}
		else{
		 	$msg = JText::_('GURU_PRCAT_REMSUCC');
		}
		
		$delete_action_obj = explode('$$$$$',$delete_action);
		if(strlen($delete_action_obj[1])>0){
			$msg = $this->_model->get_undeleted_categs($delete_action_obj[1]);
			$msg = JText::_('GURU_PRCAT_REMERR1').' '.$msg.JText::_('GURU_PRCAT_REMERR2') ;
		}
		$link = "index.php?option=com_guru&controller=guruPcategs";
		$this->setRedirect($link, $msg);
	}

	function cancel(){
	 	$msg = JText::_('GURU_PRCAT_CANCEL');
		$link = "index.php?option=com_guru&controller=guruPcategs";
		$this->setRedirect($link, $msg);
	}

	function publish(){ 
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_PRCAT_ACTION_ERROR');
		}
		elseif ($res == -1){
		 	$msg = JText::_('GURU_PRCAT_UNPUBLISHED');
		}
		elseif ($res == 1){
			$msg = JText::_('GURU_PRCAT_PUBLISHED');
		}
		else{
            $msg = JText::_('GURU_PRCAT_ACTION_ERROR');
		}
		$link = "index.php?option=com_guru&controller=guruPcategs";
		$this->setRedirect($link, $msg);
	}
	
	function unpublish(){ 
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('GURU_PRCAT_ACTION_ERROR');
		}
		elseif($res == -1){
		 	$msg = JText::_('GURU_PRCAT_UNPUBLISHED');
		} 
		elseif ($res == 1){
			$msg = JText::_('GURU_PRCAT_PUBLISHED');
		}
		else{
			$msg = JText::_('GURU_PRCAT_ACTION_ERROR');
		}
		$link = "index.php?option=com_guru&controller=guruPcategs";
		$this->setRedirect($link, $msg);
	}
	
	function orderdown(){
		$res = $this->_model->orderdown();
		$msg = "";
		$link = "index.php?option=com_guru&controller=guruPcategs";
		if($res){
			$msg = JText::_("GURU_ORDER_SUCCESSFULLY");
			$this->setRedirect($link, $msg);
		}
		else{
			$msg = JText::_("GURU_ORDER_UNSUCCESSFULLY");
			$this->setRedirect($link, $msg, 'notice');
		}				
	}
	
	function orderup(){
		$res = $this->_model->orderup();
		$msg = "";
		$link = "index.php?option=com_guru&controller=guruPcategs";
		if($res){
			$msg = JText::_("GURU_ORDER_SUCCESSFULLY");
			$this->setRedirect($link, $msg);
		}
		else{
			$msg = JText::_("GURU_ORDER_UNSUCCESSFULLY");
			$this->setRedirect($link, $msg, 'notice');
		}				
	}
	
	function saveorder(){
		$res = $this->_model->saveorder();
		$msg = "";
		$link = "index.php?option=com_guru&controller=guruPcategs";
		if($res){
			$msg = JText::_("GURU_ORDER_SUCCESSFULLY");
			$this->setRedirect($link, $msg);
		}
		else{
			$msg = JText::_("GURU_ORDER_UNSUCCESSFULLY");
			$this->setRedirect($link, $msg, 'notice');
		}
	}
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));
		$model = $this->getModel("guruPcateg");
		// Save the ordering
		$return = $model->saveorder($pks, $order);
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function delete_categ_image(){
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0");
		$sql = "update #__guru_category set image='' where id='".intval($id)."'";
		$db->setQuery($sql);
		$db->execute();
	}
};

?>