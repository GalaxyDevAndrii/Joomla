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
jimport('joomla.html.pagination');
JHTML::_('behavior.tooltip');
JHtml::_('behavior.framework');

$doc = JFactory::getDocument();
$db = JFactory::getDbo();

$doc->addStyleSheet("components/com_guru/css/general.css");
$data_post = JFactory::getApplication()->input->post->getArray();
$certificates = $this->certificates;
$teachers = $this->teachers;
$pageNav = $this->pagination;

?>

<script type="text/javascript" id="load-jquery">
	// When get this page content through Ajax Request all js files that supposed to be loaded by controller, are not loaded, so we have to load them with javascript only in case there are not already been loaded 
	var element = document.getElementById('load-jquery');
	if (typeof jQuery == 'undefined') {
		document.write('<link href="components/com_guru/css/bootstrap.min.css" rel="stylesheet">');
		document.write('<script src="components/com_guru/js/jquery_1_11_2.js"><\/script>');

	}
	element.parentNode.removeChild(element);
</script>

<script>
	function openWinCertificate1(t1, t2, t3, t4, t5, t6) {
		t1 = encodeURIComponent(t1);
		t2 = encodeURIComponent(t2);
		t3 = encodeURIComponent(t3);
		t4 = encodeURIComponent(t4);
		t5 = encodeURIComponent(t5);
		t6 = encodeURIComponent(t6);

		myWindow = window.open('<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruOrders&task=printcertificate&op=1&cn=' + t1 + '&an=' + t2 + '&id=' + t3 + '&cd=' + t4 + '&ci=' + t5 + '&tmpl=component&ct=' + t6, '', 'width=800,height=600, resizable = 0');
		myWindow.focus();
	}

	function openWinCertificate4(t1, t2, t3, t4, t5, t6) {
		t1 = encodeURIComponent(t1);
		t2 = encodeURIComponent(t2);
		t3 = encodeURIComponent(t3);
		t4 = encodeURIComponent(t4);
		t5 = encodeURIComponent(t5);
		t6 = encodeURIComponent(t6);

		myWindow = window.open('<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruOrders&task=savepdfcertificate&op=9&cn=' + t1 + '&an=' + t2 + '&id=' + t3 + '&cd=' + t4 + '&ci=' + t5 + '&tmpl=component&ct=' + t6, '', 'width=800,height=600, resizable = 0');
		myWindow.focus();
	}
</script>

