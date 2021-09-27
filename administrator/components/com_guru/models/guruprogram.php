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

jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');


class guruAdminModelguruProgram extends  JModelLegacy
{
	var $_attributes;
	var $_attribute;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;
	protected $_context = 'com_guru.guruPrograms';

	function __construct()
	{
		parent::__construct();
		$cids = JFactory::getApplication()->input->get('cid', 0, "raw");
		$this->setId((int)$cids[0]);
		$mainframe = JFactory::getApplication();
		global $option;
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');

		if (JFactory::getApplication()->input->get("limitstart") == JFactory::getApplication()->input->get("old_limit")) {
			JFactory::getApplication()->input->set("limitstart", "0");
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');
		}

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getItems();
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}

	function orderFile()
	{
		$cids = JFactory::getApplication()->input->get('cid', array(), "array");
		$cid = $cids["0"];
		$task = JFactory::getApplication()->input->get("task", "");
		$order = JFactory::getApplication()->input->get("order", array());
		$id = JFactory::getApplication()->input->get("id", "0");
		$db = JFactory::getDBO();

		if ($task == "orderupfile") {
			$old_order = $order[$cid];
			if ($old_order == 0) {
				$old_order = 0;
			} else {
				$old_order--;
			}
			$sql = "update #__guru_mediarel set order=" . intval($old_order) . " where type='pmed' and type_id=" . intval($id) . " and media_id=" . intval($cid);
			$db->setQuery($sql);
			if ($db->execute()) {
				return true;
			}
		} elseif ($task == "orderdownfile") {
			$old_order = $order[$cid];
			$old_order++;

			$sql = "update #__guru_mediarel set order=" . intval($old_order) . " where type='pmed' and type_id=" . intval($id) . " and media_id=" . intval($cid);
			$db->setQuery($sql);
			if ($db->execute()) {
				return true;
			}
		}
	}

	function saveorderFile()
	{
		$cids = JFactory::getApplication()->input->get('cid', array(), "raw");
		$task = JFactory::getApplication()->input->get("task", "");
		$order = JFactory::getApplication()->input->get("order", array(), "raw");
		$id = JFactory::getApplication()->input->get("id", "0", "raw");
		$db = JFactory::getDBO();

		if (isset($cids) && count($cids) > 0) {
			foreach ($cids as $key => $value) {
				$sql = "update #__guru_mediarel set `order`=" . intval($order[$key]) . " where `type`='pmed' and `type_id`=" . intval($id) . " and `media_id`=" . intval($value);
				$db->setQuery($sql);
				$db->execute();
			}
		}
		return true;
	}

	function getAllReminds()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_subremind ORDER BY ordering ASC, id DESC ";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}

	protected function getListQuery()
	{
		$task = JFactory::getApplication()->input->get("task", "", "raw");
		$pid = JFactory::getApplication()->input->getInt("pid", "0");
		$search = JFactory::getApplication()->input->get("search", "", "raw");
		$and = "";

		if ($task == "show") {
			$db = JFactory::getDBO();
			if (trim($search) != "") {
				$and = " and (u.username like '%" . addslashes(trim($search)) . "%' or c.firstname like '%" . addslashes(trim($search)) . "%' or c.lastname like '%" . addslashes(trim($search)) . "%')";
			}
			$sql = "SELECT DISTINCT u.id, u.`username`, CONCAT(c.firstname, ' ', c.lastname) AS name
				FROM `#__users` u
				INNER JOIN `#__guru_customer` c ON c.id = u.id
				INNER JOIN `#__guru_buy_courses` bc ON bc.userid = u.id
				INNER JOIN `#__guru_order` o ON bc.order_id = o.id
				WHERE 1 = 1
				AND bc.course_id = $pid
				$and
				AND o.`status` = 'Paid'
				ORDER BY u.id DESC ";

			return $sql;
		} else {
			if (trim($search) != "") {
				$and = " and (u.username like '%" . addslashes(trim($search)) . "%' or c.firstname like '%" . addslashes(trim($search)) . "%' or c.lastname like '%" . addslashes(trim($search)) . "%')";
			}
			$db = JFactory::getDBO();
			$sql = "select u.id, u.username, c.firstname, c.lastname from #__users u, #__guru_customer c where c.id=u.id " . $and . " order by c.id desc";
			return $sql;
		}
	}

	function orderUp()
	{
		$db = JFactory::getDBO();
		$ids = JFactory::getApplication()->input->get("cid", "", "raw");
		$table = $this->getTable("guruPrograms");
		$table->load($ids["0"]);
		if (!$table->move(-1)) {
			return false;
		}
		return true;
	}

	function orderDown()
	{
		$db = JFactory::getDBO();
		$ids = JFactory::getApplication()->input->get("cid", "", "raw");
		$table = $this->getTable("guruPrograms");
		$table->load($ids["0"]);
		if (!$table->move(1)) {
			return false;
		}
		return true;
	}

	function saveOrder()
	{
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$cid = array_values($cids);
		$order = JFactory::getApplication()->input->get('order', array(0));
		$order = array_values($order);
		$total = count($cid);

		for ($i = 0; $i < $total; $i++) {
			$sql = "update #__guru_program set ordering=" . $order[$i] . " where id=" . $cid[$i];
			$db->setQuery($sql);
			if (!$db->execute()) {
				return false;
			}
		}
		return true;
	}

	function getFilters()
	{
		$app = JFactory::getApplication('administrator');
		$filter = (object)array();
		$filter_search = $app->getUserStateFromRequest('search', 'search', '');
		$filter->search = $filter_search;

		return $filter;
	}

	function setId($id)
	{
		$this->_id = $id;
		$this->_attribute = null;
	}


	function getlistPrograms()
	{
		$tmpl = JFactory::getApplication()->input->get("tmpl", "");
		if (trim($tmpl) != "") {
			JFactory::getApplication()->input->set("tmpl", "component");
		}

		$db			= JFactory::getDBO();

		$app = JFactory::getApplication('administrator');

		$limitstart	= $app->getUserStateFromRequest('limitstart', 'limitstart', '0');
		$limit		= $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'));
		$lock		= $app->getUserStateFromRequest('course_lock_status', 'course_lock_status', 'YN');
		$course_publ_status = $app->getUserStateFromRequest('course_publ_status', 'course_publ_status', 'YN');
		$catid		= JFactory::getApplication()->input->get("catid", "-1");
		$search		= JFactory::getApplication()->input->get("search_text", "", "raw");
		$condition	= array();

		if ($limit != 0) {
			$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
		} else $limit_cond = "";



		// Get only published courses when selecting courses in setting orders 
		if (trim($tmpl) == 'component') {
			$condition[] = " published='1' ";
		} else {
			if ($course_publ_status != "YN") {
				if ($course_publ_status == "Y") {
					$condition[] = " published='1' ";
				} else if ($course_publ_status == "N") {
					$condition[] = " published='0' ";
				}
			}
		}

		//echo'<pre>';print_r(JFactory::getApplication()->input->get('tmpl', ""));echo'</pre>';die;

		if ($catid != -1) {
			$condition[] = " catid=" . $catid . " ";
		}

		if (trim($search) != "") {
			$condition[] = " (name LIKE '%" . $search . "%' OR description LIKE '%" . $search . "%')";
		}
		if (!empty($condition)) {
			$condition = " AND " . implode(" AND ", $condition);
		} else {
			$condition = "";
		}

		$sql = "SELECT * FROM #__guru_program WHERE 1=1 " . $condition . " ORDER BY `ordering` ASC";
		$this->total = $this->_getListCount($sql);

		$sql = "SELECT * FROM #__guru_program WHERE 1=1 " . $condition . " ORDER BY `ordering` ASC";
		return $sql;
	}

