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

jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');

class guruControllerguruPrograms extends guruController
{
	var $model = null;

	function __construct()
	{
		parent::__construct();
		$this->registerTask("", "view");
		$this->registerTask("listCourses", "listCourses");
		$this->registerTask("tree", "coursesTree");
		$this->registerTask("listing", "coursesListing");
		$this->registerTask("buy_action", "buyAction");
		$this->registerTask("enroll", "enroll");
		$this->_model = $this->getModel("guruProgram");
	}

	function listPrograms()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function listCourses()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("courses");
		$view->setModel($this->_model, true);
		$view->listCourses();
	}

	function buyAction()
	{
		$db = JFactory::getDBO();
		$course_id = JFactory::getApplication()->input->get("course_id", 0);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select($db->quoteName('price'));
		$query->from("#__guru_program_plans");
		$query->where($db->quoteName('product_id') . ' = ' . intval($course_id) . " AND " . $db->quoteName('default') . " = '1'");
		$db->setQuery($query);

		$price = $db->loadResult();

		if (!isset($price) && $price == NULL) {
			$price = "0";
		}

		$sql = "SELECT name FROM #__guru_program WHERE id = " . intval($course_id);
		$db->setQuery($sql);
		$name = $db->loadResult();

		$user = JFactory::getUser();
		$user_id = $user->id;
		$plan = "buy";

		if ($user_id != "0") {
			$user_courses = $this->_model->getUserCourses();

			if (isset($user_courses) && $user_courses != NULL && is_array($user_courses) && count($user_courses) > 0 && isset($user_courses[$course_id])) {
				if (isset($user_courses[$course_id])) {
					$plan = "renew";
					$price = "";
				}
			}
		}

		$action = JFactory::getApplication()->input->get("action", "");
		if ($action == "renewemail") {
			$plan = "renew";
		}

		$session = JFactory::getSession();
		$registry = $session->get('registry');
		$courses_from_cart = $registry->get('courses_from_cart', "");

		if (isset($courses_from_cart) && $courses_from_cart != "") {
			$temp_array = array("course_id" => $course_id, "value" => $price, "name" => $name, "plan" => $plan);
			$new_value = $courses_from_cart;
			$new_value[$course_id] = $temp_array;
			$registry->set('courses_from_cart', $new_value);
		} else {
			$temp_array = array("course_id" => $course_id, "value" => $price, "name" => $name, "plan" => $plan);
			$new_value = array();
			$new_value[$course_id] = $temp_array;
			$registry->set('courses_from_cart', $new_value);
		}

		$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

		$this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruBuy", false));
	}

	function coursesTree()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("tree");
		$view->setModel($this->_model, true);
		$view->listCategoryCourses();
	}

	function coursesListing()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("listing");
		$view->setModel($this->_model, true);
		$view->listCategoryCourses();
	}

	function view()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("view");
		$view->setModel($this->_model, true);
		$view->view = $this;
		$view->show();
	}

	function myprograms()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("myprograms");
		$view->setModel($this->_model, true);
		$view->showmyprograms();
	}

	function details()
	{
		$view = $this->getView("guruPrograms", "html");
		$view->setLayout("details");
		$view->setModel($this->_model, true);
		$view->details();
	}

	function pause()
	{
		if (!$this->_model->pause()) {
			$msg = JText::_('AD_CMP_CANTPAUSE');
		} else {
			$msg = JText::_('AD_CMP_PAUSED');
		}

		$link = "index.php?option=com_guru&view=guruPrograms";
		$this->setRedirect($link, $msg);
	}

	function unpause()
	{
		if (!$this->_model->unpause()) {
			$msg = JText::_('AD_CMP_CANTUNPAUSE');
		} else {
			$msg = JText::_('AD_CMP_UNPAUSED');
		}

		$link = "index.php?option=com_guru&view=guruPrograms";
		$this->setRedirect($link, $msg);
	}

	function buySelectedCourse($selected_course)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "SELECT distinct(course_id) FROM #__guru_buy_courses where userid=" . intval($user_id);
		$db->setQuery($sql);
		$all_courses = $db->loadColumn();

		$selected_course_final = explode('|', $selected_course);
		$intersect = array_intersect($selected_course_final, $all_courses);
		if (count($intersect) > 0) {
			return true;
		} else {
			return false;
		}
	}
	function hasAtLeastOneCourse()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$course_id = intval(JFactory::getApplication()->input->get("cid", 0));
		$sql = "SELECT count(*) FROM #__guru_buy_courses where userid=" . intval($user_id) . " and course_id <>" . $course_id;
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result > 0) {
			return true;
		} else {
			return false;
		}
	}

	function enroll()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		$Itemid = JFactory::getApplication()->input->get('Itemid', 0, "raw");
		if (!isset($Itemid)) {
			$data_post = JFactory::getApplication()->input->post->getArray();
			$Itemid = $data_post['Itemid'];
		}

		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruprograms"];

		if (intval($itemid_seo) > 0) {
			$Itemid = intval($itemid_seo);
		}

		$course_id = JFactory::getApplication()->input->get("cid", "0");
		$graybox = JFactory::getApplication()->input->get("graybox", "");
		$registered_user = JFactory::getApplication()->input->get("registered_user", "");
		$sql = "SELECT chb_free_courses, step_access_courses, selected_course, groups_access  FROM `#__guru_program` where id = " . intval($course_id);
		$row = $db->setQuery($sql)->loadAssoc();
		$chb_free_courses = $row["chb_free_courses"];
		$step_access_courses = $row["step_access_courses"];
		$selected_course = $row["selected_course"];
		$groups_access = explode(',', $row["groups_access"]);

		$result = '';
		if ($graybox == "true" || $graybox == "1") {
			$model = $this->getModel("guruProgram");
			$result = $model->enroll();
			if ($result == 'now') {
				$msg = JText::_("GURU_ENROLL_SUCCESSFULLY");
			} else {
				$msg = JText::_("GURU_ALREADY_ENROLLED");
			}

			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$registry->set('joomlamessage', $msg);

			echo '<script type="text/javascript">';
			echo 'window.parent.location.reload();';
			echo '</script>';
			die();
		}

		if ($user->id == 0) {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];

			if (intval($itemid_seo) > 0) {
				$Itemid = intval($itemid_seo);
			}

			return $this->setRedirect(JRoute::_("index.php?option=com_guru&view=guruLogin&returnpage=enroll&cid=" . $course_id . "&Itemid=" . $Itemid, false));
		} else {
			$model = $this->getModel("guruProgram");

			if (isset($registered_user) && $registered_user == 1) {
				if ($chb_free_courses == 1 && $step_access_courses != 0) {
					$result = $model->enroll();
				} elseif ($chb_free_courses == 1 && $step_access_courses == 0 && $selected_course == -1) {
					if ($this->hasAtLeastOneCourse()) {
						$result = $model->enroll();
					} else {
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["guruprograms"];

						if (intval($itemid_seo) > 0) {
							$Itemid = intval($itemid_seo);
						} else {
							$helper = new guruHelper();
							$itemid_menu = $helper->getCourseMenuItem(intval($course_id));

							if (intval($itemid_menu) > 0) {
								$Itemid = intval($itemid_menu);
							}
						}

						$course_page = JRoute::_("index.php?option=com_guru&view=guruPrograms&cid=" . $course_id . "&Itemid=" . $Itemid, false);
						$this->setRedirect($course_page, "");
					}
				}
			} else {
				if ($chb_free_courses == 1 && $step_access_courses == 0 && $selected_course != -1) {
					if ($this->buySelectedCourse($selected_course)) {
						$result = $model->enroll();
					} else {
						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["guruprograms"];

						if (intval($itemid_seo) > 0) {
							$Itemid = intval($itemid_seo);
						} else {
							$helper = new guruHelper();
							$itemid_menu = $helper->getCourseMenuItem(intval($course_id));

							if (intval($itemid_menu) > 0) {
								$Itemid = intval($itemid_menu);
							}
						}

						$course_page = JRoute::_("index.php?option=com_guru&view=guruPrograms&cid=" . $course_id . "&Itemid=" . $Itemid, false);
						$this->setRedirect($course_page, "");
					}
				} else if ($chb_free_courses == 1 && $step_access_courses == 1 && $user->id) {
					$userGroups = array_values($user->groups);
					if (array_intersect($groups_access, $userGroups)) {
						$result = $model->enroll();
					}
				}
			}

			$course_page = JRoute::_("index.php?option=com_guru&view=guruPrograms&cid=" . $course_id . "&Itemid=" . $Itemid, false);

			if (isset($registered_user) && $registered_user == 1 && ($step_access_courses == 0)) {
				$result = "";
			}
			if ($result == 'now') {
				$this->setRedirect($course_page, JText::_("GURU_ENROLL_SUCCESSFULLY"));
			} elseif ($result == 'old') {
				$this->setRedirect($course_page, JText::_("GURU_ALREADY_ENROLLED"));
			} elseif ($result == '') {
				$this->setRedirect($course_page, "error", 'error');
			}
		}
	}

	function check_values()
	{
		include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "guruprograms" . DIRECTORY_SEPARATOR . "tmpl" . DIRECTORY_SEPARATOR . "ajax.php");
		die();
	}

	function delete_image_ajax()
	{
		include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "guruprograms" . DIRECTORY_SEPARATOR . "tmpl" . DIRECTORY_SEPARATOR . "ajax.php");
		die();
	}
};