<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
	<table style="width: 100%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
		<tr>
			<td>
				<input type="text" name="search_text" value="<?php if (isset($data_post['search_text'])) echo $data_post['search_text']; ?>" />
				<input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT'); ?>" />
			</td>

			<td>
				<select name="search_teacher" onchange="document.adminForm.submit();">
					<option value="0"> <?php echo JText::_("GURU_SELECT_TEACHER"); ?> </option>
					<?php
					if (isset($teachers) && count($teachers) > 0) {
						foreach ($teachers as $key => $value) {
							$selected = "";

							if (intval($value["id"]) == intval($data_post['search_teacher'])) {
								$selected = 'selected="selected"';
							}

							echo '<option ' . $selected . ' value="' . $value["id"] . '">' . $value["name"] . '</option>';
						}
					}
					?>
				</select>
			</td>
		<tr>
	</table>

	<div id="js-cpanel">
		<table class="table table-bordered table-striped adminlist">
			<thead>
				<tr>
					<th>#</th>
					<th><?php echo JText::_("GURU_STUDENT_NAME"); ?></th>
					<th><?php echo JText::_("GURU_PRODNAME"); ?></th>
					<th><?php echo JText::_("GURU_TEACH_NAME"); ?></th>
					<th><?php echo JText::_("GURU_TERM"); ?></th>
					<th><?php echo JText::_("GURU_LESSONS_COMPLETED"); ?></th>
					<th><?php echo JText::_("GURU_QUIZZES_PROGRESS"); ?></th>
					<th><?php echo JText::_("GURU_QUIZES_AVG_SCORE"); ?></th>
					<th><?php echo JText::_("GURU_FINAL_EXAM_SCORE"); ?></th>
					<th><?php echo JText::_("GURU_OPTIONS"); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if (isset($certificates) && count($certificates) > 0) {
					foreach ($certificates as $key => $certificate) {
						$course_details = $this->getCourseDetails(intval($certificate->course_id));
						$course_authors = $this->getCourseAuthor($course_details["0"]["author"]);
				?>
						<tr>
							<td>
								<?php
								echo $key + 1 + $pageNav->limitstart;
								?>
							</td>
							<td>
								<a href="index.php?option=com_guru&controller=guruCustomers&task=edit&cid[]=<?php echo intval($certificate->user_id); ?>">
									<?php
									echo $certificate->firstname . " " . $certificate->lastname;
									?>
								</a>
							</td>
							<td>
								<a href="index.php?option=com_guru&controller=guruPrograms&task=edit&cid[]=<?php echo intval($certificate->course_id); ?>">
									<?php
									echo $course_details["0"]["name"];
									?>
								</a>
							</td>
							<td>
								<?php
								if (isset($course_authors) && count($course_authors) > 0) {
									$authors_name_lists = array();
									$authors_name_lists_urls = array();

									foreach ($course_authors as $key_author => $value_author) {
										$authors_name_lists_urls[] = '<a href="index.php?option=com_guru&controller=guruAuthor&task=edit&id=' . intval($value_author["id"]) . '">' . $value_author["name"] . "</a>";
										$authors_name_lists[] = $value_author["name"];
									}

									echo implode(" | ", $authors_name_lists_urls);
								}
								?>
							</td>
							<td>
								<?php
								$certterm = $course_details["0"]["certificate_term"];
								$result = intval($course_details["0"]["id_final_exam"]);
								$id_final_exam = intval($course_details["0"]["id_final_exam"]);
								$avg_quizzes_cert = $course_details["0"]["avg_certc"];

								$sql = "SELECT `max_score` FROM #__guru_quiz WHERE `id`=" . intval($result);
								$db->setQuery($sql);
								$result_maxs = $db->loadResult();

								if ($certterm == 0) {
									$details = JText::_("GURU_NO_CERT_GIVEN");
								} elseif ($certterm == 1) {
									$details = JText::_("GURU_NO_CERT_GIVEN");
								} elseif ($certterm == 2) {
									$details = JText::_("GURU_MUST_COLMP_ALL_LESS");
								} elseif ($certterm == 3) {
									$details = JText::_("GURU_MUST_PASS_FE") . " " . $result_maxs . "%";
								} elseif ($certterm == 4) {
									$details = JText::_("GURU_MUST_PASS_QAVG") . " " . $avg_quizzes_cert . "%";
								} elseif ($certterm == 5) {
									$details = JText::_("GURU_CERT_TERM_FALFE");
								} elseif ($certterm == 6) {
									$details = JText::_("GURU_CERT_TERM_FALPQAVG") . " " . $avg_quizzes_cert . "%";
								} elseif ($certterm == 7) {
									if ($course_details["0"]["record_hour"] == "") {
										$course_details["0"]["record_hour"] = "00";
									}

									if ($course_details["0"]["record_min"] == "") {
										$course_details["0"]["record_min"] = "00";
									}

									$details = JText::_("GURU_CERT_TERM_TIME_RECORDING") . " " . $course_details["0"]["record_hour"] . ":" . $course_details["0"]["record_min"] . ":00";
								}

								echo $details;
								?>
							</td>

							<td>
								<?php
								$sql = "SELECT `completed` from #__guru_viewed_lesson WHERE `user_id` =" . intval($certificate->user_id) . " and `pid`=" . intval($course_details["0"]["id"]);
								$db->setQuery($sql);
								$completed_course = $db->loadColumn();
								$completed_course = @$completed_course["0"];

								if ($completed_course == 1) {
									echo '<span  style="color:#66CC00;">' . JText::_("GURU_YES") . '</span>';
								} else {
									echo '<span  style="color:#FF0000;">' . JText::_("GURU_NO") . '</span>';
								}
								?>
							</td>

							<td>
								<?php
								$resulthasq = $course_details["0"]["hasquiz"];
								$scores_avg_quizzes = 0;
								$scores_avg_quizzes_taken = 0;
								$nb_ofscores = 0;
								$nb_ofscores_taken = 0;

								// start calculate sum for all quizes from course------------------------------------
								$sql = "select mr.`media_id` from #__guru_mediarel mr, #__guru_days d where mr.`type`='dtask' and mr.`type_id`=d.`id` and d.`pid`=" . intval($course_details["0"]["id"]);
								$db->setQuery($sql);
								$lessons = $db->loadColumn();

								if (!isset($lessons) || count($lessons) == 0) {
									$lessons = array("0");
								}

								$sql = "select mr.`media_id` from #__guru_mediarel mr where mr.`layout`='12' and mr.`type`='scr_m' and mr.`type_id` in (" . implode(", ", $lessons) . ")";
								$db->setQuery($sql);
								$all_quizzes = $db->loadColumn();
								$all_quizzes_taken_by_user = array();

								$s = 0;
								$res = 0;

								if (isset($all_quizzes) && count($all_quizzes) > 0) {
									foreach ($all_quizzes as $key_quiz => $quiz_id) {
										$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($certificate->user_id) . " and quiz_id=" . intval($quiz_id) . " and pid=" . intval($course_details["0"]["id"]) . " ORDER BY id DESC LIMIT 0,1";
										$db->setQuery($sql);
										$result_q = $db->loadColumn();
										$res = @$result_q["0"];
										$s += $res;

										if (isset($result_q["0"])) {
											$all_quizzes_taken_by_user[] = $quiz_id;
										}

										$sql = "SELECT `failed` FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($certificate->user_id) . " and quiz_id=" . intval($quiz_id) . " and pid=" . intval($course_details["0"]["id"]) . " ORDER BY id DESC LIMIT 0,1";
										$db->setQuery($sql);
										$failed = $db->loadColumn();
										$failed = @$failed["0"];
									}
								}
								// stop calculate sum for all quizes from course------------------------------------

								if (count($all_quizzes_taken_by_user) == 0 || count($all_quizzes) == 0) {
									// do nothing
								} else {
									echo count($all_quizzes_taken_by_user) . " / " . count($all_quizzes);
								}
								?>
							</td>

							<td>
								<?php
								if (is_array($all_quizzes) && count($all_quizzes) > 0) {
									$nb_ofscores = count($all_quizzes);
								}

								if ($nb_ofscores != 0) {
									$scores_avg_quizzes = intval($s / $nb_ofscores);
								}

								if (is_array($all_quizzes_taken_by_user) && count($all_quizzes_taken_by_user) > 0) {
									$nb_ofscores_taken = count($all_quizzes_taken_by_user);
								}

								if ($nb_ofscores_taken != 0) {
									$scores_avg_quizzes_taken = intval($s / $nb_ofscores_taken);
								}

								if ($resulthasq == 0 && $scores_avg_quizzes == "") {
									echo JText::_("GURU_NO_QUIZZES");
								} elseif ($resulthasq != 0 && $scores_avg_quizzes == "") {
									echo JText::_("GURU_NOT_TAKEN");
								}
								//elseif($resulthasq != 0 && isset($scores_avg_quizzes)){
								elseif (count($all_quizzes_taken_by_user) != 0 && isset($scores_avg_quizzes)) {
									/*if($scores_avg_quizzes >= intval($avg_quizzes_cert)){
	                                        echo $scores_avg_quizzes.'%'.'<span  style="color:#66CC00;">'.JText::_("GURU_QUIZ_PASSED").'</span>'; 
	                                    }
	                                    else{
	                                        echo $scores_avg_quizzes.'%'.'<span  style="color:#FF0000;">'.JText::_("GURU_QUIZ_FAILED").'</span>';
	                                    }*/

									echo $scores_avg_quizzes_taken . "%";
								}
								?>
							</td>
							<td>
								<?php
								$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($certificate->user_id) . " and quiz_id=" . intval($result) . " and pid=" . intval($course_details["0"]["id"]) . " ORDER BY id DESC LIMIT 0,1";
								$res = $db->setQuery($sql)->loadResult();
								if ($result != 0 && $res != "") {
									if ($res >= $result_maxs) {
										echo $res . '%' . '<span  style="color:#66CC00;">' . JText::_("GURU_QUIZ_PASSED") . '</span>';
									} elseif ($res < $result_maxs) {
										echo $res . '%' . '<span  style="color:#FF0000;">' . JText::_("GURU_QUIZ_FAILED") . '</span>';
									}
								} elseif (($result != 0 && $result != "")) {
									echo JText::_("GURU_NOT_TAKEN");
								} elseif ($result == 0 || $result == "") {
									echo JText::_("GURU_NO_FINAL_EXAM");
								}
								?>
							</td>
							<td>
								<?php
								$replace_text = array("'", '"');
								$replace_with = array("&acute;", "&quot;");
								$author_name = implode(", ", $authors_name_lists);
								?>

								<a href="#" onclick="openWinCertificate1('<?php echo str_replace($replace_text, $replace_with, $course_details["0"]["name"]) ?>', '<?php echo str_replace($replace_text, $replace_with, $author_name); ?>','<?php echo $certificate->id; ?>', '<?php echo $certificate->datecertificate; ?>', '<?php echo $course_details["0"]["id"]; ?>', '<?php echo intval($certificate->user_id); ?>'); return false;">
									<img title="<?php echo JText::_("GURU_VIEW_TOOLTIP"); ?>" src="<?php echo JUri::root() . "/images/stories/guru/certificates/viewed.png"; ?>" align="viewed" />
								</a>

								<a href="#" onclick="openWinCertificate4('<?php echo str_replace($replace_text, $replace_with, $course_details[0]["name"]) ?>','<?php echo str_replace($replace_text, $replace_with, $author_name); ?>','<?php echo $certificate->id; ?>', '<?php echo $certificate->datecertificate; ?>', '<?php echo $course_details["0"]["id"]; ?>', '<?php echo intval($certificate->user_id); ?>'); return false;">
									<img title="<?php echo JText::_("GURU_DLD_TOOLTIP"); ?>" src="<?php echo JUri::root() . "/images/stories/guru/certificates/download.png"; ?>" align="viewed" />
								</a>
							</td>
						</tr>
				<?php
					}
				}
				?>

				<tr>
					<td colspan="11">
						<div class="pagination pagination-toolbar pull-left">
							<?php echo $this->pagination->getListFooter(); ?>
						</div>

						<div class="btn-group pull-right">
							<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>

	<input type="hidden" name="option" value="com_guru" />
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="controller" value="guruCertificate" />
</form>