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
$data_get = JFactory::getApplication()->input->get->getArray();
$medias = $this->medias;
$medians = $this->medians;
$guruModelguruauthor =  new guruModelguruauthor();

$ins_id = $guruModelguruauthor->id_for_last_question();
$ins_id=$ins_id+1;
	
$question_type = JFactory::getApplication()->input->get("type","");
$find_true = "";
$find_false = "";
$image ="";

$media_attached_to_question = $guruModelguruauthor->getMediaFromQuestion(@$medias->media_ids);

$is_new = JFactory::getApplication()->input->get("new_add","0");
$array_elements = array();
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<style>
	.uk-grid{
		margin: 0px !important;
	}
	
	.component, .contentpane {
		background-color:#FFFFFF;
	}
</style>

<script type="text/javascript" language="javascript">
	function validateForm(type){
		if(type == 'true_false'){
			if(document.getElementById("g_question_text").value =='' || document.getElementById("g_question_text").value =='<p><br></p>'){
				alert("<?php echo JText::_("GURU_ALERT_NO_QUESTION_TEXT");?>");
				return false;
			}
			else if(document.adminForm.truefs_ans.value == ''){
				alert("<?php echo JText::_("GURU_SELECT_AN_ANSWER");?>");	
				return false;
			}	
			else{
				document.adminForm.submit();
			}
		
		}
		if(type == 'single' || type == 'multiple'){
			var checked=false;
			var array_ans = document.getElementsByName("correct_ans[]");
			var pass = "ok";	
			var selected = [];
			var number_of_options_with_text = 0;
			
			for(var i=0; i < array_ans.length; i++){
				if(array_ans[i].checked) {
					checked = true;
					selected.push(i);
				}
				
				answer_id = document.getElementById("answ"+i).value;
				
				if(document.getElementById("ans"+i).value != "" || document.getElementById("answer_media_"+answer_id+"_id").value != ""){
					number_of_options_with_text ++;
				}
			}
			
			if(number_of_options_with_text < 2){
				pass = "not_ok";
				alert("<?php echo JText::_("GURU_AT_LEAST_TWO_OPTIONS"); ?>");
				return false;
			}
			
			if(document.getElementById("g_question_text").value ==''|| document.getElementById("g_question_text").value =='<p><br></p>'){
				pass = "not_ok";
				alert("<?php echo JText::_("GURU_ALERT_NO_QUESTION_TEXT");?>");
				return false;
			}
			
			if(!checked){
				pass = "not_ok";
				alert("<?php echo JText::_("GURU_SELECT_AN_ANSWER");?>");	
				return false;
			}			
			
			if(document.getElementById("ans0").value == '' && document.getElementById("ans_media_0").innerHTML == ''){
				pass = "not_ok";
				alert("<?php echo JText::_("GURU_AT_LEAST_ONE_ANSWER");?>");
				return false;
			}
			
			for(i=0; i<selected.length; i++){
				text = document.getElementById("ans"+selected[i]).value;
				media = document.getElementById("ans_media_"+selected[i]).innerHTML;
				
				if(text == "" && media.trim() == ""){
					pass = "not_ok";
					alert("<?php echo addslashes(JText::_("GURU_ADD_TEXT_FOR_CORRECT_ANS")); ?>");
					return false;
				}
			}
			
			if(pass == "ok"){
				document.adminForm.submit();
			}
				 
		}
		else if(type == 'essay'){
			if(document.getElementById("g_question_text").value =='' || document.getElementById("g_question_text").value =='<p><br></p>'){
				alert("<?php echo JText::_("GURU_ALERT_NO_QUESTION_TEXT");?>");
				return false;
			}
			else{
				document.adminForm.submit();
			}
		}	
	}
	
	function addquestion(qid, idu){
		var completed = 0;
		var qqqqid=<?php echo intval(JFactory::getApplication()->input->get('cid')); ?>;
		var type = '<?php echo JFactory::getApplication()->input->get('type'); ?>';
		var myrow = parent.document.createElement('TR');
		myrow.id = 'trque'+idu;
		parent.document.getElementById('newquizq').value = parent.document.getElementById('newquizq').value+','+idu;
		//parent.document.getElementById('rowquestion').appendChild(myrow);
		var mycell0 = top.document.createElement('TD');
		myrow.appendChild(mycell0);
		var mycell9 = top.document.createElement('TD');
		myrow.appendChild(mycell9);
		var mycellsix = top.document.createElement('TD');
		myrow.appendChild(mycellsix);
		var mycell = top.document.createElement('TD');
		myrow.appendChild(mycell);
		var mycellthree = top.document.createElement('TD');
		myrow.appendChild(mycellthree);
		var mycellfour = top.document.createElement('TD');
		myrow.appendChild(mycellfour);
		var mycellfive = top.document.createElement('TD');
		myrow.appendChild(mycellfive);
		var span ='<span class="sortable-handler active" style="cursor: move;"><i class="icon-menu"></i></span>';
		
		var cb = '<input style="visibility:hidden" type="checkbox" onclick="isChecked(this.checked);" value="'+idu+'" name="cid[]" id="cb'+(i)+'">';
		var value = "cb"+i;
		
		mycell0.innerHTML = span;
		mycellsix.innerHTML = cb;
		mycell.innerHTML=document.adminForm.question_text.value;
		mycell9.innerHTML='<font color="#FF0000"><span onClick="delete_q('+idu+')">Remove</span></font>';
		mycellthree.innerHTML='<font color="#FF0000"><span onClick="delete_q('+idu+')">Remove</span></font>';
		
		mycellfour.innerHTML='<a style="color:#666666 !important;" class="modal" rel="{handler: \'iframe\', size: {x: 770, y: 400}}" href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=addquestion&tmpl=component&cid='+qqqqid+'&qid='+idu+'&type='+type+'"><?php echo JText::_('GURU_EDIT');?></a>';
		
		mycellfive.innerHTML='Published';
		document.adminForm.submit();
		setTimeout('document.getElementById("newquizq").click()',1000);
		return true;
	}
	
	function deleteQuestionMedia(id){
		var myDiv = document.getElementById("guru_media"+id);
		myDiv.parentNode.removeChild(myDiv);
		
		//change button text
		document.getElementById('media_button_for_question').innerHTML = "<?php echo JText::_("GURU_ATTACH_MEDIA"); ?>";
	}
	
	function deleteAnswerMedia(id, radio_value){
		var myDiv = document.getElementById("ans_media_"+radio_value+"_"+id);
		myDiv.parentNode.removeChild(myDiv);
		
		//change button text
		document.getElementById('button_media_answers_'+radio_value).innerHTML = "<?php echo JText::_("GURU_ATTACH_MEDIA"); ?>";
	}
	
	function addMoreAnswersSingle(){
		answers = document.getElementsByName("correct_ans[]");
		current = 0;
		
		for(var i=0; i<answers.length; i++){
			if(answers[i].value > current){
				current = answers[i].value;
			}
		}
		
		current++;
		
		content = '<input type="radio" class="uk-float-left wk-float-left" name="correct_ans[]" id="answ'+current+'" value="'+current+'"><span class="lbl"></span>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="uk-float-left uk-margin-left wk-float-left wk-margin-left" value="" id="ans'+current+'" name="ans_content['+current+'][]">  &nbsp;&nbsp; <input type="button" class="btn btn-danger" value=" X " onclick="javascript:deleteAnswer('+current+'); return false;" /> &nbsp;&nbsp;  <h5><?php echo JText::_("GURU_ATTACH_MEDIA"); ?></h5><input type="text" autocomplete="off" onkeyup="javascript:searchMediaAnswers(this.value, '+current+'); return false;" style="margin-top:20px; width:97%;" placeholder="<?php echo JText::_("GURU_ENTER_MEDIA_NAME"); ?>" value="" id="answer_media_'+current+'"><input type="hidden" value="" name="ans_media_ids['+current+'][]" id="answer_media_'+current+'_id"><div style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;" id="question_media_list_'+current+'">&nbsp;</div></div>';
		
		var myDiv = document.createElement('div');
		myDiv.className = "clearfix";
		myDiv.innerHTML = content;
		document.getElementById("div-options").appendChild(myDiv);
		
		return false;
	}
	
	function addMoreAnswersMultiple(){
		answers = document.getElementsByName("correct_ans[]");
		current = 0;
		
		for(var i=0; i<answers.length; i++){
			if(answers[i].value > current){
				current = answers[i].value;
			}
		}
		
		content = '<input type="checkbox" class="uk-float-left wk-float-left" name="correct_ans[]" id="answ'+(parseInt(current)+1)+'" value="'+(parseInt(current)+1)+'"><span class="lbl"></span>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" class="uk-float-left uk-margin-left wk-float-left wk-margin-left" value="" id="ans'+(parseInt(current)+1)+'" name="ans_content['+(parseInt(current)+1)+'][]"> &nbsp;&nbsp; <input type="button" class="btn btn-danger" value=" X " onclick="javascript:deleteAnswer('+(parseInt(current)+1)+'); return false;" /> &nbsp;&nbsp; <h5><?php echo JText::_("GURU_ATTACH_MEDIA"); ?></h5><input type="text" autocomplete="off" onkeyup="javascript:searchMediaAnswers(this.value, '+(parseInt(current)+1)+'); return false;" style="margin-top:20px; width:97%;" placeholder="<?php echo JText::_("GURU_ENTER_MEDIA_NAME"); ?>" value="" id="answer_media_'+(parseInt(current)+1)+'"><input type="hidden" value="" name="ans_media_ids['+(parseInt(current)+1)+'][]" id="answer_media_'+(parseInt(current)+1)+'_id"><div style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;" id="question_media_list_'+(parseInt(current)+1)+'">&nbsp;</div></div>';
		
		var myDiv = document.createElement('div');
		myDiv.className = "clearfix";
		myDiv.innerHTML = content;
		document.getElementById("div-options").appendChild(myDiv);
		
		return false;
	}
	
	function accessMediaIframe(element){
		if(document.getElementById(element).style.display == "block"){
			document.getElementById(element).style.display = "none";
		}
		else{
			document.getElementById(element).style.display = "block";
		}
		return false;
	}
	
	function searchMedia(value){
		if(value == ""){
			document.getElementById("question_media_list").style.display = "none";
			document.getElementById("question_media_id").value = "";
		}
		else{
			document.getElementById("question_media_list").style.display = "block";
		}
	
		/*if(typeof ajaxSearchReq != "undefined"){
			ajaxSearchReq.cancel();
		}*/
		
		jQuery.ajax({
			async: true,
			method: 'get',
			url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=ajaxSearchMedia&text='+encodeURI(value)+"&tmpl=component&format=raw",
			data: { 'do' : '1' },
			//async: false,
			success: function(response){
				//console.log(response);
				jQuery("#question_media_list").empty().append(response);
			}
		});
	}
	
	function selectMediaFromList(id, name){
		document.getElementById("question_media").value = name;
		document.getElementById("question_media_id").value = id;
		document.getElementById("question_media_list").style.display = "none";
	}
	
	function searchMediaAnswers(value, id){
		if(value == ""){
			document.getElementById("question_media_list_"+id).style.display = "none";
			document.getElementById("answer_media_"+id+"_id").value = "";
		}
		else{
			document.getElementById("question_media_list_"+id).style.display = "block";
		}
	
		/*if(typeof ajaxSearchReq != "undefined"){
			ajaxSearchReq.cancel();
		}*/
		
		jQuery.ajax({
			async: true,
			method: 'get',
			url: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=ajaxSearchMedia&text='+encodeURI(value)+"&answer_id="+id+"&tmpl=component&format=raw",
			data: { 'do' : '1' },
			//async: false,
			success: function(response){
				jQuery("#question_media_list_"+id).empty().append(response);
			}
		});
	}
	
	function selectMediaFromListForAnswers(id, name, answer_id){
		document.getElementById("answer_media_"+answer_id).value = name;
		document.getElementById("answer_media_"+answer_id+"_id").value = id;
		document.getElementById("question_media_list_"+answer_id).style.display = "none";
	}
	
	function deleteAnswer(key){
		parentNode = document.getElementById("answ"+key).parentNode;
		parentNode.style.height = "0px";
		parentNode.style.margin = "0px";
		parentNode.style.visibility = "hidden";
		
		document.getElementById("ans"+key).value = "";
		element_name = document.getElementById("ans"+key).getAttribute("name");
		element_name = element_name.replace("ans_content[", "");
		element_name = element_name.replace("][]", "");
		
		document.getElementById("answer_media_"+element_name).value = "";
		document.getElementById("answer_media_"+element_name+"_id").value = "";
	}
