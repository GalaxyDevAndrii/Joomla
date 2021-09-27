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

class guruControllerguruLogin extends guruController
{
	function __construct()
	{
		parent::__construct();
		$this->registerTask("", "view");
		$this->registerTask("register", "edit");
		$this->registerTask("saveCustomer", "save");
		$this->registerTask("saveAuthor", "saveauthor");
		$this->registerTask("authorprofile", "saveAuthoredit");
		$this->registerTask("log_in_user", "logUser");
		$this->registerTask("terms", "terms");
		$this->registerTask("upload_ajax_image", "uploadAjaxImage");
		$task = JFactory::getApplication()->input->get("task", "", "raw");
	}

	function view()
	{
		$returnpage = JFactory::getApplication()->input->get("returnpage", "", "raw");
		$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruprograms"];

		if (intval($itemid_seo) > 0) {
			$Itemid = intval($itemid_seo);
		}

		$app = JFactory::getAPPlication("site");

		if ($returnpage != "" && $returnpage == "enroll") {
			$user = JFactory::getUser();
			if ($user->id != "0") {
				$course_id = JFactory::getApplication()->input->get("course_id", "0", "raw");
				$link = "index.php?option=com_guru&view=guruPrograms&task=enroll&cid=" . intval($course_id) . "&Itemid=" . intval($Itemid);
				$app->redirect($link);
			}
		}

		JFactory::getApplication()->input->set('view', 'guruLogin');
		parent::display();
	}

	function edit()
	{
		$view = $this->getView("guruLogin", "html");
		$view->setLayout("editForm");
		$view->editForm();
	}

	function terms()
	{
		$view = $this->getView("guruLogin", "html");
		$view->setLayout("terms");
		$view->terms();
	}

