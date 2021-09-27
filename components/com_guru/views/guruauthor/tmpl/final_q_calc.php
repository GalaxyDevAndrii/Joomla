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

$doc = JFactory::getDocument();
//$doc->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet("components/com_guru/css/tabs.css");
$input = JFactory::getApplication()->input;
$data_get = $input->get->getArray();
$data_post = $input->post->getArray();
?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="text/javascript">
    function isFloat(nr){
        return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
    }
    
    function savequiz (pressbutton){
        var form = document.adminForm;
        if (pressbutton == 'save_quizFE' || pressbutton == 'apply_quizFE') {
            if (form['name'].value == "") {
                alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
                return false;
            } 
            else{
                max_score_pass = document.getElementById("max_score_pass").value;
                limit_time_l = document.getElementById("limit_time_l").value;
                limit_time_f = document.getElementById("limit_time_f").value;
                
                if(!isFloat(max_score_pass) || max_score_pass <= 0){
                    alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
                    return false;
                }
                
                all_quizes_nr = document.getElementById("all_quizes_nr").value;
                if(all_quizes_nr == 0){
                    if(confirm('<?php echo addslashes(JText::_("GURU_ADD_QUIZ_TO_FINAL")); ?>')){
                        window.parent.location.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=editQuizFE&v=0';
                    }
                    return false;
                }
                
                //submitform( pressbutton );
                form.task.value = pressbutton;
                form.submit();
            }
        }
        else{
            //submitform(pressbutton);
            form.task.value = pressbutton;
            form.submit();
        }
    }
    
    function makeVisible(tab){
        if(tab == 'tab1'){
            document.getElementById("li_general").className="active";
            document.getElementById("li_quizzes").className="";
            document.getElementById("li_message").className="";
            
            document.getElementById("general1").className="tab-pane active";
            document.getElementById("general1").style.display="block";
            
            document.getElementById("quizzesincl").className="";
            document.getElementById("quizzesincl").style.display="none";
            
            document.getElementById("message").className="";
            document.getElementById("message").style.display="none";
        }
        else if(tab == 'tab2'){
            document.getElementById("li_quizzes").className="active";
            document.getElementById("li_general").className="";
            document.getElementById("li_message").className="";
            
            document.getElementById("general1").className="";
            document.getElementById("general1").style.display="none";
            
            document.getElementById("quizzesincl").className="tab-pane active";
            document.getElementById("quizzesincl").style.display="block";
            
            document.getElementById("message").className="";
            document.getElementById("message").style.display="none";
        }
        else if(tab == 'tab3'){
            document.getElementById("li_quizzes").className="";
            document.getElementById("li_general").className="";
            document.getElementById("li_message").className="active";
            
            document.getElementById("general1").className="";
            document.getElementById("general1").style.display="none";
            
            document.getElementById("quizzesincl").className="";
            document.getElementById("quizzesincl").style.display="none";
            
            document.getElementById("message").className="tab-pane active";
            document.getElementById("message").style.display="block";
        }
    }

    function changeTab(tabid){
        jQuery(".header-tab").each(function(){
            jQuery(this).removeClass("uk-active");
        });

        jQuery(".content-tab").each(function(){
            jQuery(this).removeClass("uk-active");
        });

        jQuery("#"+tabid).addClass("uk-active");
        jQuery("#"+tabid+"-content").addClass("uk-active");
    }
</script>

