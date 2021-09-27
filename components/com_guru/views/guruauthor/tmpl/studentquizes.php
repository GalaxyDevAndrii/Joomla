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

$quizes = $this->quizes;
$userid = JFactory::getApplication()->input->get("userid", "0");
$pid = JFactory::getApplication()->input->get("pid", "0");
$user_name = $this->userName($userid);
$user_email = $this->userEmail($userid);
$doc = JFactory::getDocument();
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_COU_STUDENTS'))." ".trim(JText::_('GURU_DETAILS')));
$doc = JFactory::getDocument();
$image = $this->userImage($userid);

JHtml::stylesheet("components/com_guru/css/uikit.almost-flat.min.css");

$chart_values = array();

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div id="g_myquizzesstats" class="gru-myquizzesstats">
    <?php echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <div class="uk-grid wk-grid">
    	<div class="uk-width-1-1 wk-width-1-1">
        	<button class="uk-button uk-button-primary uk-float-right wk-button wk-button-primary wk-float-right" onclick="window.location='<?php echo JUri::root() ?>index.php?option=com_guru&view=guruauthor&&layout=studentdetails&userid=<?php echo $userid;?>&tmpl=component'"><?php echo JText::_("GURU_BACK"); ?></button>
        </div>
    </div>
    
    <form action="<?php echo JUri::root() ?>index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
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
                    <th class="g_cell_1"><?php echo JText::_("GURU_QUIZ_NAME"); ?></th>
                    <th class="g_cell_2 hidden-phone"><?php echo JText::_("GURU_AUTHOR_ATTEP"); ?></th>
                    <th class="g_cell_3 hidden-phone"><?php echo JText::_('GURU_CORRECT_ANSWERS'); ?></th>
                    <th class="g_cell_4 hidden-phone"><?php echo JText::_("GURU_WRONG_ANSWERS"); ?></th>
                    <th class="g_cell_5"><?php echo JText::_("GURU_FINAL_SCORE"); ?></th>
                    <th class="g_cell_6"><?php echo JText::_("GURU_DETAILS"); ?></th>
                </tr>
                <?php
                    if(isset($quizes) && count($quizes) > 0){
                        foreach($quizes as $key=>$quiz){
                ?>
                            <tr class="guru_row">
                                <td class="g_cell_1">
                                    <?php echo $quiz["name"]; ?>
                                </td>
                                <td class="g_cell_2 hidden-phone">
                                    <?php echo intval($quiz["timequizuser"])."/".$quiz["time_quiz_taken"]; ?>
                                </td>
                                <td class="g_cell_3 hidden-phone">
                                    <?php echo $quiz["correct"]; ?>
                                </td>
                                <td class="g_cell_4 hidden-phone">
                                    <?php echo $quiz["wrong"]; ?>
                                </td>
                                <td class="g_cell_5">
                                    <?php
                                        //if(trim($quiz["final_score"]) != "" && intval($quiz["final_score"]) != 0){
                                            echo $quiz["final_score"]."% / (".$quiz["max_score"]."%)";

                                            if($quiz["final_score"] < 0){
                                                $chart_values[] = 0;
                                            }
                                            else{
                                                $chart_values[] = $quiz["final_score"];
                                            }
                                        //}
                                    ?>
                                </td>
                                <td class="g_cell_6">
                                    <?php
                                        //if(trim($quiz["final_score"]) != "" && intval($quiz["final_score"]) != 0){
                                    ?>
                                            <input type="button" class="uk-button uk-button-success wk-button wk-button-success" onclick="window.location='<?php echo JURI::root()."index.php?option=com_guru&view=guruauthor&task=quizdetails&layout=quizdetails&pid=".intval($pid)."&userid=".intval($userid)."&quiz=".intval($quiz["quiz_id"]); ?>&tmpl=component&step_quiz=<?php echo intval($quiz["id"]); ?>'" value="<?php echo JText::_("GURU_DETAILS"); ?>" />
                                    <?php
                                        //}
                                    ?>        
                                </td>
                            </tr>
                <?php
                        }
                    }
                ?>
        </table>
        <?php if (!empty($chart_values)): ?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load("current", {packages:['corechart']});
            google.charts.setOnLoadCallback(drawChart);
            
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ["Element", "Value", { role: "style" } ],
                    <?php
                        if(isset($chart_values) && count($chart_values) > 0){
                            foreach($chart_values as $key => $value){
                                echo '["", '.$value.', "#009dd8"]';

                                if(isset($chart_values[$key + 1])){
                                    echo ',';
                                }
                            }
                        }
                    ?>
                ]);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                    { calc: "stringify",
                    sourceColumn: 1,
                    type: "string",
                    role: "annotation" },
                    2]);

                var options = {
                    title: "<?php echo JText::_("GURU_EXAMS_POINTS"); ?>",
                    width: 600,
                    height: 400,
                    bar: {groupWidth: "10"},
                    legend: { position: "none" },
                };
                
                var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
                chart.draw(view, options);
            }
        </script>
        <?php endif ?>
        <div id="columnchart_values" style="width: 100%; height: 300px;"></div>
                 
        <input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", ""); ?>" />
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="controller" value="guruAuthor" />
    </form> 
</div>                  