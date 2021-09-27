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

class guruAdminControllerguruFields extends guruAdminController{
	var $model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "listFields");
		$this->registerTask ("new_field", "newField");
		$this->registerTask ("new_group", "newGroup");
		$this->registerTask ("save_field", "saveField");
		$this->registerTask ("save_group", "saveGroup");
		$this->registerTask ("saveOrderAjax", "saveOrderAjax");
		$this->registerTask ("saveOrderGroupsAjax", "saveOrderGroupsAjax");
		$this->registerTask ("publish", "publish");
		$this->registerTask ("unpublish", "unpublish");
		$this->registerTask ("required", "required");
		$this->registerTask ("un-required", "unrequired");
		$this->registerTask ("published-group", "publishGroup");
		$this->registerTask ("unpublished-group", "unpublishGroup");
		$this->registerTask ("delete-group", "deleteGroup");
		$this->registerTask ("delete-field", "deleteField");

		$this->_model = $this->getModel("guruFields");
	}
	
	function listFields(){		
		$view = $this->getView("guruFields", "html"); 
		$view->setModel($this->_model, true);
		$view->display();
	}

	function newField(){		
		$view = $this->getView("guruFields", "html");
		$view->setLayout("newfield");
		$view->setModel($this->_model, true);
		$view->newField();
	}

	function newGroup(){		
		$view = $this->getView("guruFields", "html");
		$view->setLayout("newgroup");
		$view->setModel($this->_model, true);
		$view->newGroup();
	}

	function saveField(){
		if($this->_model->saveField()){
			$msg = JText::_('GURU_FIELD_SAVED');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_NOT_SAVED');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		
		echo '
			<script>
				document.getElementsByTagName("body")[0].style.visibility = "hidden";
				window.parent.location.href = "'.$link.'";
			</script>
		';
		die();
	}

	function saveGroup(){
		if($this->_model->saveGroup()){
			$msg = JText::_('GURU_GROUP_SAVED');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_GROUP_NOT_SAVED');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		
		echo '
			<script>
				document.getElementsByTagName("body")[0].style.visibility = "hidden";
				window.parent.location.href = "'.$link.'";
			</script>
		';
		die();
	}

	function publish(){
		if($this->_model->publish()){
			$msg = JText::_('GURU_FIELD_PUBLISHED_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_PUBLISHED_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	function unpublish(){
		if($this->_model->unpublish()){
			$msg = JText::_('GURU_FIELD_UNPUBLISHED_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_UNPUBLISHED_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	function required(){
		if($this->_model->required()){
			$msg = JText::_('GURU_FIELD_REQUIRED_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_REQUIRED_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	function unrequired(){
		if($this->_model->unrequired()){
			$msg = JText::_('GURU_FIELD_UNREQUIRED_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_UNREQUIRED_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	function publishGroup(){
		if($this->_model->publishGroup()){
			$msg = JText::_('GURU_FIELD_PUBLISHED_GROUP_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_PUBLISHED_GROUP_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	function unpublishGroup(){
		if($this->_model->unpublishGroup()){
			$msg = JText::_('GURU_FIELD_UNPUBLISHED_GROUP_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_FIELD_UNPUBLISHED_GROUP_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	public function saveOrderAjax(){
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("guruFields");
		// Save the ordering
		$return = $model->saveOrder();

		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}

	public function saveOrderGroupsAjax(){
		$model = $this->getModel("guruFields");
		// Save the ordering
		$return = $model->saveOrderGroups();

		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}

	function deleteGroup(){
		if($this->_model->deleteGroup()){
			$msg = JText::_('GURU_DELETE_GROUP_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_DELETE_GROUP_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}

	function deleteField(){
		if($this->_model->deleteField()){
			$msg = JText::_('GURU_DELETE_FIELD_SUCC');
			$notice = '';
		}
		else{
			$msg = JText::_('GURU_DELETE_FIELD_UNSUCC');
			$notice = 'warning';
		}

		$link = "index.php?option=com_guru&controller=guruFields";
		$this->setRedirect($link, $msg);
	}
	
	function cancel(){
	 	$msg = JText::_('GURU_CS_OPCANC');
		$link = "index.php?option=com_guru";
		$this->setRedirect($link, $msg);
	}
};

?>