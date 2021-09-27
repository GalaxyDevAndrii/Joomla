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

defined('_JEXEC') or die('Restricted access');

jimport("joomla.application.component.view");

class guruAdminViewguruQuiz extends JViewLegacy
{

	function display($tpl =  null)
	{
		JToolBarHelper::title(JText::_('GURU_Q_QUIZ_MANAGER'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList(JText::_("GURU_DELETE_QUIZ_RESULTS"), 'deletequizresult', JText::_("GURU_CLEAR_RESULTS"));
		//JToolBarHelper::addNew('editZ',JText::_('GURU_NEW_Q_BTN'));
		JToolBarHelper::addNew('duplicate', JText::_('GURU_DUPLICATE_Q_BTN'));
		JToolBarHelper::editList();
		JToolBarHelper::deleteList(JText::_("GURU_SURE_DELETE_QUIZ"));

		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;

		$ads = $this->get('listQuiz');
		$this->ads = $ads;
		parent::display($tpl);
	}

	function listquizstud($tpl =  null)
	{
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;
		$pid = JFactory::getApplication()->input->get('pid', "");
		$model = $this->getModel('guruQuiz');
		$list = $model->getlistQuizTakenStud();
		$this->ads = $list;
		parent::display($tpl);
	}
	function listStudentsQuizTaken($tpl =  null)
	{
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;
		$model = $this->getModel('guruQuiz');
		$list = $model->getlistStudentsQuizTaken();
		$this->ads = $list;
		parent::display($tpl);
	}
	function show_quizz_res($tpl =  null)
	{
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;
		$list1 = $this->get('listQuizTakenStud');
		$this->ads = $list1;
		parent::display($tpl);
	}

	function addquestion($tpl = null)
	{
		$db = JFactory::getDBO();
		$qid = JFactory::getApplication()->input->get("qid", "0", "raw");
		$cid = JFactory::getApplication()->input->get("cid", array(), "raw");

		$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE id=" . intval($qid) . " AND qid=" . intval($cid[0]));
		$medias = $db->loadObject();
		$this->medias = $medias;

		$db->setQuery("SELECT * FROM #__guru_question_answers WHERE question_id=" . intval($qid) . " order by id");
		$medians = $db->loadObjectList();
		$this->medians = $medians;

		parent::display($tpl);
	}

	function addquizzes($tpl =  null)
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDBO();
		$search_text = $input->get('search_text', "");
		$sql = "SELECT `id`, `name` FROM `#__guru_quiz`";
		if ($search_text != "") {
			$sql = $sql . " where name LIKE '%" . $search_text . "%' and is_final <> 1 ";
		} else {
			$sql = $sql . " where is_final <> 1";
		}
		$db->setQuery($sql);
		$list_quizzes = $db->loadAssocList();
		$this->list_quizzes = $list_quizzes;

		parent::display($tpl);
	}
	function settypeform($tpl = null)
	{
		$id = JFactory::getApplication()->input->get("id", "0");
		if ($id == "0") {
			JToolBarHelper::title(JText::_('GURU_Q_QUIZ_MANAGER'));
		} else {
			JToolBarHelper::title(JText::_('GURU_Q_QUIZ_MANAGER'));
		}
		parent::display($tpl);
	}
	function editquestion($tpl =  null)
	{
		$data_get = JFactory::getApplication()->input->get->getArray();
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE id = " . $data_get['qid'] . " AND qid =" . $data_get['cid'][0]);
		$medias = $db->loadObject();
		$this->medias = $medias;
		parent::display($tpl);
	}

	function editForm($tpl = null)
	{
		$app = JFactory::getApplication('administrator');
		$db = JFactory::getDBO();
		$program = $this->get('quiz');
		$value_option = JFactory::getApplication()->input->get("v");
		if ($program->is_final == 0 && $value_option == 0) {
			JToolBarHelper::title(JText::_('GURU_QUIZ') . ":<small>[" . $program->text . "]</small>");
		} else {
			JToolBarHelper::title(JText::_('GURU_FINAL_EXAM_QUIZ1') . ":<small>[" . $program->text . "]</small>");
		}
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel('cancel', JText::_('GURU_CANCEL_Q_BTN'));

		$media = $this->get('Media');
		$this->media = $media;
		$this->max_reo = $media->max_reo;
		$this->min_reo = $media->min_reo;
		$this->mmediam = $media->mmediam;
		$this->mainmedia = $media->mainmedia;
		$this->program = $program;
		$pagination = $this->get('Pagination');
		$this->pagination = $pagination;
		parent::display($tpl);
	}

	function addmedia($tpl =  null)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__guru_media");
		$medias = $db->loadObjectList();
		$this->medias = $medias;
		parent::display($tpl);
	}

	function getNrStudentsQuiz()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT DISTINCT (user_id) FROM #__guru_quiz_taken_v3";
		$db->setQuery($sql);
		$db->execute();
		$total = $db->loadAssocList();
		return intval(count($total));
	}

	function getTotalAvg()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT avg(score_quiz) as total FROM #__guru_quiz_question_taken_v3";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadResult();
		$total = @$result["total"];
		return $total;
	}
}
