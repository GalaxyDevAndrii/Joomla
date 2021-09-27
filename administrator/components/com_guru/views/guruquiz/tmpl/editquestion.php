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

JHtml::_('behavior.framework');

define('_JEXEC',1);
defined( '_JEXEC' ) or die( 'Restricted access' );

$medias = $this->medias;
$ckb_ans_array = explode('|||',$medias->answers);

$doc =JFactory::getDocument();
//These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
JHTML::_('behavior.modal');

$configuration = guruAdminModelguruQuiz::getConfigs();

$temp_size = $configuration->lesson_window_size_back;
$temp_size_array = explode("x", $temp_size);
$width = $temp_size_array["1"]-20;
$height = $temp_size_array["0"]-20;	

$data_get = JFactory::getApplication()->input->get->getArray();

?>
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
			window.parent.document.getElementById('tdq'+qid).innerHTML = '<a style="color:#666666!important; text-decoration:underline !important;" class="openModal" rel="{handler: \'iframe\', size: {x: 770, y: 400}}" data-toggle="modal" data-target="#GuruModal"href="index.php?option=com_guru&controller=guruQuiz&task=editquestion&no_html=1&cid[]='+<?php echo $data_get['cid'][0]; ?>+'&qid='+qid+'">'+document.getElementById('text').value+'</a>';
			//if joomla <= 3.8 means that it will include modal.js script witch generate sbox-window with modal
            if(document.getElementById('sbox-window')){
                window.parent.SqueezeBox.close();
            }
            //if joomla > 3.8 means that it will not include modal.js anymore and will use boostrap modal
            else{
                window.parent.jQuery('#GuruModal').modal('toggle');
            }
			
			return true;
		}
	}
</script>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php">
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
     <table id="g_quize_q" width="100%">
		<tr>
			<td>
				<strong><?php echo JText::_('GURU_QUESTION'); ?></strong>
			</td>
			<td>
				<strong><?php echo JText::_('GURU_ANSWER'); ?></strong>
				&nbsp;(<?php echo JText::_('GURU_CHECK_ANSWER'); ?>)
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" style="border-bottom:1px solid #cccccc;">
				<textarea rows="7" cols="40" name="text" id="text"><?php echo str_replace("\'", "&acute;",$medias->text);?></textarea>
				<input type="button" class="btn" value="<?php echo JText::_("GURU_SAVE_PROGRAM_BTN"); ?>" onclick="editquestion(<?php echo $data_get['qid'].','.$data_get['cid'][0];?>)">
			</td>
			<td valign="top" style="border-left:1px solid #cccccc;border-bottom:1px solid #cccccc;">
				<table width="100%">
					<tr>
						<td>1</td>
						<td>
							<input type="text" name="a1" id="a1" value="<?php echo str_replace("\'", "&acute;",$medias->a1);?>" size="32">&nbsp;<input type="checkbox" name="1a" id="1a" <?php if (in_array('1a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>2</td>
						<td>
							<input type="text" name="a2" id="a2" value="<?php echo str_replace("\'", "&acute;",$medias->a2);?>" size="32">&nbsp;<input type="checkbox" name="2a" id="2a" <?php if (in_array('2a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>3</td>
						<td>
							<input type="text" name="a3" id="a3" value="<?php echo str_replace("\'", "&acute;",$medias->a3);?>" size="32">&nbsp;<input type="checkbox" name="3a" id="3a" <?php if (in_array('3a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>4</td>
						<td>
							<input type="text" name="a4" id="a4" value="<?php echo str_replace("\'", "&acute;",$medias->a4);?>" size="32">&nbsp;<input type="checkbox" name="4a" id="4a" <?php if (in_array('4a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>5</td>
						<td><input type="text" name="a5" id="a5" value="<?php echo str_replace("\'", "&acute;",$medias->a5);?>" size="32">&nbsp;<input type="checkbox" name="5a" id="5a" <?php if (in_array('5a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>6</td>
						<td>
							<input type="text" name="a6" id="a6" value="<?php echo str_replace("\'", "&acute;",$medias->a6);?>" size="32">&nbsp;<input type="checkbox" name="6a" id="6a" <?php if (in_array('6a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>7</td>
						<td>
							<input type="text" name="a7" id="a7" value="<?php echo str_replace("\'", "&acute;",$medias->a7);?>" size="32">&nbsp;<input type="checkbox" name="7a" id="7a" <?php if (in_array('7a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>8</td>
						<td>
							<input type="text" name="a8" id="a8" value="<?php echo str_replace("\'", "&acute;",$medias->a8);?>" size="32">&nbsp;<input type="checkbox" name="8a" id="8a" <?php if (in_array('8a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>9</td>
						<td>
							<input type="text" name="a9" id="a9" value="<?php echo str_replace("\'", "&acute;",$medias->a9);?>" size="32">&nbsp;<input type="checkbox" name="9a" id="9a" <?php if (in_array('9a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
					<tr>
						<td>10</td>
						<td>
							<input type="text" name="a10" id="a10" value="<?php echo str_replace("\'", "&acute;",$medias->a10);?>" size="32">&nbsp;<input type="checkbox" name="10a" id="10a" <?php if (in_array('10a',$ckb_ans_array)) echo "checked";?>><span class="lbl"></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
    <input type="hidden" value="com_guru" name="option"/>
    <input type="hidden" value="savequestionedit" name="task"/>
    <input type="hidden" value="<?php echo intval($data_get['cid'][0]);?>" name="quizid"/>
    <input type="hidden" value="<?php echo intval($data_get['qid']);?>" name="qid"/>
    <input type="hidden" value="guruQuiz" name="controller"/>
</form>
<?php
include(JPATH_SITE.'/administrator/components/com_guru/views/modals/modal_with_iframe.php');
?>
<script type="text/javascript" language="javascript" src="components/com_guru/js/modal_with_iframe.js"> </script>