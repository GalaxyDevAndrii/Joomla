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

defined( '_JEXEC' ) or die( 'Restricted access' );

$document = JFactory::getDocument();

include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'gurutask.php');
global $Itemid;

?>
<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>
<?php

	$guruModelguruTask = new guruModelguruTask();
	$db = JFactory::getDBO();
	$my = JFactory::getUser();
	$user_id = $my->id; 
	$course_id = intval(JFactory::getApplication()->input->get("course_id", ""));
	$chb_free_courses = $guruModelguruTask->getDataChbAccessCourses($course_id);
	$step_access_courses = $guruModelguruTask->getDataStepAccessCourses($course_id);

    if($chb_free_courses == 1){// free for
        $sql = "SELECT free_limit FROM `#__guru_program` where id = ".intval($course_id);
        $db->setQuery($sql);
        $db->execute();
        $result= $db->loadAssocList();

        $free_limit = $result["0"]["free_limit"];

        if(intval($free_limit) > 0){
            $sql = "select count(*) from #__guru_buy_courses bc, #__guru_order o where o.`status`='Paid' and o.`id`=bc.`order_id` and (bc.`expired_date` >= now() OR bc.`expired_date`='0000-00-00 00:00:00') and bc.`course_id`=".intval($course_id);
            $db->setQuery($sql);
            $db->execute();
            $count_orders = $db->loadColumn();
            $count_orders = @$count_orders["0"];

            if(intval($count_orders) >= intval($free_limit)){
                $chb_free_courses = 0;
            }
        }
    }

	$tmpl = JFactory::getApplication()->input->get("tmpl", "");
	if($tmpl == "component"){
		echo '<link rel="stylesheet" href="'.JURI::root()."components/com_guru/css/uikit.almost-flat.min.css".'"/>';
	}

	if($user_id == 0) { // if you are logout

		//desktop view

	?>

            <div class="g_padding_wrap clearfix">
                <div class="guru_modal_login_page page_title g_cell span7">
					<h2><?php echo JText::_('GURU_FREE_MEMBERS');?></h2>
                </div> 
            </div>  

            

        <div class="login_row_guru g_padding_wrap clearfix">
            <div id="g_member_login_box" class="login_cell_guru g_cell span6">
                <div>
                    <div class="no-padding">
                        <div>
                            <div id="g_login_title" class="g_login_title">
                                <h2> <?php echo JText::_("GURU_HAVE_ACCOUNT"); ?></h2>
                            </div>
                            <form name="login" method="post">
                                <div class="lo_cont_wraper">
                                    <div class="control-group g_row">
                                        <label class="pull-left control-label g_cell span4" for="username"><?php echo JText::_("GURU_PROFILE_USERNAME");?>: <span class="guru_error">*</span></label>
                                        <div class="controls g_cell span8">
                                            <input type="text" class="inputbox" size="15" id="username" name="username" placeholder="Username" />
                                        </div>
                                    </div>
                                    
                                    <div class="control-group g_row">
                                        <label class="pull-left control-label g_cell span4" for="passwd"><?php echo JText::_("GURU_PROFILE_PSW");?>: <span class="guru_error">*</span></label>
                                        <div class="controls g_cell span8">
                                        <input type="password" class="inputbox" size="15" id="passwd" name="passwd" placeholder="Password" />
                                        </div>
                                    </div>
                                    <div class="control-group g_row">
                                        <div class="g_cell span4 g_offset"></div>
                                        <div class="controls g_cell span8">
                                            <label class="checkbox span8">
                                                <input type="checkbox" name="rememeber" value="1" /> <?php echo JText::_("GURU_PROFILE_REMEMBER_ME");?>	
                                            </label>
                                        </div>
                                        <div class="g_cell span4 g_offset"></div>
                                        <div class="g_cell span8">
                                            <input type="submit" class="uk-button uk-button-success wk-button wk-button-success" name="submit" value="<?php echo JText::_("GURU_LOGIN_AND_CONTINUE"); ?>" />
                                        </div> 
                                    </div>
                                  </div>  
                           
                            	<input type="hidden" name="option" value="com_guru" />
                                <input type="hidden" name="controller" value="guruProfile" />
                                <input type="hidden" name="task" value="logCustomerIn" />
                                <input type="hidden" name="course_id" value="<?php echo intval(JFactory::getApplication()->input->get("course_id", "")); ?>" />
                                <input type="hidden" name="returnpage" value="<?php echo JFactory::getApplication()->input->get("returnpage", ""); ?>" />
                                <input type="hidden" name="graybox" value="<?php echo JFactory::getApplication()->input->get("graybox", ""); ?>" />
                            </form>
                       </div>
                    </div>
                </div>  
             </div><!--end member login box-->  
        
             <div id="g_not_mamber" class="login_cell_guru g_cell span6">
                <div>
                    <div class="no-padding">
                        <div>
                            <div id="g_registration_title" class="g_login_title">
                                <h2><?php echo JText::_("GURU_CREATE_NEW_ACCOUNT"); ?></h2>
                            </div>
                            <form name="register" method="post">
                                <input type="hidden" name="option" value="com_guru" />
                                <input type="hidden" name="controller" value="guruLogin" />
                                <input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->get("Itemid", "0", "raw"); ?>" />
                                <input type="hidden" name="task" value="register" />
                                <input type="hidden" name="returnpage" value="<?php echo JFactory::getApplication()->input->get("returnpage", ""); ?>" />	
                                <input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get("cid", "0"); ?>" />  
                                <div class="lo_cont_wraper">
                                    <span>
                                        <?php echo JText::_("GURU_REGISTRATION_EASY"); ?>
                                    </span>
                                </div>   
                                <div>
                                    <input type="submit" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_MYPROGRAMS_ACTION_CONTINUE");?>" />
                                </div>
                            </form>
                        </div>
                     </div>
                  </div>         
             </div><!--end not a member box-->
        </div><!--end login row-->
        <script type="text/javascript" src="components/com_guru/js/jquery.height_equal.js"></script>
        <script>

            window.onload = equalHeight('login_cell_guru');

        </script> 

	<?php
		JFactory::getApplication()->input->set("task", "logCustomerIn");
	}

	else{
		if($chb_free_courses == 1 && $step_access_courses == 1){
	?>
        	<div class="clearfix">
                <div class="guru_modal_login_page page_title g_cell span7">
                    <h2><?php echo JText::_('GURU_FREE_MEMBERS');?></h2>
                </div> 
           </div>  

           <div class="g_row">

		   		<div class="g_cell span12">        
					<?php
                        $course_id = JFactory::getApplication()->input->get("course_id", "");
                    ?>
            		<form name="enroll_stud" method="post" action="<?php echo JRoute::_("index.php?option=com_guru&view=guruprograms&task=enroll&action=enroll&cid=".intval($course_id)); ?>">
                		<input type="submit" class="uk-button uk-button-success wk-button wk-button-success"  value="<?php echo JText::_("GURU_ENROLL_NOW"); ?>" />
                        <input type="hidden" name="option" value="com_guru" />
                        <input type="hidden" name="controller" value="guruPrograms" />
                        <input type="hidden" name="cid" value="<?php echo intval(JFactory::getApplication()->input->get("course_id", "")); ?>" />
                        <input type="hidden" name="task" value="enroll" />
                        <input type="hidden" name="graybox" value="1" />
                    </form>	 

                </div>

          </div>

    	<?php 

		}
		elseif($chb_free_courses == 1){
?>

	<div class="clearfix">

                <div class="guru_modal_login_page page_title g_cell span12">

                    <h2><?php echo JText::_('GURU_ENROLL_STUDENTS_MODAL');?></h2>

                </div> 

                <div class="guru_modal_login_page page_title g_cell span7">

                    <h3><?php echo JText::_('GURU_ENROLL_FROM_MODAL');?></h3>

                </div> 

           </div>  

           <div class="g_row">

		   		<div class="g_cell span12">        

            		<form name="enroll_stud" method="post" action="<?php echo JUri::root(true) ?>/index.php?option=com_guru&view=guruPrograms">      
                		<input type="submit" class="uk-button uk-button-success wk-button wk-button-success"  value="<?php echo JText::_("GURU_ENROLL_NOW"); ?>" />
                        <input type="hidden" name="option" value="com_guru" />
                        <input type="hidden" name="controller" value="guruPrograms" />
                        <input type="hidden" name="cid" value="<?php echo intval(JFactory::getApplication()->input->get("course_id", "")); ?>" />
                        <input type="hidden" name="task" value="enroll" />
                        <input type="hidden" name="graybox" value="1" />
                    </form>	 

                </div>

          </div>

<?php

		JFactory::getApplication()->input->set("task", "enroll");

	  }

	

	}

	//end desktop view

?>