	function getProgram()
	{
		$db = JFactory::getDbo();

		if (empty($this->_attribute)) {
			$this->_attribute = $this->getTable("guruPrograms");
			$this->_attribute->load($this->_id);
		}

		$catid = $this->_attribute->catid;
		$data = JFactory::getApplication()->input->post->getArray();


		if (!$this->_attribute->bind($data)) {
			$this->setError($item->getError());
			return false;
		}

		if (!$this->_attribute->check()) {
			$this->setError($item->getError());
			return false;
		}

		$this->_attribute->catid = $catid;


		//start get author list
		$sql = "SELECT u.id, u.name, a.commission_id FROM #__users u, #__guru_authors a where u.id=a.userid";
		$db->setQuery($sql);
		$author_list = $db->loadObjectList();

		if (!is_array($this->_attribute->author)) {
			$this->_attribute->author = explode("|", $this->_attribute->author);
		}

		$list_authors = '
			<div style="border: 1px solid #ccc; float: left; margin-right: 3px; max-height: 100px; overflow-y: scroll; padding: 5px; width: 209px;">
				<ul style="list-style: none; margin:0px;">';

		if (isset($author_list) && count($author_list) > 0) {
			foreach ($author_list as $key => $author) {
				if (intval($author->id) != 0) {
					$checked = '';

					if (in_array(intval($author->id), $this->_attribute->author)) {
						$checked = 'checked="checked"';
					}

					$list_authors .= '<li>
										<input style="opacity: 1; position: inherit;" type="checkbox" ' . $checked . ' name="author[]" value="' . intval($author->id) . '">
									 ' . $author->name . '
									 	<input type="hidden" id="commission-' . intval($author->id) . '" value="' . intval($author->commission_id) . '" />
									  </li>';
				}
			}
		}

		$list_authors .= '
				</ul>
			</div>';

		$this->_attribute->lists['author'] = $list_authors;

		//$this->_attribute->lists['author'] = JHTML::_("select.genericlist", $author_list, "author[]", "multiple", "text", "value", $this->_attribute->author);

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

		$this->_attribute->lists['level'] = JHTML::_('select.genericlist', $level_list, 'level', '', 'value', 'text', $this->_attribute->level);

		$sql = "SELECT * from #__guru_certificates where `course_id`=" . intval($this->_id);
		$db->setQuery($sql);
		$certificates_details = $db->loadObject();
		$this->_attribute->certificates_details = $certificates_details;

		return $this->_attribute;
	}