<div id="g_myquizzesfeaddedit" class="gru-myquizzesfeaddedit">
    <?php echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <div class="uk-grid uk-margin wk-grid wk-margin">
        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2">
            <h2 class="gru-page-title">
                <?php
                    if($program->id < 1){
                        echo JText::_('GURU_NEW_FE_CREATION');
                    }
                    else{
                        echo JText::_('GURU_EDIT_EXISTING_FE');
                    }
                ?>
           </h2>
        </div>
        <div class="uk-width-1-2 uk-hidden-small uk-text-right wk-width-1-2 wk-hidden-small wk-text-right">
            <div class="uk-button-group wk-button-group">
               <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:savequiz('apply_quizFE');" />
               <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:savequiz('save_quizFE');" />
               <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes"); ?>';" />
            </div>
        </div>
        <div class="uk-width-1-1 uk-visible-small wk-width-1-1 wk-visible-small">
           <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:savequiz('apply_quizFE');" />
           <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:savequiz('save_quizFE');" />
           <input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes"); ?>';" />
        </div>
    </div>
    
    <form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="uk-form uk-form-horizontal wk-form wk-form-horizontal">


        <?php
            $class1 = "uk-active";
            $class2 = "";
            $class3 = "";
            
			$session = JFactory::getSession();
			$registry = $session->get('registry');
			$added_quiz = $registry->get('added_quiz', "");
			
            if(isset($added_quiz) && $added_quiz == 1){
				$registry->set('added_quiz', "0");
                $class1 = "";
                $class2 = "uk-active";
                $class3 = "";
            }
        ?>
        
        <ul data-uk-tab="{connect:'#tab-content'}" class="uk-tab uk-padding-remove wk-tab wk-padding-remove">
            <li id="general-tab" onclick="javascript:changeTab('general-tab'); return false;" class="header-tab <?php echo $class1; ?>"><a href="#"><?php echo JText::_('GURU_GENERAL');?></a></a></li>
            <li id="quizzes-tab" onclick="javascript:changeTab('quizzes-tab'); return false;" class="header-tab <?php echo $class2; ?>"><a href="#"><?php echo JText::_('GURU_QUIZZES_INCLUDED');?></a></li>
            <li id="pass-tab" onclick="javascript:changeTab('pass-tab'); return false;" class="header-tab <?php echo $class3; ?>"><a href="#"><?php echo JText::_('GURU_PASS_FAIL_MESSAGE');?></a></li>
        </ul>
        
        <ul id="tab-content" class="uk-switcher uk-margin uk-padding-remove wk-switcher wk-margin wk-padding-remove">
            <li id="general-tab-content" class="content-tab <?php echo $class1; ?> uk-margin-top wk-margin-top">
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_NAME");?>:
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <input type="text" id="name" name="name" value="<?php echo $program->name; ?>" />
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_NAME"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_PRODDESC");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <textarea name="description" id="description" cols="40" rows="8"><?php echo stripslashes($program->description); ?></textarea>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_PRODDESC"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_SHOW_CORRECT_ANS");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls" id="show_correct_ans">
                        <?php
                            $no_checked = "";
                            $yes_cheched = "";
                            
                            if($program->show_correct_ans == "0"){
                                $no_checked = 'checked="checked"';
                            }
                            else{
                                $yes_cheched = 'checked="checked"';
                            }
                        ?>
                        <input type="hidden" name="show_correct_ans" value="0">
                        <input type="checkbox" <?php echo $yes_cheched; ?> value="1" name="show_correct_ans" class="ace-switch ace-switch-5">
                        
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SHOW_CORRECT_ANS"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_MINIMUM_SCORE_FINAL_QUIZ");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <?php
                            if(isset($program->max_score)){
                                $program->max_score = $program->max_score;
                            }
                            else{
                                $program->max_score = 70;
                            }
                        ?>
                        <input class="input-mini pull-left" type="text" id="max_score_pass" name="max_score_pass" size="6" value="<?php echo $program->max_score;?>" style="float:left !important;" />
                        <span class="pull-left" style="padding:0px 5px; line-height:30px;">%</span>
                        <select id="show_max_score_pass" name="show_max_score_pass"  class="input-small" >
                            <option value="0" <?php if($program->pbl_max_score == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->pbl_max_score == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_QUIZ_CAN_BE_TAKEN");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select id="nb_quiz_taken" name="nb_quiz_taken" class="pull-left input-mini" >
                            <option value="1" <?php if($program->time_quiz_taken == "1"){echo 'selected="selected"'; }?> >1</option>
                            <option value="2" <?php if($program->time_quiz_taken == "2"){echo 'selected="selected"'; }?> >2</option>
                            <option value="3" <?php if($program->time_quiz_taken == "3"){echo 'selected="selected"'; }?> >3</option>
                            <option value="4" <?php if($program->time_quiz_taken == "4"){echo 'selected="selected"'; }?> >4</option>
                            <option value="5" <?php if($program->time_quiz_taken == "5"){echo 'selected="selected"'; }?> >5</option>
                            <option value="6" <?php if($program->time_quiz_taken == "6"){echo 'selected="selected"'; }?> >6</option>
                            <option value="7" <?php if($program->time_quiz_taken == "7"){echo 'selected="selected"'; }?> >7</option>
                            <option value="8" <?php if($program->time_quiz_taken == "8"){echo 'selected="selected"'; }?> >8</option>
                            <option value="9" <?php if($program->time_quiz_taken == "9"){echo 'selected="selected"'; }?> >9</option>
                            <option value="10"<?php if($program->time_quiz_taken == "10"){echo 'selected="selected"'; }?> >10</option>
                            <option value="20" <?php if($program->time_quiz_taken == "20"){echo 'selected="selected"'; }?> >20</option>
                            <option value="30" <?php if($program->time_quiz_taken == "30"){echo 'selected="selected"'; }?> >30</option>
                            <option value="40" <?php if($program->time_quiz_taken == "40"){echo 'selected="selected"'; }?> >40</option>
                            <option value="50" <?php if($program->time_quiz_taken == "50"){echo 'selected="selected"'; }?> >50</option>
                            <option value="11" <?php if($program->time_quiz_taken == "11"){echo 'selected="selected"'; }?> ><?php echo JText::_("GURU_UNLIMPROMO");?></option>
                        </select>
                        <div class="pull-left" style="padding:0px 5px; line-height:30px;">
                            <?php echo JText::_("GURU_TIMES_T"); ?>
                        </div>
                        <select id="show_nb_quiz_taken" name="show_nb_quiz_taken" class="input-small" >
                            <option value="0" <?php if($program->show_nb_quiz_taken == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_nb_quiz_taken == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_SELECT_UP_TO");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select id="nb_quiz_select_up" name="nb_quiz_select_up" class="input-mini pull-left" >
                            <?php
                            if (isset($program->nb_quiz_select_up)){
                                $program->nb_quiz_select_up = $program->nb_quiz_select_up;
                            }
                            else{
                                $program->nb_quiz_select_up = 10;
                            }
                            
                                for($i=1; $i<=100; $i++){?>
                                    <option value="<?php echo $i;?>" <?php if($program->nb_quiz_select_up == $i){echo 'selected="selected"'; }?> ><?php echo $i;?></option>
                                <?php 
                                }
                                ?>
                        </select>
                        <div class="pull-left" style="padding:0px 5px; line-height:30px;">
                            <?php echo JText::_("GURU_QUESTION_RANDOM"); ?>
                        </div>
                        <select id="show_nb_quiz_select_up" name="show_nb_quiz_select_up" class="input-small" >
                            <option value="0" <?php if($program->show_nb_quiz_select_up == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_nb_quiz_select_up == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_NUMBER_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_QUESTIONS_PER_PAGE");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <?php
                            $questions_per_page = $program->questions_per_page;
                        ?>
                        <select name="questions_per_page" class="input-mini">
                            <option value="5" <?php if($questions_per_page == "5"){echo 'selected="selected"';} ?> >5</option>
                            <option value="10" <?php if($questions_per_page == "10"){echo 'selected="selected"';} ?> >10</option>
                            <option value="15" <?php if($questions_per_page == "15"){echo 'selected="selected"';} ?> >15</option>
                            <option value="20" <?php if($questions_per_page == "20"){echo 'selected="selected"';} ?> >20</option>
                            <option value="25" <?php if($questions_per_page == "25"){echo 'selected="selected"';} ?> >25</option>
                            <option value="30" <?php if($questions_per_page == "30"){echo 'selected="selected"';} ?> >30</option>
                            <option value="50" <?php if($questions_per_page == "50"){echo 'selected="selected"';} ?> >50</option>
                            <option value="100" <?php if($questions_per_page == "100"){echo 'selected="selected"';} ?> >100</option>
                            <option value="0" <?php if($questions_per_page == "0"){echo 'selected="selected"';} ?> >All</option>
                        </select>
                        &nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUESTIONS_PER_PAGE_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_IF_STUDENT_FAILED");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select name="student_failed">
                            <option value="0" <?php if($program->student_failed == "0"){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_STUDENT_CONTINUE"); ?></option>
                            <option value="1" <?php if($program->student_failed == "1"){echo 'selected="selected"';} ?> ><?php echo JText::_("GURU_STUDENT_NOT_CONTINUE"); ?></option>
                        </select>
                        &nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_IF_STUDENT_FAILED_TIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-alert wk-alert"><?php echo JText::_("GURU_TIMER"); ?></div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_EXAM_LIMIT_TIME");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <?php
                            if(isset($program->limit_time)){
                                $program->limit_time = $program->limit_time;
                            }
                            else{
                                $program->limit_time = 3;
                            }
                        ?>
                        
                        <select id="limit_time_l" name="limit_time_l" class="pull-left" style="margin-right:10px;">
                            <option value="0"> <?php echo JText::_("GURU_UNLIMPROMO"); ?> </option>
                            <?php
                                for($i=1; $i<=150; $i++){
                                    $selected = "";
                                    $time = JText::_("GURU_MINUTES");
                                    
                                    if($i == $program->limit_time){
                                        $selected = 'selected="selected"';
                                    }
                                    
                                    if($i == 1){
                                        $time = JText::_("GURU_MINUTE");
                                    }
                                    
                                    echo '<option value="'.$i.'" '.$selected.'>'.$i." ".$time.'</option>';
                                }
                            ?>
                        </select>
                        
                        <select id="show_limit_time" name="show_limit_time" class="input-small" >
                            <option value="0" <?php if($program->show_limit_time == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_limit_time == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_QUIZ_LIMIT_TIME_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_SHOW_COUNTDOWN");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select id="show_countdown" name="show_countdown" class="input-small" >
                            <option value="0" <?php if($program->show_countdown == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_countdown == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_SHOW_COUNTDOWN_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_FINISH_ALERT");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <?php
                            if (isset($program->limit_time_f)){
                                $program->limit_time_f = $program->limit_time_f;
                              }
                              else{
                                $program->limit_time_f = 1;
                              }
                        ?>
                        
                        <select id="limit_time_f" name="limit_time_f" class="pull-left">
                            <option value="2" <?php if($program->limit_time_f == "2"){echo 'selected="selected"';} ?> > 2 <?php echo JText::_("GURU_MINUTES"); ?> </option>
                            <option value="1" <?php if($program->limit_time_f == "1"){echo 'selected="selected"';} ?> > 1 <?php echo JText::_("GURU_MINUTE"); ?> </option>
                            <option value="30" <?php if($program->limit_time_f == "30"){echo 'selected="selected"';} ?> > 30 <?php echo JText::_("GURU_SECONDS"); ?> </option>
                            <option value="15" <?php if($program->limit_time_f == "15"){echo 'selected="selected"';} ?> > 15 <?php echo JText::_("GURU_SECONDS"); ?> </option>
                        </select>
                        
                        <span class="pull-left" style="padding:0px 5px; line-height:30px;">
                            <?php echo JText::_('GURU_MINUTES_BEFORE_IS_UP'); ?>
                        </span>
                        
                        <select id="show_finish_alert" name="show_finish_alert" class="input-small" >
                            <option value="0" <?php if($program->show_finish_alert == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_finish_alert == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_FINISH_ALERT_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
            </li>
            
            <li id="quizzes-tab-content" class="content-tab <?php echo $class2; ?> uk-margin-top wk-margin-top">
                <?php if ($program->id): ?>
                <a href="#" class="uk-button uk-button-primary wk-button wk-button-primary" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addquizzes&tmpl=component&cid=<?php echo $program->id;?>'); return false;">
                    <?php echo JText::_('GURU_ADD_QUIZZES');?>
                </a>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZZES"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
                
                <?php                       
                    $db =JFactory::getDBO();
                    $e = JFactory::getApplication()->input->get("e", "0");
                    $cid = JFactory::getApplication()->input->get("cid", "0");
                    
                    $sql = "SELECT  quizzes_ids FROM #__guru_quizzes_final WHERE qid=".intval($cid);

                    $db->setQuery($sql);
                    $db->execute();
                    $result = $db->loadAssocList();
                    
                    $listofids = array();
                    foreach($result as $value){
                        $listofids = array_merge($listofids, (array)$value["quizzes_ids"]);
                    }
                    
                    $listofids = implode(",", array_unique($listofids));
                    $listofids = str_replace(",,", ",", $listofids);
                    $listofids = "0".$listofids;
                    
                    $sql = "SELECT id, name, published FROM #__guru_quiz WHERE id IN (".$listofids.")";
                    $db->setQuery($sql);
                    $db->execute();
                    $result_name=$db->loadAssocList();  
                ?>
                <table class="uk-table uk-table-striped wk-table wk-table-striped">                  
                    <tbody id="rowquestion" <?php if(!isset($result_name)) { echo 'style="display: none;"';} ?>>
                        <tr>
                            <th width="42%">
                                <strong><?php echo JText::_('GURU_QUIZZES_FRONTEND');?></strong>
                            </th>
                            <th width="17%">
                                <strong><?php echo JText::_('GURU_REMOVE');?></strong>
                            </th>
                            <th width="12%">
                               <!-- <strong><?php //echo JText::_('Edit');?></strong>-->
                            </th>
                            <th width="14%">
                                <strong><?php echo JText::_('GURU_PUBLISHED');?></strong>
                            </th>
                        </tr>                                   
                     <?php 
                    if(isset($data_post['deleteq'])){
                            $hide_q2del = $data_post['deleteq'];
                    }
                    else{
                        $hide_q2del = ',';
                    }
                    $hide_q2del = explode(',', $hide_q2del);
                     
                     for ($i = 0; $i < count($result_name); $i++){ 
                        $link2_remove = '<span class="btn btn-danger" onClick="delete_fq('.$result_name[$i]["id"].','.$input->get('cid', '0').', 1)">Remove</span>';
                        $sql = "SELECT  published FROM #__guru_quizzes_final WHERE qid=".$result_name[$i]["id"];
                        $db->setQuery($sql);
                        $db->execute();
                        $published=$db->loadColumn();   
                     ?>
                     
                      <tr id="trfque<?php echo $result_name[$i]["id"]; ?>" <?php if(in_array($result_name[$i]["id"],$hide_q2del)) { ?> style="display:none" <?php } ?> class="row<?php echo $i%2; ?>">
                            <td width="42%">
                                <strong><?php echo $result_name[$i]["name"];?></strong>
                            </td>
                             <td width="17%">
                                <?php echo $link2_remove;  ?>
                            </td>
                            <td width="12%">
                                <?php   if(isset($published["0"]) && $published["0"] == 1){ 
                                    echo "<input type='hidden' id='publ".$result_name[$i]["id"]."' name='publish_q[".$result_name[$i]["id"]."]' value='1' />";
                                } 
                                else{ 
                                    echo "<input type='hidden' id='publ".$result_name[$i]["id"]."' name='publish_q[".$result_name[$i]["id"]."]' value='0' />";
                                }?>
                            </td>
                            <td width="14%" id="publishing<?php echo $result_name[$i]["id"];?>">
                                <?php 
                                    if($result_name[$i]["published"] == 1) {
                                        echo '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\''.$result_name[$i]["id"].'\');"></a>';
                                    }
                                    else{
                                        echo '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\''.$result_name[$i]["id"].'\');"></a>';
                                    }
                                ?>
                            </td>
                       </tr>      
                       <?php } ?>     
                        
                     
                        <input type="hidden" value="<?php if (isset($data_post['newquizq'])) echo $data_post['newquizq'];?>" id="newquizq" name="newquizq" >
                        <input type="hidden" value="<?php if (isset($data_post['deleteq'])) echo $data_post['deleteq'];?>" id="deleteq" name="deleteq" >
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="uk-alert wk-alert"><?php echo JText::_("GURU_SAVE_QUIZ_FIRST"); ?></div>
                <?php endif; ?>
            </li>
            
            <li id="pass-tab-content" class="content-tab <?php echo $class3; ?> uk-margin-top wk-margin-top">
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_('GURU_PASS_MESSAGE'); ?>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <?php
                            $doc = JFactory::getDocument();
                            //$doc->addScript(JURI::root().'components/com_guru/js/redactor.min.js');
                            $doc->addStyleSheet(JURI::root().'components/com_guru/css/redactor.css');
                        ?>
                        <textarea id="text" name="pass_message" class="useredactor" style="width:70%; height:100px;"><?php echo $program->pass_message; ?></textarea>
                        <?php
                            $upload_script = 'jQuery( document ).ready(function(){
                                                jQuery(".useredactor").redactor({
                                                     buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
                                                });
                                                jQuery(".redactor_useredactor").css("height","300px");
                                              });';
                            $doc->addScriptDeclaration($upload_script);
                        ?>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_('GURU_FAIL_MESSAGE'); ?>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <textarea id="text" name="fail_message" class="useredactor" style="width:70%; height:100px;"><?php echo $program->fail_message; ?></textarea>
                        <?php
                            $upload_script = 'jQuery( document ).ready(function(){
                                                jQuery(".useredactor").redactor({
                                                     buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
                                                });
                                                jQuery(".redactor_useredactor").css("height","300px");
                                              });';
                            $doc->addScriptDeclaration($upload_script);
                        ?>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_('GURU_PENDING_MESSAGE'); ?>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <textarea id="text" name="pending_message" class="useredactor" style="width:70%; height:100px;"><?php echo $program->pending_message; ?></textarea>
                        <?php
                            $upload_script = 'jQuery( document ).ready(function(){
                                                jQuery(".useredactor").redactor({
                                                     buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
                                                });
                                                jQuery(".redactor_useredactor").css("height","300px");
                                              });';
                            $doc->addScriptDeclaration($upload_script);
                        ?>
                    </div>
                </div>
                
            </li>
        </ul>
           
        <input type="hidden" name="id" value="<?php echo $program->id; ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="valueop" value="<?php echo $value_option; ?>"/>
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="image" value="<?php if(isset($data_post['image'])){echo $data_post['image'];}else{echo $program->image;}?>" />
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="time_format" id="time_format" value="<?php echo @$format; ?>" />
        <input type="hidden" name="all_quizes_nr" id="all_quizes_nr" value="<?php echo intval(count($result_name)); ?>" />
    </form>
</div>