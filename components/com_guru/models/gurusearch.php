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


class guruModelguruSearch extends JModelLegacy
{
	public $child_categories = array();

	function __construct()
	{
		parent::__construct();
	}

	function getAllChilds($parent)
	{
		$db = JFactory::getDbo();
		$this->child_categories[] = intval($parent);

		$sql = "SELECT `child_id` FROM #__guru_categoryrel WHERE parent_id=" . intval($parent);
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();

		if (isset($result) && is_array($result) && isset($result["0"])) {
			$this->child_categories[] = intval($result["0"]);

			if (intval($result["0"]) > 0) {
				$this->getAllChilds($result["0"]);
			}
		}

		return $this->child_categories;
	}

	function getCourses()
	{
		$search = JFactory::getApplication()->input->getString("search", "");
		$courses = array();

		if (trim($search) != "") {
			$module_id = JFactory::getApplication()->input->getInt("module_id");
			$moduleTable = JTable::getInstance('module');
			$moduleTable->load($module_id);
			$params = new JRegistry;
			$params->loadString($moduleTable->params);

			$db = JFactory::getDbo();
			$sortby = $params->get("sortby", "0");
			$category = $params->get("category", "0");

			$and = "";

			if (intval($category) > 0) {
				$categories_list = $this->getAllChilds(intval($category));

				if (is_array($categories_list) && count($categories_list) > 0) {
					$and .= " AND `catid` in (" . implode(",", $categories_list) . ")";
				}
			}

			switch ($sortby) {
				case "0": { // most popular
						$sql = "select * from `#__guru_program` where `published`='1' and `status`='1' and `startpublish` <= now() and (`name` like '%" . $db->escape(trim($search)) . "%' OR `description` like '%" . $db->escape(trim($search)) . "%') " . $and;
						$db->setQuery($sql);
						$db->execute();
						$courses = $db->loadAssocList();

						if (isset($courses) && count($courses) > 0) {
							$temp_courses = array();
							$user = JFactory::getUser();
							$user_groups = $user->groups;

							foreach ($courses as $key => $value) {
								$sql = "select `groups` from #__guru_category where `id`=" . intval($value["catid"]);
								$db->setQuery($sql);
								$db->execute();
								$categ_groups = $db->loadColumn();

								if (isset($value["groups_acl"]) && trim($value["groups_acl"]) != "") {
									$acl_groups = json_decode(trim($value["groups_acl"]), true);
									$intersect = array_intersect($user_groups, $acl_groups);

									if (in_array(1, $acl_groups) || in_array(9, $acl_groups) || count($intersect) > 0) {
										$temp_courses[] = $value;
									} elseif (!isset($intersect) || count($intersect) == 0) {
										// no category access
									}
								} elseif (isset($categ_groups["0"]) && trim($categ_groups["0"]) != "") {
									$user = JFactory::getUser();
									$user_groups = $user->groups;
									$acl_groups = json_decode(trim($categ_groups["0"]), true);
									$intersect = array_intersect($user_groups, $acl_groups);

									if (in_array(1, $acl_groups) || in_array(9, $acl_groups) || count($intersect) > 0) {
										$temp_courses[] = $value;
									} elseif (!isset($intersect) || count($intersect) == 0) {
										// no category access
									}
								} else {
									$temp_courses[] = $value;
								}
							}

							$courses = $temp_courses;
						}

						if (isset($courses) && count($courses) > 0) {
							$courses_temp = array();
							foreach ($courses as $key => $course) {
								$nr = $this->getStudentsNumber($course, null);

								if (count($courses_temp) == 0) {
									$courses_temp[] = array($course['id'] => $nr);
								} else {
									foreach ($courses_temp as $key => $c_id_nr) {
										if (current($c_id_nr) <= $nr) {
											array_splice($courses_temp, $key, 0, array(array($course['id'] => $nr)));
											break;
										} elseif (!isset($courses_temp[$key + 1])) {
											array_splice($courses_temp, $key + 1, 0, array(array($course['id'] => $nr)));
											break;
										}
									}
								}
							}
							$courses_temp = array_slice($courses_temp, 0, $params->get("howManyC"));
							$courses = array();
							foreach ($courses_temp as $key => $c_id_nr) {
								$sql = "select * from `#__guru_program` where `id`=" . intval(key($c_id_nr));
								$db->setQuery($sql);
								$db->execute();
								$course_temp = $db->loadAssocList();
								$courses = array_merge($courses, $course_temp);
							}
						}

						return $courses;
						break;
					}
				case "1": { // most recent
						$and .= " ORDER BY `startpublish` DESC LIMIT 0," . $params->get("howManyC") . "";
						break;
					}
				case "2": { // random
						$and .= " ORDER BY RAND() LIMIT 0," . $params->get("howManyC") . "";
						break;
					}
			}

			$sql = "select * from `#__guru_program` where 1=1 and published=1 and status=1 and `startpublish` <= now() and (`name` like '%" . $db->escape(trim($search)) . "%' OR `description` like '%" . $db->escape(trim($search)) . "%') " . $and;
			$db->setQuery($sql);
			$db->execute();
			$courses = $db->loadAssocList();
		}

		if (isset($courses) && count($courses) > 0) {
			$temp_courses = array();
			$user = JFactory::getUser();
			$user_groups = $user->groups;

			foreach ($courses as $key => $value) {
				$sql = "select `groups` from #__guru_category where `id`=" . intval($value["catid"]);
				$db->setQuery($sql);
				$db->execute();
				$categ_groups = $db->loadColumn();

				if (isset($value["groups_acl"]) && trim($value["groups_acl"]) != "") {
					$acl_groups = json_decode(trim($value["groups_acl"]), true);
					$intersect = array_intersect($user_groups, $acl_groups);

					if (in_array(1, $acl_groups) || in_array(9, $acl_groups) || count($intersect) > 0) {
						$temp_courses[] = $value;
					} elseif (!isset($intersect) || count($intersect) == 0) {
						// no category access
					}
				} elseif (isset($categ_groups["0"]) && trim($categ_groups["0"]) != "") {
					$user = JFactory::getUser();
					$user_groups = $user->groups;
					$acl_groups = json_decode(trim($categ_groups["0"]), true);
					$intersect = array_intersect($user_groups, $acl_groups);

					if (in_array(1, $acl_groups) || in_array(9, $acl_groups) || count($intersect) > 0) {
						$temp_courses[] = $value;
					} elseif (!isset($intersect) || count($intersect) == 0) {
						// no category access
					}
				} else {
					$temp_courses[] = $value;
				}
			}

			$courses = $temp_courses;
		}

		return $courses;
	}

	function getStudentsNumber($course, $params)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT count(distinct bc.`userid`) FROM #__guru_buy_courses bc, #__users u , #__guru_customer c, #__guru_order o WHERE c.`id`=bc.`userid` and bc.`userid`=u.`id` and bc.`course_id`=" . intval($course["id"]) . " and o.`userid`=c.`id` and o.`userid`=bc.`userid` and o.`status`='Paid'";
		$db->setQuery($sql);
		$db->execute();
		$result = $db->loadColumn();

		return @$result["0"];
	}
};
