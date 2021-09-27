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

//JHTML::_('behavior.modal', 'a.modal');
JHTML::_('behavior.framework');
include(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_guru' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'guruprogram.php');
include(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_guru' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'guruorder.php');
$document = JFactory::getDocument();

require_once(JPATH_BASE . "/components/com_guru/helpers/Mobile_Detect.php");

$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$action = JFactory::getApplication()->input->get("action", "");

$callback = JFactory::getApplication()->input->get("submit_action");

$Itemid = JFactory::getApplication()->input->get('Itemid', 0);
$accessJumpB = "";
$document = JFactory::getDocument();
$db = JFactory::getDBO();

$sql = "SELECT guru_turnoffjq FROM #__guru_config WHERE id=1";
$db->setQuery($sql);
$guru_turnoffjq = $db->loadResult();
$guru_turnoffjq = @$guru_turnoffjq["0"];

if (intval($guru_turnoffjq) != 0) {
    JHtml::script('components/com_guru/js/jquery_1_11_2.js');
} else {
    JHtml::_('jquery.framework');
}

//$document->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet('components/com_guru/css/tabs.css');

$app = JFactory::getApplication();
$step = $this->task;

if (strrpos($step->layout_media["0"], "iframe") === FALSE) {
    $step->layout_media["0"] = JHtml::_('content.prepare', $step->layout_media["0"]);
}

if (isset($step->id) && ($step != false)) {
    $step_id = $step->id;
    $pid = $step->pid;

    $this->saveLesson($step_id, $pid);
}

if (!isset($step->id)) {
}

if ($step == false || $step == "") {
    $view_get = JFactory::getApplication()->input->get("view");
    $email_r = JFactory::getApplication()->input->get("e");
    $catid = JFactory::getApplication()->input->get("catid");
    $module_lesson = JFactory::getApplication()->input->get("module");
    $lesson_id = JFactory::getApplication()->input->get("cid");

    if ($view_get == "guruTasks" && $email_r == 1) {
        $link = JRoute::_("index.php?option=com_guru&controller=guruLogin&task=&returnpage=registerforlogout&view=" . $view_get . "&e=" . $email_r . "&catid=" . $catid . "&module=" . $module_lesson . "&cid=" . $lesson_id . "");
    } else {
        $link = JRoute::_("index.php?option=com_guru&controller=guruLogin&task=&returnpage=registerforlogout");
    }

    $app->redirect($link);
}

$sql = "select media_id from #__guru_mediarel where type_id=" . intval($step->id) . " and type='scr_m' and layout=12";
$db->setQuery($sql);
$id = $db->loadResult();

//set meta data for each step
$document = JFactory::getDocument();
if (isset($step->metatitle) && trim($step->metatitle) != "") {
    $document->setTitle(trim($step->metatitle));
} else {
    $document->setTitle($step->name);
}
if (isset($step->metakwd) && trim($step->metakwd) != "") {
    $document->setMetaData("keywords", trim($step->metakwd));
} else {
    $document->setMetaData('keywords', $step->name);
}
if (isset($step->metadesc) && trim($step->metadesc) != "") {
    $document->setDescription(trim($step->metadesc));
} else {
    $document->setMetaData('description', @$step->description);
}

$user = JFactory::getUser();
$diff_date = 0;

$db = JFactory::getDBO();
$sql = "select open_target, lesson_window_size from #__guru_config";
$db->setQuery($sql);
$result = $db->loadAssocList();


$target = intval($result["0"]["open_target"]);
$lesson_size = $result["0"]["lesson_window_size"];
$lesson_size = explode("x", $lesson_size);
$lesson_height = $lesson_size["0"];
$lesson_width = $lesson_size["1"];

$document = JFactory::getDocument();
JHtml::script("components/com_guru/js/programs.js");
$guruModelguruTask = new guruModelguruTask();
$guruModelguruOrder = new guruModelguruOrder();

$configs = $guruModelguruTask->getConfig();

if (isset($step) && ($step != false)) {
    $skip_modules_course = $guruModelguruTask->getSkipAction($step->pid);
} else {
}
$module_pozition = "0";
$certificates = $guruModelguruTask->getCertificate();

$is_final = $guruModelguruTask->getIsFinal($step->id);

if ($is_final == "") {
    $is_final = 0;
}

$db = JFactory::getDBO();

$sql = "select avg_certc from #__guru_program where id=" . intval($step->pid);
$db->setQuery($sql);
$avg_certif = $db->loadResult();

?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");

    // Fix issues on iOS.
    if ((/iphone|ipad|ipod/i).test(navigator.userAgent) && (!window.MSStream)) {
        jQuery(function($) {
            var $object, src, isPDF;

            $object = $("iframe#blockrandom");
            if ($object.length) {
                src = $object.attr("src");
            } else {
                $object = $(".contentpane").children("object");
                if ($object.length) {
                    src = $object.attr("data");
                }
            }

            if (src) {

                // Fix one page PDF issue on iOS.
                if (src.match(/\.pdf/i)) {
                    src = "http://docs.google.com/gview?url=" + src + "&embedded=true";
                    if ($object[0].tagName === "IFRAME") {
                        $object.attr("src", src);
                    } else if ($object[0].tagName === "OBJECT") {
                        $object.attr("data", src);
                    }
                }
            }
        });
    }
</script>

<script>
    function submitgurucomment(id) {
        message = encodeURIComponent(document.getElementById('message').value);
        var req = jQuery.ajax({
            url: "<?php echo JURI::base(); ?>index.php?option=com_guru&controller=guruTasks&task=lessonmessage&lessonid=" + id + "&message=" + message + '&format=raw',
            data: {
                'do': '1'
            },
            success: function(response) {
                jQuery("#gurucommentbox").empty().append(response);
                document.getElementById('message').value = "";
                document.getElementById('submitb').disabled = "disabled";
            }
        });
    }

    function deletegurucomment(id, uid, comid) {
        var req = jQuery.ajax({
            url: "<?php echo JURI::base(); ?>index.php?option=com_guru&controller=guruTasks&task=deletecom&lessonid=" + id + "&uid=" + uid + "&comid=" + comid + '&format=raw',
            data: {
                'do': '1'
            },
            success: function(response) {
                jQuery("#gurucommentbox").empty().append(response);
            }
        });

    }

    function guruChangeText() {
        if (document.getElementById('message').value.length > 0) {
            document.getElementById('submitb').disabled = "";
        } else {
            document.getElementById('submitb').disabled = "disabled";
        }
    }

    function editgurucomment(comid) {
        var gurutext = document.getElementById('gurupostcomment' + comid).innerHTML;
        document.getElementById('gurupostcomment' + comid).style.display = "none";
        document.getElementById("message1" + comid).value = gurutext;
        document.getElementById('message1' + comid).style.display = "block";
        if (document.getElementById('delete' + comid)) {
            document.getElementById('delete' + comid).style.display = "none";
        }
        if (document.getElementById('edit' + comid)) {
            document.getElementById('edit' + comid).style.display = "none";
        }
        document.getElementById('save' + comid).style.display = "block";
    }

    function savegurucomment(lid, comid) {
        message = encodeURIComponent(document.getElementById('message1' + comid).value);
        var req = jQuery.ajax({
            url: "<?php echo JURI::base(); ?>index.php?option=com_guru&controller=guruTasks&task=editformgurupost&lessonid=" + lid + "&comid=" + comid + "&message=" + message + '&format=raw',
            data: {
                'do': '1'
            },
            success: function(response) {
                document.getElementById("gurupostcomment" + comid).value = jQuery("#gurupostcomment" + comid).empty().append(response);
                document.getElementById('gurupostcomment' + comid).style.display = "block";
                document.getElementById('message1' + comid).style.display = "none";
                if (document.getElementById('delete' + comid)) {
                    document.getElementById('delete' + comid).style.display = "table-row";
                }
                if (document.getElementById('edit' + comid)) {

                    document.getElementById('edit' + comid).style.display = "block";
                }
                document.getElementById('save' + comid).style.display = "none";
            }
        });
    }
</script>

<?php
$user = JFactory::getUser();

$sql = "select count(*) from #__extensions where element='com_kunena'";
$db->setQuery($sql);
$count = $db->loadResult();

$sql = "select allow_stud from #__guru_kunena_forum where id=1";
$db->setQuery($sql);
$allow_stud = $db->loadResult();



$sql = "select allow_edit from #__guru_kunena_forum where id=1";
$db->setQuery($sql);
$allow_edit = $db->loadResult();

$sql = "select allow_delete  from #__guru_kunena_forum where id=1";
$db->setQuery($sql);
$allow_delete = $db->loadResult();

if ($count > 0) {
    if ($deviceType != 'phone') {
        $rows_cols = 'rows="5" cols="95"';
    } else {
        $rows_cols = 'rows="3"';
    }
    $gurucommentbox = '
    <div id="' . $step->id . '" class="gurucommentform">
        <div class="guru row-fluid gurucommentform-title">' . JText::_('GURU_COMMENT_LESSON') . '</div>
        <form method="post" name="postform">
            <table>
                <tr>
                    <td valign="top">
                        <table>
                        <tr>
                            <td><span class="guru row-fluid">
                            ' . JText::_('GURU_MESSAGE') . ' </span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><textarea onkeyup="javascript:guruChangeText();" style="width:100%" name="message" id="message" ' . $rows_cols . ' ></textarea></td>
                        </tr>
                        <tr>
                            <td><input class="btn btn-success" disabled="disabled" id="submitb" name="submitb" type="button" onclick="javascript:submitgurucomment(' . $step->id . ');" value="' . JText::_('GURU_QUIZ_SUBMIT') . '" /></td>
                        </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>';
}



?>
<?php
function accessToLesson($lesson_id, $course_id, $jump_module = '')
{
    $db = JFactory::getDBO();
    $user = JFactory::getUser();
    $user_id = $user->id;

    if ($jump_module == "module") {
        return true;
    }

    // start check if user logged is teacher for this course
    $sql = "select author from #__guru_program where id=" . intval($course_id);
    $db->setQuery($sql);
    $author = $db->loadColumn();
    $author = @$author["0"];
    $author = explode("|", $author);

    if (in_array($user_id, $author)) {
        return true;
    }
    // stop check if user logged is teacher for this course

    $sql = "select step_access from #__guru_task where id=" . intval($lesson_id);
    $db->setQuery($sql);
    $lesson_acces = intval($db->loadResult());

    if ($user_id == 0 && $lesson_acces == 2) {
        return true;
    } elseif ($user_id == 0 && $lesson_acces != 2) {
        return false;
    } elseif ($user_id == 0 && $lesson_acces == 0) {
        return false;
    } elseif ($user_id == 0 && $lesson_acces == 1) {
        return false;
    } elseif ($user_id != 0 && $lesson_acces == 1) {
        return true;
    } elseif ($user_id != 0 && $lesson_acces == 2) {
        return true;
    } elseif ($user_id != 0 && $lesson_acces == 0) {
        $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where bc.course_id=" . intval($course_id) . " and bc.userid=" . intval($user_id) . " and bc.order_id=o.id and o.status='Paid'";

        $db->setQuery($sql);
        $result = $db->loadColumn();
        $result = @$result["0"];

        if ($result == 0) {
            return false;
        }
        return true;
    }
    return false;
}
$module_id = intval(JFactory::getApplication()->input->get("module"));
$sql = "select pid from #__guru_days where id=" . intval($module_id);
$db->setQuery($sql);
$catid = intval($db->loadResult());

$layout_style_display = array();

for ($i = 1; $i <= 17; $i++) {
    if ($i == $step->layout) {
        $layout_style_display[$i] = "style='display:block;'";
        //$layout_style_display[$i]="style='visibility:visible;'";
    } else {
        $layout_style_display[$i] = "style='display:none;'";
        //$layout_style_display[$i]="style='visibility:hidden;'";
    }
}

$progres_bar = $guruModelguruTask->getProgresBarSettings();
?>
<?php
$author_id = JFactory::getApplication()->input->get("author", "");
$Itemid = JFactory::getApplication()->input->get('Itemid', 0);

//---------------SECV_NON-SECV---------------------//
$user = JFactory::getUser();
$user_id = $user->id;
if ($user_id > 0) {
    $db = JFactory::getDBO();
    $sql = "select DATE_FORMAT(buy_date,'%Y-%m-%d') as date_enrolled from #__guru_buy_courses where course_id=" . intval($step->pid) . " and userid =" . $user_id;
    $db->setQuery($sql);
    $date_enrolled = $db->loadResult();
    $date_enrolled = strtotime($date_enrolled);
}

$coursetype_details = guruModelguruProgram::getCourseTypeDetails($step->pid);
$start_relaese_date = $coursetype_details[0]["start_release"];
$start_relaese_date = strtotime($start_relaese_date);

$jnow = new JDate('now');
$date9 = $jnow->toSQL(true);
$date_9 = date("Y-m-d", strtotime($date9));

$date9 = strtotime($date9);
//$interval = $start_relaese_date->diff($date9);
$interval = abs($date9 - $start_relaese_date);

$dif_days = floor($interval / (60 * 60 * 24));
$dif_week = floor($interval / (60 * 60 * 24 * 7));
$dif_month = floor($interval / (60 * 60 * 24 * 30));

$diff_enrolled = abs($date9 - @$date_enrolled);
$dif_days_enrolled = floor($diff_enrolled / (60 * 60 * 24));

if ($coursetype_details[0]["course_type"] == 1) {
    if ($coursetype_details[0]["lesson_release"] == 0) {
        $diff_date = $dif_days_enrolled;
    } elseif ($coursetype_details[0]["lesson_release"] == 1) {
        $diff_date = 1 + $dif_days_enrolled;
    } elseif ($coursetype_details[0]["lesson_release"] == 2) {
        $dif_days_enrolled = intval($dif_days_enrolled / 7);
        $diff_date = $dif_days_enrolled;
    } elseif ($coursetype_details[0]["lesson_release"] == 3) {
        $dif_days_enrolled = intval($dif_days_enrolled / 30);
        $diff_date = $dif_days_enrolled;
    }
}

$sql = "SELECT media_id FROM #__guru_mediarel WHERE type='scr_m' and type_id=" . intval($step->id) . " and layout='12'";
$db->setQuery($sql);
$result = $db->loadResult();
$quiz_id = $result;

$sql = "SELECT nb_quiz_select_up FROM #__guru_quiz WHERE id=" . intval($quiz_id);
$db->setQuery($sql);
$nb_quiz_select_up = $db->loadColumn();
$nb_of_questions = @$nb_quiz_select_up["0"];

if (intval($nb_of_questions) == 0) {
    $sql = "SELECT count(*) as total from #__guru_questions_v3 where qid=" . intval($quiz_id);
    $db->setQuery($sql);
    $total_quiz_questions = $db->loadColumn();
    $nb_of_questions = @$total_quiz_questions["0"];
}

$quizz_fe_content = $this->getQuizCalculation($quiz_id, $step->pid, $user_id, $nb_of_questions);

$completed_course = $guruModelguruOrder->courseCompleted($user->id, $step->pid);
$course_certificate_term = $guruModelguruTask->getCertificateTerm($step->pid);
$scores_avg_quizzes = $guruModelguruTask->getAvgScoresQ($user->id, $step->pid);

$certificates[0]["avg_cert"] = $avg_certif;

$sql = "select * from #__menu where link='index.php?option=com_guru&view=guruprograms&layout=view'";
$db->setQuery($sql);
$menu = $db->loadAssocList();
if (isset($menu["0"]) && isset($menu["0"]["id"])) {
    $Itemid = $menu["0"]["id"];
}

$stop_next = 0;

$session = JFactory::getSession();
$registry = $session->get('registry');
$stop_next = $registry->get('stop_next', NULL);

if (isset($stop_next)) {
    $stop_next = intval($stop_next);
    $registry->set('stop_next', NULL);
}

if ($stop_next == "1") {
    $step->nexts = 0;
}

$sql = "select pid from #__guru_days where id=" . intval($module_id);
$db->setQuery($sql);
$course_id = intval($db->loadResult());

$sql = "select author from #__guru_program where id=" . intval($course_id);
$db->setQuery($sql);
$program = $db->loadAssocList();

$author = $program["0"]["author"];
$author = explode("|", $author);
$author = array_filter($author);
?>

<?php
$sql = "SELECT max_score FROM #__guru_quiz WHERE id=" . intval($result);
$db->setQuery($sql);
$result_maxs = $db->loadResult();

$sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($result) . " and pid=" . intval($step->pid) . " ORDER BY id DESC LIMIT 0,1";
$db->setQuery($sql);

$score_quiz = $db->loadColumn();
$score_quiz = @$score_quiz["0"];
$res = $score_quiz;

if ($completed_course == 1) {
    $completed_course = true;
}

//--------for my certificate--------------------
if ($course_certificate_term == 2 && $completed_course == true) {
    $this->InsertMyCertificateDetails1($step->pid);
    $this->emailCertificate1($step->pid);
}

if ($course_certificate_term == 3 && isset($result_maxs) && $res >= intval($result_maxs)) {
    $this->InsertMyCertificateDetails1($step->pid);
    if ($is_final) {
        $this->emailCertificate1($step->pid);
    }
}

if ($course_certificate_term == 4 && $scores_avg_quizzes >= intval($certificates[0]["avg_cert"])) {
    $this->InsertMyCertificateDetails1($step->pid);
    $this->emailCertificate1($step->pid);
}

if ($course_certificate_term == 5 && $completed_course == true && isset($result_maxs) && $res >= intval($result_maxs)) {
    $this->InsertMyCertificateDetails1($step->pid);
    $this->emailCertificate1($step->pid);
}

if ($course_certificate_term == 6 && $completed_course == true && isset($scores_avg_quizzes) && ($scores_avg_quizzes >= intval($certificates[0]["avg_cert"]))) {
    $this->InsertMyCertificateDetails1($step->pid);
    $this->emailCertificate1($step->pid);
}
//----------------------------------------------

$tmpl = JFactory::getApplication()->input->get("tmpl", "");
$lesson_class = "gru-lesson-content";
$is_modal = FALSE;

if ($tmpl == "component") {
    $sql = "select `css` from #__guru_task where id=" . intval($step->id);
    $db->setQuery($sql);
    $css = $db->loadResult();

    $lesson_class = "gru-lesson-content modal-lesson-content " . $css;
    $is_modal = TRUE;
}
?>

<div class="<?php echo $lesson_class; ?>">
    <?php if (!$is_modal) { ?>
        <!-- start: uk-grid gru-nav-bar -->
        <div class="uk-grid wk-grid gru-nav-bar">
            <!-- start home icon-->
            <div class="uk-width-3-10 uk-text-left wk-width-3-10 wk-text-left">
                <a href="<?php echo JRoute::_('index.php?option=com_guru&view=guruPrograms&task=view&cid=' . $step->pid . '&Itemid=' . $Itemid); ?>">
                    <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/home.png"; ?>" alt="<?php echo JText::_("GURU_COURSE_HOME_PAGE"); ?>" title="<?php echo JText::_("GURU_COURSE_HOME_PAGE"); ?>" />
                </a>
            </div>
            <!-- stop home icon -->

            <div class="uk-width-7-10 uk-text-right wk-width-7-10 wk-text-right">
                <!-- start preview icon -->
                <?php
                $tmpl = "";
                if ($target == "1") {
                    $tmpl = "&tmpl=component";
                }
                ?>

                <?php
                if (@$step->prevs != 0 && $step->prevs != "-1") {
                    $chb_free_courses1 = $guruModelguruTask->getChbAccessCourses();
                    $step_access_courses1 = $guruModelguruTask->getStepAccessCourses();

                    if ($chb_free_courses1 == 1) { // free for
                        $sql = "SELECT free_limit FROM `#__guru_program` where id = " . intval($course_id);
                        $db->setQuery($sql);
                        $result_details = $db->loadAssocList();

                        $free_limit = $result_details["0"]["free_limit"];

                        if (intval($free_limit) > 0) {
                            $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where o.`status`='Paid' and o.`id`=bc.`order_id` and (bc.`expired_date` >= now() OR bc.`expired_date`='0000-00-00 00:00:00') and bc.`course_id`=" . intval($course_id);
                            $db->setQuery($sql);
                            $count_orders = $db->loadColumn();
                            $count_orders = @$count_orders["0"];

                            if (intval($count_orders) >= intval($free_limit)) {
                                $chb_free_courses1 = 0;
                            }
                        }
                    }

                    if ($chb_free_courses1 == 1 && $step_access_courses1 == 2) {
                        $step->prevaccess = 2;
                    }

                    if ((($user->id <= 0) && (isset($step->prevaccess) && ($step->prevaccess != 2))) || ($user->id > 0 && !in_array($user->id, $author) && accessToLesson($step->prevs, $course_id) === FALSE && $step->prevaccess != 2)) {
                        if ($target == "0") {
                ?>
                            <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . "&tmpl=component"); ?>');">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                            </a>
                            </a>
                        <?php
                        } else {
                        ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . "&tmpl=component"); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                            </a>
                        <?php
                        }
                    } else {
                        ?>
                        <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $step->prev_module . "&cid=" . $step->prevs . $tmpl); ?>">
                            <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                        </a>
                        <?php
                    }
                } elseif (@$step->prevs == "0") {
                    $current_module = $step->prev_module;

                    $chb_free_courses1 = $guruModelguruTask->getChbAccessCourses();
                    $step_access_courses1 = $guruModelguruTask->getStepAccessCourses();

                    if ($chb_free_courses1 == 1) { // free for
                        $sql = "SELECT free_limit FROM `#__guru_program` where id = " . intval($course_id);
                        $db->setQuery($sql);
                        $result_details = $db->loadAssocList();

                        $free_limit = $result_details["0"]["free_limit"];

                        if (intval($free_limit) > 0) {
                            $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where o.`status`='Paid' and o.`id`=bc.`order_id` and (bc.`expired_date` >= now() OR bc.`expired_date`='0000-00-00 00:00:00') and bc.`course_id`=" . intval($course_id);
                            $db->setQuery($sql);
                            $count_orders = $db->loadColumn();
                            $count_orders = @$count_orders["0"];

                            if (intval($count_orders) >= intval($free_limit)) {
                                $chb_free_courses1 = 0;
                            }
                        }
                    }

                    if ($chb_free_courses1 == 1 && $step_access_courses1 == 2) {
                        $step->prevaccess = 2;
                    }

                    if ($user->id <= 0 && @$step->prevaccess != 2) {
                        if ($target == "0") {
                        ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . intval($step->prev_module) . "&action=viewmodule"); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                            </a>
                        <?php
                        } else {

                        ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . intval($step->prev_module) . "&action=viewmodule&tmpl=component"); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                            </a>
                        <?php
                        }
                    } else {
                        if ($skip_modules_course == "0") { //not skip module                       
                        ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . intval($step->prev_module) . "&action=viewmodule" . $tmpl); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                            </a>
                            <?php
                        } else {
                            $prev_module = $guruModelguruTask->getPrevModule($step->pid, $step->prev_module);
                            $cid_array = $guruModelguruTask->getAllSteps($prev_module);
                            $cid = "0";
                            if (isset($cid_array) && is_array($cid_array) && count($cid_array) > 0) {
                                $cid = $cid_array[count($cid_array) - 1]["id"];
                            }

                            if (@$prev_module != 0) {
                            ?>
                                <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $prev_module . "&cid=" . $cid . $tmpl); ?>">
                                    <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/back.png"; ?>" alt="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" title="<?php echo JText::_("GURU_PREVIOUS_LESSON"); ?>" />
                                </a>
                <?php
                            }
                        }
                    }
                } elseif (@$step->prevs == "-1") {
                    //do nothing, no more preview
                }
                ?>
                <!-- stop preview icon -->

                <!-- start refresh icon -->
                <?php
                if ($target == 0) {
                ?>
                    <a onClick="window.location.href=window.location.href">
                        <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/repeat.png"; ?>" alt="<?php echo JText::_("GURU_REFRESH_PAGE"); ?>" title="<?php echo JText::_("GURU_REFRESH_PAGE"); ?>" />
                    </a>

                <?php
                } else {
                ?>
                    <a onClick="window.location.href=window.location.href">
                        <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/repeat.png"; ?>" alt="<?php echo JText::_("GURU_REFRESH_PAGE"); ?>" title="<?php echo JText::_("GURU_REFRESH_PAGE"); ?>" />
                    </a>
                <?php
                }
                ?>
                <!-- stop refresh icon -->

                <!-- start next icon -->
                <?php
                $lessons_order = $guruModelguruTask->getLessonOrder($step->pid);
                $key_of_lesson = array_search(@$step->nexts, $lessons_order);

                if (isset($step->id)) {
                    $isquizornot =  $guruModelguruTask->getIsQuizOrNot($step->id);
                    $studfailedquiz = $guruModelguruTask->studFailedQuiz($step->id);
                }

                // ---------------------------------------------------------
                $can_next = true;
                $course_id = $step->pid;

                $next_lesson_id = 0;

                if (isset($step->nexts)) {
                    $next_lesson_id = @$step->nexts;
                }

                $db = JFactory::getDbo();

                $sql = "select id from #__guru_days where pid=" . intval($course_id);
                $db->setQuery($sql);
                $course_modules = $db->loadColumn();

                if (intval($next_lesson_id) == 0) {
                    $next_module = $step->next_module;
                    $sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type_id=" . intval($next_module) . " and mr.type='dtask' and mr.media_id=t.id ORDER BY t.ordering ASC LIMIT 0, 1";
                    $db->setQuery($sql);
                    $next_lesson_id = $db->loadColumn();
                    $next_lesson_id = @$next_lesson_id["0"];
                }

                if (intval($next_lesson_id) > 0 && is_array($course_modules) && count($course_modules) > 0) {
                    $sql = "select mr.media_id from #__guru_mediarel mr, #__guru_task t where mr.type_id in (" . implode(",", $course_modules) . ") and mr.type='dtask' and mr.media_id=t.id ORDER BY t.ordering ASC";
                    $db->setQuery($sql);
                    $course_lessons = $db->loadColumn();

                    $sql = "select mr.type_id from #__guru_mediarel mr, #__guru_task t where mr.type_id in (" . implode(",", $course_lessons) . ") and mr.type='scr_m' and mr.layout='12' and mr.media_id=t.id ORDER BY t.ordering ASC";
                    $db->setQuery($sql);
                    $course_lessons_quiz = $db->loadColumn();

                    foreach ($course_lessons as $l_key => $lesson_id) {
                        if ($lesson_id == $step->id && $next_lesson_id && in_array($step->id, $course_lessons_quiz)) {
                            // is not the next lesson and current lesson is a quiz lesson
                            $sql = "select media_id from #__guru_mediarel where type='scr_m' and layout='12' and type_id=" . intval($lesson_id);
                            $db->setQuery($sql);
                            $lesson_quiz_id = $db->loadColumn();
                            $lesson_quiz_id = @$lesson_quiz_id["0"];

                            $sql = "select max_score, student_failed from #__guru_quiz where id=" . intval($lesson_quiz_id);
                            $db->setQuery($sql);
                            $lesson_quiz_details = $db->loadAssocList();
                            $lesson_quiz_max_score = $lesson_quiz_details["0"]["max_score"];
                            $if_student_failed = $lesson_quiz_details["0"]["student_failed"];

                            $sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($lesson_quiz_id) . " and pid=" . intval($course_id) . " ORDER BY id DESC LIMIT 0,1";
                            $db->setQuery($sql);
                            $score_quiz = $db->loadColumn();
                            $score_quiz = @$score_quiz["0"];

                            if (!isset($score_quiz) && $if_student_failed == 1) {
                                // quiz not passed and no access for other lessons
                                $can_next = false;
                            } elseif (isset($score_quiz) && $if_student_failed == 1) {
                                if ($score_quiz < $lesson_quiz_max_score) {
                                    $can_next = false;
                                }
                            }
                        }
                    }
                }
                //-----------------------------------------------------------

                if (isset($step->nexts) && ($step->nexts != 0) && $can_next) {
                    $chb_free_courses1 = $guruModelguruTask->getChbAccessCourses();
                    $step_access_courses1 = $guruModelguruTask->getStepAccessCourses();

                    if ($chb_free_courses1 == 1) { // free for
                        $sql = "SELECT free_limit FROM `#__guru_program` where id = " . intval($course_id);
                        $db->setQuery($sql);
                        $result_details = $db->loadAssocList();

                        $free_limit = $result_details["0"]["free_limit"];

                        if (intval($free_limit) > 0) {
                            $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where o.`status`='Paid' and o.`id`=bc.`order_id` and (bc.`expired_date` >= now() OR bc.`expired_date`='0000-00-00 00:00:00') and bc.`course_id`=" . intval($course_id);
                            $db->setQuery($sql);
                            $count_orders = $db->loadColumn();
                            $count_orders = @$count_orders["0"];

                            if (intval($count_orders) >= intval($free_limit)) {
                                $chb_free_courses1 = 0;
                            }
                        }
                    }

                    if ($chb_free_courses1 == 1 && $step_access_courses1 == 2) {
                        $step->nextaccess = 2;
                    }

                    if (($user->id <= 0 && $step->nextaccess != 2) || ($user->id > 0 && !in_array($user->id, $author) && (accessToLesson($step->nexts, $course_id) === FALSE && $step->nextaccess != 2))) {
                ?>
                        <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . "&tmpl=component"); ?>')"><img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/next.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" />
                        </a>
                        <?php

                    } else {
                        $module_id = intval(JFactory::getApplication()->input->get("module"));

                        if (in_array($user_id, $author)) {
                        ?>
                            <a id="nextbut" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $step->next_module . "&cid=" . $step->nexts . $tmpl); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/next.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" />
                            </a>
                        <?php
                        } elseif (($coursetype_details[0]["course_type"] == 1 && $diff_date > $key_of_lesson + 1)  || $coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == 1 && $coursetype_details[0]["lesson_release"] == 0)) {
                        ?>
                            <a id="nextbut" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $step->next_module . "&cid=" . $step->nexts . $tmpl); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/next.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" />
                            </a>
                            <?php
                        }
                    }
                } elseif ($can_next) {
                    $next_module = "0";
                    $stop = false;
                    $current_module = $step->module;
                    while (!$stop) {
                        $next_module = $guruModelguruTask->getNextModule($step->pid, $current_module);
                        if ($next_module == "0") {
                            $stop = true;
                        } else {
                            $cid_array = $guruModelguruTask->getAllSteps($next_module);
                            if (count($cid_array) > 0) {
                                $stop = true;
                            } else {
                                $current_module = $next_module;
                            }
                        }
                    }
                    if ($next_module != "0") {
                        $guruModelguruTask->setModule($next_module);
                        $cid_array = $guruModelguruTask->getAllSteps($next_module);
                        $cid = "0";
                        if (isset($cid_array) && is_array($cid_array) && count($cid_array) > 0) {
                            $cid = $cid_array["0"]["id"];
                            $step->nextaccess = $cid_array["0"]["step_access"];
                        }
                        if ($cid != "0") {
                            $chb_free_courses1 = $guruModelguruTask->getChbAccessCourses();
                            $step_access_courses1 = $guruModelguruTask->getStepAccessCourses();

                            if ($chb_free_courses1 == 1 && $step_access_courses1 == 2) {
                                $step->nextaccess = 2;
                            }


                            if ($user->id <= 0 && $step->nextaccess != 2) {
                            ?>
                                <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . "&tmpl=component"); ?>')">
                                    <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/next.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" />
                                </a>
                                <?php
                            } else {

                                if ($skip_modules_course == "0" && (($coursetype_details[0]["course_type"] == 1 &&  $diff_date >= $key_of_lesson + 2) || $coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == 1 && $coursetype_details[0]["lesson_release"] == 0))) {

                                    //not skip module
                                ?>
                                    <a id="nextbut" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $step->next_module . "&action=viewmodule" . $tmpl); ?>">
                                        <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/next.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" />
                                    </a>
                                    <?php
                                } else { //skip module
                                    if (($coursetype_details[0]["course_type"] == 1 && $diff_date >= $key_of_lesson + 2) || $coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == 1 && $coursetype_details[0]["lesson_release"] == 0)) {
                                    ?>
                                        <a id="nextbut" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $step->next_module . "&cid=" . $cid . $tmpl); ?>">
                                            <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/next.png"; ?>" alt="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" title="<?php echo JText::_("GURU_NEXT_LESSON"); ?>" />
                                        </a>
                <?php
                                    }
                                }
                            } //log in
                        }
                    } //cid != "0"   

                }
                ?>
                <!-- stop next icon -->

                <!-- stop certificate icon -->
                <?php
                if (@$next_module == "0") {
                    $tmpl = "&tmpl=component";

                    if ($course_certificate_term != 1) {

                        $cid = $step->id;
                        $tmpl = "";
                        if ($target == "1") {
                            $tmpl = "&tmpl=component";
                        }

                        if ($course_certificate_term == 2 && $completed_course == true) {
                ?>
                            <a id="nextbut2" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=viewcertificate" . $tmpl . "&certificate=1&pdf=1&dw=2&ci=" . $step->pid . "&prev_lesson_id=" . $cid . "&module_prev_lesson=" . $step->prev_module . "&catid=" . $catid . "&course_id=" . $step->pid); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/certificate.png"; ?>" alt="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" title="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" />
                            </a>

                        <?php
                        }

                        if ($course_certificate_term == 3 && isset($result_maxs) && $res >= intval($result_maxs)) {
                        ?>
                            <a id="nextbut3" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=viewcertificate" . $tmpl . "&certificate=1&pdf=1&dw=2&ci=" . $step->pid . "&prev_lesson_id=" . $cid . "&module_prev_lesson=" . $step->prev_module . "&catid=" . $catid . "&course_id=" . $step->pid); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/certificate.png"; ?>" alt="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" title="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" />
                            </a>
                        <?php


                        }
                        if ($course_certificate_term == 4 && $scores_avg_quizzes >= intval($certificates[0]["avg_cert"])) {
                        ?>
                            <a id="nextbut4" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=viewcertificate" . $tmpl . "&certificate=1&pdf=1&dw=2&ci=" . $step->pid . "&prev_lesson_id=" . $cid . "&module_prev_lesson=" . $step->prev_module . "&catid=" . $catid . "&course_id=" . $step->pid); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/certificate.png"; ?>" alt="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" title="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" />
                            </a>
                        <?php

                        }
                        if ($course_certificate_term == 5 && $completed_course == true && isset($result_maxs) && $res >= intval($result_maxs)) {
                        ?>
                            <a id="nextbut5" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=viewcertificate" . $tmpl . "&certificate=1&pdf=1&dw=2&ci=" . $step->pid . "&prev_lesson_id=" . $cid . "&module_prev_lesson=" . $step->prev_module . "&catid=" . $catid . "&course_id=" . $step->pid); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/certificate.png"; ?>" alt="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" title="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" />
                            </a>
                        <?php
                        }

                        if ($course_certificate_term == 6 && $completed_course == true && isset($scores_avg_quizzes) && ($scores_avg_quizzes >= intval($certificates[0]["avg_cert"]))) {
                        ?>
                            <a id="nextbut6" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=viewcertificate" . $tmpl . "&certificate=1&pdf=1&dw=2&ci=" . $step->pid . "&prev_lesson_id=" . $cid . "&module_prev_lesson=" . $step->prev_module . "&catid=" . $catid . "&course_id=" . $step->pid); ?>">
                                <img style="border:none;" src="<?php echo JUri::base() . "components/com_guru/images/certificate.png"; ?>" alt="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" title="<?php echo JText::_("GURU_MYCERTIFICATES"); ?>" />
                            </a>
                        <?php
                        }

                        ?>
                <?php
                    }
                }
                ?>
                <input type="hidden" name="certificate_link" id="certificate_link" value="<?php echo JRoute::_("index.php?option=com_guru&view=guruTasks&task=viewcertificate" . $tmpl . "&certificate=1&pdf=1&dw=2&ci=" . $step->pid . "&prev_lesson_id=" . @$cid . "&module_prev_lesson=" . $step->prev_module . "&catid=" . $catid . "&course_id=" . $step->pid); ?>">
                <!-- stop certificate icon -->
            </div>
        </div>
        <!-- stop: uk-grid gru-nav-bar -->
    <?php } ?>
    <?php if (!$is_modal) { ?>
        <!-- start: g_lesson_navs -->
        <div id="g_lesson_navs" class="clearfix">
            <div class="g_lesson_nav_bar clearfix">
                <div id="g_progress" class="pull-right">
                    <?php
                    $action = JFactory::getApplication()->input->get("action", "");
                    if ($action == "") { //we are on a lesson page       
                        if (isset($progres_bar) && $progres_bar["0"]["progress_bar"] == "0") {
                            $all_steps = $guruModelguruTask->getAllSteps($step->module);
                            $total = 0;
                            $poz = 1;
                            $line_width = 5; //black line separator
                            $cid = JFactory::getApplication()->input->get("cid", "0");

                            // start delete duplicate steps
                            if (isset($all_steps) && count($all_steps) > 0) {
                                $temp_steps1 = array();
                                $temp_steps2 = array();

                                foreach ($all_steps as $key => $value) {
                                    if (!isset($temp_steps1[$value["id"]])) {
                                        $temp_steps1[$value["id"]] = $value;
                                        $temp_steps2[] = $value;
                                    }
                                }
                                $all_steps = $temp_steps2;
                            }
                            // stop delete duplicate steps

                            if (isset($all_steps) && count($all_steps) > 0) {
                                $total = count($all_steps);
                                foreach ($all_steps as $key => $value) {
                                    if ($value["id"] == intval($cid)) {
                                        $poz = $key + 1;
                                        $module_pozition = $poz;
                                        break;
                                    }
                                }
                            }

                            $all_modules = $guruModelguruTask->getAllModules($step->pid);
                            $total_modules = 0;
                            $poz_module = 1;
                            $current_module = JFactory::getApplication()->input->get("module", 0);

                            if (isset($all_modules) && count($all_modules) > 0) {
                                $total_module = count($all_modules);
                                foreach ($all_modules as $key => $value) {
                                    if ($value["id"] == intval($current_module)) {
                                        $poz_module = $key + 1;
                                        $module_pozition = $poz_module;
                                        break;
                                    }
                                }
                            }
                    ?>
                            <div>
                                <span style="font-style:italic;">
                                    <?php
                                    echo JText::_("GURU_PROGRES_MODULE") . " " . $poz_module . "/" . $total_module . ", " . JText::_("GURU_PROGRES_LESSON") . " " . $poz . "/" . $total;
                                    ?>
                                </span>
                                <br />
                                <div class="danger" id="blank" style="width:<?php echo $progres_bar["0"]["st_width"]; ?>px; height:<?php echo $progres_bar["0"]["st_height"]; ?>px; background-color:<?php echo $progres_bar["0"]["st_notdonecolor"]; ?>; border-radius: 4px;">
                                    <div class="success" id="completed" style="float:left; height:<?php echo $progres_bar["0"]["st_height"]; ?>px; width:<?php echo (($progres_bar["0"]["st_width"] * $poz) / $total) - $line_width; ?>px; background-color:<?php echo $progres_bar["0"]["st_donecolor"]; ?>; border-radius:4px 0 0 4px;">
                                        &nbsp;
                                    </div>
                                    <div class="warning" id="separator" style="float:left; width:<?php echo $line_width; ?>px; height:<?php echo $progres_bar["0"]["st_height"]; ?>px; background-color:<?php echo $progres_bar["0"]["st_txtcolor"]; ?>;">
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else { // we are one module page

                        if (isset($progres_bar) && $progres_bar["0"]["progress_bar"] == "0") {
                            $all_modules = $guruModelguruTask->getAllModules($step->pid);
                            $total = 0;
                            $poz = 1;
                            $line_width = 5; //black line separator
                            $current_module = JFactory::getApplication()->input->get("module", 0);

                            if (isset($all_modules) && count($all_modules) > 0) {
                                $total = count($all_modules);
                                foreach ($all_modules as $key => $value) {
                                    if ($value["id"] == intval($current_module)) {
                                        $poz = $key + 1;
                                        $module_pozition = $poz;
                                        break;
                                    }
                                }
                            }
                        ?>
                            <div>
                                <span style="font-style:italic;"><?php echo JText::_("GURU_PROGRES_MODULE") . " " . $poz . "/" . $total; ?></span>
                                <br />
                                <div class="danger" id="blank" style="width:<?php echo $progres_bar["0"]["st_width"]; ?>px; height:<?php echo $progres_bar["0"]["st_height"]; ?>px; background-color:<?php echo $progres_bar["0"]["st_notdonecolor"]; ?>;  border-radius: 4px;">
                                    <div id="completed" class="success" style="float:left; height:<?php echo $progres_bar["0"]["st_height"]; ?>px; width:<?php echo (($progres_bar["0"]["st_width"] * $poz) / $total) - $line_width; ?>px; background-color:<?php echo $progres_bar["0"]["st_donecolor"]; ?>; border-radius:4px 0 0 4px;">
                                        &nbsp;
                                    </div>
                                    <div id="separator" class="warning" style="float:left; width:<?php echo $line_width; ?>px; height:<?php echo $progres_bar["0"]["st_height"]; ?>px; background-color:<?php echo $progres_bar["0"]["st_txtcolor"]; ?>;"></div>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- stop: g_lesson_navs -->
    <?php } ?>

    <!-- lesson Page title-->
    <?php
    $tmpl_request = JFactory::getApplication()->input->get("tmpl", "");

    if (isset($action) && $action == "" && $tmpl_request == "") { //we are on a lesson page   
        echo '<h2 class="gru-page-title">' . $step->name . '</h2>';
    }
    ?>
    <!--end lesson page title -->
    <?php
    $all_media = $step->layout_media;
    $all_text = $step->layout_text;
    $show_media = true;
    $stop_search = false;

    if (isset($all_media) && count($all_media) > 0) {
        foreach ($all_media as $key_media => $value_media) {
            if (trim($value_media) != "") {
                $show_media = true;
                $stop_search = true;
                break;
            }
        }
        if (!$stop_search) {
            $show_media = false;
        }
    } else {
        $show_media = false;
    }

    if (!$stop_search) {
        if (isset($all_text) && count($all_text) > 0) {
            foreach ($all_text as $key_text => $value_text) {
                if (trim($value_text) != "") {
                    $show_media = true;
                    break;
                }
            }
            $show_media = false;
        } else {
            $show_media = false;
        }
    }

    $class_span = "guru-module-title";

    $db = JFactory::getDbo();
    $sql = "select media_id from #__guru_days where id=" . intval($step->id);
    $db->setQuery($sql);
    $media_id = $db->loadColumn();
    $media_id = @$media_id["0"];

    if (intval($media_id) == 0) {
        $class_span = "guru-module-center-title";
    }

    if (isset($action) && $action != "" && $show_media === false) {

    ?>
        <!-- <h2 class="gru-page-title"><?php echo JText::_("GURU_PROGRES_MODULE") . " " . $module_pozition . ":"; ?></h2>  -->
        <div class="<?php echo $class_span; ?>"><?php echo $step->name; ?></div>
    <?php
    } elseif (isset($action) && $action != "" && $show_media === true) {
    ?>
        <!-- <h2 class="gru-page-title"><?php echo JText::_("GURU_PROGRES_MODULE") . " " . $module_pozition . ":"; ?></h2>  -->
        <div class="<?php echo $class_span; ?>"><?php echo $step->name; ?></div>
    <?php
    }
    ?>

    <?php
    if ($layout_style_display["1"] == "style='display:block;'") {
    ?>
        <div id="layout1" <?php echo $layout_style_display["1"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="media_1">
                    <?php
                    echo $step->layout_media["0"];
                    ?>
                </div>
                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="text_1">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["2"] == "style='display:block;'") {
    ?>
        <div id="layout2" <?php echo $layout_style_display["2"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1">
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_2">
                            <?php
                            if (strrpos($step->layout_media["0"], "iframe") === FALSE) {
                                $step->layout_media["0"] = JHtml::_('content.prepare', $step->layout_media["0"]);
                            }

                            echo $step->layout_media["0"];
                            ?>
                        </div>
                    </div>
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_2">
                            <?php
                            if (strrpos($step->layout_media["1"], "iframe") === FALSE) {
                                $step->layout_media["1"] = JHtml::_('content.prepare', $step->layout_media["1"]);
                            }

                            echo $step->layout_media["1"];
                            ?>
                        </div>
                    </div>
                </div>

                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="text_2">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["3"] == "style='display:block;'") {
    ?>
        <div id="layout3" <?php echo $layout_style_display["3"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_3">
                    <?php
                    echo $step->layout_media["0"];
                    ?>
                </div>
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_3">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    $lesson_jump_id = intval(JFactory::getApplication()->input->get("cid"));
                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["4"] == "style='display:block;'") {
    ?>
        <div id="layout4" <?php echo $layout_style_display["4"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1">
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="media_5">
                            <?php
                            if (strrpos($step->layout_media["0"], "iframe") === FALSE) {
                                $step->layout_media["0"] = JHtml::_('content.prepare', $step->layout_media["0"]);
                            }

                            echo $step->layout_media["0"];
                            ?>
                        </div>
                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="media_6">
                            <?php
                            if (strrpos($step->layout_media["1"], "iframe") === FALSE) {
                                $step->layout_media["1"] = JHtml::_('content.prepare', $step->layout_media["1"]);
                            }

                            echo $step->layout_media["1"];
                            ?>
                        </div>
                    </div>
                </div>

                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_4">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["5"] == "style='display:block;'") {
    ?>
        <div id="layout5" <?php echo $layout_style_display["5"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_5">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["6"] == "style='display:block;'") {
    ?>
        <div id="layout6" <?php echo $layout_style_display["6"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_7">
                    <?php
                    echo $step->layout_media["0"];
                    ?>
                </div>
            </div>
        </div>
        <script>
            // Only on iframe
            if (window.top != window.self) {
                (function(className) {
                    className = className || '';
                    document.documentElement.className = className + ' ' + 'guru-lesson-video';
                })(document.documentElement.className);
            }
        </script>
    <?php
    } elseif ($layout_style_display["7"] == "style='display:block;'") {
    ?>
        <div id="layout7" <?php echo $layout_style_display["7"]; //$layout1_styledisplay; 
                            ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="text_6">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="media_8">
                    <?php echo $step->layout_media["0"]; ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["8"] == "style='display:block;'") {
    ?>
        <div id="layout8" <?php echo $layout_style_display["8"]; //$layout2_styledisplay; 
                            ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="text_7">
                    <?php
                    $text = $step->layout_text["0"];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>

                <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1">
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_9">
                            <?php echo $step->layout_media["0"]; ?>
                        </div>
                    </div>

                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_9">
                            <?php echo $step->layout_media["1"]; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["9"] == "style='display:block;'") {
    ?>
        <div id="layout9" <?php echo $layout_style_display["9"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_8">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>

                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_11">
                    <?php echo $step->layout_media["0"]; ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["10"] == "style='display:block;'") {
    ?>
        <div id="layout10" <?php echo $layout_style_display["10"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_9">
                    <?php echo $step->layout_text["0"]; ?>
                </div>
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1">
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="media_12">
                            <?php echo $step->layout_media["0"]; ?>
                        </div>

                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 wk-width-large-1-2 wk-width-medium-1-2 wk-width-small-1-1" id="media_13">
                            <?php echo $step->layout_media[1]; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["11"] == "style='display:block;'") {
    ?>
        <div id="layout11" <?php echo $layout_style_display["11"]; //$layout3_styledisplay; 
                            ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_10">
                    <?php
                    $text = $step->layout_text[0];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
            </div>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_14">
                    <?php echo $step->layout_media["0"]; ?>
                </div>
            </div>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="text_11">
                    <?php
                    $text = $step->layout_text[1];
                    $text = JHtml::_('content.prepare', $text);
                    echo $text;
                    ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["16"] == "style='display:block;'") {
        $doc = JFactory::getDocument();

        $upload_mb = guruAdminHelper::getUploadMaxSize();
        $upload_mb *= 1048576; //transform in bytes

        $doc->addScriptDeclaration('
										    jQuery(function(){
										        function createUploader(){            
										            var uploader = new qq.FileUploader({
										                element: document.getElementById(\'fileUploader\'),
										                action: \'' . JURI::root() . 'index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&course_id=' . intval($step->pid) . '&project_id=' . intval($step->project_id) . '&lesson_id=' . intval($step->id) . '&task=upload_project_file\',
										                params:{
										                    folder:\'projects\',
										                    mediaType:\'project\'
										                },
										                onSubmit: function(id,fileName){
										                    jQuery(\'.qq-upload-list li\').css(\'display\',\'none\');
										                },
										                onComplete: function(id,fileName,responseJSON){
										                    if(responseJSON.success == true){
										                        jQuery(\'.qq-upload-success\').append(\'- <span style="color:#387C44;">Upload successful</span>\');
										                        if(responseJSON.locate) {
										                            jQuery(\'#project-file\').val(responseJSON.locate +"/"+ fileName);
										                            jQuery(\'.uploaded_media_16\').show();
																	jQuery(\'.uploaded_media_16 div\').html(fileName);
										                        }
										                    }
										                },
										                allowedExtensions: [\'pdf\', \'doc\', \'docx\', \'zip\'],
										                sizeLimit: ' . $upload_mb . ',
										                multiple: false,
										                maxConnections: 1
										            });           
										        }
										        createUploader();
										    });
										');

        //$doc->addScript('components/com_guru/js/fileuploader.js');
        JHtml::stylesheet('components/com_guru/css/fileuploader.css');
    ?>
        <div id="layout16" <?php echo $layout_style_display["16"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_16">
                    <?php echo $step->layout_media["0"]; ?>
                </div>
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1">
                    <div class="upload_media_16">
                        <h4><?php echo JText::_("GURU_UPLOAD_PROJECT"); ?></h4>
                        <div id="fileUploader"></div>
                    </div>
                    <input type="hidden" name="project_file" id="project-file" value="<?php echo $step->uploaded_file; ?>" />

                    <div class="uploaded_media_16" style="display: <?php if (trim($step->uploaded_file) == "") {
                                                                        echo 'none';
                                                                    } else {
                                                                        echo 'block';
                                                                    } ?>;">
                        <h4><?php echo JText::_("GURU_UPLOADED_PROJECT"); ?></h4>
                        <div>
                            <?php echo $step->uploaded_file; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["17"] == "style='display:block;'") {
    ?>
        <div id="layout17" <?php echo $layout_style_display["17"]; ?>>
            <div class="uk-grid wk-grid">
                <div class="uk-width-large-1-1 uk-width-medium-1-1 uk-width-small-1-1 wk-width-large-1-1 wk-width-medium-1-1 wk-width-small-1-1" id="media_16">
                    <?php echo $step->layout_media["0"]; ?>
                </div>
            </div>
        </div>
    <?php
    } elseif ($layout_style_display["12"] == "style='display:block;'") {
    ?>
        <div id="layout12" <?php if ($layout_style_display["12"] != "style='display:block;'") {
                                echo $layout_style_display["12"];
                            } ?>>
            <!--start quizz/final exam layout -->
            <script>
                // https://discussions.apple.com/thread/7208426?start=0&tstart=0
                if ((/iphone|ipad|ipod/i).test(navigator.userAgent)) {
                    jQuery(function($) {
                        $('html,body').css({
                            height: '100%',
                            overflow: 'auto',
                            '-webkit-overflow-scrolling': 'touch'
                        });
                    });
                }
            </script>
            <div id="media_15">
                <!--start quizz div with form -->
                <div class="">
                    <!--start g_row -->
                    <div class="">
                        <!--start g_cell-->
                        <div id="the_quiz">
                            <!-- start quizz/final exam content -->
                            <?php
                            $user = JFactory::getUser();
                            if ($user->id == 0) {
                            ?>
                                <div class="alert alert-info">
                                    <?php echo JText::_("GURU_QUIZ_IS_NOT_FOR_GUESTS"); ?>
                                </div>
                            <?php
                            } else {
                            ?>
                                <form onsubmit="return validateQuizQuestions();" method="post" action="" name="quizz_exam" id="quizz_exam">
                                    <?php
                                    JHtml::stylesheet("components/com_guru/css/quiz.css");
                                    $database = JFactory::getDBO();

                                    if (!isset($id)) {
                                        $id = JFactory::getApplication()->input->get("quize_id");
                                        $id = intval($id);
                                    }

                                    $sql = "SELECT published FROM #__guru_quiz WHERE id=" . $id;
                                    $database->setQuery($sql);
                                    $result = $database->loadColumn();
                                    $result = @$result["0"];

                                    if ($result == 1) {
                                        $sql = "select `quizzes_ids` from #__guru_quizzes_final where `qid`=" . intval($id);
                                        $database->setQuery($sql);
                                        $database->execute();
                                        $final_quiz_ids = $database->loadColumn();

                                        $view_quiz = true;

                                        if (isset($final_quiz_ids["0"]) && trim($final_quiz_ids["0"]) != "") {
                                            $quiz_unpublished = true;
                                            $final_quiz_ids = explode(",", $final_quiz_ids["0"]);

                                            if (isset($final_quiz_ids) && is_array($final_quiz_ids) && count($final_quiz_ids) > 0) {
                                                foreach ($final_quiz_ids as $keyquiz => $includedquiz) {
                                                    $sql = "SELECT `published` FROM #__guru_quiz WHERE `id`=" . intval($includedquiz);
                                                    $database->setQuery($sql);
                                                    $resultinclquiz = $database->loadColumn();
                                                    $resultinclquiz = @$resultinclquiz["0"];

                                                    if (intval($resultinclquiz) == 1) {
                                                        $quiz_unpublished = false;
                                                    }
                                                }
                                            }

                                            if ($quiz_unpublished) {
                                                $view_quiz = false;
                                            }
                                        }

                                        if ($view_quiz) {
                                            $sql = "SELECT show_countdown, max_score, nb_quiz_select_up, time_quiz_taken, retake_passed_quiz FROM #__guru_quiz WHERE id=" . intval($id);
                                            $database->setQuery($sql);
                                            $result = $database->loadObject();

                                            $user = JFactory::getUser();
                                            $user_id = $user->id;
                                            $time_quiz_taken = $result->time_quiz_taken;
                                            $nb_of_questions = $result->nb_quiz_select_up;
                                            $retake_passed_quiz = $result->retake_passed_quiz;

                                            if (intval($nb_of_questions) == 0) {
                                                $sql = "SELECT count(*) as total from #__guru_questions_v3 where qid=" . intval($id);
                                                $db->setQuery($sql);
                                                $total_quiz_questions = $db->loadColumn();
                                                $nb_of_questions = @$total_quiz_questions["0"];
                                            }

                                            $sql = "SELECT score_quiz FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($id) . " and pid=" . intval($step->pid) . " ORDER BY `id` DESC LIMIT 0,1";
                                            $database->setQuery($sql);
                                            $result_q = $database->loadObject();

                                            $sql = "SELECT count(id) as time_quiz_taken_per_user FROM #__guru_quiz_question_taken_v3 WHERE user_id=" . intval($user_id) . " and quiz_id=" . intval($id) . " and pid=" . intval($step->pid);
                                            $database->setQuery($sql);
                                            $time_quiz_taken_per_user = $database->loadColumn();
                                            $time_quiz_taken_per_user = $time_quiz_taken_per_user["0"];

                                            if ($time_quiz_taken < 11) {
                                                $time_user = $time_quiz_taken - $time_quiz_taken_per_user["0"];
                                            }

                                            if (@$result_q->score_quiz >= intval($result->max_score) && intval($retake_passed_quiz) == 0) {
                                                $pass = 1;

                                                $quizz_fe_content_failed = $this->generatePassed_Failed_quizzes($id, $step->pid, $nb_of_questions, $pass);
                                                echo $quizz_fe_content_failed;
                                            } elseif (isset($time_quiz_taken_per_user) && intval($time_quiz_taken_per_user) >= $time_quiz_taken && $time_quiz_taken < 11) {
                                                $pass = 0;

                                                if (@$result_q->score_quiz >= intval($result->max_score)) {
                                                    $pass = 1;
                                                }

                                                $quizz_fe_content_pass = $this->generatePassed_Failed_quizzes($id, $step->pid, $nb_of_questions, $pass);
                                                echo $quizz_fe_content_pass;
                                            } else {
                                                if ($callback == 0 || $callback == NULL) {
                                                    if ($result->show_countdown == 0) {
                                                        if (trim(@$step->layout_media[0]) != "") {
                                                            $timer = $guruModelguruTask->createTimer($id);
                                                            echo "<br/>" . $timer . "<br/>";

                                                            $session = JFactory::getSession();
                                                            $registry = $session->get('registry');
                                                            $quiz_id_session = $registry->get('stop_next', "0");

                                                            $database = JFactory::getDBO();

                                                            if (isset($id)) {
                                                                $sql = "SELECT limit_time, limit_time_f, show_finish_alert, reset_time from #__guru_quiz WHERE id=" . intval($id);
                                                                $database->setQuery($sql);
                                                                $result = $database->loadObject();
                                                            }

                                                            $minutes = 0;
                                                            $seconds = 0;

                                                            $minutes = intval(@$result->limit_time);
                                                            $seconds = 0;

                                                            $reset_time = intval(@$result->reset_time);

                                                            if (intval($minutes) != 0) {
                                                                $session = JFactory::getSession();
                                                                $registry = $session->get('registry');
                                                                $registry->set('quiz_id', "0");

                                                                $m1 = JFactory::getApplication()->input->cookie->get(intval($id) . "-m1");
                                                                $m2 = JFactory::getApplication()->input->cookie->get(intval($id) . "-m2");

                                                                if (isset($m1) && trim($m1) != "" && $reset_time == 0) {
                                                                    $minutes = $m1;
                                                                }

                                                                if (isset($m2) && trim($m2) != "" && $reset_time == 0) {
                                                                    $seconds = $m2;
                                                                }

                                                                echo '<script language="javascript" type="text/javascript">
    																								window.onload = iJoomlaTimer(' . $minutes . ', ' . $seconds . ', ' . intval($id) . ', ' . intval($result->limit_time_f) . ', ' . intval($result->show_finish_alert) . ');
                                                                                                  </script>';
                                                            }
                                                        }
                                                    }

                                                    echo $step->layout_media["0"];
                                                } else {
                                                    $quiz_id = JFactory::getApplication()->input->get("quize_id");
                                                    $nb_of_questions = JFactory::getApplication()->input->get("question_number");
                                                    echo $quizz_fe_content;
                                                }
                                            }
                                        } else {
                                            echo '<span class="uk-panel uk-panel-box wk-panel wk-panel-box"> ' . JText::_("GURU_UNPL_QUIZ") . '</span>';
                                        }
                                    } //end if($result == 1)
                                    else {
                                        echo '<span class="uk-panel uk-panel-box wk-panel wk-panel-box"> ' . JText::_("GURU_UNPL_QUIZ") . '</span>';
                                    }
                                    ?>
                                    <input type="hidden" name="controller" value="guruTasks" />
                                    <input type="hidden" name="option" value="com_guru" />
                                    <input type="hidden" name="pid" value="<?php echo $step->pid; ?>" />
                                    <input type="hidden" name="task" value="quizz_fe_submit" />
                                    <input type="hidden" id="autosubmit" name="autosubmit" value="0" />
                                </form>
                            <?php
                            }
                            ?>
                        </div><!-- end quizz/final exam content -->
                    </div>
                    <!--end g_cell-->
                </div>
                <!--end g_row-->
            </div><!-- end  quizz div with form  -->
        </div><!-- end quizz/final exam layout -->
    <?php
    }
    $coursetype_details = guruModelguruProgram::getCourseTypeDetails($step->pid);


    $sql = "SELECT step_access_courses  FROM #__guru_program where id = " . intval($step->pid);
    $db->setQuery($sql);
    $step_access_coursesjump = $db->loadResult();

    $sql = "SELECT chb_free_courses  FROM #__guru_program where id = " . intval($step->pid);
    $db->setQuery($sql);
    $chb_free_coursesjump = $db->loadResult();


    if ($chb_free_coursesjump == 1 && $step_access_coursesjump == 2) {
        $accessJumpB = 1;
    }

    ?>
    <?php if (is_array($step->layout_jump) && count($step->layout_jump) > 0) { ?>

        <div id="g_jump_button_ref_1">

            <?php
            if (isset($step->layout_jump["0"])) {
            ?>
                <?php
                $tmpl = "";
                if ($target == 1) {
                    $tmpl = "&tmpl=component";
                }

                $jump_details = $guruModelguruTask->getJumpStep($step->layout_jump[0]->jump);
                $module_link = @$jump_details["0"]["module_id1"];
                $jump_cid = @$jump_details["0"]["jump_step"];
                $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&module=" . intval($module_link) . "&task=view&cid=" . $jump_cid . $tmpl);
                if ($jump_details["0"]["type_selected"] == "module") {
                    $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $jump_cid . "&action=viewmodule" . $tmpl);
                }

                $db = JFactory::getDBO();
                $sql = "select step_access from #__guru_task where id=" . intval($jump_cid);

                $db->setQuery($sql);
                $lesson_acces = intval($db->loadResult());

                if (!accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && $accessJumpB != 1) {
                ?>
                    <?php
                    $style_jump = "";
                    if ($target == 0) {
                    }
                    ?>

                    <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . '&tmpl=component'); ?>');">
                        <?php echo $step->layout_jump[0]->text; ?>
                    </a>
                <?php
                } elseif (!accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && $accessJumpB == 1) {
                ?>
                    <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[0]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                <?php
                } elseif (accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && ($coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == '1' && $coursetype_details[0]["lesson_release"] == '0'))) {
                ?>
                    <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[0]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                    <?php
                } else {
                    $lesson_jump_id = intval(JFactory::getApplication()->input->get("cid"));
                    $lesson_jump_order = $guruModelguruTask->getLessonJumpOrder($step->pid, $lesson_jump_id);

                    if ($lesson_jump_order <= $diff_date) {

                    ?>
                        <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[0]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                <?php
                    }
                }
                ?>
            <?php
            }
            ?>


            <?php
            if (isset($step->layout_jump["1"])) {
            ?>
                <?php
                $jump_details = $guruModelguruTask->getJumpStep($step->layout_jump[1]->jump);
                $module_link = @$jump_details["0"]["module_id1"];
                $jump_cid = @$jump_details["0"]["jump_step"];
                $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&module=" . intval($module_link) . "&task=view&cid=" . $jump_cid . $tmpl);
                if ($jump_details["0"]["type_selected"] == "module") {
                    $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $jump_cid . "&action=viewmodule" . $tmpl);
                }

                $db = JFactory::getDBO();
                $sql = "select step_access from #__guru_task where id=" . intval($jump_cid);
                $db->setQuery($sql);
                $lesson_acces = intval($db->loadResult());

                if (!accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && $accessJumpB != 1) {
                ?>
                    <?php
                    $style_jump = "";
                    if ($target == 0) {
                    }
                    ?>
                    <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . '&tmpl=component'); ?>');">
                        <?php echo $step->layout_jump[1]->text; ?>
                    </a>
                <?php
                } elseif (accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && ($coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == '1' && $coursetype_details[0]["lesson_release"] == '0'))) {
                ?>
                    <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[1]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                    <?php
                } else {
                    $lesson_jump_id = intval(JFactory::getApplication()->input->get("cid"));
                    $lesson_jump_order = $guruModelguruTask->getLessonJumpOrder($step->pid, $lesson_jump_id);

                    if ($lesson_jump_order <= $diff_date) {
                    ?>
                        <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[1]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                <?php
                    }
                }
                ?>
            <?php
            }
            ?>
        </div>
        <div id="g_jump_button_ref_2">

            <?php
            if (isset($step->layout_jump["2"])) {
            ?>
                <?php
                $jump_details = $guruModelguruTask->getJumpStep($step->layout_jump[2]->jump);
                $module_link = @$jump_details["0"]["module_id1"];
                $jump_cid = @$jump_details["0"]["jump_step"];
                $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&module=" . intval($module_link) . "&task=view&cid=" . $jump_cid . $tmpl);
                if ($jump_details["0"]["type_selected"] == "module") {
                    $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $jump_cid . "&action=viewmodule" . $tmpl);
                }

                $db = JFactory::getDBO();
                $sql = "select step_access from #__guru_task where id=" . intval($jump_cid);
                $db->setQuery($sql);
                $lesson_acces = intval($db->loadResult());

                if (!accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && $accessJumpB != 1) {
                ?>
                    <?php
                    $style_jump = "";
                    if ($target == 0) {
                    }
                    ?>

                    <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . '&tmpl=component'); ?>');">
                        <?php echo $step->layout_jump[2]->text; ?>
                    </a>
                <?php
                } elseif (accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && ($coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == '1' && $coursetype_details[0]["lesson_release"] == '0'))) {
                ?>
                    <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[2]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                    <?php
                } else {
                    $lesson_jump_id = intval(JFactory::getApplication()->input->get("cid"));
                    $lesson_jump_order = $guruModelguruTask->getLessonJumpOrder($step->pid, $lesson_jump_id);

                    if ($lesson_jump_order <= $diff_date) {
                    ?>
                        <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[2]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                <?php
                    }
                }
                ?>
            <?php
            }
            ?>


            <?php

            if (isset($step->layout_jump["3"])) {
            ?>
                <?php
                $jump_details = $guruModelguruTask->getJumpStep($step->layout_jump[3]->jump);
                $module_link = @$jump_details["0"]["module_id1"];
                $jump_cid = @$jump_details["0"]["jump_step"];
                $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&module=" . intval($module_link) . "&task=view&cid=" . $jump_cid . $tmpl);
                if ($jump_details["0"]["type_selected"] == "module") {
                    $jump_link = JRoute::_("index.php?option=com_guru&view=guruTasks&catid=" . intval($catid) . "&task=view&module=" . $jump_cid . "&action=viewmodule" . $tmpl);
                }

                $db = JFactory::getDBO();
                $sql = "select step_access from #__guru_task where id=" . intval($jump_cid);
                $db->setQuery($sql);
                $lesson_acces = intval($db->loadResult());

                if (!accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && $accessJumpB != 1) {
                ?>
                    <?php
                    $style_jump = "";
                    if ($target == 0) {
                    }
                    ?>
                    <a href="#" onclick="openMyModal(0, 0, '<?php echo JRoute::_("index.php?option=com_guru&view=guruEditplans&course_id=" . intval($catid) . '&tmpl=component'); ?>');">
                        <?php echo $step->layout_jump[3]->text; ?>
                    </a>
                <?php
                } elseif (accessToLesson($jump_cid, $course_id, $jump_details["0"]["type_selected"]) && ($coursetype_details[0]["course_type"] == 0 || ($coursetype_details[0]["course_type"] == '1' && $coursetype_details[0]["lesson_release"] == '0'))) {
                ?>
                    <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[3]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                    <?php
                } else {
                    $lesson_jump_id = intval(JFactory::getApplication()->input->get("cid"));
                    $lesson_jump_order = $guruModelguruTask->getLessonJumpOrder($step->pid, $lesson_jump_id);

                    if ($lesson_jump_order <= $diff_date) {
                    ?>
                        <input class="btn btn-danger" type="button" name="JumpButton" value="<?php echo $step->layout_jump[3]->text; ?>" onclick="if(eval(document.getElementById('id-lesson-<?php echo $jump_cid; ?>'))){document.getElementById('id-lesson-<?php echo $jump_cid; ?>').click();}else{if(eval(document.getElementById('id-module-<?php echo $jump_cid; ?>'))){document.getElementById('id-module-<?php echo $jump_cid; ?>').click();}}" />
                <?php
                    }
                }
                ?>
            <?php
            }
            ?>

        </div>
    <?php } ?>

    <?php
    if (isset($step->audio)) {
    ?>
        <div id="div_audio">
            <?php
            //$step->audio = str_replace('style="', 'style="position:absolute; top:-100px;', $step->audio);
            echo $step->audio;
            ?>
        </div>
    <?php
    }
    ?>

    <?php
    $verifie = JFactory::getApplication()->input->get("action", "");

    if ($verifie != "viewmodule" && FALSE) { //if you come from module page
        $sql = "select count(*) from #__extensions where element='com_kunena' and name='com_kunena'";
        $db->setQuery($sql);
        $count = $db->loadResult();
        if ($count > 0) {

            $user = JFactory::getUser();
            $user_id = $user->id;

            $sql = "select enabled from #__extensions WHERE element='ijoomlagurudiscussbox'";
            $db->setQuery($sql);
            $enabled = $db->loadResult();

            $sql = "select count(id) from #__kunena_categories WHERE alias='" . $step->alias . "'";
            $db->setQuery($sql);
            $board_less = $db->loadResult();

            if ($enabled == 1) { //if the plugin  is enabled
                if ($board_less != 0) { //if we have category created for the lesson
                    $sql = "select numPosts from #__kunena_categories WHERE name='" . addslashes(trim($step->name)) . "' order by id desc limit 0,1";
                    $db->setQuery($sql);
                    $numposts = $db->loadResult();

                    if ($user_id != 0) { //if you are login
                        if ($allow_stud == 0) {
                            echo $gurucommentbox;
                        }
                        if ($allow_stud == 0) {
                            if ($numposts  != 0) {
    ?>
                                <div class="gurucommentform-title"><?php echo JText::_('GURU_POST_IN_DISCUSSION'); ?></div>
                            <?php
                            } ?>
                            <div id="gurucommentbox">
                                <?php
                                $sql = "select id, name, userid from #__kunena_messages WHERE subject='" . addslashes(trim($step->name)) . "' order by id desc";
                                $db->setQuery($sql);
                                $resultid = $db->loadAssocList();

                                $jnow = new JDate('now');
                                $date_currentk = $jnow->toSQL(true);
                                $int_current_datek = strtotime($date_currentk);

                                $sql = "select id from #__kunena_categories WHERE name='" . addslashes(trim($step->name)) . "' order by id desc limit 0,1";
                                $db->setQuery($sql);
                                $catkunena = $db->loadResult();

                                $sql = "select id from #__kunena_topics WHERE subject='" . addslashes(trim($step->name)) . " - " . intval($step->id) . "' order by id asc limit 0,1";
                                $db->setQuery($sql);
                                $idmess = $db->loadResult();

                                for ($i = 0; $i < count($resultid); $i++) {
                                    $sql = "select message from #__kunena_messages_text WHERE mesid=" . $resultid[$i]["id"];
                                    $db->setQuery($sql);
                                    $result = $db->loadResult();

                                    $sql = "select time from #__kunena_messages WHERE id=" . $resultid[$i]["id"];
                                    $db->setQuery($sql);
                                    $datestart = $db->loadResult();

                                    $timepast = $guruModelguruTask->get_time_difference($datestart, $int_current_datek);

                                    if ($timepast["days"] == 0) {
                                        if ($timepast["hours"] == 0) {
                                            if ($timepast["minutes"] == 0) {
                                                $difference = "a few seconds ago";
                                            } else {
                                                $difference = $timepast["minutes"] . " " . JText::_("GURU_REAL_MINUTES") . " " . JText::_("GURU_AGO");
                                            }
                                        } else {
                                            $difference = $timepast["hours"] . " " . JText::_("GURU_REAL_HOURS") . ", " .
                                                $timepast["minutes"] . " " . JText::_("GURU_REAL_MINUTES") . " " . JText::_("GURU_AGO");
                                        }
                                    } else {
                                        $difference = $timepast["days"] . " " . JText::_("GURU_REAL_DAYS") . ", " .
                                            $difference = $timepast["hours"] . " " . JText::_("GURU_REAL_HOURS") . ", " .
                                            $timepast["minutes"] . " " . JText::_("GURU_REAL_MINUTES") . " " . JText::_("GURU_AGO");
                                    }



                                    if ($deviceType != 'phone') {
                                        $rows_cols = ' rows="2" cols="90"';
                                        $style = 'style="width:100%"';
                                    } else {
                                        $rows_cols = 'rows="3"';
                                        $style = 'style="width:50%"';
                                    }

                                ?>
                                    <div class="guru row-fluid guru-header">
                                        <span><?php echo get_avatar($comment, 32); ?></span>
                                        <span><?php echo JText::_('GURU_POSTED'); ?>:<?php echo $difference; ?></span>
                                        <span style="float:right;"><a href=<?php echo JUri::base() . 'index.php?option=com_kunena&view=topic&catid=' . $catkunena . '&id=' . $idmess . '&Itemid=0#' . $resultid[$i]["id"]; ?>>#<?php echo $resultid[$i]["id"]; ?></a></span>
                                        <span><?php echo JText::_('GURU_COMMENTED_BY') . ' ' . $resultid[$i]["name"]; ?>
                                        </span>
                                    </div>
                                    <div class="guru-reply-body clearfix">
                                        <div style="display:block;" id="gurupostcomment<?php echo $resultid[$i]["id"]; ?>" class="guru-text"><?php echo $result; ?></div>
                                        <textarea style="display:none;" <?php echo $style; ?> name="message1<?php echo $resultid[$i]["id"]; ?>" id="message1<?php echo $resultid[$i]["id"]; ?>" <?php echo $rows_cols; ?>></textarea>
                                        <input style="display:none;" id="save<?php echo $resultid[$i]["id"]; ?>" name="save" class="uk-button uk-button-small uk-button-success wk-button wk-button-small wk-button-success" type="button" onclick="javascript:savegurucomment('<?php echo $step->id; ?>','<?php echo $resultid[$i]["id"]; ?>');" value="<?php echo JText::_('GURU_SAVE'); ?>" />
                                        <div>
                                            <?php if ($user_id == $resultid[$i]["userid"]) {
                                                if ($allow_delete == 0) {
                                                    echo '<span style="display:block; float:left;"><a id="delete' . $resultid[$i]["id"] . '" href="#" onclick="javascript:deletegurucomment(' . $step->id . ', ' . $resultid[$i]["userid"] . ', ' . $resultid[$i]["id"] . '); return false;">' . JText::_("GURU_DELETE") . '</a></span>';
                                                }
                                                if ($allow_edit == 0) {
                                                    echo '<span style="float:right;display:block "><a id="edit' . $resultid[$i]["id"] . '" href="#" onclick="javascript:editgurucomment(' . $resultid[$i]["id"] . '); return false;">' . JText::_("GURU_EDIT") . '</a></span>';
                                                }
                                            } else {
                                                echo "";
                                            } ?>
                                        </div>
                                    </div>
                                <?php } //end for
                                ?>
                            </div>
                            <!--end div id gurucommentbox -->
    <?php
                        } //end allow_stud

                    } //end if you are login


                } //end if we have category created for the lesson

            } // end if the plugin  is enabled


        } // end if you come from module page 
    } //end if count

    ?>

</div>