	public static function getProgramCategory($catid)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT id,name FROM #__guru_category WHERE id='" . $catid . "' ";
		$database->setQuery($sql);
		$name = $database->loadObject();
		return $name;
	}

	public static function getpdays($pid)
	{
		// we find out how many Days has a given program
		$database = JFactory::getDBO();
		$sql = "SELECT count(id) as how_many FROM #__guru_days WHERE pid='" . $pid . "' ";
		$database->setQuery($sql);
		$rows = $database->loadObject();
		return $rows;
	}

	function getProgramPlans($id = 0)
	{
		$data = JFactory::getApplication()->input->get->getArray();
		$db = JFactory::getDBO();
		$id_post = JFactory::getApplication()->input->get('cid', "", "raw");

		if (($id == 0) && (isset($id_post))) {
			@$id = (int) $id_post[0];
		}

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
		$id_post = JFactory::getApplication()->input->get('cid', "", "raw");
		if (($id == 0) && (isset($id_post))) {
			@$id = (int) $id_post[0];
		}
		$sql = "SELECT emailreminder_id FROM #__guru_program_reminders
                   WHERE product_id=" . $id;
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}

	function getProgramRenewals($id = 0)
	{
		$data = JFactory::getApplication()->input->get->getArray();
		$db = JFactory::getDBO();
		$id_post = JFactory::getApplication()->input->get('cid', "", "raw");
		if (($id == 0) && (isset($id_post))) {
			@$id = (int) $id_post[0];
		}

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
	public static function getStudentsNumber($pid)
	{
		$pid = (int) $pid;
		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(DISTINCT u.id )
            FROM `#__users` u
			INNER JOIN `#__guru_customer` c ON c.id = u.id
            INNER JOIN `#__guru_buy_courses` bc ON bc.userid = u.id
            INNER JOIN `#__guru_order` o ON bc.order_id = o.id
            WHERE 1 = 1
            AND bc.course_id = $pid
            AND o.`status` = 'Paid'";

		return (int) $db->setQuery($sql)->loadResult();
	}

	function countQuizzTaken($id, $pid)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) from `#__guru_quiz_question_taken_v3` WHERE user_id =" . intval($id) . " and pid=" . $pid;
		return (int) $db->setQuery($sql)->loadResult();
	}

	function getCourseName($pid)
	{
		$database = JFactory::getDBO();
		$sql = "SELECT name FROM #__guru_program WHERE id=" . $pid;
		$database->setQuery($sql);
		$course_name = $database->loadResult();
		return $course_name;
	}


	function store()
	{
		require_once(JPATH_BASE . '/../components/com_guru/helpers/helper.php');
		$guruHelper = new guruHelper();

		$item = $this->getTable('guruPrograms');
		$data = JFactory::getApplication()->input->post->getArray();

		if (trim($data["id"]) == "") {
			$data["id"] = 0;
		}

		if (trim($data["endpublish"]) == "") {
			$data["endpublish"] = "0000-00-00 00:00:00";
		}

		$pre_req = JFactory::getApplication()->input->get("pre_req", "", "raw");
		$pre_req_books = JFactory::getApplication()->input->get("pre_req_books", "", "raw");
		$reqmts = JFactory::getApplication()->input->get("reqmts", "", "raw");
		$mail_purchase_subject = JFactory::getApplication()->input->get("mail_purchase_subject", "", "raw");
		$mail_purchase_template = JFactory::getApplication()->input->get("mail_purchase_template", "", "raw");

		$data["pre_req"] = $pre_req;
		$data["pre_req_books"] = $pre_req_books;
		$data["reqmts"] = $reqmts;
		$data["mail_purchase_subject"] = $mail_purchase_subject;
		$data["mail_purchase_template"] = $mail_purchase_template;

		$database = JFactory::getDbo();
		$db = JFactory::getDbo();
		$data['startpublish'] = date('Y-m-d H:i:s', strtotime($data['startpublish']));

		if ($data['endpublish'] != 'Never' && $data['endpublish'] != '' && $data['endpublish'] != "0000-00-00 00:00:00") { // calendar change
			$data['endpublish'] = date('Y-m-d H:i:s', strtotime($data['endpublish']));
		}

		$data['image_avatar'] = $data["image_avatar"];

		$jnow = new JDate('now');
		$date2 = $jnow->toSQL();
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

		if ($data['alias'] == '') {
			$data['alias'] = JFilterOutput::stringURLSafe($data['name']);
		} else {
			$data['alias'] = JFilterOutput::stringURLSafe($data['alias']);
		}

		$final_quiz = "0";
		$sql = "select id_final_exam from #__guru_program where id=" . intval($data['id']);
		$db->setQuery($sql);
		$id_final_exam = $db->loadResult();
		if (isset($id_final_exam) && intval($id_final_exam) != 0) {
			$final_quiz = intval($id_final_exam);
		}

		if ($final_quiz != "0" && $data["final_quizzes"] == 0) {
			// delete final exam
			$progid = $data['id'];

			$sql = "SELECT id FROM #__guru_days WHERE pid='" . $progid . "' order by ordering desc limit 0,1";
			$db->setQuery($sql);
			$moduleid = $db->loadResult();

			$sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type='dtask' and mr.type_id=" . intval($moduleid) . " and mr.media_id=t.id order by t.ordering desc limit 0,1";
			$db->setQuery($sql);
			$lesson_id = $db->loadResult();

			$sql = "delete FROM #__guru_mediarel WHERE type = 'dtask' AND type_id = " . intval($moduleid) . " and media_id = " . intval($lesson_id);
			$db->setQuery($sql);
			$db->execute();

			$sql = "delete from #__guru_task where id=" . intval($lesson_id);
			$db->setQuery($sql);
			$db->execute();
		}

		if (isset($data["groups"])) {
			$data["groups_access"] = implode(",", $data["groups"]);
		} else {
			$data["groups_access"] = "";
		}

		$data["author"] = "|" . implode("|", $data["author"]) . "|";

		if (intval($data["id"]) == 0) {
			$sql = "select max(ordering) from #__guru_program";
			$db->setQuery($sql);
			$max = $db->loadResult();
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

		if (!isset($data["groups_acl"])) {
			$data["groups_acl"] = "";
		} else {
			$data["groups_acl"] = json_encode($data["groups_acl"]);
		}

		if (!$item->bind($data)) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}
		if (!$item->check()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		if (!$item->store()) {
			JFactory::getApplication()->enqueueMessage($item->getError(), 'error');
			return false;
		}

		if (isset($data["custom_certificate"]) && $data["custom_certificate"] == 1) {
			$library_pdf = JFactory::getApplication()->input->get("library_pdf", "", "raw");
			$certificate_sett = JFactory::getApplication()->input->get("certificate_sett", "", "raw");
			$image = JFactory::getApplication()->input->get("image_cert", "", "raw");
			$st_donecolor1 = JFactory::getApplication()->input->get("st_donecolor1", "", "raw");
			$st_donecolor2 = JFactory::getApplication()->input->get("st_donecolor2", "", "raw");
			$avg_cert = JFactory::getApplication()->input->get("avg_cert", "70", "raw");
			$certificate = JFactory::getApplication()->input->get("certificate", "", "raw");
			$certificate_page = JFactory::getApplication()->input->get("certificate_page", "", "raw");
			$email_template = JFactory::getApplication()->input->get("email_template", "", "raw");
			$email_mycertificate = JFactory::getApplication()->input->get("email_mycertificate", "", "raw");
			$subjectt3 = JFactory::getApplication()->input->get("subjectt3", "", "raw");
			$subjectt4 = JFactory::getApplication()->input->get("subjectt4", "", "raw");
			$font = JFactory::getApplication()->input->get("font", "", "raw");
			$library_pdf = JFactory::getApplication()->input->get("library_pdf", "", "raw");
			$certificatepdf = JFactory::getApplication()->input->get("certificatepdf", "", "raw");

			if (!file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_guru' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'MPDF') && $library_pdf == 1) {
				$app = JFactory::getApplication('administrator');
				$msg = JText::_('GURU_NO_MPDF_MSG');

				$app->enqueueMessage($msg, "error");
				$app->redirect('index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=' . intval($data['id']));
			}

			$sql = "select count(*) from #__guru_certificates where `course_id`=" . intval($data['id']);
			$db->setQuery($sql);
			$cert_exist = $db->loadResult();

			if (intval($cert_exist) > 0) {
				$sql = "UPDATE #__guru_certificates set `general_settings`='" . addslashes(trim($certificate_sett)) . "', `design_background`='" . addslashes(trim($image)) . "', `design_background_color`='" . addslashes(trim($st_donecolor1)) . "', `design_text_color`='" . addslashes(trim($st_donecolor2)) . "', `avg_cert`='" . addslashes(trim($avg_cert)) . "', `templates1`='" . addslashes(trim($certificate)) . "', `templates2`='" . addslashes(trim($certificate_page)) . "', `templates3`='" . addslashes(trim($email_template)) . "', `templates4`='" . addslashes(trim($email_mycertificate)) . "', `subjectt3`='" . addslashes(trim($subjectt3)) . "', `subjectt4`='" . addslashes(trim($subjectt4)) . "', `font_certificate`='" . addslashes(trim($font)) . "', `library_pdf`='" . addslashes(trim($library_pdf)) . "', `templatespdf`='" . addslashes($certificatepdf) . "' where `course_id`=" . intval($data['id']);
				$db->setQuery($sql);
				$db->execute();
			} else {
				$sql = "insert into #__guru_certificates (`general_settings`, `design_background`, `design_background_color`, `design_text_color`, `avg_cert`, `templates1`, `templates2`, `templates3`, `templates4`, `subjectt3`, `subjectt4`, `font_certificate`, `library_pdf`, `templatespdf`, `course_id`) values ('" . addslashes(trim($certificate_sett)) . "', '" . addslashes(trim($image)) . "', '" . addslashes(trim($st_donecolor1)) . "', '" . addslashes(trim($st_donecolor2)) . "', '" . addslashes(trim($avg_cert)) . "', '" . addslashes(trim($certificate)) . "', '" . addslashes(trim($certificate_page)) . "', '" . addslashes(trim($email_template)) . "', '" . addslashes(trim($email_mycertificate)) . "', '" . addslashes(trim($subjectt3)) . "', '" . addslashes(trim($subjectt4)) . "', '" . addslashes(trim($font)) . "', '" . addslashes(trim($library_pdf)) . "', '" . addslashes($certificatepdf) . "', " . intval($data['id']) . ")";
				$db->setQuery($sql);
				$db->execute();
			}

			/*echo "<pre>";
			print_r($data);
			die($sql);*/
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

		$sql = "select count(*) from #__extensions where element='com_kunena' and name='com_kunena'";
		$db->setQuery($sql);
		$count = $db->loadResult();

		if ($count > 0) {
			if (JComponentHelper::isEnabled('com_kunena', true)) {
				$db->setQuery("SELECT forumboardcourse,forumboardlesson FROM #__guru_kunena_forum WHERE id=1 ");
				$ressult = $db->loadAssocList();

				if (($data['id'] == 0 || $data['id'] == "") && $ressult[0]["forumboardcourse"] == 1) {
					$db->setQuery("SELECT `kunena_category` FROM #__guru_kunena_forum WHERE id=1");
					$kunena_category = $db->loadResult();

					if (intval($kunena_category) == 0) {
						$nameofmainforum = JText::_('GURU_TREECOURSE');
					} else {
						$sql = "SELECT `name` FROM #__kunena_categories WHERE id='" . intval($kunena_category) . "'";
						$db->setQuery($sql);
						$nameofmainforum = $db->loadResult();
					}

					$sql = "SELECT name FROM #__kunena_categories WHERE name='" . addslashes($nameofmainforum) . "'";
					$db->setQuery($sql);
					$result = $db->loadResult();

					if (!$result) {
						$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES (" . intval($kunena_category) . ", '" . $db->escape($nameofmainforum) . "', 'course', 0, 0, 'joomla.level', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
						$db->setQuery($sql);
						$db->execute($sql);
					}

					$sql = "SELECT id FROM #__kunena_categories WHERE name='" . $nameofmainforum . "'";
					$db->setQuery($sql);
					$idmainforum = $db->loadResult();

					$sql = "INSERT INTO #__kunena_categories ( parent_id, name, alias, icon_id, locked, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse, ordering, published, channels, checked_out, checked_out_time, review, allow_anonymous, post_anonymous, hits, description, headerdesc, class_sfx, allow_polls, topic_ordering, numTopics, numPosts, last_topic_id, last_post_id, last_post_time, params) VALUES ( '" . $idmainforum . "', '" . $db->escape($data['name']) . "', '" . $db->escape($data['alias']) . "', 0, 0, 'joomla.group', 1, 1, 1, 8, 1, 2, 1, NULL, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, '', '', '', 0, 'lastpost', 0, 0, 0, 0, 0, '{}')";
					$db->setQuery($sql);
					$db->execute($sql);
				}

				$sql = "SELECT id FROM #__kunena_categories WHERE parent_id='" . intval($idmainforum) . "' and name='" . addslashes($data['name']) . "'";
				$db->setQuery($sql);
				$resultid = $db->loadResult();

				if ($resultid) {
					if (trim($data['alias']) != "") {
						$sql = "select count(*) from #__kunena_aliases where `alias`='" . $db->escape($data['alias']) . "' and `item`=" . intval($resultid[0]);
						$db->setQuery($sql);
						$exist_alias = $db->loadResult();

						if (!$exist_alias) {
							$sql = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES (  '" . $data['alias'] . "', 'catid', '" . $resultid[0] . "', 0)";
							$db->setQuery($sql);
							$db->execute($sql);
						}
					}
				}

				if (intval($data['id']) == 0) {
					$ask = "SELECT id FROM #__guru_program ORDER BY id DESC LIMIT 1 ";
					$db->setQuery($ask);
					$data['id'] = $db->loadResult();
				}

				$sql = "INSERT INTO #__guru_kunena_courseslinkage (idcourse, coursename, catidkunena) VALUES (  '" . $data['id'] . "', '" . addslashes($data['name']) . "', '" . $resultid[0] . "')";
				$db->setQuery($sql);
				$db->execute($sql);
			}
		}

		if (intval($data['id']) == 0) {
			$ask = "SELECT id FROM #__guru_program ORDER BY id DESC LIMIT 1 ";
			$db->setQuery($ask);
			$data['id'] = $db->loadResult();
		}
		$progid = $data['id'];
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
			if (isset($data['mediafiletodel'])) {
				$id_tmp_med_files_2_remove = explode(',', $data['mediafiletodel']);
			} else {
				$id_tmp_med_files_2_remove = array();
			}

			$poz = 1;

			foreach ($thefiles as $files) {
				if ($files != "") {
					$array = (isset($data["order"])) ? $data["order"] : array();
					if (intval($files) > 0 && !in_array($files, $id_tmp_med_files_2_remove)) {
						$access = "access" . $files;
						$order = $poz++;

						if (isset($array[$files]) && intval($array[$files]) != 0) {
							$order = $array[$files];
						}

						$db = JFactory::getDbo();
						$sql = "insert into #__guru_mediarel(`type`, `type_id`, `media_id`, `mainmedia`, `access`, `order`) values ('pmed', '" . trim($progid) . "', '" . trim($files) . "', '0', '" . trim($data[$access]) . "', '" . trim($order) . "')";
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		} // end if

		if (isset($data['preqfiles'])) {
			//delete old records
			if ($data['id'] > 0) {
				$db->setQuery("DELETE FROM #__guru_mediarel WHERE type='preq' AND type_id='" . $data['id'] . "'");
				$db->execute();
			}
			//delete end

			if (intval($data['id']) == 0) {
				$ask = "SELECT id FROM #__guru_program ORDER BY id DESC LIMIT 1 ";
				$db->setQuery($ask);
				$data['id'] = $db->loadResult();
			}
			$progid = $data['id'];

			$thefiles = explode(',', $data['preqfiles']);

			/*$id_tmp_med_task_2_remove = array();
			if(isset($data['preqfiletodel']))
				$id_tmp_med_files_2_remove = explode(',', $data['preqfiletodel']);*/

			foreach ($thefiles as $files) {
				if (intval($files) > 0) {
					$db->setQuery("INSERT INTO #__guru_mediarel (`type`,`type_id`, `media_id`, `mainmedia`, `access`, `order`) VALUES ('preq','" . $progid . "','" . $files . "','0','0','0')");
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
				$registry->set('empltyprice', "1");

				return false;
			}
			$data['subscription_default'] == $element ? $default = '1' : $default = '0';

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->insert('#__guru_program_plans');
			$query->columns(array($db->quoteName('product_id'), $db->quoteName('plan_id'), $db->quoteName('price'), $db->quoteName('default')));
			$query->values(intval($progid) . ',' . $db->quote(trim($element)) . ',' . $db->quote(trim($data['subscription_price'][$element])) . ',' . $default);
			$db->setQuery($query);
			$db->execute();
		}
		// Subscriptions - END

		// Renewals
		$sql = "DELETE FROM #__guru_program_renewals WHERE product_id = '" . $progid . "' ";
		$db->setQuery($sql);
		$db->execute();

		if (isset($data['renewals'])) {
			foreach ($data['renewals'] as $element) {
				$data['renewal_default'] == $element ? $default = '1' : $default = '0';
				$data['renewal_price'][$element] = ($data['renewal_price'][$element] != '') ? $data['renewal_price'][$element] : 0;
				
				$data['renewal_price'][$element] = $guruHelper->savePrice($data['renewal_price'][$element]);
				
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->clear();
				$query->insert('#__guru_program_renewals');
				$query->columns(array($db->quoteName('product_id'), $db->quoteName('plan_id'), $db->quoteName('price'), $db->quoteName('default')));
				$query->values(intval($progid) . ',' . $db->quote(trim($element)) . ',' . $db->quote(trim($data['renewal_price'][$element])) . ',' . $default);
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
		$moduleid = $db->loadResult();

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
			$lesson_id = $db->loadResult();

			$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_m' AND type_id = " . intval($lesson_id) . " and layout = 12";
			$db->setQuery($sql);
			$media_id = $db->loadResult();
			$type = "quiz";

			if (intval($media_id) == 0) {
				$sql = "SELECT media_id FROM #__guru_mediarel WHERE type = 'scr_m' AND type_id = " . intval($lesson_id);
				$db->setQuery($sql);
				$media_id = $db->loadResult();
				$type = "";
			}

			$sql = "select ordering from #__guru_task where id=" . intval($lesson_id);
			$db->setQuery($sql);
			$max_ordering = $db->loadResult();

			$name_exam = JText::_("GURU_FINAL_EXAM") . $name_quiz;

			if ($type == 'quiz') {
				// check if is final quiz
				$sql = "select is_final from #__guru_quiz where id=" . intval($media_id);
				$db->setQuery($sql);
				$is_final = $db->loadResult();

				if ($is_final == 0) { // is not final quiz
					// add final quizl);
					$sql = "INSERT INTO #__guru_task (name, alias, category, difficultylevel, points, image, published, startpublish, endpublish, metatitle, metakwd, metadesc, time, ordering, step_access) VALUES ('" . addslashes(trim($name_exam)) . "', 'final-exam', NULL, 'hard', NULL, NULL, 1, now(), '0000-00-00 00:00:00', '', '', '', 0," . ($max_ordering + 1) . ", 0)";
					$db->setQuery($sql);
					$db->execute();

					$db->setQuery("SELECT max(id) FROM #__guru_task");
					$max_id = $db->loadResult();

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
					$new_quiz_name = $db->loadResult();

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
				$db->execute();
				$max_id = $db->loadResult();

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
		if (isset($data['reminders'])) {
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
			$sql = "UPDATE #__guru_program set certificate_course_msg = '" . addslashes($data['coursemessage']) . "' where id=" . $data['id'];
			$db->setQuery($sql);
			$db->execute();
		}
		if ($data['id'] > 0) {
			$sql = "UPDATE #__guru_kunena_courseslinkage set coursename = '" . addslashes($data['name']) . "' where idcourse=" . $data['id'];
			$db->setQuery($sql);
			$db->execute();

			//  $sql = "SELECT catidkunena  FROM #__guru_kunena_courseslinkage where idcourse=".$data['id']." order by id desc limit 0,1";
			//  $db->setQuery($sql);
			//  $catidkunena = $db->loadResult();

			//  $sql = "SELECT coursename  FROM #__guru_kunena_courseslinkage where idcourse=".$data['id']." order by id desc limit 0,1";
			//  $db->setQuery($sql);
			//  $coursename = $db->loadResult();

		}
		JFactory::getApplication()->triggerEvent('onGuruAfterSaveCourse', array($progid));
		return $progid;
	}

	function parse_quiz($id, $type)
	{
		$database = JFactory::getDBO();
		$q = "SELECT * FROM #__guru_config WHERE id = '1' ";
		$database->setQuery($q);
		$configs = $database->loadObject();

		if ($type == "quiz") {
			$q  = "SELECT * FROM #__guru_quiz WHERE id = " . $id;
			$database->setQuery($q);
			$result = $database->loadObject();
			$the_media = $result;
			$the_media->type = "quiz";
		} else {
			$q = "SELECT * FROM #__guru_media WHERE id = " . $id;
			$database->setQuery($q);
			$result = $database->loadObject();
			$the_media = $result;
		}

		if ($the_media->type == 'text') {
			$media = $the_media->code;
		}
		if ($the_media->type == 'docs') {
			$the_base_link = explode('administrator/', $_SERVER['HTTP_REFERER']);
			$the_base_link = $the_base_link[0];

			//$media = 'The selected element is a text file that can\'t have a preview';
			$media = JText::_("GURU_NO_PREVIEW");

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
			}

			if ($the_media->source == 'local' && $the_media->width == 1)
				$media = '<br /><a href="' . $the_base_link . '/' . $configs->docsin . '/' . $the_media->local . '" target="_blank">' . $the_media->name . '</a>';

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
			$the_media->source = $the_media->id;

			$media = '';

			$q  = "SELECT * FROM #__guru_quiz WHERE id = " . $the_media->source;
			$database->setQuery($q);
			$result_quiz = $database->loadObject();
			$media .= '<span class="guru-quiz__title">' . $result_quiz->name . '</span>';
			$media .= '<span class="guru-quiz__desc">' . $result_quiz->description . '</span>';

			if ($result_quiz->is_final == 1) {
				$sql = "SELECT 	quizzes_ids FROM #__guru_quizzes_final WHERE qid=" . $the_media->source;
				$database->setQuery($sql);
				$result = $database->loadResult();
				$result_qids = explode(",", trim($result, ","));


				$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid IN (" . implode(",", $result_qids) . ") and published=1";
			} else {
				$q  = "SELECT * FROM #__guru_questions_v3 WHERE qid =" . $the_media->source . " and published=1";
			}

			$database->setQuery($q);
			$quiz_questions = $database->loadObjectList();

			$media = $media . '<div id="the_quiz">';

			$question_number = 1;

			for ($i = 0; $i < count($quiz_questions); $i++) {
				$one_question = $quiz_questions[$i];
				$question_answers_number = 0;


				$media .= '<ul class="guru_list">';
				$media .= 	'<li class="question">' . $one_question->text . '</li>';

				//$media = $media.'<div align="left" style="padding-left:30px;">';
				if ($one_question->a1 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a1) . '" \' type="checkbox" value="1a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a1 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a2 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a2) . '" \' type="checkbox" value="2a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a2 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a3 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a3) . '" \' type="checkbox" value="3a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a3 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a4 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a4) . '" \' type="checkbox" value="4a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a4 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a5 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a5) . '" \' type="checkbox" value="5a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a5 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a6 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a6) . '" \' type="checkbox" value="6a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a6 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a7 != '') {
					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a7) . '" \' type="checkbox" value="7a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a7 . '</input></li>';

					$question_answers_number++;
				}
				if ($one_question->a8 != '') {
					$question_answers_number++;

					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a8) . '" \' type="checkbox" value="8a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a8 . '</input></li>';
				}
				if ($one_question->a9 != '') {
					$question_answers_number++;

					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a9) . '" \' type="checkbox" value="9a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a9 . '</input></li>';
				}
				if ($one_question->a10 != '') {
					$question_answers_number++;

					$media .= '<li class="answer"><input onclick=\'document.getElementById("question_answergived' . $question_number . '").value="' . str_replace("'", "$$$$$", $one_question->a10) . '" \' type="checkbox" value="10a' . $question_number . '" name="q' . $question_number . '">&nbsp;' . $one_question->a10 . '</input></li>';
				}
				//$media = $media.'</div>';	
				$media = $media . '</ul>';

				$the_first_answer = explode(',', $one_question->answers);
				$the_first_answer = $the_first_answer[0];
				$the_first_answer = str_replace('a', '', $the_first_answer);

				if (intval($the_first_answer) == 1)
					$the_right_answer = $one_question->a1;
				if (intval($the_first_answer) == 2)
					$the_right_answer = $one_question->a2;
				if (intval($the_first_answer) == 3)

					$the_right_answer = $one_question->a3;
				if (intval($the_first_answer) == 4)
					$the_right_answer = $one_question->a4;
				if (intval($the_first_answer) == 5)
					$the_right_answer = $one_question->a5;
				if (intval($the_first_answer) == 6)
					$the_right_answer = $one_question->a6;
				if (intval($the_first_answer) == 7)
					$the_right_answer = $one_question->a7;
				if (intval($the_first_answer) == 8)
					$the_right_answer = $one_question->a8;
				if (intval($the_first_answer) == 9)
					$the_right_answer = $one_question->a9;
				if (intval($the_first_answer) == 10)
					$the_right_answer = $one_question->a10;

				$media = $media . '<input type="hidden" value="" name="question_answergived' . $question_number . '" id="question_answergived' . $question_number . '" />';
				$media = $media . '<input type="hidden" value="' . str_replace("'", "$$$$$", $the_right_answer) . '" name="question_answerright' . $question_number . '" id="question_answerright' . $question_number . '" />';
				$media = $media . '<input type="hidden" value="' . str_replace("'", "$$$$$", $one_question->text) . '" name="the_question' . $question_number . '" id="the_question' . $question_number . '" />';
				$question_number++;
			}
			$media = $media . '<input type="hidden" value="' . ($question_number - 1) . '" name="question_number" id="question_number" />';
			$media = $media . '<br /><div align="left" style="padding-left:30px;"><input type="submit" value="Submit" onclick="get_quiz_result()" /></div>';
			$media = $media . '</div>';
		}
		return $media;
	}



	function more_media_files($ids)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT *, id as media_id FROM #__guru_media WHERE id in (" . $ids . ") GROUP BY media_id");
		$more_media_files = $db->loadObjectList();
		$this->more_media_files = $more_media_files;
		return true;
	}

	public static function existing_ids($ids)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = " . $ids . " AND type='pmed' ");
		$existing_ids = $db->loadObjectList();
		return $existing_ids;
	}

	public static function preq_existing_ids($ids)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT media_id FROM #__guru_mediarel WHERE type_id = " . $ids . " AND type='preq' ");
		$existing_ids = $db->loadColumn();
		return $existing_ids;
	}

	function delFileMedia($id, $cid)
	{
		$database = JFactory::getDBO();
		$query = "DELETE FROM #__guru_mediarel WHERE type_id=" . $id . " AND media_id=" . $cid;
		$database->setQuery($query);
		if (!$database->execute()) {
			return false;
		}
		return true;
	}

	function delete()
	{
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$item = $this->getTable('guruPrograms');
		$database = JFactory::getDBO();

		$sql = "SELECT imagesin FROM #__guru_config WHERE id ='1' ";
		$database->setQuery($sql);
		$imagesin = $database->loadResult();

		foreach ($cids as $cid) {
			$stud_nb = 	$this->getStudentsNumber($cid);
			if ($stud_nb > 0) {
				return "denined";
			}
			// we delete the image asociated to this program - begin
			$sql = "SELECT image FROM #__guru_program WHERE id =" . $cid;
			$database->setQuery($sql);
			$image = $database->loadResult();
			$targetPath = JPATH_SITE . '/' . $imagesin . '/';
			unlink($targetPath . $image);
			$query = "DELETE FROM #__guru_mediarel WHERE type = 'pmed' AND type_id = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();
			// we delete the media relation - end	

			// we delete the email relation - begin
			$query = "DELETE FROM #__guru_mediarel WHERE type = 'email' AND type_id = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();
			// we delete the email relation - end	

			// we delete the DAYS along with the relations and images - begin
			$sql = "SELECT image, id FROM #__guru_days WHERE id =" . $cid;
			$database->setQuery($sql);
			$day_array = $database->loadObjectList();
			foreach ($day_array as $one_day) {
				unlink($targetPath . $one_day->image);
				// we delete the relation with tasks
				$query = "DELETE FROM #__guru_mediarel WHERE type = 'dtask' AND type_id = '" . $one_day->id . "'";
				$database->setQuery($query);
				$database->execute();
				// we delete the relation with media
				$query = "DELETE FROM #__guru_mediarel WHERE type = 'dmed' AND type_id = '" . $one_day->id . "'";
				$database->setQuery($query);
				$database->execute();
			}


			$query = "DELETE FROM #__guru_days WHERE pid = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();
			// we delete the DAYS along with the relations and images - end			

			// we delete the program_status - begin
			$query = "DELETE FROM #__guru_programstatus WHERE pid = '" . $cid . "'";
			$database->setQuery($query);
			$database->execute();
			// we delete the program_status - end			


			$query = "SELECT deleted_boards FROM #__guru_kunena_forum WHERE id =1 ";
			$database->setQuery($query);
			$deleted_boards = $database->loadResult();

			$sql = "select count(*) from #__extensions where element='com_kunena'";
			$database->setQuery($sql);
			$count = $database->loadResult();

			if ($count > 0) {
				if ($deleted_boards == 1) {
					$sql = "SELECT alias FROM #__guru_program WHERE id =" . $cid;
					$database->setQuery($sql);
					$alias = $database->loadResult();

					$query = "DELETE FROM #__kunena_categories WHERE alias = '" . $alias . "'";
					$database->setQuery($query);
					$database->execute();

					$query = "DELETE FROM #__kunena_aliases WHERE alias = '" . $alias . "'";
					$database->setQuery($query);
					$database->execute();
				} elseif ($deleted_boards == 2) {
					$sql = "SELECT alias FROM #__guru_program WHERE id =" . $cid;
					$database->setQuery($sql);
					$alias = $database->loadResult();

					$query = "UPDATE #__kunena_categories set published=0 WHERE alias = '" . $alias . "'";
					$database->setQuery($query);
					$database->execute();
				}
			}

			if (!$item->delete($cid)) {
				$this->setError($item->getError());
				return false;
			}
		}
		return true;
	}

	function publish()
	{

		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");
		$task = JFactory::getApplication()->input->get('task', '');
		if ($task == 'publish') {
			$sql = "update #__guru_program set published='1' where id in ('" . implode("','", $cids) . "')";
			$ret = 1;
		} else {
			$ret = -1;
			$sql = "update #__guru_program set published='0' where id in ('" . implode("','", $cids) . "')";
		}
		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}

		return $ret;
	}

	function publishEdit()
	{
		$db = JFactory::getDBO();
		$cids = JFactory::getApplication()->input->get('cid', array(0), "raw");

		$task = JFactory::getApplication()->input->get('task', '');
		if ($task == 'publish') {
			$sql = "update #__guru_media set published='1' where id in ('" . implode("','", $cids) . "')";
			$ret = 1;
		} else {
			$ret = -1;
			$sql = "update #__guru_media set published='0' where id in ('" . implode("','", $cids) . "')";
		}
		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}

		return $ret;
	}

	function delmedia($tid, $cid)
	{
		$db = JFactory::getDBO();

		$sql = "delete from #__guru_mediarel where type='pmed' and type_id=" . $tid . " and media_id=" . $cid . " and mainmedia='0'";

		$db->setQuery($sql);
		if (!$db->execute()) {
			//$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
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

	function getLastViewedLessandMod($id, $pid, $action = 'list')
	{
		$db = JFactory::getDBO();
		$sql = "SELECT lesson_id, module_id from #__guru_viewed_lesson WHERE user_id =" . intval($id) . " and pid=" . $pid;
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		@$result_lesson = $result[0]->lesson_id;
		$result_lesson = explode('||', trim($result_lesson, "||"));
		$result_lesson1 = end($result_lesson);
		@$result_module = $result[0]->module_id;
		$result_module = explode('||', trim($result_module, "||"));
		$result_module1 = end($result_module);

		$sql = "SELECT name FROM #__guru_task WHERE id=" . intval($result_lesson1);
		$db->setQuery($sql);
		$result_lesson = $db->loadResult();

		$sql = "SELECT title FROM #__guru_days WHERE id=" . intval($result_module1);
		$db->setQuery($sql);
		$result_module = $db->loadResult();

		$sql = "SELECT id FROM #__guru_days WHERE pid=" . $pid . " ORDER BY ordering";
		$db->setQuery($sql);
		$result_module_id = $db->loadColumn();

		if (isset($result_module1) && $result_module1 != NULL) {
			$sql = "SELECT id FROM #__guru_task t WHERE id IN (SELECT media_id FROM #__guru_mediarel WHERE type = 'dtask' AND type_id in (" . $result_module1 . ")) ORDER BY t.ordering";
			$db->setQuery($sql);
			$result_lesson_id = $db->loadColumn();
		}

		$module_nb = array_search($result_module1, $result_module_id);
		$module_nb += 1;
		@$lesson_nb = array_search($result_lesson1, $result_lesson_id);
		$lesson_nb += 1;

		if ($result_module != "") {
			if ($action == "export") {
				$result = $result_module . " / " . $result_lesson;
			} else {
				$result = JText::_('GURU_PROMDAY') . " " . $module_nb . ":" . " " . $result_module . "<br/>" . JText::_('GURU_TASK_TASK') . " " . $lesson_nb . ":" . " " . $result_lesson;
			}
		} else {
			$result = "";
		}

		return $result;
	}

	function courseCompleted($id, $pid)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT completed from #__guru_viewed_lesson WHERE user_id =" . intval($id) . " and pid=" . $pid;
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result == 1) {
			return true;
		} else {
			return false;
		}
	}

	function dateCourseCompleted($id, $pid)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT date_completed from #__guru_viewed_lesson WHERE user_id =" . intval($id) . " and pid=" . $pid;
		$db->setQuery($sql);
		$result = $db->loadResult();
		return $result;
	}

	function dateLastVisit($id, $pid)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT date_last_visit from #__guru_viewed_lesson WHERE user_id =" . intval($id) . " and pid=" . $pid;
		return $db->setQuery($sql)->loadResult();
	}

	function checkbox_construct($rowNum, $recId, $name = 'cid')
	{
		$db = JFactory::getDBO();

		$sql = " SELECT id FROM #__guru_order GROUP BY id";

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

	function getItems()
	{
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		if ($limit != 0) {
			$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";
		} else {
			$limit_cond = NULL;
		}

		$task = JFactory::getApplication()->input->get("task", "");

		if ($task == "show") {
			$query = $this->getListQuery();
		} else {
			$query = $this->getlistPrograms();
		}
		$result = $this->_getList($query . $limit_cond);
		$this->_total = $this->_getListCount($query);
		return $result;
	}

	function duplicate()
	{
		jimport('joomla.filesystem.folder');
		$cid	= JFactory::getApplication()->input->get('cid', array(), "raw");
		$n		= count($cid);

		if ($n == 0) {
			return JFactory::getApplication()->enqueueMessage(JText::_('No items selected'), 'error');
		}
		foreach ($cid as $id) {
			$row 	= $this->getTable('guruPrograms');
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
				guruAdminModelguruProgram::duplicate_day($one_day, $new_prog_id, $old_prog_id);
			}

			guruAdminModelguruProgram::duplicate_plans($new_prog_id, $old_prog_id);

			// we will duplicate now the days from the program - end
		}
		return 1;
	}

	function duplicate_plans($prog_id, $old_prog_id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__guru_program_plans WHERE product_id = " . $old_prog_id;
		$db->setQuery($sql);
		$program_plans = $db->loadObjectList();

		foreach ($program_plans as $value) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->insert('#__guru_program_plans');
			$query->columns(array($db->quoteName('product_id'), $db->quoteName('plan_id'), $db->quoteName('price'), $db->quoteName('default')));
			$query->values(intval($prog_id) . ',' . $db->quote(trim($value->plan_id)) . ',' . $db->quote(trim($value->price)) . ',' . $value->default);
			$db->setQuery($query);
			$db->execute();
		}

		$sql = "SELECT * FROM #__guru_program_renewals WHERE product_id = " . $old_prog_id;
		$db->setQuery($sql);
		$program_plans = $db->loadObjectList();

		foreach ($program_plans as $value) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->clear();
			$query->insert('#__guru_program_renewals');
			$query->columns(array($db->quoteName('product_id'), $db->quoteName('plan_id'), $db->quoteName('price'), $db->quoteName('default')));
			$query->values(intval($prog_id) . ',' . $db->quote(trim($value->plan_id)) . ',' . $db->quote(trim($value->price)) . ',' . $value->default);
			$db->setQuery($query);
			$db->execute();
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
			//$this->setError($db->getErrorMsg());
			return false;
		}

		$sql = "SELECT max(id) FROM #__guru_days ";
		$db->setQuery($sql);
		$the_day_copy_id = $db->loadResult();

		// we duplicate now the tasks + media (inside mediarel table) - BEGIN
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select("DISTINCT media_id" . ", " . $db->quoteName('type') . ", " . $db->quoteName('type_id') . ", " . $db->quoteName('mainmedia') . ", " . $db->quoteName('text_no') . ", " . $db->quoteName('layout') . ", " . $db->quoteName('access') . ", " . $db->quoteName('order'));
		$query->from("#__guru_mediarel");
		$query->where($db->quoteName('type_id') . ' = ' . intval($old_day_id));
		$db->setQuery($query);

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
					$sql = "INSERT INTO #__guru_task (name, alias, category, difficultylevel, points, image, published, startpublish, endpublish, metatitle, metakwd, metadesc, time, ordering, step_access, final_lesson, forum_kunena_generatedt, groups_access, duration, description, css) VALUES ('" . addslashes(trim($old_lesson["0"]["name"])) . "', '" . addslashes(trim($old_lesson["0"]["alias"])) . "', " . intval($old_lesson["0"]["category"]) . ", '" . trim($old_lesson["0"]["difficultylevel"]) . "', " . intval($old_lesson["0"]["points"]) . ", '" . trim($old_lesson["0"]["image"]) . "', '" . $old_lesson["0"]["published"] . "', '" . $old_lesson["0"]["startpublish"] . "', '" . $old_lesson["0"]["endpublish"] . "', '" . addslashes(trim($old_lesson["0"]["metatitle"])) . "', '" . addslashes(trim($old_lesson["0"]["metakwd"])) . "', '" . addslashes(trim($old_lesson["0"]["metadesc"])) . "', " . $old_lesson["0"]["time"] . ", " . $old_lesson["0"]["ordering"] . ", " . $old_lesson["0"]["step_access"] . ", '" . $old_lesson["0"]["final_lesson"] . "', '" . $old_lesson["0"]["forum_kunena_generatedt"] . "', '" . $old_lesson["0"]["groups_access"] . "', '" . $old_lesson["0"]["duration"] . "', '" . addslashes($old_lesson["0"]["description"]) . "', '" . $old_lesson["0"]["css"] . "')";
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
														layout,
														text_no
											) VALUES (
														'" . $value->type . "', 
														'" . $media_id . "', 
														'" . $value->media_id . "' , 
														'" . $value->mainmedia . "'	,
														'" . $value->layout . "',
														'" . $value->text_no . "'
													)";
							$db->setQuery($sql);
							if (!$db->execute()) {
								//$this->setError($db->getErrorMsg());
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
								//$this->setError($db->getErrorMsg());
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
				//$this->setError($db->getErrorMsg());
				return false;
			}
		}
		// we duplicate now the tasks + media (inside mediarel table) - END
	}

	function exportFile()
	{
		$pid = JFactory::getApplication()->input->get("pid", 0);
		$db = JFactory::getDBO();

		$sql = "select distinct u.username, u.id, u.email, c.firstname, c.lastname  from #__guru_customer c, #__users u, #__guru_buy_courses bc where c.id=bc.userid and bc.course_id =" . intval($pid) . " and bc.userid=u.id order by c.id desc";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$n = count($result);

		$sql = "select `name`, `alias` from #__guru_program where `id` = " . intval($pid);
		$db->setQuery($sql);
		$course_details = $db->loadAssocList();
		$course_name = $course_details["0"]["name"];
		$course_alias = $course_details["0"]["alias"];

		$sql = "select datetype FROM #__guru_config where id=1 ";
		$db->setQuery($sql);
		$format_date = $db->loadResult();

		$header  = $course_name . "\n" . "\n";
		$header .= "  #  ,First Name, Last Name, Email, Username, Progress, Last Visit,         , Quiz Name, Date/Time Taken, Score";
		$data  = $header . "\n" . "\n";

		for ($i = 0; $i < $n; $i++) {
			$lesson_module_progress = "";
			$date_last_visit = "";
			$completed_progress = $this->courseCompleted($result[$i]->id, $pid);
			$date_completed = $this->dateCourseCompleted($result[$i]->id, $pid);
			$date_completed = date("" . $format_date . "", strtotime($date_completed));

			if ($completed_progress == true) {
				$var_lang = JText::_('GURU_COMPLETED');
				$lesson_module_progress = $var_lang . " " . "(" . date("Y-m-d", strtotime($date_completed)) . ")";
			} else {
				$lesson_module_progress = $this->getLastViewedLessandMod($result[$i]->id, $pid, 'export');
			}

			$date_last_visit = $this->dateLastVisit($result[$i]->id, $pid);

			if ($date_last_visit != "0000-00-00" && $date_last_visit != NULL) {
				$date_last_visit = date("" . $format_date . "", strtotime($date_last_visit));
			} else {
				$date_last_visit = "";
			}

			$sql = "select q.`name`, qt.`date_taken_quiz`, qt.`score_quiz` from #__guru_quiz q, #__guru_quiz_question_taken_v3 qt where q.`id`=qt.`quiz_id` and qt.`user_id`=" . intval($result[$i]->id) . " and qt.`pid`=" . intval($pid);
			$db->setQuery($sql);
			$quiz_details = $db->loadAssocList();
			$count_quizz_taken = 0;

			if (isset($quiz_details) && is_array($quiz_details) && count($quiz_details) > 0) {
				$count_quizz_taken = count($quiz_details);
			}

			$firstname = $result[$i]->firstname;
			$lastname  = $result[$i]->lastname;
			$username = $result[$i]->username;
			$email = $result[$i]->email;

			$data .= '  ' . ($i + 1) . '  ,' . $firstname . ' , ' . $lastname . ', ' . $email . ', ' . $username . ', ' . $lesson_module_progress . ', ' . $date_last_visit;

			$first_quiz_added = true;

			if ($count_quizz_taken > 0) {
				if (isset($quiz_details) && is_array($quiz_details) && count($quiz_details) > 0) {
					foreach ($quiz_details as $quiz_key => $quiz_detail) {
						if ($first_quiz_added) {
							$data .= ', ' . ', ' . $quiz_detail["name"] . ', ' . $quiz_detail["date_taken_quiz"] . ', ' . $quiz_detail["score_quiz"] . "%" . "\n";
							$first_quiz_added = false;
						} else {
							$data .= '' . ', ' . ', ' . '' . ', ' . '' . ', ' . '' . ', ' . '' . ', ' . '' . ', ' . '' . ', ' . $quiz_detail["name"] . ', ' . $quiz_detail["date_taken_quiz"] . ', ' . $quiz_detail["score_quiz"] . "%" . "\n";
						}
					}
				}

				$data .= "\n";
			} else {
				$data .= "\n";
			}
		}

		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=Students-Enrolled-" . $course_alias . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $data;
		exit();
	}

	function getDateFormat()
	{
		$db = JFactory::getDBO();
		$sql = "Select datetype FROM #__guru_config where id=1 ";
		$db->setQuery($sql);
		$format_date = $db->loadResult();
		return $format_date;
	}

	function approve()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		$db = JFactory::getDBO();
		$sql = "update #__guru_program set status='1' where id=" . intval($cids["0"]);
		$db->setQuery($sql);
		if (!$db->execute()) {
			return FALSE;
		}

		$this->sendEmailForApprove($cids["0"]);

		return TRUE;
	}

	function pending()
	{
		$cids = JFactory::getApplication()->input->get("cid", array(), "raw");
		$db = JFactory::getDBO();
		$sql = "update #__guru_program set status='0' where id=" . intval($cids["0"]);
		$db->setQuery($sql);
		if (!$db->execute()) {
			return FALSE;
		}

		$this->sendEmailForPending($cids["0"]);

		return TRUE;
	}

	function sendEmailForApprove($course_id)
	{
		$db = JFactory::getDBO();
		$sql = "select p.*, u.name as username from #__guru_program p, #__users u where u.id=p.author and p.id=" . intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$sql = "select template_emails, fromname, fromemail from #__guru_config";
		$db->setQuery($sql);
		$confic = $db->loadAssocList();
		$template_emails = $confic["0"]["template_emails"];
		$template_emails = json_decode($template_emails, true);
		$fromname = $confic["0"]["fromname"];
		$fromemail = $confic["0"]["fromemail"];

		$sql = "select u.email from #__users u where u.id=" . intval($result["0"]["author"]);
		$db->setQuery($sql);
		$email = $db->loadResult();
		$email = array($email);

		$app = JFactory::getApplication();
		$site_name = $app->getCfg('sitename');

		$subject = $template_emails["approve_subject"];
		$body = $template_emails["approve_body"];

		$approve_url = '<a href="' . JURI::root() . "administrator/index.php?option=com_guru&controller=guruPrograms&cid[]=" . intval($result["0"]["id"]) . "&task=approve" . '" target="_blank">' . JURI::root() . "administrator/index.php?option=com_guru&controller=guruPrograms&cid[]=" . intval($result["0"]["id"]) . "&task=approve" . '</a>';

		$subject = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $subject);
		$subject = str_replace("[COURSE_NAME]", $result["0"]["name"], $subject);
		$subject = str_replace("[COURSE_APPROVE_URL]", $approve_url, $subject);
		$subject = str_replace("[SITE_NAME]", $site_name, $subject);

		$body = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $body);
		$body = str_replace("[COURSE_NAME]", $result["0"]["name"], $body);
		$body = str_replace("[COURSE_APPROVE_URL]", $approve_url, $body);
		$body = str_replace("[SITE_NAME]", $site_name, $body);

		$send_teacher_email_course_approved = isset($template_emails["send_teacher_email_course_approved"]) ? $template_emails["send_teacher_email_course_approved"] : 1;

		if ($send_teacher_email_course_approved) {
			JFactory::getMailer()->sendMail($fromemail, $fromname, $email, $subject, $body, 1);
		}
	}

	function sendEmailForPending($course_id)
	{
		$db = JFActory::getDBO();
		$sql = "select p.*, u.name as username from #__guru_program p, #__users u where u.id=p.author and p.id=" . intval($course_id);
		$db->setQuery($sql);
		$result = $db->loadAssocList();

		$sql = "select template_emails, fromname, fromemail, admin_email from #__guru_config";
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

		$subject = $template_emails["unapprove_subject"];
		$body = $template_emails["unapprove_body"];

		$approve_url = '<a href="' . JURI::root() . "administrator/index.php?option=com_guru&controller=guruPrograms&cid[]=" . intval($result["0"]["id"]) . "&task=approve" . '" target="_blank">' . JURI::root() . "administrator/index.php?option=com_guru&controller=guruPrograms&cid[]=" . intval($result["0"]["id"]) . "&task=approve" . '</a>';

		$subject = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $subject);
		$subject = str_replace("[COURSE_NAME]", $result["0"]["name"], $subject);
		$subject = str_replace("[COURSE_APPROVE_URL]", $approve_url, $subject);
		$subject = str_replace("[SITE_NAME]", $site_name, $subject);

		$body = str_replace("[AUTHOR_NAME]", $result["0"]["username"], $body);
		$body = str_replace("[COURSE_NAME]", $result["0"]["name"], $body);
		$body = str_replace("[COURSE_APPROVE_URL]", $approve_url, $body);
		$body = str_replace("[SITE_NAME]", $site_name, $body);

		for ($i = 0; $i < count($email); $i++) {
			$send_teacher_email_course_unapproved = isset($template_emails["send_teacher_email_course_unapproved"]) ? $template_emails["send_teacher_email_course_unapproved"] : 1;

			if ($send_teacher_email_course_unapproved) {
				JFactory::getMailer()->sendMail($fromemail, $fromname, $email[$i], $subject, $body, 1);
			}
		}
	}
};
