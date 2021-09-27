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
JHtml::_('behavior.framework');

require_once (JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

$db = JFactory::getDBO();
$div_menu = $this->authorGuruMenuBar();
$myquizzes = $this->myquizzes;
$user = JFactory::getUser();
$user_id = $user->id;
$v = "";

$config = $this->config;
$allow_teacher_action = json_decode($config->st_authorpage);//take all the allowed action from administator settings
$teacher_add_quizzesfe = @$allow_teacher_action->teacher_add_quizzesfe; //allow or not action Add quiz
$teacher_edit_quizzesfe = @$allow_teacher_action->teacher_edit_quizzesfe; //allow or not action Edit quiz

$selectcoursesd = JFactory::getApplication()->input->get("selectcoursesd", "0");
$doc = JFactory::getDocument();
//$doc->addScript('components/com_guru/js/jquery-dropdown.js');

$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_QUIZZES')));
$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="application/javascript">
    function deleteQuiz(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            if(confirm("<?php echo JText::_("GURU_REMOVE_AUTHOR_COURSES"); ?>")){
                document.adminForm.task.value='removeQuiz';
                document.adminForm.submit();
            }
        }   
    }
    function duplicateQuiz(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            document.adminForm.task.value='duplicateQuiz';
            document.adminForm.submit();
        }
    }
    
    function unpublishQuiz(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            document.adminForm.task.value='unpublish_quiz';
            document.adminForm.submit();
        }
    }
    
    function publishQuiz(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            document.adminForm.task.value='publish_quiz';
            document.adminForm.submit();
        }   
    }
    
    function editOptions(){
        display = document.getElementById("button-options").style.display;
        
        if(display == "none"){
            document.getElementById("button-options").style.display = "";
            document.getElementById("new-options").value = "<?php echo JText::_('GURU_NEW'); ?> \u2227";
        }
        else{
            document.getElementById("button-options").style.display = "none";
            document.getElementById("new-options").value = "<?php echo JText::_('GURU_NEW'); ?> \u2228";
        }
    }
    
    function editOptions2(){
        display = document.getElementById("button-options2").style.display;
        
        if(display == "none"){
            document.getElementById("button-options2").style.display = "";
            document.getElementById("new-options2").value = "<?php echo JText::_('GURU_NEW'); ?> \u2227";
        }
        else{
            document.getElementById("button-options2").style.display = "none";
            document.getElementById("new-options2").value = "<?php echo JText::_('GURU_NEW'); ?> \u2228";
        }
    }

    function addNewQuiz(){
        if(jQuery("#list-sub-button").is(":visible")){
            jQuery("#list-sub-button").hide();
        }
        else{
            jQuery("#list-sub-button").show();
        }
    }
</script>

<style>
    div.g_inline_child button.btn{
        height:26px !important;
    }
</style>

