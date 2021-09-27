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

class guruControllerguruQuiz extends guruController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("", "listQuiz");

		$this->_model = $this->getModel("guruQuiz");
	}

	function listQuiz() {

		$view = $this->getView("guruQuiz", "html");
		$view->setModel($this->_model, true);

		$view->display();


	}

	function edit () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1);
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		$view->editForm();

	}
	
	function creat () { 
		$view = $this->getView("adagencyReports", "html");
		$view->setModel($this->_model, true);

		$view->display();

	}
	
	function emptyrep () { 
		$view = $this->getView("adagencyReports", "html");
		$view->setModel($this->_model, true);

		$view->emptyrep();

	}


	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('LANGSAVED');
		} else {
			$msg = JText::_('LANGSAVEFAILED');
		}
		$link = "index.php?option=com_guru&view=guruQuiz";
		$this->setRedirect($link, $msg);

	}

	function upload () {
		$msg = $this->_model->upload();

		$link = "index.php?option=com_guru&view=guruQuiz";
		$this->setRedirect($link, $msg);
		
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('LANGREMERROR');
		} else {
		 	$msg = JText::_('LALNGREMSUCC');
		}
		
		$link = "index.php?option=com_guru&view=guruQuiz";
		$this->setRedirect($link, $msg);
		
	}

	function cancel () {
	 	$msg = JText::_('LANGCANCELED');	
		$link = "index.php?option=com_guru&view=guruQuiz";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('LANGPUBLICHERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('LANGUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('LANGPUBSUCC');
		} else {
                 	$msg = JText::_('LANGUNSPECERROR');
		}
		
		$link = "index.php?option=com_guru&view=guruQuiz";
		$this->setRedirect($link, $msg);


	}
	
	function addquestion () {
		JFactory::getApplication()->input->set ("hidemainmenu", 1); 
		$view = $this->getView("guruQuiz", "html");
		$view->setLayout("addquestion");
		$view->setModel($this->_model, true);
		$view->addquestion();

	}
	
	function savequestion () {
		$db = JFactory::getDbo();
		$data_post = JFactory::getApplication()->input->post->getArray();
		
		$qtext = $db->escape($data_post['text']);
		$quizid = intval($data_post['quizid']);
		$a1 = $db->escape($data_post['a1']);
		$a2 = $db->escape($data_post['a2']);
		$a3 = $db->escape($data_post['a3']);
		$a4 = $db->escape($data_post['a4']);
		$a5 = $db->escape($data_post['a5']);
		$a6 = $db->escape($data_post['a6']);
		$a7 = $db->escape($data_post['a7']);
		$a8 = $db->escape($data_post['a8']);
		$a9 = $db->escape($data_post['a9']);
		$a0 = $db->escape($data_post['a0']);
		$answers = $data_post['1a'].$data_post['2a'].$data_post['3a'].$data_post['4a'].$data_post['5a'].$data_post['6a'].$data_post['7a'].$data_post['8a'].$data_post['9a'].$data_post['0a'];
		$this->_model->addquestion($qtext,$quizid,$a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9,$a0,$answers);
	}

};

?>