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

defined('_JEXEC') or die('Restricted access');

jimport("joomla.aplication.component.model");


class guruAdminModelguruQuiz extends JModelLegacy
{
	var $_languages;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
		global $option;
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");

		if (is_array($cids) && isset($cids["0"])) {
			$cids = $cids["0"];
		}

		$mainframe = JFactory::getApplication("admin");
		// Get the pagination request variables
		$app = JFactory::getApplication('administrator');
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
		$this->setId((int)$cids);

		if (JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")) {
			JFactory::getApplication()->input->set("limitstart", "0");
			$this->setState('limitstart', 0);
		}
	}

	function setId($id)
	{
		$this->_id = $id;
		$this->_installpath = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR;
		$this->_plugin = null;
	}
	public static function StudentsQuizzNo($id)
	{
		$db = JFactory::getDBO();
		$sql = "select count( distinct user_id) from #__guru_quiz_taken_v3 where quiz_id=" . $id . " and user_id <> 0";
		$db->setQuery($sql);
		$tmp = $db->loadResult();
		return $tmp;
	}

	public static function NbOfTimesandStudents($quiz_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT user_id, avg(score_quiz) as score_by_user FROM #__guru_quiz_question_taken_v3 WHERE quiz_id=" . intval($quiz_id) . " group by user_id";
		$db->setQuery($sql);
		return  $db->loadAssocList();
	}

	function getlistStudentsQuizTaken()
	{
		$quiz_id =  intval(JFactory::getApplication()->input->get("id", "", "raw"));
		$db = JFactory::getDBO();
		/*$sql = "select u.id, u.username, u.email, c.firstname, c.lastname, tq.date_taken_quiz, tq.score_quiz, tq.id as tq_id  from #__guru_customer c, #__users u, #__guru_quiz_taken_v3 tq where c.id=u.id and c.id = tq.user_id and u.id IN (select  user_id from #__guru_quiz_taken_v3 where quiz_id=".$quiz_id.") and tq.quiz_id=".$quiz_id." order by c.id desc";*/

		$sql = "select u.id, u.username, u.email, c.firstname, c.lastname, tq.date_taken_quiz, tq.score_quiz, tq.id as tq_id  from #__guru_customer c, #__users u, #__guru_quiz_question_taken_v3 tq where c.id=u.id and c.id = tq.user_id and u.id IN (select  user_id from #__guru_quiz_taken_v3 where quiz_id=" . $quiz_id . ") and tq.quiz_id=" . $quiz_id . " order by c.id desc";

		$db->setQuery($sql);
		$tmp = $db->loadObjectList();
		return $tmp;
	}

	function getScoreQuizTaken($quiz_id, $user_id, $tq_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and id=" . intval($tq_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}

	function DataTaken($quiz_id, $user_id, $tq_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT 	date_taken_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and id=" . intval($tq_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}


	function getlistQuiz()
	{
		$data_post = JFactory::getApplication()->input->post->getArray();

		if (empty($this->_plugins)) {
			$sql = "SELECT * FROM #__guru_quiz AS c WHERE 1=1 ";

			$search_cond = NULL;
			$limit_cond = NULL;

			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$search_quiz = $registry->get('search_quiz', "");

			if (isset($search_quiz) && trim($search_quiz) != "") {
				$src = $search_quiz;
				$search_cond = " AND (c.id LIKE '%" . $src . "%' OR c.name LIKE '%" . $src . "%' OR c.description LIKE '%" . $src . "%' or c.image LIKE '%" . $src . "%' )";
			}

			if (isset($data_post['search_quiz'])) {
				$src = $data_post['search_quiz'];
				$registry->set('search_quiz', $src);

				$search_cond = " AND (c.id LIKE '%" . $src . "%' OR c.name LIKE '%" . $src . "%' OR c.description LIKE '%" . $src . "%' or c.image LIKE '%" . $src . "%' )";
			}

			if (isset($data_post['quiz_select_type'])) {
				$src = $data_post['quiz_select_type'];

				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('quiz_select_type', $src);

				if ($src == 1) {
					$search_cond = " AND c.is_final= 0";
				} elseif ($src == 2) {
					$search_cond = " AND c.is_final= 1";
				}
			}

			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');

			if ($limit != 0) {
				$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
			}

			$published = NULL;

			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$quiz_publ_status = $registry->get('quiz_publ_status', "");

			if (isset($quiz_publ_status) && trim($quiz_publ_status) != "") {
				$src = $quiz_publ_status;

				if (($src == 'Y') || ($src == 'N')) {
					if ($src == 'Y') {
						$src = 1;
					} else {
						$src = 0;
					}
					$published = " AND c.published =" . $src . " ";
				}
			}

			if (isset($data_post['quiz_publ_status'])) {
				$src = $data_post['quiz_publ_status'];
				if (($src == 'Y') || ($src == 'N')) {
					if ($src == 'Y') {
						$src = 1;
					} else {
						$src = 0;
					}
					$published = " AND c.published =" . $src . " ";
				}
			}

			$orderby = ' ORDER BY c.id desc';

			$sql .= $search_cond . $published;
			$this->_total = $this->_getListCount($sql);
			$this->_languages = $this->_getList($sql . $orderby . $limit_cond);
		}

		return $this->_languages;
	}

	function getlistQuizTakenStud()
	{
		$db = JFactory::getDBO();
		$user_id = JFactory::getApplication()->input->get('cid', "", "raw");
		$pid = JFactory::getApplication()->input->get('pid', "", "raw");
		$sql = "SELECT * FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . $user_id . " and pid=" . $pid;
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		$this->_total = count($result);

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		if ($limit != 0) {
			$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
		}

		$sql = "SELECT * FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . $user_id . " and pid=" . $pid;
		$db->setQuery($sql);
		$result = $this->_getList($sql . $limit_cond);

		return $result;
	}
	function getshow_quizz_res()
	{
		$db = JFactory::getDBO();
		$quiz_id = JFactory::getApplication()->input->get('quiz_id', "", "raw");
		$sql = "SELECT * FROM #__guru_question WHERE qid=" . $quiz_id;
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		return $result;
	}

	public static function getQuizName($quiz_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_quiz WHERE id=" . $quiz_id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	public static function getStudName($user_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT name FROM #__users WHERE id=" . $user_id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	public static function getCourseName($pid)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program WHERE id=" . $pid;
		$database->setQuery($sql);
		$course_name = $database->loadResult();
		return $course_name;
	}
	function getScoreQuiz($quiz_id, $user_id, $id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT 	score_quiz FROM #__guru_quiz_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and id=" . intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}
	function getAnsGived($user_id, $id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT q.id, answers_gived FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and user_id=" . intval($user_id) . " and show_result_quiz_id=" . intval($id) . " ORDER BY q.reorder";
		$db->setQuery($sql);
		$result_ansgived = $db->loadObjectList("id");
		return $result_ansgived;
	}
	function getAnsRight($quiz_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT id, answers FROM #__guru_questions_v3 WHERE qid=" . intval($quiz_id) . " ORDER BY reorder ";
		$db->setQuery($sql);
		$result_ansright = $db->loadObjectList("id");
		return $result_ansright;
	}
	function getAllAns($quiz_id, $id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=" . $id . " ORDER BY reorder";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$i = 0;
		foreach ($result as $key => $value) {
			$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE qid=" . intval($quiz_id) . " and id=" . $value->question_id;
			$db->setQuery($sql);
			$choices = $db->loadAssocList();
			$correct_ans = "";

			if (!isset($choices["0"])) {
				continue;
			}

			if ($choices[0]['a1'] != "") {
				$correct_ans .= "1a|||";
			}
			if ($choices[0]['a2'] != "") {
				$correct_ans .= "2a|||";
			}
			if ($choices[0]['a3'] != "") {
				$correct_ans .= "3a|||";
			}
			if ($choices[0]['a4'] != "") {
				$correct_ans .= "4a|||";
			}
			if ($choices[0]['a5'] != "") {
				$correct_ans .= "5a|||";
			}
			if ($choices[0]['a6'] != "") {
				$correct_ans .= "6a|||";
			}
			if ($choices[0]['a7'] != "") {
				$correct_ans .= "7a|||";
			}
			if ($choices[0]['a8'] != "") {
				$correct_ans .= "8a|||";
			}
			if ($choices[0]['a9'] != "") {
				$correct_ans .= "9a|||";
			}
			if ($choices[0]['a10'] != "") {
				$correct_ans .= "10a|||";
			}
			$result_allans[$value->question_id] = $correct_ans;
		}
		return $result_allans;
	}
	function getAllAnsText($quiz_id, $id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT question_id FROM #__guru_quiz_question_taken_v3 qq, #__guru_questions_v3 q  WHERE qq.question_id=q.id and show_result_quiz_id=" . $id . " ORDER BY reorder";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$i = 0;
		foreach ($result as $key => $value) {
			$sql = "SELECT a1, a2,a3,a4,a5,a6,a7,a8,a9,a10 FROM #__guru_questions_v3 WHERE qid=" . intval($quiz_id) . " and id=" . $value->question_id;
			$db->setQuery($sql);
			$choices = $db->loadAssocList();
			$correct_ans = "";

			if (!isset($choices["0"])) {
				continue;
			}

			if ($choices[0]['a1'] != "") {
				$correct_ans .= $choices[0]['a1'] . "|||";
			}
			if ($choices[0]['a2'] != "") {
				$correct_ans .= $choices[0]['a2'] . "|||";
			}
			if ($choices[0]['a3'] != "") {
				$correct_ans .= $choices[0]['a3'] . "|||";
			}
			if ($choices[0]['a4'] != "") {
				$correct_ans .= $choices[0]['a4'] . "|||";
			}
			if ($choices[0]['a5'] != "") {
				$correct_ans .= $choices[0]['a5'] . "|||";
			}
			if ($choices[0]['a6'] != "") {
				$correct_ans .= $choices[0]['a6'] . "|||";
			}
			if ($choices[0]['a7'] != "") {
				$correct_ans .= $choices[0]['a7'] . "|||";
			}
			if ($choices[0]['a8'] != "") {
				$correct_ans .= $choices[0]['a8'] . "|||";
			}
			if ($choices[0]['a9'] != "") {
				$correct_ans .= $choices[0]['a9'] . "|||";
			}
			if ($choices[0]['a10'] != "") {
				$correct_ans .= $choices[0]['a10'] . "|||";
			}
			$result_allans[$value->question_id] = $correct_ans;
		}
		return $result_allans;
	}

	function getQuestionName($id, $quiz_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT id, text FROM #__guru_questions_v3 WHERE qid=" . intval($quiz_id) . " ORDER BY reorder";
		$db->setQuery($sql);
		$result_question = $db->loadObjectList("id");
		return $result_question;
	}

	function saveorder()
	{
		$db = JFactory::getDBO();
		$data = JFactory::getApplication()->input->post->getArray();
		$ok = true;

		if ($data['task'] == 'saveorder') {
			// Combine the ids with their ordering numbers
			$order = array_combine($data['cid'], $data['order']);
			// Sort ascending the order array
			asort($order);
			// The new value for each item [will be auto-incremented below]
			$new_val = 0;

			foreach ($order as $key => $value) {
				$sql = "UPDATE #__guru_quiz SET ordering = '" . $new_val . "' WHERE id=" . $key;
				$sqlz[] = $sql;
				$db->setQuery($sql);
				if (!$db->execute()) {
					$ok = false;
				}
				$new_val++;
			}
		} elseif ($data['task'] == 'orderup' || $data['task'] == 'orderdown') {
			$current_item['id'] = (int) $data['cid'][0];

			$sql = "SELECT ordering FROM #__guru_quiz WHERE id = {$current_item['id']}";
			$db->setQuery($sql);
			$current_item['ordering'] = $db->loadResult();

			$compare = ($data['task'] == 'orderup') ? '<' : '>';
			$desc_or_asc = ($data['task'] == 'orderup') ? 'DESC' : 'ASC';

			$sql = "SELECT id, ordering FROM #__guru_quiz WHERE ordering " . $compare . " {$current_item['ordering']} ORDER BY ordering " . $desc_or_asc . " LIMIT 1";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$previous_item = $db->loadAssoc();

			// If we have a previous/next item, interchange the 2
			if (!empty($previous_item)) {

				// Update ordering for the current item
				$sql = "UPDATE #__guru_quiz SET ordering = '{$previous_item['ordering']}' WHERE id = {$current_item['id']}";
				$sqlz[] = $sql;
				$db->setQuery($sql);
				if (!$db->execute()) {
					$ok = false;
				}

				// Update ordering for the current item
				$sql = "UPDATE #__guru_quiz SET ordering = '{$current_item['ordering']}' WHERE id = {$previous_item['id']}";
				$sqlz[] = $sql;
				$db->setQuery($sql);
				if (!$db->execute()) {
					$ok = false;
				}
			}
		}
		return $ok;
	}

	function saveOrderQuest()
	{
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$cid = array_values($cids);
		$order = JFactory::getApplication()->input->get('order', array(0), "raw");
		$order = array_values($order);
		$total = count($cid);

		$order_poz = 1;

		for ($i = 0; $i < $total; $i++) {
			$sql = "update #__guru_questions_v3 set question_order=" . intval($order_poz) . " where id=" . intval(@$cid[$i]);
			echo $sql . "<br />";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}

			$order_poz++;
		}

		return true;
	}

	function getquiz()
	{
		$jnow = new JDate('now');

		if (empty($this->_attribute)) {
			$this->_attribute = $this->getTable("guruQuiz");
			$this->_attribute->load($this->_id);
		}

		$data = JFactory::getApplication()->input->post->getArray();

		if (!$this->_attribute->bind($data)) {
			$this->setError($item->getError());
			return false;
		}
		if (!$this->_attribute->check()) {
			$this->setError($item->getError());
			return false;
		}

		if ($this->_attribute->id <= 0) {
			$this->_attribute->text = JText::_('GURU_NEW_Q_BTN');
			$this->_attribute->published = 1;
			$this->_attribute->startpublish =  $jnow->toSQL();
		} else $this->_attribute->text = JText::_('GURU_EDIT_Q_BTN');

		if (substr($this->_attribute->endpublish, 0, 4) == '0000' || $this->_attribute->id < 1)
			$this->_attribute->endpublish = JText::_('GURU_NEVER');


		if (!isset($this->_attribute->published)) {
			$this->_attribute->published = 1;
		}
		$this->_attribute->lists['published'] = '<input type="hidden" name="published" value="0">';
		if ($this->_attribute->published == 0) {
			$this->_attribute->lists['published'] .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published">';
		} else {
			$this->_attribute->lists['published'] .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		$this->_attribute->lists['published'] .= '<span class="lbl"></span>';

		//start get author list
		$db = JFactory::getDBO();
		$sql = "SELECT u.id, u.name FROM #__users u, #__guru_authors la where u.id=la.userid";
		$db->setQuery($sql);
		$result_authors = $db->loadObjectList();

		$author_listl = array();
		$author_listl[] = JHTML::_("select.option", JText::_('GURU_SELECT'), "0");
		for ($i = 0; $i < count($result_authors); $i++) {
			$author_listl[] = JHTML::_("select.option", $result_authors[$i]->name, $result_authors[$i]->id);
		}
		$this->_attribute->lists['author'] = JHTML::_("select.genericlist", $author_listl, "author", "", "text", "value", $this->_attribute->author);


		return $this->_attribute;
	}

	function getQuizById()
	{
		$db = Jfactory::getDBO();
		$query = "select * from #__guru_quiz where id=" . intval($this->_id) . " limit 1";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}

	function getMedia()
	{
		$db = Jfactory::getDBO();
		$media = new StdClass;

		$app = JFactory::getApplication('admin');
		$limit = $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get("limitstart", "0", "raw");
		if ($this->_id == 0) {
			$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE qid='0' ");

			$media->mmediam = $db->loadObjectList();
			$media->max_reo = 0;
			$media->min_reo = 0;
			$media->mainmedia = 0;
		} else {
			$db->setQuery("SELECT count(*) FROM #__guru_questions_v3 WHERE qid='" . $this->_id . "'");
			$total = $db->loadColumn();
			$total = @$total["0"];
			$media->total = $total;

			$sql_limit = "LIMIT " . $limitstart . "," . $limit;
			if (intval($limit) == 0) {
				$sql_limit = "";
			}
			$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE qid='" . $this->_id . "' ORDER BY question_order " . $sql_limit);
			$media->mmediam = $db->loadObjectList();
			$db->setQuery("SELECT id FROM #__guru_questions_v3 WHERE qid = '" . $this->_id . "' ORDER BY question_order  DESC LIMIT 1");
			$media->max_reo = $db->loadResult();
			$db->setQuery("SELECT id FROM #__guru_questions_v3 WHERE qid = '" . $this->_id . "' ORDER BY question_order  ASC LIMIT 1");
			$media->min_reo = $db->loadResult();
			$db->setQuery("SELECT * FROM #__guru_media WHERE id in (SELECT media_id FROM #__guru_mediarel WHERE type='qmed' AND type_id = " . $this->_id . ") ");
			$media->mainmedia = $db->loadObjectList();
		}
		return $media;
	}

	public static function QuestionNo($id)
	{
		$db = JFactory::getDBO();

		$sql = "select is_final from #__guru_quiz where id=" . intval($id);
		$db->setQuery($sql);
		$is_final = $db->loadResult();
		$is_final = @$is_final["0"];

		if ($is_final == 1) { // final exam
			$sql = "select nb_quiz_select_up from #__guru_quiz where id=" . intval($id);
			$db->setQuery($sql);
			$tmp = $db->loadResult();
			return $tmp;
		} else {
			$sql = "select count(id) from #__guru_questions_v3 where qid=" . $id;
			$db->setQuery($sql);
			$tmp = $db->loadResult();
			return $tmp;
		}
	}

	function store()
	{
		$app = JFactory::getApplication('administrator');
		$item = $this->getTable('guruQuiz');

		$data = JFactory::getApplication()->input->post->getArray();

		if (trim($data["id"]) == "") {
			$data["id"] = 0;
		}

		if (trim($data["endpublish"]) == "") {
			$data["endpublish"] = "0000-00-00 00:00:00";
		}

		if (trim($data["student_failed_quiz"]) == "") {
			$data["student_failed_quiz"] = 0;
		}

		if (!isset($data["final_quiz"])) {
			$data["final_quiz"] = 0;
		}

		$pass_message = JFactory::getApplication()->input->get("pass_message", "", "raw");
		$fail_message = JFactory::getApplication()->input->get("fail_message", "", "raw");
		$pending_message = JFactory::getApplication()->input->get("pending_message", "", "raw");

		$data['pass_message'] = $pass_message;
		$data['fail_message'] = $fail_message;
		$data['pending_message'] = $pending_message;

		$db = JFactory::getDBO();

		$data['description'] = $data['description'];
		$data['startpublish'] = date('Y-m-d H:i:s', strtotime($data['startpublish']));

		if ($data['endpublish'] != JText::_('GURU_NEVER') && trim($data['endpublish']) != "" && $data['endpublish'] != "0000-00-00 00:00:00") { // calendar change
			$data['endpublish'] = date('Y-m-d H:i:s', strtotime($data['endpublish']));
		}

		$res = true;

		if (!$item->bind($data)) {
			$res = false;
		}

		if (!$item->check()) {
			$res = false;
		}

		if (!$item->store()) {
			$res = false;
		} else {
			$this->_id = $item->id;
		}

		$app->setUserState('new_quiz_id', $item->id);

		$new_quiz = 0;
		if ($data['id'] == "") {
			$data['id'] = $item->id;
			$app->setUserState('new_quiz_id', $data['id']);
		}

		if ($data['id'] == 0) {
			$ask = "SELECT id FROM #__guru_quiz ORDER BY id DESC LIMIT 1 ";
			$db->setQuery($ask);
			$data['id'] = $db->loadResult();
			$new_quiz = 1;
		}
		$quizid = $data['id'];

		$md = "SELECT id FROM #__guru_media WHERE source='" . $quizid . "' ORDER BY id DESC LIMIT 1";
		$db->setQuery($md);
		$md_id = $db->loadResult();

		if (@$data['valueop'] == 1) {
			//Save settings for quiz timer 
			$sql = "UPDATE #__guru_quiz SET max_score='" . $data['max_score_pass'] . "', pbl_max_score='" . $data['show_max_score_pass'] . "', time_quiz_taken='" . $data['nb_quiz_taken'] . "', show_nb_quiz_taken='" . $data['show_nb_quiz_taken'] . "', nb_quiz_select_up='" . $data['nb_quiz_select_up'] . "', show_nb_quiz_select_up='" . $data['show_nb_quiz_select_up'] . "', final_quiz= '" . $data['final_quiz'] . "', limit_time='" . $data['limit_time_l'] . "', limit_time_f = '" . $data['limit_time_f'] . "', show_finish_alert = '" . $data['show_finish_alert'] . "', student_failed_quiz = '" . $data['student_failed_quiz'] . "', is_final ='1' WHERE id='" . $quizid . "'";
			$db->setQuery($sql);
			$db->execute();
		} else {
			$sql = "UPDATE #__guru_quiz SET max_score='" . $data['max_score_pass'] . "', pbl_max_score='" . $data['show_max_score_pass'] . "', time_quiz_taken='" . $data['nb_quiz_taken'] . "', show_nb_quiz_taken='" . $data['show_nb_quiz_taken'] . "', nb_quiz_select_up='" . $data['nb_quiz_select_up'] . "', show_nb_quiz_select_up='" . $data['show_nb_quiz_select_up'] . "', final_quiz= '" . @$data['final_quiz'] . "', limit_time='" . $data['limit_time_l'] . "', limit_time_f = '" . $data['limit_time_f'] . "', show_finish_alert = '" . $data['show_finish_alert'] . "', student_failed_quiz = '" . @$data['student_failed_quiz'] . "',  is_final ='0'  WHERE id='" . $quizid . "'";
			$db->setQuery($sql);
			$db->execute();
		}
		//END Save settings for quiz timer 

		if (!$md_id) {
			$sql = "INSERT INTO #__guru_media (id ,name ,instructions ,type ,source ,uploaded ,code ,url ,local ,width ,height ,published, option_video_size, category_id, auto_play, show_instruction, author) VALUES (NULL , '" . addslashes($data['name']) . "', '" . addslashes($data['description']) . "', 'quiz', '" . $quizid . "', '0', NULL , NULL , NULL , '0', '0', '" . $data['published'] . "', 0, 0, 0, 0, 0);";
		} else {
			$sql = "UPDATE #__guru_media SET name = '" . addslashes($data['name']) . "',instructions = '" . addslashes($data['description']) . "',published = '" . $data['published'] . "' WHERE source = '" . $quizid . "' LIMIT 1 ;";
		}
		$db->setQuery($sql);
		$db->execute();


		if ($new_quiz && @$data['valueop'] == 0) {
			$sql = "UPDATE #__guru_questions_v3 SET 
						qid = '" . $quizid . "'
						WHERE qid ='0' ";
			$db->setQuery($sql);
			if (!$db->execute()) {
				//$this->setError($db->getErrorMsg());
				return false;
			}
		} elseif (@$data['valueop'] == 1) {
			$sql = "UPDATE #__guru_quizzes_final SET 
						qid = '" . $quizid . "'
						WHERE qid ='0' ";
			$db->setQuery($sql);
			if (!$db->execute()) {
				//$this->setError($db->getErrorMsg());
				return false;
			}
		}

		if (isset($data['mediafiles'])) {
			//delete old records
			if ($data['id'] > 0) {
				$db->setQuery("DELETE FROM #__guru_mediarel WHERE type='qmed' AND type_id='" . $data['id'] . "'");
				$db->execute();
			}
			//delete end
			$ask = "SELECT id FROM #__guru_quiz ORDER BY id DESC LIMIT 1 ";
			$db->setQuery($ask);
			$data['id'] = $db->loadResult();

			$progid = $data['id'];

			$thefiles = explode(',', $data['mediafiles']);

			if (isset($data['mediafiletodel']))
				$id_tmp_med_files_2_remove = explode(',', $data['mediafiletodel']);

			foreach ($thefiles as $files) {
				if (intval($files) > 0 && !in_array($files, $id_tmp_med_files_2_remove)) {
					$db->setQuery("INSERT INTO #__guru_mediarel (id,type,type_id,media_id,mainmedia) VALUES ('','qmed','" . $progid . "','" . $files . "','0')");
					$db->execute();
				}
			}
		} // end if		

		if (isset($data['deleteq']) && trim($data['deleteq']) != "") {
			$thefiles = explode(',', trim($data['deleteq'], ","));
			$new_quiz_added = explode(',', trim($data['new_quiz_added'], ","));

			foreach ($thefiles as $files) {
				if (in_array($files, $new_quiz_added)) {
					continue;
				}

				if (intval($files) > 0 && $data['valueop'] == 0) {
					$sql = "delete from #__guru_questions_v3 where id=" . $files;
					$db->setQuery($sql);
					$db->execute();
				} else {
					$sql = "select quizzes_ids from #__guru_quizzes_final where qid=" . $quizid . " order by id DESC LIMIT 0,1 ";
					$db->setQuery($sql);
					$result = $db->loadResult();
					$result_array = explode(",", $result);
					$data_deleteq_array = explode(",", $data['deleteq']);

					$temp_result_array = array();

					foreach ($result_array as $keyres => $valueres) {
						if (!in_array($valueres, $data_deleteq_array)) {
							$temp_result_array[] = $valueres;
						}
					}

					$newvalues = implode(",", $temp_result_array);

					if (trim($data['deleteq']) == "") {
						$newvalues = $data['new_quiz_added'];
					}

					$sql = "update #__guru_quizzes_final set quizzes_ids='" . $newvalues . "' where qid=" . $quizid;
					$db->setQuery($sql);
					$db->execute();
				} // end if
			} // end for
		} // end if
		else {
			$sql = "select count(*) from #__guru_quizzes_final where qid=" . $quizid;
			$db->setQuery($sql);
			$exist_final = $db->loadResult();

			if ($exist_final != 0 && trim($data['new_quiz_added']) != "") {
				$sql = "update #__guru_quizzes_final set quizzes_ids='" . $data['new_quiz_added'] . "' where qid=" . $quizid;
				$db->setQuery($sql);
				$db->execute();
			} else {
				if (trim($data['new_quiz_added']) != "") {
					$sql = "insert into #__guru_quizzes_final (`quizzes_ids`, `qid`, `published`) values ('" . $data['new_quiz_added'] . "', '" . intval($quizid) . "', '1')";
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}

		if (isset($data['order_q'])) {
			foreach ($data['order_q'] as $key => $value) {
				if (isset($data['publish_q'][$key])) {
					$published_cond = ",published = '" . $data['publish_q'][$key] . "'";
				}
				$sql = "UPDATE #__guru_questions_v3 SET 
						reorder = '" . intval($value) . "'" . $published_cond . "
						WHERE id ='" . $key . "' ";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		return $res;
	}
	public static function getAmountQuestions($id)
	{
		$db = Jfactory::getDBO();
		$query = "select count(id) from #__guru_questions_v3 where qid=" . intval($id);
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}


	public static function getAmountQuizzes($id)
	{
		$db = Jfactory::getDBO();
		$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=" . $id;
		$db->setQuery($sql);
		$result = $db->loadColumn();
		@$result_qids = explode(",", trim($result["0"], ","));
		if (isset($result_qids[0]) && $result_qids[0] != "") {
			$query = "select count(id) from #__guru_questions_v3 where qid IN (" . implode(",", $result_qids) . ")";
			$db->setQuery($query);
			$result = $db->loadResult();
			return $result;
		}
	}


	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			if (!$this->_total) {
				$task = JFactory::getApplication()->input->get("task", "", "raw");
				if ($task == "listQuizStud") {
					$this->getlistQuizTakenStud();
				} else {
					$this->getlistQuiz();
				}
			}
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}

	function more_media_files($ids)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_media WHERE id in (" . $ids . ") GROUP BY media_id");
		$more_media_files = $db->loadObjectList();
		$this->more_media_files = $more_media_files;
		return true;
	}

	function existing_ids($ids)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = " . $ids . " AND type='qmed' ");
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}

	function upload()
	{
		//$absolutepath = JPATH_SITE;
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		//get the image folder 

		$sqla = "SELECT imagesin FROM #__guru_config LIMIT 1";
		$db->setQuery($sqla);
		$imgfolder = $db->loadResult();
		$targetPath = JPATH_SITE . '/' . $imgfolder . '/';
		$failed = '0';

		$file_request = JFactory::getApplication()->input->files->get('image_file', NULL);

		if (isset($file_request)) {

			$filename = $file_request['name'];
			if ($filename) {

				$filenameParts = explode('.', $filename);
				$extension = '';
				if (count($filenameParts) > 1)
					$extension = array_pop($filenameParts);
				$extension = strtolower($extension);
				if (!in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))) {
					//mosErrorAlert("The image must be gif, png, jpg, jpeg, swf");
					$text = strip_tags(addslashes(nl2br("The image must be gif, png, jpg, jpeg.")));
					echo "<script>alert('$text'); </script>";
					$failed = 1;
				}
				if ($failed != 1) {
					if (!move_uploaded_file($file_request['tmp_name'], $targetPath . $filename)) {
						//mosErrorAlert("Upload of ".$filename." failed");
						$text = strip_tags(addslashes(nl2br("Upload of " . $filename . " failed.")));
						echo "<script>alert('$text'); </script>";
					} else {
						return $filename;
					}
				}
			}
		}
	}

	function delete()
	{
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$item = $this->getTable('guruQuiz');
		$database = JFactory::getDBO();

		$sql = "SELECT id_final_exam FROM #__guru_program";
		$database->setQuery($sql);
		$database->execute();
		$existingfinalexam_ids = $database->loadColumn();

		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		if (!$database->execute()) {
			return;
		}
		$imagesin = $database->loadResult();

		foreach ($cids as $cid) {
			$sql = "select count(*) from #__guru_mediarel where type='scr_m' and media_id=" . intval($cid) . " and layout=12";
			$database->setQuery($sql);
			$database->execute();
			$count = $database->loadColumn();
			$count = @$count["0"];

			if (intval($count) > 0) {
				return "assigned";
			}

			if (in_array($cid, $existingfinalexam_ids)) {
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('is_atribuited', "1");

				return false;
			}

			$sql = "SELECT image FROM #__guru_quiz WHERE id =" . $cid;
			$database->setQuery($sql);
			if (!$database->execute()) {
				return;
			}
			$image = $database->loadResult();

			if (!$item->delete($cid)) {
				$this->setError($item->getError());
				return false;
			}

			$query = "DELETE FROM #__guru_questions_v3 WHERE qid = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();

			$query = "SELECT id FROM #__guru_media WHERE source = '" . $cid . "'";
			$database->setQuery($query);
			$med_id = $database->loadResult();

			if ($med_id) {
				$query = "DELETE FROM #__guru_mediarel WHERE media_id = '" . $med_id . "'";
				$database->setQuery($query);
				$database->execute();

				$query = "DELETE FROM #__guru_media WHERE id = '" . $med_id . "'";
				$database->setQuery($query);
				$database->execute();
			}

			$query = "DELETE FROM #__guru_mediarel WHERE type = 'qmed' AND type_id = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();

			$query = "DELETE FROM #__guru_mediarel WHERE type = 'tquiz' AND media_id = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();

			$targetPath = JPATH_SITE . '/' . $imagesin . '/';
			unlink($targetPath . $image);
		}
		return true;
	}

	function getreportsAdvertisers()
	{
		if (empty($this->_package)) {
			$db = JFactory::getDBO();
			$sql = "SELECT a.aid, a.company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY a.company ASC";
			$db->setQuery($sql);
			$this->_package = $db->loadObjectList();
		}
		return $this->_package;
	}

	function addquestion($qtext, $quizid, $question_type, $media_ids, $points, $true_false_ch, $question_id, $from_save_or_not, $ans_content)
	{
		$db = JFactory::getDBO();

		if (intval($question_id) == 0) {
			$query = 'SELECT MAX( question_order ) FROM #__guru_questions_v3 WHERE qid ="' . $quizid . '" ';
			$db->setQuery($query);
			$reorder = $db->loadResult();
			$reorder = intval($reorder) + 1;
			$media_ids1 = json_encode($media_ids);

			$sql = "INSERT INTO #__guru_questions_v3 (qid, type, question_content, media_ids, points, published, question_order) VALUES ('" . $quizid . "','" . addslashes($question_type) . "', '" . addslashes($qtext) . "' , '" . $media_ids1 . "' , " . $points . ", '1', " . $reorder . ");";
			$db->setQuery($sql);

			if (!$db->execute()) {
				return false;
			}
		} else {
			$media_ids1 = json_encode($media_ids);
			$sql = "UPDATE #__guru_questions_v3 SET question_content = '" . addslashes($qtext) . "', media_ids = '" . $media_ids1 . "',points = " . $points . " WHERE id =" . $question_id . " LIMIT 1";
			$db->setQuery($sql);
			if (!$db->execute()) {
				//$this->setError($db->getErrorMsg());
				return false;
			}
		}

		$query = 'SELECT MAX(id) FROM #__guru_questions_v3';
		$db->setQuery($query);
		$id_question = $db->loadColumn();

		$correct_ans_true = '-1';
		$correct_ans_false = '-1';

		// start true/false question
		if ($question_type == 'true_false') {
			$choose_answer = JFactory::getApplication()->input->get("truefs_ans", "-1", "raw");
			if ($choose_answer == 1) {
				$correct_ans_true = 0;
				$correct_ans_false = 1;
			} elseif ($choose_answer == 0) {
				$correct_ans_true = 1;
				$correct_ans_false = 0;
			}

			if (intval($question_id) == 0) {
				$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes(JText::_("GURU_QUESTION_OPTION_TRUE")) . "', '' , '" . addslashes($correct_ans_true) . "'," . intval($id_question["0"]) . ");";
				$db->setQuery($sql);
				if (!$db->execute()) {
					//$this->setError($db->getErrorMsg());
					return false;
				}

				$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes(JText::_("GURU_QUESTION_OPTION_FALSE")) . "', '' , '" . addslashes($correct_ans_false) . "', " . intval($id_question["0"]) . ");";
				$db->setQuery($sql);
				if (!$db->execute()) {
					//$this->setError($db->getErrorMsg());
					return false;
				}
			} else {
				$sql = "UPDATE #__guru_question_answers set correct_answer= '" . addslashes($correct_ans_true) . "' where answer_content_text='" . addslashes(JText::_("GURU_QUESTION_OPTION_TRUE")) . "' and question_id=" . intval($question_id);
				$db->setQuery($sql);
				if (!$db->execute()) {
					//$this->setError($db->getErrorMsg());
					return false;
				}

				$sql = "UPDATE #__guru_question_answers set correct_answer= '" . addslashes($correct_ans_false) . "' where answer_content_text='" . addslashes(JText::_("GURU_QUESTION_OPTION_FALSE")) . "' and question_id=" . intval($question_id);
				$db->setQuery($sql);
				if (!$db->execute()) {
					//$this->setError($db->getErrorMsg());
					return false;
				}
			}
		}

		// start single choice question
		if ($question_type == 'single') {
			$correct_ans = JFactory::getApplication()->input->get("correct_ans", array(), "raw");
			$ans_media_ids = JFactory::getApplication()->input->get("ans_media_ids", array(), "raw");

			$sql = "select id from #__guru_question_answers where question_id=" . intval($question_id);
			$db->setQuery($sql);
			$saved_answers_ids = $db->loadColumn();

			if (isset($saved_answers_ids) && count($saved_answers_ids) > 0) {
				$saved_answers_ids_temp = array();

				foreach ($saved_answers_ids as $key => $id) {
					if (trim($ans_content[$id]["0"]) == "" && trim($ans_media_ids[$id]["0"]) == "") {
						$saved_answers_ids_temp[] = $id;
					}
				}

				$saved_answers_ids = $saved_answers_ids_temp;

				if (is_array($saved_answers_ids) && count($saved_answers_ids) > 0) {
					$sql = "delete from #__guru_question_answers where id in (" . implode(",", $saved_answers_ids) . ") and question_id=" . intval($question_id);
					$db->setQuery($sql);
					$db->execute();
				}
			}

			if (isset($ans_content) && is_array($ans_content) && count($ans_content) > 0) {
				foreach ($ans_content as $key => $value) {
					if (intval($ans_media_ids[$key]["0"]) == 0 && trim($value["0"]) == "") {
						continue;
					}

					$correct = "0";

					if ($correct_ans["0"] == $key) {
						$correct = "1";
					}

					$media = "";
					if (isset($ans_media_ids[$key]) && intval($ans_media_ids[$key]["0"]) != 0) {
						$media = json_encode($ans_media_ids[$key]);
					}

					if (intval($question_id) == 0) {
						$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes($value["0"]) . "', '" . addslashes($media) . "' , '" . addslashes($correct) . "'," . intval($id_question["0"]) . ");";
						$db->setQuery($sql);

						if (!$db->execute()) {
							//$this->setError($db->getErrorMsg());
							return false;
						}
					} else {
						$sql = "";

						if ($this->isNewAnswer($key, $question_id)) {
							$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . trim(addslashes($value["0"])) . "', '" . addslashes($media) . "', '" . addslashes($correct) . "', " . intval($question_id) . ")";
						} else {
							$sql = "UPDATE #__guru_question_answers set correct_answer= '" . addslashes($correct) . "', media_ids='" . addslashes($media) . "',answer_content_text='" . trim(addslashes($value["0"])) . "' where id=" . intval($key);
						}

						$db->setQuery($sql);
						if (!$db->execute()) {
							//$this->setError($db->getErrorMsg());
							return false;
						}
					}
				}
			}
		}

		// start multiple choice question
		if ($question_type == 'multiple') {
			$correct_ans = JFactory::getApplication()->input->get("correct_ans", array(), "raw");
			$ans_media_ids = JFactory::getApplication()->input->get("ans_media_ids", array(), "raw");

			$sql = "select id from #__guru_question_answers where question_id=" . intval($question_id);
			$db->setQuery($sql);
			$saved_answers_ids = $db->loadColumn();

			if (isset($saved_answers_ids) && count($saved_answers_ids) > 0) {
				$saved_answers_ids_temp = array();

				foreach ($saved_answers_ids as $key => $id) {
					if (trim($ans_content[$id]["0"]) == "" && trim($ans_media_ids[$id]["0"]) == "") {
						$saved_answers_ids_temp[] = $id;
					}
				}

				$saved_answers_ids = $saved_answers_ids_temp;

				if (is_array($saved_answers_ids) && count($saved_answers_ids) > 0) {
					$sql = "delete from #__guru_question_answers where id in (" . implode(",", $saved_answers_ids) . ") and question_id=" . intval($question_id);
					$db->setQuery($sql);
					$db->execute();
				}
			}

			if (isset($ans_content) && is_array($ans_content) && count($ans_content) > 0) {
				foreach ($ans_content as $key => $value) {
					if (intval($ans_media_ids[$key]["0"]) == 0 && trim($value["0"]) == "") {
						continue;
					}

					$correct = "0";
					if (in_array($key, $correct_ans)) {
						$correct = "1";
					}

					$media = "";
					if (isset($ans_media_ids[$key])) {
						$media = json_encode($ans_media_ids[$key]);
					}

					if (intval($question_id) == 0) {
						$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes($value["0"]) . "', '" . addslashes($media) . "' , '" . addslashes($correct) . "'," . intval($id_question["0"]) . ");";
						$db->setQuery($sql);
						if (!$db->execute()) {

							//$this->setError($db->getErrorMsg());
							return false;
						}
					} else {
						$sql = "";
						if ($this->isNewAnswer($key, $question_id)) {
							$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes($value["0"]) . "', '" . addslashes($media) . "' , '" . addslashes($correct) . "'," . intval($question_id) . ");";
						} else {
							$sql = "UPDATE #__guru_question_answers set correct_answer= '" . addslashes($correct) . "', media_ids='" . addslashes($media) . "',answer_content_text='" . addslashes($value["0"]) . "' where id=" . intval($key);
						}

						$db->setQuery($sql);
						if (!$db->execute()) {
							//$this->setError($db->getErrorMsg());
							return false;
						}
					}
				}
			}
		}

		if ($from_save_or_not == 'saveandclose') {
			return true;
		} elseif ($from_save_or_not == 'savekeep') {
			if (intval($question_id) == 0) {
				return $id_question["0"];
			} else {
				return $question_id;
			}
		}
	}

	function isNewAnswer($ans_id, $question_id)
	{
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_question_answers where id=" . intval($ans_id) . " and question_id=" . intval($question_id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = @$result["0"];
		if (intval($result) == 0) {
			return true;
		}
		return false;
	}

	function delquestion($id, $qid)
	{
		$db = JFactory::getDBO();

		$sql = "delete from #__guru_questions_v3 where id=" . $id . " and qid=" . $qid;

		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	function addmedia($toinsert, $taskid, $mainmedia)
	{
		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__guru_mediarel ( id , type , type_id , media_id , mainmedia ) VALUES ('', 'qmed', '" . $taskid . "' , '" . $toinsert . "', '" . $mainmedia . "');";
		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}

	function delmedia($tid, $cid)
	{
		$db = JFactory::getDBO();

		$sql = "delete from #__guru_mediarel where type='qmed' and type_id=" . $tid . " and media_id=" . $cid;

		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	public static function id_for_last_question()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT max(id) FROM #__guru_questions_v3 ";
		$db->setQuery($sql);
		$id = $db->loadResult();
		return $id;
	}

	function publish()
	{
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '', "raw");
		$id = 0;

		if (is_array($cids) && isset($cids["0"])) {
			$id = $cids["0"];
		}

		$sql = "SELECT is_final from #__guru_quiz where id='" . intval($id) . "' ";
		$db->setQuery($sql);
		$id = $db->loadResult();

		if ($task == 'publish') {
			$sql = "update #__guru_quiz set published='1' where id in ('" . implode("','", $cids) . "')";
			if ($id == 0) {
				$ret = 1;
			} else {
				$ret = 2;
			}
		} else {
			$sql = "update #__guru_quiz set published='0' where id in ('" . implode("','", $cids) . "')";
			if ($id == 0) {
				$ret = -1;
			} else {
				$ret = -2;
			}
		}

		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return $ret;
	}

	function cancel()
	{
		$data_post = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$sql = "delete from #__guru_questions_v3 where qid='0' ";
		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}

		if (isset($data_post['newquizq'])) {
			$thefiles = $data_post['newquizq'];
			$thefiles = explode(',', $thefiles);
			foreach ($thefiles as $files) {
				if (intval($files) > 0) {
					$db->setQuery("delete from #__guru_questions_v3 where id='" . $files . "'");
					$db->execute();
				}
			}
		}

		return 1;
	}

	function removequizresults()
	{
		$quiz_id = JFactory::getApplication()->input->get('cid', "", "raw");
		$db = JFactory::getDBO();
		$sql = "delete from #__guru_quiz_question_taken_v3 where quiz_id=" . intval($quiz_id["0"]);
		$db->setQuery($sql);
		$db->execute();

		$sql = "delete from #__guru_quiz_taken_v3 where quiz_id=" . intval($quiz_id["0"]);
		$db->setQuery($sql);

		if ($db->execute()) {
			return 1;
		}
	}


	public static function getConfigs()
	{
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__guru_config LIMIT 1";

		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}
		$result = $db->loadObject();
		return $result;
	}

	function checkbox_construct($rowNum, $recId, $name = 'cid')
	{
		$db = JFactory::getDBO();

		$sql = " SELECT media_id FROM #__guru_mediarel WHERE type_id in ( SELECT media_id FROM #__guru_mediarel WHERE type_id in (SELECT id FROM #__guru_days WHERE pid in (SELECT id FROM #__guru_order GROUP BY id)) AND type = 'dtask' GROUP BY media_id ) AND  type = 'tquiz' ";

		$db->setQuery($sql);
		$result = $db->loadColumn();

		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		$influence = $db->loadResult(); // we have selected the INFLUENCE 		
		if (($influence == 0 && in_array($recId, $result))) {
			$not = 'not';
			$disabled = 'disabled="disabled"';
		} else {
			$disabled = '';
			$not = '';
		}

		return '<input type="checkbox" id="' . $not . 'cb' . $rowNum . '" ' . $disabled . '" name="' . $name . '[]" value="' . $recId . '" onclick="isChecked(this.checked);" />$$$$$' . $disabled;
	}

	function duplicate()
	{

		$cid	= JFactory::getApplication()->input->get('cid', array(), "raw");
		$n		= count($cid);
		if ($n == 0) {
			return JFactory::getApplication()->enqueueMessage(JText::_('No items selected'), 'error');
		}

		foreach ($cid as $id) {
			$row 	= $this->getTable('guruQuiz');
			$db = JFactory::getDBO();
			// load the row from the db table
			$row->load((int) $id);

			$row->name = JText::_('GURU_Q_COPY_TITLE') . ' ' . $row->name;
			if ($row->image != '') {
				$sql = "SELECT imagesin FROM #__guru_config WHERE id = 1";
				$db->setQuery($sql);
				$configs = $db->loadResult();
				copy(JPATH_SITE . '/' . $configs . '/' . $row->image, JPATH_SITE . '/' . $configs . '/copy_' . $row->image);
				$row->image = 'copy_' . $row->image;
			}
			$old_quiz_id = $row->id;

			$row->id = 0;

			if (!$row->check()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			if (!$row->store()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			$row->checkin();
			unset($row);

			$isfinal = "SELECT is_final FROM #__guru_quiz WHERE id= " . $old_quiz_id;
			$db->setQuery($isfinal);
			$isfinal = $db->loadColumn();
			$isfinal = $isfinal[0];

			if ($isfinal == 0) {
				$ask = "SELECT id FROM #__guru_questions_v3 WHERE qid= " . $old_quiz_id;
				$db->setQuery($ask);
				$question_array = $db->loadColumn();
			}

			$sql = "SELECT max(id) FROM #__guru_quiz ";
			$db->setQuery($sql);
			$new_quiz_id = $db->loadColumn();
			$new_quiz_id = $new_quiz_id[0];

			$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=" . $id;
			$db->setQuery($sql);
			$result_fq = $db->loadColumn();

			if ($isfinal == 0) {
				foreach ($question_array as $question) {
					$sql = "SELECT * FROM #__guru_questions_v3 WHERE id = " . $question;
					$db->setQuery($sql);
					$the_question_object = $db->loadObject();


					$sql = "SELECT * FROM #__guru_question_answers WHERE question_id = " . $question . " ORDER BY id";
					$db->setQuery($sql);
					$the_question_answers_object = $db->loadObjectList();


					$sql = "INSERT INTO #__guru_questions_v3 
															( 
																qid , 
																type , 
																question_content , 
																media_ids , 
																points ,
																published,
																question_order
													) VALUES (
																'" . $new_quiz_id . "', 
																'" . $the_question_object->type . "', 
																'" . addslashes($the_question_object->question_content) . "' , 
																'" . $the_question_object->media_ids . "', 
																'" . $the_question_object->points . "',
																'" . $the_question_object->published . "',
																'" . $the_question_object->question_order . "'									
															)";
					$db->setQuery($sql);
					if (!$db->execute()) {
						//$this->setError($db->getErrorMsg());
						return false;
					}

					$sql = "SELECT max(id) FROM #__guru_questions_v3 WHERE qid = " . $new_quiz_id;
					$db->setQuery($sql);
					$id_last_question = $db->loadColumn();

					foreach ($the_question_answers_object as $question_answer) {



						$sql = "INSERT INTO #__guru_question_answers 
																( 
																	answer_content_text , 
																	media_ids , 
																	correct_answer , 
																	question_id
														) VALUES (
																	'" . addslashes($question_answer->answer_content_text) . "', 
																	'" . addslashes($question_answer->media_ids) . "', 
																	'" . addslashes($question_answer->correct_answer) . "' , 
																	'" . $id_last_question["0"] . "'							
																)";
						$db->setQuery($sql);
						if (!$db->execute()) {
							//$this->setError($db->getErrorMsg());
							return false;
						}
					}
				}
			} else {
				$sql = "INSERT INTO #__guru_quizzes_final (quizzes_ids, qid, published)VALUES('" . $result_fq[0] . "', '" . $new_quiz_id . "',1)";
				$db->setQuery($sql);
				if (!$db->execute()) {
					//$this->setError($db->getErrorMsg());
					return false;
				}
			}
		}
		return 1;
	}

	function getMediaFromQuestion($mediaids)
	{
		$db = JFactory::getDBO();
		$mediaa_for_question = json_decode($mediaids);
		$list_of_media_names = array();
		if (is_array($mediaa_for_question) && count($mediaa_for_question) > 0) {
			foreach ($mediaa_for_question as $key => $value) {
				$sql = "SELECT id,name, type FROM #__guru_media WHERE id =" . intval($value);
				$db->setQuery($sql);
				$result = $db->loadAssocList();
				$list_of_media_names[@$result["0"]["id"]] = @$result["0"];
			}
		}
		return $list_of_media_names;
	}

	function getMediaFromAnswer($mediaids)
	{
		$db = JFactory::getDBO();
		$mediaa_for_question = json_decode($mediaids);
		$list_of_media_names = array();
		if (is_array($mediaa_for_question) && count($mediaa_for_question) > 0) {
			foreach ($mediaa_for_question as $key => $value) {
				$sql = "SELECT id,name, type FROM #__guru_media WHERE id =" . intval($value);
				$db->setQuery($sql);
				$result = $db->loadAssocList();
				$list_of_media_names[@$result["0"]["id"]] = @$result["0"];
			}
		}
		return $list_of_media_names;
	}

	function copyquestion()
	{
		$db = JFactory::getDbo();
		$qid = JFactory::getApplication()->input->get('qid', '0', "raw");
		$question = JFactory::getApplication()->input->get('question', '0', "raw");

		if (intval($question) > 0) {
			$sql = "select * from #__guru_questions_v3 where `id`=" . intval($question);
			$db->setQuery($sql);
			$old_question = $db->loadAssocList();

			if (isset($old_question) && count($old_question) > 0) {
				$sql = "select max(`question_order`) from #__guru_questions_v3 where `qid`=" . intval($qid);
				$db->setQuery($sql);
				$question_order = $db->loadColumn();
				$question_order = @$question_order["0"];
				$question_order = intval($question_order) + 1;

				$sql = "insert into #__guru_questions_v3 (`qid`, `type`, `question_content`, `media_ids`, `points`, `published`, `question_order`) values ('" . intval($old_question["0"]["qid"]) . "', '" . trim($old_question["0"]["type"]) . "', '" . trim($old_question["0"]["question_content"]) . " Copy', '" . trim($old_question["0"]["media_ids"]) . "', '" . intval($old_question["0"]["points"]) . "', '" . intval($old_question["0"]["published"]) . "', '" . intval($question_order) . "')";
				$db->setQuery($sql);
				$db->execute();

				$sql = "select max(`id`) from #__guru_questions_v3 where `qid`=" . intval($qid);
				$db->setQuery($sql);
				$max_id = $db->loadColumn();
				$max_id = @$max_id["0"];

				$sql = "select * from #__guru_question_answers where `question_id`=" . intval($question);
				$db->setQuery($sql);
				$answers = $db->loadAssocList();

				if (isset($answers) && count($answers) > 0) {
					foreach ($answers as $key => $value) {
						$sql = "insert into #__guru_question_answers (`answer_content_text`, `media_ids`, `correct_answer`, `question_id`) values ('" . $db->escape($value["answer_content_text"]) . "', '" . $db->escape($value["media_ids"]) . "', '" . intval($value["correct_answer"]) . "', '" . intval($max_id) . "')";
						$db->setQuery($sql);
						$db->execute();
					}
				}

				return true;
			}
		}

		return false;
	}
};