</script>

<form method="post" name="adminForm" id="adminForm" action="<?php echo JUri::root() ?>index.php" onsubmit="validateForm('<?php echo $question_type;?>'); return false;" enctype="multipart/form-data">

	<div class="uk-grid wk-grid">
        <div class="uk-width-1-1 uk-text-right wk-width-1-1 wk-text-right">
        	<button class="uk-button uk-button-success wk-button wk-button-success" onclick="addquestion(<?php echo $data_get['cid'].','.$ins_id;?>);"><?php echo JText::_("GURU_SV_AND_CL"); ?></button>
        	<button class="uk-button uk-button-success wk-button wk-button-success" onclick="document.adminForm.task.value='savequestionandkeep';"><?php echo JText::_("GURU_SAVE"); ?></button>
        </div>
    </div>
    
    <?php
		if($question_type == 'true_false'){ // start true/false choice question type
			echo '<div class="uk-grid wk-grid"><div class="uk-width-1-1 wk-width-1-1"><h2 class="gru-page-title">'.JText::_("GURU_QUIZ_TRUE_FALSE")." ".JText::_("GURU_QUESTION").'</h2></div></div>';
	?>
    	<div class="uk-grid wk-grid">
    		<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-2-3 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-2-3">
        		<?php
                	echo '<b>'.JText::_("GURU_THE")." ".JText::_("GURU_QUESTION").'</b>';
				?>
                <div>
                    <textarea id="g_question_text" name="question_text" class="useredactor" style="width:80%; height:200px;"><?php echo html_entity_decode(@$medias->question_content);?></textarea>
                     <link rel="stylesheet" href="<?php echo JURI::root()."administrator/components/com_guru/css/redactor.css"; ?>" />
                    <!-- <script src="<?php echo JURI::root()."administrator/components/com_guru/js/redactor.min.js"; ?>"></script> -->
                </div> 
                
                <h3><?php echo JText::_("GURU_ATTACH_MEDIA"); ?></h3>
            
				<?php
                    $media_name = "";
                    $media_id = "";
                    
                    if(isset($media_attached_to_question) && count($media_attached_to_question) > 0){
                        foreach($media_attached_to_question as $media_key=>$media_value){
                            $media_name = $media_value["name"];
                            $media_id = $media_value["id"];
                        }
                    }
                ?>
                
                <input type="text" id="question_media" name="question_media" value="<?php echo $media_name; ?>" placeholder="<?php echo JText::_("GURU_ENTER_MEDIA_NAME"); ?>" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMedia(this.value); return false;" autocomplete="off" />
                <input type="hidden" id="question_media_id" name="question_media_ids[]" value="<?php echo $media_id; ?>" />
                <div id="question_media_list" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
                    &nbsp;
                </div>
                
			</div>
        
        	<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-3 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-3">
                <div style="margin-bottom:50px;">
                    <?php echo '<b>'.JText::_("GURU_PICK_CORRECT_ANS").'</b>';?>
                    <br/><br/><br/>
                    <?php
                        $id_question_req = JFactory::getApplication()->input->get("qid", "0");
                        $find_true = 2;
                        $find_false = 2;
                        if(intval($id_question_req) >0){
                            if($medians["0"]->correct_answer == 1){
                                $find_true = 0;
                                $find_false = 1;
                            }
                            else{
                                if($medians["0"]->correct_answer != -1){
                                    $find_true = 1;
                                    $find_false = 0;
                                }
                            }
                        }
                    ?>
                    <div>
                        <div class="pull-left">
                            <input type="radio" name="truefs_ans" value="0" <?php if(@$medians["0"]->correct_answer == 1){echo 'checked="checked"';}?> />
                            <span class="lbl"></span>
                        </div>
                        <div class="pull-left">
                            &nbsp;<?php echo JText::_("GURU_QUESTION_OPTION_TRUE"); ?>&nbsp;&nbsp;
                        </div>
                        <div class="pull-left">
                            <input type="radio" name="truefs_ans" value="1" <?php if(@$medians["1"]->correct_answer == 1){echo 'checked="checked"';} ?> />
                            &nbsp;
                            <span class="lbl"></span>
                        </div>
                        <div class="pull-left">
                            &nbsp;<?php echo JText::_("GURU_QUESTION_OPTION_FALSE"); ?>&nbsp;&nbsp;
                        </div>
                    </div>   
                </div>
            	<div class="clearfix"></div>
                <div>
                    <?php echo '<b>'.JText::_("GURU_OPTIONS").'</b>';?>
                    <br/><br/><br/>
                    <div>
                        <?php echo JText::_("GURU_QUESTION_WEIGHT")?>
                        <select name="question_weight_tf" id="question_weight_tf">
                            <?php
                            for($i=1; $i<=10; $i++){
                            ?>
                                <option value="<?php echo $i;?>" <?php if(@$medias->points == $i){echo 'selected="selected"';} ?> ><?php echo $i;?></option>
                            <?php    
                            }
                            ?>
                        </select>
                    </div>
    
                </div>
        	</div>
    <?php
		}
		else if($question_type == 'single'){ // start single choice question type
			echo '<div class="uk-grid wk-grid"><div class="uk-width-1-1 wk-width-1-1"><h2 class="gru-page-title">'.JText::_("GURU_QUIZ_SINGLE_CHOICE")." ".JText::_("GURU_QUESTION").'</h2></div></div>';
		?>
        	<div class="uk-grid wk-grid">
				<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-2 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-2">
					<?php echo '<b>'.JText::_("GURU_THE")." ".JText::_("GURU_QUESTION").'</b>';
                    ?>
                    <div>
                        <textarea id="g_question_text" name="question_text" class="useredactor" style="width:80%; height:200px;"><?php echo html_entity_decode(@$medias->question_content);?></textarea>
                        <link rel="stylesheet" href="<?php echo JURI::root()."administrator/components/com_guru/css/redactor.css"; ?>" />
                        <!-- <script src="<?php echo JURI::root()."administrator/components/com_guru/js/redactor.min.js"; ?>"></script> -->
                    </div> 
                    
                    <h3><?php echo JText::_("GURU_ATTACH_MEDIA"); ?></h3>
            
					<?php
						$media_name = "";
						$media_id = "";
						
						if(isset($media_attached_to_question) && count($media_attached_to_question) > 0){
							foreach($media_attached_to_question as $media_key=>$media_value){
								$media_name = $media_value["name"];
								$media_id = $media_value["id"];
							}
						}
					?>
					
					<input type="text" id="question_media" name="question_media" value="<?php echo $media_name; ?>" placeholder="<?php echo JText::_("GURU_ENTER_MEDIA_NAME"); ?>" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMedia(this.value); return false;" autocomplete="off" />
					<input type="hidden" id="question_media_id" name="question_media_ids[]" value="<?php echo $media_id; ?>" />
					<div id="question_media_list" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
						&nbsp;
					</div>
                    
				</div>
				
                <div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-2 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-2">
                    <div id="div-options">
                        <div class="clearfix">
                            <?php echo '<b>'.JText::_("GURU_PICK_CORRECT_ANS").'</b>';?>
                        </div>
                        
                        <?php
                            if(isset($is_new) && $is_new == 1){
								$button_label = "";
								
								$attach_media1 = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
								$attach_media1 .= '<input type="text" id="answer_media_0" value="" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, 0); return false;" autocomplete="off" />
								<input type="hidden" id="answer_media_0_id" name="ans_media_ids[0][]" value="" />
								<div id="question_media_list_0" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
									&nbsp;
								</div>';
								
								$attach_media2 = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
								$attach_media2 .= '<input type="text" id="answer_media_1" value="" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, 1); return false;" autocomplete="off" />
								<input type="hidden" id="answer_media_1_id" name="ans_media_ids[1][]" value="" />
								<div id="question_media_list_1" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
									&nbsp;
								</div>';
								
								$attach_media3 = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
								$attach_media3 .= '<input type="text" id="answer_media_2" value="" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, 2); return false;" autocomplete="off" />
								<input type="hidden" id="answer_media_2_id" name="ans_media_ids[2][]" value="" />
								<div id="question_media_list_2" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
									&nbsp;
								</div>';
								
								$array_elements = array( array("id"=>"0", "default"=>"0", "answer_content"=>"", "media"=>array("0"=>$attach_media1), "label"=>$button_label), array("id"=>"0", "default"=>"0", "answer_content"=>"", "media"=>array("0"=>$attach_media2), "label"=>$button_label),array("id"=>"0", "default"=>"0", "answer_content"=>"", "media"=>array("0"=>$attach_media3), "label"=>$button_label));
							}
                            else{
                                $button_label = "";
								
								foreach($medians as $key=>$value){
                                    $media_attached_to_answers = $guruModelguruauthor->getMediaFromAnswer($value->media_ids);
                                    $temp = array();
                                    
                                    if(count($media_attached_to_answers) > 0){
										foreach($media_attached_to_answers as $key_media=>$value_media){
											$attach_media = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
									
											$media_name = $value_media["name"];
											$media_id = $value_media["id"];
											
											$attach_media .= '<input type="text" id="answer_media_'.intval($value->id).'" value="'.$media_name.'" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, '.intval($value->id).'); return false;" autocomplete="off" />
											<input type="hidden" id="answer_media_'.intval($value->id).'_id" name="ans_media_ids['.$value->id.'][]" value="'.$media_id.'" />
											<div id="question_media_list_'.intval($value->id).'" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
												&nbsp;
											</div>';
											
											$temp[] = $attach_media;
										}
									}
									else{
										$attach_media = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
									
										$media_name = "";
										$media_id = "";
										
										$attach_media .= '<input type="text" id="answer_media_'.intval($value->id).'" value="'.$media_name.'" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, '.intval($value->id).'); return false;" autocomplete="off" />
										<input type="hidden" id="answer_media_'.intval($value->id).'_id" name="ans_media_ids['.$value->id.'][]" value="'.$media_id.'" />
										<div id="question_media_list_'.intval($value->id).'" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
											&nbsp;
										</div>';
										
										$temp[] = $attach_media;
									}
                                
                                    $array_elements[] = array("id"=>$value->id, "default"=>$value->correct_answer, "answer_content"=>$value->answer_content_text, "media"=>$temp, "label"=>$button_label);
                                }
                            }
                            
                            $content_to_display = "";
                            
							foreach($array_elements as $key=>$value){
                                $checked = '';
                                if($value["default"] == "1"){
                                    $checked = 'checked="checked"';
                                }
                                
                                $radio_value = $value["id"];
                                if(isset($is_new) && $is_new == 1){
                                    $radio_value = $key;
                                }
                                
                                $content_to_display .= '<div class="clearfix" style="margin-bottom:25px;">
                                                            <input type="radio" class="uk-float-left wk-float-left" value="'.$radio_value.'" id="answ'.$key.'" name="correct_ans[]" '.$checked.' /><span class="lbl"></span>&nbsp; &nbsp;
                                                            <input type="text" class="uk-float-left uk-margin-left wk-float-left wk-margin-left" name="ans_content['.$radio_value.'][]" id="ans'.$key.'" value="'.$value["answer_content"].'" /> &nbsp;&nbsp; <input type="button" class="uk-button uk-button-danger wk-button wk-button-danger" value=" X " onclick="javascript:deleteAnswer('.intval($key).'); return false;" /> &nbsp;&nbsp;
                                                            <div id="ans_media_'.$radio_value.'" class="clearfix">
																'.implode(" ", $value["media"]).'
															</div>
                                                        </div>';
                            }
							
                            echo $content_to_display;
                        ?>
                    </div>
                
					<input type="button" onclick="javascript:addMoreAnswersSingle();" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_ADD_MORE"); ?>" style="margin-top:10px;" />
                
                	<div class="clearfix"></div>
                	<br/>
                	<?php echo '<b>'.JText::_("GURU_OPTIONS").'</b>';?>
                	<div class="clearfix"></div>
                    <div>
                        <?php echo JText::_("GURU_QUESTION_WEIGHT")?>
                        <select name="question_weight_tf" id="question_weight_tf">
                            <?php
                            for($i=1; $i<=10; $i++){
                            ?>
                                <option value="<?php echo $i;?>" <?php if(@$medias->points == $i){echo 'selected="selected"';} ?> ><?php echo $i;?></option>
                            <?php    
                            }
                            ?>
                        </select>
                    </div>
				</div>
			</div>
		<?php
		}
		else if($question_type == 'multiple'){ // start mutiple choice question type
			echo '<div class="uk-grid wk-grid"><div class="uk-width-1-1 wk-width-1-1"><h2 class="gru-page-title">'.JText::_("GURU_QUIZ_MULTIPLE_CHOICE")." ".JText::_("GURU_QUESTION").'</h2></div></div>';
		?>
			<div class="uk-grid wk-grid">
				<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-2 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-2">
					<?php echo '<b>'.JText::_("GURU_THE")." ".JText::_("GURU_QUESTION").'</b>';
                    ?>
                    <div>
                        <textarea id="g_question_text" name="question_text" class="useredactor" style="width:80%; height:200px;"><?php echo html_entity_decode(@$medias->question_content);?></textarea>
                        <link rel="stylesheet" href="<?php echo JURI::root()."administrator/components/com_guru/css/redactor.css"; ?>" />
                        <!-- <script src="<?php //echo JURI::root()."administrator/components/com_guru/js/redactor.min.js"; ?>"></script> -->
                    </div> 
                    
                    <h3><?php echo JText::_("GURU_ATTACH_MEDIA"); ?></h3>
            
					<?php
                        $media_name = "";
                        $media_id = "";
                        
                        if(isset($media_attached_to_question) && count($media_attached_to_question) > 0){
                            foreach($media_attached_to_question as $media_key=>$media_value){
                                $media_name = $media_value["name"];
                                $media_id = $media_value["id"];
                            }
                        }
                    ?>
                    
                    <input type="text" id="question_media" name="question_media" value="<?php echo $media_name; ?>" placeholder="<?php echo JText::_("GURU_ENTER_MEDIA_NAME"); ?>" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMedia(this.value); return false;" autocomplete="off" />
                    <input type="hidden" id="question_media_id" name="question_media_ids[]" value="<?php echo $media_id; ?>" />
                    <div id="question_media_list" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
                        &nbsp;
                    </div>
                    
				</div>
                
				<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-2 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-2">
                    <div id="div-options">
                        <div class="clearfix">
                            <?php echo '<b>'.JText::_("GURU_PICK_CORRECT_ANS").'</b>';?>
                        </div>
                        
                        <?php
                            if(isset($is_new) && $is_new == 1){
								$button_label = "";
								
								$attach_media1 = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
								$attach_media1 .= '<input type="text" id="answer_media_0" value="" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, 0); return false;" autocomplete="off" />
								<input type="hidden" id="answer_media_0_id" name="ans_media_ids[0][]" value="" />
								<div id="question_media_list_0" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
									&nbsp;
								</div>';
								
								$attach_media2 = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
								$attach_media2 .= '<input type="text" id="answer_media_1" value="" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, 1); return false;" autocomplete="off" />
								<input type="hidden" id="answer_media_1_id" name="ans_media_ids[1][]" value="" />
								<div id="question_media_list_1" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
									&nbsp;
								</div>';
								
								$attach_media3 = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
								$attach_media3 .= '<input type="text" id="answer_media_2" value="" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, 2); return false;" autocomplete="off" />
								<input type="hidden" id="answer_media_2_id" name="ans_media_ids[2][]" value="" />
								<div id="question_media_list_2" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
									&nbsp;
								</div>';
								
								$array_elements = array( array("id"=>"0", "default"=>"0", "answer_content"=>"", "media"=>array("0"=>$attach_media1), "label"=>$button_label), array("id"=>"0", "default"=>"0", "answer_content"=>"", "media"=>array("0"=>$attach_media2), "label"=>$button_label),array("id"=>"0", "default"=>"0", "answer_content"=>"", "media"=>array("0"=>$attach_media3), "label"=>$button_label));
							}
                            else{
                                $button_label = "";
								
								foreach($medians as $key=>$value){
                                    $button_label = JText::_("GURU_ATTACH_MEDIA");
                                    $media_attached_to_answers = $guruModelguruauthor->getMediaFromAnswer($value->media_ids);
                                    $temp = array();
                                    
                                    if(count($media_attached_to_answers) > 0){
										foreach($media_attached_to_answers as $key_media=>$value_media){
											$attach_media = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
									
											$media_name = $value_media["name"];
											$media_id = $value_media["id"];
											
											$attach_media .= '<input type="text" id="answer_media_'.intval($value->id).'" value="'.$media_name.'" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, '.intval($value->id).'); return false;" autocomplete="off" />
											<input type="hidden" id="answer_media_'.intval($value->id).'_id" name="ans_media_ids['.$value->id.'][]" value="'.$media_id.'" />
											<div id="question_media_list_'.intval($value->id).'" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
												&nbsp;
											</div>';
											
											$temp[] = $attach_media;
										}
									}
									else{
										$attach_media = '<h5>'.JText::_("GURU_ATTACH_MEDIA").'</h5>';
									
										$media_name = "";
										$media_id = "";
										
										$attach_media .= '<input type="text" id="answer_media_'.intval($value->id).'" value="'.$media_name.'" placeholder="'.JText::_("GURU_ENTER_MEDIA_NAME").'" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMediaAnswers(this.value, '.intval($value->id).'); return false;" autocomplete="off" />
										<input type="hidden" id="answer_media_'.intval($value->id).'_id" name="ans_media_ids['.$value->id.'][]" value="'.$media_id.'" />
										<div id="question_media_list_'.intval($value->id).'" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
											&nbsp;
										</div>';
										
										$temp[] = $attach_media;
									}
                                
                                    $array_elements[] = array("id"=>$value->id, "default"=>$value->correct_answer, "answer_content"=>$value->answer_content_text, "media"=>$temp, "label"=>$button_label);
                                }
                            }
                            
                            $content_to_display = "";
                            foreach($array_elements as $key=>$value){
                                $checked = '';
                                if($value["default"] == "1"){
                                    $checked = 'checked="checked"';
                                }
                                
                                $radio_value = $value["id"];
                                if(isset($is_new) && $is_new == 1){
                                    $radio_value = $key;
                                }
                                
                                $content_to_display .= '<div class="clearfix" style="margin-bottom:25px;">
                                                            <input type="checkbox" class="uk-float-left wk-float-left" value="'.$radio_value.'" id="answ'.$key.'" name="correct_ans[]" '.$checked.' /><span class="lbl"></span>&nbsp; &nbsp;
                                                            <input type="text" class="uk-float-left uk-margin-left wk-float-left wk-margin-left" name="ans_content['.$radio_value.'][]" id="ans'.$key.'" value="'.$value["answer_content"].'" />&nbsp;&nbsp; <input type="button" class="uk-button uk-button-danger wk-button wk-button-danger" value=" X " onclick="javascript:deleteAnswer('.intval($key).'); return false;" /> &nbsp;&nbsp;
                                                            <div id="ans_media_'.$radio_value.'" class="clearfix">
																'.implode(" ", $value["media"]).'
															</div>
                                                        </div>';
                            }
                            echo $content_to_display;
                        ?>
                    </div>
                
					<input type="button" onclick="javascript:addMoreAnswersMultiple();" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_ADD_MORE"); ?>" style="margin-top:10px;" />
                
                    <div class="clearfix"></div>
                    <br/>
                    <?php echo '<b>'.JText::_("GURU_OPTIONS").'</b>';?>
                    <div class="clearfix"></div>
                    <div>
                        <?php echo JText::_("GURU_QUESTION_WEIGHT")?>
                        <select name="question_weight_tf" id="question_weight_tf">
                            <?php
                            for($i=1; $i<=10; $i++){
                            ?>
                                <option value="<?php echo $i;?>" <?php if(@$medias->points == $i){echo 'selected="selected"';} ?> ><?php echo $i;?></option>
                            <?php    
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
		<?php		
		}
		else if($question_type == 'essay'){ // start essay question type
			echo '<div class="uk-grid wk-grid"><div class="uk-width-1-1 wk-width-1-1"><h2 class="gru-page-title">'.JText::_("GURU_QUIZ_ESSAY")." ".JText::_("GURU_QUESTION").'</h2></div></div>';
	?>
    	<div class="uk-grid wk-grid">
			<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-2 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-2">
				<?php echo '<b>'.JText::_("GURU_THE")." ".JText::_("GURU_QUESTION").'</b>';
                ?>
                <div>
                	<?php
						$editor  = new JEditor(JFactory::getConfig()->get("editor"));
						echo $editor->display( 'question_text', ''.@$medias->question_content,'80%', '200px', '20', '50', true, "g_question_text");
					?>
                </div> 
                
                <h3><?php echo JText::_("GURU_ATTACH_MEDIA"); ?></h3>
            
				<?php
                    $media_name = "";
                    $media_id = "";
                    
                    if(isset($media_attached_to_question) && count($media_attached_to_question) > 0){
                        foreach($media_attached_to_question as $media_key=>$media_value){
                            $media_name = $media_value["name"];
                            $media_id = $media_value["id"];
                        }
                    }
                ?>
                
                <input type="text" id="question_media" name="question_media" value="<?php echo $media_name; ?>" placeholder="<?php echo JText::_("GURU_ENTER_MEDIA_NAME"); ?>" style="margin-top:20px; width:97%;" onkeyup="javascript:searchMedia(this.value); return false;" autocomplete="off" />
                <input type="hidden" id="question_media_id" name="question_media_ids[]" value="<?php echo $media_id; ?>" />
                <div id="question_media_list" style="background-color: #f5f5f5; display: none; max-height: 200px; overflow-y: scroll; padding: 5px; width: 98%;">
                    &nbsp;
                </div>
                
            </div>
        
        	<div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-2 wk-width-small-1-1 wk-width-medium-1-1 wk-width-large-1-2">
				<?php echo '<b>'.JText::_("GURU_OPTIONS").'</b>';?>
                <br/><br/><br/>
                <div>
                    <?php echo JText::_("GURU_QUESTION_WEIGHT")?>
                    <select name="question_weight_tf" id="question_weight_tf">
                        <?php
                        for($i=1; $i<=100; $i++){
                        ?>
                            <option value="<?php echo $i;?>" <?php if(@$medias->points == $i){echo 'selected="selected"';} ?> ><?php echo $i;?></option>
                        <?php    
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    <?php
		}
    
$upload_script = 'jQuery(function(){
					jQuery(".useredactor").redactor({
						 buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
					});
					jQuery(".redactor_useredactor").css("height","200px");
				  });';
$doc->addScriptDeclaration($upload_script);
?>			
	<input type="hidden" value="com_guru" name="option"/>
	<input type="hidden" value="savequestion" name="task"/>
    <input type="hidden" value="<?php echo $question_type;?>" name="type"/>
	<input type="hidden" value="<?php echo intval($data_get['cid']);?>" name="quizid"/>
	<input type="hidden" value="guruAuthor" name="controller"/>
    <input type="hidden" value="<?php echo @$data_get['is_from_modal']; ?>" name="is_from_modal">
    <input type="hidden" value="<?php echo @$data_get['qid']; ?>" name="question_id">
    <input type="hidden" value="<?php echo JFactory::getApplication()->input->get("tmpl", ""); ?>" name="tmpl">
</form>