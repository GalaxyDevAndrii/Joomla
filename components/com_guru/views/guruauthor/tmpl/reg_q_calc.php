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
$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="text/javascript">

    function isFloat(nr){
        return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
    }
    
    function save_quizFE(pressbutton){
        var form = document.adminForm;
        if (pressbutton == 'save_quizFE' || pressbutton == 'apply_quizFE') {
            if (form['name'].value == "") {
                alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
            } 
            else {
                //---------------------------------
                max_score_pass = document.getElementById("max_score_pass").value;
                limit_time_l = document.getElementById("limit_time_l").value;
                limit_time_f = document.getElementById("limit_time_f").value;
                nb_quiz_select_up = document.getElementById("nb_quiz_select_up").value;
                
                if(!isFloat(max_score_pass) || max_score_pass <= 0){
                    alert("<?php echo JText::_("GURU_INVALID_NUMBER_AVG"); ?>");
                    return false;
                }
                else if(nb_quiz_select_up == 'text1'){
                    alert("<?php echo JText::_("GURU_ADD_QUESTION_ALERT"); ?>");
                    return false;
                }
                //---------------------------------
                //submitform( pressbutton );
                form.task.value = pressbutton;
                form.submit();
            }
        }
        else {
            //submitform( pressbutton );
            form.task.value = pressbutton;
            form.submit();
        }
    }
    function changeSort(){
        var changeSort="";
        changeSort = document.getElementById("sortquestion").value;
        if(changeSort == 'ASC'){
            document.getElementById("sortquestion").value = "DESC";
        }
        else{
            document.getElementById("sortquestion").value = "ASC";
        }
        document.adminForm.task.value = 'editQuizFE';
        document.adminForm.submit();
    }
    
    function makeVisible(tab){
        if(tab == 'tab1'){
            document.getElementById("li_general").className="active";
            document.getElementById("li_quizzes").className="";
            document.getElementById("li_message").className="";
            
            document.getElementById("general").className="tab-pane active";
            document.getElementById("general").style.display="block";
            
            document.getElementById("question").className="";
            document.getElementById("question").style.display="none";
            
            document.getElementById("message").className="";
            document.getElementById("message").style.display="none";
        }
        else if(tab == 'tab2'){
            document.getElementById("li_quizzes").className="active";
            document.getElementById("li_general").className="";
            document.getElementById("li_message").className="";
            
            document.getElementById("general").className="";
            document.getElementById("general").style.display="none";
            
            document.getElementById("question").className="tab-pane active";
            document.getElementById("question").style.display="block";
            
            document.getElementById("message").className="";
            document.getElementById("message").style.display="none";
        }
        else if(tab == 'tab3'){
            document.getElementById("li_quizzes").className="";
            document.getElementById("li_general").className="";
            document.getElementById("li_message").className="active";
            
            document.getElementById("general").className="";
            document.getElementById("general").style.display="none";
            
            document.getElementById("question").className="";
            document.getElementById("question").style.display="none";
            
            document.getElementById("message").className="tab-pane active";
            document.getElementById("message").style.display="block";
        }
    }
    
    function editOptionsQ(){
        display = document.getElementById("button-options2").style.display;
                    
        if(display == "none"){
            document.getElementById("button-options2").style.display = "";
        }
        else{
            document.getElementById("button-options2").style.display = "none";
        }
    }
    
    jQuery(document).click(function(e){
        if (jQuery(e.target).attr('id') != 'guru-dropdown-toggle' && jQuery(e.target).attr('id') != 'icon-bell' && jQuery(e.target).attr('id') != 'badge-important' && jQuery(e.target).attr('id') != 'new-options-button2'){
            if(eval(document.getElementById("button-options2"))){
                document.getElementById("button-options2").style.display = "none";
            }
        }
    })
    
    function showContent1(href){
        first = true;
        jQuery( '#myModal1 .modal-bodyc iframe').attr('src', href);
        screen_height = window.innerHeight;
        screen_width = window.innerWidth;
        
        document.getElementById('myModal1').style.height = (screen_height -110)+'px';
        document.getElementById('myModal1').style.width = '80%';
        document.getElementById('myModal1').style.left = '33%';
        
        document.getElementById('quiz_edit').style.height = (screen_height -150)+'px';
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

    function addNewQuestion(){
        if(jQuery("#list-sub-button").is(":visible")){
            jQuery("#list-sub-button").hide();
        }
        else{
            jQuery("#list-sub-button").show();
        }
    }
</script>

<?php
    $dateformat = $this->gurudateformat;
?>

<div id="myModal" class="modal hide" style="display:none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    </div>
    <div class="modal-body">
    </div>
</div>


<div id="g_myquizzesaddedit" class="clearfix com-cont-wrap">
    <?php echo $div_menu; //MENU TOP OF AUTHORS?>
    
    <div class="uk-grid uk-margin wk-grid wk-margin">
        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2">
            <h2 class="gru-page-title"> 
                <?php
                    if($program->id < 1){
                        echo JText::_('GURU_NEW_Q_CREATION');
                    }
                    else{
                        echo JText::_('GURU_EDIT_EXISTING_QUIZ');
                    }
                ?>
           </h2>
        </div>
        <div class="uk-width-1-2 uk-hidden-small uk-text-right wk-width-1-2 wk-hidden-small wk-text-right">
            <div class="uk-button-group wk-button-group">
                <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:save_quizFE('apply_quizFE');" />
                <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:save_quizFE('save_quizFE');" />
                <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes"); ?>';" />
            </div>
        </div>
        <div class="uk-width-1-1 uk-visible-small wk-width-1-1 wk-visible-small">
            <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:save_quizFE('apply_quizFE');" />
            <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="javascript:save_quizFE('save_quizFE');" />
            <input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=authorquizzes&layout=authorquizzes"); ?>';" />
        </div>
    </div>
     
    <form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="uk-form uk-form-horizontal wk-form wk-form-horizontal">
           
        <?php
            $sortquestion = JFactory::getApplication()->input->get("sortquestion", "");
            $active_pagination = JFactory::getApplication()->input->get("active_pagination", "0");
            
            $class1 = "uk-active";
            $class2 = "";
            $class3 = "";
            
            if($sortquestion !=""){
                $class1 = "";
                $class2 = "uk-active";
                $class3 = "";
            }
            elseif($active_pagination == "1"){
                $class1 = "";
                $class2 = "uk-active";
                $class3 = "";
            }
            else{
                $class1 = "uk-active";
                $class2 = "";
                $class3 = "";
                
				$session = JFactory::getSession();
				$registry = $session->get('registry');
				$added_questions_tab = $registry->get('added_questions_tab', "");
				
                if(isset($added_questions_tab) && $added_questions_tab == 1){
					$registry->set('added_questions_tab', "1");
                    $class1 = "";
                    $class2 = "uk-active";
                    $class3 = "";
                }
            }
            
        ?>
        
        <ul data-uk-tab="{connect:'#tab-content'}" class="uk-tab uk-padding-remove wk-tab wk-padding-remove">
            <li id="general-tab" onclick="javascript:changeTab('general-tab'); return false;" class="header-tab <?php echo $class1; ?>"><a href="#"><?php echo JText::_('GURU_GENERAL');?></a></a></li>
            <li id="questions-tab" onclick="javascript:changeTab('questions-tab'); return false;" class="header-tab <?php echo $class2; ?>"><a href="#"><?php echo JText::_('GURU_QUESTIONS');?></a></li>
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
                        <?php echo JText::_("GURU_MINIMUM_SCORE_QUIZ");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <span>
                           <?php
                                if (isset($program->max_score)){
                                    $program->max_score = $program->max_score;
                                }
                                else{
                                    $program->max_score = 70;
                                }
                            ?>
                            <input type="text" id="max_score_pass" name="max_score_pass" value="<?php echo $program->max_score;?>" class="input-mini pull-left" />&nbsp;
                            <select id="show_max_score_pass" name="show_max_score_pass"  class="input-small" >
                                <option value="0" <?php if($program->pbl_max_score == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                                <option value="1" <?php if($program->pbl_max_score == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_MAX_SCORE_TOOLTIP'); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_QUIZ_CAN_BE_TAKEN");?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select id="nb_quiz_taken" name="nb_quiz_taken" class="input-mini pull-left" >
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
                            <option value="11" <?php if($program->time_quiz_taken == "11"){echo 'selected="selected"'; }?>><?php echo JText::_("GURU_UNLIMPROMO");?></option>
                        </select>
                        <span class="pull-left" style="line-height: 30px; padding: 0 5px;">
                            <?php echo JText::_("GURU_TIMES_T"); ?>
                        </span>
                        
                        <select id="show_nb_quiz_taken" name="show_nb_quiz_taken" class="input-small" >
                            <option value="0" <?php if($program->show_nb_quiz_taken == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_SHOW"); ?></option>
                            <option value="1" <?php if($program->show_nb_quiz_taken == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("GURU_HIDE"); ?></option>
                        </select>&nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('GURU_TIMES_TAKEN_TOOLTIP'); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_SELECT_UP_TO");?>:
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <select id="nb_quiz_select_up" name="nb_quiz_select_up" class="pull-left" >
                            <?php
                            if(isset($amount_quest) && $amount_quest !=0 ){
							?>
                            	<option value="0"> <?php echo JText::_("GURU_SHOW_ALL_QUESTIONS"); ?> </option>
                            <?php
                                for($i=$amount_quest; $i>=1; $i--){
							?>
                                    <option value="<?php echo $i;?>" <?php if($program->nb_quiz_select_up == $i){echo 'selected="selected"'; }?> >
										<?php echo $i;?>
									</option>
                                <?php 
                                }
                            }
                            else{
                            ?>
                                <option value="text1"><?php echo "Please add questions first";?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <span class="pull-left" style="padding:0px 5px; line-height:30px;">
                            <?php echo JText::_('GURU_QUESTION_RANDOM'); ?>
                        </span>
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
                        <?php echo JText::_("GURU_QUIZ_LIMIT_TIME");?>:
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
                            if(isset($program->limit_time_f)){
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
            
            <li id="questions-tab-content" class="content-tab <?php echo $class2; ?> uk-margin-top wk-margin-top">
                <div class="inline-block" data-uk-dropdown="{mode:'click'}" aria-haspopup="true" aria-expanded="false">
                    <button class="uk-button uk-button-success wk-button wk-button-success inline-block" onclick="addNewQuestion(); return false;">
                        <?php echo JText::_('GURU_ADD_QUESTION'); ?> <i class="uk-icon-caret-down wk-icon-caret-down"></i>
                    </button>

                    <div id="list-sub-button" class="uk-dropdown uk-dropdown-small wk-dropdown wk-dropdown-small" style="">
                        <ul class="uk-nav uk-nav-dropdown wk-nav wk-nav-dropdown">
                            <li>
                                <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&cid=<?php echo $program->id;?>&type=true_false&new_add=1'); return false;">
                                    <?php echo JText::_("GURU_QUIZ_TRUE_FALSE"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&cid=<?php echo $program->id;?>&type=single&new_add=1'); return false;">
                                    <?php echo JText::_("GURU_QUIZ_SINGLE_CHOICE"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&cid=<?php echo $program->id;?>&type=multiple&new_add=1'); return false;">
                                    <?php echo JText::_("GURU_QUIZ_MULTIPLE_CHOICE"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&cid=<?php echo $program->id;?>&type=essay&new_add=1'); return false;">
                                    <?php echo JText::_("GURU_QUIZ_ESSAY"); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_QUIZ_ADD_QUESTION"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
                
                <table id="articleList" class="uk-table uk-table-striped wk-table wk-table-striped">
                    <tr>
                        <th width="1%">
                            <a href="#" onclick="changeSort();"><i class="icon-menu-2"></i></a>
                            <?php
                                $sortquestion = JFactory::getApplication()->input->get("sortquestion", "");
                            ?>
                            <input type="hidden" name="sortquestion" id="sortquestion" value="<?php echo $sortquestion; ?>" />
                        </th>
                        <th width="1%"></th>
                        <th width="42%">
                            <strong><?php echo JText::_('GURU_QUESTIONS');?></strong>
                        </th>
                        <th width="12%">
                            <strong><?php echo JText::_('GURU_TYPE');?></strong>
                        </th>
                        <th width="17%">
                            <strong><?php echo JText::_('GURU_REMOVE');?></strong>
                        </th>
                        <th width="17%">
                            <strong><?php echo JText::_('GURU_COPY');?></strong>
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
                    $i = 0;
                    
                    $questions_per_page = $program->questions_per_page;
                    
                    foreach ($mmediam as $mmedial) { 
                        $link2_remove = '<font class="uk-button uk-button-danger wk-button wk-button-danger"><span onClick="delete_q('.$mmedial->id.','.$program->id.',0)">'.JText::_('GURU_REMOVE').'</span></font>';
                        
                        if(intval($questions_per_page) != "0" && $i % $questions_per_page == 0 && $i != 0){
                            echo '<tr><td colspan="6" class="quiz-limit-page">'.JText::_("GURU_PAGE").' '.($i / $questions_per_page).'<hr style="border-top: 2px solid red;"></td></tr>';
                        }
                    ?>
                        
                        <tr class="row<?php echo $i%2;?>" id="trque<?php echo $mmedial->id; ?>" <?php if(in_array($mmedial->id,$hide_q2del)) { ?> style="display:none" <?php } ?>>
                            <td>
                                <span class="sortable-handler active" style="cursor: move;">
                                    <i class="icon-menu"></i>
                                </span>
                                <input type="text" class="width-20 text-area-order " value="<?php echo $mmedial->question_order; ?>" size="5" name="order[]" style="display:none;">
                            </td> 
                            <td width="1%" style="text-align:center; visibility:hidden;">>
                                <?php
                                    $checked = JHTML::_('grid.id', $i, $mmedial->id); echo $checked;
                                ?>
                            </td>
                            <td id="tdq<?php echo $mmedial->id?>" width="42%">
                                <a href="#" class="question-title" onclick="javascript:openMyModal('0', '0', '<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&is_from_modal=1&tmpl=component&no_html=1&cid=<?php echo $program->id.'&qid='.$mmedial->id;?>&type=<?php echo $mmedial->type;?>'); return false;">
                                    <?php
                                        if(strlen(strip_tags($mmedial->question_content)) > 55){
                                            echo mb_substr(str_replace("\'","&acute;" , strip_tags($mmedial->question_content)), 0, 55).'...';
                                        }
                                        else{
                                            echo str_replace("\'","&acute;", strip_tags($mmedial->question_content));
                                        }
                                    ?>
                                </a>
                            </td>
                            <td nowrap="nowrap">
                                <?php
                                    if($mmedial->type == "true_false"){
                                        echo JText::_("GURU_QUIZ_TRUE_FALSE");
                                    }
                                    elseif($mmedial->type == "single"){
                                        echo JText::_("GURU_QUIZ_SINGLE_CHOICE");
                                    }
                                    elseif($mmedial->type == "multiple"){
                                        echo JText::_("GURU_QUIZ_MULTIPLE_CHOICE");
                                    }
                                    elseif($mmedial->type == "essay"){
                                        echo JText::_("GURU_QUIZ_ESSAY");
                                    }
                                ?>
                            </td>
                            <td width="17%">
                                <?php echo $link2_remove; ?>
                            </td>
                            <td width="17%">
                                <a href="<?php echo JURI::root(); ?>/index.php?option=com_guru&controller=guruAuthor&task=copyquestion&tmpl=component&qid=<?php echo intval($program->id); ?>&question=<?php echo intval($mmedial->id); ?>" >
                                    <?php echo JText::_("GURU_COPY_QUESTION"); ?>
                                </a>
                            </td>
                            <td width="14%" id="publishing<?php echo $mmedial->id;?>">
                                <?php 
                                    if($mmedial->published == 1) {
                                        echo '<a class="icon-ok" style="cursor: pointer; text-decoration: none;" onclick="javascript:unpublish(\''.$mmedial->id.'\');"></a>';
                                    }
                                    else{
                                        echo '<a class="icon-remove" style="cursor: pointer; text-decoration: none;" onclick="javascript:publish(\''.$mmedial->id.'\');"></a>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php
                    $i++;
                    }//end foreach
                    ?>
                    <input type="hidden" value="<?php if (isset($data_post['newquizq'])) echo $data_post['newquizq'];?>" id="newquizq" name="newquizq" >
                    <input type="hidden" value="<?php if (isset($data_post['deleteq'])) echo $data_post['deleteq'];?>" id="deleteq" name="deleteq" >
                </table>
                
                <?php
                    $limit = $this->pagination->getLimitBox();
                    $limit = str_replace('onchange="', 'onchange="document.adminForm.active_pagination.value=1; ', $limit);
                    echo $limit;
                    $media = $this->media;
                    $app = JFactory::getApplication('site');
                    $limit = $app->getUserStateFromRequest( 'limit', 'limit', $app->getCfg('list_limit'), 'int');
                    $limitstart = JFactory::getApplication()->input->get("limitstart", "0");
                    $total = @$media->total;
                    
                    if($total <= $limit){
                        // no pagination
                    }
                    else{
                        $nr_pages = 0;
                        if(intval($limit) > 0){
                            $nr_pages = ceil($total / $limit);
                        }
                        
                        echo '<div class="pagination pagination-centered"><ul class="uk-pagination wk-pagination">';
                        for($i=1; $i<=$nr_pages; $i++){
                            $current_page = ($limitstart / $limit) + 1;
                            echo '<li>';
                            if($current_page == $i){
                                echo '<span class="pagenav">'.$i.'</span>';
                            }
                            else{
                                echo '<a href="#" onclick="document.adminForm.limitstart.value='.(($i-1) * $limit).'; document.adminForm.active_pagination.value=1; document.adminForm.submit(); return false;">'.$i.'</a>';
                            }
                            echo '</li>';
                        }
                        echo '</ul></div>';
                    }
                    
                    echo '<input type="hidden" name="limitstart" value="'.$limitstart.'" />';
                    echo '<input type="hidden" name="active_pagination" value="0" />';
                ?>
       
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
        
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>   
        <input type="hidden" name="id" value="<?php echo $program->id; ?>" />
        <input type="hidden" name="task" value="editQuizFE" />
        <input type="hidden" name="valueop" value="<?php echo $value_option; ?>"/>
        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="image" value="<?php if(isset($data_post['image'])){echo $data_post['image'];}else{echo $program->image;}?>" />
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="time_format" id="time_format" value="<?php echo @$format; ?>" />
        <?php
            $cid = JFactory::getApplication()->input->get("cid", "0");
            $v = JFactory::getApplication()->input->get("v", "");
            $e = JFactory::getApplication()->input->get("e", "");
        ?>
        <input type="hidden" name="cid" id="cid" value="<?php echo $cid; ?>" />
        <input type="hidden" name="v" id="v" value="<?php echo $v; ?>" />
        <input type="hidden" name="e" id="e" value="<?php echo $e; ?>" />
    </form>
</div>