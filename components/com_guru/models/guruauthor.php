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
jimport('joomla.filesystem.folder');

class guruModelguruauthor extends JModelList
{
	protected $_context = 'com_guru.guruauthor';
	var $_attribute;
	var $_id = null;
	var $_userid = null;
	var $return_array = array();
	var $_total = null;

	function __construct()
	{
		parent::__construct();
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->_id = $id;
		if ($id > 0) {
			$db = JFactory::getDBO();
			$query = "SELECT userid 
					FROM #__guru_authors
					WHERE id=" . intval($id);
			$db->setQuery($query);
			$result = $db->loadObject();
			if (isset($result)) {
				$this->_userid = $result->userid;
			}
		}
		$this->_attribute = null;
	}

	function checkAuthorProfile($user_id)
	{
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_authors where userid=" . intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if ($result["0"] > 0) {
			return true;
		} else {
			return false;
		}
	}

	function checkAuthorProfileEnabled($user_id)
	{
		$db = JFactory::getDBO();
		$sql = "select enabled from #__guru_authors where userid=" . intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if (@$result["0"] == 1) {
			return true;
		} else {
			return false;
		}
	}

	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState();
		// Add archive properties
		$app = JFactory::getApplication("site");
		$config = JFactory::getConfig();
		$itemid = JFactory::getApplication()->input->get('Itemid', 0, "raw");
		$limit = $app->getUserStateFromRequest('com_guru.guruauthor' . $itemid . '.limit', 'limit', $config->get('list_limit', 20));
		$this->setState('list.limit', $limit);

		$filter_search = $app->getUserStateFromRequest($this->_context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $filter_search);
	}

	function getListQuery()
	{
		$layout = JFactory::getApplication()->input->get("layout", "", "raw");
		if (trim($layout) == "" || trim($layout) == "index.php") {
			$layout = JFactory::getApplication()->input->get("task", "", "raw");
		}
		$sql = "";
		if ($layout == "mystudents") {
			$sql = $this->getStudents();
		} elseif ($layout == "authormycourses") {
			$sql = $this->getAuthorMyCourses();
		} elseif ($layout == "authormymedia") {
			$sql = $this->getAuthorMyMedia();
		} elseif ($layout == "authorquizzes") {
			$sql = $this->getAuthorMyQuizzes();
		} elseif ($layout == "authoressays") {
			$sql = $this->getAuthorMyEssays();
		}
		return $sql;
	}

	function getStudents()
	{
		$app = JFactory::getApplication("site");
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$action = JFactory::getApplication()->input->get("action", "", "raw");
		$id_quiz = JFactory::getApplication()->input->get("qid", "", "raw");
		$and = "";

		$sql = "select id from #__guru_program where author like '%|" . intval($user->id) . "|%' OR author='" . intval($user->id) . "'";
		$db->setQuery($sql);
		$courses_ids = $db->loadColumn();

		if ($action == "passed") {
			$sql = "SELECT max(id) FROM #__guru_quiz_question_taken_v3
					WHERE quiz_id=" . intval($id_quiz) . " GROUP BY user_id, quiz_id";
			$db->setQuery($sql);
			$result_latest_ids = $db->loadColumn();

			if (!is_array($result_latest_ids) || count($result_latest_ids) == 0) {
				$result_latest_ids = array("0");
			}

			$sql = "SELECT user_id, quiz_id, score_quiz
					FROM #__guru_quiz_question_taken_v3
					WHERE quiz_id=" . intval($id_quiz) . " and id IN (" . implode(",", $result_latest_ids) . ")";
			$db->setQuery($sql);
			$result = $db->loadAssocList();

			$sql = "select max_score from #__guru_quiz where id=" . intval($id_quiz);
			$db->setQuery($sql);
			$max_score = $db->loadColumn();
			$max_score = @$max_score["0"];

			if (isset($result) && count($result) > 0) {
				$user_ids = array();
				foreach ($result as $key => $value) {
					if ($value["score_quiz"] >= $max_score) {
						$user_ids[] = $value["user_id"];
					}
				}
			}

			$and .= " and c.userid in (" . implode(", ", $user_ids) . ")";
		} elseif ($action == "failed") {
			$sql = "SELECT max(id) FROM #__guru_quiz_question_taken_v3
					WHERE quiz_id=" . intval($id_quiz) . " GROUP BY user_id, quiz_id";
			$db->setQuery($sql);
			$result_latest_ids = $db->loadColumn();

			if (!is_array($result_latest_ids) || count($result_latest_ids) == 0) {
				$result_latest_ids = array("0");
			}

			$sql = "SELECT user_id, quiz_id, score_quiz
					FROM #__guru_quiz_question_taken_v3
					WHERE quiz_id=" . intval($id_quiz) . " and id IN (" . implode(",", $result_latest_ids) . ")";
			$db->setQuery($sql);
			$result = $db->loadAssocList();

			$sql = "select max_score from #__guru_quiz where id=" . intval($id_quiz);
			$db->setQuery($sql);
			$max_score = $db->loadColumn();
			$max_score = @$max_score["0"];

			if (isset($result) && count($result) > 0) {
				$user_ids = array();
				foreach ($result as $key => $value) {
					if ($value["score_quiz"] < $max_score) {
						$user_ids[] = $value["user_id"];
					}
				}
			}

			$and .= " and c.userid in (" . implode(", ", $user_ids) . ")";
		}


		if (isset($courses_ids) && count($courses_ids) > 0) {
			$sql = "select distinct(userid) from #__guru_buy_courses where course_id in (" . implode(",", $courses_ids) . ")";
			$db->setQuery($sql);
			$students_ids = $db->loadColumn();

			if (isset($students_ids) && count($students_ids) > 0) {
				$search = $app->getUserStateFromRequest($this->_context . '.filter.search', 'filter_search', '', 'string');

				if (trim($search) != "") {
					$and .= " and (u.name like '%" . addslashes(trim($search)) . "%' OR u.username like '%" . addslashes(trim($search)) . "%' OR u.email like '%" . addslashes(trim($search)) . "%' OR CONCAT(cust.firstname, ' ', cust.lastname) like '%" . addslashes(trim($search)) . "%' )";
				}

				$cid = JFactory::getApplication()->input->get("cid", "0", "raw");
				if (intval($cid) > 0) {
					$courses_ids = array($cid);
				}

				if (intval($cid) > 0 && trim($action) != "") {
					$sql = "select g.user_id from #__guru_viewed_lesson g, #__users u where pid = " . intval($cid) . " and completed='1' and g.user_id=u.id";
					$db->setQuery($sql);
					$user_ids = $db->loadColumn();

					if ($action == "complete") {
						$and .= " and c.userid in (" . implode(", ", $user_ids) . ")";
					} elseif ($action == "notcomplete") {
						if (!isset($user_ids) || count($user_ids) == 0) {
							$user_ids = array("0");
						}
						$and .= " and c.userid not in (" . implode(", ", $user_ids) . ")";
					} elseif ($action == "pass") {
						$sql = "select distinct(userid) from #__guru_buy_courses where course_id=" . intval($cid);
						$db->setQuery($sql);
						$students = $db->loadColumn();

						$sql = "select id_final_exam from #__guru_program where id=" . intval($cid);
						$db->setQuery($sql);
						$result = $db->loadColumn();
						$final_id = @$result["0"];

						$sql = "select max_score from #__guru_quiz where id=" . intval($final_id);
						$db->setQuery($sql);
						$max_score = @$max_score["0"];

						if (isset($students) && count($students) > 0) {
							$sql = "select user_id, score_quiz from #__guru_quiz_question_taken_v3 where quiz_id=" . intval($final_id);
							$db->setQuery($sql);
							$result = $db->loadAssocList("user_id");
							$user_ids = array();
							foreach ($students as $key => $stud_id) {
								if (isset($result[$stud_id])) {
									$score_quiz = $result[$stud_id]["score_quiz"];
									$score_quiz_array = explode("|", $score_quiz);
									$percent = 0;

									if (intval($score_quiz_array["1"]) > 0) {
										$percent = (intval($score_quiz_array["0"]) * 100) / intval($score_quiz_array["1"]);
									}

									if ($percent >= $max_score) {
										$user_ids[] = $stud_id;
									}
								}
							}
						}

						$and .= " and c.userid in (" . implode(", ", $user_ids) . ")";
					}
				}

				$filter_course = $app->getUserStateFromRequest($this->_context . '.filter.course', 'filter_course', '', 'string');
				$this->setState('filter.course', $filter_course);
				$all_filtered_courses = $courses_ids;

				if (intval($filter_course) != 0) {
					$all_filtered_courses = array(intval($filter_course));
				}

				$sql = "select distinct(c.userid), cust.firstname, cust.lastname, A.courses, u.*, cust.image from #__guru_buy_courses c, (select userid, GROUP_CONCAT(course_id SEPARATOR '-') as courses from #__guru_buy_courses where course_id in (" . implode(",", $courses_ids) . ") group by userid) as A, #__users u, #__guru_customer cust where course_id in (" . implode(",", $all_filtered_courses) . ") and c.userid=A.userid and u.id=c.userid and cust.id=u.id " . $and;
			}
		}
		return $sql;
	}

	function getAuthorMyCourses()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$app = JFactory::getApplication("site");

		$search = $app->getUserStateFromRequest($this->_context . '.filter.search', 'filter_search', '', 'string');
		$and = "";
		if (trim($search) != "") {
			$and .= " and (name like '%" . addslashes(trim($search)) . "%' OR description like '%" . addslashes(trim($search)) . "%' OR introtext like '%" . addslashes(trim($search)) . "%')";
		}

		$userid = JFactory::getApplication()->input->get("userid", "0", "raw");
		if (intval($userid) > 0) {
			$sql = "select course_id from #__guru_buy_courses where userid=" . intval($userid);
			$db->setQuery($sql);
			$course_ids = $db->loadColumn();
			$and .= " and id in (" . implode(", ", $course_ids) . ")";
		}

		$quiz = JFactory::getApplication()->input->get("quiz", "0", "raw");
		if (intval($quiz) > 0) {
			$sql = "select type_id from #__guru_mediarel where layout='12' and media_id=" . intval($quiz) . " and type='scr_m'";
			$db->setquery($sql);
			$result_type_id = $db->loadColumn();

			if (isset($result_type_id) && intval($result_type_id) != 0) {
				$sql = "select pid from #__guru_days where id IN (SELECT type_id from #__guru_mediarel where media_id IN (" . implode(",", $result_type_id) . ") and type='dtask')";
				$db->setquery($sql);
				$result_pid = $db->loadColumn();
				$and .= " and id in (" . implode(", ", $result_pid) . ")";
			}
		}

		$sql = "SELECT * FROM #__guru_program WHERE (author like '%|" . intval($user->id) . "|%' OR author='" . intval($user->id) . "') " . $and . " ORDER BY id DESC";

		return $sql;
	}

	function getAuthorMyMedia()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$app = JFactory::getApplication("site");
		$condition	= array();

		$search_media = JFactory::getApplication()->input->get("search_media", "", "raw");
		$status		= JFactory::getApplication()->input->get("media_publ_status", "YN", "raw");
		$type		= JFactory::getApplication()->input->get('media_type', "", "raw");
		$media_category		= JFactory::getApplication()->input->get('media_category', "", "raw");

		if (trim($type) == "-" || trim($type) == "") {
			$type = $app->getUserStateFromRequest('media_type', 'media_type', "", 'string');
		}

		if ($status == "Y") {
			$condition[] = " c.published=1 ";
		} else if ($status == "N") {
			$condition[] = " c.published=0 ";
		}

		if ($search_media != "") {
			$condition[] = " (c.name LIKE '%" . $search_media . "%' OR c.instructions LIKE '%" . $search_media . "%' OR c.local LIKE '%" . $search_media . "%' OR c.url='%" . $search_media . "%' ) ";
		}

		if (trim($type) != "-" && trim($type) != "") {
			$condition[] = " c.type='" . $type . "' ";
		}

		if (trim($media_category) != "-" && trim($media_category) != "") {
			$condition[] = " c.category_id=" . intval($media_category);
		}

		if (!empty($condition))
			$condition = " AND " . implode(" AND ", $condition);
		else $condition = "";

		$sql = "SELECT * FROM #__guru_media AS c WHERE type<>'quiz' and author=" . intval($user->id) . $condition . " ORDER BY id desc";

		return $sql;
	}

	function getAuthorMyQuizzes()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$app = JFactory::getApplication("site");
		$condition	= array();

		$course = JFactory::getApplication()->input->get("selectcoursesd", "", "raw");
		$type		= JFactory::getApplication()->input->get("quiz_select_type", "", "raw");
		$search		= JFactory::getApplication()->input->get('search_quiz', "", "raw");

		if ($type == "1") {
			$condition[] = " is_final=0";
		} else if ($type == "2") {
			$condition[] = " is_final=1 ";
		}

		if ($search != "") {
			$condition[] = " (name LIKE '%" . $search . "%') ";
		}

		if (intval($course) != "0") {
			$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=" . intval($course);
			$db->setQuery($sql);
			$lessons = $db->loadColumn();

			if (!isset($lessons) || count($lessons) == 0) {
				$lessons = array("0");
			}

			$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (" . implode(", ", $lessons) . ")";
			$db->setQuery($sql);
			$q_ids = $db->loadColumn();

			$condition[] = " id in (" . implode(", ", $q_ids) . ") ";
		}

		if (!empty($condition)) {
			$condition = " AND " . implode(" AND ", $condition);
		} else {
			$condition = "";
		}

		$sql = "SELECT * FROM #__guru_quiz WHERE author=" . intval($user->id) . $condition . " ORDER BY id DESC";

		return $sql;
	}

	function getAuthorMyEssays()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$filter_courses = $app->getUserStateFromRequest($this->_context . '.filter.courses', 'filter_courses', '', 'string');
		$this->setState('filter.courses', $filter_courses);

		$filter_essays = $app->getUserStateFromRequest($this->_context . '.filter.essays', 'filter_essays', '', 'string');
		$this->setState('filter.essays', $filter_essays);

		$filter_status = $app->getUserStateFromRequest($this->_context . '.filter.status', 'filter_status', '', 'string');
		$this->setState('filter.status', $filter_status);

		$filter_search = $app->getUserStateFromRequest($this->_context . '.filter.search', 'filter_search', '', 'string');
		$filter_search_request = JFactory::getApplication()->input->get("filter_search", "", "raw");

		if (trim($filter_search_request) == "") {
			$filter_search = trim($filter_search_request);
		}

		$this->setState('filter.search', $filter_search);

		$and_course_filter = "";
		$and_essays_filter = "";
		$and_status_filter = "";
		$and_search_filter = "";

		if (intval($filter_courses) > 0) {
			$and_course_filter = " and qt3.pid=" . intval($filter_courses);
		}

		if (intval($filter_essays) > 0) {
			$and_essays_filter = " and q3.id=" . intval($filter_essays);
		}

		if (intval($filter_status) > 0) {
			$and_status_filter = " and qem.grade >= '0'";
		} else {
			$and_status_filter = " and qem.grade IS NULL";
		}

		if (trim($filter_search) != "") {
			$and_search_filter = " and (u.email like '%" . $db->escape(trim($filter_search)) . "%' OR c.firstname like '%" . $db->escape(trim($filter_search)) . "%' OR c.lastname like '%" . $db->escape(trim($filter_search)) . "%' OR qt3.answers_given like '%" . $db->escape(trim($filter_search)) . "%')";
		}

		//-------------------------------------------------- quiz change
		$quizzes_ids = array("0");

		$sql = "select `id` from #__guru_program where `author`='" . intval($user->id) . "' OR `author` like '%|" . $user->id . "|%'";
		$db->setQuery($sql);
		$authors_courses = $db->loadColumn();
		foreach ($authors_courses as $course_id) {
			$query = "SELECT question_ids FROM `#__guru_quiz_question_taken_v3` WHERE pid = " . $db->q($course_id);
			$results = $db->setQuery($query)->loadColumn();
			if (!$results) {
				continue;
			}

			foreach ($results as $value) {
				if (!trim($value)) {
					continue;
				}

				$ids = explode(',', $value);
				foreach ($ids as $id) {
					if (in_array($id, $quizzes_ids)) {
						continue;
					}
					
					$quizzes_ids[] = $id;
				}
			}
		}

		if (!isset($authors_courses) || count($authors_courses) == 0) {
			$authors_courses = array("0");
		}

		$authors_courses = implode(',', $authors_courses);
		$quizzes_ids = implode(',', $quizzes_ids);
		$query = 
			"SELECT 
				q3.question_content, 
				q3.points, 
				u.email, 
				c.id AS user_id, 
				c.firstname, 
				c.lastname, 
				c.image, 
				qem.grade, 
				qem.feedback_quiz_results, 
				qt3.question_id, 
				qt3.answers_given, 
				qt3.pid,
				qt3.quiz_id AS qid
			FROM `#__guru_quiz_taken_v3` qt3
			INNER JOIN `#__guru_questions_v3` q3 ON q3.qid = qt3.quiz_id AND q3.id = qt3.question_id
			INNER JOIN `#__users` u ON u.id = qt3.user_id
			INNER JOIN `#__guru_customer` c ON c.id = qt3.user_id
			INNER JOIN `#__guru_quiz` q ON q.id = qt3.quiz_id
			LEFT JOIN `#__guru_quiz_essay_mark` qem ON qem.question_id = qt3.question_id AND qem.user_id = qt3.user_id
			WHERE 1=1
			AND q3.`type` = 'essay'
			AND qt3.question_id IN ($quizzes_ids)
			AND qt3.pid IN ($authors_courses)
			$and_course_filter
			$and_status_filter
			$and_search_filter
			ORDER BY qt3.id DESC";
		
		return $query;
	}

	function getAllRowsMediaCat($parent, $level)
	{
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_media_categories where parent_id=" . intval($parent);
		$db->setquery($sql);
		$result = $db->loadAssocList();

		if (isset($result) && is_array($result) && count($result) > 0) {
			$level++;
			foreach ($result as $key => $value) {
				$sql = "select count(id) from #__guru_media where category_id=" . intval($value["id"]);
				$db->setquery($sql);
				$result = $db->loadResult();
				$value["nb_medias"] = $result;
				$value["level"] = $level;
				$this->return_array[] = $value;
				$this->getAllRowsMediaCat($value["id"], $level);
			}
		}
		return $this->return_array;
	}

	function getAuthorMyMediaCategories()
	{
		$config = new JConfig();
		$app = JFactory::getApplication('site');
		$limit		= $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		$return = $this->getAllRowsMediaCat(0, 0);

		$filter_state = JFactory::getApplication()->input->get("filter_state", "raw");
		$filter_search = JFactory::getApplication()->input->get("filter_search", "raw");

		if ($filter_state != "-1" || trim($filter_search) != "") {
			if (isset($return) && count($return) > 0) {
				foreach ($return as $key => $value) {
					if ($filter_state == "1") {
						if ($value["published"] == 0) {
							unset($return[$key]);
						}
					} elseif ($filter_state == "0") {
						if ($value["published"] == 1) {
							unset($return[$key]);
						}
					}

					if (trim($filter_search) != "") {
						$name = mb_strtolower($value["name"]);
						$search = mb_strtolower(trim($filter_search));
						if (strpos($name, $search) === FALSE) {
							unset($return[$key]);
						}
					}
				}
			}
		}

		$this->_total = count($return);
		if (isset($return) && count($return) > 0 && $limit != 0) {
			$return = array_slice($return, (int)($limitstart), (int)($limit));
		}

		$pagination = $this->getPagination();
		$pagination->limitstart = $limitstart;
		$pagination->total = $this->_total;
		@$pagination->pagesTotal = ceil($this->_total / $pagination->limit);
		@$pagination->pagesStop = ceil($this->_total / $pagination->limit);
		@$pagination->pagesCurrent = ($limitstart / $limit + 1);
		$this->set("Pagination", $pagination);

		return $return;
	}
	public function getData()
	{
		$app = JFactory::getApplication("site");
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			// Get the page/component configuration
			$params = $app->getParams();
			// Get the pagination request variables
			$limit		= JFactory::getApplication()->input->get('limit', 12, "raw");
			$limitstart	= JFactory::getApplication()->input->get('limitstart', 0, "raw");
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $limitstart, $limit);
		}
		return $this->_data;
	}

	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$result = parent::_getList($query, $limitstart, $limit);
		$odd = 1;
		foreach ($result as $k => $row) {
			$result[$k]->odd = $odd;
			$odd = 1 - $odd;
		}
		return $result;
	}

	function getAuthorList()
	{
		$db = JFactory::getDBO();
		$query = "SELECT a.*, u.`name`, u.`username`, u.`email` 
				 FROM `#__guru_authors` a
				 INNER JOIN `#__users` u ON a.`userid` = u.`id` 
				 WHERE a.`enabled` = '1' and u.`block`= 0
				 ORDER BY a.`ordering`";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		for ($i = 0; $i < count($result); $i++) {
			if (trim($result[$i]->images) != "") {
				$path = explode("/", $result[$i]->images);
				$result[$i]->imageName = $path[count($path) - 1];
			}
		}
		return $result;
	}

	function getAuthor()
	{
		$db = JFactory::getDBO();
		$item = JFactory::getApplication()->input->get('Itemid', 0, "raw");
		$cid = JFactory::getApplication()->input->get('cid', 0, "raw");

		if ($item != 0) {
			$sql = "SELECT `link`, `params` FROM #__menu WHERE id=" . intval($item);
			$db->setQuery($sql);
			$menu_details = $db->loadAssocList();
			$params = $menu_details["0"]["params"];
			$params = json_decode($params);
			$menu_link = $menu_details["0"]["link"];

			if (isset($params) && isset($params->cid) && strpos($menu_link, "view=guruauthor") !== FALSE) {
				$cid = $params->cid;
			}
		}

		$query = "SELECT a.*, u.name, u.username, u.email FROM #__guru_authors a, #__users u WHERE a.userid=u.id AND a.id=" . intval($cid);
		$db->setQuery($query);
		$result = $db->loadObject();
		//get the courses by this author

		if (!empty($result)) {
			$config = JFactory::getConfig();
			$offset = $config->get('offset');
			$jnow 	= new JDate('now', $offset);
			$date 	= $jnow->toSQL(true);
			$query = "SELECT `id`, `alias`, `name`, `level`, `startpublish` 
				FROM `#__guru_program` 
				WHERE (author like '%|" . intval($result->userid) . "|%' OR author='" . intval($result->userid) . "') 
				AND published=1 
				AND startpublish <='$date' 
				AND (endpublish >='$date' OR endpublish = '0000-00-00 00:00:00' )";

			$db->setQuery($query);
			$result2 = $db->loadObjectList();
			if (!empty($result2)) {
				$result->courses = $result2;
			}
		}
		return $result;
	}

	function getConfig()
	{
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config limit 1";
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	function existAuthor($userid)
	{
		$db = JFactory::getDBO();
		$sql = "select count(*) as total from #__guru_authors where userid=" . $userid;
		$db->setQuery($sql);
		$result = $db->loadObject();
		if ($result->total == 0) {
			return false;
		}
		return true;
	}
	function getAuthorDetails()
	{
		$db = JFactory::getDBO();
		$author_id = "";
		$user = JFactory::getUser();
		if (isset($user) && $user->id > 0) {
			$author_id = $user->id;
		} else {
			$author_id = JFactory::getApplication()->input->get("id", "0", "raw");
		}

		$type = JFactory::getApplication()->input->get("author_type", "1", "raw");
		$result = new StdClass();

		if ($author_id > 0) {
			if ($this->existAuthor($author_id)) {
				$sql = "select u.*,a.id as lmsid,a.* from #__users u, #__guru_authors a where u.id=a.userid and a.userid=" . $author_id;
			} else {
				$sql = "select * from #__users u where id=" . $author_id;
			}

			$db->setQuery($sql);
			$result = $db->loadObject();

			$result->userid = $author_id;

			if (!isset($result->lmsid)) {
				$result->id = 0;
			} else {
				$result->id = $result->lmsid;
			}
		} else {
			$get = JFactory::getApplication()->input->get->getArray();
			foreach ($get as $key => $val) {
				$result->$key = $val;
			}
		}

		$result->type = $type;

		if (!isset($result->id)) {
			$result->id = 0;
		}
		$result->userid = $author_id;

		if (!isset($result->username)) {
			$result->usernam = "";
		}

		if (!isset($result->name)) {
			$result->name = "";
		}

		if (!isset($result->email)) {
			$result->email = "";
		}

		if (!isset($result->author_title)) {
			$result->author_title = "";
		}

		if (!isset($result->website) || $result->website == "") {
			$result->website = "http://";
		}

		if (!isset($result->blog) || $result->blog == "") {
			$result->blog = "http://";
		}

		if (!isset($result->facebook) || $result->facebook == "") {
			$result->facebook = "http://";
		}

		if (!isset($result->twitter) || $result->twitter == "") {
			$result->twitter = "";
		}

		//show/hide drop-down options
		$show_options = array();
		$show_options[] = JHTML::_('select.option', '1', JText::_('GURU_SHOW'));
		$show_options[] = JHTML::_('select.option', '0', JText::_('GURU_HIDE'));

		if (!isset($result->show_email)) {
			$result->show_email = 1;
		}
		$result->lists['show_email'] = JHTML::_('select.genericlist', $show_options, 'show_email', 'class="input-small pull-left"', 'value', 'text', $result->show_email);

		if (!isset($result->show_website)) {
			$result->show_website = 1;
		}
		$result->lists['show_website'] = JHTML::_('select.genericlist', $show_options, 'show_website', 'class="input-small pull-left"', 'value', 'text', $result->show_website);

		if (!isset($result->show_blog)) {
			$result->show_blog = 1;
		}
		$result->lists['show_blog']	= JHTML::_('select.genericlist', $show_options, 'show_blog', 'class="input-small pull-left"', 'value', 'text', $result->show_blog);

		if (!isset($result->show_facebook)) {
			$result->show_facebook = 1;
		}
		$result->lists['show_facebook']	= JHTML::_('select.genericlist', $show_options, 'show_facebook', 'class="input-small pull-left"', 'value', 'text', $result->show_facebook);

		if (!isset($result->show_twitter)) {
			$result->show_twitter = 1;
		}
		$result->lists['show_twitter'] = JHTML::_('select.genericlist', $show_options, 'show_twitter', 'class="input-small pull-left"', 'value', 'text', $result->show_twitter);

		$id_a = JFactory::getApplication()->input->get("id", "0", "raw");
		if ((!isset($result->gid)) && (isset($id_a) && ($id_a != ""))) {
			$query = "select group_id from #__user_usergroup_map where user_id='" . $id_a . "'";
			$db->setQuery($query);
			$res = $db->loadResult();

			if (isset($res) && $res != "") {
				$result->gid = $res;
			} else {
				$result->gid = "";
			}
		} elseif (!isset($result->gid)) {
			$result->gid = "";
		}

		$user = JFactory::getUser();
		$user_id = $user->id;

		$sql_u = "select group_id from #__user_usergroup_map where user_id=" . intval($user_id);
		$db->setQuery($sql_u);
		$res_user_current = $db->loadResult();

		$result->lists['gid'] = '<select name="gid" id="gid" class="inputbox" size="10" >';
		if ($res_user_current == 8) {
			//$result->lists['gid'] .= JHtml::_('select.options', UsersHelper::getGroups(), 'value', 'text', $result->gid);
		} else {
			//$result->lists['gid'] .= str_replace("- Super Users", "", JHtml::_('select.options', UsersHelper::getGroups(), 'value', 'text', $result->gid));
		}
		$result->lists['gid'] .= '</select>';

		if (!isset($result->images) || $result->images == "") {
			$result->images = "";
		}

		if (!isset($result->usertype)) {
			$result->usertype = 0;
		}

		if (!isset($result->full_bio)) {
			$result->full_bio = "";
		}

		return $result;
	}

	function delete()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		foreach ($cids as $cid) {
			$stud_nb = 	$this->getStudentsNumber($cid);

			if (intval($stud_nb) > 0) {
				return false;
			}

			$sql = "DELETE FROM #__guru_program WHERE id=" . intval($cid);
			$db->setQuery($sql);
			$db->execute();

			//---------------------------------------
			JEventDispatcher::getInstance()->trigger('onAfterGuruCourseDeleted', array(intval($cid)));
			//---------------------------------------
		}

		return true;
	}

	public function getStudentsNumber($pid)
	{
		$database = JFactory::getDbo();
		$user = JFactory::getUser();

		$sql = "SELECT count(distinct bc.userid) FROM #__guru_buy_courses bc, #__users u , #__guru_customer c, #__guru_order o WHERE c.id=bc.userid and  bc.userid=u.id and bc.course_id=" . $pid . " and o.userid=c.id and o.userid=bc.userid and bc.`userid` not in (" . intval($user->id) . ")";
		$database->setQuery($sql);
		$database->execute();
		$students_number = $database->loadResult();

		return $students_number;
	}

	function deleteMedia()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		foreach ($cids as $cid) {
			$sql = "DELETE FROM #__guru_media WHERE author=" . intval($user->id) . " and id=" . intval($cid);
			$db->setQuery($sql);
			$db->execute();
		}

		return true;
	}
	public function removeMediaCat()
	{
		$db = JFactory::getDBO();
		$data = JFactory::getApplication()->input->post->getArray();
		$ids = $data['cid'];
		if (!empty($ids)) {
			foreach ($ids as $key => $id) {
				$sql = "select count(id) from #__guru_media where category_id=" . $id;
				$db->setQuery($sql);
				$res = $db->loadColumn();
				$res = $res[0];

				$sql = "select count(*) from #__guru_media_categories where parent_id=" . $id;
				$db->setQuery($sql);
				$res1 = $db->loadColumn();
				$res1 = $res1[0];
				$app = JFactory::getApplication('site');

				if ($res > 0 || $res1 > 0) {
					$msg = JText::_('GURU_NO_DELETE_MEDIACAT');
					$app->redirect('index.php?option=com_guru&view=guruauthor&task=authormymediacategories&layout=authormymediacategories', $msg, 'error');
				}

				$sql = "delete from #__guru_media_categories where id=" . $id;
				$db->setQuery($sql);
				$db->execute();
			}
		}
		return true;
	}
	public function removeQuiz()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		foreach ($cids as $cid) {
			$sql = "DELETE FROM #__guru_quiz WHERE author=" . intval($user->id) . " and id=" . intval($cid);
			$db->setQuery($sql);
			$db->execute();
		}

		return true;
	}
	function getProgram()
	{
		$db = JFactory::getDBO();
		if (intval($this->_id) == 0) {
			$this->_id = JFactory::getApplication()->input->get("id", "0", "raw");
		}

		if (empty($this->_attribute)) {
			$this->_attribute = $this->getTable("guruPrograms");

			if (is_array($this->_id)) {
				$this->_id = $this->_id["0"];
			}

			$this->_attribute->load($this->_id);
		}

		$data = JFactory::getApplication()->input->post->getArray();

		if (!$this->_attribute->bind($data)) {
			return false;
		}

		if (!$this->_attribute->check()) {
			return false;
		}

		//start get author list
		$sql = "SELECT u.id, u.name FROM #__users u, #__guru_authors la where u.id=la.userid";
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		$author_list = array();
		$author_list[] = JHTML::_("select.option", JText::_('GURU_SELECT'), "0");
		for ($i = 0; $i < count($result); $i++)
			$author_list[] = JHTML::_("select.option", $result[$i]->name, $result[$i]->id);
		$this->_attribute->lists['author'] = JHTML::_("select.genericlist", $author_list, "author", "", "text", "value", $this->_attribute->author);
		if ($this->_attribute->published == 1) {
			$checkedd = 'checked="checked"';
		} else {
			$checkedd = '';
		}

		$this->_attribute->lists['published']  = '<input type="hidden" name="published" value="0">';


		if ($this->_attribute->published == 1) {
			$this->_attribute->lists['published'] .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published"><span class="lbl"></span>';
		} else {
			$this->_attribute->lists['published'] .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published"><span class="lbl"></span>';
		}

		$level_list = array();
		$level_list[] = JHTML::_("select.option", "0", JText::_("GURU_BEGINNERS"));
		$level_list[] = JHTML::_("select.option", "1", JText::_("GURU_INTERMEDIATE"));
		$level_list[] = JHTML::_("select.option", "2", JText::_("GURU_ADVANCED"));

		$this->_attribute->lists['level'] = JHTML::_('select.genericlist', $level_list, 'level', 'class="input-medium"', 'value', 'text', $this->_attribute->level);

		return $this->_attribute;
	}
	function getAllPlans()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_subplan WHERE published<>'0' ORDER BY ordering ASC, id DESC ";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}

	function getAllReminds()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_subremind ORDER BY ordering ASC, id DESC ";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}

	function getDateFormat()
	{
		$db = JFactory::getDBO();
		$sql = "Select datetype FROM #__guru_config where id=1 ";
		$db->setQuery($sql);
		$format_date = $db->loadColumn();
		$format_date = $format_date[0];
		return $format_date;
	}
	function getConfigs()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";

		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function getStudent()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		if (intval($id) != 0) {
			$sql = "SELECT u.id as user_id,
						 u.name as name, 
						 u.email as email, 
						 u.username as username,
						 g.company,
						 g.firstname,
						 g.lastname
						 FROM #__users u, #__guru_customer g where u.id = " . intval($id) . " and u.id=g.id";
			$db->setQuery($sql);
			$result = $db->loadAssocList();
			return $result;
		} else {
			return array();
		}
	}

	function existCustomer($id)
	{
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_customer where id=" . intval($id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result > 0) {
			return true;
		} else {
			return false;
		}
	}

	function encriptPassword($password)
	{
		$salt = "";
		for ($i = 0; $i <= 32; $i++) {
			$d = rand(1, 30) % 2;
			$salt .= $d ? chr(rand(65, 90)) : chr(rand(48, 57));
		}
		$hashed = md5($password . $salt);
		$encrypted = $hashed . ':' . $salt;
		return $encrypted;
	}

	function saveJoomlaUser()
	{
		$db = JFactory::getDBO();
		$user_id = "";
		$password = JFactory::getApplication()->input->get("password", "", "raw");
		$password = $this->encriptPassword($password);
		$name = JFactory::getApplication()->input->get("firstname", "", "raw");
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		$block = "0";
		$sendEmail = "0";
		$jnow = new JDate('now');
		$registerDate = $jnow->toSQL(true);
		$lastvisitDate = "0000-00-00 00:00:00";

		$sql = "insert into #__users(name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params) values ('" . addslashes(trim($name)) . "', '" . addslashes(trim($username)) . "', '" . addslashes(trim($email)) . "', '" . $password . "', 0, 0, '" . $registerDate . "', '" . $lastvisitDate . "', '', '')";
		$db->setQuery($sql);

		if ($db->execute()) {
			$sql = "select id from #__users where name='" . addslashes(trim($name)) . "' and username='" . addslashes(trim($username)) . "' and email='" . addslashes(trim($email)) . "'";
			$db->setQuery($sql);
			$user_id = $db->loadColumn();
			$user_id = @$user_id["0"];
		}

		if ($user_id != "") {
			$query = "select student_group  from #__guru_config where id='1'";
			$db->setQuery($query);
			$student_group = $db->loadResult();

			if (isset($student_group) && $student_group != 2) {
				$group_id = $student_group;
			} else {
				$query = "select id from #__usergroups where title='Registered'";
				$db->setQuery($query);
				$group_id = $db->loadResult();
			}
			$query = "insert into #__user_usergroup_map(user_id, group_id) values('" . $user_id . "', '" . $group_id . "')";
			$db->setQuery($query);
			$group_id = $db->loadResult();
		}
		return $user_id;
	}

	function store()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
		$guruHelper = new guruHelper();

		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "", "raw");
		$company = JFactory::getApplication()->input->get("company", "", "raw");
		$firstname = JFactory::getApplication()->input->get("firstname", "", "raw");
		$lastname = JFactory::getApplication()->input->get("lastname", "", "raw");
		$return = array();
		$sql = "";
		$item = $this->getTable('guruPrograms');
		$user = JFactory::getUser();
		$user_id = (int) $user->id;

		$configs = $this->getConfig();
		$st_authorpage = json_decode($configs->st_authorpage, true);
		@$teacher_approve_courses = $st_authorpage["teacher_approve_courses"];

		$come_from =  JFactory::getApplication()->input->get("g_page", "", "raw");

		if ($come_from == "courseadd" || $come_from == "courseedit") {
			$data = JFactory::getApplication()->input->post->getArray();
			$data['startpublish'] = date('Y-m-d H:i:s', strtotime($data['startpublish']));

			if ($data['endpublish'] != 'Never' && $data['endpublish'] != '' && $data['endpublish'] != "0000-00-00 00:00:00") { // calendar change
				$data['endpublish'] = date('Y-m-d H:i:s', strtotime($data['endpublish']));
			} elseif ($data["endpublish"] == "") {
				$data["endpublish"] = "0000-00-00 00:00:00";
			}

			if ($data["id"] == "") {
				$data["id"] = 0;
			}

			$data['image_avatar'] = $data["image_avatar"];
			$data["author"] = intval($user_id);
			$jnow = new JDate('now');
			$date2 = $jnow->toSQL(true);

			if ($data['id'] != NULL) {
				$sql1 = "SELECT lesson_release FROM #__guru_program where id=" . $data['id'];
				$db->setQuery($sql1);
				$less_release_db = $db->loadResult();

				if ($less_release_db != $data['lesson_release']) {
					$sql = "UPDATE #__guru_program set start_release = '" . $date2 . "' WHERE id = '" . $data['id'] . "' ";
					$db->setQuery($sql);
					$db->execute();
				}
			}

			$data['description'] = JFactory::getApplication()->input->get("description", "", "raw");
			$data['introtext']	 = JFactory::getApplication()->input->get("introtext", "", "raw");

			$data['pre_req'] = JFactory::getApplication()->input->get("pre_req", "", "raw");
			$data['pre_req_books'] = JFactory::getApplication()->input->get("pre_req_books", "", "raw");
			$data['reqmts'] = JFactory::getApplication()->input->get("reqmts", "", "raw");

			$data['alias'] = JFilterOutput::stringURLSafe($data['name']);

			$final_quiz = "0";
			$sql = "select id_final_exam from #__guru_program where id=" . intval($data['id']);
			$db->setQuery($sql);
			$id_final_exam = $db->loadColumn();
			$id_final_exam = @$id_final_exam["0"];
			if (isset($id_final_exam) && intval($id_final_exam) != 0) {
				$final_quiz = intval($id_final_exam);
			}

			if ($final_quiz != "0" && $data["final_quizzes"] == 0) {
				// delete final exam
				$progid = $data['id'];

				$sql = "SELECT id FROM #__guru_days WHERE pid='" . $progid . "' order by ordering desc limit 0,1";
				$db->setQuery($sql);
				$moduleid = $db->loadColumn();
				$moduleid = @$moduleid["0"];

				$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type='dtask' and mr.type_id=" . intval($moduleid) . " and mr.media_id=t.id order by t.ordering desc limit 0,1";
				$db->setQuery($sql);
				$lesson_id = $db->loadColumn();
				$lesson_id = @$lesson_id["0"];

				$sql = "delete FROM #__guru_mediarel WHERE type = 'dtask' AND type_id = " . intval($moduleid) . " and media_id = " . intval($lesson_id);
				$db->setQuery($sql);
				$db->execute();

				$sql = "delete from #__guru_task where id=" . intval($lesson_id);
				$db->setQuery($sql);
				$db->execute();
			}

			if (intval($id) == 0) {
				if ($teacher_approve_courses == "1") { // NO
					$data["status"] = "0";
				} elseif ($teacher_approve_courses == "0") { // YES
					$data["status"] = "1";
				}
			}

			if (isset($data["groups"])) {
				$data["groups_access"] = implode(",", $data["groups"]);
			}

			if (intval($data["id"]) == 0) {
				$sql = "select max(ordering) from #__guru_program";
				$db->setQuery($sql);
				$max = $db->loadColumn();
				$max = @$max["0"];
				$new_ordering = intval($max) + 1;
				$data["ordering"] = $new_ordering;
			}

			$og_title = JFactory::getApplication()->input->get("og_title", "", "raw");
			$og_type = JFactory::getApplication()->input->get("og_type", "", "raw");
			$og_image = JFactory::getApplication()->input->get("og_image", "", "raw");
			$og_url = JFactory::getApplication()->input->get("og_url", "", "raw");
			$og_desc = JFactory::getApplication()->input->get("og_desc", "", "raw");
			$og_details = array("og_title" => trim($og_title), "og_type" => trim($og_type), "og_image" => trim($og_image), "og_url" => trim($og_url), "og_desc" => trim($og_desc));
			$data["og_tags"] = json_encode($og_details);

			if (!$item->bind($data)) {
				return false;
			}
			if (!$item->check()) {
				return false;
			}

			if (!$item->store()) {
				return false;
			}

			if (isset($data['echbox'])) {
				$email_chbox = $data['echbox'];
			} else {
				$email_chbox = '';
			}

			//delete old records
			if ($data['id'] > 0) {
				$db->setQuery("DELETE FROM #__guru_mediarel WHERE type='email' AND type_id='" . $data['id'] . "'");
				$db->execute();
			}
			//delete end

			if ($data['id'] == "") {
				$data['id'] = $item->id;
			}

			if ($data['id'] == 0) {
				$ask = "SELECT id FROM #__guru_program ORDER BY id DESC LIMIT 1 ";
				$db->setQuery($ask);
				$data['id'] = $db->loadResult();
			}
			$progid = (int) $data['id'];

			$sql = "SELECT count(id) FROM #__guru_customer where id=" . intval($user_id);
			$db->setQuery($sql);
			$count = $db->loadColumn();
			$count = $count[0];

			$courses = intval($progid) . "-0.0-1";
			$amount = 0;

			$timezone = new DateTimeZone(JFactory::getConfig()->get('offset'));
			$jnow = new JDate('now');
			$jnow->setTimezone($timezone);
			$buy_date = $jnow->toSQL(true);
			//$buy_date = date("Y-m-d H:i:s");

			$plan_id = "1";
			$order_expiration = "0000-00-00 00:00:00";
			$jnow = new JDate('now');
			$current_date_string = $jnow->toSQL(true);
			$temp = explode(" ", $user->name);
			if (isset($temp) && count($temp) > 1) {
				$last_name = $temp[count($temp) - 1];
				unset($temp[count($temp) - 1]);
				$first_name = implode(" ", $temp);
			} else {
				if (count($temp) == 1) {
					$first_name = $user->name;
					$last_name  = $user->name;
				}
			}

			if ($count == 0) {
				$sql = "INSERT INTO #__guru_customer(id,company, firstname, lastname) VALUES ('" . intval($user_id) . "','','" . addslashes(trim($first_name)) . "','" . addslashes(trim($last_name)) . "')";
				$db->setQuery($sql);
				$db->execute();
			}

			$sql = "SELECT COUNT(*)
				FROM `#__guru_buy_courses` a
				INNER JOIN `#__guru_order` b ON b.id = a.order_id
				WHERE a.userid = '$user_id'
				AND a.course_id = '$progid'
				AND a.expired_date = '0000-00-00 00:00:00'
				AND a.plan_id = 1";

			$db->setQuery($sql);
			$result = $db->loadResult();

			if (!$result) { // add a new license
				$sql = "select currency from #__guru_config where id=1";
				$db->setQuery($sql);
				$currency = $db->loadColumn();
				$currency = $currency[0];

				$sql = "insert into #__guru_order (userid, order_date, courses, status, amount, amount_paid, processor, number_of_licenses, currency, promocodeid, published, form) values (" . intval($user_id) . ", '" . $buy_date . "', '" . intval($progid) . "-0-1', 'Paid', '0', '-1','paypaypal','0','" . $currency . "','0','1', '')";
				$db->setQuery($sql);
				$db->execute();

				$sql = "select MAX(id) from #__guru_order";
				$db->setQuery($sql);
				$max_id = $db->loadColumn();
				$max_id = $max_id[0];

				$sql = "insert into #__guru_buy_courses (userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values (" . $user_id . ", " . $max_id . " , " . $progid . ", '" . $amount . "', '" . $buy_date . "', '" . $order_expiration . "', '" . $plan_id . "', 0)";
				$db->setQuery($sql);
				$db->execute();
			}


			// start send email to administrator if auto approve course is set to  NO
			if ($teacher_approve_courses == "1") { // NO
				$sql = "select status from #__guru_program where id=" . intval($progid);
				$db->setQuery($sql);
				$status = $db->loadColumn();
				$status = @$status["0"];

				if (intval($status) == 0) { // Not Approved
					$this->sendEmailForAskApprove($progid);
				}
			}
			// stop send email to administrator if auto approve course is set to  NO

			if ($email_chbox != '')
				foreach ($email_chbox as $email_chbox_val) {
					if (intval($email_chbox_val) > 0) {
						$db->setQuery("INSERT INTO #__guru_mediarel (id,type,type_id,media_id,mainmedia) VALUES ('','email','" . $progid . "','" . $email_chbox_val . "','0')");
						$db->execute();
					}
				}

			if (isset($data['mediafiles'])) {
				//delete old records
				if ($data['id'] > 0) {
					$db->setQuery("DELETE FROM #__guru_mediarel WHERE type='pmed' AND type_id='" . $data['id'] . "'");
					$db->execute();
				}
				//delete end
				if (intval($data['id']) == 0) {
					$ask = "SELECT id FROM #__guru_program ORDER BY id DESC LIMIT 1 ";
					$db->setQuery($ask);
					$data['id'] = $db->loadResult();
				}
				$progid = $data['id'];
				$thefiles = explode(',', $data['mediafiles']);

				$id_tmp_med_task_2_remove = array();
				if (isset($data['mediafiletodel']))
					$id_tmp_med_files_2_remove = explode(',', $data['mediafiletodel']);

				$poz = 1;
				foreach ($thefiles as $files) {
					if ($files != "") {
						$array = $data["order"];
						if (intval($files) > 0 && !in_array($files, $id_tmp_med_files_2_remove)) {
							$access = "access" . $files;
							$order = $poz++;
							if (isset($array[$files]) && intval($array[$files]) != 0) {
								$order = $array[$files];
							}

							$db = JFactory::getDbo();
							$query = $db->getQuery(true);
							$query->clear();
							$query->insert('#__guru_mediarel');
							$query->columns(array($db->quoteName('type'), $db->quoteName('type_id'), $db->quoteName('media_id'), $db->quoteName('mainmedia'), $db->quoteName('access'), $db->quoteName('order')));
							$query->values("'pmed'," . $db->quote(trim($progid)) . ',' . $db->quote(trim($files)) . ", '0', " . $db->quote(trim($data[$access])) . ", "  . intval($db->quote(trim($data[$order]))));
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			} // end if

			if (isset($data['preqfiles'])) {
				//delete old records
				if ($data['id'] > 0) {
					//$db->setQuery("DELETE FROM #__guru_mediarel WHERE type='preq' AND type_id='".$data['id']."'");
					//$db->execute();
				}
				//delete end
				if (intval($data['id']) == 0) {
					$ask = "SELECT id FROM #__guru_program ORDER BY id DESC LIMIT 1 ";
					$db->setQuery($ask);
					$data['id'] = $db->loadResult();
				}
				$progid = $data['id'];


				$thefiles = explode(',', $data['preqfiles']);

				$id_tmp_med_task_2_remove = array();
				if (isset($data['preqfiletodel']))
					$id_tmp_med_files_2_remove = explode(',', $data['preqfiletodel']);

				foreach ($thefiles as $files) {
					if (intval($files) > 0 && !in_array($files, $id_tmp_med_files_2_remove)) {
						$db->setQuery("INSERT INTO #__guru_mediarel (id,type,type_id,media_id,mainmedia) VALUES ('','preq','" . $progid . "','" . $files . "','0')");
						$db->execute();
					}
				}
			} // end if

			$sql = "DELETE FROM #__guru_program_plans WHERE product_id = '" . $progid . "' ";
			$db->setQuery($sql);
			$db->execute();

			foreach ($data['subscriptions'] as $element) {
				$data['subscription_price'][$element] = $guruHelper->savePrice($data['subscription_price'][$element]);

				if ($data['subscription_price'][$element] == 0 && !isset($data['chb_free_courses'])) {
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set("empltyprice", "1");

					return false;
				}
				$data['subscription_default'] == $element ? $default = '1' : $default = '0';


				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->clear();
				$query->insert('#__guru_program_plans');
				$query->columns(array($db->quoteName('product_id'), $db->quoteName('plan_id'), $db->quoteName('price'), $db->quoteName('default')));
				$query->values(intval($progid) . ',' . intval($element) . ',' . $db->quote(trim($data['subscription_price'][$element])) . ',' . $default);
				$db->setQuery($query);
				$db->execute();
			}
			// Subscriptions - END

			// Renewals
			$sql = "DELETE FROM #__guru_program_renewals WHERE product_id = '" . $progid . "' ";
			$db->setQuery($sql);
			$db->execute();

			if (!empty($data['renewals'])) {
				foreach ($data['renewals'] as $element) {
					$data['renewal_default'] == $element ? $default = '1' : $default = '0';
					
					$data['renewal_price'][$element] = $guruHelper->savePrice($data['renewal_price'][$element]);
					
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->clear();
					$query->insert('#__guru_program_renewals');
					$query->columns(array($db->quoteName('product_id'), $db->quoteName('plan_id'), $db->quoteName('price'), $db->quoteName('default')));
					$query->values(intval($progid) . ',' . intval($element) . ',' . $db->quote(trim($data['renewal_price'][$element])) . ',' . $default);
					$db->setQuery($query);
					$db->execute();
				}
				// Renewals - END
			}

			//SEQUESNITAL_NON-SEQUENTIAL Course START

			if ($data['lesson_release'] != '0') {
				$sql = "UPDATE #__guru_program set course_type =" . $data['course_type'] . " , lesson_release=" . $data['lesson_release'] . " , lessons_show =" . $data['lessons_show'] . "  WHERE id = '" . $progid . "' ";
				if ($less_release_db != $data['lesson_release']) {
					$sql = "UPDATE #__guru_program set start_release = '" . $date2 . "' WHERE id = '" . $progid . "' ";
					$db->setQuery($sql);
					$db->execute();
				}
			} elseif ($data['lesson_release'] == '0') {
				$sql = "UPDATE #__guru_program set course_type =" . $data['course_type'] . " , lesson_release=" . $data['lesson_release'] . " , lessons_show =" . $data['lessons_show'] . "  WHERE id = '" . $progid . "' ";
			}
			$db->setQuery($sql);
			$db->execute();


			//SEQUESNITAL_NON-SEQUENTIAL Course END

			//FINAL EXAM QUIZ START

			$sql = "SELECT id FROM #__guru_days WHERE pid='" . $progid . "' order by ordering desc limit 0,1";
			$db->setQuery($sql);
			$moduleid = $db->loadColumn();
			$moduleid = @$moduleid["0"];

			$sql = "SELECT name FROM #__guru_quiz WHERE id='" . $data['final_quizzes'] . "' ";
			$db->setQuery($sql);
			$name_quiz = $db->loadResult();

			$db->setQuery("SELECT id_final_exam  FROM #__guru_program WHERE id=" . $progid);
			$id_final_exam = $db->loadResult();

			$sql = "UPDATE #__guru_program set id_final_exam =" . intval($data['final_quizzes']) . " WHERE id=" . $progid;
			$db->setQuery($sql);
			$db->execute();

			$sql = "UPDATE #__guru_program set certificate_term = " . $data['certificate_setts'] . " WHERE id=" . $progid;
			$db->setQuery($sql);
			$db->execute();

			if ($data['final_quizzes'] != '0' && $data['final_quizzes'] != $id_final_exam) {
				$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type='dtask' and mr.type_id=" . intval($moduleid) . " and mr.media_id=t.id order by t.ordering desc limit 0,1";
				$db->setQuery($sql);
				$lesson_id = $db->loadColumn();
				$lesson_id = @$lesson_id["0"];

				$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_m' AND type_id = " . intval($lesson_id) . " and layout = 12";
				$db->setQuery($sql);
				$media_id = $db->loadColumn();
				$media_id = @$media_id["0"];
				$type = "quiz";

				if (intval($media_id) == 0) {
					$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_m' AND type_id = " . intval($lesson_id);
					$db->setQuery($sql);
					$media_id = $db->loadColumn();
					$media_id = @$media_id["0"];
					$type = "";
				}

				$sql = "select ordering from #__guru_task where id=" . intval($lesson_id);
				$db->setQuery($sql);
				$max_ordering = $db->loadColumn();
				$max_ordering = @$max_ordering["0"];

				$name_exam = JText::_("GURU_FINAL_EXAM") . " " . $name_quiz;

				if ($type == 'quiz') {
					// check if is final quiz
					$sql = "select is_final from #__guru_quiz where id=" . intval($media_id);
					$db->setQuery($sql);
					$is_final = $db->loadColumn();
					$is_final = @$is_final["0"];

					if ($is_final == 0) { // is not final quiz
						// add final quizl);
						$sql = "INSERT INTO #__guru_task (name, alias, category, difficultylevel, points, image, published, startpublish, endpublish, metatitle, metakwd, metadesc, time, ordering, step_access) VALUES ('" . addslashes(trim($name_exam)) . "', 'final-exam', NULL, 'hard', NULL, NULL, 1, now(), '0000-00-00 00:00:00', '', '', '', 0," . ($max_ordering + 1) . ", 0)";
						$db->setQuery($sql);
						$db->execute();

						$db->setQuery("SELECT max(id) FROM #__guru_task");
						$max_id = $db->loadColumn();
						$max_id = @$max_id["0"];

						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia) VALUES ('scr_l','" . $max_id . "','12','0')");
						$db->execute();

						$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout) VALUES ('scr_m','" . $max_id . "','" . $data['final_quizzes'] . "','1',12)");
						$db->execute();

						$query = "INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no) VALUES ('dtask','" . $moduleid . "','" . $max_id . "','0','0')";
						$db->setQuery($query);
						$db->execute();
					} else { // already is final exam
						$sql = "select name from #__guru_quiz where id=" . intval($data['final_quizzes']);
						$db->setQuery($sql);
						$new_quiz_name = $db->loadColumn();

						$new_quiz_name = @$new_quiz_name["0"];
						$new_quiz_id = $data['final_quizzes'];

						$sql = "update #__guru_task set name = '" . addslashes(trim($name_exam)) . "' where id=" . intval($lesson_id);
						$db->setQuery($sql);
						$db->execute();

						$sql = "update #__guru_mediarel set media_id=" . intval($new_quiz_id) . " where type='scr_m' and type_id=" . intval($lesson_id);
						$db->setQuery($sql);
						$db->execute();
					}
				} else {
					// add final quizl);
					$sql = "INSERT INTO #__guru_task (name, alias, category, difficultylevel, points, image, published, startpublish, endpublish, metatitle, metakwd, metadesc, time, ordering, step_access) VALUES ('" . addslashes(trim($name_exam)) . "', 'final-exam', NULL, 'hard', NULL, NULL, 1, now(), '0000-00-00 00:00:00', '', '', '', 0," . ($max_ordering + 1) . ", 0)";
					$db->setQuery($sql);
					$db->execute();

					$db->setQuery("SELECT max(id) FROM #__guru_task");
					$max_id = $db->loadColumn();
					$max_id = @$max_id["0"];

					$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia) VALUES ('scr_l','" . $max_id . "','12','0')");
					$db->execute();

					$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout) VALUES ('scr_m','" . $max_id . "','" . $data['final_quizzes'] . "','1',12)");
					$db->execute();

					$query = "INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no) VALUES ('dtask','" . $moduleid . "','" . $max_id . "','0','0')";
					$db->setQuery($query);
					$db->execute();
				}
			}

			//FINAL EXAM QUIZ END

			// Email reminders
			$sql = "DELETE FROM #__guru_program_reminders WHERE product_id = '" . $progid . "' ";
			$db->setQuery($sql);
			$db->execute();

			if (!empty($data['reminders'])) {
				foreach ($data['reminders'] as $element) {
					$sql = "INSERT INTO #__guru_program_reminders (product_id ,emailreminder_id ,send)
						VALUES ('{$progid}', '{$element}', '1');";
					$sqlz[] = $sql;
					$db->setQuery($sql);
					$db->execute();
				}
			}

			//Free Courses
			if (isset($data['chb_free_courses'])) {
				$sql = "UPDATE #__guru_program set chb_free_courses = '1' where id=" . $data['id'];
				$db->setQuery($sql);
				$db->execute();
			} else {
				$sql = "UPDATE #__guru_program set chb_free_courses = '0' where id=" . $data['id'];
				$db->setQuery($sql);
				$db->execute();
			}
			if (isset($data['step_access_courses'])) {
				$sql = "UPDATE #__guru_program set step_access_courses = " . $data['step_access_courses'] . " where id=" . $data['id'];
				$db->setQuery($sql);
				$db->execute();
			}
			if (isset($data['selected_course'])) {
				$anyCourseSelected = false;
				$course_value = "";
				foreach ($data['selected_course'] as $key => $value) {
					if ($value == "-1") {
						$anyCourseSelected = true;
						break;
					} else {
						$course_value .= $value . "|";
					}
				}

				if ($anyCourseSelected) {
					$sql = "UPDATE #__guru_program set selected_course = '-1' where id=" . $data['id'];
				} else {
					$sql = "UPDATE #__guru_program set selected_course = '" . $course_value . "' where id=" . $data['id'];
				}
				$db->setQuery($sql);
				$db->execute();
			}
			//Avg certificate
			if (isset($data['avg_cert'])) {
				$sql = "UPDATE #__guru_program set avg_certc = '" . $data['avg_cert'] . "' where id=" . $data['id'];
				$db->setQuery($sql);
				$db->execute();
			}
			if (isset($data['coursemessage'])) {
				$sql = "UPDATE #__guru_program set certificate_course_msg = '" . $data['coursemessage'] . "' where id=" . $data['id'];
				$db->setQuery($sql);
				$db->execute();
			}
			if ($data['id'] > 0) {
				$sql = "UPDATE #__guru_kunena_courseslinkage set coursename = '" . addslashes($data['name']) . "' where idcourse=" . $data['id'];
				$db->setQuery($sql);
				$db->execute();

				$sql = "SELECT catidkunena  FROM #__guru_kunena_courseslinkage where idcourse=" . $data['id'] . " order by id desc limit 0,1";
				$db->setQuery($sql);
				$catidkunena = $db->loadResult();

				$sql = "SELECT coursename  FROM #__guru_kunena_courseslinkage where idcourse=" . $data['id'] . " order by id desc limit 0,1";
				$db->setQuery($sql);
				$coursename = $db->loadResult();
			}
			$return["id"] = $progid;

			if ($come_from == "courseadd") {
				//---------------------------------------
				JEventDispatcher::getInstance()->trigger('onAfterGuruCourseCreated', array(intval($progid)));
				//---------------------------------------
			}

			JFactory::getApplication()->triggerEvent('onGuruAfterSaveCourse', array($progid));

			return $return;
		}

		if (!$this->existCustomer($id)) {
			$action = JFactory::getApplication()->input->get("action", "", "raw");
			if ($action != "existing") {
				$id = $this->saveJoomlaUser();
			}
			$sql = "insert into #__guru_customer(id, company, firstname, lastname) values (" . intval($id) . ", '" . $company . "', '" . addslashes(trim($firstname)) . "', '" . $lastname . "')";
		} else {
			$sql = "update #__guru_customer set company='" . $company . "', firstname='" . addslashes(trim($firstname)) . "', lastname='" . $lastname . "' where id=" . intval($id);
		}
		$db->setQuery($sql);
		if ($db->execute()) {
			$return["error"] = TRUE;
			$return["id"] = $id;
		} else {
			$return["error"] = false;
			$return["id"] = 0;
		}
		$this->updateUserActivation($id);

		return $return;
	}

	function updateUserActivation($id)
	{
		$db = JFactory::getDBO();
		$sql = 'UPDATE #__users set block=0, activation="" where id =' . intval($id);
		$db->setQuery($sql);
		$db->execute();
	}

	function getProgramPlans($id = 0)
	{
		$data = JFactory::getApplication()->input->get->getArray();
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('id', "", "raw");

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select($db->quoteName('plan_id') . ", " . $db->quoteName('price') . ", " . $db->quoteName('default'));
		$query->from("#__guru_program_plans");
		$query->where($db->quoteName('product_id') . ' = ' . intval($id));
		$db->setQuery($query);

		$res = $db->loadObjectList();

		return $res;
	}

	function getProgramReminds($id = 0)
	{
		$data = JFactory::getApplication()->input->get->getArray();
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('id', "", "raw");

		$sql = "SELECT emailreminder_id FROM #__guru_program_reminders
                   WHERE product_id=" . intval($id);
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}

	function getProgramRenewals($id = 0)
	{
		$data = JFactory::getApplication()->input->get->getArray();
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('id', "", "raw");

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select($db->quoteName('plan_id') . ", " . $db->quoteName('price') . ", " . $db->quoteName('default'));
		$query->from("#__guru_program_renewals");
		$query->where($db->quoteName('product_id') . ' = ' . intval($id));
		$db->setQuery($query);

		$res = $db->loadObjectList();

		return $res;
	}

	function duplicateCourse()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			jimport('joomla.filesystem.folder');
			foreach ($cids as $key => $id) {
				$row = $this->getTable('guruPrograms');
				$db = JFactory::getDBO();
				// load the row from the db table
				$row->load((int) $id);

				$old_prog_id = $row->id;

				$sql = "SELECT imagesin FROM #__guru_config WHERE id = 1";
				$db->setQuery($sql);
				$configs = $db->loadResult();

				$row->name 	= JText::_('GURU_CS_COPY_TITLE') . ' ' . $row->name;
				$row->id 			= 0;
				$time_now = time();
				$min = ($time_now / 60 % 60);
				$sec = $time_now % 60;

				$increment = $min . $sec;
				$row->alias = $row->alias . $increment;


				if (!$row->check()) {
					return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
				}
				if (!$row->store()) {
					return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
				}
				$row->checkin();
				unset($row);

				$sql = "SELECT max(id) FROM #__guru_program ";
				$db->setQuery($sql);
				$new_prog_id = $db->loadColumn();
				$new_prog_id = $new_prog_id[0];

				// we will duplicate now the days from the program - begin
				$sql = "SELECT id FROM #__guru_days WHERE pid = " . $old_prog_id;
				$db->setQuery($sql);
				$the_days_array = $db->loadColumn();

				// duplicate exercise files for course ----------------------------------
				$sql = "select * from #__guru_mediarel where type='pmed' and type_id=" . intval($old_prog_id);
				$db->setQuery($sql);
				$old_exercises = $db->loadAssocList();
				if (isset($old_exercises) && count($old_exercises) > 0) {
					foreach ($old_exercises as $key => $mediarel_value) {
						$type = "pmed";
						$type_id = $new_prog_id;
						$media_id = $mediarel_value["media_id"];
						$sql = "insert into #__guru_mediarel (type, type_id, media_id) values ('" . $type . "', " . intval($type_id) . ", " . intval($media_id) . ")";
						$db->setQuery($sql);
						$db->execute();
					}
				}
				// duplicate exercise files for course ----------------------------------

				foreach ($the_days_array as $one_day) {
					$this->duplicate_day($one_day, $new_prog_id, $old_prog_id);
				}
				$this->duplicate_plans($new_prog_id, $old_prog_id);
			}
			return true;
		}
	}

	function duplicate_day($old_day_id, $prog_id, $old_prog_id)
	{
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__guru_days WHERE id = " . $old_day_id;
		$db->setQuery($sql);
		$the_day_object = $db->loadObject();

		$sql = "SELECT imagesin FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		$configs = $db->loadColumn();
		$configs = $configs[0];

		$new_image = $the_day_object->image;
		if ($the_day_object->image != '') {
			$new_image = 'copy_' . $the_day_object->image;
			// do a copy of the image on the server
			copy(JPATH_SITE . '/' . $configs . '/' . $the_day_object->image, JPATH_SITE . '/' . $configs . '/' . $new_image);
		}

		$sql = "INSERT INTO #__guru_days 
											( 
												pid , 
												title , 
												description , 
												image , 
												published ,
												startpublish,
												endpublish,
												metatitle,
												metakwd,
												metadesc,
												afterfinish,
												url,
												pagetitle,
												pagecontent,
												ordering,
												locked
									) VALUES (
												'" . $prog_id . "', 
												'" . $db->escape($the_day_object->title) . "', 
												'" . $db->escape($the_day_object->description) . "' , 
												'" . $new_image . "', 
												'" . $the_day_object->published . "',
												'" . $the_day_object->startpublish . "',
												'" . $the_day_object->endpublish . "',
												'" . $db->escape($the_day_object->metatitle) . "',
												'" . $db->escape($the_day_object->metakwd) . "',
												'" . $db->escape($the_day_object->metadesc) . "',
												'" . $the_day_object->afterfinish . "',
												'" . $the_day_object->url . "',
												'" . $db->escape($the_day_object->pagetitle) . "',
												'" . $db->escape($the_day_object->pagecontent) . "',
												'" . $db->escape($the_day_object->ordering) . "',
												'" . $the_day_object->locked . "'												
											)";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return false;
		}

		$sql = "SELECT max(id) FROM #__guru_days ";
		$db->setQuery($sql);
		$the_day_copy_id = $db->loadColumn();
		$the_day_copy_id = $the_day_copy_id[0];

		// we duplicate now the tasks + media (inside mediarel table) - BEGIN
		$sql = "SELECT * FROM #__guru_mediarel WHERE type_id = " . $old_day_id;
		$db->setQuery($sql);
		$media_rel_object_list = $db->loadObjectList();

		$task_list = '';
		foreach ($media_rel_object_list as $media_rel_object) {
			$media_id = $media_rel_object->media_id;
			$mediaforvideo = $media_id;

			if ($media_rel_object->type == "dtask") {
				$sql = "select * from #__guru_task where id=" . intval($media_rel_object->media_id);
				$db->setQuery($sql);
				$old_lesson = $db->loadAssocList();
				if (isset($old_lesson) && count($old_lesson) > 0) {
					$sql = "INSERT INTO #__guru_task (name, alias, category, difficultylevel, points, image, published, startpublish, endpublish, metatitle, metakwd, metadesc, time, ordering, step_access) VALUES ('" . addslashes(trim($old_lesson["0"]["name"])) . "', '" . addslashes(trim($old_lesson["0"]["alias"])) . "', " . intval($old_lesson["0"]["category"]) . ", '" . trim($old_lesson["0"]["difficultylevel"]) . "', " . intval($old_lesson["0"]["points"]) . ", '" . trim($old_lesson["0"]["image"]) . "', '" . $old_lesson["0"]["published"] . "', '" . $old_lesson["0"]["startpublish"] . "', '" . $old_lesson["0"]["endpublish"] . "', '" . addslashes(trim($old_lesson["0"]["metatitle"])) . "', '" . addslashes(trim($old_lesson["0"]["metakwd"])) . "', '" . addslashes(trim($old_lesson["0"]["metadesc"])) . "', " . $old_lesson["0"]["time"] . ", " . $old_lesson["0"]["ordering"] . ", " . $old_lesson["0"]["step_access"] . ")";
					$db->setQuery($sql);
					if ($db->execute()) {
						$sql = "select max(id) from #__guru_task";
						$db->setQuery($sql);
						$media_id = $db->loadResult();
					}

					$sql = "select * from #__guru_mediarel WHERE type_id = " . $mediaforvideo;
					$db->setQuery($sql);
					$media_content = $db->loadObjectList();

					foreach ($media_content as $value) {
						if ($value->type == 'scr_m' || $value->type == 'scr_t') {
							$sql = "INSERT INTO #__guru_mediarel 
													( 
														type , 
														type_id , 
														media_id , 
														mainmedia,
														layout
											) VALUES (
														'" . $value->type . "', 
														'" . $media_id . "', 
														'" . $value->media_id . "' , 
														'" . $value->mainmedia . "'	,
														'" . $value->layout . "'											
													)";
							$db->setQuery($sql);
							if (!$db->execute()) {
								return false;
							}
						} elseif ($value->type == 'scr_l') {
							$sql = "INSERT INTO #__guru_mediarel 
													( 
														type , 
														type_id , 
														media_id 
											) VALUES (
														'" . $value->type . "', 
														'" . $media_id . "', 
														'" . $value->media_id . "' 											
													)";
							$db->setQuery($sql);
							if (!$db->execute()) {
								return false;
							}
						}
					}
				}
			}

			$sql = "INSERT INTO #__guru_mediarel 
												( 
													type , 
													type_id , 
													media_id , 
													mainmedia
										) VALUES (
													'" . $media_rel_object->type . "', 
													'" . $the_day_copy_id . "', 
													'" . $media_id . "' , 
													'" . $media_rel_object->mainmedia . "'												
												)";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}
	}

	function duplicate_plans($prog_id, $old_prog_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_program_plans WHERE product_id = " . $old_prog_id;
		$db->setQuery($sql);
		$program_plans = $db->loadObjectList();
		foreach ($program_plans as $value) {
			$sql = "INSERT INTO #__guru_program_plans 
									( 
										`product_id` , 
										`plan_id` , 
										`price` , 
										`default`
							) VALUES (
										'" . $prog_id . "', 
										'" . $value->plan_id . "' , 
										'" . $value->price . "'	,
										'" . $value->default . "'											
									)";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}

		$sql = "SELECT * FROM `#__guru_program_renewals` WHERE product_id = " . $old_prog_id;
		$db->setQuery($sql);
		$program_plans = $db->loadObjectList();
		foreach ($program_plans as $value) {
			$sql = "INSERT INTO `#__guru_program_renewals` 
									( 
										product_id , 
										plan_id , 
										price , 
										`default`
							) VALUES (
										'" . $prog_id . "', 
										'" . $value->plan_id . "' , 
										'" . $value->price . "'	,
										'" . $value->default . "'											
									)";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}
	}

	function unpublishCourse()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			$db = JFActory::getDBO();
			$sql = "update #__guru_program set published='0' where id in (" . implode(", ", $cids) . ")";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
			return true;
		}
	}

	function publishCourse()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");

		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			$db = JFActory::getDBO();

			foreach ($cids as $key => $id) {
				$sql = "select status from #__guru_program where id=" . intval($id);
				$db->setQuery($sql);
				$status = $db->loadColumn();
				$status = @$status["0"];

				if (intval($status) == 0) { // Not Approved
					$this->sendEmailForAskApprove($id);
				}

				$sql = "update #__guru_program set published='1' where id=" . intval($id);
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
			return true;
		}
	}
	function unpublishMedia()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			$db = JFActory::getDBO();
			$sql = "update #__guru_media set published='0' where id in (" . implode(", ", $cids) . ")";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
			return true;
		}
	}

	function publishMedia()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			$db = JFActory::getDBO();
			$sql = "update #__guru_media set published='1' where id in (" . implode(", ", $cids) . ")";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
			return true;
		}
	}
	function unpublishMediaCat()
	{
		$db	= JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get('cid', "", "raw");
		foreach ($cid as $key => $id) {
			$sql = "update #__guru_media_categories set published=0 where id=" . $id;
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}
		return true;
	}

	function publishMediaCat()
	{
		$db	= JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get('cid', "", "raw");
		foreach ($cid as $key => $id) {
			$sql = "update #__guru_media_categories set published=1 where id=" . $id;
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}
		return true;
	}
	function unpublishQuiz()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			$db = JFActory::getDBO();
			$sql = "update #__guru_quiz set published='0' where id in (" . implode(", ", $cids) . ")";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
			return true;
		}
	}

	function publishQuiz()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		if (!isset($cids) || count($cids) == 0) {
			return FALSE;
		} else {
			$db = JFActory::getDBO();
			$sql = "update #__guru_quiz set published='1' where id in (" . implode(", ", $cids) . ")";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
			return true;
		}
	}
	function getFilters()
	{
		$user = JFactory::getUser();
		$user_id = $user->id;
		$app = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		$filters = (object)array();
		$pid = $app->getUserStateFromRequest("pid", "pid", "0");
		$sql = "SELECT lp.id as pid,name as name 
				FROM #__guru_program lp
				LEFT JOIN #__guru_days ld
				on lp.id=ld.pid 
				where (lp.author = " . intval($user_id) . " OR lp.author like '%|" . intval($user_id) . "|%')
				GROUP BY lp.id, name ORDER BY name ASC";
		$db->setQuery($sql);
		$allprg = $db->loadObjectList();
		$filters->pid  =  JHTML::_('select.genericlist', $allprg, 'pid', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', "pid", "name", $pid);
		return $filters;
	}

	function getlistDays()
	{
		$pid = JFactory::getApplication()->input->get("pid", "0", "raw");
		if ($pid == 0) {
			$sql = "SELECT * FROM #__guru_days WHERE 
					pid = ( SELECT id FROM #__guru_program WHERE id in 
					( SELECT pid FROM #__guru_days GROUP BY pid ) 
					ORDER BY name ASC LIMIT 1 ) ORDER BY ordering ASC";

			$this->_total = $this->_getListCount($sql);
			$this->_modules = $this->_getList($sql);
		} else {
			$sql = "SELECT * FROM #__guru_days WHERE pid =" . $pid . " ORDER BY ordering ASC ";
			$this->_modules = $this->_getList($sql);
			$this->_total = $this->_getListCount($sql);
		}
		return $this->_modules;
	}

	function getlistDaysJumps()
	{
		$pid = JFactory::getApplication()->input->get("progrid", "0", "raw");
		if ($pid == 0) {
			$sql = "SELECT * FROM #__guru_days WHERE 
					pid = ( SELECT id FROM #__guru_program WHERE id in 
					( SELECT pid FROM #__guru_days GROUP BY pid ) 
					ORDER BY name ASC LIMIT 1 ) ORDER BY ordering ASC";

			$this->_total = $this->_getListCount($sql);
			$this->_modules = $this->_getList($sql);
		} else {
			$sql = "SELECT * FROM #__guru_days WHERE pid =" . $pid . " ORDER BY ordering ASC ";
			$this->_modules = $this->_getList($sql);
			$this->_total = $this->_getListCount($sql);
		}
		return $this->_modules;
	}


	function getday()
	{
		$db = JFactory::getDBO();
		$data = JFactory::getApplication()->input->post->getArray();

		$cids = JFactory::getApplication()->input->get('cid', array(), "raw");

		if (!is_array($cids)) {
			$cids = array($cids);
		}

		$id = intval($cids[0]);

		if (empty($this->_attribute)) {
			$this->_attribute = $this->getTable("guruDays");
			$this->_attribute->load($id);
		}

		if (!$this->_attribute->bind($data)) {
			return false;
		}

		if (!$this->_attribute->check()) {
			return false;
		}

		if ($this->_attribute->id > 0) {
			$this->_attribute->text = JText::_('New');
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_task as b WHERE a.type='dtask' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=" . $this->_attribute->id);
			$this->_attribute->alltasks = $db->loadObjectList();
			$db->setQuery("SELECT a.*,b.* FROM #__guru_mediarel as a, #__guru_media as b WHERE a.type='dmed' AND a.media_id=b.id AND a.mainmedia=0 AND a.type_id=" . $this->_attribute->id);
			$this->_attribute->daymmedia = $db->loadObjectList();
			$this->_attribute->nodegroup = @$data['node'];
		} else {
			$this->_attribute->text = JText::_('Edit');
			$this->_attribute->alltasks = new stdClass();
			$this->_attribute->daymmedia = new stdClass();
			if (intval(@$data['pid']) > 0) {
				$this->_attribute->ordering = $this->order_for_last_day($data['pid']) + 1;
				$this->_attribute->nodegroup = $this->id_for_last_day($data['pid']) + 1;
			} else {
				$this->_attribute->ordering = 1;
				$this->_attribute->nodegroup = 1;
			}
			$jnow = new JDate('now');
			$this->_attribute->startpublish =  $jnow->toSQL(true);
		}

		if (substr($this->_attribute->endpublish, 0, 4) == '0000' || $this->_attribute->id < 1) {
			$this->_attribute->endpublish = JText::_('GURU_NEVER');
		}
		$javascript = 'onchange="document.adminForm.submit();"';
		$sql = "SELECT id as pid,name as name FROM #__guru_program WHERE 1=1 ORDER BY name ASC";
		$db->setQuery($sql);
		$allprg = $db->loadObjectList();
		$this->_attribute->lists['pid']  =  JHTML::_('select.genericlist', $allprg, 'pid', 'class="inputbox" size="1"' . $javascript, "pid", "name", $this->_attribute->pid);

		if (!isset($this->_attribute->published)) {
			$this->_attribute->published = 1;
		}
		$this->_attribute->lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->_attribute->published);

		if (!isset($this->_attribute->locked))
			$this->_attribute->locked = 0;
		$locked = array();
		$locked[] = JHTML::_('select.option', "0", JText::_('GURU_DAY_LOCKED_N'));
		$locked[] = JHTML::_('select.option', "1", JText::_('GURU_DAY_LOCKED_Y'));
		$this->_attribute->lists['locked']  =  JHTML::_('select.genericlist', $locked, 'locked', 'class="inputbox" size="1"', "value", "text", $this->_attribute->locked);

		if (!isset($this->_attribute->afterfinish)) {
			$this->_attribute->afterfinish = 0;
		}
		$afterfinish = array();
		$javascript = 'onChange="javascript: displayblock(this.value);"';
		$afterfinish[] = JHTML::_('select.option', '0', JText::_('GURU_DAY_AFTER_FINNISH1'));
		$afterfinish[] = JHTML::_('select.option', '1', JText::_('GURU_DAY_AFTER_FINNISH2'));
		$afterfinish[] = JHTML::_('select.option', '2', JText::_('GURU_DAY_AFTER_FINNISH3'));
		$this->_attribute->lists['afterfinish']  =  JHTML::_('select.genericlist', $afterfinish, 'afterfinish', 'class="inputbox" size="1" ' . $javascript, "value", "text", $this->_attribute->afterfinish);

		return $this->_attribute;
	}

	function store_new_module()
	{
		$db =  JFactory::getDBO();
		$pid = JFactory::getApplication()->input->get("pid", "", "raw");
		$title = JFactory::getApplication()->input->get("title", "", "raw");
		$alias = JFilterOutput::stringURLSafe($title);
		$jnow = new JDate('now');
		$startpublish = $jnow->toSQL(true);
		$endpublish = '0000-00-00 00:00:00';

		$sql = "select max(ordering) from #__guru_days where pid=" . intval($pid);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if (!isset($result[0]) && $result[0] == NULL) {
			$result[0] = 0;
		}

		$media_id = JFactory::getApplication()->input->get("db_media_1", "0", "raw");
		$access = JFactory::getApplication()->input->get("access", "0", "raw");

		$sql = "insert into #__guru_days (pid, title, alias, published, startpublish, endpublish, locked, afterfinish, ordering, media_id, access) values (" . $pid . ", '" . addslashes(trim($title)) . "', '" . trim($alias) . "', 1, '" . $startpublish . "', '" . $endpublish . "', 0, 0, " . ($result[0] + 1) . ", " . intval($media_id) . ", " . intval($access) . ")";

		$db->setQuery($sql);
		$db->setQuery($sql);

		if ($db->execute()) {
			$sql = "select max(id) from #__guru_days where pid=" . intval($pid);
			$db->setQuery($sql);
			$module_id = $db->loadColumn();
			$module_id = @$module_id["0"];

			$sql = "select id_final_exam from #__guru_program where id=" . intval($pid);
			$db->setQuery($sql);
			$id_final_exam = $db->loadColumn();
			$id_final_exam = @$id_final_exam["0"];

			if (intval($id_final_exam) > 0) {
				$sql = "select type_id from #__guru_mediarel where type='scr_m' and media_id=" . intval($id_final_exam) . " and layout='12' order by id desc limit 0,1";
				$db->setQuery($sql);
				$type_id = $db->loadColumn();
				$type_id = @$type_id["0"];

				if (intval($type_id) > 0) {
					$sql = "select type_id from #__guru_mediarel where type='dtask' and media_id=" . intval($type_id);
					$db->setQuery($sql);
					$final_module_id = $db->loadColumn();
					$final_module_id = @$final_module_id["0"];

					if (intval($final_module_id) == 0) {
						$sql = "update #__guru_mediarel set type_id=" . intval($module_id) . " where type='dtask' and media_id=" . intval($type_id);
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}

			return true;
		}
		return false;
	}

	function store_module()
	{
		$db = JFactory::getDBO();
		$item = $this->getTable('guruDays');
		$data = JFactory::getApplication()->input->post->getArray();

		$data['pagecontent'] = JFactory::getApplication()->input->get('pagecontent', '', 'raw');
		$data['description'] = JFactory::getApplication()->input->get('description', '', 'raw');

		$data['media_id'] = $data["db_media_1"];

		$data['alias'] = JFilterOutput::stringURLSafe($data['title']);

		$res = true;

		if (!$item->bind($data)) {
			$res = false;
		}
		if (!$item->check()) {
			$res = false;
		}
		if (!$item->store()) {
			$res = false;
		}

		$data['id'] = JFactory::getApplication()->input->get('newdayid', "0", "raw");

		$data['mediafilesday'] = substr(@$data['mediafilesday'], 1, -1);
		$mediafilesday = explode(",,", $data['mediafilesday']);
		for ($i = 0; $i < count($mediafilesday); $i++)
			$this->addtask($mediafilesday[$i], $data['id'], "0");

		$sql = "SELECT influence FROM #__guru_config WHERE id = 1";
		$db->setQuery($sql);
		$influence = $db->loadResult(); // we have selected the INFLUENCE 

		$sql = "SELECT locked FROM #__guru_days WHERE id = " . $data['id'];
		$db->setQuery($sql);
		$locked = $db->loadResult(); // we have selected the LOCKED property for a day 		

		if ($influence == 1 && $locked == 0) {
			$sql = "SELECT lp.id,days,tasks FROM #__guru_programstatus  lp
					LEFT JOIN #__guru_days ld
					ON lp.pid=ld.id
					WHERE lp.id = " . $data['id'] . "
					GROUP BY lp.id";
			$db->setQuery($sql);
			$days_array = $db->loadObjectList();

			foreach ($days_array as $one_day_array) {
				$one_day_array_days = $one_day_array->days;
				$one_day_array_id = $one_day_array->id;
				$one_day_array_tasks = $one_day_array->tasks;

				$pos = strpos($one_day_array_days, $data['id'] . ',');
				if ($pos === false) { // if the day is not in the programstatus_table (NEW DAY) we add it at "the end" - begin
					$new_day_array = $one_day_array_days . $data['id'] . '-0;';

					$task_status = $one_day_array_tasks . ';';

					$sql = "UPDATE #__guru_programstatus SET 
								days='" . $new_day_array . "', tasks = '" . $task_status .
						"'	where id = '" . $one_day_array_id . "' ";
					$db->setQuery($sql);
					$db->execute();
				}
			} //endforeach
		}
		return true;
	}
	function getAllMediaCategory()
	{
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_media_categories";
		$db->setQuery($sql);
		$result = $db->loadAssocList("id");
		return $result;
	}

	function getfiltersMedia()
	{
		$app = JFactory::getApplication("site");
		$db = JFactory::getDBO();
		$status  = $app->getUserStateFromRequest("media_publ_status", "media_publ_status", "YN", "string");
		$type  = $app->getUserStateFromRequest('media_type', 'media_type', " ", 'string');
		$media_category = $app->getUserStateFromRequest('media_category', 'media_category', " ", 'string');
		$filter = (object)array();

		$statusOption = array();
		$javascript = "onchange='document.adminForm.submit();'";
		$statusOption[] = JHTML::_("select.option", JText::_("GURU_STATUS"), "YN");
		$statusOption[] = JHTML::_("select.option", JText::_("GURU_PUBLISHED"), "Y");
		$statusOption[] = JHTML::_("select.option", JText::_("GURU_UNPUBLISHED"), "N");

		$filter->status = JHTML::_("select.genericlist", $statusOption, "media_publ_status", "size=1 " . $javascript . ' class="input-small"', "text", "value", $status);

		$typeOption = array();
		$javascript = "onchange='document.adminForm.submit();'";

		$typeOption[] = JHTML::_("select.option", JText::_("GURU_TYPE"), "-");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_VIDEO"), "video");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_AUDIO"), "audio");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_DOCS"), "docs");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_URL"), "url");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_IMAGE"), "image");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_text"), "text");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_MEDIATYPEFILE_"), "file");

		$filter->type = JHTML::_("select.genericlist", $typeOption, "media_type", "size=1 " . $javascript . ' class="input-small"', "text", "value", $type);

		$sql = "select id, name from #__guru_media_categories";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		$categoryOption = array();
		$categoryOption[] = JHTML::_("select.option", JText::_("GURU_CATEGORY"), "-");
		if (isset($result) && count($result) > 0) {
			foreach ($result as $key => $value) {
				$categoryOption[] = JHTML::_("select.option", $value["name"], $value["id"]);
			}
		}
		$filter->media_category = JHTML::_("select.genericlist", $categoryOption, "media_category", "size=1 " . $javascript, "text", "value", $media_category);

		return $filter;
	}

	function addtask($toinsert, $taskid, $mainmedia)
	{
		$db = JFactory::getDBO();
		$sql = "INSERT INTO #__guru_mediarel ( id , type , type_id , media_id , mainmedia ) VALUES ('', 'dtask', '" . $taskid . "' , '" . $toinsert . "', '" . $mainmedia . "');";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return false;
		}
		return true;
	}

	function getlistaddmedia()
	{
		$data_post = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$type = JFactory::getApplication()->input->get("type", "", "raw");

		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$condition = array();
		$user = JFactory::getUser();

		$sql = "SELECT m.*, mc.name as categ_name FROM #__guru_media m LEFT OUTER JOIN #__guru_media_categories mc on mc.id=m.category_id where author=" . intval($user->id) . " ";

		if ($type != "") {
			$sql .= "AND m.type='" . $type . "' ";
		}

		if ($task != 'addtext') {
			$search_text = JFactory::getApplication()->input->get('search_text', "null", "raw");

			if ($search_text == "null") {
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$search_value = $registry->get("search_value", "");

				if (isset($search_value) && $search_value != "") {
					$search_text = $search_value;
				}
			}

			if ($search_text != "null" && $search_text != "") {
				$sql = $sql . " AND m.name LIKE '%" . addslashes(JFactory::getApplication()->input->get('search_text')) . "%' ";
				$registry->set("search_value", $search_text);
			}

			if (isset($data_post['filter_type'])) {
				if ($data_post['filter_type'] != '' && $data_post['filter_type'] != NULL) {
					$sql .= " AND m.type='" . $data_post['filter_type'] . "'";
				} elseif ($data_post['filter_type'] == NULL) {
					$session = JFactory::getSession();
					$registry = $session->get('registry');
					$registry->set("filter_type_tskmed", "");
				}
			}

			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$filter_status_tskmed = $registry->get("filter_status_tskmed", "");

			if (isset($data_post['filter_status']) && ($data_post['filter_status'] != '')) {
				if ($data_post['filter_status'] == '1') {
					$sql .= " AND m.published=1 ";
				} elseif ($data_post['filter_status'] == '2') {
					$sql .= " AND m.published=0 ";
				}
			} elseif (isset($data_post['filter2']) && ($data_post['filter2'] != '') && ($data_post['filter2'] != 0)) {
				if ($data_post['filter2'] == '1') {
					$sql .= " AND m.published=1 ";
				} elseif ($data_post['filter2'] == '2') {
					$sql .= " AND m.published=0 ";
				}
			} elseif (isset($filter_status_tskmed) && ($filter_status_tskmed != '')) {
				if ($filter_status_tskmed == '1') {
					$sql .= " AND m.published=1 ";
				} elseif ($filter_status_tskmed == '2') {
					$sql .= " AND m.published=0 ";
				}
			}
			if (isset($data_post['filter_status'])) {
				$registry->set("filter_status_tskmed", $data_post['filter_status']);
			} elseif (isset($data_post['filter2'])) {
				$registry->set("filter_status_tskmed", $data_post['filter2']);
			}
		}

		$media_category = JFactory::getApplication()->input->get("filter_media", "", "raw");

		$session = JFactory::getSession();
		$registry = $session->get('registry');

		if ($media_category == "") {
			$media_category = $registry->get("filter_media", "");
		} elseif ($media_category == "-1") {
			$registry->set("filter_media", $media_category);
		}

		if ($media_category != "" && $media_category != "-1") {
			$sql .= " AND m.category_id=" . intval($media_category);
		}

		$search_text = JFactory::getApplication()->input->get('search_text', "null", "raw");
		$search_value = $registry->get("search_value", "");

		if ($search_text == "null") {
			if (isset($search_value) && $search_value != "") {
				$search_text = $search_value;
			}
		} elseif ($search_text == "") {
			$registry->set("search_value", "");
		}

		if ($search_text != "null" && $search_text != "") {
			$sql = $sql . " AND m.name LIKE '%" . $search_text . "%' ";
			$registry->set("search_value", $search_text);
		}

		if ($task == 'addmedia' && $type != "quiz" && $type != "text") {
			$sql .= " AND m.type <> 'text' AND m.type <> 'quiz' ";
		} elseif ($task == 'addmedia' && $type == "quiz") {
			$sql .= " AND m.type='quiz' ";
		} else {
			$sql .= " AND m.type='text' ";
		}

		$sql .= " order by m.id desc ";


		$limit_cond = NULL;

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		if ($limit != 0) {
			$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
		}

		$db->setQuery($sql . $limit_cond);
		$medias = $db->loadObjectList();
		$this->_total = $this->_getListCount($sql);

		if (($this->_total > 1) && (count($medias) == 0)) {
			$limit_cond = NULL;
			if ($limit != 0) {
				$limit_cond = " LIMIT 0," . $limit . " ";
			}
			$db->setQuery($sql . $limit_cond);
			$medias = $db->loadObjectList();
		}
		return $medias;
	}

	function getlistProjects()
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');
		$user = JFactory::getUser();

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		$limit_cond = "";

		if ($limit != 0) {
			$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
		}

		$search_text = JFactory::getApplication()->input->get('search_project', "", "raw");
		$and = "";
		if ($search_text != "") {
			$and = " AND title like '%" . $search_text . "%' ";
		}

		$sql = "SELECT count(*) FROM  #__guru_projects WHERE published=1 " . $and;
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = @$result["0"];
		$this->_total = intval($result);

		$sql = "SELECT * FROM #__guru_projects WHERE `published`=1 and `author_id`=" . intval($user->id) . " " . $and . $limit_cond;
		$db->setQuery($sql);

		return $db->loadObjectList();
	}

	function getlistScorm()
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');
		$user = JFactory::getUser();

		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		$limit_cond = "";

		if ($limit != 0) {
			$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
		}

		$search_text = JFactory::getApplication()->input->get('search_scorm', "", "raw");
		$and = "";

		if ($search_text != "") {
			$and = " AND title like '%" . $search_text . "%' ";
		}

		$pid = JFactory::getApplication()->input->get('pid', "0", "raw");

		if (intval($pid) > 0) {
			$and .= " AND `course_id`=" . intval($pid);
		}

		$sql = "SELECT count(*) FROM  #__guru_scorm_items WHERE published=1 " . $and;
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = @$result["0"];
		$this->_total = intval($result);

		$sql = "SELECT * FROM #__guru_scorm_items WHERE `published`=1 and `author_id`=" . intval($user->id) . " " . $and . $limit_cond;
		$db->setQuery($sql);

		return $db->loadObjectList();
	}

	function getTask()
	{
		$id = JFactory::getApplication()->input->get("cid", "0", "raw");
		if (empty($this->_attribute)) {
			$this->_attribute = $this->getTable("guruTasks");
			$this->_attribute->load($id);
		}
		$data = JFactory::getApplication()->input->post->getArray();

		if (!$this->_attribute->bind($data)) {
			return false;
		}

		if (!$this->_attribute->check()) {
			return false;
		}

		return $this->_attribute;
	}

	function parse_media_preview($media)
	{
		$configs = $this->getConfig();

		$no_plugin_for_code = 0;
		$aheight = 0;
		$awidth = 0;
		$vheight = 0;
		$vwidth = 0;

		if ($media->type === 'zoom') {
			$data = new JRegistry($media->code);
			$html = '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_ID').': ' . $data->get('meeting_id') . '</h3>';
			$html .= '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD').': ' . $data->get('meeting_pass') . '</h3>';
			$html .= '<div><a class="btn btn-primary" target="_blank" href="' . $data->get('invite_link') . '" >'.JText::_('GURU_ZOOM_CLICK_TO_JOIN').'</a></div>';

			return $html;
		}

		//start video
		if ($media->type == 'video') {
			if ($media->source == 'url' || $media->source == 'local') {
				if ($media->width == 0 || $media->height == 0) {
					$media->width = 300;
					$media->height = 400;
				}
			} elseif ($media->source == 'code') {
				if ($media->width == 0 || $media->height == 0) {
					//parse the code to get the width and height
					$begin_tag = strpos($media->code, 'width="');
					if ($begin_tag !== false) {
						$remaining_code = substr($media->code, $begin_tag + 7, strlen($media->code));
						$end_tag = strpos($remaining_code, '"');
						$media->width = substr($remaining_code, 0, $end_tag);
						$begin_tag = strpos($media->code, 'height="');
						if ($begin_tag !== false) {
							$remaining_code = substr($media->code, $begin_tag + 8, strlen($media->code));
							$end_tag = strpos($remaining_code, '"');
							$media->height = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						} else {
							$media->height = 300;
							$media->width = 400;
						}
					} else {
						$media->height = 300;
						$media->width = 400;
					}
				} else {
					$replace_with = 'width="' . $media->width . '"';
					$media->code = preg_replace('#width="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'height="' . $media->height . '"';
					$media->code = preg_replace('#height="[0-9]+"#', $replace_with, $media->code);

					$replace_with = 'name="width" value="' . $media->width . '"';
					$media->code = preg_replace('#name="width" value="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'name="height" value="' . $media->height . '"';
					$media->code = preg_replace('#name="height" value="[0-9]+"#', $replace_with, $media->code);
				}
			}
			$vwidth = $media->width;
			$vheight = $media->height;
		}
		//end video

		//start audio	
		elseif ($media->type == 'audio') {
			if ($media->source == 'url' || $media->source == 'local') {
				if ($media->width == 0 || $media->height == 0) {
					$media->width = 20;
					$media->height = 300;
				}
			} elseif ($media->source == 'code') {
				if ($media->width == 0 || $media->height == 0) {
					$begin_tag = strpos($media->code, 'width="');
					if ($begin_tag !== false) {
						$remaining_code = substr($media->code, $begin_tag + 7, strlen($media->code));
						$end_tag = strpos($remaining_code, '"');
						$media->width = substr($remaining_code, 0, $end_tag);
						$begin_tag = strpos($media->code, 'height="');
						if ($begin_tag !== false) {
							$remaining_code = substr($media->code, $begin_tag + 8, strlen($media->code));
							$end_tag = strpos($remaining_code, '"');
							$media->height = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						} else {
							$media->height = 20;
							$media->width = 300;
						}
					} else {
						$media->height = 20;
						$media->width = 300;
					}
				} else {
					$replace_with = 'width="' . $media->width . '"';
					$media->code = preg_replace('#width="[0-9]+"#', $replace_with, $media->code);
					$replace_with = 'height="' . $media->height . '"';
					$media->code = preg_replace('#height="[0-9]+"#', $replace_with, $media->code);
				}
			}
			$awidth = $media->width;
			$aheight = $media->height;
		}

		$parts = explode(".", $media->local);
		$extension = strtolower($parts[count($parts) - 1]);

		if ($media->type == 'video' || $media->type == 'audio') {
			if ($media->type == 'video' && $extension == "avi") {
				$media->code = '<object id="MediaPlayer1" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" type="application/x-oleobject" width="' . $media->width . '" height="' . $media->height . '">
<param name="fileName" value="' . JURI::root() . $configs->videoin . "/" . $media->local . '">
<param name="animationatStart" value="true">
<param name="transparentatStart" value="true">
<param name="autoStart" value="true">
<param name="showControls" value="true">
<param name="Volume" value="10">
<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" src="' . JURI::root() . $configs->videoin . "/" . $media->local . '" name="MediaPlayer1" width="' . $media->width . '" height="' . $media->height . '" autostart="1" showcontrols="1" volume="10">
</object>';
			} elseif ($no_plugin_for_code == 0) {
				$helper = new guruHelper();
				$media->code = $helper->create_media_using_plugin($media, $configs, $awidth, $aheight, $vwidth, $vheight);
			}
		}
		//end audio

		//start docs type
		if ($media->type == 'docs') {
			//$media->code = 'The selected element is a text file that can\'t have a preview';
			$media->code = JText::_("GURU_NO_PREVIEW");

			if ($media->source == 'local' && (substr($media->local, (strlen($media->local) - 3), 3) == 'txt' || substr($media->local, (strlen($media->local) - 3), 3) == 'pdf') && $media->width > 1) {
				if (substr($media->local, (strlen($media->local) - 3), 3) == 'xls') {
					include_once(JPATH_SITE . DS . "components" . DS . "com_guru" . DS . "helpers" . DS . "excel_reader.php");
					$data = new Spreadsheet_Excel_Reader(JPATH_SITE . DS . $configs->docsin . '/' . $media->local);
					$boundsheets = $data->boundsheets;

					if (isset($boundsheets) && count($boundsheets) > 0) {
						$sheets = array();

						foreach ($boundsheets as $key => $sheet) {
							$display = "none";
							$sheets[$key] = $sheet["name"];

							if ($key == 0) {
								$display = "block";
							}

							$media->code .= '<div class="contentpane excel-content" id="sheet-' . intval($key) . '" style="display:' . $display . ';" >' . $data->dump(true, true, $key) . '</div>';
						}

						if (isset($sheets) && count($sheets) > 0) {
							foreach ($sheets as $key => $sheet) {
								$btn_class = "uk-button uk-button-primary wk-button wk-button-primary sheet-btn";

								if ($key == 0) {
									$btn_class = "uk-button uk-button-success wk-button wk-button-success sheet-btn";
								}

								$media->code .= '<input type="button" id="btn-sheet-' . $key . '" class="' . $btn_class . '" value="' . $sheet . '" onclick="changeSheet(' . intval($key) . ')" />';
							}
						}

						$media->code .= '<input type="hidden" id="nr-sheets" value="' . intval(count($sheets)) . '" />';
					}
				} else {
					$media->code = '<div class="contentpane">
									<iframe id="blockrandom" name="iframe" src="' . JURI::root() . $configs->docsin . '/' . $media->local . '" width="' . $media->width . '" height="' . $media->height . '" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
								  </div>';
				}

				if ($media->show_instruction == 2) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				} elseif ($media->show_instruction == 1) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br /><div  style="text-align:center"><i>' . $media->instructions . '</i></div>';
				} elseif ($media->show_instruction == 0) {
					$media->code = '<div  style="text-align:center"><i>' . $media->instructions . '</i></div>' . stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				}

				return 	$media->code;
			} elseif ($media->source == 'url' && (substr($media->url, (strlen($media->url) - 3), 3) == 'txt' || substr($media->url, (strlen($media->url) - 3), 3) == 'pdf') && $media->width > 1) {
				$media->code = '<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="' . $media->url . '" width="' . $media->width . '" height="' . $media->height . '" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
						</div>';
				if ($media->show_instruction == 2) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				} elseif ($media->show_instruction == 1) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br /><div  style="text-align:center"><i>' . $media->instructions . '</i></div>';
				} elseif ($media->show_instruction == 0) {
					$media->code = '<div  style="text-align:center"><i>' . $media->instructions . '</i></div>' . stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				}
				return 	$media->code;
			} elseif ($media->source == 'local' && $media->width == 1) {
				$media->code = '<br /><a href="' . JURI::root() . $configs->docsin . '/' . $media->local . '" target="_blank">' . $media->name . '</a>';
				if ($media->show_instruction == 2) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				} elseif ($media->show_instruction == 1) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br /><div  style="text-align:center"><i>' . $media->instructions . '</i></div>';
				} elseif ($media->show_instruction == 0) {
					$media->code = '<div  style="text-align:center"><i>' . $media->instructions . '</i></div>' . stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				}
				return 	$media->code;
			} elseif ($media->source == 'url'  && $media->width == 0) {
				$media->code = '<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="' . $media->url . '" width="100%" height="600" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe> </div>';
			} else if ($media->source == 'url'  && $media->width == 1) {
				$media->code = '<a href="' . $media->url . '" target="_blank">' . $media->name . '</a>';
			}
		}
		//end doc

		//start url
		if ($media->type == 'url') {
			if ($media->width > 1) {
				$media->code = '<div class="contentpane">
							<iframe id="blockrandom" name="iframe" src="' . $media->url . '" width="100%" height="700px" scrolling="auto" align="top" frameborder="2" class="wrapper"> This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
						</div>';
				if ($media->show_instruction == 2) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				} elseif ($media->show_instruction == 1) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br /><div  style="text-align:center"><i>' . $media->instructions . '</i></div>';
				} elseif ($media->show_instruction == 0) {
					$media->code = '<div  style="text-align:center"><i>' . $media->instructions . '</i></div>' . stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				}
				return 	$media->code;
			} else {
				$media->code = '<a href="' . $media->url . '" target="_blank">' . $media->url . '</a>';
			}
		}
		//end url

		//start image				
		if ($media->type == 'image') {
			$size = "";
			if (intval($media->width) != '0') {
				$size = 'width="' . $media->width . '"';
			} else {
				$size = 'height="' . $media->height . '"';
			}
			$media->code = '<img ' . $size . ' src="' . JURI::root() . $configs->imagesin . '/media/thumbs/' . $media->local . '" />';
		}
		//end image

		//start text
		if ($media->type == 'text') {
			$media->code = $media->code;
		}
		//end text

		//start docs type
		if ($media->type == 'file') {
			$media->code = JText::_('GURU_NO_PREVIEW');
			$x = filesize(JPATH_SITE . DIRECTORY_SEPARATOR . $configs->filesin . '/' . $media->local) / (1024 * 1024);
			$x = number_format($x, 2, '.', '');
			if ($media->source == 'local' && (substr($media->local, (strlen($media->local) - 3), 3) == 'zip' || substr($media->local, (strlen($media->local) - 3), 3) == 'exe')) {
				$media->code = '<br /><a href="' . JURI::root() . $configs->filesin . '/' . $media->local . '" target="_blank">' . $media->local . " (" . $x . " Ko)" . '</a>';
				//return stripslashes($media->code);
				if ($media->show_instruction == 2) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				} elseif ($media->show_instruction == 1) {
					$media->code = stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br /><div  style="text-align:center"><i>' . $media->instructions . '</i></div>';
				} elseif ($media->show_instruction == 0) {
					$media->code = '<div  style="text-align:center"><i>' . $media->instructions . '</i></div>' . stripslashes($media->code) . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
				}
				return 	$media->code;
			} else if ($media->source == 'url') {
				$media->code = '<a href="' . $media->url . '" target="_blank">' . $media->name . '</a>';
			}
		}
		//end doc
		if ($media->type == 'text') {
			$media->code = $media->code;
		} else {
			if ($media->show_instruction == 2) {
				$media->code = $media->code . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
			} elseif ($media->show_instruction == 1) {
				$media->code = $media->code . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br /><div  style="text-align:center"><i>' . $media->instructions . '</i></div>';
			} elseif ($media->show_instruction == 0) {
				$media->code = '<div  style="text-align:center"><i>' . $media->instructions . '</i></div>' . $media->code . '<br /><br /><div style="text-align:center"><i>' . $media->name . '</i></div><br />';
			}
		}

		return stripslashes($media->code);
	}

	function parse_media($id, $layout_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$configs = $db->loadObject();

		if (!isset($media)) {
			$media = "";
		}

		$default_size = $configs->default_video_size;
		$default_width = "";
		$default_height = "";
		if (trim($default_size) != "") {
			$default_size = explode("x", $default_size);
			$default_width = $default_size["1"];
			$default_height = $default_size["0"];
		}

		if ($layout_id != 15 && $layout_id != 16 && $layout_id != 17) {
			$sql = "SELECT * FROM #__guru_media WHERE id = " . $id;
			$db->setQuery($sql);
			$the_media = $db->loadObject();
			@$the_media->code = stripslashes($the_media->code);
		} elseif ($layout_id == 15) {
			$sql = "SELECT * FROM #__guru_quiz
						WHERE id = " . $id;
			$db->setQuery($sql);
			$the_media = $db->loadObject();
			@$the_media->type = "quiz";
			$the_media->code = "";
		} elseif ($layout_id == 16) {
			$sql = "SELECT * FROM #__guru_projects WHERE id = " . $id;
			$db->setQuery($sql);
			$the_media = $db->loadObject();

			@$the_media->type = "project";
			$the_media->code = "";
		} elseif ($layout_id == 17) {
			$sql = "SELECT * FROM #__guru_scorm_items WHERE id = " . $id;
			$db->setQuery($sql);
			$the_media = $db->loadObject();

			@$the_media->type = "scorm";
			$the_media->code = "";
		}

		$no_plugin_for_code = 0;
		$aheight = 0;
		$awidth = 0;
		$vheight = 0;
		$vwidth = 0;

		if (@$the_media->type == 'video') {
			if (intval($default_width) == 0) {
				$default_width = "100%";
			}

			if ($the_media->source == 'url' || $the_media->source == 'local') {
				if (($the_media->width == 0 || $the_media->height == 0) && $the_media->option_video_size == 1) {
					$vheight = 300;
					$vwidth = 400;
				} elseif (($the_media->width != 0 && $the_media->height != 0) && $the_media->option_video_size == 1) {
					$vheight = $the_media->height;
					$vwidth = $the_media->width;
				} elseif ($the_media->option_video_size == 0) {
					$vheight = $default_height;
					$vwidth = $default_width;
				}
			} elseif ($the_media->source == 'code') {
				if (($the_media->width == 0 || $the_media->height == 0) && $the_media->option_video_size == 1) {
					$begin_tag = strpos($the_media->code, 'width="');
					if ($begin_tag !== false) {
						$remaining_code = substr($the_media->code, $begin_tag + 7, strlen($the_media->code));
						$end_tag = strpos($remaining_code, '"');
						$vwidth = substr($remaining_code, 0, $end_tag);
						$begin_tag = strpos($the_media->code, 'height="');
						if ($begin_tag !== false) {
							$remaining_code = substr($the_media->code, $begin_tag + 8, strlen($the_media->code));
							$end_tag = strpos($remaining_code, '"');
							$vheight = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						} else {
							$vheight = 300;
							$vwidth = 400;
						}
					} else {
						$vheight = 300;
						$vwidth = 400;
					}
				} elseif (($the_media->width != 0 || $the_media->height != 0) && $the_media->option_video_size == 1) {
					$replace_with = 'width="' . $the_media->width . '"';
					$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'height="' . $the_media->height . '"';
					$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'name="width" value="' . $the_media->width . '"';
					$the_media->code = preg_replace('#name="width" value="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'name="height" value="' . $the_media->height . '"';
					$the_media->code = preg_replace('#name="height" value="[0-9]+"#', $replace_with, $the_media->code);
					$vheight = $the_media->height;
					$vwidth = $the_media->width;
				} elseif ($the_media->option_video_size == 0) {
					$replace_with = 'width="' . $default_width . '"';
					$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'height="' . $default_height . '"';
					$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);

					$replace_with = 'name="width" value="' . $default_width . '"';
					$the_media->code = preg_replace('#value="[0-9]+" name="width"#', $replace_with, $the_media->code);
					$replace_with = 'name="height" value="' . $default_height . '"';
					$the_media->code = preg_replace('#value="[0-9]+" name="height"#', $replace_with, $the_media->code);

					$replace_with = 'name="width" value="' . $default_width . '"';
					$the_media->code = preg_replace('/name="width" value="[0-9]+"/', $replace_with, $the_media->code);
					$replace_with = 'name="height" value="' . $default_height . '"';
					$the_media->code = preg_replace('/name="height" value="[0-9]+"/', $replace_with, $the_media->code);

					$vheight = $default_height;
					$vwidth = $default_width;
				}
			}
		} elseif (@$the_media->type == 'audio') {
			if ($the_media->source == 'url' || $the_media->source == 'local') {
				if ($the_media->width == 0 || $the_media->height == 0) {
					$aheight = 20;
					$awidth = 300;
				} else {
					$aheight = $the_media->height;
					$awidth = $the_media->width;
				}
			} elseif ($the_media->source == 'code') {
				if ($the_media->width == 0 || $the_media->height == 0) {
					$begin_tag = strpos($the_media->code, 'width="');
					if ($begin_tag !== false) {
						$remaining_code = substr($the_media->code, $begin_tag + 7, strlen($the_media->code));
						$end_tag = strpos($remaining_code, '"');
						$awidth = substr($remaining_code, 0, $end_tag);

						$begin_tag = strpos($the_media->code, 'height="');
						if ($begin_tag !== false) {
							$remaining_code = substr($the_media->code, $begin_tag + 8, strlen($the_media->code));

							$end_tag = strpos($remaining_code, '"');
							$aheight = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						} else {
							$aheight = 20;
							$awidth = 300;
						}
					} else {
						$aheight = 20;
						$awidth = 300;
					}
				} else {
					$replace_with = 'width="' . $the_media->width . '"';
					$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'height="' . $the_media->height . '"';
					$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
					$aheight = $the_media->height;
					$awidth = $the_media->width;
				}
			}
		}

		$parts = explode(".", @$the_media->local);
		$extension = strtolower($parts[count($parts) - 1]);

		if (@$the_media->type == 'video' || @$the_media->type == 'audio') {
			if ($the_media->type == 'video' && $extension == "avi") {
				$media = '<object width="' . $vwidth . '" height="' . $vheight . '" type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" id="MediaPlayer1">
<param value="' . JURI::root() . $configs->videoin . "/" . $the_media->local . '" name="fileName">
<param value="true" name="animationatStart">
<param value="true" name="transparentatStart">
<param value="true" name="autoStart">
<param value="true" name="showControls">
<param value="10" name="Volume">
<param value="false" name="autoplay">
<embed width="' . $vwidth . '" height="' . $vheight . '" type="video/x-msvideo" src="' . JURI::root() . $configs->videoin . "/" . $the_media->local . '" name="plugin">
</object>';
			} elseif ($no_plugin_for_code == 0) {
				$helper = new guruHelper();

				require_once(JPATH_ROOT . '/components/com_guru/helpers/videos/helper.php');
				$parsedVideoLink = parse_url($the_media->url);
				preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', @$parsedVideoLink['host'], $matches);
				$domain	= @$matches['domain'];

				if (!empty($domain)) {
					$provider		= explode('.', $domain);
					$providerName	= strtolower($provider[0]);

					if ($providerName == "youtu") {
						$providerName = "youtube";
					}

					$libraryPath	= JPATH_ROOT . '/components/com_guru/helpers/videos' . '/' . $providerName . '.php';

					if (!file_exists($libraryPath)) {
						$temp_media = $the_media;
						$temp_media->source = 'local';
						$temp_media->local = $temp_media->url;
						$temp_media->exception = "1";

						$the_media = $temp_media;
					}
				}

				$media = $helper->create_media_using_plugin($the_media, $configs, $awidth, $aheight, $vwidth, $vheight);
			}
		}

		if (@$the_media->type == 'docs') {
			$the_base_link = JURI::root();

			$media = JText::_('GURU_NO_PREVIEW');
			//$media = JText::_("GURU_TASKS");

			if ($the_media->source == 'local' && (substr($the_media->local, (strlen($the_media->local) - 3), 3) == 'txt' || substr($the_media->local, (strlen($the_media->local) - 3), 3) == 'pdf') && $the_media->width > 1) {
				$media = '<div class="contentpane">
							<iframe id="blockrandom"
								name="iframe"
								src="' . $the_base_link . '/' . $configs->docsin . '/' . $the_media->local . '"
								width="' . $the_media->width . '"
								height="' . $the_media->height . '"
								scrolling="auto"
								align="top"
								frameborder="2"
								class="wrapper">
								This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
							</div>';
				return stripslashes($media . '<div  style="text-align:center"><i>' . $the_media->instructions . '</i></div>');
			} elseif (@$the_media->source == 'url' && (substr($the_media->url, (strlen($the_media->url) - 3), 3) == 'txt' || substr($the_media->url, (strlen($the_media->url) - 3), 3) == 'pdf') && $the_media->width > 1) {
				$media = '<div class="contentpane">
							<iframe id="blockrandom"
								name="iframe"
								src="' . $the_media->url . '"
								width="' . $the_media->width . '"
								height="' . $the_media->height . '"
								scrolling="auto"
								align="top"
								frameborder="2"
								class="wrapper">
								This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
							</div>';
				return stripslashes($media . '<div  style="text-align:center"><i>' . $the_media->instructions . '</i></div>');
			}

			if (@$the_media->source == 'local' && $the_media->width == 1) {
				$media = '<br /><a href="' . $the_base_link . $configs->docsin . '/' . $the_media->local . '" target="_blank">' . $the_media->name . '</a>';
				return stripslashes($media . '<div  style="text-align:center"><i>' . $the_media->instructions . '</i></div>');
			}
			if ($the_media->source == 'url'  && $the_media->width == 0)
				$media = '<div class="contentpane">
							<iframe id="blockrandom"
								name="iframe"
								src="' . $the_media->url . '"
								width="100%"
								height="600"
								scrolling="auto"
								align="top"
								frameborder="2"
								class="wrapper">
								This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
							</div>';

			if (@$the_media->source == 'url'  && $the_media->width == 1)
				$media = '<a href="' . $the_media->url . '" target="_blank">' . $the_media->name . '</a>';
		}

		if (@$the_media->type == 'url') {
			$src = $the_media->url;
			$media = '<a href="' . $src . '" target="_blank">' . $src . '</a>';
		}
		if (@$the_media->type == 'Article') {
			$media = self::getArticleById($the_media->code);
		}

		if (@$the_media->type == 'image') {
			$img_size = @getimagesize(JPATH_SITE . DIRECTORY_SEPARATOR . $configs->imagesin . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'thumbs' . $the_media->local);
			$img_width = $img_size[0];
			$img_height = $img_size[1];
			if ($img_width > 0 && $img_height > 0) {
				$thumb_width = 0;
				$thumb_height = 0;
				if ($the_media->width > 0) {
					$thumb_width = $the_media->width;
					$thumb_height = $img_height / ($img_width / $the_media->width);
				} elseif ($the_media->height > 0) {
					$thumb_height = $the_media->height;
					$thumb_width = $img_width / ($img_height / $the_media->height);
				} else {
					$thumb_height = 200;
					$thumb_width = $img_width / ($img_height / 200);
				}
				$media = '<img width="' . $thumb_width . '" height="' . $thumb_height . '" src="' . JURI::root() . DIRECTORY_SEPARATOR . $configs->imagesin . '/media/thumbs' . $the_media->local . '" />';
			}
			if (!isset($media)) {
				$media = NULL;
			}
		}

		if (@$the_media->type == 'project') {
			$db = JFactory::getDbo();

			@$sql = "select c.`name` from #__guru_program c, #__guru_projects p where c.`id`=p.`course_id` and p.`id`=" . intval($the_media->id);
			$db->setQuery($sql);
			$course_name = $db->loadColumn();
			$course_name = @$course_name["0"];

			@$sql = "select u.`name` from #__users u, #__guru_projects p where u.`id`=p.`author_id` and p.`id`=" . intval($the_media->id);
			$db->setQuery($sql);
			$user_name = $db->loadColumn();
			$user_name = @$user_name["0"];

			$media = '
				<table style="margin:auto;">
					<tr>
						<th style="text-align:right; padding-right:15px;">' . JText::_("GURU_TITLE") . ':</th>
						<td style="text-align:left; padding:0px !important;">' . @$the_media->title . '</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">' . JText::_("GURU_COURSE") . ':</th>
						<td style="text-align:left; padding:0px !important;">' . $course_name . '</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">' . JText::_("GURU_AUTHOR_CERTIFICATE") . ':</th>
						<td style="text-align:left; padding:0px !important;">' . $user_name . '</td>
					</tr>
				</table>
			';
		}

		if (@$the_media->type == 'scorm') {
			$db = JFactory::getDbo();

			@$sql = "select c.`name` from #__guru_program c, #__guru_scorm_items p where c.`id`=p.`course_id` and p.`id`=" . intval($the_media->id);
			$db->setQuery($sql);
			$course_name = $db->loadColumn();
			$course_name = @$course_name["0"];

			@$sql = "select u.`name` from #__users u, #__guru_scorm_items p where u.`id`=p.`author_id` and p.`id`=" . intval($the_media->id);
			$db->setQuery($sql);
			$user_name = $db->loadColumn();
			$user_name = @$user_name["0"];

			$media = '
				<table style="margin:auto;">
					<tr>
						<th style="text-align:right; padding-right:15px;">' . JText::_("GURU_TITLE") . ':</th>
						<td style="text-align:left; padding:0px !important;">' . @$the_media->title . '</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">' . JText::_("GURU_COURSE") . ':</th>
						<td style="text-align:left; padding:0px !important;">' . $course_name . '</td>
					</tr>
					<tr>
						<th style="text-align:right; padding-right:15px;">' . JText::_("GURU_AUTHOR_CERTIFICATE") . ':</th>
						<td style="text-align:left; padding:0px !important;">' . $user_name . '</td>
					</tr>
				</table>
			';
		}

		if (@$the_media->type == 'quiz') {
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root() . "components/com_guru/css/quiz.css");

			include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "gurutask.php");
			$quiz_content = guruModelguruTask::parse_media(intval($the_media->id), 12);

			$media = $quiz_content;
		}

		if (@$the_media->type == "file") {
			$media = '<a target="_blank" href="' . JURI::ROOT() . $configs->filesin . '/' . $the_media->local . '">' . $the_media->name . '</a><br/><br/>' . $the_media->instructions;
		}

		if ($the_media->type === 'zoom') {
			$data = new JRegistry($the_media->code);
			$html = '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_ID').': ' . $data->get('meeting_id') . '</h3>';
			$html .= '<h3>'.JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD').': ' . $data->get('meeting_pass') . '</h3>';
			$html .= '<div><a class="btn btn-primary" target="_blank" href="' . $data->get('invite_link') . '" >'.JText::_('GURU_ZOOM_CLICK_TO_JOIN').'</a></div>';

			return $html;
		}

		return stripslashes($media);
	}
	function getmainMedia()
	{
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		if ($id == 0) {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			$id = $cid["0"];
		}

		$database = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media
				WHERE id = " . $id;
		$database->setQuery($sql);
		$database->execute();
		$media = $database->loadObject();

		$media->code = stripslashes($media->code);
		if ($media->type == 'Article') {
			$id = $media->code;
			$media->code = $this->getArticleById($id);
		}

		$configs = $this->getConfig();
		$video_size = $configs->default_video_size;

		if ($media->type != "url" && $media->type != "image" && $media->type != "docs" && $media->type != "audio"  && $media->option_video_size == 0) {
			if (trim($video_size) != "") {
				$temp = explode("x", trim($video_size));
				$media->width = $temp["1"];
				$media->height = $temp["0"];
			}
		}
		if ($media->width == 0) {
			$media->width = 400;
		}

		if ($media->type == "video" && $media->source == "url") {
			require_once(JPATH_ROOT . '/components/com_guru/helpers/videos/helper.php');
			if (!preg_match("~^(?:f|ht)tps?://~i", $media->url)) {
				$media->url = "http://" . $media->url;
			}
			$parsedVideoLink = parse_url($media->url);
			preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $parsedVideoLink['host'], $matches);
			$domain	= $matches['domain'];


			if (!empty($domain)) {
				$provider		= explode('.', $domain);
				$providerName	= strtolower($provider[0]);

				if ($providerName == "youtu") {
					$providerName = "youtube";
				}

				$libraryPath	= JPATH_ROOT . '/components/com_guru/helpers/videos' . '/' . $providerName . '.php';

				if (file_exists($libraryPath)) {
					require_once($libraryPath);
					$className		= 'PTableVideo' . ucfirst($providerName);
					$videoObj		= new $className();
					$videoObj->init($media->url);
					$video_id		= $videoObj->getId();

					if ($providerName == "youtube" || $providerName == "vimeo" || $providerName == "dailymotion") {
						$video_id = $video_id . "?autoplay=" . $media->auto_play;
					}

					$videoPlayer	= $videoObj->getViewHTML($video_id, $media->width, $media->height);

					$media->code = $videoPlayer;
				} else {
					$temp_media = $media;
					$temp_media->source = 'local';
					$temp_media->local = $temp_media->url;
					$temp_media->exception = "1";

					$media->code = $this->parse_media_preview($temp_media);
				}
			}
		} else {
			$media->code = $this->parse_media_preview($media);
		}

		return $media;
	}
	function getArticleById($id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT jc.introtext, jc.fulltext FROM #__content jc WHERE id = " . $id;
		$db->setQuery($sql);
		$row = $db->loadAssoc();
		$fullArticle = $row['introtext'] . $row['fulltext'];
		if (!strlen(trim($fullArticle))) $fullArticle = "Article is empty ";
		return $fullArticle;
	}

	function getlistQuiz()
	{
		$app = JFactory::getApplication("site");
		$limit		= $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		$user = JFactory::getUser();

		$db = JFactory::getDBO();
		$search_text = JFactory::getApplication()->input->get('search_quiz', "", "raw");

		$and = " AND `author`=" . intval($user->id);

		if ($search_text != "") {
			$and = "AND name like '%" . $search_text . "%' ";
		}

		$sql = "SELECT count(*) FROM  #__guru_quiz WHERE is_final <> 1 " . $and;
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = @$result["0"];
		$this->_total = intval($result);

		$sql = "SELECT * FROM #__guru_quiz WHERE is_final<>1 " . $and . " ORDER BY ordering ";
		$db->setQuery($sql);

		$return = $db->loadObjectList();

		$this->_total = count($return);
		if (isset($return) && count($return) > 0 && $limit != 0) {
			$return = array_slice($return, (int)($limitstart), (int)($limit));
		}

		$pagination = $this->getPagination();
		$pagination->limitstart = $limitstart;
		$pagination->total = $this->_total;
		@$pagination->pagesTotal = ceil($this->_total / $pagination->limit);
		@$pagination->pagesStop = ceil($this->_total / $pagination->limit);
		@$pagination->pagesCurrent = ($limitstart / $limit + 1);
		$this->set("Pagination", $pagination);

		return $return;
	}

	function duplicateMedia()
	{
		$cid	= JFactory::getApplication()->input->get('cid', array(), "raw");
		$n		= count($cid);

		foreach ($cid as $id) {
			$row 	= $this->getTable('guruMedia');
			$db = JFactory::getDBO();
			// load the row from the db table
			$row->load((int) $id);

			$row->name 	= JText::_('GURU_MEDIA_COPY_TITLE') . ' ' . $row->name;
			$row->id 			= 0;

			if ($row->local != NULL && $row->local != 'NULL' && $row->local != '') {
				$sql = "SELECT videoin,audioin,docsin FROM #__guru_config WHERE id ='1' ";
				$db->setQuery($sql);
				$config = $db->loadObject();

				if ($row->source == 'local') {
					if ($row->type == 'audio') $imgfolder = $config->audioin;
					if ($row->type == 'video') $imgfolder = $config->videoin;
					if ($row->type == 'docs') $imgfolder = $config->docsin;
					$targetPath = JPATH_SITE . '/' . $imgfolder . '/';
					copy($targetPath . $row->local, $targetPath . 'copy_' . $row->local);
				}
				$row->local = 'copy_' . $row->local;
			}

			if (!$row->check()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			if (!$row->store()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			$row->checkin();
			unset($row);
		}
		return 1;
	}

	function getAllRows($parent, $level)
	{
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_media_categories where parent_id=" . intval($parent);
		$db->setquery($sql);
		$result = $db->loadAssocList();
		if (isset($result) && is_array($result) && count($result) > 0) {
			$level++;
			foreach ($result as $key => $value) {
				$value["level"] = $level;
				$this->return_array[] = $value;
				$this->getAllRows($value["id"], $level);
			}
		}
		return $this->return_array;
	}

	function getfile()
	{
		$config = $this->getConfig();
		$folder	= JFactory::getApplication()->input->get("directory", "", "raw");
		$txt = JFactory::getApplication()->input->get("txt", "0", "raw");
		$fileType = JFactory::getApplication()->input->get("type", "", "raw");
		$db = JFactory::getDBO();
		if ($folder != 'root')
			$getin = DIRECTORY_SEPARATOR . $folder;
		else $getin = '';

		$id = JFactory::getApplication()->input->get("cid", "0", "raw");
		$id = intval($id);

		$user = JFactory::getUser();
		$sql = "select local from #__guru_media where source='local' and author=" . intval($user->id);
		$db->setQuery($sql);
		$all_my_files = $db->loadColumn();

		if (empty($this->_package)) {
			$this->_package = $this->getTable("guruMedia");
			$this->_package->load($id);
			$data = JFactory::getApplication()->input->post->getArray();

			if (!$this->_package->bind($data)) {
				return false;
			}

			if (!$this->_package->check()) {
				return false;
			}
		}

		if ($id < 1) {
			$this->_package->text = JText::_("GURU_NEW");
		} else {
			$this->_package->text = JText::_("GURU_EDIT");
		}

		$this->_package->lists['flash_directory'] = JURI::root() . "/images/stories/";
		$task = JFactory::getApplication()->input->get('task', '', "raw");
		$selected_item_New = JFactory::getApplication()->input->get('selected_item_New', '', "raw");
		//start type drop-down
		$typeOption	  = array();
		$javascript	  = "onchange=changeType(this.value)";
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_SELECT"), "-");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_VIDEO"), "video");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_AUDIO"), "audio");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_DOCS"), "docs");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_URL"), "url");
		//$typeOption[] = JHTML::_("select.option", JText::_("GURU_ARTICLE"),"article");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_IMAGE"), "image");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_ZOOM_MEETING"), "zoom");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_TEXT"), "text");
		$typeOption[] = JHTML::_("select.option", JText::_("GURU_FILE"), "file");

		if ($this->_package->type == "" && $txt == 1) {
			$this->_package->type = "text";
		}

		if ($this->_package->type == "") {
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$type = $registry->get('type', "");

			if (isset($type) && $type != "") {
				$this->_package->type = $type;
			}
			if (isset($selected_item_New)) {
				$this->_package->type = $selected_item_New;
			}
		}

		$this->_package->lists['type'] = JHTML::_("select.genericlist", $typeOption, "type", "size=1 class=uk-width-1-1 " . $javascript, "text", "value", $this->_package->type);

		if (!isset($this->_package->published)) {
			$this->_package->published = 1;
		}


		$approved = '<input type="hidden" name="published" value="0">';
		if ($this->_package->published == 1) {
			$approved .= '<input type="checkbox" checked="checked" value="1" class="ace-switch ace-switch-5" name="published">';
		} else {
			$approved .= '<input type="checkbox" value="1" class="ace-switch ace-switch-5" name="published">';
		}
		$approved .= '<span class="lbl"></span>';

		$this->_package->lists['approved'] = $approved;

		//start get author list
		$sql = "SELECT u.id, u.name FROM #__users u, #__guru_authors la where u.id=la.userid";
		$db->setQuery($sql);
		$result_authors = $db->loadObjectList();

		$author_listl = array();
		$author_listl[] = JHTML::_("select.option", JText::_('GURU_SELECT'), "0");
		for ($i = 0; $i < count($result_authors); $i++) {
			$author_listl[] = JHTML::_("select.option", $result_authors[$i]->name, $result_authors[$i]->id);
		}
		$this->_package->lists['author'] = JHTML::_("select.genericlist", $author_listl, "author", "", "text", "value", $this->_package->author);


		//start video
		$directory 	= JPATH_SITE . DIRECTORY_SEPARATOR . $config->videoin;
		chmod($directory, 0755);
		$directoryt = JPATH_SITE . DIRECTORY_SEPARATOR . $config->videoin . $getin;

		$allfolders = JFolder::folders($directory);

		$javascript 	= 'onchange="changefolder();" onClick=""';
		$videoOption[] = JHTML::_("select.option", "...", "root");
		foreach ($allfolders as $fille) {
			$extension_array = explode('.', $fille);
			$extension = $extension_array[count($extension_array) - 1];
			$extension = strtolower($extension);

			$allowed_extensions = array("lv", "swf", "mov", "mp4", "wmv", "wma", "mp3", "3gp", "webm", "ogv", "ogg", "divx");

			if (in_array($extension, $allowed_extensions)) {
				if (in_array(trim($fille), $all_my_files)) {
					$videoOption[] = JHTML::_("select.option", $fille, $fille);
				}
			}
		}
		$this->_package->lists['video_dir'] = JHTML::_("select.genericlist", $videoOption, "video_dir", "size=1 " . $javascript, "text", "value", $this->_package->type);


		$javascript 	= 'onchange="" onClick=""';
		$allfiles = JFolder::files($directoryt);
		$imageOption = array();

		if (count($allfiles) > 0) {
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array) - 1];
				$extension = strtolower($extension);

				if ($extension == 'mov' || $extension == 'avi' || $extension == 'wmv' || $extension == 'swf' || $extension == 'mpg' || $extension == 'mpeg' || $extension == 'fla' ||  $extension == 'mp4' ||  $extension == 'flv') {
					if (in_array(trim($fille), $all_my_files)) {
						$imageOption[] = JHTML::_("select.option", $fille, $fille);
					}
				}
			}
			$this->_package->lists['video_url'] = JHTML::_("select.genericlist", $imageOption, "localfile", "size=10 style='width:100%;' " . $javascript, "text", "value", $this->_package->local);
		} else {
			$this->_package->lists['video_url'] = "";
			$this->_package->lists['video_url'] = '<select onclick="" onchange="" size="10" name="localfile_v" id="localfile_v" style="width:100%;"><option value="0">...</option></select>';
		}

		//end video



		//start audio
		$directory 	= JPATH_SITE . '/' . $config->audioin;
		chmod($directory, 0755);
		$directoryt 	= JPATH_SITE . '/' . $config->audioin . $getin;

		$allfolders = JFolder::folders($directory);

		$audioOption[] = array();
		$javascript 	= 'onchange="changefolder();" onClick=""';
		$audioOption[] = JHTML::_("select.option", "...", "root");
		if (count($allfolders)) {
			foreach ($allfolders as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array) - 1];
				$extension = strtolower($extension);
				if ($extension == 'mp3' || $extension == 'wav') {
					if (in_array(trim($fille), $all_my_files)) {
						$audioOption[] = JHTML::_("select.option", $fille, $fille);
					}
				}
			}
			$this->_package->lists['audio_dir'] = JHTML::_("select.genericlist", $audioOption, "audio_dir", "size=10 " . $javascript, "text", "value", $directory);
		} else {
			$this->_package->lists['audio_dir'] = "";
		}


		$javascript	= 'onchange="" onClick=""';
		$allfiles = JFolder::files($directoryt);
		$audioUrl = array();
		if (count($allfiles) > 0) {
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array) - 1];
				$extension = strtolower($extension);
				if ($extension == 'mp3' || $extension == 'wav') {
					if (in_array(trim($fille), $all_my_files)) {
						$audioUrl[] = JHTML::_("select.option", $fille, $fille);
					}
				}
			}
			$this->_package->lists['audio_url'] = JHTML::_("select.genericlist", $audioUrl, "localfile_a", "size=10 style='width:100%;' " . $javascript, "text", "value", $this->_package->local);
		} else {
			$this->_package->lists['audio_url'] = '<select onclick="" onchange="" size="10" name="localfile_a" id="localfile_a" style="width:100%;"><option value="0">...</option></select>';
		}
		//end audio


		//start docs
		$directory 	= JPATH_SITE . '/' . $config->docsin;
		chmod($directory, 0755);
		$directoryt = JPATH_SITE . '/' . $config->docsin . $getin;

		$allfolders = JFolder::folders($directory);
		$javascript	= 'onchange="changefolder();" onClick=""';
		$docsOption[] = JHTML::_("select.option", "../", "root");

		foreach ($allfolders as $fille) {
			$extension_array = explode('.', $fille);
			$extension = $extension_array[count($extension_array) - 1];
			$extension = strtolower($extension);
			if ($extension == 'doc' || $extension == 'docx' || $extension == 'txt' || $extension == 'pdf' || $extension == 'csv' || $extension == 'htm' || $extension == 'html' || $extension == 'xhtml' || $extension == 'xml' || $extension == 'sxw' || $extension == 'rtf' || $extension == 'odt' || $extension == 'css' || $extension == 'odp' || $extension == 'pps' || $extension == 'ppt' || $extension == 'sxi' || $extension == 'xls') {
				if (in_array(trim($fille), $all_my_files)) {
					$docsOption[] = JHTML::_("select.option", $fille, $fille);
				}
			}
		}
		$this->_package->lists['docs_dir'] = JHTML::_("select.genericlist", $docsOption, "docs_dir", "size=1 " . $javascript, "text", "value", $directory);


		$allfiles = JFolder::files($directoryt);
		$javascript 	= 'onchange="" onClick=""';
		if (count($allfiles) > 0) {
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array) - 1];
				$extension = strtolower($extension);
				if ($extension == 'doc' || $extension == 'docx' || $extension == 'txt' || $extension == 'pdf' || $extension == 'csv' || $extension == 'htm' || $extension == 'html' || $extension == 'xhtml' || $extension == 'xml' || $extension == 'sxw' || $extension == 'rtf' || $extension == 'odt' || $extension == 'css' || $extension == 'odp' || $extension == 'pps' || $extension == 'ppt' || $extension == 'sxi' || $extension == 'xls') {
					if (in_array(trim($fille), $all_my_files)) {
						$docsOption[] = JHTML::_("select.option", $fille, $fille);
					}
				}
			}
			$this->_package->lists['docs_url'] = JHTML::_("select.genericlist", $docsOption, "localfile_d", "size=10 " . $javascript, "text", "value", $this->_package->local);
		} else {
			$this->_package->lists['docs_url'] = '<select onclick="" onchange="" size="10" name="localfile_d" id="localfile_d"><option value="0">...</option></select>';
		}

		//end docs

		//start files
		$directory 	= JPATH_SITE . '/' . $config->filesin;
		chmod($directory, 0755);
		$directoryt = JPATH_SITE . '/' . $config->filesin . $getin;

		$allfolders = JFolder::folders($directory);
		$javascript	= 'onchange="changefolder();" onClick=""';
		$filesOption[] = JHTML::_("select.option", "../", "root");

		foreach ($allfolders as $fille) {
			$extension_array = explode('.', $fille);
			$extension = $extension_array[count($extension_array) - 1];
			$extension = strtolower($extension);
			if ($extension == 'zip' || $extension == 'exe') {
				if (in_array(trim($fille), $all_my_files)) {
					$filesOption[] = JHTML::_("select.option", $fille, $fille);
				}
			}
		}
		$this->_package->lists['files_dir'] = JHTML::_("select.genericlist", $filesOption, "files_dir", "size=1 " . $javascript, "text", "value", $directory);


		$allfiles = JFolder::files($directoryt);
		$javascript 	= 'onchange="" onClick=""';
		if (count($allfiles) > 0) {
			foreach ($allfiles as $fille) {
				$extension_array = explode('.', $fille);
				$extension = $extension_array[count($extension_array) - 1];
				$extension = strtolower($extension);
				if ($extension == 'zip' || $extension == 'exe') {
					if (in_array(trim($fille), $all_my_files)) {
						$filesOption[] = JHTML::_("select.option", $fille, $fille);
					}
				}
			}
			$this->_package->lists['files_url'] = JHTML::_("select.genericlist", $filesOption, "localfile_f", "size=10 style='width:100%;' " . $javascript, "text", "value", $this->_package->local);
		} else {
			$this->_package->lists['files_url'] = '<select onclick="" onchange="" size="10" name="localfile_f" id="localfile_f" style="width:100%;"><option value="0">...</option></select>';
		}
		//end files	

		return $this->_package;

		if (intval($this->_package->id) == 0) {
			$this->_package->hide_name = 1;
		}
	}

	function getproject()
	{
		$cid = JFactory::getApplication()->input->get("cid", "0", "raw");

		if (intval($cid) == 0) {
			$cid = JFactory::getApplication()->input->get("id", "0", "raw");
		}

		$db = JFactory::getDbo();

		$sql = "select * from #__guru_projects where `id`=" . intval($cid);
		$db->setQuery($sql);
		$project = $db->loadAssocList();

		return @(object)$project["0"];
	}

	function getscorm()
	{
		$cid = JFactory::getApplication()->input->get("cid", "0", "raw");

		if (intval($cid) == 0) {
			$cid = JFactory::getApplication()->input->get("id", "0", "raw");
		}

		$db = JFactory::getDbo();

		$sql = "select * from #__guru_scorm_items where `id`=" . intval($cid);
		$db->setQuery($sql);
		$scorm = $db->loadAssocList();

		return @(object)$scorm["0"];
	}

	function parse_audio($id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);

		$configs = $db->loadObject();

		$sql = "SELECT * FROM #__guru_media
					WHERE id = " . $id;
		$db->setQuery($sql);
		$the_media = $db->loadObject();
		$the_media->code = stripslashes($the_media->code);

		$no_plugin_for_code = 0;
		$aheight = 0;
		$awidth = 0;
		$vheight = 0;
		$vwidth = 0;
		if ($the_media->type == 'audio') {
			if ($the_media->source == 'url' || $the_media->source == 'local') {
				if ($the_media->width == 0 || $the_media->height == 0) {
					$aheight = 20;
					$awidth = 300;
				} else {
					$aheight = $the_media->height;
					$awidth = $the_media->width;
				}
			} elseif ($the_media->source == 'code') {
				if ($the_media->width == 0 || $the_media->height == 0) {
					$begin_tag = strpos($the_media->code, 'width="');
					if ($begin_tag !== false) {
						$remaining_code = substr($the_media->code, $begin_tag + 7, strlen($the_media->code));
						$end_tag = strpos($remaining_code, '"');
						$awidth = substr($remaining_code, 0, $end_tag);

						$begin_tag = strpos($the_media->code, 'height="');
						if ($begin_tag !== false) {
							$remaining_code = substr($the_media->code, $begin_tag + 8, strlen($the_media->code));
							$end_tag = strpos($remaining_code, '"');
							$aheight = substr($remaining_code, 0, $end_tag);
							$no_plugin_for_code = 1;
						} else {
							$aheight = 20;
							$awidth = 300;
						}
					} else {
						$aheight = 20;
						$awidth = 300;
					}
				} else {
					$replace_with = 'width="' . $the_media->width . '"';
					$the_media->code = preg_replace('#width="[0-9]+"#', $replace_with, $the_media->code);
					$replace_with = 'height="' . $the_media->height . '"';
					$the_media->code = preg_replace('#height="[0-9]+"#', $replace_with, $the_media->code);
					$aheight = $the_media->height;
					$awidth = $the_media->width;
				}
			}
		}

		$awidth = "200";
		$aheight = "20";
		if ($the_media->type == 'audio') {
			if (!isset($layout_id)) {
				$layout_id = "";
			}
			if ($no_plugin_for_code == 0) {
				$helper = new guruHelper();
				$media = $helper->create_media_using_plugin($the_media, $configs, $awidth, $aheight, $vwidth, $vheight, $layout_id);
			} else {
				$media = $the_media->code;
			}
		}

		if (!isset($media)) {
			$media = NULL;
		}

		return stripslashes($media);
	}
	function getCategories()
	{
		$db = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->get('cid', 0, "raw");
		$ids = JFactory::getApplication()->input->get('id', '0', "raw");

		if (isset($cid["0"]) && $cid["0"] != 0) {
			$id = $cid["0"];
		} else {
			$id = $ids;
		}
		$sql = "SELECT * FROM #__guru_media_categories WHERE id =" . $id;
		$db->setQuery($sql);
		$categories = $db->loadObjectList();

		return $categories;
	}

	function getParent()
	{
		$db	= JFactory::getDBO();
		$sql = "select parent_id from #__guru_media_categories";
		$db->setQuery($sql);
		$res = $db->loadObjectList();

		return $res;
	}

	function parse_txt($id)
	{
		$db = JFactory::getDBO();

		$q = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$db->setQuery($q);
		$configs = $db->loadObject();

		$q  = "SELECT * FROM #__guru_media WHERE id = " . $id;
		$db->setQuery($q);
		$result = $db->loadObject();
		$the_media = $result;

		if ($the_media->type == 'text') {
			$media = $the_media->code;
			if (strpos($media, 'src="') !== FALSE) {
				$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
				$the_base_link = $the_base_link[0];
				$media = str_replace('src="', 'src="' . $the_base_link, $media);
			}
		}
		if ($the_media->type == 'docs') {

			$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
			$the_base_link = $the_base_link[0];

			$media = JText::_('GURU_NO_PREVIEW');
			if ($the_media->source == 'local' && (substr($the_media->local, (strlen($the_media->local) - 3), 3) == 'txt' || substr($the_media->local, (strlen($the_media->local) - 3), 3) == 'pdf') && $the_media->width == 0) {
				$media = '<div class="contentpane">
									<iframe id="blockrandom"
										name="iframe"
										src="' . $the_base_link . '/' . $configs->docsin . '/' . $the_media->local . '"
										width="100%"
										height="600"
										scrolling="auto"
										align="top"
										frameborder="2"
										class="wrapper">
										This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
									</div>';
			} elseif ($the_media->source == 'url' && (substr($the_media->url, (strlen($the_media->url) - 3), 3) == 'txt' || substr($the_media->url, (strlen($the_media->url) - 3), 3) == 'pdf') && $the_media->width == 0) {
				$media = '<div class="contentpane">
									<iframe id="blockrandom"
										name="iframe"
										src="' . $the_media->url . '"
										width="100%"
										height="600"
										scrolling="auto"
										align="top"
										frameborder="2"
										class="wrapper">
										This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
									</div>';
			}


			if ($the_media->source == 'local' && $the_media->width == 1)
				$media = '<a href="' . $the_base_link . '/' . $configs->docsin . '/' . $the_media->local . '" target="_blank">' . $the_media->name . '</a>';

			if ($the_media->source == 'url'  && $the_media->width == 0)
				$media = '<div class="contentpane">
								<iframe id="blockrandom"
									name="iframe"
									src="' . $the_media->url . '"
									width="100%"
									height="600"
									scrolling="auto"
									align="top"
									frameborder="2"
									class="wrapper">
									This option will not work correctly. Unfortunately, your browser does not support inline frames.</iframe>
								</div>';

			if ($the_media->source == 'url'  && $the_media->width == 1)
				$media = '<a href="' . $the_media->url . '" target="_blank">' . $the_media->name . '</a>';
		}
		if ($the_media->type == 'quiz') {
			$media = '';

			$q  = "SELECT * FROM #__guru_quiz WHERE id = " . $the_media->source;
			$db->setQuery($q);
			$result_quiz = $db->loadObject();

			$media = $media . '<strong>' . $result_quiz->name . '</strong><br /><br />';
			$media = $media . $result_quiz->description . '<br /><br />';

			$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid = " . $the_media->source . " and published=1";
			$db->setQuery($q);
			$quiz_questions = $db->loadObjectList();

			foreach ($quiz_questions as $one_question) {
				$media = $media . '<div align="left">' . $one_question->text . '<div>';

				$media = $media . '<div align="left" style="padding-left:30px;">';
				if ($one_question->a1 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a1 . '</input><br />';
				if ($one_question->a2 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a2 . '</input><br />';
				if ($one_question->a3 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a3 . '</input><br />';
				if ($one_question->a4 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a4 . '</input><br />';
				if ($one_question->a5 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a5 . '</input><br />';
				if ($one_question->a6 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a6 . '</input><br />';
				if ($one_question->a7 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a7 . '</input><br />';
				if ($one_question->a8 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a8 . '</input><br />';
				if ($one_question->a9 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a9 . '</input><br />';
				if ($one_question->a10 != '')
					$media = $media . '<input type="radio" name="' . $one_question->id . '">' . $one_question->a10 . '</input><br />';
				$media = $media . '</div>';
			}

			$media = $media . '<br /><div align="left" style="padding-left:30px;"><input type="submit" value="' . JText::_("GURU_SUBMIT") . '" disabled="disabled" /></div>';
		}
		if (!isset($media)) {
			$media = NULL;
		}
		$media = $media . '<div  style="text-align:center"><i>' . $the_media->instructions . '</i></div>';

		return stripslashes($media);
	}

	function storeMedia()
	{
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$item = $this->getTable('guruMedia');
		$data = JFactory::getApplication()->input->post->getArray();
		$data['code_v'] = JFactory::getApplication()->input->get('code_v', '', 'raw');
		$data['text'] = JFactory::getApplication()->input->get('text', '', 'raw');

		$config = $this->getConfig();

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('type', $data['type']);
		$registry->set('category_id', @$data['category_id']);

		//start video type
		if ($data['type'] == 'video') {
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';

			if (trim($data["url_v"]) != "") {
				$data["source_v"] = "url";
			}

			if ($data['source_v'] == '') {
				if (isset($data['localfile']) && ($data['localfile'] != '')) {
					$data['source'] = 'local';
				} elseif (isset($data['code_v'])) {
					$data['source'] = 'code';
				} elseif (isset($data['url_v'])) {
					$data['source'] = 'url';
				}
			} else {
				$data['source'] = $data['source_v'];
			}

			if ($data['source'] == 'code')
				$data['code'] = $data['code_v'];
			if ($data['source'] == 'url')
				$data['url'] = $data['url_v'];

			if ($data['source'] == 'local') {
				if (strpos($data['localfile'], $config->videoin) !== false) {
					$data['localfile'] = substr($data['localfile'], strlen($config->videoin) + 1);
				}
				$data['local']	= $data['localfile'];
			}

			$data['width'] = $data['width_v'];
			$data['height'] = $data['height_v'];
		}
		//end video type

		//start audio type
		if ($data['type'] == 'audio') {
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';

			if ($data['source_a'] == 'code') {
				$data['code'] = $data['code_a'];
				$data['source'] = "code";
			} elseif ($data['source_a'] == 'url') {
				$data['url'] = $data['url_a'];
				$data['source'] = "url";
			} else {
				if ($data['uploaded_tab'] == 0) {
					$data['local'] = $data['localfile_a'];
					$data['uploaded'] = 1;
					$data['uploaded_tab'] = 0;
					$data['source'] = "local";
				} elseif (isset($data['localfile_a']) && $data['localfile_a'] != '') {
					$data['local']	= $data['localfile_a'];
					$data['uploaded'] = 0;
					$data['uploaded_tab'] = 1;
					$data['source'] = "local";
				}
			}

			if (isset($data['source']) && ($data['source'] == 'local')) {
				$data['local']	= $data['localfile_a'];
			}
			$data['width'] = $data['width_a'];
			$data['height'] = $data['height_a'];
		}
		//end audio type

		//start image type
		if ($data['type'] == 'image') {
			if ($data['media_prop'] == 'w') {
				if ($data['media_fullpx'] > 0)
					$data['width'] = intval($data['media_fullpx']);
				else
					$data['width'] = 200;
				$data['height'] = 0;
			}
			if ($data['media_prop'] == 'h') {
				if ($data['media_fullpx'] > 0)
					$data['height'] = intval($data['media_fullpx']);
				else
					$data['height'] = 200;
				$data['width'] = 0;
			}
			if (isset($data['image']))
				$data['local'] = $data['image'];
			$data['url'] = '';
			$data['code'] = '';
		}
		//end image type

		//start document type
		if ($data['type'] == 'docs') {

			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';

			$data['source'] = $data['source_d'];

			if ($data['source_d'] == 'url') {
				$data['url'] = $data['url_d'];
			} elseif ($data['source_d'] == '') {
				$data['local']	= $data['localfile_d'];
				$data['source'] = "local";
			}

			// else we display the doc in a LINK
			if ($data['display_as'] == 'link') {
				$data['width'] = 1; // else we display the doc in a LINK
				$data['height'] = 0;
			}
		}
		//end document type

		//start files type
		if ($data['type'] == 'file') {
			$data['code'] = '';
			$data['url'] = '';
			$data['local'] = '';

			$data['source'] = $data['source_f'];

			if ($data['source_f'] == 'url')
				$data['url'] = $data['url_f'];
			if ($data['source_f'] == '') {
				$data['local']	= $data['localfile_f'];
				$data['source'] = "local";
			}
			// else we display the doc in a LINK

			$data['width'] = 300;
			$data['height'] = 20;
		}
		//end files type

		if ($data['type'] == 'url') {
			$data['source'] = '';
			$data['local'] = '';
			//$data['width'] = 0; // if it's 0 then we display the doc in a WRAPPER
			if ($data['display_as2'] == 'link') {
				$data['width'] = 1; // else we display the doc in a LINK
			}
			//$data['height'] = 200;
			$data['url'] = $data['url'];
			$data['code'] = '';
		}

		if ($data['type'] == 'text') {
			$data['code'] = $data['text'];
			$data['local'] = '';
			$data['width'] = 0;
			$data['height'] = 0;
			$data['url'] = '';

			if ($data['text'] == '' || $data['text'] == NULL) {
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('isempty', "1");

				return false;
			}
		}
		if ($data['type'] == 'Article') {
			$data['code'] = $data['articleid'];
			$data['local'] = '';
			$data['width'] = 0;
			$data['height'] = 0;
			$data['url'] = '';
		}

		if ($data['type'] === 'zoom') {
			$meetingData = new JRegistry;
			$meetingData->set('meeting_id', $data['meeting_id_z']);
			$meetingData->set('meeting_pass', $data['meeting_password_z']);
			$meetingData->set('invite_link', $data['invite_link_z']);

			$data['code'] = $meetingData->toString();
		}

		if (!isset($data["hide_name"])) {
			$data["hide_name"] = 0;
		}

		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "select id from #__guru_media_categories where user_id=" . intval($user_id);
		$db->setQuery($sql);
		$categ_id = $db->loadColumn();
		$categ_id = @$categ_id["0"];

		if (intval($categ_id) == 0) {
			$sql = "select name from #__users where id=" . intval($user_id);
			$db->setQuery($sql);
			$name = $db->loadColumn();
			$name = @$name["0"];
			$sql = "insert into #__guru_media_categories (name, published, user_id, parent_id, child_id) values ('" . $name . "\'s media', '1', '" . intval($user_id) . "', 0, 0)";
			$db->setQuery($sql);
			$db->execute();

			$sql = "select id from #__guru_media_categories where user_id=" . intval($user_id);
			$db->setQuery($sql);
			$categ_id = $db->loadColumn();
			$categ_id = @$categ_id["0"];
		}

		$data["category_id"] = intval($categ_id);

		if (trim($data["id"]) == "") {
			$data["id"] = 0;
		}

		if (!isset($data["option_video_size"])) {
			$data["option_video_size"] = 0;
		}

		if (!$item->bind($data)) {
			$this->setError($item->getError());
			return false;
		}

		if (!$item->check()) {
			$this->setError($item->getError());
			return false;
		}
		if (!$item->store()) {
			$this->setError($item->getError());
			return false;
		}

		if (intval($data['id']) > 0) {
			$newid = intval($data['id']);
		} else {
			if ((isset($newid)) && ($newid > 0)) {
			} else {
				$sql = "SELECT id FROM #__guru_media ORDER BY id DESC LIMIT 1 ";
				$database->setQuery($sql);
				$newid = $database->loadColumn();
				$newid = $newid[0];
			}
		}

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('type', "");

		return $newid;
	}

	function last_media()
	{
		$database = JFactory::getDBO();
		$ask = "SELECT id FROM #__guru_media ORDER BY id DESC LIMIT 1 ";
		$database->setQuery($ask);
		$newid = $database->loadColumn();
		return $newid["0"];
	}

	function getMediaInfo($id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_media WHERE id = " . $id . " ";
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	function storeMediaCat()
	{
		$database = JFactory::getDBO();
		$item = $this->getTable('guruMediacategs');
		$data = JFactory::getApplication()->input->post->getArray();
		if (!$item->bind($data)) {
			$return["0"] = false;
		}
		// Make sure the news record is valid
		if (!$item->check()) {
			$return["0"] = false;
		}
		// Store the web link table to the database
		if (!$item->store()) {
			$return["0"] = false;
		}
		$return["0"] = true;
		$return["1"] = $item->id;
		return $return;
	}
	function duplicateMediaCat()
	{
		$cid	= JFactory::getApplication()->input->get('cid', array(), "raw");
		$n		= count($cid);

		foreach ($cid as $id) {
			$row 	= $this->getTable('guruMediacategs');;
			$db = JFactory::getDBO();
			// load the row from the db table
			$row->load((int) $id);

			$row->name 	= JText::_('GURU_MEDIA_COPY_TITLE') . ' ' . $row->name;
			$row->id 			= 0;

			if (!$row->check()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			if (!$row->store()) {
				return JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
			$row->checkin();
			unset($row);
		}
		return 1;
	}

	function storeLesson()
	{
		$item = $this->getTable('guruTasks');
		$data = JFactory::getApplication()->input->post->getArray();
		$database = JFactory::getDBO();
		$return_array = array();

		if ($data["id"] == "") {
			$data["id"] = 0;
		}

		if (!isset($data["endpublish"]) || $data["endpublish"] == "") {
			$data["endpublish"] = "0000-00-00 00:00:00";
		}

		if (!isset($data["access"]) || $data["access"] == "") {
			$data["access"] = "0";
		}

		$course_id = JFactory::getApplication()->input->get("day", "0", "raw");
		$module_id = JFactory::getApplication()->input->get("my_menu_id", "0", "raw");
		$change_order = false;
		$last_lesson_id = 0;

		$sql = "select id from #__guru_days where pid=" . intval($course_id) . " order by ordering desc";
		$database->setQuery($sql);
		$database->execute();
		$ids = $database->loadColumn();
		$last_module_id = $ids["0"];

		if ($last_module_id == $module_id) {
			$sql = "select id_final_exam from #__guru_program where id=" . intval($course_id);
			$database->setQuery($sql);
			$database->execute();
			$id_final_exam = $database->loadColumn();
			$id_final_exam = @$id_final_exam["0"];

			if (intval($id_final_exam) > 0) {
				$change_order = true;
				$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type='dtask' and mr.type_id=" . intval($module_id) . " and mr.media_id=t.id order by t.ordering desc limit 0,1";
				$database->setQuery($sql);
				$database->execute();
				$lesson_id = $database->loadColumn();
				$last_lesson_id = @$lesson_id["0"];
			}
		}

		$minutes = $data["minutes"];
		$seconds = $data["seconds"];

		if (trim($minutes) != "" || trim($seconds) != "") {
			$data["duration"] = trim($minutes) . "x" . trim($seconds);
		} elseif (trim($minutes) == "" && trim($seconds) == "") {
			$data["duration"] = "";
		}

		$data['startpublish'] = date('Y-m-d H:i:s', strtotime($data['startpublish']));

		if ($data['endpublish'] != 'Never' && $data['endpublish'] != '' && $data['endpublish'] != "0000-00-00 00:00:00") { // calendar change
			$data['endpublish'] = date('Y-m-d H:i:s', strtotime($data['endpublish']));
		}
		$db = JFactory::getDBO();

		$id = JFactory::getApplication()->input->get("id", "", "raw");

		if ($id == "" || $id == "0") {
			$data['alias'] = JFilterOutput::stringURLSafe($data['name']);

			//start set the order. this step must to be the last one
			$query = "select max(ordering) as ordering from #__guru_task";
			$database->setQuery($query);
			$database->execute();
			$result = $database->loadObject();
			$data['ordering'] = intval($result->ordering) + 1;
			//end set the order. this step must to be the last one
		}

		$groups = JFactory::getApplication()->input->get("groups", array(), "raw");
		if (isset($groups) && count($groups) > 0) {
			$data["groups_access"] = implode(",", $groups);
		}

		$description = JFactory::getApplication()->input->get("description", "", "raw");
		$data["description"] = $description;

		if (!$item->bind($data)) {
			return false;
		}
		if (!$item->check()) {
			return false;
		}
		if (!$item->store()) {
			return false;
		}

		$return_array["id"] = $item->id;
		if ($data['id'] == "" || $data['id'] == 0) {
			$new_lesson = "yes";
		} else {
			$new_lesson = "no";
		}

		$db->setQuery("SELECT forumboardcourse,forumboardlesson FROM #__guru_kunena_forum WHERE id=1 ");
		$ressult = $db->loadAssocList();

		if ($ressult[0]["forumboardlesson"] == 1) {
			$new_lesson = "no";
		}

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$lesson_removed = $registry->get('lesson_removed', "");

		if (isset($lesson_removed) && $lesson_removed == "yes") {
			$new_lesson = "yes";
		}

		if (!isset($data['id']) || $data['id'] == 0) {
			$db->setQuery("SELECT max(id) FROM #__guru_task ");
			$data['id'] = $db->loadResult();
		}

		// scr_l = the layout for the screen
		$db->setQuery("DELETE FROM #__guru_mediarel WHERE type_id='" . $data['id'] . "' AND type='scr_l' ");
		$db->execute();

		$db->setQuery("INSERT INTO #__guru_mediarel (`type`,`type_id`,`media_id`,`mainmedia`,`access`,`order`) VALUES ('scr_l','" . $data['id'] . "','" . $data['layout_db'] . "','0', '0', '0')");
		$db->execute();

		// scr_m = the file type for the screen - media
		// mainmedia = 1 for the first media
		// mainmedia = 2 for the second media
		$db->setQuery("DELETE FROM #__guru_mediarel WHERE type_id='" . $data['id'] . "' AND type='scr_m' ");
		$db->execute();

		// scr_t = 	the file type for the screen - text	
		// mainmedia = 1 for normal text
		// mainmedia = 2 for quiz	
		$db->setQuery("DELETE FROM #__guru_mediarel WHERE type_id='" . $data['id'] . "' AND type='scr_t' ");
		$db->execute();

		if (isset($data['day']) && intval($data['day']) > 0) {
			$queri = "INSERT INTO #__guru_mediarel (`type`,`type_id`,`media_id`,`mainmedia`,`text_no`,`access`,`order`) VALUES ('dtask','" . intval($data['my_menu_id']) . "','" . $data['id'] . "','0','0','0','0')";
			$db->setQuery($queri);
			$db->execute();
		}

		if (1 == 1) {
			if (intval($data['db_media_1']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_1']) . "','1',1, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_1']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_1']) . "','" . $mainmedia . "',1, '0', '0')");
				$db->execute();
			}
		}

		if (1 == 1) {
			if (intval($data['db_media_2']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_2']) . "','1',2, '0', '0')");
				$db->execute();
			}
			if (intval($data['db_media_3']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_3']) . "','2',2, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_2']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_2']) . "','" . $mainmedia . "',2, '0', '0')");
				$db->execute();
			}
		}

		if (1 == 1) {
			if (intval($data['db_media_4']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_4']) . "','1',3, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_3']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_3']) . "','" . $mainmedia . "',3, '0', '0')");
				$db->execute();
			}
		}

		if (1 == 1) {
			if (intval($data['db_media_5']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_5']) . "','1',4, '0', '0')");
				$db->execute();
			}
			if (intval($data['db_media_6']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_6']) . "','2',4, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_4']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_4']) . "','" . $mainmedia . "',4, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_5']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_5']) . "','" . $mainmedia . "',5, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			if (intval($data['db_media_7']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_7']) . "','1',6, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			if (intval($data['db_media_8']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_8']) . "','1',7, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_6']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_6']) . "','" . $mainmedia . "',7, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			if (intval($data['db_media_9']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_9']) . "','1',8, '0', '0')");
				$db->execute();
			}
			if (intval($data['db_media_10']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_10']) . "','2',8, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_7']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_7']) . "','" . $mainmedia . "',8, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			if (intval($data['db_media_11']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_11']) . "','1',9, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_8']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_8']) . "','" . $mainmedia . "',9, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			if (intval($data['db_media_12']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_12']) . "','1',10, '0', '0')");
				$db->execute();
			}
			if (intval($data['db_media_13']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_13']) . "','2',10, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_9']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_9']) . "','" . $mainmedia . "',10, '0', '0')");
				$db->execute();
			}
		}

		if (1 == 1) {
			if (intval($data['db_media_14']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_14']) . "','1',11, '0', '0')");
				$db->execute();
			}

			$mainmedia = 1;
			if (intval($data['text_is_quiz']) == 1)
				$mainmedia = 2;
			if (intval($data['db_text_10']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_10']) . "','" . $mainmedia . "', 1, 11, '0', '0')");
				$db->execute();
			}

			if (intval($data['db_text_11']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,layout,`access`,`order`) VALUES ('scr_t','" . $data['id'] . "','" . intval($data['db_text_11']) . "','" . $mainmedia . "', 2,11, '0', '0')");
				$db->execute();
			}
		}

		if (1 == 1) {
			if (intval($data['db_media_15']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_15']) . "','1',12, '0', '0')");
				$db->execute();

				$listoflessons = "select distinct(media_id) from #__guru_mediarel where type='dtask' and type_id IN (select id from #__guru_days where pid=" . intval($data['day']) . ")";
				$db->setquery($listoflessons);
				$listoflessons = $db->loadColumn();


				$listoflessons = implode(",", $listoflessons);
				if ($listoflessons == "") {
					$listoflessons = "0";
				}

				$count_quiz = "select count(media_id) from #__guru_mediarel where type='scr_l' and media_id='12' and type_id IN (" . $listoflessons . ")";
				$db->setquery($count_quiz);
				$count_quiz = $db->loadColumn();
				$count_quiz = $count_quiz["0"];

				$sql = "UPDATE #__guru_program set hasquiz = " . intval($count_quiz) . " WHERE id =" . intval($data['day']);
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if (1 == 1) {
			if (intval($data['db_media_16']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_16']) . "','1',16, '0', '0')");
				$db->execute();
			}
		}


		if (1 == 1) {
			if (intval($data['db_media_17']) > 0) {
				$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_17']) . "','1',17, '0', '0')");
				$db->execute();
			}

			$db = JFactory::getDbo();
			$sql = "select * from #__guru_scorm_items where `id`=" . intval($data['db_media_17']);
			$db->setQuery($sql);
			$scorm_item = $db->loadAssocList();

			if (isset($scorm_item["0"]) && intval($scorm_item["0"]) > 0) {
				include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "scorm.php");
				$scorm_guru = new scormGuruImport();
				$scorm_added = $scorm_guru->uploadScormLesson($data['id'], $scorm_item);
			}
		}

		// jump buttons - Start //

		// delete existing buttons before inserting the new ones
		$sql = "DELETE FROM #__guru_mediarel WHERE type='jump' AND type_id='" . $data['id'] . "'";
		$db->setQuery($sql);
		$db->execute();

		// insert the 4 buttons
		if (intval($data['jumpbutton1']) != 0) {
			$sql1 = "INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`,`order`) VALUES ('jump','" . $data['id'] . "','" . intval($data['jumpbutton1']) . "','0','0', '0', '0')";
			$db->setQuery($sql1);
			$db->execute();
		}
		if (intval($data['jumpbutton2']) != 0) {
			$sql = "INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`,`order`) VALUES ('jump','" . $data['id'] . "','" . intval($data['jumpbutton2']) . "','0','0', '0', '0')";
			$db->setQuery($sql);
			$db->execute();
		}

		if (intval($data['jumpbutton3']) != 0) {
			$sql = "INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`,`order`) VALUES ('jump','" . $data['id'] . "','" . intval($data['jumpbutton3']) . "','0','0', '0', '0')";
			$db->setQuery($sql);
			$db->execute();
		}

		if (intval($data['jumpbutton4']) != 0) {
			$sql = "INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,text_no,`access`,`order`) VALUES ('jump','" . $data['id'] . "','" . intval($data['jumpbutton4']) . "','0','0', '0', '0')";
			$db->setQuery($sql);
			$db->execute();
		}

		if (!isset($data['db_media_99'])) {
			$data['db_media_99'] = 0;
		}
		if (intval($data['db_media_99']) > 0) {
			$db->setQuery("INSERT INTO #__guru_mediarel (type,type_id,media_id,mainmedia,layout,`access`,`order`) VALUES ('scr_m','" . $data['id'] . "','" . intval($data['db_media_99']) . "','1',99, '0', '0')");
			$db->execute();
		}

		// jump buttons - End //

		//start- kunenea forum integration//
		$sql = "select count(*) from #__extensions where element='com_kunena' and name='com_kunena'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if ($count > 0) {
			if (JComponentHelper::isEnabled('com_kunena', true)) {
				$sql = "select forumboardlesson from #__guru_kunena_forum where id=1";
				$db->setQuery($sql);
				$forumboardlesson = $db->loadResult();
				if ($data['kunenabuttonactive'] == 'on') {
					$forumboardlesson = 1;
				}
				if ($forumboardlesson != 0) {
					if ($new_lesson == "no") {
						$sql = "UPDATE #__guru_task SET forum_kunena_generatedt = '1' WHERE id=" . intval($data['id']);
						$db->setQuery($sql);
						$db->execute();

						$db->setQuery("SELECT `kunena_category` FROM #__guru_kunena_forum WHERE id=1");
						$kunena_category = $db->loadColumn();
						$kunena_category = @$kunena_category["0"];

						if (intval($kunena_category) == 0) {
							$nameofmainforum = JText::_('GURU_TREECOURSE');
						} else {
							$sql = "SELECT `name` FROM #__kunena_categories WHERE id='" . intval($kunena_category) . "'";
							$db->setQuery($sql);
							$nameofmainforum = $db->loadResult();
						}

						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='0' and name='" . addslashes($nameofmainforum) . "'";
						$db->setQuery($sql);
						$result = $db->loadResult();


						if (!$result) {
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES (" . intval($kunena_category) . ", '" . addslashes($nameofmainforum) . "', '" . addslashes(strtolower($nameofmainforum)) . "', 0, 0, 'joomla.level', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();

							$sql = "SELECT id FROM #__kunena_categories WHERE name='" . addslashes($nameofmainforum) . "'";
							$db->setQuery($sql);
							$idmainforum = $db->loadResult();

							if (trim($nameofmainforum) != "") {
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '" . addslashes(strtolower($nameofmainforum)) . "', 'catid', " . $idmainforum . ", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
						}

						$sql = "SELECT name from #__guru_program where id =" . intval($data['day']);
						$db->setQuery($sql);
						$coursename = $db->loadResult();

						$sql = "SELECT alias from #__guru_program where id =" . intval($data['day']);
						$db->setQuery($sql);
						$aliascourse = $db->loadResult();




						$sql = "SELECT id FROM #__kunena_categories WHERE name='" . addslashes($nameofmainforum) . "'";
						$db->setQuery($sql);
						$idmainforum = $db->loadResult();


						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='" . $idmainforum . "' and name='" . addslashes($coursename) . "'";
						$db->setQuery($sql);
						$result1 = $db->loadResult();

						if (!$result1) {
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '" . $idmainforum . "', '" . addslashes($coursename) . "', '" . addslashes($aliascourse) . "', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();

							if (trim($aliascourse) != "") {
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '" . addslashes($aliascourse) . "', 'catid', " . $idmainforum . ", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
						}

						$sql = "SELECT title from #__guru_days where pid =" . intval($data['day']) . " and id IN (SELECT type_id FROM #__guru_mediarel WHERE media_id=" . $data['id'] . ")";
						$db->setQuery($sql);
						$modulename = $db->loadResult();

						$sql = "SELECT alias from #__guru_days where pid =" . intval($data['day']) . " and id IN (SELECT type_id FROM #__guru_mediarel WHERE media_id=" . $data['id'] . ")";
						$db->setQuery($sql);
						$aliasmodule = $db->loadResult();

						$sql = "SELECT id FROM #__kunena_categories WHERE alias ='" . addslashes($aliascourse) . "'";
						$db->setQuery($sql);
						$idmaincourse = $db->loadResult();


						$sql = "SELECT name FROM #__kunena_categories WHERE parent_id='" . $idmaincourse . "' and name='" . addslashes($modulename) . "'";
						$db->setQuery($sql);
						$resultmodule = $db->loadResult();



						if (!$resultmodule) {
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '" . $idmaincourse . "', '" . addslashes($modulename) . "', '" . addslashes($aliasmodule) . "', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);
							$db->execute();

							$sql = "SELECT id FROM #__kunena_categories WHERE parent_id='" . $idmaincourse . "' and alias='" . addslashes($aliasmodule) . "'";
							$db->setQuery($sql);
							$id_alias_module = $db->loadResult();

							if (trim($aliasmodule) != "") {
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '" . addslashes($aliasmodule) . "', 'catid', " . $id_alias_module . ", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
						}
						
						$sql = "SELECT alias from #__guru_task where id =" . intval($data['id']);
						$db->setQuery($sql);
						$aliaslesson = $db->loadResult();

						$sql = "SELECT id FROM #__kunena_categories WHERE alias ='" . addslashes($aliasmodule) . "'";
						$db->setQuery($sql);
						$resultidmodule = $db->loadResult();

						$sql = "INSERT INTO #__guru_kunena_courseslinkage (idcourse, coursename, catidkunena) VALUES (  '" . $data['day'] . "', '" . addslashes($coursename) . "', '" . $resultidmodule . "')";
						$db->setQuery($sql);
						$db->execute();

						$sql = "SELECT count(*) FROM #__kunena_categories WHERE alias='" . addslashes($aliaslesson) . "' and parent_id=" . intval($resultidmodule);
						$db->setQuery($sql);
						$result2 = $db->loadResult();
						if ($result2 == 0) {
							$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( " . $resultidmodule . ", '" . addslashes($data['name']) . "', '" . addslashes($aliaslesson) . "', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
							$db->setQuery($sql);

							$sql = "SELECT id FROM #__kunena_categories WHERE parent_id='" . $resultidmodule . "' and alias='" . addslashes($aliaslesson) . "'";
							$db->setQuery($sql);
							$id_alias_lesson = $db->loadResult();

							if ($aliaslesson && $id_alias_lesson) {
								$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '" . addslashes($aliaslesson) . "', 'catid', " . $id_alias_lesson . ", 0)";
								$db->setQuery($sql);
								$db->execute();
							}
						}


						$sql = "SELECT id FROM #__kunena_categories WHERE  alias='" . addslashes($aliaslesson) . "'";
						$db->setQuery($sql);
						$resultidlesson = $db->loadResult();


						$sql = "INSERT INTO #__guru_kunena_lessonslinkage (idlesson, lessonname, catidkunena) VALUES (  '" . $data['id'] . "', '" . addslashes($data['name']) . "', '" . $resultidlesson . "')";
						$db->setQuery($sql);
						$db->execute();

						$sql = "SELECT catidkunena  FROM #__guru_kunena_lessonslinkage where idlesson=" . $data['id'] . " order by id desc limit 0,1";
						$db->setQuery($sql);
						$catidkunena = $db->loadResult();

						$sql = "UPDATE #__kunena_categories set name='" . $db->escape($data['name']) . "' WHERE id=" . intval($catidkunena);
						$db->setQuery($sql);
						$db->execute();

						//end- kunenea forum integration//
					}
				}
			}
		}

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$registry->set('lesson_removed', "");

		$return_array["return"] = true;

		if ($change_order) {
			$id_new_lesson = $return_array["id"];
			$sql = "select ordering from #__guru_task where id=" . intval($id_new_lesson);
			$db->setQuery($sql);
			$new_lesson_ordering = $db->loadColumn();
			$new_lesson_ordering = @$new_lesson_ordering["0"];

			$sql = "select ordering from #__guru_task where id=" . intval($last_lesson_id);
			$db->setQuery($sql);
			$last_lesson_ordering = $db->loadColumn();
			$last_lesson_ordering = @$last_lesson_ordering["0"];

			$sql = "update #__guru_task set ordering = " . intval($new_lesson_ordering) . " where id=" . intval($last_lesson_id);
			$db->setQuery($sql);
			$db->execute();

			$sql = "update #__guru_task set ordering = " . intval($last_lesson_ordering) . " where id=" . intval($id_new_lesson);
			$db->setQuery($sql);
			$db->execute();
		}

		return $return_array;
	}

	function getquiz()
	{
		$jnow = new JDate('now');
		$id = JFactory::getApplication()->input->get("cid", "0", "raw");

		if (empty($this->_attribute)) {
			$this->_attribute = $this->getTable("guruQuiz");
			$this->_attribute->load($id);
		}

		$data = JFactory::getApplication()->input->post->getArray();

		if (!$this->_attribute->bind($data)) {
			return false;
		}
		if (!$this->_attribute->check()) {
			return false;
		}

		if ($this->_attribute->id <= 0) {
			$this->_attribute->text = JText::_('GURU_NEW_Q_BTN');
			$this->_attribute->published = 1;
			$this->_attribute->startpublish =  $jnow->toSQL(true);
		} else $this->_attribute->text = JText::_('GURU_EDIT_Q_BTN');

		if (substr($this->_attribute->endpublish, 0, 4) == '0000' || $this->_attribute->id < 1)
			$this->_attribute->endpublish = JText::_('GURU_NEVER');


		if (!isset($this->_attribute->published)) {
			$this->_attribute->published = 1;
		}
		$this->_attribute->lists['published'] = '<input type="hidden" name="published" value="0">';
		if ($this->_attribute->published == 0) {
			$this->_attribute->lists['published'] .= '<input type="checkbox" value="0" class="ace-switch ace-switch-5" name="published">';
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

	function getMedia()
	{
		$db = Jfactory::getDBO();
		$media = new StdClass;

		$id = JFactory::getApplication()->input->get("cid", "0", "raw");
		$sortquestion = JFactory::getApplication()->input->get("sortquestion", "", "raw");
		$app = JFactory::getApplication('site');
		$limit		= $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get("limitstart", "0", "raw");

		if ($id == 0) {
			$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE qid='0' ");
			$media->mmediam = $db->loadObjectList();
			$media->max_reo = 0;
			$media->min_reo = 0;
			$media->mainmedia = 0;
		} else {
			$column_order = "ORDER BY question_order";
			if ($sortquestion != "") {
				$column_order = "ORDER BY text " . $sortquestion;
			}

			$db->setQuery("SELECT count(*) FROM #__guru_questions_v3 WHERE qid='" . $id . "'");
			$total = $db->loadColumn();
			$total = @$total["0"];
			$media->total = $total;

			$sql_limit = "LIMIT " . $limitstart . "," . $limit;
			if (intval($limit) == 0) {
				$sql_limit = "";
			}

			$db->setQuery("SELECT * FROM #__guru_questions_v3 WHERE qid='" . $id . "' " . $column_order . " " . $sql_limit);
			$media->mmediam = $db->loadObjectList();

			$db->setQuery("SELECT id FROM #__guru_questions_v3 WHERE qid = '" . $id . "' ORDER BY question_order DESC LIMIT 1");
			$media->max_reo = $db->loadResult();
			$db->setQuery("SELECT id FROM #__guru_questions_v3 WHERE qid = '" . $id . "' ORDER BY question_order ASC LIMIT 1");
			$media->min_reo = $db->loadResult();
			$db->setQuery("SELECT * FROM #__guru_media WHERE id in (SELECT media_id FROM #__guru_mediarel WHERE type='qmed' AND type_id = " . $id . ") ");
			$media->mainmedia = $db->loadObjectList();
		}
		return $media;
	}

	function storeQuiz()
	{
		$app = JFactory::getApplication('site');
		$item = $this->getTable('guruQuiz');
		$user = JFactory::getUser();
		$user_id = $user->id;
		$jnow 	= new JDate('now');
		$now 	= $jnow->toSQL(true);
		$db = JFactory::getDBO();

		$data = JFactory::getApplication()->input->post->getArray();

		if (trim($data["id"]) == "") {
			$data["id"] = 0;
		}

		if (!isset($data["final_quiz"])) {
			$data["final_quiz"] = 0;
		}

		if (!isset($data["student_failed_quiz"])) {
			$data["student_failed_quiz"] = 0;
		}

		if (!isset($data["published"])) {
			$data["published"] = 1;
		}

		$data["nb_quiz_select_up"] = intval($data["nb_quiz_select_up"]);

		$pass_message = JFactory::getApplication()->input->get("pass_message", "", "raw");
		$fail_message = JFactory::getApplication()->input->get("fail_message", "", "raw");
		$pending_message = JFactory::getApplication()->input->get("pending_message", "", "raw");

		$data['pass_message'] = $pass_message;
		$data['fail_message'] = $fail_message;
		$data['pending_message'] = $pending_message;

		$data['description'] = $data['description'];
		if ($data['id'] == 0) {
			$data['startpublish'] = $now;
		} else {
			$sql = "select startpublish from #__guru_quiz where id=" . intval($data['id']);
			$db->setQuery($sql);
			$startpublish = $db->loadColumn();
			$startpublish = @$startpublish["0"];

			$data['startpublish'] = $startpublish;
		}

		$data["author"] = intval($user_id);
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

		if (intval($data['id']) == 0) {
			/*$ask = "SELECT id FROM #__guru_quiz ORDER BY id DESC LIMIT 1 ";
			$db->setQuery( $ask );
			$data['id'] = $db->loadResult();*/
			$data["id"] = $item->id;
			$new_quiz = 1;
			$app->setUserState('new_quiz_id', $data['id']);
		}

		$quizid = $data['id'];

		$md = "SELECT id FROM #__guru_media WHERE source='" . $quizid . "' ORDER BY id DESC LIMIT 1";
		$db->setQuery($md);
		$md_id = $db->loadResult();

		if (isset($data['valueop']) && $data['valueop'] == 1) {
			//Save settings for quiz timer
			$sql = "UPDATE #__guru_quiz SET max_score='" . $data['max_score_pass'] . "', pbl_max_score='" . $data['show_max_score_pass'] . "', time_quiz_taken='" . $data['nb_quiz_taken'] . "', show_nb_quiz_taken='" . $data['show_nb_quiz_taken'] . "', nb_quiz_select_up='" . intval($data['nb_quiz_select_up']) . "', show_nb_quiz_select_up='" . $data['show_nb_quiz_select_up'] . "', final_quiz= '" . @$data['final_quiz'] . "', limit_time='" . $data['limit_time_l'] . "', limit_time_f = '" . $data['limit_time_f'] . "', show_finish_alert = '" . $data['show_finish_alert'] . "', student_failed_quiz = '" . @$data['student_failed_quiz'] . "', is_final ='1' WHERE id='" . $quizid . "'";
			$db->setQuery($sql);
			$db->execute();
		} else {
			$sql = "UPDATE #__guru_quiz SET max_score='" . $data['max_score_pass'] . "', pbl_max_score='" . $data['show_max_score_pass'] . "', time_quiz_taken='" . $data['nb_quiz_taken'] . "', show_nb_quiz_taken='" . $data['show_nb_quiz_taken'] . "', nb_quiz_select_up='" . intval($data['nb_quiz_select_up']) . "', show_nb_quiz_select_up='" . $data['show_nb_quiz_select_up'] . "', final_quiz= '" . @$data['final_quiz'] . "', limit_time='" . $data['limit_time_l'] . "', limit_time_f = '" . $data['limit_time_f'] . "', show_finish_alert = '" . $data['show_finish_alert'] . "', student_failed_quiz = '" . @$data['student_failed_quiz'] . "',  is_final ='0'  WHERE id='" . $quizid . "'";
			$db->setQuery($sql);
			$db->execute();
		}
		//END Save settings for quiz timer 

		if (!$md_id) {
			$sql = "INSERT INTO #__guru_media (name ,instructions ,type ,source ,uploaded ,code ,url ,local ,width ,height ,published, option_video_size, category_id, auto_play, show_instruction, author) VALUES ('" . addslashes($data['name']) . "', '" . addslashes($data['description']) . "', 'quiz', '" . $quizid . "', '0', NULL , NULL , NULL , '0', '0', '" . $data['published'] . "', 0, 0, 0, 0, 0);";
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
				return false;
			}
		} elseif (isset($data['valueop']) && $data['valueop'] == 1) {
			$sql = "UPDATE #__guru_quizzes_final SET 
						qid = '" . $quizid . "'
						WHERE qid ='0' ";
			$db->setQuery($sql);
			if (!$db->execute()) {
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
			if (intval($data['id']) == 0) {
				$ask = "SELECT id FROM #__guru_quiz ORDER BY id DESC LIMIT 1 ";
				$db->setQuery($ask);
				$data['id'] = $db->loadResult();
			}
			$progid = $data['id'];

			$thefiles = explode(',', $data['mediafiles']);

			$id_tmp_med_task_2_remove = array();
			if (isset($data['mediafiletodel']))
				$id_tmp_med_files_2_remove = explode(',', $data['mediafiletodel']);

			foreach ($thefiles as $files) {
				if (intval($files) > 0 && !in_array($files, $id_tmp_med_files_2_remove)) {
					$db->setQuery("INSERT INTO #__guru_mediarel (id,type,type_id,media_id,mainmedia) VALUES ('','qmed','" . $progid . "','" . $files . "','0')");
					$db->execute();
				}
			}
		} // end if

		if (isset($data['deleteq'])) {
			$thefiles = explode(',', trim($data['deleteq'], ","));

			foreach ($thefiles as $files) {
				if (intval($files) > 0 && (isset($data['valueop']) && $data['valueop'] == 0)) {
					$sql = "delete from #__guru_questions_v3 where id=" . $files;
					$db->setQuery($sql);
					$db->execute();
				} else {
					$sql = "select quizzes_ids from #__guru_quizzes_final where qid=" . intval($quizid) . " order by id DESC LIMIT 0,1 ";
					$db->setQuery($sql);
					$result = $db->loadResult();


					$newvalues = str_replace($data['deleteq'], "", $result);


					$sql = "update #__guru_quizzes_final set quizzes_ids='" . $newvalues . "' where qid=" . intval($quizid);
					$db->setQuery($sql);
					$db->execute();
				} // end if
			} // end for
		} // end if

		if (isset($data['order_q'])) {
			foreach ($data['order_q'] as $key => $value) {
				if (isset($data['publish_q'][$key])) {
					$published_cond = ",published = '" . $data['publish_q'][$key] . "'";
				}
				$sql = "UPDATE #__guru_questions_v3 SET 
						question_order = '" . intval($value) . "'" . $published_cond . "
						WHERE id ='" . $key . "' ";
				$db->setQuery($sql);
				$db->execute();
			}
		}
		return $res;
	}

	function getQuizById()
	{
		$db = Jfactory::getDBO();
		$query = "select * from #__guru_quiz where id=" . intval($this->_id) . " limit 1";
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}

	function addquestion($qtext, $quizid, $question_type, $media_ids, $points, $true_false_ch, $question_id, $from_save_or_not, $ans_content)
	{
		$db = JFactory::getDBO();
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

		$query = 'SELECT MAX(id) FROM #__guru_questions_v3';
		$db->setQuery($query);
		$id_question = $db->loadColumn();

		// start true/false question
		if ($question_type == 'true_false') {
			$true_default = "0";
			$false_default = "0";

			if (intval($true_false_ch) == "0") {
				$true_default = "1";
				$false_default = "0";
			} elseif (intval($true_false_ch) == "1") {
				$true_default = "0";
				$false_default = "1";
			}

			$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes(JText::_("GURU_QUESTION_OPTION_TRUE")) . "', '' , '" . addslashes($true_default) . "'," . intval($id_question["0"]) . ");";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}

			$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes(JText::_("GURU_QUESTION_OPTION_FALSE")) . "', '' , '" . addslashes($false_default) . "', " . intval($id_question["0"]) . ");";
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
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

					$sql = "INSERT INTO #__guru_question_answers(answer_content_text, media_ids, correct_answer, question_id) VALUES ('" . addslashes($value["0"]) . "', '" . addslashes($media) . "' , '" . addslashes($correct) . "'," . intval($id_question["0"]) . ");";
					$db->setQuery($sql);
					if (!$db->execute()) {
						return false;
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

	function updatequestion($qtext, $quizid, $question_type, $media_ids, $points, $true_false_ch, $question_id, $from_save_or_not, $ans_content)
	{
		$db = JFactory::getDBO();
		$media_ids1 = json_encode($media_ids);
		$sql = "UPDATE #__guru_questions_v3 SET question_content = '" . addslashes($qtext) . "', media_ids = '" . $media_ids1 . "',points = " . $points . " WHERE id =" . $question_id . " LIMIT 1";
		$db->setQuery($sql);
		if (!$db->execute()) {
			return false;
		}
		$query = 'SELECT MAX(id) FROM #__guru_questions_v3';
		$db->setQuery($query);
		$id_question = $db->loadColumn();

		// start true/false question
		if ($question_type == 'true_false') {
			$true_default = "0";
			$false_default = "0";

			if (intval($true_false_ch) == "0") {
				$true_default = "1";
				$false_default = "0";
			} elseif (intval($true_false_ch) == "1") {
				$true_default = "0";
				$false_default = "1";
			}

			$sql = "UPDATE #__guru_question_answers set correct_answer= '" . addslashes($true_default) . "' where answer_content_text='" . addslashes(JText::_("GURU_QUESTION_OPTION_TRUE")) . "' and question_id=" . intval($question_id);
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}

			$sql = "UPDATE #__guru_question_answers set correct_answer= '" . addslashes($false_default) . "' where answer_content_text='" . addslashes(JText::_("GURU_QUESTION_OPTION_FALSE")) . "' and question_id=" . intval($question_id);
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
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

	function getCurrentJump()
	{
		$db = JFactory::getDBO();
		$data_get = JFactory::getApplication()->input->get->getArray();

		if (isset($data_get['id'])) {
			$id = intval($data_get['id']);
		} else {
			return NULL;
		}
		$sql = "SELECT * FROM #__guru_jump WHERE id = " . $id;
		$db->setQuery($sql);
		return $db->loadObject();
	}

	function saveJump()
	{
		$data = JFactory::getApplication()->input->post->getArray();
		$db = JFactory::getDBO();
		$pieces = explode("|", $data['selstep']);
		$module_id = $data["jump_mod_id"];

		$type_selected = JFactory::getApplication()->input->get("type_selected", "", "raw");

		if (isset($data['editid']) && ($data['editid'] != 0)) {
			$sql = "UPDATE #__guru_jump SET text = '" . trim(addslashes($data['jumptext'])) . "',jump_step='" . $pieces["0"] . "', module_id1=" . intval($module_id) . ", type_selected='" . trim($type_selected) . "' WHERE id = " . $data['editid'] . " LIMIT 1 ;";
			$db->setQuery($sql);
			$db->execute();
			$ret[] = $data['editid'];
		} else {
			$sql = "INSERT INTO #__guru_jump (button ,text ,jump_step, module_id1, type_selected)
				VALUES ('" . $pieces["1"] . "', '" . trim(addslashes($data['jumptext'])) . "', '" . $pieces["0"] . "', " . intval($module_id) . ", '" . trim($type_selected) . "');";
			$db->setQuery($sql);
			$db->execute();
			if (!isset($last_id) || ($last_id == 0)) {
				$sql = "SELECT id FROM #__guru_jump ORDER BY id DESC LIMIT 1";
				$db->setQuery($sql);
				$last_id = $db->loadResult($sql);
			}
			$ret[] = $last_id;
		}
		$ret[] = $pieces[1];
		$ret[] = $data['jumptext'];

		return $ret;
	}

	function duplicateQuiz()
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

			$row->name = JText::_('GURU_CS_COPY_TITLE') . ' ' . $row->name;
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

					$sql = "INSERT INTO #__guru_questions_v3 
															( 
																qid, 
																type, 
																question_content, 
																media_ids, 
																points, 
																published, 
																question_order
													) VALUES (
																'" . $new_quiz_id . "', 
																'" . $db->escape($the_question_object->type) . "', 
																'" . $db->escape($the_question_object->question_content) . "' , 
																'" . $db->escape($the_question_object->media_ids) . "', 
																'" . $db->escape($the_question_object->points) . "',
																'" . $db->escape($the_question_object->published) . "',
																'" . $db->escape($the_question_object->question_order) . "'
															)";
					$db->setQuery($sql);
					if (!$db->execute()) {
						return false;
					}
				}
			} else {
				$sql = "INSERT INTO #__guru_quizzes_final (quizzes_ids, qid, published)VALUES('" . $result_fq[0] . "', '" . $new_quiz_id . "',1)";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
		}
		return true;
	}

	function getJumps()
	{
		$data_get = JFactory::getApplication()->input->get->getArray();

		$stepid = $data_get['cid'];
		$db = JFactory::getDBO();
		$sql = "SELECT j . *
		FROM #__guru_jump AS j, #__guru_mediarel AS m
		WHERE j.id = m.media_id
		AND m.type = 'jump'
		AND m.type_id =" . intval($stepid) . "
		ORDER BY j.button ASC
		LIMIT 10";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	function getTotalStudentsByCourseId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select distinct(g.userid) from #__guru_buy_courses g, #__users u, #__guru_order o where g.course_id=" . intval($id) . " and g.userid=u.id and o.id = g.order_id";
		$db->setQuery($sql);
		$result = $db->loadColumn();

		return count($result);
	}

	function getStudentCompleteByCourseId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select count(*) from #__guru_viewed_lesson g, #__users u where g.pid=" . intval($id) . " and g.completed='1' and g.user_id = u.id";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return intval(@$result["0"]);
	}

	function getQuizzesByCourseId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");

		$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=" . intval($id);
		$db->setQuery($sql);
		$lessons = $db->loadColumn();

		if (!isset($lessons) || count($lessons) == 0) {
			$lessons = array("0");
		}

		$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (" . implode(", ", $lessons) . ")";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return count($result);
	}

	function getScoreByCourseId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");

		$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=" . intval($id);
		$db->setQuery($sql);
		$lessons = $db->loadColumn();

		if (!isset($lessons) || count($lessons) == 0) {
			$lessons = array("0");
		}

		$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (" . implode(", ", $lessons) . ")";
		$db->setQuery($sql);
		$ids = $db->loadColumn();

		if (isset($ids) && count($ids) > 0) {
			$sql = "SELECT avg(max_score) FROM #__guru_quiz WHERE id in (" . implode(", ", $ids) . ")";
			$db->setQuery($sql);
			$result = $db->loadColumn();
			$result = @$result["0"];
			return number_format((float)$result, 2, '.', '');
		} else {
			return "0";
		}
	}

	function getFinalExamByCourseId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select id_final_exam from #__guru_program where id=" . intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = @$result["0"];
		return $result;
	}

	function getPassByCourseId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select distinct(userid) from #__guru_buy_courses where course_id=" . intval($id);
		$db->setQuery($sql);
		$students = $db->loadColumn();
		$final_id = $this->getFinalExamByCourseId();

		$sql = "select max_score from #__guru_quiz where id=" . intval($final_id);
		$db->setQuery($sql);
		$max_score = @$max_score["0"];

		$total = 0;

		if (isset($students) && count($students) > 0) {
			$sql = "select user_id, score_quiz from #__guru_quiz_question_taken_v3 where quiz_id=" . intval($final_id);
			$db->setQuery($sql);
			$result = $db->loadAssocList("user_id");

			$score = array();
			foreach ($students as $key => $stud_id) {
				if (isset($result[$stud_id])) {
					$score_quiz = $result[$stud_id]["score_quiz"];
					$score_quiz_array = explode("|", $score_quiz);

					$percent = 0;

					if (isset($score_quiz_array["1"]) && intval($score_quiz_array["1"]) > 0) {
						$percent = (intval($score_quiz_array["0"]) * 100) / intval($score_quiz_array["1"]);
					}

					if ($percent >= $max_score) {
						$score[] = $percent;
						$total++;
					} else {
						$score[] = 0;
					}
				} else {
					$score[] = 0;
				}
			}
		} else {
			return "0";
		}

		$sum = array_sum($score);
		$return = $sum / count($score);
		$return = number_format((float)$return, 2, '.', '');

		$array = array("percent" => $return, "total" => $total);

		return $array;
	}

	function getCourseName()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select name from #__guru_program where id=" . intval($id);
		$db->setQuery($sql);
		$name = $db->loadColumn();
		$name = @$name["0"];
		return $name;
	}
	function getCourseName1($id)
	{
		$db = JFactory::getDBO();
		$sql = "select name from #__guru_program where id=" . intval($id);
		$db->setQuery($sql);
		$name = $db->loadColumn();
		$name = @$name["0"];
		return $name;
	}

	function getQuizzName()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select name from #__guru_quiz where id=" . intval($id);
		$db->setQuery($sql);
		$name = $db->loadColumn();
		$name = @$name["0"];
		return $name;
	}

	function getTotalStudentsByQuizzId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select distinct(q.user_id) from #__guru_quiz_question_taken_v3 q, #__users u where quiz_id=" . intval($id) . " and q.user_id=u.id";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return count($result);
	}

	function getScoreToPassByQuizzId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select max_score from #__guru_quiz where id=" . intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return @$result["0"];
	}

	function getAvgScoreByQuizzId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "SELECT max(id) FROM #__guru_quiz_question_taken_v3
				WHERE quiz_id=" . intval($id) . " GROUP BY user_id, quiz_id";
		$db->setQuery($sql);
		$result_latest_ids = $db->loadColumn();

		if (!is_array($result_latest_ids) || count($result_latest_ids) == 0) {
			$result_latest_ids = array("0");
		}

		$sql = "SELECT user_id, quiz_id, score_quiz
				FROM #__guru_quiz_question_taken_v3
				WHERE quiz_id=" . intval($id) . " and id IN (" . implode(",", $result_latest_ids) . ")";
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		if (isset($result) && count($result) > 0) {
			$sum = "";
			foreach ($result as $key => $value) {
				@$sum += $value["score_quiz"];
			}
			$return = $sum / count($result);
			$return = number_format((float)$return, 2, '.', '');
			return $return;
		} else {
			return "0";
		}
	}

	function getStudentsPassByQuizzId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "SELECT max(id) FROM #__guru_quiz_question_taken_v3
				WHERE quiz_id=" . intval($id) . " GROUP BY user_id, quiz_id";
		$db->setQuery($sql);
		$result_latest_ids = $db->loadColumn();

		if (!is_array($result_latest_ids) || count($result_latest_ids) == 0) {
			$result_latest_ids = array("0");
		}

		$sql = "SELECT user_id, quiz_id, score_quiz
				FROM #__guru_quiz_question_taken_v3
				WHERE quiz_id=" . intval($id) . " and id IN (" . implode(",", $result_latest_ids) . ")";
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$sql = "select max_score from #__guru_quiz where id=" . intval($id);
		$db->setQuery($sql);
		$max_score = $db->loadColumn();
		$max_score = @$max_score["0"];

		if (isset($result) && count($result) > 0) {
			$sum = 0;
			foreach ($result as $key => $value) {
				if ($value["score_quiz"] >= $max_score) {
					$sum++;
				}
			}
			return $sum;
		} else {
			return "0";
		}
	}

	function getStudentsFailedByQuizzId()
	{
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "SELECT max(id) FROM #__guru_quiz_question_taken_v3
				WHERE quiz_id=" . intval($id) . " GROUP BY user_id, quiz_id";
		$db->setQuery($sql);
		$result_latest_ids = $db->loadColumn();

		if (!is_array($result_latest_ids) || count($result_latest_ids) == 0) {
			$result_latest_ids = array("0");
		}

		$sql = "SELECT user_id, quiz_id, score_quiz
				FROM #__guru_quiz_question_taken_v3
				WHERE quiz_id=" . intval($id) . " and id IN (" . implode(",", $result_latest_ids) . ")";
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$sql = "select max_score from #__guru_quiz where id=" . intval($id);
		$db->setQuery($sql);
		$max_score = $db->loadColumn();
		$max_score = @$max_score["0"];

		if (isset($result) && count($result) > 0) {
			$sum = 0;
			foreach ($result as $key => $value) {
				if ($value["score_quiz"] < $max_score) {
					$sum++;
				}
			}
			return $sum;
		} else {
			return "0";
		}
	}
	function last_quiz()
	{
		$database = JFactory::getDBO();
		$ask = "SELECT id FROM #__guru_quiz ORDER BY id DESC LIMIT 1 ";
		$database->setQuery($ask);
		$newid = $database->loadColumn();
		return $newid["0"];
	}

	function saveOrderQuest()
	{
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$cid = array_values($cids);
		$order = JFactory::getApplication()->input->get('order', array(0), "raw");
		$order = array_values($order);
		$total = count($cid);
		for ($i = 0; $i < $total; $i++) {
			$sql = "update #__guru_questions_v3 set reorder=" . $order[$i] . " where id=" . $cid[$i];
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}
		return true;
	}

	function saveorderFile()
	{
		$cids = JFactory::getApplication()->input->get('cid', array(), "raw");
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$order = JFactory::getApplication()->input->get("order", array(), "raw");
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$db = JFactory::getDBO();
		if (isset($cids) && count($cids) > 0) {
			foreach ($cids as $key => $value) {
				$sql = "update #__guru_mediarel set order=" . intval($order[$key]) . " where type='pmed' and type_id=" . intval($id) . " and media_id=" . intval($value);
				$db->setQuery($sql);
				$db->execute();
			}
		}
		return true;
	}

	function sendEmailForAskApprove($course_id)
	{
		$db = JFactory::getDBO();
		$sql = "select p.author from #__guru_program p where p.id=" . intval($course_id);
		$db->setQuery($sql);
		$authors = $db->loadAssocList();
		$authors = $authors["0"]["author"];

		$sql = "select * from #__guru_program where id=" . intval($course_id);
		$db->setQuery($sql);
		$course_details = $db->loadAssocList();

		$authors = explode("|", $authors);
		if (isset($authors) && count($authors) > 0) {
			foreach ($authors as $key => $author) {
				if (intval($author) == 0) {
					continue;
				}

				$sql = "select u.id, u.name as username from #__users u where u.id=" . intval($author);
				$db->setQuery($sql);
				$result = $db->loadAssocList();

				$sql = "select template_emails, fromname, fromemail,admin_email from #__guru_config";
				$db->setQuery($sql);
				$confic = $db->loadAssocList();
				$template_emails = $confic["0"]["template_emails"];
				$template_emails = json_decode($template_emails, true);
				$fromname = $confic["0"]["fromname"];
				$fromemail = $confic["0"]["fromemail"];

				$sql = "select u.email from #__users u, #__user_usergroup_map ugm where u.id=ugm.user_id and ugm.group_id='8' and u.id IN (" . $confic["0"]["admin_email"] . ")";
				$db->setQuery($sql);
				$email = $db->loadColumn();

				$app = JFactory::getApplication();
				$site_name = $app->getCfg('sitename');

				$subject = $template_emails["ask_approve_subject"];
				$body = $template_emails["ask_approve_body"];

				$approve_url = '<a href="' . JURI::root() . "administrator/index.php?option=com_guru&controller=guruPrograms&cid[]=" . intval($result["0"]["id"]) . "&task=approve" . '" target="_blank">' . JURI::root() . "administrator/index.php?option=com_guru&controller=guruPrograms&cid[]=" . intval($result["0"]["id"]) . "&task=approve" . '</a>';

				$subject = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $subject);
				$subject = str_replace("[COURSE_NAME]", $course_details["0"]["name"], $subject);
				$subject = str_replace("[COURSE_APPROVE_URL]", $approve_url, $subject);

				$body = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $body);
				$body = str_replace("[COURSE_NAME]", $course_details["0"]["name"], $body);
				$body = str_replace("[COURSE_APPROVE_URL]", $approve_url, $body);

				for ($i = 0; $i < count($email); $i++) {
					$send_admin_email_course_approved = isset($template_emails["send_admin_email_course_approved"]) ? $template_emails["send_admin_email_course_approved"] : 1;

					if ($send_admin_email_course_approved) {
						JFactory::getMailer()->sendMail($fromemail, $fromname, $email[$i], $subject, $body, 1);
					}

					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->clear();
					$query->insert('#__guru_logs');
					$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
					$query->values(intval($author) . ',' . $db->quote('email-to-ask-approved') . ',' . '0' . ',' . $db->quote(trim($email[$i])) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}

	function getStudentDetails()
	{
		$userid = JFactory::getApplication()->input->get("userid", "0", "raw");
		$db = JFactory::getDBO();
		$courses = array();
		$user = JFactory::getUser();

		if (intval($userid) > 0) {
			$sql = "select distinct(bc.course_id), p.name, vl.completed from #__guru_buy_courses bc left outer join #__guru_viewed_lesson vl on vl.pid=bc.course_id and vl.user_id=bc.userid, #__guru_program p where bc.userid=" . intval($userid) . " and bc.course_id=p.id and (p.author=" . intval($user->id) . " OR p.author like '%|" . intval($user->id) . "|%')";
			$db->setQuery($sql);
			$courses = $db->loadAssocList("course_id");
			$courses_ids = array_keys($courses);

			if (isset($courses_ids) && count($courses_ids) > 0) {
				foreach ($courses_ids as $key => $course_id) {
					$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_days d where mr.type='dtask' and mr.type_id=d.id and d.pid=" . intval($course_id);
					$db->setQuery($sql);
					$lessons = $db->loadColumn();

					if (!isset($lessons) || count($lessons) == 0) {
						$lessons = array("0");
					}

					$sql = "select mr.media_id from #__guru_mediarel mr where mr.layout='12' and mr.type='scr_m' and mr.type_id in (" . implode(", ", $lessons) . ")";
					$db->setQuery($sql);
					$ids = $db->loadColumn();

					if (isset($ids) && count($ids) > 0) {
						$courses[$course_id]["quizes"] = count($ids);
					} else {
						$courses[$course_id]["quizes"] = "0";
					}

					/*$sql = "select count(*) from #__guru_quiz_question_taken_v3 where user_id=".intval($userid)." and pid=".intval($course_id)." group by quiz_id";
					$db->setQuery($sql);
					$db->execute();
					$taken = $db->loadColumn();
					$taken = @$taken["0"];*/

					$sql = "select * from #__guru_quiz_question_taken_v3 where user_id=" . intval($userid) . " and pid=" . intval($course_id);
					$db->setQuery($sql);
					$taken = $db->loadAssocList();
					$taken_ids = array();

					if (isset($taken) && is_array($taken) && count($taken) > 0) {
						foreach ($taken as $keytaken => $valuetaken) {
							if (isset($valuetaken["quiz_id"]) && intval($valuetaken["quiz_id"]) > 0) {
								if (!in_array($valuetaken["quiz_id"], $taken_ids)) {
									$taken_ids[$valuetaken["quiz_id"]] = $valuetaken["quiz_id"];
								}
							}
						}
					}

					$taken = count($taken_ids);

					$courses[$course_id]["taken"] = intval($taken);

					$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE id in (select max(id) from #__guru_quiz_question_taken_v3 where user_id=" . intval($userid) . " and pid=" . intval($course_id) . " group by quiz_id order by id desc)";
					$db->setQuery($sql);
					$all_score_taken = $db->loadAssocList();

					if (isset($all_score_taken) && count($all_score_taken) > 0) {
						$sum_taken = 0;
						foreach ($all_score_taken as $key_taken => $value_taken) {
							$score_quiz = $value_taken["score_quiz"];
							$sum_taken += $score_quiz;
						}

						$sum_taken = $sum_taken / count($all_score_taken);
						$sum_taken = number_format((float)$sum_taken, 2, '.', '');

						$courses[$course_id]["taken_percent"] = $sum_taken;
					}

					$avg = "0";

					if (isset($ids) && count($ids) > 0) {
						$sql = "SELECT avg(max_score) FROM #__guru_quiz WHERE id in (" . implode(", ", $ids) . ")";
						$db->setQuery($sql);
						$result = $db->loadColumn();
						$result = @$result["0"];
						$avg = number_format((float)$result, 2, '.', '');
					}

					$courses[$course_id]["avg"] = $avg;

					$sql = "select id_final_exam from #__guru_program where id=" . intval($course_id);
					$db->setQuery($sql);
					$result = $db->loadColumn();
					$final_id = @$result["0"];

					$sql = "select max_score from #__guru_quiz where id=" . intval($final_id);
					$db->setQuery($sql);
					$max_score = $db->loadColumn();
					$max_score = @$max_score["0"];

					$courses[$course_id]["final_min_score"] = $max_score;

					$sql = "select user_id, score_quiz from #__guru_quiz_question_taken_v3 where quiz_id=" . intval($final_id) . " and user_id=" . intval($userid) . " ORDER BY id DESC LIMIT 0,1";
					$db->setQuery($sql);
					$result = $db->loadAssocList("user_id");

					if (isset($result[$userid])) {
						$score_quiz = $result[$userid]["score_quiz"];
						$percent = $score_quiz;
						$courses[$course_id]["final_score"] = $percent;
					} else {
						$courses[$course_id]["final_score"] = 0;
					}
				}
			}
		}
		return $courses;
	}

	function getStudentQuizes()
	{
		$userid = JFactory::getApplication()->input->get("userid", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("pid", "0", "raw");
		$db = JFactory::getDBO();
		$return = array();

		$sql = "SELECT * FROM #__guru_quiz_question_taken_v3 WHERE `user_id`=" . intval($userid) . " and `pid`=" . intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$step_access_quiz = array();

		if (isset($result) && count($result) > 0) {
			foreach ($result as $key => $value) {
				$sql = "select `name`, `max_score`, `time_quiz_taken` from #__guru_quiz where `id`=" . intval($value["quiz_id"]);
				$db->setQuery($sql);
				$quiz_details_result = $db->loadAssocList();

				$result[$key]["name"] = $quiz_details_result["0"]["name"];
				$result[$key]["max_score"] = $quiz_details_result["0"]["max_score"];
				$result[$key]["time_quiz_taken"] = $quiz_details_result["0"]["time_quiz_taken"] == "11" ? JText::_("GURU_UNLIMITED") : $quiz_details_result["0"]["time_quiz_taken"];

				$score_quiz1 = $value["score_quiz"];
				$question_ids = explode(",", $value["question_ids"]);
				$count_right_answer = $value["count_right_answer"];

				if (!is_array($count_right_answer)) {
					$count_right_answer = array($count_right_answer);
				}

				if (isset($question_ids) && count($question_ids) > 0) {
					$sql = "select id, points from #__guru_questions_v3 where id in (" . implode(",", $question_ids) . ") and type='essay'";
					$db->setQuery($sql);
					$essay_questions = $db->loadAssocList("id");

					$sql = "select question_id, grade from #__guru_quiz_essay_mark where question_id in (" . implode(",", $question_ids) . ") and user_id=" . intval($userid);
					$db->setQuery($sql);
					$essay_questions_marked = $db->loadAssocList("question_id");

					if (isset($essay_questions) && count($essay_questions) > 0) {
						foreach ($essay_questions as $essay_key => $essay_question) {
							if (isset($essay_questions_marked[$essay_key])) {
								$total_points = $essay_question["points"];
								$grade = $essay_questions_marked[$essay_key]["grade"];
								$grade_result = (100 * $grade) / $total_points;

								if ($grade_result >= 50) {
									$count_right_answer["0"]++;
								}
							}
						}
					}
				}

				$questions_ids_array = explode(",", $value["question_ids"]);
				$total = count($questions_ids_array);

				$score_quiz = @$score_quiz1["0"];

				if (isset($step_access_quiz[intval($userid) . "-" . intval($value["quiz_id"]) . "-" . intval($course_id)])) {
					$step_access_quiz[intval($userid) . "-" . intval($value["quiz_id"]) . "-" . intval($course_id)]++;
					$result[$key]["timequizuser"] = $step_access_quiz[intval($userid) . "-" . intval($value["quiz_id"]) . "-" . intval($course_id)];
				} else {
					$result[$key]["timequizuser"] = 1;
					$step_access_quiz[intval($userid) . "-" . intval($value["quiz_id"]) . "-" . intval($course_id)] = 1;
				}

				if (isset($score_quiz) && trim($score_quiz) != "") {
					$wrong = $total - $count_right_answer["0"];

					$result[$key]["correct"] = $count_right_answer["0"];
					$result[$key]["wrong"] = $wrong;

					$result[$key]["final_score"] = $value["score_quiz"];
				} else {
					$result[$key]["correct"] = "0";
					$result[$key]["wrong"] = "0";
					$result[$key]["final_score"] = "0";
				}
			}

			$return = $result;
		}

		return $return;
	}

	function getStudentQuizCompleted()
	{
		$db = JFactory::getDBO();
		$pid = JFactory::getApplication()->input->get("pid", "0", "raw");
		$user_id = JFactory::getApplication()->input->get("userid", "0", "raw");
		$quiz = JFactory::getApplication()->input->get("quiz", "0", "raw");

		$sql = "SELECT * FROM #__guru_quiz_taken_v3 WHERE user_id=" . intval($user_id) . " and pid=" . intval($pid);
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		$sql = "SELECT * FROM #__guru_quiz_taken_v3 WHERE user_id=" . intval($user_id) . " and pid=" . intval($pid);
		$db->setQuery($sql);
		$result = $this->_getList($sql);
	}

	function action()
	{
		$action = JFactory::getApplication()->input->get("action", "", "raw");
		$db = JFactory::getDBO();

		if ($action == "delete") {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			if (isset($cid) && count($cid) > 0) {
				foreach ($cid as $key => $id) {
					// deleting the old day-task relation
					$sql = "DELETE FROM #__guru_mediarel 
							WHERE type='dtask' AND media_id = " . $id;
					$db->setQuery($sql);
					if (!$db->execute()) {
						return false;
					}

					//delete all the relation between this task and medias
					$sql = "DELETE FROM #__guru_mediarel 
							WHERE (type='scr_m' or type='scr_t' or type='scr_l') AND type_id=" . $id;
					$db->setQuery($sql);
					if (!$db->execute()) {
						return false;
					}

					// deleting the task 
					$sql = "DELETE FROM #__guru_task 
							WHERE id = " . $id;
					$db->setQuery($sql);
					if (!$db->execute()) {
						return false;
					}
				}
			}
		} elseif ($action == "publish") {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			if (isset($cid) && count($cid) > 0) {
				$sql = "update #__guru_task set published='1' where id in (" . implode(",", $cid) . ")";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
		} elseif ($action == "unpublish") {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			if (isset($cid) && count($cid) > 0) {
				$sql = "update #__guru_task set published='0' where id in (" . implode(",", $cid) . ")";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
		} elseif ($action == "students") {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			if (isset($cid) && count($cid) > 0) {
				$sql = "update #__guru_task set step_access='0' where id in (" . implode(",", $cid) . ")";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
		} elseif ($action == "members") {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			if (isset($cid) && count($cid) > 0) {
				$sql = "update #__guru_task set step_access='1' where id in (" . implode(",", $cid) . ")";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
		} elseif ($action == "guests") {
			$cid = JFactory::getApplication()->input->get("cid", array(), "raw");
			if (isset($cid) && count($cid) > 0) {
				$sql = "update #__guru_task set step_access='2' where id in (" . implode(",", $cid) . ")";
				$db->setQuery($sql);
				if (!$db->execute()) {
					return false;
				}
			}
		} elseif ($action == "descending") {
			$pid = JFactory::getApplication()->input->get("pid", "0", "raw");
			$sql = "select id, title from #__guru_days where pid=" . intval($pid);
			$db->setQuery($sql);
			$modules = $db->loadAssocList();

			if (isset($modules) && count($modules) > 0) {
				foreach ($modules as $key => $mod_id) {
					$sql = "SELECT distinct(lm.media_id), lt.name, lt.ordering
							FROM #__guru_mediarel lm
							LEFT JOIN #__guru_task lt
							ON lm.media_id=lt.id
							WHERE type='dtask' AND type_id = " . $mod_id["id"] . " order by lt.name DESC";
					$db->setQuery($sql);
					$lessons = $db->loadAssocList();

					if (isset($lessons) && count($lessons) > 0) {
						$i = 1;
						foreach ($lessons as $less_key => $lesson) {
							$sql = "update #__guru_task set ordering=" . intval($i) . " where id = " . intval($lesson["media_id"]);
							$db->setQuery($sql);
							if (!$db->execute()) {
								return false;
							}
							$i++;
						}
					}
				}
			}
		} elseif ($action == "ascending") {
			$pid = JFactory::getApplication()->input->get("pid", "0", "raw");
			$sql = "select id, title from #__guru_days where pid=" . intval($pid);
			$db->setQuery($sql);
			$modules = $db->loadAssocList();

			if (isset($modules) && count($modules) > 0) {
				foreach ($modules as $key => $mod_id) {
					$sql = "SELECT distinct(lm.media_id), lt.name, lt.ordering
							FROM #__guru_mediarel lm
							LEFT JOIN #__guru_task lt
							ON lm.media_id=lt.id
							WHERE type='dtask' AND type_id = " . $mod_id["id"] . " order by lt.name ASC";
					$db->setQuery($sql);
					$lessons = $db->loadAssocList();

					if (isset($lessons) && count($lessons) > 0) {
						$i = 1;
						foreach ($lessons as $less_key => $lesson) {
							$sql = "update #__guru_task set ordering=" . intval($i) . " where id = " . intval($lesson["media_id"]);
							$db->setQuery($sql);
							if (!$db->execute()) {
								return false;
							}
							$i++;
						}
					}
				}
			}
		}
		return true;
	}

	function getAuthorcommissions()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "SELECT commission_id, paypal_email, paypal_other_information from #__guru_authors WHERE userid=" . intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getAuthorcommissionsPending()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "SELECT amount_paid_author, currency from #__guru_authors_commissions WHERE author_id=" . intval($user_id) . " and status_payment='pending'";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	function getAuthorcommissionsPaid()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "SELECT amount_paid_author, currency from #__guru_authors_commissions WHERE author_id=" . intval($user_id) . " and status_payment='paid'";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}

	function applyCommissions()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$data = JFactory::getApplication()->input->post->getArray();
		$sql = "UPDATE #__guru_authors set paypal_email = '" . $data["paypal_email"] . "', paypal_other_information = '" . $data["paypal_other_information"] . "', paypal_option = '" . $data["payment_option"] . "'  WHERE userid=" . intval($user_id);
		$db->setQuery($sql);
		if ($db->execute()) {
			return true;
		}
	}
	function getPaidDetails()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$author_id = $user->id;
		$and = "";
		$filter_course = JFactory::getApplication()->input->get("filter_course", "", "raw");

		$app = JFactory::getApplication('site');

		if (intval($filter_course) != 0) {
			$and .= " and  course_id=" . intval($filter_course);
		}

		$sql = "select * from #__guru_authors_commissions  where status_payment='paid' and author_id=" . intval($author_id) . " " . $and . " ";

		$db->setQuery($sql);
		$result = $db->loadAssocList();

		if (isset($result) && count($result) > 0) {
			$temp = array();
			foreach ($result as $key => $value) {
				// author_id-course_id
				if (isset($temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]])) {
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["amount_paid_author"] = $temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["amount_paid_author"] + $value["amount_paid_author"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["data"] = $value["data"];
				} else {
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["amount_paid_author"] = $value["amount_paid_author"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["author_id"] = $value["author_id"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["course_id"] = $value["course_id"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["id"] = $value["id"];
					$temp[$value["course_id"] . "-" . $value["author_id"] . "-" . $value["history"] . "-" . $value["currency"]]["data"] = $value["data"];
				}
			}
			$result = $temp;
		}

		$export = JFactory::getApplication()->input->get("export", "", "raw");
		if ($export != "") {
			self::exportDetails($result);
		}

		$limitstart = JFactory::getApplication()->input->get("limitstart", "0", "raw");
		$old_limit = JFactory::getApplication()->input->get("old_limit", "0", "raw");

		$this->_total = count($result);
		$pagination = $this->getPagination();
		$limit = $pagination->limit;

		if ($limit != $old_limit) {
			$limitstart = 0;
		}

		$pagination->limitstart = $limitstart;
		$pagination->total = $this->_total;
		$pagination->pagesTotal = ceil($this->_total / $pagination->limit);
		$pagination->pagesStop = ceil($this->_total / $pagination->limit);
		$pagination->pagesCurrent = ($limitstart / $limit + 1);

		if (count($result) <= $limit && $limitstart == 0) {
			//do nothing
		} elseif (count($result) > $limit && $limitstart == 0 && $limit == 0) {
			//do nothing
		} else {
			$result = array_slice($result, $limitstart, $limit);
		}

		$this->set("Pagination", $pagination);

		return $result;
	}

	function exportDetails($result)
	{
		$db = JFactory::getDBO();
		$config = self::getConfig();
		$currencypos = $config->currencypos;
		$character = "GURU_CURRENCY_" . $config->currency;
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$jnow = new JDate('now');
		$current_date = $jnow->toSQL(true);
		$current_date = self::getDateFormat2($current_date);
		$auth_name = self::getAuthorName();
		$course_id_request = JFactory::getApplication()->input->get("course_id", "0", "raw");
		$course_name_top  = self::getCourseName1($course_id_request);

		if ($task == 'details_paid') {
			$header1 = array("" . JText::_("GURU_DETAILS_CSV1") . "", "" . $auth_name . "", "" . $course_name_top . "", "" . $current_date . "");
			$header2 = array("#", "" . JText::_('GURU_ID') . "", "" . JText::_('GURU_MYORDERS_ORDER_DATE') . "", "" . JText::_('GURU_PRICE') . "", "" . JText::_('GURU_O_PAID') . "", "" . JText::_('GURU_COU_STUDENTS') . "", "" . JText::_('GURU_PROMOCODE') . " (% / " . JText::_("GURU_VALUE") . ")", "" . JText::_('GURU_COMMISSIONS') . "");
		} elseif ($task == 'paid_commission') {
			$header1 = array("" . JText::_("GURU_DETAILS_CSV2") . "", "" . $auth_name . "", "" . $current_date . "");
			$header2 = array("#", "" . JText::_('GURU_ID') . "", "" . JText::_('GURU_MYORDERS_ORDER_DATE') . "", "" . JText::_('GURU_COURSE_NAME') . "", "" . JText::_('VIEWORDERSAMOUNTPAID') . "");
		} elseif ($task == 'pending_commission') {
			$header1 = array("" . JText::_("GURU_DETAILS_CSV3") . "", "" . $auth_name . "", "" . $current_date . "");
			$header2 = array("#", "" . JText::_('GURU_ID') . "", "" . JText::_('GURU_MYORDERS_ORDER_DATE') . "", "" . JText::_('GURU_COURSE_NAME') . "", "" . JText::_('GURU_PRICE') . "", "" . JText::_('GURU_O_PAID') . "", "" . JText::_('GURU_COU_STUDENTS') . "", "" . JText::_('GURU_PROMOCODE') . " (% / " . JText::_("GURU_VALUE") . ")", "" . JText::_('GURU_COMMISSIONS') . "");
		}

		$data = implode(",", $header1);
		$data .= "\n\n";
		$data .= implode(",", $header2);
		$data .= "\n";
		$inc = 1;
		if (isset($result) && count($result) > 0) {
			$total_commission = array();
			$sum_price = array();
			$sum_price_paid = array();
			$sum_promo = array();

			foreach ($result as $key => $value) {
				if (!isset($value["currency"])) {
					$temp_key = explode("-", $key);
					$value["currency"] = $temp_key["3"];
				}

				$student_name = self::getStudentName(@$value["customer_id"]);
				$course_name = self::getCourseName1(@$value["course_id"]);
				$promo_name = self::getPromoDetails(@$value["promocode_id"], @$value["price"]);
				$promo_for_calc = self::getPromoDetailsT(@$value["promocode_id"], @$value["price"]);

				if (isset($total_commission[$value["currency"]])) {
					$total_commission[$value["currency"]] += $value["amount_paid_author"];
				} else {
					$total_commission[$value["currency"]] = $value["amount_paid_author"];
				}

				if (isset($sum_price[$value["currency"]])) {
					$sum_price[$value["currency"]] += @$value["price"];
				} else {
					$sum_price[$value["currency"]] = @$value["price"];
				}

				if (isset($sum_price_paid[$value["currency"]])) {
					$sum_price_paid[$value["currency"]] += @$value["price_paid"];
				} else {
					$sum_price_paid[$value["currency"]] = @$value["price_paid"];
				}

				if (isset($sum_promo[$value["currency"]])) {
					$sum_promo[$value["currency"]] += $promo_for_calc;
				} else {
					$sum_promo[$value["currency"]] = $promo_for_calc;
				}

				if ($task == 'details_paid') {
					$data_on = self::getDateFormat2($value["data"]);
					$data .= $inc . ",";
					$data .= $value["id"] . ",";
					$data .= $data_on . ",";
					if ($currencypos == 0) {
						$data .= JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . $value["price"] . ",";
					} else {
						$data .= $value["price"] . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . ",";
					}
					if ($currencypos == 0) {
						$data .= JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . $value["price_paid"] . ",";
					} else {
						$data .= $value["price_paid"] . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . ",";
					}
					$data .= $student_name["0"] . ",";
					$data .= $promo_name . ",";
					if ($currencypos == 0) {
						$data .= '"' . JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . number_format($value["amount_paid_author"], 2) . '"' . "\n";
					} else {
						$data .= '"' . number_format($value["amount_paid_author"], 2) . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . '"' . "\n";
					}
				} elseif ($task == 'paid_commission') {
					$data_on = self::getDateFormat2($value["data"]);
					$data .= $inc . ",";
					$data .= $value["id"] . ",";
					$data .= $data_on . ",";
					$data .= $course_name . ",";
					if ($currencypos == 0) {
						$data .= '"' . JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . number_format($value["amount_paid_author"], 2) . '"' . "\n";
					} else {
						$data .= '"' . number_format($value["amount_paid_author"], 2) . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . '"' . "\n";
					}
				} elseif ($task == 'pending_commission') {
					$data_on = self::getDateFormat2($value["data"]);
					$data .= $inc . ",";
					$data .= $value["id"] . ",";
					$data .= $data_on . ",";
					$data .= $course_name . ",";
					if ($currencypos == 0) {
						$data .= JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . $value["price"] . ",";
					} else {
						$data .= $value["price"] . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . ",";
					}
					if ($currencypos == 0) {
						$data .= JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . $value["price_paid"] . ",";
					} else {
						$data .= $value["price_paid"] . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . ",";
					}
					$data .= $student_name[0] . ",";
					$data .= $promo_name . ",";
					if ($currencypos == 0) {
						$data .= '"' . JText::_("GURU_CURRENCY_" . $value["currency"]) . " " . number_format($value["amount_paid_author"], 2) . '"' . "\n";
					} else {
						$data .= '"' . number_format($value["amount_paid_author"], 2) . " " . JText::_("GURU_CURRENCY_" . $value["currency"]) . '"' . "\n";
					}
				}
				$inc++;
			}

			//-----------------------------------------------------------------------------------
			$total_commissionf = "";
			if (isset($total_commission) && count($total_commission) > 0) {
				$temp = array();
				if ($currencypos == 0) {
					foreach ($total_commission as $currency => $value) {
						$temp[$currency] = JText::_("GURU_CURRENCY_" . $currency) . " " . number_format($value, 2);
					}
					$total_commissionf = implode(" | ", $temp);
				} else {
					foreach ($temp as $currency => $value) {
						$temp[$currency] = number_format($value, 2) . " " . JText::_("GURU_CURRENCY_" . $currency);
					}
					$total_commissionf = implode(" | ", $temp);
				}
			}
			$total_commissionf = '"' . $total_commissionf . '"';
			//-----------------------------------------------------------------------------------

			//-----------------------------------------------------------------------------------
			$sum_pricef = "";
			if (isset($sum_price) && count($sum_price) > 0) {
				$temp = array();
				if ($currencypos == 0) {
					foreach ($sum_price as $currency => $value) {
						$temp[$currency] = JText::_("GURU_CURRENCY_" . $currency) . " " . number_format($value, 2);
					}
					$sum_pricef = implode(" | ", $temp);
				} else {
					foreach ($temp as $currency => $value) {
						$temp[$currency] = number_format($value, 2) . " " . JText::_("GURU_CURRENCY_" . $currency);
					}
					$sum_pricef = implode(" | ", $temp);
				}
			}
			$sum_pricef = '"' . $sum_pricef . '"';
			//-----------------------------------------------------------------------------------

			//-----------------------------------------------------------------------------------
			$sum_price_paidf = "";
			if (isset($sum_price_paid) && count($sum_price_paid) > 0) {
				$temp = array();
				if ($currencypos == 0) {
					foreach ($sum_price_paid as $currency => $value) {
						$temp[$currency] = JText::_("GURU_CURRENCY_" . $currency) . " " . number_format($value, 2);
					}
					$sum_price_paidf = implode(" | ", $temp);
				} else {
					foreach ($temp as $currency => $value) {
						$temp[$currency] = number_format($value, 2) . " " . JText::_("GURU_CURRENCY_" . $currency);
					}
					$sum_price_paidf = implode(" | ", $temp);
				}
			}
			$sum_price_paidf = '"' . $sum_price_paidf . '"';
			//-----------------------------------------------------------------------------------

			//-----------------------------------------------------------------------------------
			$sum_promof = "";
			if (isset($sum_promo) && count($sum_promo) > 0) {
				$temp = array();
				if ($currencypos == 0) {
					foreach ($sum_promo as $currency => $value) {
						$temp[$currency] = JText::_("GURU_CURRENCY_" . $currency) . " " . number_format($value, 2);
					}
					$sum_promof = implode(" | ", $temp);
				} else {
					foreach ($temp as $currency => $value) {
						$temp[$currency] = number_format($value, 2) . " " . JText::_("GURU_CURRENCY_" . $currency);
					}
					$sum_promof = implode(" | ", $temp);
				}
			}
			$sum_promof = '"' . $sum_promof . '"';
			//-----------------------------------------------------------------------------------
		}

		if ($task == 'details_paid') {
			$data .= "\n";
			$data .= "," . "," . JText::_('GURU_SUMMARY') . ",";
			$data .= $sum_pricef . ",";
			$data .= $sum_price_paidf . "," . ",";
			$data .= $sum_promof . ",";
			$data .= $total_commissionf;
			$csv_filename = "paid_commissions_details.csv";
		} elseif ($task == 'paid_commission') {
			$data .= "\n";
			$data .= "," . "," . "," . JText::_('GURU_SUMMARY') . ",";
			$data .= $total_commissionf;
			$csv_filename = "paid_commissions.csv";
		} elseif ($task == 'pending_commission') {
			$data .= "\n";
			$data .= "," . "," . "," . JText::_('GURU_SUMMARY') . ",";
			$data .= $sum_pricef . ",";
			$data .= $sum_price_paidf . "," . ",";
			$data .= $sum_promof . ",";
			$data .= $total_commissionf;
			$csv_filename = "pending_commission.csv";
		}


		$size_in_bytes = strlen($data);
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=" . $csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $data;
		exit();
	}

	function getDateFormat2($data)
	{
		$config = self::getConfig();
		$datetype  = $config->datetype;
		if ($config->hour_format == 12) {
			$format = " Y-m-d h:i:s A ";
			switch ($datetype) {
				case "d-m-Y H:i:s":
					$format = "d-m-Y h:i:s A";
					break;
				case "d.m.Y H:i":
					$format = "d.m.Y h:i A";
					break;
				case "d/m/Y H:i:s":
					$format = "d/m/Y h:i:s A";
					break;
				case "m-d-Y H:i:s":
					$format = "m-d-Y h:i:s A";
					break;
				case "m/d/Y H:i:s":
					$format = "m/d/Y h:i:s A";
					break;
				case "Y-m-d H:i:s":
					$format = "Y-m-d h:i:s A";
					break;
				case "Y/m/d H:i:s":
					$format = "Y/m/d h:i:s A";
					break;
				case "d-m-Y":
					$format = "d-m-Y";
					break;
				case "d/m/Y":
					$format = "d/m/Y";
					break;
				case "m-d-Y":
					$format = "m-d-Y";
					break;
				case "m/d/Y":
					$format = "m/d/Y";
					break;
				case "Y-m-d":
					$format = "Y-m-d";
					break;
				case "Y/m/d":
					$format = "Y/m/d";
					break;
			}
			$date_int = strtotime($data);
			$date_string = JHTML::_('date', $date_int, $format);
		} else {
			$date_int = strtotime($data);
			//$date_string = date("Y-m-d H:i:s", $date_int);
			$format = "Y-m-d H:M:S";
			switch ($datetype) {
				case "d-m-Y H:i:s":
					$format = "d-m-Y H:i:s";
					break;
				case "d.m.Y H:i":
					$format = "d.m.Y H:i";
					break;
				case "d/m/Y H:i:s":
					$format = "d/m/Y H:i:s";
					break;
				case "m-d-Y H:i:s":
					$format = "m-d-Y H:i:s";
					break;
				case "m/d/Y H:i:s":
					$format = "m/d/Y H:i:s";
					break;
				case "Y-m-d H:i:s":
					$format = "Y-m-d H:i:s";
					break;
				case "Y/m/d H:i:s":
					$format = "Y/m/d H:i:s";
					break;
				case "d-m-Y":
					$format = "d-m-Y";
					break;
				case "d/m/Y":
					$format = "d/m/Y";
					break;
				case "m-d-Y":
					$format = "m-d-Y";
					break;
				case "m/d/Y":
					$format = "m/d/Y";
					break;
				case "Y-m-d":
					$format = "Y-m-d";
					break;
				case "Y/m/d":
					$format = "Y/m/d";
					break;
			}
			$date_string = JHTML::_('date', $date_int, $format);
		}
		return $date_string;
	}

	function getPendingDetails()
	{
		$and = "";
		$filter_promocode = JFactory::getApplication()->input->get("filter_promocode", "", "raw");
		$filter_course = JFactory::getApplication()->input->get("filter_course", "", "raw");

		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$block = JFactory::getApplication()->input->get("block", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");
		$user = JFactory::getUser();
		$author_id = $user->id;
		$db = JFactory::getDBO();
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$all_users = "Select id from #__users u where (u.name like '%" . addslashes(trim(@$search)) . "%' or u.username like '%" . addslashes(trim(@$search)) . "%' or u.email like '%" . addslashes(trim(@$search)) . "%')";
		$db->setQuery($all_users);
		$all_users = $db->loadColumn();

		if (intval($filter_promocode) != 0) {
			$and .= " and  promocode_id=" . intval($filter_promocode);
		}
		if (intval($filter_course) != 0) {
			$and .= " and  course_id=" . intval($filter_course);
		}


		if ($task == "details_paid") {
			$sql = "select * from #__guru_authors_commissions  where status_payment='paid' and course_id=" . intval($course_id) . " and author_id=" . intval($author_id) . " and history =" . $block . "  " . $and . " ";
		} else {
			$sql = "select * from #__guru_authors_commissions  where status_payment='pending' and author_id=" . intval($author_id) . " " . $and . " ";
		}
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$export = JFactory::getApplication()->input->get("export", "", "raw");
		if ($export != "") {
			self::exportDetails($result);
		}

		$limitstart = JFactory::getApplication()->input->get("limitstart", "0", "raw");
		$old_limit = JFactory::getApplication()->input->get("old_limit", "0", "raw");

		$this->_total = count($result);
		$pagination = $this->getPagination();

		$limit = $pagination->limit;

		if ($limit != $old_limit) {
			$limitstart = 0;
		}

		$pagination->limitstart = $limitstart;
		$pagination->total = $this->_total;
		$pagination->pagesTotal = ceil($this->_total / $pagination->limit);
		$pagination->pagesStop = ceil($this->_total / $pagination->limit);
		$pagination->pagesCurrent = ($limitstart / $limit + 1);

		if (count($result) <= $limit && $limitstart == 0) {
			//do nothing
		} elseif (count($result) > $limit && $limitstart == 0 && $limit == 0) {
			//do nothing
		} else {
			$result = array_slice($result, $limitstart, $limit);
		}

		$this->set("Pagination", $pagination);

		return $result;
	}

	function getPendingDetailsTotal()
	{
		$block = JFactory::getApplication()->input->get("block", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");
		$user = JFactory::getUser();
		$author_id = $user->id;
		$db = JFactory::getDBO();
		$task = JFactory::getApplication()->input->get("task", "0", "raw");

		if ($task == "details_paid") {
			$sql = "select amount_paid_author, currency from #__guru_authors_commissions  where status_payment='paid' and course_id=" . intval($course_id) . " and author_id=" . intval($author_id) . " and history =" . $block;
		} else {
			$sql = "select amount_paid_author, currency from #__guru_authors_commissions  where status_payment='pending' and author_id=" . intval($author_id);
		}
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getPendingDetailsTotalPrice()
	{
		$block = JFactory::getApplication()->input->get("block", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");
		$user = JFactory::getUser();
		$author_id = $user->id;
		$db = JFactory::getDBO();
		$task = JFactory::getApplication()->input->get("task", "0", "raw");
		if ($task == "details_paid") {
			$sql = "select price, currency from #__guru_authors_commissions  where status_payment='paid' and course_id=" . intval($course_id) . " and author_id=" . intval($author_id) . " and history =" . $block;
		} else {
			$sql = "select price, currency from #__guru_authors_commissions  where status_payment='pending' and author_id=" . intval($author_id);
		}
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getPendingDetailsTotalPricePaid()
	{
		$block = JFactory::getApplication()->input->get("block", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");
		$user = JFactory::getUser();
		$author_id = $user->id;
		$db = JFactory::getDBO();
		$task = JFactory::getApplication()->input->get("task", "0", "raw");
		if ($task == "details_paid") {
			$sql = "select price_paid, currency from #__guru_authors_commissions  where status_payment='paid' and course_id=" . intval($course_id) . " and author_id=" . intval($author_id) . " and history =" . $block;
		} else {
			$sql = "select price_paid, currency from #__guru_authors_commissions  where status_payment='pending' and author_id=" . intval($author_id);
		}
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getStudentName($id)
	{
		$db = JFactory::getDBO();
		$sql = "select u.name from #__users u where u.id=" . intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result;
	}
	function getPromoDetails($promo_id, $total)
	{
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='" . intval($promo_id) . "'";
		$db->setQuery($sql);
		$promo = $db->loadObjectList();
		$promo_details = @$promo["0"];
		$config = self::getConfig();
		$currencypos = $config->currencypos;
		$character = "GURU_CURRENCY_" . $config->currency;
		if ($promo_id == 0) {
			$value_to_display = "-";
		} else {
			if ($promo_details->typediscount == '0') { //use absolute values		
				$percent = ($promo_details->discount * 100) / $total;
				if ($currencypos == 0) {
					$value_to_display = $promo_details->title . " (" . round($percent, 1) . "% / " . JText::_($character) . $promo_details->discount . ")";
				} else {
					$value_to_display = $promo_details->title . " (" . round($percent, 1) . "% / " . $promo_details->discount . JText::_($character) . ")";
				}
			} else { //use percentage
				$percent = ($total * $promo_details->discount) / 100;
				$percent = number_format($percent, 2);

				if ($currencypos == 0) {
					$value_to_display = $promo_details->title . " (" . $promo_details->discount . "% / " . JText::_($character) . $percent . ")";
				} else {
					$value_to_display = $promo_details->title . " (" . $promo_details->discount . "% / " . $percent . JText::_($character) . ")";
				}
			}
		}
		return $value_to_display;
	}
	function getPromoDetailsT($promo_id, $total)
	{
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_promos where id='" . intval($promo_id) . "'";
		$db->setQuery($sql);
		$promo = $db->loadObjectList();
		$promo_details = @$promo["0"];
		$config = self::getConfig();
		$currencypos = $config->currencypos;
		if ($promo_id == 0) {
			$percent = 0;
		} else {
			if ($promo_details->typediscount == '0') { //use absolute values		
				$percent = ($promo_details->discount * 100) / $total;
			} else { //use percentage
				$percent = ($total * $promo_details->discount) / 100;
			}
		}
		return $percent;
	}

	function getAllCourses($teacher_id = 0)
	{
		$db = JFactory::getDBO();
		$where = "";

		if (intval($teacher_id) > 0) {
			$where .= "where (u.author='" . intval($teacher_id) . "' OR u.author like '%|" . intval($teacher_id) . "|%')";
		}

		$sql = "select  u.id, u.name from #__guru_program u " . $where;
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getAuthorName()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$author_id = $user->id;
		$sql = "SELECT u.name FROM #__users u WHERE u.id=" . intval($author_id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result["0"];
	}
	function getAllPromos()
	{
		$db = JFactory::getDBO();
		$sql = "select  u.id, u.code from #__guru_promos u";
		$db->setQuery($sql);
		$result = $db->loadAssocList();
		return $result;
	}
	function getAuthorPaymetOption()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$author_id = $user->id;
		$sql = "SELECT paypal_option FROM #__guru_authors  WHERE userid=" . intval($author_id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		return $result["0"];
	}

	function id_for_last_question()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT max(id) FROM #__guru_questions_v3 ";
		$db->setQuery($sql);
		$id = $db->loadResult();
		return $id;
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

	function storequizdetails()
	{
		$db = JFactory::getDBO();
		$quiz_id = JFactory::getApplication()->input->get("quiz_id", "0", "raw");
		$user_id = JFactory::getApplication()->input->get("user_id", "0", "raw");
		$pid = JFactory::getApplication()->input->get("pid", "0", "raw");
		$grade = JFactory::getApplication()->input->get("grade", array(), "raw");
		$feedback = JFactory::getApplication()->input->get("feedback", array(), "raw");
		$feedback_quiz_results = JFactory::getApplication()->input->get("feedback_quiz_results", array(), "raw");
		$total_points = 0;
		$send_email_to_student = TRUE;

		$sql = "select max(id) from #__guru_quiz_question_taken_v3 where user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and pid=" . intval($pid);
		$db->setQuery($sql);
		$answer_id = $db->loadColumn();
		$answer_id = @$answer_id["0"];

		if (isset($grade) && count($grade) > 0) {
			foreach ($grade as $question_id => $points) {
				$sql = "select count(*) from #__guru_quiz_essay_mark where question_id=" . intval($question_id) . " and user_id=" . intval($user_id);
				$db->setQuery($sql);
				$count = $db->loadColumn();
				$count = $count["0"];
				$total_points += intval($points);

				$sql = "";
				if ($count > 0) {
					$sql = "update #__guru_quiz_essay_mark set grade='" . intval($points) . "', feedback='" . addslashes(trim($feedback[$question_id])) . "', feedback_quiz_results='" . addslashes(trim($feedback_quiz_results[$question_id])) . "', date='" . date("Y-m-d") . "' where question_id=" . intval($question_id) . " and user_id=" . intval($user_id);
				} else {
					$sql = "insert into #__guru_quiz_essay_mark (question_id, user_id, grade, feedback, feedback_quiz_results, date) values ('" . intval($question_id) . "', '" . intval($user_id) . "', '" . intval($points) . "', '" . addslashes(trim($feedback[$question_id])) . "', '" . addslashes(trim($feedback_quiz_results[$question_id])) . "', '" . date("Y-m-d") . "')";
				}
				$db->setQuery($sql);
				if (!$db->execute()) {
					return FALSE;
				}

				if (trim($feedback_quiz_results[$question_id]) == "") {
					$send_email_to_student = FALSE;
				}
			}
		}

		$sql = "select points, question_ids from #__guru_quiz_question_taken_v3 where id=" . intval($answer_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$calculated_points = $result["0"]["points"];
		$question_ids = $result["0"]["question_ids"];

		$sql = "select sum(points) from #__guru_questions_v3 where id in (" . $question_ids . ")";
		$db->setQuery($sql);
		$total_quiz_points = $db->loadColumn();
		$total_quiz_points = @$total_quiz_points["0"];

		$current_score = (($calculated_points + $total_points) * 100) / $total_quiz_points;
		$current_score = number_format((float)$current_score, 2, '.', '');

		$sql = "update #__guru_quiz_question_taken_v3 set score_quiz='" . $current_score . "' where id=" . intval($answer_id);
		$db->setQuery($sql);
		$db->execute();

		//--------------------------------------------------------------
		$sql = "select template_emails, fromname, fromemail,admin_email from #__guru_config";
		$db->setQuery($sql);
		$confic = $db->loadAssocList();
		$template_emails = $confic["0"]["template_emails"];
		$template_emails = json_decode($template_emails, true);
		$fromname = $confic["0"]["fromname"];
		$fromemail = $confic["0"]["fromemail"];

		$app = JFactory::getApplication();
		$site_name = $app->getCfg('sitename');

		$app = JFactory::getApplication();
		$site_name = $app->getCfg('sitename');

		$subject = $template_emails["chek_quiz_subject"];
		$body = $template_emails["chek_quiz_body"];

		$sql = "select name, email from #__users where id=" . intval($user_id);
		$db->setQuery($sql);
		$user_details = $db->loadAssocList();
		$user_name = $user_details["0"]["name"];
		$student_email = $user_details["0"]["email"];

		$sql = "select name from #__guru_quiz where id=" . intval($quiz_id);
		$db->setQuery($sql);
		$quize_name = $db->loadColumn();
		$quize_name = @$quize_name["0"];

		$link_to_quiz = '<a href="' . JRoute::_("index.php?option=com_guru&view=guruauthor&task=mystudents&layout=mystudents", true, -1) . '" target="_blank">' . $quize_name . '</a>';

		$subject = str_replace("[STUDENT_FIRST_NAME]", $user_name, $subject);
		$subject = str_replace("[SITE_NAME]", $site_name, $subject);
		$subject = str_replace("[QUIZ_NAME]", $quize_name, $subject);
		$subject = str_replace("[LINK_TO_QUIZ_RESULT]", $link_to_quiz, $subject);

		$body = str_replace("[STUDENT_FIRST_NAME]", $user_name, $body);
		$body = str_replace("[SITE_NAME]", $site_name, $body);
		$body = str_replace("[QUIZ_NAME]", $quize_name, $body);
		$body = str_replace("[LINK_TO_QUIZ_RESULT]", $link_to_quiz, $body);

		$send_student_email_checked_results = isset($template_emails["send_student_email_checked_results"]) ? $template_emails["send_student_email_checked_results"] : 1;

		if ($send_student_email_checked_results) {
			JFactory::getMailer()->sendMail($fromemail, $fromname, $student_email, $subject, $body, 1);
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->insert('#__guru_logs');
		$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
		$query->values(intval($user_id) . ',' . $db->quote('my-quiz-marcked') . ',' . '0' . ',' . $db->quote(trim($student_email)) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
		$db->setQuery($query);
		$db->execute();

		//***************

		$feedback_quiz_results = JFactory::getApplication()->input->get("feedback_quiz_results", array(), "raw");

		if (isset($feedback_quiz_results) && count($feedback_quiz_results) > 0) {
			foreach ($feedback_quiz_results as $question_id => $text) {
				$sql = "select feedback_quiz_results from #__guru_quiz_essay_mark where question_id=" . intval($question_id) . " and user_id=" . intval($user_id);
				$db->setQuery($sql);
				$feedback_quiz_results = $db->loadColumn();
				$feedback_quiz_results = @$feedback_quiz_results["0"];

				if ($feedback_quiz_results != $text) {
					$subject = $template_emails["feedback_subject"];
					$body = $template_emails["feedback_body"];

					$first_20 = "";
					if (trim($text) != "") {
						$words = explode(" ", $text);
						if (count($words) <= 20) {
							$first_20 = $text;
						} else {
							$temp = array_slice($words, 0, 20);
							$temp = implode(" ", $temp);
							$first_20 = $temp;
						}
					}

					$subject = str_replace("[STUDENT_FIRST_NAME]", $user_name, $subject);
					$subject = str_replace("[SITE_NAME]", $site_name, $subject);
					$subject = str_replace("[QUIZ_NAME]", $quize_name, $subject);
					$subject = str_replace("[LINK_TO_QUIZ_RESULT]", $link_to_quiz, $subject);
					$subject = str_replace("[FEEDBACK_FIRST_20_WORDS]", $first_20, $subject);

					$body = str_replace("[STUDENT_FIRST_NAME]", $user_name, $body);
					$body = str_replace("[SITE_NAME]", $site_name, $body);
					$body = str_replace("[QUIZ_NAME]", $quize_name, $body);
					$body = str_replace("[LINK_TO_QUIZ_RESULT]", $link_to_quiz, $body);
					$body = str_replace("[FEEDBACK_FIRST_20_WORDS]", $first_20, $body);

					$send_student_email_modified_feedback = isset($template_emails["send_student_email_modified_feedback"]) ? $template_emails["send_student_email_modified_feedback"] : 1;

					if ($send_student_email_modified_feedback) {
						JFactory::getMailer()->sendMail($fromemail, $fromname, $student_email, $subject, $body, 1);
					}

					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->clear();
					$query->insert('#__guru_logs');
					$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
					$query->values(intval($user_id) . ',' . $db->quote('change-feedback-quiz') . ',' . '0' . ',' . $db->quote(trim($student_email)) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($body)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		//--------------------------------------------------------------

		return TRUE;
	}

	function getForMark()
	{
		$db = JFactory::getDbo();

		$and = "";
		$items = array();
		$filter_search = JFactory::getApplication()->input->get("filter_search", "", "raw");
		$filter_quiz = JFactory::getApplication()->input->get("filter_quiz", "0", "raw");
		$filter_quiz_type = JFactory::getApplication()->input->get("filter_quiz_type", "", "raw");

		if (trim($filter_search) != "") {
			$and .= " and (c.firstname like '%" . addslashes(trim($filter_search)) . "%' OR c.lastname like '%" . addslashes(trim($filter_search)) . "%' OR qq.name like '%" . addslashes(trim($filter_search)) . "%')";
		}

		if (intval($filter_quiz) != 0) {
			$and .= " and q.quiz_id=" . intval($filter_quiz);
		}

		if ($filter_quiz_type != "" && $filter_quiz_type != -1) {
			$and .= " and qq.final_quiz=" . intval($filter_quiz_type);
		}

		$course_id = JFactory::getApplication()->input->get("id", "0", "raw");
		$sql = "select q.question_ids, q.quiz_id, q.user_id, u.email, c.firstname, c.lastname, c.image, qq.name as quiz_name, qq.final_quiz from #__guru_quiz_question_taken_v3 q, #__guru_customer c, #__users u, #__guru_quiz qq where q.pid=" . intval($course_id) . " and u.id=c.id and u.id=q.user_id and qq.id=q.quiz_id" . $and;
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		if (isset($result) && count($result) > 0) {
			foreach ($result as $key => $value) {
				if (trim($value["question_ids"]) != "") {
					$sql = "select q.id from #__guru_questions_v3 q where q.id in (" . $value["question_ids"] . ") and q.type='essay'";
					$db->setQuery($sql);
					$essay = $db->loadColumn();

					if (isset($essay) && count($essay) > 0) {
						foreach ($essay as $key_essay => $value_essay) {
							$sql = "select count(*) from #__guru_quiz_essay_mark where question_id=" . intval($value_essay) . " and user_id=" . $value["user_id"];
							$db->setQuery($sql);
							$marked = $db->loadColumn();
							$marked = @$marked["0"];

							if (intval($marked) == 0) {
								$items[$value["quiz_id"] . "-" . $value["user_id"]] = $value;
							}
						}
					}
				}
			}
		}

		return $items;
	}

	function getAuthorNrCourses($author)
	{
		$db = JFactory::getDbo();
		$author_id = $author->userid;

		$sql = "select count(*) from #__guru_program where (author=" . intval($author_id) . " OR author like '%|" . intval($author_id) . "|%') and status='1' and startpublish <= now() and (endpublish='0000-00-00 00:00:00' OR endpublish >= now())";
		$db->setQuery($sql);
		$count = $db->loadColumn();
		$count = @$count["0"];

		return $count;
	}

	function saveMark()
	{
		$db = JFactory::getDbo();
		$user_logged = JFactory::getUser();

		$user_id = JFactory::getApplication()->input->get("user_id", "0", "raw");
		$question_id = JFactory::getApplication()->input->get("question_id", "0", "raw");
		$quiz_id = JFactory::getApplication()->input->get("quiz_id", "0", "raw");
		$teacher_answer = JFactory::getApplication()->input->get("teacher_answer", "", "raw");
		$grade = JFactory::getApplication()->input->get("grade", "0", "raw");
		$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");

		$sql = "select count(*) from #__guru_quiz_essay_mark where question_id=" . intval($question_id) . " and user_id=" . intval($user_id);
		$db->setQuery($sql);
		$count = $db->loadResult();

		$sql = "";

		if (intval($count) > 0) {
			$sql = "update #__guru_quiz_essay_mark set grade=" . intval($grade) . ", feedback_quiz_results='" . $db->escape(trim($teacher_answer)) . "' where question_id=" . intval($question_id) . " and user_id=" . intval($user_id);
		} else {
			$sql = "insert into #__guru_quiz_essay_mark (question_id, user_id, grade, feedback, feedback_quiz_results, date) values (" . intval($question_id) . ", " . intval($user_id) . ", " . intval($grade) . ", '', '" . $db->escape(trim($teacher_answer)) . "', '" . date("Y-m-d") . "')";
		}

		$db->setQuery($sql);
		if (!$db->execute()) {
			echo '0';
		} else {
			$all_essays_has_answers = true;

			$sql = "SELECT question_ids FROM `#__guru_quiz_question_taken_v3` WHERE user_id=" . intval($user_id) . " AND quiz_id=" . intval($quiz_id) . " ORDER BY id desc limit 0,1";
			$db->setQuery($sql);
			$user_questions = $db->loadResult();

			if (!isset($user_questions) || trim($user_questions) == "") {
				$user_questions = "0";
			}

			$sql = "select id from #__guru_questions_v3 where id in (" . $user_questions . ") and type='essay'";
			$db->setQuery($sql);
			$essays = $db->loadColumn();

			if (isset($essays) && count($essays) > 0) {
				foreach ($essays as $essay_id) {
					$sql = "select count(*) from #__guru_quiz_essay_mark where question_id=" . intval($essay_id) . " and user_id=" . intval($user_id);
					$db->setQuery($sql);
					$count = $db->loadResult();

					if (intval($count) == 0) {
						$all_essays_has_answers = false;
					}
				}
			}

			if ($all_essays_has_answers) {
				/* start update quiz score */
				if (!isset($essays) || count($essays) == 0 || intval($essays["0"]) == 0) {
					$essays = array("0");
				}

				$total_points = 0;
				$sql = "select question_id, grade from #__guru_quiz_essay_mark where question_id in (" . implode(",", $essays) . ") and user_id=" . intval($user_id);
				$db->setQuery($sql);
				$all_grades = $db->loadAssocList();

				$sql = "select max(id) from #__guru_quiz_question_taken_v3 where user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and pid=" . intval($course_id);
				$db->setQuery($sql);
				$answer_id = $db->loadResult();

				if (isset($all_grades) && count($all_grades) > 0) {
					foreach ($all_grades as $key => $all_grade) {
						$total_points += intval($all_grade["grade"]);
					}
				}

				$sql = "select points, question_ids from #__guru_quiz_question_taken_v3 where id=" . intval($answer_id);
				$db->setQuery($sql);
				$result = $db->loadAssocList();

				$calculated_points = @$result["0"]["points"];
				$question_ids = @$result["0"]["question_ids"];

				if (!isset($question_ids) || trim($question_ids) == "") {
					$question_ids = "0";
				}

				$sql = "select sum(points) from #__guru_questions_v3 where id in (" . $question_ids . ")";
				$db->setQuery($sql);
				$total_quiz_points = $db->loadResult();

				@$current_score = (($calculated_points + $total_points) * 100) / $total_quiz_points;
				$current_score = number_format((float)$current_score, 2, '.', '');
				$simple_current_score = $current_score;
				$simple_current_score = str_replace(".00", "", $simple_current_score);

				$sql = "update #__guru_quiz_question_taken_v3 set score_quiz='" . $current_score . "' where id=" . intval($answer_id);
				$db->setQuery($sql);
				$db->execute();
				/* stop update quiz score */

				$sql = "select email from #__users where id=" . intval($user_id);
				$db->setQuery($sql);
				$student_email = $db->loadResult();

				$sql = "select firstname from #__guru_customer where id=" . intval($user_id);
				$db->setQuery($sql);
				$firstname = $db->loadResult();

				$sql = "select name from #__guru_quiz where id=" . intval($quiz_id);
				$db->setQuery($sql);
				$quiz_name = $db->loadResult();

				$sql = "select u.name from #__users u, #__guru_quiz q where q.id=" . intval($quiz_id) . " and u.id=q.author";
				$db->setQuery($sql);
				$teacher_name = $db->loadResult();

				//-------------------------------------------------- quiz change
				$sql = "select `author` from #__guru_quiz where `id` = " . intval($quiz_id);
				$db->setQuery($sql);
				$quiz_author_database = $db->loadResult();

				if (intval($quiz_author_database) != intval($user_logged->id)) {
					$teacher_name = $user_logged->name;
				}
				//--------------------------------------------------

				$sql = "select template_emails, fromname, fromemail,admin_email from #__guru_config";
				$db->setQuery($sql);
				$confic = $db->loadAssocList();
				$template_emails = $confic["0"]["template_emails"];
				$template_emails = json_decode($template_emails, true);

				$subject = $template_emails["feedback_subject"];
				$message = $template_emails["feedback_body"];

				$subject = str_replace("[QUIZ_NAME]", $quiz_name, $subject);
				$subject = str_replace("[STUDENT_FIRST_NAME]", $firstname, $subject);
				$subject = str_replace("[TEACHER_FULL_NAME]", $teacher_name, $subject);
				$subject = str_replace("[TOTAL_QUIZ_SCORE]", $simple_current_score . "%", $subject);

				$message = str_replace("[QUIZ_NAME]", $quiz_name, $message);
				$message = str_replace("[STUDENT_FIRST_NAME]", $firstname, $message);
				$message = str_replace("[TEACHER_FULL_NAME]", $teacher_name, $message);
				$message = str_replace("[TOTAL_QUIZ_SCORE]", $simple_current_score . "%", $message);

				preg_match_all('/\[loop\](.*)\[end of loop\]/msU', $message, $matches);
				$temp_message = "";

				if (isset($matches["1"])) {
					$temp_message = $matches["1"]["0"];
				}

				if (isset($essays) && count($essays) > 0) {
					$temp_message2 = array();

					foreach ($essays as $key => $essay_id) {
						$sql = "select q.id, m.user_id, q.question_content, m.grade, m.feedback_quiz_results from #__guru_questions_v3 q, #__guru_quiz_essay_mark m where q.id = m.question_id and m.question_id=" . intval($essay_id) . " and m.user_id=" . intval($user_id) . " GROUP BY q.id, m.user_id, q.question_content, m.grade, m.feedback_quiz_results";
						$db->setQuery($sql);
						$answer = $db->loadAssocList();

						if (isset($answer) && count($answer) > 0) {
							$temp = $temp_message;
							$temp = str_replace("[ESSAY_QUESTION_TITLE]", strip_tags($answer["0"]["question_content"]), $temp);
							$temp = str_replace("[SCORE]", $answer["0"]["grade"], $temp);
							$temp = str_replace("[TEACHER_FULL_NAME]", $teacher_name, $temp);
							$temp = str_replace("[FEEDBACK_CONTENT]", $answer["0"]["feedback_quiz_results"], $temp);

							$temp_message2[] = $temp;
						}
					}

					$message = preg_replace('/\[loop\](.*)\[end of loop\]/msU', "<br />" . implode("<br />", $temp_message2) . "<br />", $message);
				}

				$configs = JFactory::getConfig();
				JFactory::getMailer()->sendMail($configs->get('mailfrom'), $configs->get('fromname'), $student_email, $subject, $message, 1);

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->clear();
				$query->insert('#__guru_logs');
				$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
				$query->values(intval($user_logged->id) . ',' . $db->quote('email-essay-graded') . ',' . '0' . ',' . $db->quote(trim($student_email)) . ',' . $db->quote(trim($subject)) . ',' . $db->quote(trim($message)) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
				$db->setQuery($query);
				$db->execute();

				require_once JPATH_ROOT . '/components/com_guru/models/gurutask.php';
				$taskModel = new guruModelguruTask;
				$taskModel->completeCourse($course_id, $user_id);
			}

			echo '1';
		}

		die();
	}

	function ajaxSearchMedia()
	{
		$db = JFactory::getDbo();
		$text = JFactory::getApplication()->input->get("text", "", "raw");
		$answer_id = JFactory::getApplication()->input->get("answer_id", "-1", "raw");
		$user = JFactory::getUser();
		$list = "";

		if (trim($text) != "") {
			$sql = "select id, name from #__guru_media where name like '%" . $db->escape(trim($text)) . "%' and type <> 'quiz' and author=" . intval($user->id);
			$db->setQuery($sql);
			$media = $db->loadAssocList();

			if (isset($media) && count($media) > 0) {
				$list = '<table style="border:0px">';

				foreach ($media as $key => $value) {
					$list .= '<tr>';
					$list .= 	'<td style="border:0px">';

					if (intval($answer_id) == -1) {
						$list .= 	'<a href="#" onclick="javascript:selectMediaFromList(\'' . intval($value["id"]) . '\', \'' . addslashes($value["name"]) . '\'); return false;">' . $value["name"] . "</a>";
					} else {
						$list .= 	'<a href="#" onclick="javascript:selectMediaFromListForAnswers(\'' . intval($value["id"]) . '\', \'' . addslashes($value["name"]) . '\', \'' . intval($answer_id) . '\'); return false;">' . $value["name"] . "</a>";
					}


					$list .= 	'</td>';
					$list .= '</tr>';
				}

				$list .= '</table>';
			} else {
				$list  = '<table>';
				$list .= 	'<tr>';
				$list .= 		'<td>';
				$list .= 			JText::_("GURU_NO_MATCHING");
				$list .= 		'</td>';
				$list .= 	'</tr>';
				$list .= '</table>';
			}
		}

		echo $list;
		die();
	}

	function getStudentCourses()
	{
		$db = JFactory::getDbo();
		$user_id = JFactory::getApplication()->input->get("id", "0", "raw");

		$sql = "select o.*, bc.*, s.name as plan_name, p.name as course_name, p.id as course_id, p.certificate_term as certerm, p.hasquiz, p.avg_certc, p.id_final_exam from #__guru_order o, #__guru_buy_courses bc left outer join #__guru_subplan s on bc.plan_id=s.id, #__guru_program p where o.status='Paid' and o.id=bc.order_id and bc.userid=" . intval($user_id) . " and bc.course_id=p.id and p.published=1 and o.userid=bc.userid ORDER BY o.id DESC";

		$db->setQuery($sql);
		$result = $db->loadObjectList();

		return $result;
	}

	function projectCourse($project_id)
	{
		$db = JFactory::getDbo();

		$sql = "select c.`name` from #__guru_program c, #__guru_projects p where c.`id`=p.`course_id` and p.`id`=" . intval($project_id);
		$db->setQuery($sql);
		$course_name = $db->loadColumn();
		$course_name = @$course_name["0"];

		return $course_name;
	}

	function scormCourse($scorm_id)
	{
		$db = JFactory::getDbo();

		$sql = "select c.`name` from #__guru_program c, #__guru_scorm_items p where c.`id`=p.`course_id` and p.`id`=" . intval($scorm_id);
		$db->setQuery($sql);
		$course_name = $db->loadColumn();
		$course_name = @$course_name["0"];

		return $course_name;
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

	function getStudentCertificates($user_id, $type)
	{
		$db = JFactory::getDbo();
		$author = JFactory::getUser();

		$sql = "select o.*, bc.*, s.name as plan_name, p.name as course_name, p.id as course_id , p.certificate_term as certerm from #__guru_order o, #__guru_buy_courses bc left outer join #__guru_subplan s on bc.plan_id=s.id, #__guru_program p where o.status='Paid' and o.id=bc.order_id and bc.userid=" . intval($user_id) . " and bc.course_id=p.id and p.published=1 and o.userid=bc.userid and (p.`author`='" . intval($author->id) . "' OR p.`author` like '%|" . intval($author->id) . "|%') ORDER BY o.id DESC";

		$db->setQuery($sql);
		$my_courses = $db->loadAssocList();

		if ($type == "sum") {
			$sum_certificates = 0;
			$k = 0;
			$hascertificate = false;
			$already_edited = array();
			$db		= JFactory::getDBO();
			$datetype = "SELECT datetype from #__guru_config WHERE id=1";
			$db->setQuery($datetype);
			$datetype = $db->loadResult();

			$s = 0;
			$n = count($my_courses);
			$scores_avg_quizzes = 0;

			foreach ($my_courses as $key => $course) {
				$id = $my_courses[$key]["course_id"];
				$avg_quizzes_cert = "SELECT avg_certc FROM #__guru_program WHERE id=" . intval($id);
				$db->setQuery($avg_quizzes_cert);
				$avg_quizzes_cert = $db->loadResult();

				$sql = "SELECT hasquiz from #__guru_program WHERE id=" . intval($id);
				$db->setQuery($sql);
				$resulthasq = $db->loadResult();

				// start calculate sum for all quizes from course------------------------------------
				$sql = "select mr.`media_id` from #__guru_mediarel mr, #__guru_days d where mr.`type`='dtask' and mr.`type_id`=d.`id` and d.`pid`=" . intval($course["course_id"]);
				$db->setQuery($sql);
				$lessons = $db->loadColumn();

				if (!isset($lessons) || count($lessons) == 0) {
					$lessons = array("0");
				}

				$sql = "select mr.`media_id` from #__guru_mediarel mr where mr.`layout`='12' and mr.`type`='scr_m' and mr.`type_id` in (" . implode(", ", $lessons) . ")";
				$db->setQuery($sql);
				$all_quizzes = $db->loadColumn();

				$s = 0;
				$res = 0;

				if (isset($all_quizzes) && count($all_quizzes) > 0) {
					foreach ($all_quizzes as $key_quiz => $quiz_id) {
						$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and pid=" . intval($id) . " ORDER BY id DESC LIMIT 0,1";
						$db->setQuery($sql);
						$result_q = $db->loadColumn();
						$res = @$result_q["0"];
						$s += $res;

						$sql = "SELECT `failed` FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($quiz_id) . " and pid=" . intval($id) . " ORDER BY id DESC LIMIT 0,1";
						$db->setQuery($sql);
						$failed = $db->loadColumn();
						$failed = @$failed["0"];
					}
				}
				// stop calculate sum for all quizes from course------------------------------------

				$nb_ofscores = 0;

				if (is_array($all_quizzes) && count($all_quizzes) > 0) {
					$nb_ofscores = count($all_quizzes);
				}

				if ($nb_ofscores != 0) {
					$scores_avg_quizzes = intval($s / $nb_ofscores);
				}

				$certterm = $my_courses[$key]["certerm"];

				if (!in_array($id, $already_edited)) {
					$already_edited[] = $id;

					$sql = "SELECT id_final_exam FROM #__guru_program WHERE id=" . intval($id);
					$db->setQuery($sql);
					$result = $db->loadResult();
					$id_final_exam = $result;

					$sql = "SELECT max_score FROM #__guru_quiz WHERE id=" . intval($result);
					$db->setQuery($sql);
					$result_maxs = $db->loadResult();

					$sql = "SELECT course_id FROM #__guru_mycertificates WHERE user_id = " . intval($user_id);
					$db->setQuery($sql);
					$certcourseidlist = $db->loadColumn();

					$sql = "SELECT completed from #__guru_viewed_lesson WHERE user_id =" . intval($user_id) . " and pid=" . intval($id);
					$db->setQuery($sql);
					$result = $db->loadColumn();
					$result = @$result["0"];

					$completed_course = $result == 1 ? true : false;

					if ($certterm == 1 || $certterm == 0) {
						$hascertficate = false;
					}

					if ($certterm == 2) {
						if ($completed_course == true) {
							$hascertficate = true;
						} else {
							$hascertficate = false;
						}
					} elseif ($certterm == 3) {
						if ($res >= $result_maxs) {
							$hascertficate = true;
						} else {
							$hascertficate = false;
						}
					} elseif ($certterm == 4) {
						if ($scores_avg_quizzes >= intval($avg_quizzes_cert)) {
							$hascertficate = true;
						} else {
							$hascertficate = false;
						}
					} elseif ($certterm == 5) {
						$res_final_exam = 0;

						if (intval($id_final_exam) > 0) {
							$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($id_final_exam) . " and pid=" . intval($id) . " ORDER BY id DESC LIMIT 0,1";
							$db->setQuery($sql);
							$result_q = $db->loadColumn();

							$res_final_exam = @$result_q["0"];
						}

						if ($completed_course == true && isset($result_maxs) && $res_final_exam >= intval($result_maxs)) {
							$hascertficate = true;
						} else {
							$hascertficate = false;
						}
					} elseif ($certterm == 6) {
						if ($completed_course == true && isset($scores_avg_quizzes) && ($scores_avg_quizzes >= intval($avg_quizzes_cert))) {
							$hascertficate = true;
						} else {
							$hascertficate = false;
						}
					} elseif ($certterm == 7) {
						if ($completed_course == true) {
							$hascertficate = true;
						} else {
							$hascertficate = false;
						}
					}

					if ($hascertficate && !in_array($id_final_exam, $certcourseidlist)) {
						$db = JFactory::getDbo();

						$sql = "select `author` from #__guru_program where `id`=" . intval($course["course_id"]);
						$db->setQuery($sql);
						$course_author = $db->loadColumn();
						$course_author = @$course_author["0"];
						$course_author = explode("|", $course_author);
						$certificate_course_author = 0;

						foreach ($course_author as $key_author => $value_author) {
							if (intval($value_author) != 0) {
								$certificate_course_author = intval($value_author);
								break;
							}
						}

						$joomla_user = JFactory::getUser();
						$jnow = new JDate('now');
						$current_date_cert = $jnow->toSQL(true);

						$certcourseidlist[] = $id_final_exam;
						$certcourseidlist[] = $id;
					}

					if (in_array($id_final_exam, $certcourseidlist) && ($hascertficate == true || $hascertficate == 1)) {
						$sum_certificates++;
					}
				}
			}

			return intval($sum_certificates);
		} elseif ($type == "html") {
		}
	}

	function getStudentCertificateCourses()
	{
		$db = JFactory::getDbo();
		$user_id = JFactory::getApplication()->input->get("userid", "0", "raw");
		$author = JFactory::getUser();

		$sql = "select o.*, bc.*, s.name as plan_name, p.name as course_name, p.id as course_id , p.certificate_term as certerm from #__guru_order o, #__guru_buy_courses bc left outer join #__guru_subplan s on bc.plan_id=s.id, #__guru_program p where o.status='Paid' and o.id=bc.order_id and bc.userid=" . intval($user_id) . " and bc.course_id=p.id and p.published=1 and o.userid=bc.userid and (p.`author`='" . intval($author->id) . "' OR p.`author` like '%|" . intval($author->id) . "|%') ORDER BY o.id DESC";

		$db->setQuery($sql);
		$result = $db->loadAssocList();

		return $result;
	}

	function getConfigSettings()
	{
		$db = JFactory::getDbo();

		$sql = "SELECT * FROM #__guru_config WHERE id=1";
		$db->setQuery($sql);
		$result = $db->loadObject();

		return $result;
	}
}
