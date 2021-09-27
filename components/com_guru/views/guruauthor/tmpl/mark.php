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
$div_menu = $this->authorGuruMenuBar();
$items = $this->items;
$doc = JFactory::getDocument();
//$doc->addScript('components/com_guru/js/guru_modal.js');
$course_id = JFactory::getApplication()->input->get("id", "0");
$filter_quiz = JFactory::getApplication()->input->get("filter_quiz", "");
$filter_quiz_type = JFactory::getApplication()->input->get("filter_quiz_type", "");

$all_quiz = array();
$all_quiz_type = array();

if(isset($items) && count($items) > 0){
	foreach($items as $key=>$value){
		$all_quiz[$value["quiz_id"]] = $value["quiz_name"];
		$all_quiz_type[$value["final_quiz"]] = $value["final_quiz"];
	}
}

//$doc->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet("components/com_guru/css/tabs.css");

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<div id="g_mycoursesauthor" class="clearfix com-cont-wrap">
    <?php
        echo $div_menu;
    ?>
    
    <h2 class="gru-page-title"><?php echo JText::_('GURU_QUIZZES_FOR_MARK');?></h2>
    
    <div id="g_mycoursesauthorcontent" class="g_sect clearfix">
        <form  action="<?php echo JUri::root() ?>index.php" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
            <div class="gru-page-filters">
            	<input type="text" class="form-control inputbox" name="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" >
                <span class="input-group-btn hidden-phone">
                    <button class="uk-button uk-button-primary wk-button wk-button-primary" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
                </span>
                &nbsp;
                <select name="filter_quiz" onchange="document.adminForm.submit();">
                    <option value="0"><?php echo JText::_("GURU_FILTER_BY_QUIZ"); ?></option>
                    <?php
                        if(isset($all_quiz) && count($all_quiz) > 0){
                            foreach($all_quiz as $key=>$value){
                                $selected = "";
                                if($filter_quiz == $key){
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                            }
                        }
                    ?>
                </select>
                
				<?php
					if(count($all_quiz_type) > 1){
				?>
					&nbsp;
                    <select name="filter_quiz_type" onchange="document.adminForm.submit();">
                        <option value="-1"><?php echo JText::_("GURU_FILTER_BY_QUIZ_TYPE"); ?></option>
                        <option value="0" <?php if($filter_quiz_type == '0'){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_REGULAR_QUIZ"); ?></option>
                        <option value="1" <?php if($filter_quiz_type == '1'){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_FQUIZ"); ?></option>
                    </select>
				<?php
					}
				?>
            </div>
            
            <div class="clearfix"></div>
            
            <table id="g_authorstudent" class="uk-table uk-table-striped wk-table wk-table-striped">
                <tr class="g_table_header">
                    <th width="1%" class="hidden-phone"></th>
                    <th class="g_cell_3"><?php echo JText::_('GURU_STUDENT_NAME'); ?></th>
                    <th class="g_cell_4"><?php echo JText::_("GURU_QUIZ_NAME"); ?></th>
                    <th class="g_cell_5"><?php echo JText::_("GURU_QUIZ_TYPE"); ?></th>
                    <th class="g_cell_6 uk-text-center wk-text-center"><?php echo JText::_("GURU_MARK"); ?></th>
                </tr>
                <?php
                    if(isset($items) && count($items) > 0){
                        foreach($items as $key=>$item){
                ?>
                            <tr class="guru_row">
                                <td class="center hidden-phone">
                                    <?php
                                        if(trim($item["image"]) == ""){
                                            $grav_url = "http://www.gravatar.com/avatar/".md5(strtolower(trim($item["email"])))."?d=mm&s=40";
                                            echo '<img src="'.$grav_url.'" alt="'.$item["firstname"]." ".$item["lastname"].'" title="'.$item["firstname"]." ".$item["lastname"].'"/>';
                                        }
                                        else{
                                            echo '<img src="'.JURI::root().trim($item["image"]).'" style="width:40px;" />';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        echo $item["firstname"]." ".$item["lastname"];
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=editQuizFE&cid=".$item["quiz_id"]."&v=".$item["final_quiz"]."&e=1"); ?>">
                                    <?php
                                        echo $item["quiz_name"];
                                    ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        if($item["final_quiz"] == "0"){
                                            echo JText::_("GURU_REGULAR_QUIZ");
                                        }
                                        else{
                                            echo JText::_("GURU_FQUIZ");
                                        }
                                    ?>
                                </td>
                                <td class="uk-text-center wk-text-center">
                                    <a href="#" onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=quizdetails&layout=quizdetails&pid=<?php echo intval($course_id); ?>&userid=<?php echo intval($item["user_id"]); ?>&quiz=<?php echo intval($item["quiz_id"]); ?>&tmpl=component&action=mark');">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                <?php
                        }
                    }
                ?>
            </table>
                
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="controller" value="guruAuthor" />
            <input type="hidden" name="view" value="guruauthor" />
            <input type="hidden" name="task" value="mark" />
            <input type="hidden" name="id" value="<?php echo intval($course_id); ?>" />
        </form>
    </div>
</div>