	function isCustomer($user_id)
	{
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_customer where id=" . intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result > 0) {
			return true;
		}
		return false;
	}

	function buyCourses($user_id)
	{
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__guru_order where userid=" . intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();

		if ($result > 0) {
			return true;
		} else {
			return false;
		}
	}

	function logUser()
	{
		//global $mainframe;
		$app = JFactory::getApplication();
		$lang = $app->getLanguage()->getTag();
		$db = JFactory::getDbo();
		//global $Itemid;
		$Itemid = JFactory::getApplication()->input->getInt("Itemid", "0");

		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruprograms"];

		if (intval($itemid_seo) > 0) {
			$Itemid = intval($itemid_seo);
		}

		$return_page = JFactory::getApplication()->input->get("returnpage", "mycourses", "raw");
		$course_id = JFactory::getApplication()->input->get("cid", "0", "raw");

		if ($return = JFactory::getApplication()->input->get('return', '', "raw")) {
			$return = base64_decode($return);
		}

		$options = array();
		$options['remember'] = JFactory::getApplication()->input->get('remember', false, "raw");
		$options['return'] = $return;

		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$password = JFactory::getApplication()->input->get("passwd", "", "raw");

		$credentials = array();
		$credentials['username'] = $username;
		$credentials['password'] = $password;

		$err = $app->login($credentials, $options);

		$link = "";
		if ($return_page == "checkout") {
			$user = JFactory::getUser();
			$user_id = $user->id;
			if (!$this->isCustomer($user_id)) {
				$link = "index.php?option=com_guru&view=guruBuy&task=checkout&from=login&returnpage=checkout";
			} else {
				$link = "index.php?option=com_guru&view=guruBuy";
			}
		} elseif ($return_page == "myorders") {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruorders"];

			if (intval($itemid_seo) > 0) {
				$itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=" . intval($Itemid);
		} elseif ($return_page == "myquizandfexam") {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruorders"];

			if (intval($itemid_seo) > 0) {
				$itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruorders&layout=myquizandfexam&Itemid=" . intval($Itemid);
		} elseif ($return_page == "mycourses") {
			$link = "index.php?option=com_guru&view=guruorders&layout=mycourses&Itemid=" . intval($Itemid);
		} elseif ($return_page == "mycertificates") {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruorders"];

			if (intval($itemid_seo) > 0) {
				$itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruorders&layout=mycertificates&Itemid=" . intval($Itemid);
		} elseif ($return_page == "enroll") {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprograms"];

			if (intval($itemid_seo) > 0) {
				$itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruPrograms&task=enroll&cid=" . $course_id . "&Itemid=" . intval($Itemid);
		} elseif ($return_page == "authorprofile") {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprofile"];

			if (intval($itemid_seo) > 0) {
				$itemid = intval($itemid_seo);
			} else {
				$user = JFactory::getUser();
				$user_id = $user->id;

				$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

				if (intval($itemid_menu) > 0) {
					$itemid = intval($itemid_menu);
				}
			}

			$teacher_redirect = $this->getTeacherRedirectUrl();

			if (trim($teacher_redirect) != "") {
				$link = trim($teacher_redirect);
			} else {
				$link = "index.php?option=com_guru&view=guruauthor&task=authorprofile&Itemid=" . intval($Itemid);
			}
		} elseif ($return_page == "authormycourses") {
			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&Itemid=" . intval($Itemid);
		} elseif ($return_page == "authormymediacategories") {
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymediacategories&Itemid=" . intval($Itemid);
		} elseif ($return_page == "authormymedia") {
			$link = "index.php?option=com_guru&view=guruauthor&task=authormymedia&Itemid=" . intval($Itemid);
		} elseif ($return_page == "authorquizzes") {
			$link = "index.php?option=com_guru&view=guruauthor&task=authorquizzes&Itemid=" . intval($Itemid);
		} elseif ($return_page == "authorcommissions") {
			$link = "index.php?option=com_guru&view=guruauthor&task=authorcommissions&Itemid=" . intval($Itemid);
		} elseif ($return_page == "mystudents" || $return_page == "studentquizes" ||  $return_page == "studentdetails" || $return_page == "quizdetails") {
			$link = "index.php?option=com_guru&view=guruauthor&task=mystudents&Itemid=" . intval($Itemid);
		} elseif ($return_page == "registerforlogout") {
			$view_get = JFactory::getApplication()->input->get("view", "", "raw");
			$email_r = JFactory::getApplication()->input->get("e", "", "raw");
			$catid = JFactory::getApplication()->input->get("catid", "", "raw");
			$module_lesson = JFactory::getApplication()->input->get("module", "", "raw");
			$lesson_id = JFactory::getApplication()->input->get("cid", "", "raw");
			if (($view_get == "guruTasks" || $view_get == "gurutasks") && $email_r == "1") {
				$link = "index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $module_lesson . "&cid=" . $lesson_id;
			} else {
				$link = "index.php?option=com_guru";
			}
		} elseif ($return_page == "open_lesson") {
			$lesson_id = JFactory::getApplication()->input->get("lesson_id", "0", "raw");

			if (intval($lesson_id) != 0) {
				$sql = "select type_id from #__guru_mediarel where type='dtask' and media_id=" . intval($lesson_id) . " and type_id <> '0'";
				$db->setQuery($sql);
				$module_id = $db->loadColumn();
				$module_id = @$module_id["0"];

				$sql = "select pid from #__guru_days where id=" . intval($module_id);
				$db->setQuery($sql);
				$course_id = $db->loadColumn();
				$course_id = @$course_id["0"];

				if (intval($course_id) > 0) {
					$sql = "select chb_free_courses, step_access_courses, groups_access, selected_course from #__guru_program where id=" . intval($course_id);
					$db->setQuery($sql);
					$course_access_details = $db->loadAssocList();

					$chb_free_courses = $course_access_details["0"]["chb_free_courses"];
					$step_access_courses = $course_access_details["0"]["step_access_courses"];
					$groups_access = $course_access_details["0"]["groups_access"];
					$selected_course = $course_access_details["0"]["selected_course"];

					if ($chb_free_courses == "1") {
						// free
						if ($step_access_courses == "1") {
							// members
							if (trim($groups_access) == "") {
								// free for all user groups
								include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
								$model_programs = new guruModelguruProgram();
								JFactory::getApplication()->input->set("cid", intval($course_id));
								$model_programs->enroll();
							} else {
								$groups_access = explode(",", $groups_access);

								if ($this->userInGroups($groups_access)) {
									include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
									$model_programs = new guruModelguruProgram();
									JFactory::getApplication()->input->set("cid", intval($course_id));
									$model_programs->enroll();
								} else {
									$app = JFactory::getApplication();
									$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

									echo '
										<script>
											window.parent.location.href = "' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";
										</script>
									';

									die();

									$app->redirect(JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid));
									return true;
								}
							}
						} elseif ($step_access_courses == "0") {
							// students
							if ($selected_course == -1) {
								// any course
								if ($this->isValidCustomer()) {
									include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
									$model_programs = new guruModelguruProgram();
									JFactory::getApplication()->input->set("cid", intval($course_id));
									$model_programs->enroll();
								} else {
									$app = JFactory::getApplication();
									$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

									$app->redirect(JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid));
									return true;
								}
							} else {
								$user_courses = $this->getUserCourses();
								$selected_course = explode("|", $selected_course);
								$selected_course = array_filter($selected_course);

								if (isset($user_courses) && count($user_courses) > 0) {
									$exist = false;
									foreach ($user_courses as $key => $value) {
										if (in_array($key, $selected_course)) {
											$exist = true;
											break;
										}
									}

									if ($exist) {
										include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
										$model_programs = new guruModelguruProgram();
										JFactory::getApplication()->input->set("cid", intval($course_id));
										$model_programs->enroll();
									} else {
										$app = JFactory::getApplication();
										$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

										/*$app->redirect(JURI::root()."index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=".intval($course_id)."&Itemid=".intval($Itemid));
										return true;*/

										echo '<script type="text/javascript" language="javascript">';
										echo 	'window.parent.location.href="' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";';
										echo '</script>';
										die();
									}
								} else {
									$app = JFactory::getApplication();
									$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

									/*$app->redirect(JURI::root()."index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=".intval($course_id)."&Itemid=".intval($Itemid));
									return true;*/

									echo '<script type="text/javascript" language="javascript">';
									echo 	'window.parent.location.href="' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";';
									echo '</script>';
									die();
								}
							}
						}
					}
				}
			}

			echo '<script type="text/javascript" language="javascript">';
			echo 	'window.parent.location.reload(true);';
			echo '</script>';
			die();
		} else {
			$user = JFactory::getUser();

			if ($user->id > 0) {
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruprofile"];

				if (intval($itemid_seo) > 0) {
					$Itemid = intval($itemid_seo);
				} else {
					$itemid_menu = $helper->getStudentMenuItem(intval($user->id));

					if (intval($itemid_menu) > 0) {
						$Itemid = intval($itemid_menu);
					}
				}

				$link = "index.php?option=com_guru&view=guruProfile&task=edit&Itemid=" . intval($Itemid);

				$student_redirect = $this->getStudentRedirectUrl();

				if (trim($student_redirect) != "") {
					$this->setRedirect(trim($student_redirect));
					return true;
				}
			} else {
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["gurulogin"];

				if (intval($itemid_seo) > 0) {
					$Itemid = intval($itemid_seo);
				}

				$link = "index.php?option=com_guru&view=guruLogin&Itemid=" . intval($Itemid);

				$student_redirect = $this->getStudentRedirectUrl();

				if (trim($student_redirect) != "") {
					$this->setRedirect(trim($student_redirect));
					return true;
				}
			}
		}

		if (JLanguageMultilang::isEnabled()) {
			$link .= '&lang=' . $lang;
		}

		$this->setRedirect(JRoute::_($link, false));
	}

	function getStudentRedirectUrl()
	{
		$db = JFactory::getDbo();

		$sql = "select `student_params` from #__guru_config";
		$db->setQuery($sql);
		$db->query();
		$student_params = $db->loadColumn();
		$student_params = @$student_params["0"];
		$student_params = json_decode($student_params, true);

		if (isset($student_params["student_redirect"]) && trim($student_params["student_redirect"]) != "") {
			return trim($student_params["student_redirect"]);
		} else {
			return "";
		}
	}

	function getUserCourses()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;
		$sql = "select bc.course_id from #__guru_buy_courses bc, #__guru_order o where bc.userid=" . intval($user_id) . " and bc.order_id=o.id and o.status='Paid'";
		$db->setQuery($sql);
		$result = $db->loadAssocList("course_id");

		return $result;
	}

	function userInGroups($groups)
	{
		$user = JFactory::getUser();
		$user_groups = $user->groups;

		if (!is_array($groups)) {
			$groups = explode(",", $groups);
		}

		if (is_array($user_groups) && count($user_groups) == 0) {
			$db = JFactory::getDbo();
			$sql = "select group_id from #__user_usergroup_map where user_id=" . intval($user->id);
			$db->setQuery($sql);
			$user_groups = $db->loadColumn();
		}

		$intersect = array_intersect($user_groups, $groups);

		if (isset($intersect) && count($intersect) > 0) {
			return true;
		}
		return false;
	}

	function isValidCustomer()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$user_id = $user->id;

		$sql = "select count(*) from #__guru_buy_courses bc, #__guru_customer c where bc.userid=" . intval($user_id) . " and c.id=" . intval($user_id);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result > 0) {
			return true;
		} else {
			return false;
		}
	}


	function save()
	{
		$app = JFactory::getApplication();
		$Itemid = $app->input->get('Itemid', 0, "raw");
		$lang = $app->getLanguage()->getTag();

		$helper = new guruHelper();
		$itemid_seo = $helper->getSeoItemid();
		$itemid_seo = @$itemid_seo["guruprograms"];

		if (intval($itemid_seo) > 0) {
			$Itemid = intval($itemid_seo);
		}

		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$configs = $db->loadAssocList();

		$show_captcha = $configs["0"]["captcha"];
		$course_id = intval(JFactory::getApplication()->input->get("course_id", "", "raw"));

		if ($show_captcha == "1") {
			$usersParams = JComponentHelper::getParams('com_users');
			$user_captcha = $usersParams->get("captcha");

			if (!isset($user_captcha) || $user_captcha == "0") {
				$globalConfigs = JFactory::getConfig();
				$user_captcha = $globalConfigs->get("captcha");

				if (!isset($user_captcha) || $user_captcha == "0") {
					$user_captcha = "recaptcha";
				}
			}

			$plugin = JPluginHelper::getPlugin('captcha', $user_captcha);

			if (isset($plugin->params)) {
				$params = new JRegistry($plugin->params);
				$public_key = $params->get("public_key", "");
				$private_key = $params->get("private_key", "");

				if (trim($public_key) != "" && trim($private_key) != "") {
					$username = JFactory::getApplication()->input->get("username", "", "raw");
					$email = JFactory::getApplication()->input->get("email", "", "raw");
					$firstname = JFactory::getApplication()->input->get("firstname", "", "raw");
					$lastname = JFactory::getApplication()->input->get("lastname", "", "raw");
					$company = JFactory::getApplication()->input->get("company", "", "raw");
					$id = JFactory::getApplication()->input->get("id", "0", "raw");

					$session = JFactory::getSession();
					$registry = $session->get('registry');

					$registry->set('username', $username);
					$registry->set('email', $email);
					$registry->set('firstname', $firstname);
					$registry->set('lastname', $lastname);
					$registry->set('company', $company);

					$g_recaptcha_response = JFactory::getApplication()->input->get("g-recaptcha-response", "", "raw");
					$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $private_key . "&response=" . $g_recaptcha_response . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
					$response = json_decode($response, true);

					if ($response["success"] === false) {
						$msg = JText::_('GURU_USERNAME_NOT_VALID_CAPTCHA');
						$return = JFactory::getApplication()->input->get("returnpage", "", "raw");

						$helper = new guruHelper();
						$itemid_seo = $helper->getSeoItemid();
						$itemid_seo = @$itemid_seo["gurulogin"];

						if (intval($itemid_seo) > 0) {
							$Itemid = intval($itemid_seo);
						}

						$link = "index.php?option=com_guru&view=guruLogin&returnpage=" . $return . "&task=register&Itemid=" . $Itemid . "&cid=" . $course_id;
						$this->setRedirect($link, $msg, 'error');
						return false;
					}
				}
			} else {
				$msg = JText::_('GURU_USERNAME_NOT_VALID_CAPTCHA');
				$return = JFactory::getApplication()->input->get("returnpage", "", "raw");

				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["gurulogin"];

				if (intval($itemid_seo) > 0) {
					$Itemid = intval($itemid_seo);
				}

				$link = "index.php?option=com_guru&view=guruLogin&returnpage=" . $return . "&task=register&Itemid=" . $Itemid . "&cid=" . $course_id;
				$this->setRedirect($link, $msg, 'error');
				return false;
			}
		}

		$model = $this->getModel("guruLogin");
		$return_page = JFactory::getApplication()->input->get("returnpage", "", "raw");
		$validate = $model->isNewUser();

		if ($validate === FALSE) {
			$msg = JText::_('GURU_USERNAME_EMAIL_UNIQUE');
			$return = JFactory::getApplication()->input->get("returnpage", "", "raw");

			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];

			if (intval($itemid_seo) > 0) {
				$Itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruLogin&returnpage=" . $return . "&task=register&Itemid=" . $Itemid . "&cid=" . $course_id;
			$this->setRedirect($link, $msg);
			return false;
		} else {
			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruprofile"];

			if (intval($itemid_seo) > 0) {
				$Itemid = intval($itemid_seo);
			}
		}

		$return = $model->store();

		if ($return) {
			global $app;
			$options = array();
			$options['remember'] = JFactory::getApplication()->input->get('remember', false, "raw");

			$username = JFactory::getApplication()->input->get("username", "", "raw");
			$password = JFactory::getApplication()->input->get("password", "", "raw");

			$credentials = array();
			$credentials['username'] = trim($username);
			$credentials['password'] = trim($password);
			$credentials['email'] = JFactory::getApplication()->input->get("email", "", "raw");

			$session = JFactory::getSession();
			$registry = $session->get('registry');

			$registry->set('username', "");
			$registry->set('email', "");
			$registry->set('firstname', "");
			$registry->set('lastname', "");
			$registry->set('company', "");

			$registered_user = JFactory::getApplication()->input->get("registered_user", "", "raw");

			$user = new JUser();
			$params = JComponentHelper::getParams('com_users');
			$useractivation = $params->get('useractivation');

			if ($useractivation == 0) {	// None
				$err = $app->login($credentials, $options);
			} elseif (isset($configs["0"]["auto_approve"]) && $configs["0"]["auto_approve"] == "1") {
				$err = $app->login($credentials, $options);
			} else {
				$this->sendJoomlaEmail($return["1"]);

				if ($return_page == "open_lesson") {
					$lesson_id = JFactory::getApplication()->input->get("lesson_id", "0", "raw");

					if (intval($lesson_id) != 0) {
						$sql = "select type_id from #__guru_mediarel where type='dtask' and media_id=" . intval($lesson_id) . " and type_id <> '0'";
						$db->setQuery($sql);
						$module_id = $db->loadColumn();
						$module_id = @$module_id["0"];

						$sql = "select pid from #__guru_days where id=" . intval($module_id);
						$db->setQuery($sql);
						$course_id = $db->loadColumn();
						$course_id = @$course_id["0"];

						$msg = JText::_("GURU_STUDENT_SAVED_PENDING");

						$session = JFactory::getSession();
						$registry = $session->get('registry');
						$registry->set('joomlamessage', $msg);

						echo '<script type="text/javascript">';
						echo 'window.parent.location.reload(true);';
						echo '</script>';
						die();
					}
				} else {
					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["gurulogin"];

					if (intval($itemid_seo) > 0) {
						$Itemid = intval($itemid_seo);
					}

					$link = "index.php?option=com_guru&view=guruLogin&Itemid=" . $Itemid;
					if (JLanguageMultilang::isEnabled()) {
						$link .= '&lang=' . $lang;
					}
					$this->setRedirect(JRoute::_($link), JText::_("GURU_STUDENT_SAVED_PENDING"), "notice");
				}
				return true;
			}

			if (intval($return_page) != "0") {
				$return_page = "guruprograms";
			}

			if ($return_page == "checkout") {
				$user = JFactory::getUser();
				$user_id = $user->id;

				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["gurubuy"];

				if (intval($itemid_seo) > 0) {
					$Itemid = intval($itemid_seo);
				}

				if (!$this->buyCourses($user_id)) {
					$link = "index.php?option=com_guru&view=guruBuy&task=checkout&from=login&Itemid=" . $Itemid;
				} else {
					$link = "index.php?option=com_guru&view=guruBuy&Itemid=" . $Itemid;
				}
			} elseif ($return_page == "mycourses") {
				$link = "index.php?option=com_guru&view=guruorders&layout=mycourses&Itemid=" . $Itemid;
			} elseif ($return_page == "myorders") {
				$link = "index.php?option=com_guru&view=guruorders&layout=myorders&Itemid=" . $Itemid;
			} elseif ($return_page == "mycertificates") {
				$link = "index.php?option=com_guru&view=guruorders&layout=mycertificates&Itemid=" . $Itemid;
			} elseif ($return_page == "enroll") {
				$link = "index.php?option=com_guru&view=guruPrograms&task=enroll&cid=" . $course_id . "&Itemid=" . $Itemid . "&registered_user=" . $registered_user;
			} elseif ($return_page == "authorprofile") {
				$helper = new guruHelper();
				$itemid_seo = $helper->getSeoItemid();
				$itemid_seo = @$itemid_seo["guruprofile"];

				if (intval($itemid_seo) > 0) {
					$Itemid = intval($itemid_seo);
				} else {
					$user = JFactory::getUser();
					$user_id = $user->id;

					$itemid_menu = $helper->getTeacherMenuItem(intval($user_id));

					if (intval($itemid_menu) > 0) {
						$Itemid = intval($itemid_menu);
					}
				}

				$teacher_redirect = $this->getTeacherRedirectUrl();
				if (trim($teacher_redirect) != "") {
					$link = trim($teacher_redirect);
				} else {
					$link = "index.php?option=com_guru&view=guruauthor&task=authorprofile&Itemid=" . intval($Itemid);
				}
			} elseif ($return_page == "registerforlogout") {
				$view_get = JFactory::getApplication()->input->get("view", "", "raw");
				$email_r = JFactory::getApplication()->input->get("e", "", "raw");
				$catid = JFactory::getApplication()->input->get("catid", "", "raw");
				$module_lesson = JFactory::getApplication()->input->get("module", "", "raw");
				$lesson_id = JFactory::getApplication()->input->get("cid", "", "raw");
				if (($view_get == "guruTasks" || $view_get == "gurutasks") && $email_r == "1") {
					$link = "index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $module_lesson . "&cid=" . $lesson_id;
				} else {
					$link = "index.php?option=com_guru";
				}
			} elseif ($return_page == "guruprograms") {
				$db = JFactory::getDBO();
				$user = JFactory::getUser();
				$user_id = $user->id;
				$courses = intval($course_id) . "-0.0-1";
				$amount = 0;
				$buy_date = date("Y-m-d H:i:s");
				$plan_id = "1";
				$order_expiration = "0000-00-00 00:00:00";
				$jnow = new JDate('now');
				$current_date_string = $jnow->toSQL(true);

				$sql = "select count(*) from #__guru_buy_courses where userid=" . intval($user_id) . " and course_id=" . intval($course_id) . " and order_id='0' and expired_date < '" . $current_date_string . "'";
				$db->setQuery($sql);
				$result = $db->loadResult();

				if ($result == 0) { // add a new license
					$sql = "insert into #__guru_buy_courses (userid, order_id, course_id, price, buy_date, expired_date, plan_id, email_send) values (" . $user_id . ", 0 , " . $course_id . ", '" . $amount . "', '" . $buy_date . "', '" . $order_expiration . "', '" . $plan_id . "', 0)";
					$db->setQuery($sql);
					$db->execute();
					$msg = JText::_("GURU_ENROLL_SUCCESSFULLY");
				} else {
					$msg = JText::_("GURU_ALREADY_ENROLLED");
				}

				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$registry->set('joomlamessage', $msg);

				echo '<script type="text/javascript">';
				echo 'window.parent.location.reload(true);';
				echo '</script>';
				die();
			} elseif ($return_page == "open_lesson") {
				$lesson_id = JFactory::getApplication()->input->get("lesson_id", "0", "raw");

				if (intval($lesson_id) != 0) {
					$sql = "select type_id from #__guru_mediarel where type='dtask' and media_id=" . intval($lesson_id) . " and type_id <> '0'";
					$db->setQuery($sql);
					$module_id = $db->loadColumn();
					$module_id = @$module_id["0"];

					$sql = "select pid from #__guru_days where id=" . intval($module_id);
					$db->setQuery($sql);
					$course_id = $db->loadColumn();
					$course_id = @$course_id["0"];

					$app = JFactory::getApplication();
					$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

					$helper = new guruHelper();
					$itemid_seo = $helper->getSeoItemid();
					$itemid_seo = @$itemid_seo["guruprograms"];

					if (intval($itemid_seo) > 0) {
						$Itemid = intval($itemid_seo);
					}

					if (intval($course_id) > 0) {
						$sql = "select chb_free_courses, step_access_courses, groups_access, selected_course from #__guru_program where id=" . intval($course_id);
						$db->setQuery($sql);
						$course_access_details = $db->loadAssocList();

						$chb_free_courses = $course_access_details["0"]["chb_free_courses"];
						$step_access_courses = $course_access_details["0"]["step_access_courses"];
						$groups_access = $course_access_details["0"]["groups_access"];
						$selected_course = $course_access_details["0"]["selected_course"];

						if ($chb_free_courses == "1") {
							// free
							if ($step_access_courses == "1") {
								// members
								if (trim($groups_access) == "") {
									// free for all user groups
									include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
									$model_programs = new guruModelguruProgram();
									JFactory::getApplication()->input->set("cid", intval($course_id));
									$model_programs->enroll();

									echo '<script type="text/javascript">';
									echo 'window.parent.location.reload(true);';
									echo '</script>';
									die();
								} else {
									$groups_access = explode(",", $groups_access);

									if ($this->userInGroups($groups_access)) {
										include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
										$model_programs = new guruModelguruProgram();
										JFactory::getApplication()->input->set("cid", intval($course_id));
										$model_programs->enroll();

										echo '<script type="text/javascript">';
										echo 'window.parent.location.reload(true);';
										echo '</script>';
										die();
									} else {
										$app = JFactory::getApplication();
										$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

										$helper = new guruHelper();
										$itemid_seo = $helper->getSeoItemid();
										$itemid_seo = @$itemid_seo["guruprograms"];

										if (intval($itemid_seo) > 0) {
											$Itemid = intval($itemid_seo);
										}

										echo '<script type="text/javascript">';
										echo 'window.parent.location.href="' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";';
										echo '</script>';
										die();

										return true;
									}
								}
							} elseif ($step_access_courses == "0") {
								// students
								if ($selected_course == -1) {
									// any course
									if ($this->isValidCustomer()) {
										include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
										$model_programs = new guruModelguruProgram();
										JFactory::getApplication()->input->set("cid", intval($course_id));
										$model_programs->enroll();

										echo '<script type="text/javascript">';
										echo 'window.parent.location.reload(true);';
										echo '</script>';
										die();
									} else {
										$app = JFactory::getApplication();
										$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

										$helper = new guruHelper();
										$itemid_seo = $helper->getSeoItemid();
										$itemid_seo = @$itemid_seo["guruprograms"];

										if (intval($itemid_seo) > 0) {
											$Itemid = intval($itemid_seo);
										}

										echo '<script type="text/javascript">';
										echo 'window.parent.location.href="' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";';
										echo '</script>';
										die();

										return true;
									}
								} else {
									$user_courses = $this->getUserCourses();
									$selected_course = explode("|", $selected_course);
									$selected_course = array_filter($selected_course);

									if (isset($user_courses) && count($user_courses) > 0) {
										$exist = false;
										foreach ($user_courses as $key => $value) {
											if (in_array($key, $selected_course)) {
												$exist = true;
												break;
											}
										}

										if ($exist) {
											include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "guruprogram.php");
											$model_programs = new guruModelguruProgram();
											JFactory::getApplication()->input->set("cid", intval($course_id));
											$model_programs->enroll();

											echo '<script type="text/javascript">';
											echo 'window.parent.location.reload(true);';
											echo '</script>';
											die();
										} else {
											$app = JFactory::getApplication();
											$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

											$helper = new guruHelper();
											$itemid_seo = $helper->getSeoItemid();
											$itemid_seo = @$itemid_seo["guruprograms"];

											if (intval($itemid_seo) > 0) {
												$Itemid = intval($itemid_seo);
											}

											echo '<script type="text/javascript">';
											echo 'window.parent.location.href="' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";';
											echo '</script>';
											die();
										}
									} else {
										$app = JFactory::getApplication();
										$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");

										$helper = new guruHelper();
										$itemid_seo = $helper->getSeoItemid();
										$itemid_seo = @$itemid_seo["guruprograms"];

										if (intval($itemid_seo) > 0) {
											$Itemid = intval($itemid_seo);
										}

										echo '<script type="text/javascript">';
										echo 'window.parent.location.href="' . JURI::root() . "index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id=" . intval($course_id) . "&Itemid=" . intval($Itemid) . '";';
										echo '</script>';
										die();
									}
								}
							}
						} elseif ($chb_free_courses == "0") {
							echo '<script type="text/javascript">';
							echo 'window.parent.location.reload(true);';
							echo '</script>';
							die();
						}
					}

					return true;
				}
			}

			if (JLanguageMultilang::isEnabled()) {
				$link .= '&lang=' . $lang;
			}

			$msg = JText::_('DSCUSTOMERSAVED');
			$this->setRedirect(JRoute::_($link, false), $msg);
		} else {
			$msg = JText::_('DSCUSTOMERSAVEERR');
			$return = JFactory::getApplication()->input->get("returnpage", "", "raw");

			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["gurulogin"];

			if (intval($itemid_seo) > 0) {
				$Itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruLogin&returnpage=checkout&Itemid=" . $Itemid;
			if (JLanguageMultilang::isEnabled()) {
				$link .= '&lang=' . $lang;
			}

			$this->setRedirect(JRoute::_($link), $msg, "notice");
		}
	}

	function saveauthor()
	{
		$Itemid = JFactory::getApplication()->input->get('Itemid', 0, "raw");
		$auid = JFactory::getApplication()->input->get("auid", "0", "raw");
		$name = JFactory::getApplication()->input->get("name", "", "raw");
		$username = JFactory::getApplication()->input->get("username", "", "raw");
		$email = JFactory::getApplication()->input->get("email", "", "raw");
		$author_title = JFactory::getApplication()->input->get("author_title", "", "raw");
		$website = JFactory::getApplication()->input->get("website", "", "raw");
		$blog = JFactory::getApplication()->input->get("blog", "", "raw");
		$facebook = JFactory::getApplication()->input->get("facebook", "0", "raw");
		$twitter = JFactory::getApplication()->input->get("twitter", "0", "raw");
		$full_bio = JFactory::getApplication()->input->get("full_bio", "", "raw");
		$images = JFactory::getApplication()->input->get("images", "", "raw");

		$session = JFactory::getSession();
		$registry = $session->get('registry');

		$registry->set('name', $name);
		$registry->set('username', $username);
		$registry->set('email', $email);
		$registry->set('author_title', $author_title);
		$registry->set('website', $website);
		$registry->set('blog', $blog);
		$registry->set('facebook', $facebook);
		$registry->set('twitter', $twitter);
		$registry->set('full_bio', $full_bio);
		$registry->set('images', $images);

		if (intval($auid) == 0) {
			$db = JFactory::getDBO();
			$sql = "select * from #__guru_config";
			$db->setQuery($sql);
			$configs = $db->loadAssocList();

			$show_captcha = $configs["0"]["captcha"];

			if ($show_captcha == "1") {
				$usersParams = JComponentHelper::getParams('com_users');
				$user_captcha = $usersParams->get("captcha");

				if (!isset($user_captcha) || $user_captcha == "0") {
					$globalConfigs = JFactory::getConfig();
					$user_captcha = $globalConfigs->get("captcha");

					if (!isset($user_captcha) || $user_captcha == "0") {
						$user_captcha = "recaptcha";
					}
				}

				$plugin = JPluginHelper::getPlugin('captcha', $user_captcha);

				if (isset($plugin->params)) {
					$params = new JRegistry($plugin->params);
					$public_key = $params->get("public_key", "");
					$private_key = $params->get("private_key", "");

					if (trim($public_key) != "" && trim($private_key) != "") {
						$g_recaptcha_response = JFactory::getApplication()->input->get("g-recaptcha-response", "", "raw");
						$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $private_key . "&response=" . $g_recaptcha_response . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
						$response = json_decode($response, true);

						if ($response["success"] === false) {
							$msg = JText::_('GURU_USERNAME_NOT_VALID_CAPTCHA');
							$return = JFactory::getApplication()->input->get("returnpage", "", "request");
							$link = "index.php?option=com_guru&view=guruAuthor&task=authorregister&returnpage=" . $return . "&Itemid=" . $Itemid;
							$this->setRedirect($link, $msg, 'error');
							return false;
						}
					}
				} else {
					$msg = JText::_('GURU_USERNAME_NOT_VALID_CAPTCHA');
					$return = JFactory::getApplication()->input->get("returnpage", "", "request");
					$link = "index.php?option=com_guru&view=guruAuthor&task=authorregister&returnpage=" . $return . "&Itemid=" . $Itemid;
					$this->setRedirect($link, $msg, 'error');
					return false;
				}
			}
		}

		$model = $this->getModel("guruLogin");
		$return_page = JFactory::getApplication()->input->get("returnpage", "", "raw");
		$db = JFactory::getDBO();
		$sql = "select * from #__guru_config";
		$db->setQuery($sql);
		$configs = $db->loadAssocList();

		$allow_teacher_action = json_decode($configs["0"]["st_authorpage"]); //take all the allowed action from administator settings
		$teacher_aprove = @$allow_teacher_action->teacher_aprove; //allow or not aprove teacher

		$validate = $model->isNewUser();

		if ($validate === FALSE) {
			$msg = JText::_('GURU_USERNAME_EMAIL_UNIQUE');
			$return = JFactory::getApplication()->input->get("returnpage", "", "raw");
			$link = "index.php?option=com_guru&view=guruAuthor&task=authorregister&returnpage=" . $return . "&Itemid=" . $Itemid;
			$this->setRedirect($link, $msg, 'error');
			return false;
		}

		$return = $model->store();
		$msg = "";
		$link = "";
		$notice = "";

		if ($return["0"]) {
			$session = JFactory::getSession();
			$registry = $session->get('registry');

			$registry->set('name', "");
			$registry->set('username', "");
			$registry->set('email', "");
			$registry->set('author_title', "");
			$registry->set('website', "");
			$registry->set('blog', "");
			$registry->set('facebook', "");
			$registry->set('twitter', "");
			$registry->set('full_bio', "");
			$registry->set('images', "");

			$msg = "";
			if ($teacher_aprove == 1) { // NO
				$msg = JText::_('GURU_TEACHER_SAVED_PENDING');
			} else {
				$msg = JText::_('GURU_TEACHER_SAVED_ACTIVATED');
			}

			$userr = JFactory::getUser();
			$logged_userid = $userr->get('id');

			if ($logged_userid) {
				$msg = JText::_('GURU_AU_AUTHOR_DETAILS_SAVED');
			}

			$helper = new guruHelper();
			$itemid_seo = $helper->getSeoItemid();
			$itemid_seo = @$itemid_seo["guruauthor"];

			if (intval($itemid_seo) > 0) {
				$Itemid = intval($itemid_seo);
			}

			$link = "index.php?option=com_guru&view=guruauthor&task=authormycourses&layout=authormycourses&Itemid=" . $Itemid;
			$notice = "Success";
			//$this->setRedirect(JRoute::_($link, false), $msg, "Success");
		} else {
			$msg = JText::_('DSCUSTOMERSAVEERR');
			$return = JFactory::getApplication()->input->get("returnpage", "", "raw");
			$link = "index.php?option=com_guru&view=guruAuthor&task=authorregister&returnpage=" . $return . "&Itemid=" . $Itemid;
			$notice = "notice";
			//$this->setRedirect(JRoute::_($link, false), $msg, "notice");
		}

		//we need to login, first we check if we already are logged in
		$userr = JFactory::getUser();
		$logged_userid = $userr->get('id');

		if (isset($logged_userid) && $logged_userid == 0) {
			if ($teacher_aprove == 1) { // NO
				$user_status = $this->sendJoomlaEmail($return["1"]);
				if (intval($user_status) != 0) {
					global $app;
					$options = array();
					$options['remember'] = JFactory::getApplication()->input->get('remember', false, "raw");

					$username = JFactory::getApplication()->input->get("username", "", "raw");
					$password = JFactory::getApplication()->input->get("password", "", "raw");

					$credentials = array();
					$credentials['username'] = trim($username);
					$credentials['password'] = trim($password);
					$credentials['email'] = JFactory::getApplication()->input->get("email", '', "raw");

					if ($return["1"]->enabled == 1) {
						$err = $app->login($credentials, $options);
					}
				}
			}
		}

		if ($teacher_aprove == 0) { // YES
			global $app;
			$options = array();
			$options['remember'] = JFactory::getApplication()->input->get('remember', false, "raw");

			$username = JFactory::getApplication()->input->get("username", "", "raw");
			$password = JFactory::getApplication()->input->get("password", "", "raw");

			$credentials = array();
			$credentials['username'] = trim($username);
			$credentials['password'] = trim($password);
			$credentials['email'] = JFactory::getApplication()->input->get("email", '', "raw");

			if ($return["1"]->enabled == 1) {
				$err = $app->login($credentials, $options);
			}
		}

		$this->setRedirect(JRoute::_($link), $msg, $notice);
	}

	function sendJoomlaEmail($data)
	{
		$lang = JFactory::getLanguage();
		$extension = 'com_users';
		$base_dir = JPATH_SITE;
		$language_tag = ''; //'en-GB';
		$lang->load($extension, $base_dir, $language_tag, true);

		$data = (array)$data;
		$user = $data;
		$params = JComponentHelper::getParams('com_users');
		// Prepare the data for the user object.
		//$data['email'] = JStringPunycode::emailToPunycode($data['email1']);
		$useractivation = $params->get('useractivation');
		$sendpassword = $params->get('sendpassword', 1);

		$config = JFactory::getConfig();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Compile the notification mail values.
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['siteurl'] = JUri::root();

		// Handle account activation/confirmation emails.
		if ($useractivation == 2) {
			// Set the link to confirm the user email.
			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'] . "&g=1", false);

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword) {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			} else {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}

			$emailBody = str_replace("\n\r", "<br/>", $emailBody);
			$emailBody = str_replace("\n", "<br/>", $emailBody);
			$emailBody = str_replace("\r", "<br/>", $emailBody);
		} elseif ($useractivation == 1) {
			// Set the link to activate the user account.
			$uri = JUri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'] . "&g=1", false);

			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword) {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			} else {
				$emailBody = JText::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}

			$emailBody = str_replace("\n\r", "<br/>", $emailBody);
			$emailBody = str_replace("\n", "<br/>", $emailBody);
			$emailBody = str_replace("\r", "<br/>", $emailBody);
		} else {
			$db = JFactory::getDBO();
			$sql = "select template_emails from #__guru_config";
			$db->setQuery($sql);
			$configs = $db->loadAssocList();
			$template_emails = $configs["0"]["template_emails"];
			$template_emails = json_decode($template_emails, true);

			$pending_teacher_subject = $template_emails["pending_teacher_subject"];
			$pending_teacher_body = $template_emails["pending_teacher_body"];

			$name = JFactory::getApplication()->input->get("name", "", "raw");
			$app = JFactory::getApplication();
			$site_name = $app->getCfg('sitename');
			$username = JFactory::getApplication()->input->get("username", "", "raw");
			$password = JFactory::getApplication()->input->get("password", "", "raw");
			$password = $sendpassword ? $password : '********';

			$pending_teacher_subject = str_replace("[AUTHOR_NAME]", $name, $pending_teacher_subject);
			$pending_teacher_subject = str_replace("[SITE_NAME]", $site_name, $pending_teacher_subject);
			$pending_teacher_subject = str_replace("[USERNAME]", $username, $pending_teacher_subject);
			$pending_teacher_subject = str_replace("[PASSWORD]", $password, $pending_teacher_subject);

			$pending_teacher_body = str_replace("[AUTHOR_NAME]", $name, $pending_teacher_body);
			$pending_teacher_body = str_replace("[SITE_NAME]", $site_name, $pending_teacher_body);
			$pending_teacher_body = str_replace("[USERNAME]", $username, $pending_teacher_body);
			$pending_teacher_body = str_replace("[PASSWORD]", $password, $pending_teacher_body);

			$emailSubject = $pending_teacher_subject;
			$emailBody = $pending_teacher_body;
		}

		// Send the registration email.
		if (trim($emailSubject) != "" && trim($emailBody) != "") {
			if ($data['guru_teacher'] == "1") {
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody, true);
			} elseif ($data['guru_teacher'] == "2") {
				$send_teacher_email_teacher_pending = isset($template_emails["send_teacher_email_teacher_pending"]) ? $template_emails["send_teacher_email_teacher_pending"] : 1;

				if ($send_teacher_email_teacher_pending) {
					$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody, true);
				}
			}
		}

		$email_name = "user-registration";

		if ($data['guru_teacher'] == "1") {
			$email_name = "student-registration";
		} elseif ($data['guru_teacher'] == "2") {
			$email_name = "teacher-registration";
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->insert('#__guru_logs');
		$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
		$query->values(intval($data["id"]) . ',' . $db->quote(trim($email_name)) . ',' . '0' . ',' . $db->quote(trim($data['email'])) . ',' . $db->quote(trim($emailSubject)) . ',' . $db->quote(trim($emailBody)) . ',' . $db->quote(trim($data["registerDate"])) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
		$db->setQuery($query);
		$db->execute();

		// Send Notification mail to administrators
		if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)) {
			$emailSubject = JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBodyAdmin = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);

			// Get all admin users
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = ' . 1);

			$db->setQuery($query);

			try {
				$rows = $db->loadObjectList();
			} catch (RuntimeException $e) {
				$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
				return false;
			}

			// Send mail to all superadministrators id
			foreach ($rows as $row) {
				if (trim($emailSubject) != "" && trim($emailBodyAdmin) != "") {
					$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin, 1);
				}

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->clear();
				$query->insert('#__guru_logs');
				$query->columns(array($db->quoteName('userid'), $db->quoteName('emailname'), $db->quoteName('emailid'), $db->quoteName('to'), $db->quoteName('subject'), $db->quoteName('body'), $db->quoteName('buy_date'), $db->quoteName('send_date'), $db->quoteName('buy_type')));
				$query->values(intval($data["id"]) . ',' . $db->quote(trim($email_name)) . ',' . '0' . ',' . $db->quote(trim($row->email)) . ',' . $db->quote(trim($emailSubject)) . ',' . $db->quote(trim($emailBodyAdmin)) . ',' . $db->quote(trim($data["registerDate"])) . ',' . $db->quote(trim(date("Y-m-d H:i:s"))) . ',' . "''");
				$db->setQuery($query);
				$db->execute();

				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					return false;
				}
			}
		}

		// Check for an error.
		if ($return !== true) {
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = JFactory::getDbo();
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('block') . ' = ' . (int) 0)
				->where($db->quoteName('sendEmail') . ' = ' . (int) 1);
			$db->setQuery($query);

			try {
				$sendEmail = $db->loadColumn();
			} catch (RuntimeException $e) {
				$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
				return false;
			}

			if (count($sendEmail) > 0) {
				$jdate = new JDate;

				// Build the query to add the messages
				foreach ($sendEmail as $userid) {
					$values = array($db->quote($userid), $db->quote($userid), $db->quote($jdate->toSql(true)), $db->quote(JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')), $db->quote(JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])));
					$query->clear()
						->insert($db->quoteName('#__messages'))
						->columns($db->quoteName(array('user_id_from', 'user_id_to', 'date_time', 'subject', 'message')))
						->values(implode(',', $values));
					$db->setQuery($query);

					try {
						$db->execute();
					} catch (RuntimeException $e) {
						$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
						return false;
					}
				}
			}
			return false;
		}

		if ($useractivation == 1) {
			return "useractivate";
		} elseif ($useractivation == 2) {
			return "adminactivate";
		} else {
			return $user["id"];
		}
	}

	function saveAuthoredit()
	{
		$id = JFactory::getApplication()->input->get("auid", "0", "raw");
		$model = $this->getModel("guruLogin");
		$ress = $model->update($id);
		if ($ress) {
			$msg = JText::_('GURU_AU_AUTHOR_DETAILS_SAVED');
			$link = "index.php?option=com_guru&view=guruauthor&layout=authorprofile&Itemid=" . $Itemid;
			$this->setRedirect(JRoute::_($link, false), $msg, "Success");
		}
	}

	function uploadAjaxImage()
	{
		include_once(JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_guru" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "fileuploader.php");
		die();
	}

	function getTeacherRedirectUrl()
	{
		$db = JFactory::getDbo();

		$sql = "select `st_authorpage` from #__guru_config";
		$db->setQuery($sql);
		$db->query();
		$st_authorpage = $db->loadColumn();
		$st_authorpage = @$st_authorpage["0"];
		$st_authorpage = json_decode($st_authorpage, true);

		if (isset($st_authorpage["teacher_redirect"]) && trim($st_authorpage["teacher_redirect"]) != "") {
			return trim($st_authorpage["teacher_redirect"]);
		} else {
			return "";
		}
	}
};
