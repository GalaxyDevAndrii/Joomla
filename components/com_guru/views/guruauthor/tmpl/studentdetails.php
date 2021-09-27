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
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
$div_menu = $this->authorGuruMenuBar();

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

$details = $this->details;
$userid = JFactory::getApplication()->input->get("userid", "0");
$user_name = $this->userName($userid);
$user_email = $this->userEmail($userid);
$itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
$doc = JFactory::getDocument();
$image = $this->userImage($userid);

$helper = new guruHelper();
$itemid_seo = $helper->getSeoItemid();
$itemid_seo = @$itemid_seo["guruprograms"];

if(intval($itemid_seo) > 0){
	$itemid = intval($itemid_seo);
}

JHtml::stylesheet("components/com_guru/css/uikit.almost-flat.min.css");

require_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."guruorder.php");
$guruModelguruOrder = new guruModelguruOrder();

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div id="g_myquizzesstats" class="gru-myquizzesstats">
    <?php echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <form  action="<?php echo JUri::root() ?>index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
            <h4>
                <?php
                    if(trim($image) == ""){
                        $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($user_email)))."?d=mm&s=40";
                        echo '<img src="'.$grav_url.'" alt="'.$user_name.'" title="'.$user_name.'"/>&nbsp;';
                    }
                    else{
                        echo '<img src="'.JURI::root().trim($image).'" style="width:40px;" alt="'.$user_name.'" title="'.$user_name.'" />&nbsp;';
                    }
                    echo $user_name;
                ?>
            </h4>
            <table class="uk-table uk-table-striped wk-table wk-table-striped">
                <tr>
                    <th class="g_cell_1"><?php echo JText::_("GURU_COURSE_NAME"); ?></th>
                    <th class="g_cell_2 hidden-phone"><?php echo JText::_('GURU_COMPLETED'); ?></th>
                    <th class="g_cell_3 hidden-phone"><?php echo JText::_("GURU_QUIZZES_COMPLETED")." / ".JText::_("GURU_TOTAL_QUIZZES"); ?></th>
                    <th class="g_cell_4"><?php echo JText::_("GURU_AVG_SCORE")." / ".JText::_("GURU_AVG_SCORE_TO_PASS"); ?></th>
                    <th class="g_cell_5"><?php echo JText::_("GURU_FINAL_EXAM_SCORE")." / ".JText::_("GURU_SCORE_TO_PASS"); ?></th>
                    <th class="g_cell_6"><?php echo JText::_("GURU_LESSONS_VIEWED"); ?></th>
                    <th class="g_cell_7"><?php echo JText::_("GURU_RESULTS"); ?></th>
                </tr>
                <?php
                    if(isset($details) && count($details) > 0){
                        foreach($details as $key=>$detail){
                            $helper = new guruHelper();
                            $itemid_menu = $helper->getCourseMenuItem(intval($detail["course_id"]));
                            $itemid_course = $itemid;

                            if(intval($itemid_menu) > 0){
                                $itemid_course = intval($itemid_menu);
                            }
                ?>
                            <tr class="guru_row">
                                <td class="g_cell_1">
                                    <a target="_blank" href="<?php echo JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($detail["course_id"])."&Itemid=".$itemid_course); ?>"><i class="fa fa-eye"></i></a>&nbsp;
                                    <?php echo $detail["name"]; ?>
                                </td>
                                <td class="g_cell_2 hidden-phone">
                                    <?php
                                        if($detail["completed"] == "1"){
                                            echo JText::_("JYES");
                                        }
                                        else{
                                            echo JText::_("JNO");
                                        }
                                    ?>
                                </td>
                                <td class="g_cell_3 hidden-phone">
                                    <?php
                                        echo $detail["taken"]." / ".$detail["quizes"];
                                    ?>
                                </td>
                                <td class="g_cell_4">
                                    <?php
                                        if(isset($detail["taken_percent"])){
                                            echo $detail["taken_percent"]."%";
                                        }
                                        else{
                                            echo "0.00%";
                                        }
                                        echo " / ".$detail["avg"]."%";
                                    ?>
                                </td>
                                <td class="g_cell_4">
                                    <?php
                                        echo $detail["final_score"]."%"." / ".$detail["final_min_score"]."%";
                                    ?>
                                </td>
                                <td class="g_cell_4">
                                    <?php
                                        $all_lessons = $this->getAllLessons($detail["course_id"]);
                                        $viewed_lessons = $this->getAllViewedLessons($detail["course_id"], $userid);
                                        if($all_lessons != 0 && $viewed_lessons != 0){
                                            echo '<a href="'.JURI::root().'index.php?option=com_guru&view=guruauthor&layout=studentdetailslesson&userid='.intval($userid).'&pid='.intval($detail["course_id"]).'&tmpl=component">'.$all_lessons.' / '.$viewed_lessons.'</a>';
                                        }
                                        else{
                                            echo '0 / 0';
                                        }

                                        $time_recorded = $guruModelguruOrder->dateCourseRecordTime($userid, $detail["course_id"]);

                                        if(isset($time_recorded) && $time_recorded["show_time"]){
                                            $time_recorded["time"] = preg_replace("/:/", "h ", $time_recorded["time"], 1);
                                            $time_recorded["time"] = preg_replace("/:/", "m ", $time_recorded["time"], 2);
                                            $time_recorded["time"] .= "s";
                                        ?>

                                            <br />

                                            <div class="record-time-label">
                                            <?php
                                                echo JText::_("GURU_CERT_TERM_TIME_RECORDED").": ".$time_recorded["time"];
                                            ?>
                                            </div>

                                        <?php
                                        }

                                        $progress_lessons = 0;

                                        if(intval($all_lessons) > 0){
                                            $progress_lessons = ($viewed_lessons * 100) / $all_lessons;
                                        }
                                    ?>
                                    <div style="display: block; width: 100%;">
                                        <div class="uk-progress uk-progress-small">
                                            <div class="uk-progress-bar" style="width: <?php echo number_format($progress_lessons, 2); ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="g_cell_4 text-centered">
                                    <input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_RESULT"); ?>" onclick="window.location='<?php echo JURI::root()."index.php?option=com_guru&view=guruauthor&task=studentquizes&layout=studentquizes&pid=".intval($detail["course_id"])."&userid=".intval($userid)."&tmpl=component"; ?>'" />
                                </td>
                            </tr>
                <?php
                        }
                    }
                ?>
        </table>
        
        <input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", ""); ?>" />
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="controller" value="guruAuthor" />
    </form>
</div>                 