<div id="g_myquizzesauthor" class="clearfix com-cont-wrap">
    <?php   echo $div_menu; //MENU TOP OF AUTHORS?>
    <!--BUTTONS -->
    <div class="g_inline_child clearfix">
        <div class="uk-grid uk-margin-top uk-margin-bottom wk-grid wk-margin-top wk-margin-bottom">
            <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo JText::_('GURU_AUTHOR_MY_QUIZZES');?></h2></div>
            <div class="uk-width-1-2 uk-text-right uk-hidden-small wk-width-1-2 wk-text-right wk-hidden-small">
                <div class="uk-button-group wk-button-group">
                <?php
                    if($teacher_add_quizzesfe == 0){
                ?>
                            <div class="open-new-button uk-button-dropdown wk-button-dropdown no-padding inline-block" data-uk-dropdown="{mode:'click'}">                
                                <!-- This is the button toggling the dropdown -->
                                <button class="uk-button uk-button-success wk-button wk-button-success inline-block" onclick="addNewQuiz(); return false;"><?php echo JText::_('GURU_NEW'); ?>&nbsp;<span class="fa fa-caret-down"></span></button>
                            
                                <!-- This is the dropdown -->
                                <div id="list-sub-button" class="uk-dropdown uk-dropdown-small wk-dropdown wk-dropdown-small" style="top: 30px; left: 0px;">
                                    <ul class="uk-nav uk-nav-dropdown uk-padding-remove uk-text-left wk-nav wk-nav-dropdown wk-padding-remove wk-text-left">
                                        <li>
                                            <a href="#" onclick="window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=0';">
                                                <?php echo JText::_("GURU_REGULAR_QUIZ"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=1';">
                                                <?php echo JText::_("GURU_FINAL_EXAM_QUIZ"); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                <?php
                    }
                ?> 
                    <button class="uk-button wk-button" onclick="duplicateQuiz();"><?php echo JText::_('GURU_DUPLICATE'); ?></button>
                    <button class="uk-button uk-button-danger wk-button wk-button-danger" onclick="deleteQuiz();"><?php echo JText::_('GURU_DELETE'); ?></button>
                </div>
            </div>

            <div class="uk-width-1-2 uk-text-right uk-visible-small wk-width-1-2 wk-text-right wk-visible-small">
                <div class="uk-button-group wk-button-group">
                <?php
                    if($teacher_add_quizzesfe == 0){
                ?>
                            <div class="uk-button-dropdown uk-width-1-1 wk-button-dropdown wk-width-1-1 no-padding" data-uk-dropdown="{mode:'click'}">                
                                <!-- This is the button toggling the dropdown -->
                                <button class="uk-button uk-button-success wk-button wk-button-success"><?php echo JText::_('GURU_NEW'); ?>&nbsp;<span class="fa fa-caret-down"></span></button>
                            
                                <!-- This is the dropdown -->
                                <div class="uk-dropdown uk-dropdown-small wk-dropdown wk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown wk-nav wk-nav-dropdown">
                                        <li>
                                            <a href="#" onclick="window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=0';">
                                                <?php echo JText::_("GURU_REGULAR_QUIZ"); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" onclick="window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=1';">
                                                <?php echo JText::_("GURU_FINAL_EXAM_QUIZ"); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                <?php
                    }
                ?> 
                    <button class="uk-button uk-width-1-1 wk-button wk-width-1-1" onclick="duplicateQuiz();"><?php echo JText::_('GURU_DUPLICATE'); ?></button>
                    <button class="uk-button uk-button-danger uk-width-1-1 wk-button wk-button-danger wk-width-1-1" onclick="deleteQuiz();"><?php echo JText::_('GURU_DELETE'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- -->

    
    <div id="g_myquizzesauthorcontent" class="g_sect clearfix">
        <form class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data" action="<?php echo JUri::getInstance() ?>" style="padding-top:10px;">
            <div class="gru-page-filters">
                <div class="gru-filter-item">
                    <select class="uk-form-width-small wk-form-width-small" name="selectcoursesd" id="selectcoursesd" onchange="document.adminForm.submit();" >
                        <option value="0" <?php if($selectcoursesd == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_COURSE");?></option>
                        <?php
                            $my_courses = $this->getMyCourses();
                            if(isset($my_courses) && count($my_courses) > 0){
                                foreach($my_courses as $key=>$course){
                                    $selected = "";
                                    if($course["id"] == $selectcoursesd){
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="'.$course["id"].'" '.$selected.'>'.$course["name"].'</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
                
                <div class="gru-filter-item">
                    <select onchange="document.adminForm.submit()" name="quiz_select_type" class="uk-form-width-small wk-form-width-small">
                        <?php 
							$session = JFactory::getSession();
							$registry = $session->get('registry');
							$quiz_select_type = $registry->get('quiz_select_type', NULL);
							
                            if(isset($quiz_select_type)){
                                $pb = $quiz_select_type;
                            }
                            
							if(isset($data_post['quiz_select_type'])){
                                $pb = $data_post['quiz_select_type'];
                            }
							
                            if(!isset($pb)){
								$pb=NULL;
							}
                        ?>
                        <option <?php if($pb=='0') { echo "selected='selected'";} ?> value="0"><?php echo JText::_("GURU_SELECT_TYPE2"); ?></option>
                        <option <?php if($pb=='1') { echo "selected='selected'";} ?> value="1"><?php echo JText::_("GURU_QUIZZES_FILTER"); ?></option>
                        <option <?php if($pb=='2') { echo "selected='selected'";} ?> value="2"><?php echo JText::_("GURU_FQUIZZES_FILTER"); ?></option>
                    </select>
                </div>
                
                <div class="gru-filter-item">
                    <input type="text" class="form-control" name="search_quiz" id="filter_search" value="<?php if(isset($data_post['search_quiz'])){echo $data_post['search_quiz'];} ?>" class="uk-form-width-medium wk-form-width-medium" />
                    <button class="uk-button uk-button-primary wk-button wk-button-primary" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
                </div>
            </div>
             
            <table id="g_authorquiz" class="uk-table uk-table-striped wk-table wk-table-striped">
                <tr>
                    <th class="g_cell_1"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                    <th class="g_cell_2 hidden-phone"><?php echo JText::_('GURU_ID'); ?></th>
                    <th class="g_cell_3"> <?php echo JText::_('GURU_NAME'); ?></th>
                    <th class="g_cell_4 hidden-phone"><?php echo JText::_('GURU_TYPE'); ?></th>
                    <th class="g_cell_5 hidden-phone"><?php echo JText::_('GURU_STATS'); ?></th>
                    <th class="g_cell_6 hidden-phone"><?php echo JText::_('GURU_TAB_REQUIREMENTS_COURSES'); ?></th>
                    <th class="g_cell_7"><?php echo JText::_("GURU_PROGRAM_DETAILS_STATUS"); ?></th>
                </tr>
                <?php
                @$n = count($myquizzes);
                
                if(isset($myquizzes) && is_array($myquizzes) && count($myquizzes) > 0 && $myquizzes !== FALSE){
                    for ($i = 0; $i < $n; $i++):
                        $id = $myquizzes[$i]->id;
                        $checked = JHTML::_('grid.id', $i, $id);
                        $published = JHTML::_('grid.published', $myquizzes, $i );
                        if($myquizzes[$i]->is_final == 0){
                            $v = 0; 
                        }
                        else{
                            $v = 1;
                        }
                ?>
                    <tr class="guru_row">
                        <td class="g_cell_1"><?php echo $checked;?></td>
                        <td class="g_cell_2 hidden-phone"><?php echo $id;?></td>
                        <td class="g_cell_3">
                        <?php 
                        if($teacher_edit_quizzesfe == 0){
                        ?>
                            <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=editQuizFE&cid=".$id."&v=".$v."&e=1"); ?>"><?php echo $myquizzes[$i]->name; ?></a>
                        <?php
                        }
                        else{
                            echo $myquizzes[$i]->name;
                        }
                        ?>
                        </td>
                        
                        <td class="g_cell_4 hidden-phone">
                         <?php
                             if($myquizzes[$i]->is_final == 0){
                                echo JText::_('GURU_MEDIATYPEQUIZ'); 
                                
                             }
                             else{
                                echo JText::_('GURU_FQUIZ');
                             }
                         
                         ?>
                        </td>
                        <td class="g_cell_5 hidden-phone"><a href="<?php echo JUri::root() ?>index.php?option=com_guru&view=guruauthor&task=quizz_stats&id=<?php echo intval($myquizzes[$i]->id); ?>"><i class="fa fa-list"></i></a></td>                                                             
                        <td class="g_cell_6 hidden-phone">
                             <?php
                                $itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
								
								$helper = new guruHelper();
								$itemid_seo = $helper->getSeoItemid();
								$itemid_seo = @$itemid_seo["guruprograms"];
								
								if(intval($itemid_seo) > 0){
									$itemid = intval($itemid_seo);
								}
								
                                $sql = "select type_id from #__guru_mediarel where layout='12' and media_id=".intval($id)." and type='scr_m'";
                                $db->setquery($sql);
                                $db->execute();
                                $result_type_id = $db->loadColumn();

                                if(isset($result_type_id) && intval($result_type_id) != 0){
                                    
                                    $sql = "select p.id, p.name, p.alias from #__guru_days d, #__guru_program p where d.id IN (SELECT type_id from #__guru_mediarel where media_id IN (".implode(",",$result_type_id).") and type='dtask') and d.pid=p.id and (p.author=".intval($user_id)." OR p.author like '%|".intval($user_id)."|%')";
                                    $db->setquery($sql);
                                    $db->execute();
                                    $result_pid = $db->loadAssocList();
                                    $comma = ", ";
                                    
                                    foreach($result_pid as $key=>$value){
                                        $alias = isset($value["alias"]) ? trim($value["alias"]) : JFilterOutput::stringURLSafe($value["name"]);
                                        
                                        $helper = new guruHelper();
                                        $itemid_menu = $helper->getCourseMenuItem(intval($value["id"]));
                                        $itemid_course = $itemid;

                                        if(intval($itemid_menu) > 0){
                                            $itemid_course = intval($itemid_menu);
                                        }

                                        $link = JRoute::_("index.php?option=com_guru&view=guruPrograms&task=view&cid=".intval($value["id"])."-".$alias."&Itemid=".intval($itemid_course));

                                        if(($key+1) == count($result_pid)){
                                            $comma = "";
                                        }
                                        echo '<a href="'.$link.'">'. $value["name"]."</a>".$comma;
                                    }
                                    
                                }
                                else{
                                    
                                    echo "-";
                                }                                                               
                                
                            ?>
                       </td>
                      <td class="g_cell_7">
                             <?php 
                                if($myquizzes[$i]->published == 0){
                                    echo '<i class="fa fa-times-circle"></i>';
                                }
                                else{
                                    echo '<i class="fa fa-check-circle-o"></i>';
                                }
                              ?>
                        </td>
                    </tr>
                <?php 
                        endfor;
                    }
                ?>  
                   
            </table>
           
           <?php
                echo $this->pagination->getLimitBox();
                $pages = $this->pagination->getPagesLinks();
                include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
                $helper = new guruHelper();
                $pages = $helper->transformPagination($pages);
                echo $pages;
            ?>
            
            <input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get("task", "authorquizzes"); ?>" />
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="controller" value="guruAuthor" />
            <input type="hidden" name="boxchecked" value="" />
        </form>
   </div> 
 </div>              