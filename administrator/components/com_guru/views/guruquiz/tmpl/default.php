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
$doc =JFactory::getDocument();
//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
JHTML::_('behavior.modal');
$data_post = JFactory::getApplication()->input->post->getArray();
?>

<?php
$k = 0;
$n = count($this->ads);	
?>
</script>
    	<script language="javascript" type="text/javascript">
        Joomla.submitbutton = function(pressbutton){
            var form = document.adminForm;
            if (pressbutton=='duplicate') {
                if (form['boxchecked'].value == 0) {
                        alert( "<?php echo JText::_("GURU_Q_MAKESEL_JAVAMSG");?>" );
                } 
                else{
                    submitform( pressbutton );
                }
            }
            else {
                submitform( pressbutton );
            }
        }
    </script>
    <div id="editcell">
        <div id="myModal" class="modal-small modal hide">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
         <div class="container-fluid">
                  <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video" onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=30034914&tmpl=component')" class="pull-right guru_video" href="#">
                            <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                        <?php echo JText::_("GURU_QUIZ_VIDEO"); ?>                  
                  </a><br/>
                    <a data-toggle="modal" data-target="#myModal" class="pull-right guru_video"  onclick="showContentVideo('index.php?option=com_guru&controller=guruAbout&task=vimeo&id=52930939&tmpl=component')">
                            <img src="<?php echo JURI::base(); ?>components/com_guru/images/icon_video.gif" class="video_img" />
                        <?php echo JText::_("GURU_QUIZF_VIDEO"); ?>                  
                  </a>
            </div>	
            <div class="clearfix"></div>
            <div class="well well-minimized">
                <?php echo JText::_("GURU_QUIZ_SETTINGS_DESCRIPTION"); ?>
            </div>
        <form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
        	<table style="width:100%;" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
                <tr>
                    <td>
                    	<?php
                        	$session = JFactory::getSession();
							$registry = $session->get('registry');
							$search_quiz = $registry->get('search_quiz', "");
							$search_value = "";
							
							if(isset($data_post['search_quiz'])) {
                                $search_value = $data_post['search_quiz'];
                                $registry->set('search_quiz', $data_post['search_quiz']);
                            }
							elseif(isset($search_quiz) && trim($search_quiz) != ""){
                                $search_value = $search_quiz;
                            }
						?>
                    
                        <input type="text" name="search_quiz" value="<?php echo $search_value; ?>" />
                        <input class="btn btn-primary" type="submit" name="submit_search" value="<?php echo JText::_('GURU_SEARCHTXT');?>" />
                    </td>
                    
                    <td>
                        <?php echo JText::_('GURU_COURSE_PUBL');?>
                        <select onchange="document.adminForm.submit()" name="quiz_publ_status">
                        <?php 
							$session = JFactory::getSession();
							$registry = $session->get('registry');
							$quiz_publ_status = $registry->get('quiz_publ_status', "");
						
                            if(isset($quiz_publ_status) && trim($quiz_publ_status) != ""){
                                $pb = trim($quiz_publ_status);
                            }
                            
							if(isset($data_post['quiz_publ_status'])){
                                $pb = $data_post['quiz_publ_status'];
                            }
							
                            if(!isset($pb)) {$pb=NULL;}
                        ?>
                        <option <?php if($pb=='YN') { echo "selected='selected'";} ?> value="YN"><?php echo JText::_("GURU_SELECT"); ?></option>
                        <option <?php if($pb=='Y') { echo "selected='selected'";} ?> value="Y"><?php echo JText::_("GURU_PUBLISHED"); ?></option>
                        <option <?php if($pb=='N') { echo "selected='selected'";} ?> value="N"><?php echo JText::_("GURU_UNPUBLISHED"); ?></option>
                        </select>	
                    </td>	
                    <td>
                        <?php echo JText::_('GURU_SELECT_TYPE2');?>
                        <select onchange="document.adminForm.submit()" name="quiz_select_type">
                        <?php
							$session = JFactory::getSession();
							$registry = $session->get('registry');
							$quiz_select_type = $registry->get('quiz_select_type', "");
						 
                            if(isset($quiz_select_type) && trim($quiz_select_type) != ""){
                                $pb = $quiz_select_type;
                            }
							
                            if(isset($data_post['quiz_select_type'])){
                                $pb = $data_post['quiz_select_type'];
                            }
							
                            if(!isset($pb)){
								$pb=NULL;
							}
                        ?>
                        <option <?php if($pb=='0') { echo "selected='selected'";} ?> value="0"><?php echo JText::_("GURU_ANY"); ?></option>
                        <option <?php if($pb=='1') { echo "selected='selected'";} ?> value="1"><?php echo JText::_("GURU_QUIZZES_FILTER"); ?></option>
                        <option <?php if($pb=='2') { echo "selected='selected'";} ?> value="2"><?php echo JText::_("GURU_FQUIZZES_FILTER"); ?></option>
                        </select>	
                    </td>			
                </tr>
            </table>
            
            <table class="table table-striped table-bordered adminlist">
                <thead>
                    <tr>
                        <th width="5%">
                        	<input type="checkbox" onclick="Joomla.checkAll(this)" name="toggle" value="" />
                            <span class="lbl"></span>
                        </th>
                        <th width="5%"><?php echo JText::_('ID');?></th>
                        <th width="26%"><?php echo JText::_('GURU_QUIZ');?></th>
                        <th width="10%"><?php echo JText::_('GURU_TYPE');?></th>
                        <th width="15%"><?php echo JText::_('GURU_QUESTIONS');?></th>
                        <th width="15%"><?php echo JText::_('GURU_TREECUSTOMERS');?></th>
                        <th width="15%"><?php echo "Export" ;?></th>
                        <th width="8%"><?php echo JText::_('GURU_PUBLISHED');?></th>
                    </tr>
                </thead>                
                <tbody>
                <?php
                    for ($i = 0; $i < $n; $i++){
                        $ad = $this->ads[$i];
                        $id = $ad->id;
                        $checked = JHTML::_('grid.id', $i, $id);
                        /*$checked_array = guruAdminModelguruQuiz::checkbox_construct( $i, $id, $name='cid' );
                        $checked_array_expld = explode('$$$$$', $checked_array);
                        
                        $checked = "";
                        
                        if(isset($checked_array_expld["0"]) && trim($checked_array_expld["0"]) != ""){
                            $checked = $checked_array_expld["0"];
                        }*/		
                        $published = JHTML::_('grid.published', $ad, $i );
                        $link = "index.php?option=com_guru&controller=guruQuiz&task=edit&cid[]=".$id."&v=".$ad->is_final."&e=1";
                        
                        $howManyQuestions = guruAdminModelguruQuiz::QuestionNo($id);
						$howManyStudentsQuizz = guruAdminModelguruQuiz::StudentsQuizzNo($id);
						if($ad->is_final == 1){
							$type = JText::_('GURU_FINAL_EXAM_QUIZ1');
						}
						else{
							$type = JText::_('GURU_REGULAR_QUIZ');
						}

                ?>
                    <tr class="row<?php echo $k;?>"> 
                        <td align="center"><?php echo $checked;?><span class="lbl"></span></td>		
                        <td><?php echo $ad->id;?></td>		
                        <td nowrap><a class="a_guru" href="<?php echo $link;?>" ><?php echo $ad->name;?></a></td>	
                        <td><?php echo $type;?></td>	
                        <td><?php echo $howManyQuestions;?></td>
                        <td><a rel="{handler: 'iframe', size: {x: 700, y: 500}}" href="<?php echo JRoute::_("index.php?option=com_guru&controller=guruQuiz&task=listStudentsQuizTaken&id=".intval($id)."&tmpl=component"); ?>" class="modal modal-quiz-results"><?php echo $howManyStudentsQuizz ;?></a></td>
                        <td align="center"><span style="padding-right:10px;"><a target="_blank"  href="<?php echo JRoute::_("index.php?option=com_guru&controller=guruQuiz&task=exportpdf&id=".intval($id)); ?>"><img src="<?php echo JURI::base(); ?>components/com_guru/images/pdf.png"/></a></span><span><a target="_blank" href="<?php echo JRoute::_("index.php?option=com_guru&controller=guruQuiz&task=export&id=".intval($id)); ?>"><img src="<?php echo JURI::base(); ?>components/com_guru/images/excel.png"/></a></span></td>
                        <td><?php echo $published;?></td>		
                    </tr>
                    <input type="hidden" name="valueop" value="<?php echo $ad->is_final;?>" />
                <?php 
                        $k = 1 - $k;
                    }//end for
                ?>
                    <tr>
                        <td colspan="11">
                            <div class="pagination pagination-toolbar">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </div>
                            <div class="btn-group pull-left">
                                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                                <?php echo $this->pagination->getLimitBox(); ?>
                           </div>
                        </td>
            	</tr>
                </tbody>
            </table>
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="controller" value="guruQuiz" />
            <input type="hidden" name="id" value="<?php echo @$ad->id;?>">
            <input type="hidden" name="old_limit" value="<?php echo JFactory::getApplication()->input->get("limitstart"); ?>" />
        </form>
        
    </div>
    <script language="javascript">
	var first = false;
	function showContentVideo(href){
	first = true;
	jQuery.ajax({
      url: href,
      success: function(response){
       jQuery( '#myModal .modal-body').html(response);
      }
    });
}

	jQuery('#myModal').on('hide', function () {
	 jQuery('div.modal-body').html('');
	});
	jQuery('body').click(function () {
		if(!first){
			jQuery('#myModal .modal-body iframe').attr('src', '');
		}
		else{
			first = false;
		}
	});
</script>