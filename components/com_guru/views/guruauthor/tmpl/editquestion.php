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

JHtml::_('behavior.framework');
$medias = $this->medias;
$ckb_ans_array = explode('|||',$medias->answers);

$doc = JFactory::getDocument();
JHTML::_('behavior.modal');

$configuration = $this->getConfigsObject();

$temp_size = $configuration->lesson_window_size_back;
$temp_size_array = explode("x", $temp_size);
$width = $temp_size_array["1"]-20;
$height = $temp_size_array["0"]-20;	
$data_get = JFactory::getApplication()->input->get->getArray();
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<style>
	table.adminlist {
		background-color:#E7E7E7;
		border-spacing:1px;
		color:#666666;
		width:100%;
		font-family:Arial,Helvetica,sans-serif;
		font-size:11px;
	}
</style>
<script type="text/javascript" language="javascript">
	function editquestion (qid, id) {
		var completed = 0;
		for(i=1; i<=10; i++){
			if(document.getElementById('a'+i).value!=''){
				completed = i;
			}
		}
		var existing_answer = 0;	
		for(i=1; i<=completed; i++){
			if(document.getElementById(i+'a').checked == true){
				existing_answer = 1;
			}
		}		
		if (document.adminForm.text.value=='') {
			alert("Please enter the question text.");
			return false;
		} 
		else if (document.adminForm.a1.value=='') {
			alert("You must have at least one answer for your question");
			return false;
		} 
		else if (existing_answer == 0) {
			alert("Please check at least one answer as the correct answer");	
			return false;		
		} 
		else {
			document.adminForm.submit();
			window.parent.document.getElementById('tdq'+qid).innerHTML = '<a style="color:#666666!important; text-decoration:underline !important;" class="modal" rel="{handler: \'iframe\', size: {x: 770, y: 400}, iframeOptions: {id: \'g_teacher_editquestionss\'}}" href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=editquestion&is_from_modal=1&no_html=1&cid='+<?php echo $data_get['cid']; ?>+'&qid='+qid+'">'+document.getElementById('text').value+'</a>';
			//window.parent.document.getElementById("sbox-window").close();
			//window.parent.SqueezeBox.close();
			
			return true;
		}
	}
</script>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JUri::root() ?>index.php">
	<input type="hidden" name="page_width" value="0" />
	<input type="hidden" name="page_height" value="0" />
 	<script type="text/javascript">
		<?php 
			if($configuration->back_size_type == "1"){ 
				echo 'document.adminForm.page_width.value="'.$width.'";';
				echo 'document.adminForm.page_height.value="'.$height.'";';
			}
			
		?>
	</script>
    <input type="button" class="uk-button uk-button-success wk-button wk-button-success pull-right clearfix" value="<?php echo JText::_("GURU_SAVE_PROGRAM_BTN"); ?>" onclick="editquestion(<?php echo $data_get['qid'].','.$data_get['cid'];?>)">
    <div class="clearfix"></div>
    <div id="g_quize_q" class="g_table_wrap">
        <div class="g_table clearfix" id="g_media_list_table">  
            <div class="g_table_row">
                <div class="g_cell span6 g_table_cell g_th">
                    <div>
                        <div>
                            <?php echo JText::_('GURU_QUESTION'); ?>
                        </div>
                    </div>
                </div>
                <div class="g_cell span6 g_table_cell g_th">
                    <div>
                        <div>
                            <?php echo JText::_('GURU_ANSWER'); ?>
                            &nbsp;(<?php echo JText::_('GURU_CHECK_ANSWER'); ?>)
                        </div>
                    </div>
                </div>
			</div>
			
            <div class="g_table_row">
                <div class="g_cell span6 g_table_cell">
                    <div>
                        <div>
                            <textarea class="span12" rows="7" cols="40" name="text" id="text"><?php echo str_replace("\'", "&acute;",$medias->text);?></textarea>
                        </div>
                    </div>
                </div>
                <div class="g_cell span6 g_table_cell">
                    <div>
                        <div>
                            <div class="g_table_wrap">
        						<div class="g_table clearfix" id="g_media_list_table">
                                <?php
                                	for($i=1; $i<=10; $i++){
                                ?>
                                		<div class="g_table_row">
                                            <div class="g_cell span1 g_table_cell">
                                                <div>
                                                    <div>
                                                        <?php echo $i; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="g_cell span11 g_table_cell">
                                                <div>
                                                    <div>
                                                    	<?php
                                                        	$value = "";
															switch($i){
																case "1":{
																	$value = $medias->a1;
																	break;
																}
																case "2":{
																	$value = $medias->a2;
																	break;
																}
																case "3":{
																	$value = $medias->a3;
																	break;
																}
																case "4":{
																	$value = $medias->a4;
																	break;
																}
																case "5":{
																	$value = $medias->a5;
																	break;
																}
																case "6":{
																	$value = $medias->a6;
																	break;
																}
																case "7":{
																	$value = $medias->a7;
																	break;
																}
																case "8":{
																	$value = $medias->a8;
																	break;
																}
																case "9":{
																	$value = $medias->a9;
																	break;
																}
																case "10":{
																	$value = $medias->a10;
																	break;
																}
															}
														?>
                                                        <input type="text" class="pull-left answer" name="a<?php echo $i; ?>" id="a<?php echo $i; ?>" value="<?php echo str_replace("\'", "&acute;", $value);?>" size="32">
                                                        &nbsp;<input type="checkbox" name="<?php echo $i; ?>a" id="<?php echo $i; ?>a" <?php if (in_array($i.'a',$ckb_ans_array)) echo "checked";?>>
                                                        <span class="lbl"></span>
                                                    </div>
                                                </div>
                                            </div>
										</div>
								<?php
                                    }
								?>
                                </div>
							</div>
                        </div>
                    </div>
                </div>
			</div>
           	
    	</div>
	</div>
    
    <input type="hidden" value="com_guru" name="option"/>
    <input type="hidden" value="savequestion" name="task"/>
    <input type="hidden" value="<?php echo intval($data_get['cid']);?>" name="quizid"/>
    <input type="hidden" value="<?php echo intval($data_get['qid']);?>" name="qid"/>
    <input type="hidden" value="guruAuthor" name="controller"/>
    <input type="hidden" value="<?php echo $data_get['is_from_modal'] ?>" name="is_from_modal">
</form>