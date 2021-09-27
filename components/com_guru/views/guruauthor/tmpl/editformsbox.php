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
	JHtml::_('bootstrap.framework');
	JHTML::_('behavior.tooltip');
	JHtml::_('behavior.calendar');
	
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::root()."components/com_guru/css/quiz.css");
	$doc->addStyleSheet(JURI::root()."components/com_guru/css/tabs.css");
	
	$the_text_id	= NULL;
	$program 		= $this->program;
	$mainmedia 		= $this->mainmedia;
	$mainquiz 		= $this->mainquiz;
	$mmediam 		= $this->mmediam;
	$lists 			= $this->lists;
	$jumps 			= $this->jumps;
	$tem_lay 		= $this->tem_lay;
	$the_layout 	= $this->the_layout;
	$configuration 	= $this->getConfigsObject();

	$temp_size = $configuration->lesson_window_size_back;
	$temp_size_array = explode("x", $temp_size);
	$width = $temp_size_array["1"]-20;
	$height = $temp_size_array["0"]-20;

	$course_config = json_decode($configuration->ctgpage);		
	$full_image_size = $course_config->ctg_image_size;
	$full_image_proportional = $course_config->ctg_image_size_type == "0" ? "w" : "h";		
	$button1		= NULL;
	$button2		= NULL;
	$button3		= NULL;
	$button4		= NULL;
	$cid			= JFactory::getApplication()->input->get("cid", "0");
	$screen_id		= $cid;
	$progrid		= JFactory::getApplication()->input->get("progrid","0");
	$module         = JFactory::getApplication()->input->get("module","0");
	
	if(intval($module) == 0){
		$module         = JFactory::getApplication()->input->get("day","0");
	}

	$db = JFactory::getDBO();
	$sql = "SELECT name  FROM #__guru_program
	WHERE id =".intval($progrid);
	$db->setQuery($sql);
	$db->execute();
	$coursename = $db->loadColumn();
	
	$sql = "SELECT `step_access_courses` FROM #__guru_program WHERE id =".intval($progrid);
	$db->setQuery($sql);
	$db->execute();
	$step_access_courses = $db->loadColumn();
	$step_access_courses = @$step_access_courses["0"];
	
	$sql = "SELECT title  FROM #__guru_days
	WHERE id =".intval($module);
	$db->setQuery($sql);
	$db->execute();
	$modulename = $db->loadColumn();

	if(isset($jumps))
	foreach($jumps as $element){
		if($element->button==1){
			$button1=$element;
		} elseif($element->button==2){
			$button2=$element;
		} elseif($element->button==3){
			$button3=$element;
		} elseif($element->button==4){
			$button4=$element;
		}
	}
	
	$sql = "Select datetype FROM #__guru_config where id=1 ";
	$db->setQuery($sql);
	$format_date = $db->loadColumn();
	$dateformat = $format_date[0];

	
	$format = "%m-%d-%Y";
	switch($dateformat){
		case "d-m-Y H:i:s": $format = "%d-%m-%Y %H:%M:%S";
			  break;
		case "d.m.Y H:i": $format = "%d.%m.%Y %H:%M"; 
			break;
		case "d/m/Y H:i:s": $format = "%d/%m/%Y %H:%M:%S"; 
			  break;
		case "m-d-Y H:i:s": $format = "%m-%d-%Y %H:%M:%S"; 
			  break;
		case "m/d/Y H:i:s": $format = "%m/%d/%Y %H:%M:%S"; 
			  break;
		case "Y-m-d H:i:s": $format = "%Y-%m-%d %H:%M:%S"; 
			  break;
		case "Y/m/d H:i:s": $format = "%Y/%m/%d %H:%M:%S"; 
			  break;
		case "d-m-Y": $format = "%d-%m-%Y"; 
			  break;
		case "d/m/Y": $format = "%d/%m/%Y"; 
			  break;
		case "m-d-Y": $format = "%m-%d-%Y"; 
			  break;
		case "m/d/Y": $format = "%m/%d/%Y"; 
			  break;
		case "Y-m-d": $format = "%Y-%m-%d"; 
			  break;
		case "Y/m/d": $format = "%Y/%m/%d";		
			  break;  	  	  	  	  	  	  	  	  	  
	}

JHtml::script('plugins/content/jw_allvideos/includes/js/mediaplayer/jwplayer.min.js');
JHtml::script('plugins/content/jw_allvideos/includes/js/wmvplayer/silverlight.js');
JHtml::script('plugins/content/jw_allvideos/includes/js/wmvplayer/wmvplayer.js');
JHtml::script('plugins/content/jw_allvideos/includes/js/quicktimeplayer/AC_QuickTime.js');	
JHtml::stylesheet('plugins/content/jw_allvideos/jw_allvideos/tmpl/Framed/css/template.css');
?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");

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

<style>
	.component, .contentpane {
		background-color:#FFFFFF;
	}
</style>

<style>

#startpublish, #endpublish{
	width:160px;
	float:left;
}

#rowsmedia {
	background-color:#eeeeee;
}
#rowsmedia tr{
	background-color:#eeeeee;
}
#rowsmainmedia {
	background-color:#eeeeee;
}
#rowsmainmedia tr{
	background-color:#eeeeee;
}
div.modal1 {
		left: 4% !important;
		width: 770px;
		top:6%!important;
		position: fixed;
    	z-index: 9999;
}

.modal-backdrop, .modal-backdrop.fade.in {
    opacity: 0.4 !important;
}

body {
	font-size:13px!important;	
}
table {
	font-size:13px!important;	
}
.modal-header {
    border-bottom: none!important;
}
#tab-content{
	margin-top:15px !important;
}
select {
    width: 209px!important;
}

</style>



<script language="javascript" type="text/javascript">	
function showContent1(href){
	document.getElementById('modal1-body').innerHTML = '<iframe id="g_lesson_level2" height="100%" style="height:98% !important;" width="1300" frameborder="0"></iframe>';
	jQuery( '#myModal1 .modal1-body iframe').attr('src', href);
	return false;
}

function close_gb2(){
	window.parent.location.reload(true);
	window.parent.setTimeout('document.getElementById("sbox-window").close()', 1);
}

function page_refresh(sid) {
	//submitform('applyLesson');
	window.location.reload(true);
}

function jump(button, id, title){
	document.getElementById("jumpbutton"+button).value=id;
	for(var i=1;i<=17;i++){
		if(eval(document.getElementById("jumptitle"+button+"L"+i))){
			document.getElementById("jumptitle"+button+"L"+i).innerHTML=title;
			
			jQuery('<a id="deljmp'+button+'L'+i+'" href="#" onclick="javascript:deleteJumpButton(\'jmp'+button+'L'+i+'\', \'jumpbutton'+button+'\', \'jumptitle'+button+'L'+i+'\', \'deljmp'+button+'L'+i+'\'); return false;" style=""><i class="uk-icon-trash-o wk-icon-trash-o"></i>').insertAfter(document.getElementById("jmp"+button+'L'+i));
			
			document.getElementById("jmp"+button+"L"+i).className="uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
			document.getElementById("jumptitle"+button+"L"+i).style.color="#FFFFFF";
		}
	}
	
	for(var i=1;i<=11;i++){
		document.getElementById("jmp"+button+"L"+i).href="<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&button="+button+"&id="+id+"&progrid="+document.getElementById('day').value;
		elemm = document.getElementById("jmp"+button+"L"+i);
		//alert(elemm.href);
		jQuery(elemm).attr("onclick", "showContent1(\'"+elemm.href+"\')");
	}	
}


function test(x, idu, qwe){
	var change = new Array();
	if((x==3)||(x==6)||(x==10)||(x==13)){
		change=new Array(3,6,10,13);
	} 
	else if(x<15) {
		change=new Array(1,2,4,5,7,8,9,11,12,14);
	} 
	else if(x==15){
		change[0]="15";   
	}
	else if(x==99){
		change[0]="99";
	}
	replaced_with = document.getElementById('media_'+x);

	for(i=0; i<=change.length-1; i++){
		rand1  = Math.floor((Math.random() * 10) + 1);
		rand2  = Math.floor((Math.random() * 10) + 1);
		rand = rand1+""+rand2;
		qwe_temp = qwe.replace(/avID_/g, "avID_"+rand);
		
		replace_m = change[i];
		if(typeof(replace_m) != "undefined"){
			to_be_replaced = document.getElementById('media_'+replace_m);
			var element_input = document.createElement("div");
			element_input.innerHTML = qwe_temp;
			to_be_replaced.innerHTML = "";
			to_be_replaced.appendChild(element_input);
			
			document.getElementById('before_menu_med_'+replace_m).style.display = 'none';
			document.getElementById('after_menu_med_'+replace_m).style.display = '';
			document.getElementById('db_media_'+replace_m).value = idu;
			screen_id = <?php echo intval($screen_id);?>; 
			if(replace_m != 99){
				replace_edit_link = document.getElementById('a_edit_media_'+replace_m);
				if(replace_m == 15){
					replace_edit_link.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&tmpl=component&cid='+ idu +'&scr=' + screen_id+'&type=quiz';
				}
				else{
					replace_edit_link.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid='+ idu +'&scr=' + screen_id;
				}
				
				jQuery(replace_edit_link).attr("onclick", "showContent1(\'"+replace_edit_link.href+"\')");
			}
		}				
	}
}

function txtest(x, idu,qwe){	
	if(x<11){
		var change=new Array(1,2,3,4,5,6,7,8,9,10);
		replaced_with = document.getElementById('text_'+x);
		for (i=0;i<=change.length-1;i++){
			if(change[i]==x) continue;
			replace_m = change[i];
			to_be_replaced = document.getElementById('text_'+replace_m);
			to_be_replaced.innerHTML = '&nbsp;'+qwe+'&nbsp;';
	
			document.getElementById('before_menu_txt_'+replace_m).style.display = 'none';
			document.getElementById('after_menu_txt_'+replace_m).style.display = '';
			document.getElementById('db_text_'+replace_m).value = idu;
			
			screen_id = <?php echo intval($screen_id);?>;
			replace_edit_link = document.getElementById('a_edit_text_'+replace_m);
			replace_edit_link.href = '<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid='+ idu +'&scr=' + screen_id;
			replace_edit_link.onchange = function() {showContent1(replace_edit_link.href)};
			
		}		
	} 
	else {
	
	}	
	//window.setTimeout('close_modal()', 200);	
}
function elementInArray(element, array){
	exist = false;
	for(x=0; x<array.length; x++){
		if(array[x] && (element == array[x])){
			exist = true;
		}
	}
	return exist;
}

function get_quiz_result(){
	var how_many_right_answers = 0;
	var number_of_questions = document.getElementById("question_number").value;
	var quize_name = document.getElementById("quize_name").value;	
	
	var quiz_result_header = '<span class="guru_quiz_title"><?php echo JText::_("GURU_QUIZ_RESULT"); ?>:</span>';
	var quiz_result_content = '';
	
	for(i=1; i<=number_of_questions; i++){			
		var the_answer = document.getElementById("question_answergived"+i).value;//selected answer			
		var the_answer_array = new Array();
		the_answer_array = the_answer.split(",");//selected answers
		
		var the_right_answer = document.getElementById("question_answerright"+i).value;//the correct answer
		var the_right_array = new Array();
		the_right_answer_array = the_right_answer.split(",");//selected answers
		
		var the_question = document.getElementById("the_question"+i).value;//question name
		
		var all_answers = document.getElementById("all_answers"+i).value;//all question answers
		var all_answers_array = new Array();
		all_answers_array = all_answers.split(",");//selected answers
		
		var correct_answer = true;
		var answer_count = 0;
		var right_answer_count = 0;
           
		for(t=0; t<the_answer_array.length; t++){
			if(the_answer_array[t] != ""){
				if(!elementInArray(the_answer_array[t], the_right_answer_array)){
					gasit = false;
					break;
				}
				else{
					gasit = true;
					answer_count++;
				}
			}
		}
		quiz_result_content += '<div id="the_quiz">';      
            quiz_result_content += '<ul class="guru_list">';
            if(the_right_answer_array.length == answer_count){
                how_many_right_answers = how_many_right_answers +1;               
                quiz_result_content += '<li class="question right">'+i+'. '+the_question+'</li>';                               
            }
            else{   
                quiz_result_content += '<li class="question wrong g_quize_q">'+i+'. '+the_question+'</li>';               
            }
           
            for(j=0; j<all_answers_array.length; j++){
                //--------------------------------------------
                inArray = false;
                for(k=0; k<the_right_answer_array .length; k++){
                if(all_answers_array[j] == the_right_answer_array [k]){
                inArray = true;
                }
                }
                //--------------------------------------------
               
                if(inArray){
                quiz_result_content += '<li class="correct">'+all_answers_array[j]+'</li>';
                }
                else{
                quiz_result_content += '<li class="incorrect">'+all_answers_array[j]+'</li>';
                }
            }
			quiz_result_content += '</ul>';  
			quiz_result_content += '</div>';  
	}

	quiz_result_header += '<span class="guru_quiz_score"><?php echo JText::_("GURU_YOUR_SCORE"); ?>: '+how_many_right_answers+'/'+number_of_questions+'</span>';
	quize_result = quiz_result_header + quiz_result_content;
	document.getElementById("media_15").innerHTML = quize_result;
}

function setQuestionValue(question_nr, answer){
	existing_value = document.getElementById('question_answergived'+question_nr).value;
	existing_value = answer+","+existing_value;
	document.getElementById('question_answergived'+question_nr).value = existing_value;
}


function submitbutton(pressbutton) {
	var form = document.adminForm;
	//alert(pressbutton);
	if(pressbutton=='saveLesson' || pressbutton=='applyLesson' || pressbutton=='save2Lesson'){
		 if(form['name'].value == ""){
			alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
		 }
		 else if(form['difficultylevel'].value == "none"){
		 	alert( "<?php echo JText::_("GURU_TASKS_JS_DIFF");?>" );		
		 }
		 else if(form['difficultylevel'].value == "<?php echo JText::_('GURU_SELLEVEL'); ?>"){
			alert( "<?php echo JText::_("GURU_TASKS_JS_DIFF");?>" );
			return false;
		 }
		 else if(form['layout_db'].value == 12 && form['step_access'].value == 2){
		 	alert("<?php echo JText::_("GURU_CAN_NOT_CREATE_QUIIZ"); ?>");
			return false;
		 }
		 else{
			//submitform( pressbutton );
			form.task.value = pressbutton;
            form.submit();
		}
	}
	else{ 
		//submitform( pressbutton );
		form.task.value = pressbutton;
        form.submit();
	}
}

function timeToStamp(string_date){
	var form = document.adminForm;
	var time_format = form["time_format"].value;
	myDate = string_date.split(" ");
	myDate = myDate[0].split("-");
	
	if(myDate instanceof Array && myDate.length > 1){
	}
	else{
		myDate = myDate[0].split("/");
	}
	var newDate = '';
	
	switch (time_format){
		case "%m/%d/%Y %H:%M:%S" :
			newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
			break;
		case "%Y-%m-%d %H:%M:%S" :
			newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
			break;
		case "%d-%m-%Y" :
			newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
			break;
		case "%m/%d/%Y" :
			newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
			break;
		case "%Y-%m-%d" :
			newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
			break;
	}
	
	return newDate;
}

function validDateTime(datetime){
	var form = document.adminForm;
	var time_format = form["time_format"].value;
	
	if(datetime == ""){
		return false;
	}
	if(datetime == "<?php echo JText::_("GURU_NEVER");?>"){
		return true;
	}
	
	 
	
	switch (time_format){
		case "%m/%d/%Y %H:%M:%S" :
			var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/ ;
			if(!(date_regex.test(datetime))){
				return false;
			}
			break;
		case "%Y-%m-%d %H:%M:%S" :
			var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) [0-9]{2}:[0-9]{2}:[0-9]{2}$/ ;
			if(!(date_regex.test(datetime))){
				return false;
			}
			break;
		case "%d-%m-%Y" :
			var date_regex = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/ ;
			if(!(date_regex.test(datetime))){
				return false;
			}
			break;
		case "%m/%d/%Y" :
			var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/ ;
			if(!(date_regex.test(datetime))){
				return false;
			}
			break;
		case "%Y-%m-%d" :
			var date_regex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/ ;
			if(!(date_regex.test(datetime))){
				return false;
			}
			break;
	}
	return true;
}

function isInt(n){
	return parseInt(n) == n;
}

function submitbutton2(pressbutton) {
	var form = document.adminForm;
	if(pressbutton=='saveLesson' || pressbutton=='applyLesson' || pressbutton=='save2Lesson'){
		if(form['minutes'].value != ''){
			if(!isInt(form['minutes'].value) || form['minutes'].value < 0){
				alert( "<?php echo JText::_("GURU_NOT_VALID_MINUTES");?>" );
				return false;
			}
		}
		
		if(form['seconds'].value != ''){
			if(!isInt(form['seconds'].value) || form['seconds'].value < 0){
				alert( "<?php echo JText::_("GURU_NOT_VALID_SECONDS");?>" );
				return false;
			}
			
			if(form['seconds'].value > 59){
				alert( "<?php echo JText::_("GURU_NOT_VALID_SECONDS_VALUE");?>" );
				return false;
			}
		}
		
		if (form['name'].value == ""){
			alert( "<?php echo JText::_("GURU_TASKS_JS_NAME");?>" );
		}
		else if(form['difficultylevel'].value == "none"){
			alert( "<?php echo JText::_("GURU_TASKS_JS_DIFF");?>" );
			return false;
		}
		else if(form['layout_db'].value == 12 && form['step_access'].value == 2){
		 	alert("<?php echo JText::_("GURU_CAN_NOT_CREATE_QUIIZ"); ?>");
			return false;
		}
		else{
			start_date = form['startpublish'].value;
			end_date = form['endpublish'].value;

			if(form['endpublish'].value != "Never"){
				start_date = new Date(timeToStamp(start_date)).getTime();
				end_date = new Date(timeToStamp(end_date)).getTime();
				
				if(Date.parse(start_date) > Date.parse(end_date)){
					alert("<?php echo JText::_("GURU_DATE_GRATER"); ?>");
					return false;
				}
			}
			
			Joomla.submitform( pressbutton );
		}
	}
	else{
		Joomla.submitform( pressbutton );
	}
}

function delete_mm(i){
	document.getElementById('trmm'+i).style.display = 'none';
	document.getElementById('show_upload_link_m').style.display = '';
	document.getElementById('delete_mm').value = i;
}
function delete_mq(i){
	document.getElementById('trmq'+i).style.display = 'none';
	document.getElementById('show_upload_link_q').style.display = '';
	document.getElementById('delete_mq').value = i;
}
function delete_temp(i){
	document.getElementById('trm'+i).style.display = 'none';
	document.getElementById('delete_temp').value =  document.getElementById('delete_temp').value+','+i;
}


function ChangeLayout(number){
	form = document.adminForm;
	
	if(number == 12 && form['step_access'].value == 2){
		alert("<?php echo JText::_("GURU_CAN_NOT_CREATE_QUIIZ"); ?>");
		return false;
	}
	
	for(i=1; i<=17; i++){
		if(i==number){
			if(eval(document.getElementById('layout_img_'+i))){
				document.getElementById('layout_img_'+i).style.background = '#b3e5fc';
				document.getElementById('layout'+i).style.display = '';
			}
		}	
		else{
			if(eval(document.getElementById('layout_img_'+i))){
				document.getElementById('layout_img_'+i).style.background = '';
				document.getElementById('layout'+i).style.display = 'none';			
			}
		}
	}
	
	document.getElementById('layout_db').value = number;		
}

function deleteJumpButton(div_id, hidden_id, span_id, delete_id){
	document.getElementById(hidden_id).value = "0";
	document.getElementById(span_id).innerHTML = "<?php echo JText::_('GURU_TASK_JMP_BUT'); ?>";
	//document.getElementById(span_id).style.color = "#0B55C4";
	document.getElementById(delete_id).style.display = "none";
	document.getElementById(div_id).className="uk-button uk-button-primary wk-button wk-button-primary";
	return false;
}

function AutoGen(){
	document.getElementById('forumboardlesson1').style.display = 'none';
	document.getElementById('forumboardlesson2').style.display = 'table-row';
	document.getElementById('kunenabuttonactive').value = 'on';
}

</script>
<?php
	$class1 = "";
	$class2 = "";
	$class3 = "";
	$class4 = "";

	if(isset($button1->id) && $button1->id != NULL){
		$class1 = 'class="g_jumpt"';
	}
	if(isset($button2->id) && $button2->id != NULL){
		$class2 = 'class="g_jumpt"';
	}
	if(isset($button3->id) && $button3->id != NULL){
		$class3 = 'class="g_jumpt"';
	}
	if(isset($button4->id) && $button4->id != NULL){
		$class4 = 'class="g_jumpt"';
	}

?>

<div id="myModal1" class="modal1" style="display:none; width:90% !important; height:100%">
    <div class="modal-header">
        <button style="opacity: 1; position: absolute; right: -30px !important; top: -15px;" type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">
        	<img src="components/com_guru/images/closebox.png">
		</button>
     </div>
     <div class="modal1-body" id="modal1-body" style="background-color:#FFFFFF; height:90%;" >
     	<iframe id="g_lesson_media_content" height="415" width="700" frameborder="0"></iframe>
    </div>
</div>

<div class="uk-clearfix uk-margin-bottom wk-clearfix wk-margin-bottom">
	<div id="toolbar" class="uk-button-group uk-float-right wk-button-group wk-float-right">
        <button class="uk-button uk-button-small wk-button wk-button-small" onclick="javascript:submitbutton2('saveLesson');">
            <span class="fa fa-check"></span>
            Save &amp; Close
        </button>
        <button class="uk-button uk-button-small uk-button-success wk-button wk-button-small wk-button-success" onclick="javascript:submitbutton2('applyLesson');">
            <span class="fa fa-floppy-o"></span>
            Save
        </button>
        <button class="uk-button uk-button-small wk-button wk-button-small" onclick="javascript:submitbutton2('saveLessonNew');">
            <span class="fa fa-check"></span>
            Save &amp; New
        </button>
	</div>
</div>

<form class="uk-form uk-form-horizontal wk-form wk-form-horizontal" action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
 	<input type="hidden" name="page_width" value="0" />
	<input type="hidden" name="page_height" value="0" />
	
 <?php
	
	if ($program->id>0){
		$the_layout_is = $this->select_layout($program->id);
	}
	else{
		$the_layout_is = 6;
	}		
	$layout_media1	= 0;
	$style_before_menu_med_1 = '';
	$style_after_menu_med_1 = 'style="display:none"';
	$layout_text1 = 0;
	$style_before_menu_txt_1 = '';
	$style_after_menu_txt_1 = 'style="display:none"';
				
	$text_is_quiz = 0;
	$the_media_id = $this->select_media($program->id, 1, 1);
	if($the_media_id > 0){
		$layout_media1	= $the_media_id;
		$layout_media1_content = $this->parse_media($layout_media1,"1");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media1_content = str_replace('avID_', 'avID_'.$rand, $layout_media1_content);
		
		$style_before_menu_med_1 = 'style="display:none"';
		$style_after_menu_med_1 = '';						
	}

	$the_text_obj = $this->select_text($program->id, NULL, 1);
				
	if($the_text_obj){
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text1	= $the_text_id;
			$layout_text1_content = $this->parse_txt($layout_text1);
			$style_before_menu_txt_1 = 'style="display:none"';
			$style_after_menu_txt_1 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}	
		}
	}						
			
	$layout_media2	= 0;
	$layout_media3	= 0;
	$style_before_menu_med_2 = '';
	$style_after_menu_med_2 = 'style="display:none"';
	$style_before_menu_med_3 = '';
	$style_after_menu_med_3 = 'style="display:none"';	
	$layout_text2 = 0;
	$style_before_menu_txt_2 = '';
	$style_after_menu_txt_2 = 'style="display:none"';					
	$text_is_quiz = 0;
				
	$the_media_id = $this->select_media($program->id, 1, 2);
	if($the_media_id > 0){
		$layout_media2	= $the_media_id;
		$layout_media2_content = $this->parse_media ($layout_media2,"2");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media2_content = str_replace('avID_', 'avID_'.$rand, $layout_media2_content);
		
		$style_before_menu_med_2 = 'style="display:none"';
		$style_after_menu_med_2 = '';						
	}
				
	$the_media_id = $this->select_media($program->id, 2, 2);
	if($the_media_id > 0){
		$layout_media3	= $the_media_id;
		$layout_media3_content = $this->parse_media($layout_media3, "3");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media3_content = str_replace('avID_', 'avID_'.$rand, $layout_media3_content);
		
		$style_before_menu_med_3 = 'style="display:none"';
		$style_after_menu_med_3 = '';						
	}		
				
	$the_text_obj = $this->select_text($program->id, NULL, 2);
				
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text2	= $the_text_id;
			$layout_text2_content = $this->parse_txt($layout_text2);
			$style_before_menu_txt_2 = 'style="display:none"';
			$style_after_menu_txt_2 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}
	}											


	$layout_media4	= 0;
	$style_before_menu_med_4 = '';
	$style_after_menu_med_4 = 'style="display:none"';
	$layout_text3 = 0;
	$style_before_menu_txt_3 = '';
	$style_after_menu_txt_3 = 'style="display:none"';		
		
	$text_is_quiz = 0;
				
	$the_media_id = $this->select_media($program->id, 1, 3);
	if($the_media_id > 0){
		$layout_media4	= $the_media_id;
		$layout_media4_content = $this->parse_media ($layout_media4,"4");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media4_content = str_replace('avID_', 'avID_'.$rand, $layout_media4_content);
		
		$style_before_menu_med_4 = 'style="display:none"';
		$style_after_menu_med_4 = '';						
	}
					
	$the_text_obj = $this->select_text($program->id, NULL, 3);
				
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text3	= $the_text_id;
			$layout_text3_content = $this->parse_txt ($layout_text3);
			$style_before_menu_txt_3 = 'style="display:none"';
			$style_after_menu_txt_3 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}
	}										
			
	$layout_media5	= 0;
	$layout_media6	= 0;
	$layout_media16	= 0;
	$layout_media17	= 0;
	$style_before_menu_med_5 = '';


	$style_after_menu_med_5 = 'style="display:none"';
	$style_before_menu_med_6 = '';
	$style_after_menu_med_6 = 'style="display:none"';		
	$layout_text4 = 0;
	$style_before_menu_txt_4 = '';
	$style_after_menu_txt_4 = 'style="display:none"';			
		
	$text_is_quiz = 0;
	$the_media_id = $this->select_media($program->id, 1, 4);
	if($the_media_id > 0){
		$layout_media5	= $the_media_id;
		$layout_media5_content = $this->parse_media ($layout_media5,"5");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media5_content = str_replace('avID_', 'avID_'.$rand, $layout_media5_content);
		
		$style_before_menu_med_5 = 'style="display:none"';
		$style_after_menu_med_5 = '';						
	}
					
	$the_media_id = $this->select_media($program->id, 2, 4);
	if($the_media_id > 0){
		$layout_media6	= $the_media_id;
		$layout_media6_content = $this->parse_media($layout_media6, "6");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media6_content = str_replace('avID_', 'avID_'.$rand, $layout_media6_content);
		
		$style_before_menu_med_6 = 'style="display:none"';
		$style_after_menu_med_6 = '';						
	}

	$style_before_menu_med_16 = '';
	$style_after_menu_med_16 = 'style="display:none"';

	$style_before_menu_med_17 = '';
	$style_after_menu_med_17 = 'style="display:none"';

	$the_media_id = $this->select_media($program->id, 1, 16);

	if($the_media_id > 0){
		$layout_media16	= $the_media_id;
		$layout_media16_content = $this->parse_media($layout_media16, "16");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media16_content = str_replace('avID_', 'avID_'.$rand, $layout_media16_content);
		
		$style_before_menu_med_16 = 'style="display:none"';
		$style_after_menu_med_16 = '';
	}

	$the_media_id = $this->select_media($program->id, 1, 17);
	
	if($the_media_id > 0){
		$layout_media17	= $the_media_id;
		$layout_media17_content = $this->parse_media($layout_media17, "17");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media17_content = str_replace('avID_', 'avID_'.$rand, $layout_media17_content);
		
		$style_before_menu_med_17 = 'style="display:none"';
		$style_after_menu_med_17 = '';						
	}
	
	$the_text_obj = $this->select_text($program->id, NULL, 4);
				
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text4	= $the_text_id;
			$layout_text4_content = $this->parse_txt ($layout_text4);
			$style_before_menu_txt_4 = 'style="display:none"';
			$style_after_menu_txt_4 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}							
		}
	}											

			
	$layout_text5 = 0;
	$style_before_menu_txt_5 = '';
	$style_after_menu_txt_5 = 'style="display:none"';	
	$text_is_quiz = 0;
		
	$the_text_obj = $this->select_text($program->id, NULL, 5);
			
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text5	= $the_text_id;
			$layout_text5_content = $this->parse_txt ($layout_text5);
			$style_before_menu_txt_5 = 'style="display:none"';
			$style_after_menu_txt_5 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}		
	}				
			
	$layout_media7	= 0;
	$style_before_menu_med_7 = '';
	$style_after_menu_med_7 = 'style="display:none"';
		
	$the_media_id = $this->select_media($program->id, 1, 6);
	if($the_media_id > 0){
		$layout_media7	= $the_media_id;
		$layout_media7_content = $this->parse_media ($layout_media7,"7");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media7_content = str_replace('avID_', 'avID_'.$rand, $layout_media7_content);
		
		$style_before_menu_med_7 = 'style="display:none"';
		$style_after_menu_med_7 = '';
	}
							
	$layout_media8	= 0;
	$style_before_menu_med_8 = '';
	$style_after_menu_med_8 = 'style="display:none"';
	$layout_text6 = 0;
	$style_before_menu_txt_6 = '';
	$style_after_menu_txt_6 = 'style="display:none"';		
	$text_is_quiz = 0;
	$the_media_id = $this->select_media($program->id, 1, 7);
	if($the_media_id > 0){
		$layout_media8	= $the_media_id;
		$layout_media8_content = $this->parse_media($layout_media8, "8");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media8_content = str_replace('avID_', 'avID_'.$rand, $layout_media8_content);
		
		$style_before_menu_med_8 = 'style="display:none"';
		$style_after_menu_med_8 = '';						
	}

	$the_text_obj = $this->select_text($program->id, NULL, 7);
	if($the_text_obj){
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text6	= $the_text_id;
			$layout_text6_content = $this->parse_txt ($layout_text6);
			$style_before_menu_txt_6 = 'style="display:none"';
			$style_after_menu_txt_6 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}	
		}
	}							
		
	$layout_media9	= 0;
	$layout_media10	= 0;
	$style_before_menu_med_9 = '';
	$style_after_menu_med_9 = 'style="display:none"';
	$style_before_menu_med_10 = '';
	$style_after_menu_med_10 = 'style="display:none"';	
	$layout_text7 = 0;
	$style_before_menu_txt_7 = '';
	$style_after_menu_txt_7 = 'style="display:none"';					
	$text_is_quiz = 0;
				
	$the_media_id = $this->select_media($program->id, 1, 8);
	if($the_media_id > 0){
		$layout_media9	= $the_media_id;
		$layout_media9_content = $this->parse_media ($layout_media9,"9");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media9_content = str_replace('avID_', 'avID_'.$rand, $layout_media9_content);
		
		$style_before_menu_med_9 = 'style="display:none"';
		$style_after_menu_med_9 = '';						
	}
				
	$the_media_id = $this->select_media($program->id, 2, 8);
	if($the_media_id > 0){
		$layout_media10	= $the_media_id;
		$layout_media10_content = $this->parse_media ($layout_media10,"10");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media10_content = str_replace('avID_', 'avID_'.$rand, $layout_media10_content);
		
		$style_before_menu_med_10 = 'style="display:none"';
		$style_after_menu_med_10 = '';						
	}		
				
	$the_text_obj = $this->select_text($program->id, NULL, 8);
				
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text7	= $the_text_id;
			$layout_text7_content = $this->parse_txt ($layout_text7);
			$style_before_menu_txt_7 = 'style="display:none"';
			$style_after_menu_txt_7 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}
	}							
		
	$layout_media11	= 0;
	$style_before_menu_med_11 = '';
	$style_after_menu_med_11 = 'style="display:none"';
	$layout_text8 = 0;
	$style_before_menu_txt_8 = '';
	$style_after_menu_txt_8 = 'style="display:none"';		
	$text_is_quiz = 0;
	$the_media_id = $this->select_media($program->id, 1, 9);
	if($the_media_id > 0){
		$layout_media11	= $the_media_id;
		$layout_media11_content = $this->parse_media ($layout_media11,"11");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media11_content = str_replace('avID_', 'avID_'.$rand, $layout_media11_content);
		
		$style_before_menu_med_11 = 'style="display:none"';
		$style_after_menu_med_11 = '';						
	}
					
	$the_text_obj = $this->select_text($program->id, NULL, 9);
				
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text8	= $the_text_id;
			$layout_text8_content = $this->parse_txt ($layout_text8);
			$style_before_menu_txt_8 = 'style="display:none"';
			$style_after_menu_txt_8 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}
	}											
		
	$layout_media12	= 0;
	$layout_media13	= 0;
	$style_before_menu_med_12 = '';
	$style_after_menu_med_12 = 'style="display:none"';
	$style_before_menu_med_13 = '';
	$style_after_menu_med_13 = 'style="display:none"';		
	$layout_text9 = 0;
	$style_before_menu_txt_9 = '';
	$style_after_menu_txt_9 = 'style="display:none"';			
	$text_is_quiz = 0;
				
	$the_media_id = $this->select_media($program->id, 1, 10);
	if($the_media_id > 0){
		$layout_media12	= $the_media_id;
		$layout_media12_content = $this->parse_media ($layout_media12,"12");
		$style_before_menu_med_12 = 'style="display:none"';
		$style_after_menu_med_12 = '';						
	}
					
	$the_media_id = $this->select_media($program->id, 2, 10);
	if($the_media_id > 0){
		$layout_media13	= $the_media_id;
		$layout_media13_content = $this->parse_media ($layout_media13,"13");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media13_content = str_replace('avID_', 'avID_'.$rand, $layout_media13_content);
		
		$style_before_menu_med_13 = 'style="display:none"';
		$style_after_menu_med_13 = '';						
	}		
	$the_text_obj = $this->select_text($program->id, NULL, 10);
				
	if($the_text_obj){	
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text9	= $the_text_id;
			$layout_text9_content = $this->parse_txt ($layout_text9);
			$style_before_menu_txt_9 = 'style="display:none"';
			$style_after_menu_txt_9 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}							
		}
	}											
			
	$layout_media14	= 0;
	$style_before_menu_med_14 = '';
	$style_after_menu_med_14 = 'style="display:none"';
	$layout_text10 = 0;
	$style_before_menu_txt_10 = '';
	$style_after_menu_txt_10 = 'style="display:none"';		
	$layout_text11 = 0;
	$style_before_menu_txt_11 = '';
	$style_after_menu_txt_11 = 'style="display:none"';	
		
	$text_is_quiz = 0;		
	$the_media_id = $this->select_media($program->id, 1, 11);
	if($the_media_id > 0){
		$layout_media14	= $the_media_id;
		$layout_media14_content = $this->parse_media ($layout_media14,"14");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media14_content = str_replace('avID_', 'avID_'.$rand, $layout_media14_content);
		
		$style_before_menu_med_14 = 'style="display:none"';
		$style_after_menu_med_14 = '';						
	}
					
	$the_text_obj = $this->select_text($program->id, 1, 11);
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id1 = $the_text_obj[0];
		if($the_text_id1 > 0){
			$layout_text10	= $the_text_id1;
			$layout_text10_content = $this->parse_txt ($layout_text10);
			$style_before_menu_txt_10 = 'style="display:none"';
			$style_after_menu_txt_10 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}					
	}
					
	$the_text_obj = $this->select_text($program->id, 2, 11);											
	if($the_text_obj){				
		$the_text_obj = explode('$$$$$', $the_text_obj);
		$the_text_id = $the_text_obj[0];
		if($the_text_id > 0){
			$layout_text11	= $the_text_id;
			$layout_text11_content = $this->parse_txt ($layout_text11);
			$style_before_menu_txt_11 = 'style="display:none"';
			$style_after_menu_txt_11 = '';
			if($the_text_obj[1] == 2){
				$text_is_quiz = 1;						
				$the_quiz_id = $this->real_quiz_id($the_text_id);
			}						
		}					
	}										
			
	$layout_media15	= 0;
	$style_before_menu_med_15 = '';
	$style_after_menu_med_15 = 'style="display:none"';
	$the_media_id = $this->select_media($program->id, 1, 12);
	

	if($the_media_id > 0){
		$layout_media15	= $the_media_id;
		$layout_media15_content = $this->parse_media ($layout_media15,"15");
		
		$rand  = rand(1, 99);
		$rand .= rand(1, 99);
		$layout_media15_content = str_replace('avID_', 'avID_'.$rand, $layout_media15_content);
		
		$style_before_menu_med_15 = 'style="display:none"';
		$style_after_menu_med_15 = '';						
	}
		
	$narration	= 0;
	$style_before_menu_med_99 = '';
	$style_after_menu_med_99 = 'style="display:none"';
		
	$the_media_id = $this->select_media($program->id, 1, 99);
	if($the_media_id > 0){
		$narration	= $the_media_id;
		$narration_content = $this->parse_audio($narration);
		$narration_title = $this->getMediaName($narration);
		$style_before_menu_med_99 = 'style="display:none"';
		$style_after_menu_med_99 = '';						
	}
	
	$db = JFactory::getDBO();
	$kunenayn = "SELECT forum_kunena_generatedt FROM #__guru_task where id=".intval($program->id);
	$db->setQuery($kunenayn);
	$db->execute();
	$kunenayn = $db->loadColumn();

	?>
    <ul class="uk-tab wk-tab" data-uk-tab="{connect:'#tab-content'}">
        <li id="general-tab" class="header-tab uk-active" onclick="javascript:changeTab('general-tab'); return false;"><a href="#"><?php echo JText::_("GURU_GENERAL");?></a></li>
        <li id="layout-tab" class="header-tab" onclick="javascript:changeTab('layout-tab'); return false;"><a href="#"><?php echo JText::_("GURU_LAYOUT");?></a></li>
        <li id="naration-tab" class="header-tab" onclick="javascript:changeTab('naration-tab'); return false;"><a href="#"><?php echo JText::_("GURU_NARATION");?></a></li>
        <li id="publishing-tab" class="header-tab" onclick="javascript:changeTab('publishing-tab'); return false;"><a href="#"><?php echo JText::_("GURU_PUBLISHING");?></a></li>
        <li id="access-tab" class="header-tab" onclick="javascript:changeTab('access-tab'); return false;"><a href="#"><?php echo JText::_("GURU_ACCESS");?></a></li>
    </ul>
   
    <ul class="uk-switcher uk-margin wk-switcher wk-margin" id="tab-content">
        <li id="general-tab-content" class="content-tab uk-active">
        	<div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_LESSON_TITLE");?>:
                    <span class="uk-text-danger wk-text-danger">*</span>
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <input class="inputbox" type="text" name="name" size="40" maxlength="255" value="<?php echo str_replace('"', '&quot;', $program->name); ?>" />
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_TITLE"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_LEVEL");?>:
                    <span class="uk-text-danger wk-text-danger">*</span>
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <?php echo $lists['difficulty'];?>
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_LEVEL"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_DURATION");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <span>
						<?php
                            $minutes = "";
                            $seconds = "";
                            if(isset($program->duration) && trim($program->duration) != ""){
                                $temp = explode("x", trim($program->duration));
                                $minutes = $temp["0"];
                                $seconds = $temp["1"];
                            }
                        ?>
                        <div style="width:20%; line-height: 30px; float:left;">
                            <input class="inputbox uk-float-left wk-float-left" type="text" name="minutes" style="width:50px; padding: 5px;" value="<?php echo $minutes; ?>" /> &nbsp; <?php echo JText::_("GURU_PROGRAM_DETAILS_MINUTES"); ?>
                        </div>
                        
                        <div style="width:20%; line-height: 30px; float:left;">
                            <input class="inputbox uk-float-left wk-float-left" type="text" name="seconds" style="width:50px; padding: 5px;" value="<?php echo $seconds; ?>" /> &nbsp; <?php echo JText::_("GURU_PROGRAM_DETAILS_SECONDS"); ?>
                        </div>
                    
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_DURATION"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                   </span>
                </div>
            </div>

            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_CUSTOM_CSS");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                   	<input class="inputbox" type="text" name="css" size="40" maxlength="255" value="<?php echo $program->css; ?>" />
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_CUSTOM_CSS"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_DESCRIPTION");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                	<div style="width: 100%; float: left; height: 300px; max-height: 300px;">
	            		<style type="text/css">
	            			iframe#description_ifr{
	            				max-height: 250px;
	            			}
	            		</style>
		            	<?php
		            		$editor = new JEditor(JFactory::getConfig()->get("editor"));
		                    echo $editor->display( 'description', ''.stripslashes($program->description),'100%', '220px', '20', '50' );
		                ?>
	            	</div>
                    <!-- <textarea name="description" rows="10" style="width:100%;"><?php echo $program->description; ?></textarea> -->
                </div>
            </div>
        </li>
        
        <li id="layout-tab-content" class="content-tab">
        	<?php
			$srcimg = JURI::base()."/components/com_guru/images/";
	
			$layout_styledisplay = array(1 => 'style="display:none"',2 => 'style="display:none"',3 => 'style="display:none"',4 => 'style="display:none"',5 => 'style="display:none"',6 => 'style="display:none"',7 => 'style="display:none"', 8 => 'style="display:none"', 9 => 'style="display:none"', 10 => 'style="display:none"', 11 => 'style="display:none"', 12 => 'style="display:none;"', 16 => 'style="display:none;"', 17 => 'style="display:none;"');
			$layout_styledisplay[$the_layout_is] = 'style=""';
			
			$layout_imgstyle = array(1 => '',2 => '',3 => '',4 => '',5 => '',6 => '',7 => '',8 => '',9 => '',10 => '',11 => '', 12 => '', 16 => '', 17 => '');
			$layout_imgstyle[$the_layout_is] = 'style="background:#b3e5fc;"';	
		?>
        
			<div id="g_table_layout">
				<div class="uk-grid wk-grid">
                	<div class="uk-width-medium-2-10 uk-text-bold wk-width-medium-2-10 wk-text-bold">
						<?php echo JText::_('GURU_SEL_LAY');?>
                    </div>
                    
                    <div class="uk-width-medium-8-10 uk-text-left wk-width-medium-8-10 wk-text-left">
                    	<img src="<?php echo JURI::root()."administrator/components/com_guru/images/lm/media_back.gif";?>" alt="media"/>
                        <?php echo strtolower(JText::_('GURU_MEDIA_MEDIA'));?>
                        
                        &nbsp;&nbsp;<img src="<?php echo JURI::root()."administrator/components/com_guru/images/lm/text_back.gif";?>" alt="text"/>
						<?php echo JText::_('GURU_MEDIATYPETEXT_ONLY');?>
					
						&nbsp;&nbsp;<img src="<?php echo JURI::root()."administrator/components/com_guru/images/lm/quiz_back.gif";?>" alt="text"/>
						<?php echo JText::_("GURU_QUIZ"); ?>
                    </div>
				</div>
                
				<div class="uk-grid wk-grid">
                    <div class="uk-width-1-1 wk-width-1-1 guru_layouts">
                        <img onClick="javascript:ChangeLayout(6);" class="layout-image" id="layout_img_6" <?php echo $layout_imgstyle[6]; ?> src="<?php echo $srcimg.'screen-6.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(5);" class="layout-image" id="layout_img_5" <?php echo $layout_imgstyle[5]; ?> src="<?php echo $srcimg.'screen-5.gif';?>" alt="" />
                        
                        <?php
							if(intval($step_access_courses) == 2){
						?>
								<img onClick="alert('<?php echo JText::_("GURU_CAN_NOT_CREATE_QUIIZ"); ?>');" class="layout-image" id="layout_img_12" src="<?php echo $srcimg.'screen-12.gif';?>" alt="" />
						<?php
							}
							else{
						?>
                        		<img onClick="javascript:ChangeLayout(12);" class="layout-image" id="layout_img_12" <?php echo $layout_imgstyle[12]; ?> src="<?php echo $srcimg.'screen-12.gif';?>" alt="" />
						<?php
                        	}
						?>
                        
                        <img onClick="javascript:ChangeLayout(16);" class="layout-image" id="layout_img_16" <?php echo $layout_imgstyle[16]; ?> src="<?php echo $srcimg.'screen-16.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(17);" class="layout-image" id="layout_img_17" <?php echo $layout_imgstyle[17]; ?> src="<?php echo $srcimg.'screen-17.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(1);" class="layout-image" id="layout_img_1" <?php echo $layout_imgstyle[1]; ?> src="<?php echo $srcimg.'screen-1.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(7);" class="layout-image" id="layout_img_7" <?php echo $layout_imgstyle[7]; ?> src="<?php echo $srcimg.'screen-7.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(2);" class="layout-image" id="layout_img_2" <?php echo $layout_imgstyle[2]; ?> src="<?php echo $srcimg.'screen-2.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(8);" class="layout-image" id="layout_img_8" <?php echo $layout_imgstyle[8]; ?> src="<?php echo $srcimg.'screen-8.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(3);" class="layout-image" id="layout_img_3" <?php echo $layout_imgstyle[3]; ?> src="<?php echo $srcimg.'screen-3.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(9);" class="layout-image" id="layout_img_9" <?php echo $layout_imgstyle[9]; ?> src="<?php echo $srcimg.'screen-9.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(4);" class="layout-image" id="layout_img_4" <?php echo $layout_imgstyle[4]; ?> src="<?php echo $srcimg.'screen-4.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(10);" class="layout-image" id="layout_img_10" <?php echo $layout_imgstyle[10]; ?> src="<?php echo $srcimg.'screen-10.gif';?>" alt="" />
                        <img onClick="javascript:ChangeLayout(11);" class="layout-image" id="layout_img_11" <?php echo $layout_imgstyle[11]; ?> src="<?php echo $srcimg.'screen-11.gif';?>" alt="" />
					</div>
				</div>
				
                <br />
                <input name="text_is_quiz" type="hidden" value="<?php if(isset($text_is_quiz)) {echo $text_is_quiz;} else {$text_is_quiz=0;}?>">
				
                <div id="layout1" <?php echo $layout_styledisplay[1]; ?>>
					<div id="menu_med_1">
						<div class="uk-grid wk-grid">
							<div class="uk-width-5-10 uk-text-center uk-margin-bottom wk-width-5-10 wk-text-center wk-margin-bottom">
                                <div id="before_menu_med_1" <?php echo $style_before_menu_med_1;?>>
                                    <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=1 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>											
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <script type="text/javascript">
                                        document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=1&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                                    </script>
                                </div>
                                
                                <div id="after_menu_med_1" <?php echo $style_after_menu_med_1;?>>
                                    <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=1 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                                    <script type="text/javascript">
                                        <?php
                                    if( $layout_media1 == 0){
                                        
                                    ?>
                                    document.write('<a id="a_edit_media_1" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media1;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                    <?php }
                                        else{
                                    ?>
                                        document.write('<a  id="a_edit_media_1" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media1;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                    <?php }?>
                                    </script>
									
									<script type="text/javascript">
                                            document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=1&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                                    </script>
                                </div>
							</div>
							
							<div class="uk-width-5-10 uk-text-center uk-margin-bottom wk-width-5-10 wk-text-center wk-margin-bottom">
                                <div id="before_menu_txt_1" <?php echo $style_before_menu_txt_1;?>>
                                     <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=1 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <script type="text/javascript">
                                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=1&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                                    </script>
                                </div>
                                
                                <div id="after_menu_txt_1" <?php echo $style_after_menu_txt_1;?>>
                                    <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=1');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                                    <?php if(!isset($text_is_quiz)||$text_is_quiz == 0) {?>
                                    <script type="text/javascript">
                                    <?php
                                    if( $the_text_id == 0){
                                    
                                    ?>
                                        document.write('<a id="a_edit_text_1" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                    <?php }
                                    else{
                                    ?>
                                        document.write('<a  id="a_edit_text_1" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                    <?php }?>
                                    </script>
                                    <?php } else {?>
                                        <script type="text/javascript">
                                        document.write('<a id="a_edit_text_1" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_quiz_id;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                    </script>
                                    <?php } ?>
                                    <script type="text/javascript">
                                    document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=1&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                                    </script>
                                </div>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>
					
					<div class="uk-grid wk-grid">
						<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                            <div id="media_1">
                                <?php
                                    if($layout_media1 == 0){
                                ?>
                                        <div class="g_admin_media">&nbsp;</div>
                                <?php
                                    }
                                    else{
                                        echo $layout_media1_content;
                                    }
                                ?>
                            </div>
						</div>
                        
						<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                            <div id="text_1">
                                <?php
                                    if($layout_text1 == 0){
                                ?>
                                        <div class="g_admin_text">&nbsp;</div>
                                <?php
                                    }
                                    else{
                                        echo $layout_text1_content;
                                    }
                                ?>
                            </div>
						</div>
					</div>
                    
				<div class="clearfix"></div>
                
				<div id="g_jump_button_ref" class="uk-grid wk-grid">
					<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button1)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L1" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle1L1" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>       
                        <?php
                            if(isset($button1)){
                        ?>
                                <a id="deljmp1L1" href="#" onclick="javascript:deleteJumpButton('jmp1L1', 'jumpbutton1', 'jumptitle1L1', 'deljmp1L1'); return false;" style="">
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
                    
                    <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button2)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L1" href="#" class="<?php echo $class; ?>">
                                <span id="jumptitle2L1" <?php if(isset($button2->id)){echo '  ';}?>>
                                    <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                                </span>
                            </a>
                        <?php
                            if(isset($button2)){
                        ?>
                                <a id="deljmp2L1" href="#" onclick="javascript:deleteJumpButton('jmp2L1', 'jumpbutton2', 'jumptitle2L1', 'deljmp2L1'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
				</div>
				
				<div id="g_jump_button_ref" class="uk-grid wk-grid">
					<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button3)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L1" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle3L1" <?php if(isset($button3->id)){echo '  ';}?>>
                                <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                        <?php
                            if(isset($button3)){
                        ?>
                                <a id="deljmp3L1" href="#" onclick="javascript:deleteJumpButton('jmp3L1', 'jumpbutton3', 'jumptitle3L1', 'deljmp3L1'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
                    
                    <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button4)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L1" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle4L1" <?php if(isset($button4->id)){echo '  ';}?>>
                                <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                        
                        <?php
                            if(isset($button4)){
                        ?>
                                <a id="deljmp4L1" href="#" onclick="javascript:deleteJumpButton('jmp4L1', 'jumpbutton4', 'jumptitle4L1', 'deljmp4L1'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
				</div>
		</div><!-- end layout 1-->

		
		<div id="layout2" <?php echo $layout_styledisplay[2]; ?>>
			<div id="menu_med_2">
				<div class="uk-grid wk-grid">
					<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
						
                        <div id="before_menu_med_2"  <?php echo $style_before_menu_med_2;?>>
                            <a  class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=2 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal"  class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=2&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                            </script>
                        </div>
                            
                        <div id="after_menu_med_2"  <?php echo $style_after_menu_med_2;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=2 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                            <script type="text/javascript">
                                <?php
                                if( $layout_media2 == 0){
                                    
                                ?>
                                document.write('<a  id="a_edit_media_2" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media2;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_media_2" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media2;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                <?php }?>
                            </script>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=2&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                            </script>
                        </div>
						<br />
                        <div class="uk-grid uk-text-center wk-grid wk-text-center">
                            <div id="media_2" class="uk-width-1-1 wk-width-1-1">
                                <?php
                                    if($layout_media2 == 0){
                                ?>
                                        <div class="g_admin_media">&nbsp;</div>
                                <?php
                                    }
                                    else{
                                        echo $layout_media2_content;
                                    }
                                ?>
                            </div>
                        </div>
						
                        <div class="uk-grid wk-grid">
							<div id="menu_med_3" class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                                <div id="before_menu_med_3" <?php echo $style_before_menu_med_3;?>>
                                    <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=3 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <script type="text/javascript">
                                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=3&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                                    </script>
                                </div>
                                <div id="after_menu_med_3"  <?php echo $style_after_menu_med_3;?>>
                                    <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=3 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                                    <script type="text/javascript">
                                    <?php
                                    if( $layout_media3 == 0){
                                        
                                    ?>
                                    document.write('<a  id="a_edit_media_3" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media3;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                    <?php }
                                        else{
                                    ?>
                                        document.write('<a  id="a_edit_media_3" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media3;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                    <?php }?>
                                    </script>
                                    <script type="text/javascript">
                                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=3&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                                    </script>
                                </div>      
							</div>
                        </div>
                        
                        <div class="uk-grid wk-grid">
							<div id="media_3" class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
								<?php 
                                    if ($layout_media3 == 0){
                                ?>
                                        <div class="g_admin_media">&nbsp;</div>
                                <?php
                                    }
                                    else{
                                        echo $layout_media3_content;
                                    }
                                 ?>								
                            </div>
						</div>
					</div>
					
					<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                        <div id="before_menu_txt_2" <?php echo $style_before_menu_txt_2;?>>
                             <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=2 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal"  class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=2&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
                        
                        <div id="after_menu_txt_2" <?php echo $style_after_menu_txt_2;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=2');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                            <?php if ($text_is_quiz == 0) {?>
                            <script type="text/javascript">
                                <?php
                                if( $the_text_id == 0){
                                    
                                ?>
                                document.write('<a id="a_edit_text_2" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_text_2" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }?>
                            </script>
                            <?php } else {?>
                            <script type="text/javascript">
                                document.write('<a id="a_edit_text_2" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                            </script>
                            <?php } ?>
                            <script type="text/javascript">
                            document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=2&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
                        <br />
                        <div id="text_2">
                            <?php
                                if($layout_text2 == 0){
                            ?>
                                    <div class="g_admin_text">&nbsp;</div>
                            <?php
                                }
                                else{
                                    echo $layout_text2_content;
                                }
                            ?>
                        </div>
					</div>
				</div>
			</div>
			
			
			
				<div id="g_jump_button_ref" class="uk-grid wk-grid">
					<div class="uk-width-1-2 uk-text-right wk-width-1-2 wk-text-right">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button1)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L2" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle1L2" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>       
                        <?php
                            if(isset($button1)){
                        ?>
                                <a id="deljmp1L2" href="#" onclick="javascript:deleteJumpButton('jmp1L2', 'jumpbutton1', 'jumptitle1L2', 'deljmp1L2'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
                    
                    <div class="uk-width-1-2 uk-text-left wk-width-1-2 wk-text-left">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button2)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L2" href="#" class="<?php echo $class; ?>">
                                <span id="jumptitle2L2" <?php if(isset($button2->id)){echo '  ';}?>>
                                    <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                                </span>
                            </a>
                        <?php
                            if(isset($button2)){
                        ?>
                                <a id="deljmp2L2" href="#" onclick="javascript:deleteJumpButton('jmp2L2', 'jumpbutton2', 'jumptitle2L2', 'deljmp2L2'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
				</div>
				
				<div id="g_jump_button_ref" class="uk-grid wk-grid">
					<div class="uk-width-1-2 uk-text-right uk-width-1-2 wk-text-right">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button3)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L2" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle3L2" <?php if(isset($button3->id)){echo '  ';}?>>
                                <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                        <?php
                            if(isset($button3)){
                        ?>
                                <a id="deljmp3L2" href="#" onclick="javascript:deleteJumpButton('jmp3L2', 'jumpbutton3', 'jumptitle3L2', 'deljmp3L2'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
                    
                    <div class="uk-width-1-2 uk-text-left wk-width-1-2 wk-text-left">
						<?php
                            $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                            if(isset($button4)){
                                $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                            }
                        ?>
                        
                        <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L2" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle4L2" <?php if(isset($button4->id)){echo '  ';}?>>
                                <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                        
                        <?php
                            if(isset($button4)){
                        ?>
                                <a id="deljmp4L2" href="#" onclick="javascript:deleteJumpButton('jmp4L2', 'jumpbutton4', 'jumptitle4L2', 'deljmp4L2'); return false;" >
                                    <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                                </a>
                        <?php
                            }
                        ?>
					</div>
				</div>
		</div><!-- end layout 2 -->
		
		
		
		<div id="layout3" <?php echo $layout_styledisplay[3]; ?>>
			<div id="menu_med_4">
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                        <div id="before_menu_med_4"  <?php echo $style_before_menu_med_4;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=4 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=4&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                            </script>
                        </div>
                        
                        <div id="after_menu_med_4"  <?php echo $style_after_menu_med_4;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=4 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                            <script type="text/javascript">
                            <?php
                            if( $layout_media4 == 0){
                                
                            ?>
                            document.write('<a  id="a_edit_media_4" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media4;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a  id="a_edit_media_4" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media4;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }?>
                            </script>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=4&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                            </script>
                        </div>
					</div>
				</div>
			</div>
			<br/>
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">					
                     <div id="media_4">
                        <?php
                            if($layout_media4 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media4_content;
                            }
                        ?>
                    </div>
				</div>
			</div>
		   
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div id="before_menu_txt_3" <?php echo $style_before_menu_txt_3;?>>
                        <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=3 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=3&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                        </script>
                    </div>
                    
                    <div id="after_menu_txt_3" <?php echo $style_after_menu_txt_3;?>>
                        <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=3');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                        <?php if ($text_is_quiz == 0) {?>
                        <script type="text/javascript">
                        <?php
                        if( $the_text_id == 0){
                            
                        ?>
                        document.write('<a id="a_edit_text_3" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a  id="a_edit_text_3" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }?>
                        </script>
                        <?php } else {?>
                        <script type="text/javascript">
                        document.write('<a id="a_edit_text_3" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        </script>
                        <?php } ?>
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=3&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                        </script>
                    </div>
				</div>
			</div>
			
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                      <div id="text_3">
                        <?php
                            if($layout_text3 == 0){
                        ?>
                                <div class="g_admin_text">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_text3_content;
                            }
                        ?>
                    </div>
				</div>
			</div>
			
			
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L3" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L3" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L3" href="#" onclick="javascript:deleteJumpButton('jmp1L3', 'jumpbutton1', 'jumptitle1L3', 'deljmp1L3'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">   
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                     
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L3" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle2L3" <?php if(isset($button2->id)){echo '  ';}?>>
                                <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L3" href="#" onclick="javascript:deleteJumpButton('jmp2L3', 'jumpbutton2', 'jumptitle2L3', 'deljmp2L3'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L3" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L3" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L3" href="#" onclick="javascript:deleteJumpButton('jmp3L3', 'jumpbutton3', 'jumptitle3L3', 'deljmp3L3'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L3" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L3" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L3" href="#" onclick="javascript:deleteJumpButton('jmp4L3', 'jumpbutton4', 'jumptitle4L3', 'deljmp4L3'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div><!-- end layout 3 -->
		
		<div id="layout4" <?php echo $layout_styledisplay[4]; ?>>
			<div id="menu_med_5">
				<div class="uk-grid wk-grid">
					<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                        <div id="before_menu_med_5"  <?php echo $style_before_menu_med_5;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=5 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=5&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                            </script>
                        </div>
                        <div id="after_menu_med_5" <?php echo $style_after_menu_med_5;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=5 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                            <script type="text/javascript">
                                <?php
                                if( $layout_media5 == 0){
                                    
                                ?>
                                document.write('<a  id="a_edit_media_5" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media5;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_media_5" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media5;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                <?php }?>
                            </script>
                            <script type="text/javascript">
                                    document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=5&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                            </script>
                        </div>
                        <br/>
                        <div id="media_5">
                            <?php
                                if($layout_media5 == 0){
                            ?>
                                    <div class="g_admin_media">&nbsp;</div>
                            <?php
                                }
                                else{
                                    echo $layout_media5_content;
                                }
                            ?>
                        </div>
					</div>
                    
					<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                        <div id="menu_med_6">
                            <div id="before_menu_med_6" <?php echo $style_before_menu_med_6;?>>
                                <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=6 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <script type="text/javascript">
                                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=6&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                                </script>
                            </div>
                            <div id="after_menu_med_6" <?php echo $style_after_menu_med_6;?>>
                                <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=6 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                                
                                <script type="text/javascript">
                                <?php
                                if( $layout_media6 == 0){
                                    
                                ?>
                                document.write('<a  id="a_edit_media_6" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media6;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_media_6" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media6;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                <?php }?>
                                </script>
                                
                                <script type="text/javascript">
                                    document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=6&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_("GURU_DAY_NEW_MEDIA"); ?></a>');
                                </script>
                            </div>
                            <div class="clearfix"></div>
                            <div id="media_6">
                                <?php
                                    if($layout_media6 == 0){
                                ?>
                                        <div class="g_admin_media">&nbsp;</div>
                                <?php
                                    }
                                    else{
                                        echo $layout_media6_content;
                                    }
                                ?>
                            </div>
                        </div>
					</div>
				</div>
			</div>
				  
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                        <div id="before_menu_txt_4" <?php echo $style_before_menu_txt_4;?>>
                             <a data-toggle="modal"  class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=4 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal"  class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=4&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
                        <div id="after_menu_txt_4" <?php echo $style_after_menu_txt_4;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=4');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                            <?php if ($text_is_quiz == 0) {?>
                            <script type="text/javascript">
                                <?php
                                if( $the_text_id == 0){
                                    
                                ?>
                                document.write('<a id="a_edit_text_4" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_text_4" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }?>
                            </script>
                            <?php } else {?>
                            <script type="text/javascript">
                                document.write('<a id="a_edit_text_4" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                            </script>
                            <?php } ?>
                            
                            
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=4&scr=<?php echo $program->id;?>&action=addtext \');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
                        <br />
                        <div id="text_4">
                            <?php
                                if($layout_text4 == 0){
                            ?>
                                    <div class="g_admin_text">&nbsp;</div>
                            <?php
                                }
                                else{
                                    echo $layout_text4_content;
                                }
                            ?>
                        </div>
					</div>
				</div>
					
					
										  
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L4" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L4" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L4" href="#" onclick="javascript:deleteJumpButton('jmp1L4', 'jumpbutton1', 'jumptitle1L4', 'deljmp1L4'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L4" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle2L4" <?php if(isset($button2->id)){echo '  ';}?>>
                                <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L4" href="#" onclick="javascript:deleteJumpButton('jmp2L4', 'jumpbutton2', 'jumptitle2L4', 'deljmp2L4'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L4" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L4" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L4" href="#" onclick="javascript:deleteJumpButton('jmp3L4', 'jumpbutton3', 'jumptitle3L4', 'deljmp3L4'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L4" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L4" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L4" href="#" onclick="javascript:deleteJumpButton('jmp4L4', 'jumpbutton4', 'jumptitle4L4', 'deljmp4L4'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div><!-- end layout 4 -->
		 
		 <div id="layout5" <?php echo $layout_styledisplay[5]; ?>>
			<div id="menu_med_5">
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                        <div id="before_menu_txt_5" <?php echo $style_before_menu_txt_5;?>>
                             <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=5 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=5&scr=<?php echo $program->id;?>&action=addtext\' );" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
                        
                        <div id="after_menu_txt_5" <?php echo $style_after_menu_txt_5;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=5');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                            <?php if ($text_is_quiz == 0) {?>
                            <script type="text/javascript">
                                <?php
                                if( $the_text_id == 0){
                                    
                                ?>
                                document.write('<a id="a_edit_text_5" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_text_5" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }?>
                            </script>
                            <?php } else {?>
                            <script type="text/javascript">
                                document.write('<a id="a_edit_text_5" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                            </script>
                            <?php } ?>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=5&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
					</div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div class="g_layout_jom">
                        <div id="text_5">
                            <?php if ($layout_text5 == 0) {?>
                            <div class="g_admin_text">&nbsp;</div>
                            <?php }else{
                            echo $layout_text5_content;
                             }?>		
                        </div>	
                    </div>
				</div>
			</div>
			
						
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L5" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L5" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L5" href="#" onclick="javascript:deleteJumpButton('jmp1L5', 'jumpbutton1', 'jumptitle1L5', 'deljmp1L5'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L5" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle2L5" <?php if(isset($button2->id)){echo '  ';}?>>
                                <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L5" href="#" onclick="javascript:deleteJumpButton('jmp2L5', 'jumpbutton2', 'jumptitle2L5', 'deljmp2L5'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L5" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L5" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L5" href="#" onclick="javascript:deleteJumpButton('jmp3L5', 'jumpbutton3', 'jumptitle3L5', 'deljmp3L5'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L5" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L5" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L5" href="#" onclick="javascript:deleteJumpButton('jmp4L5', 'jumpbutton4', 'jumptitle4L5', 'deljmp4L5'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div><!-- end layout 5-->
		
		<div id="layout6" <?php echo $layout_styledisplay[6]; ?>>
			<div id="menu_med_7">
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                        <div id="before_menu_med_7" <?php echo $style_before_menu_med_7;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=7 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=7&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                            </script>
                        </div>
                        <div id="after_menu_med_7" <?php echo $style_after_menu_med_7;?>>
                           <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=7 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                            <script type="text/javascript">
                            <?php
                            if( $layout_media7 == 0){
                                
                            ?>
                            document.write('<a  id="a_edit_media_7" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media7;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a  id="a_edit_media_7" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media7;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }?>
                            </script>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=7&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                            </script>
                        </div>
					</div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div class="g_layout_jom">
                        <div id="media_7">
                        <?php
                            if($layout_media7 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                $sql = "SELECT type  FROM #__guru_media WHERE id =".intval($layout_media7);
                                $db->setQuery($sql);
                                $db->execute();
                                $type = $db->loadColumn();
								
                                if(@$type["0"] == 'Article'){
                                    $url=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                                    $url="http://".substr($url,0,strpos($url,"administrator"));
                                    $pattern = '/src="([^"]*)"/';
                                    preg_match($pattern, $layout_media7_content, $matches);
                                    $src = @$matches["1"];
                                    for($i=0; $i<count($src); $i++){
                                        $src1 = $url.$src;
                                         $layout_media7_content = str_replace($src, $src1,  $layout_media7_content);
                                    }
                                    echo $layout_media7_content;					
                                }
                                else{
                                    echo $layout_media7_content;
                                }
                         }
                        ?>
                        </div>
                    </div>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L6" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L6" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L6" href="#" onclick="javascript:deleteJumpButton('jmp1L6', 'jumpbutton1', 'jumptitle1L6', 'deljmp1L6'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L6" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L6" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L6" href="#" onclick="javascript:deleteJumpButton('jmp2L6', 'jumpbutton2', 'jumptitle2L6', 'deljmp2L6'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L6" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L6" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L6" href="#" onclick="javascript:deleteJumpButton('jmp3L6', 'jumpbutton3', 'jumptitle3L6', 'deljmp3L6'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L6" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L6" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L6" href="#" onclick="javascript:deleteJumpButton('jmp4L6', 'jumpbutton4', 'jumptitle4L6', 'deljmp4L6'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div><!-- end layout 6-->

		<div id="layout16" <?php echo $layout_styledisplay[16]; ?>>
			<div id="menu_med_16">
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                        <div id="before_menu_med_16" <?php echo $style_before_menu_med_16;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addproject&tmpl=component&cid=<?php echo $program->id;?>&med=16 ');" href="#"><?php echo JText::_("GURU_SELECT_PROJECT"); ?></a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&tmpl=component&cid=0&med=16&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_PROJECT');?></a>');
                            </script>
                        </div>
                        <div id="after_menu_med_16" <?php echo $style_after_menu_med_16;?>>
                           <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addproject&tmpl=component&cid=<?php echo $program->id;?>&med=16 ');" href="#"><?php echo JText::_("GURU_REPLACE_PROJECT"); ?></a>				
                            <script type="text/javascript">
                            <?php
                            if( $layout_media16 == 0){
                                
                            ?>
                            document.write('<a id="a_edit_media_16" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&tmpl=component&cid=<?php echo $layout_media16;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_PROJECT"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a id="a_edit_media_16" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&tmpl=component&cid=<?php echo $layout_media16;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_PROJECT"); ?></a>');
                            <?php }?>
                            </script>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editProject&tmpl=component&cid=0&med=16&scr=<?php echo $program->id;?>&action=addproject\');" href="#"><?php echo JText::_('GURU_DAY_NEW_PROJECT');?></a>');
                            </script>
                        </div>
					</div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div class="g_layout_jom">
                        <div id="media_16">
                        <?php
                            if($layout_media16 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media16_content;
                         	}
                        ?>
                        </div>
                    </div>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L16" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L16" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L16" href="#" onclick="javascript:deleteJumpButton('jmp1L16', 'jumpbutton1', 'jumptitle1L16', 'deljmp1L16'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L16" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L16" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L16" href="#" onclick="javascript:deleteJumpButton('jmp2L16', 'jumpbutton2', 'jumptitle2L16', 'deljmp2L16'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L16" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L16" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L16" href="#" onclick="javascript:deleteJumpButton('jmp3L16', 'jumpbutton3', 'jumptitle3L16', 'deljmp3L16'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L16" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L16" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L16" href="#" onclick="javascript:deleteJumpButton('jmp4L16', 'jumpbutton4', 'jumptitle4L16', 'deljmp4L16'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div><!-- end layout 16-->

		<div id="layout17" <?php echo $layout_styledisplay[17]; ?>>
			<div id="menu_med_17">
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                        <div id="before_menu_med_17" <?php echo $style_before_menu_med_17;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addscorm&tmpl=component&cid=<?php echo $program->id;?>&med=17&pid=<?php echo intval($progrid); ?>');" href="#"><?php echo JText::_("GURU_SELECT_SCORM"); ?></a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editScorm&tmpl=component&cid=0&med=17&scr=<?php echo $program->id;?>&action=addmedia&pid=<?php echo intval($progrid); ?>\');" href="#"><?php echo JText::_('GURU_DAY_NEW_SCORM');?></a>');
                            </script>
                        </div>
                        <div id="after_menu_med_17" <?php echo $style_after_menu_med_17;?>>
                           <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addscorm&tmpl=component&cid=<?php echo $program->id;?>&med=17&pid=<?php echo intval($progrid); ?>');" href="#"><?php echo JText::_("GURU_REPLACE_SCORM"); ?></a>				
                            <script type="text/javascript">
                            <?php
                            if( $layout_media17 == 0){
                                
                            ?>
                            document.write('<a id="a_edit_media_17" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editScorm&tmpl=component&cid=<?php echo $layout_media17;?>&scr=<?php echo $program->id;?>&pid=<?php echo intval($progrid); ?>"><?php echo JText::_("GURU_DAY_EDIT_SCORM"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a id="a_edit_media_17" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editScorm&tmpl=component&cid=<?php echo $layout_media17;?>&scr=<?php echo $program->id;?>&pid=<?php echo intval($progrid); ?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_SCORM"); ?></a>');
                            <?php }?>
                            </script>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editScorm&tmpl=component&cid=0&med=17&scr=<?php echo $program->id;?>&action=addscorm&pid=<?php echo intval($progrid); ?>\');" href="#"><?php echo JText::_('GURU_DAY_NEW_SCORM');?></a>');
                            </script>
                        </div>
					</div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div class="g_layout_jom">
                        <div id="media_17">
                        <?php
                            if($layout_media17 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media17_content;
                         	}
                        ?>
                        </div>
                    </div>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L17" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L17" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L17" href="#" onclick="javascript:deleteJumpButton('jmp1L17', 'jumpbutton1', 'jumptitle1L17', 'deljmp1L17'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L17" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L17" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L17" href="#" onclick="javascript:deleteJumpButton('jmp2L17', 'jumpbutton2', 'jumptitle2L17', 'deljmp2L17'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L17" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L17" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L17" href="#" onclick="javascript:deleteJumpButton('jmp3L17', 'jumpbutton3', 'jumptitle3L17', 'deljmp3L17'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L17" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L17" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L17" href="#" onclick="javascript:deleteJumpButton('jmp4L17', 'jumpbutton4', 'jumptitle4L17', 'deljmp4L17'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		</div><!-- end layout 17-->
		
		<div id="layout7" <?php echo $layout_styledisplay[7];?> >
			<div id="menu_med_7">
				<div class="uk-grid wk-grid">
					<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                        <div id="before_menu_txt_6" <?php echo $style_before_menu_txt_6;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=6 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=6&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
                        
                        <div id="after_menu_txt_6" <?php echo $style_after_menu_txt_6;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=6');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                            <?php if ($text_is_quiz == 0) {?>
                            <script type="text/javascript">
                                <?php
                                if( $the_text_id == 0){
                                    
                                ?>
                                document.write('<a id="a_edit_text_6" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }
                                    else{
                                ?>
                                    document.write('<a  id="a_edit_text_6" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button k-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                                <?php }?>
                            </script>
                            <?php } else {?>
                            <script type="text/javascript">
                                document.write('<a id="a_edit_text_6" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                            </script>
                            <?php } ?>
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=6&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                            </script>
                        </div>
					</div>
					
					<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                        <div id="menu_med_8">
                            <div id="before_menu_med_8" <?php echo $style_before_menu_med_8;?>>
                             <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=8 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                                document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=8&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                            </script>
                            </div>
                            <div id="after_menu_med_8" <?php echo $style_after_menu_med_8;?>>
                                <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=8 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                                <script type="text/javascript">
                                    <?php
                                    if( $layout_media8 == 0){
                                        
                                    ?>
                                    document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  id="a_edit_media_8" data-toggle="modal" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media8;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                    <?php }
                                        else{
                                    ?>
                                        document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" id="a_edit_media_8" data-toggle="modal" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media8;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                                    <?php }?>
                                </script>
                                <script type="text/javascript">
                                    document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=8&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                                </script>
                            </div>									
                        </div>
					</div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                    <div id="text_6">
                        <?php
                            if($layout_text6 == 0){
                        ?>
                                <div class="g_admin_text">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_text6_content;
                            }
                        ?>
                    </div>
				</div>
                
				<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                    <div id="media_8">
                    <?php
                        if($layout_media8 == 0){
                    ?>
                            <div class="g_admin_media">&nbsp;</div>
                    <?php
                        }
                        else{
                            echo $layout_media8_content;
                        }
                    ?>
                    </div>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L7" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L7" <?php if(isset($button1->id)){echo '  ';}?>>
                            <?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                        
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L7" href="#" onclick="javascript:deleteJumpButton('jmp1L7', 'jumpbutton1', 'jumptitle1L7', 'deljmp1L7'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L7" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L7" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L7" href="#" onclick="javascript:deleteJumpButton('jmp2L7', 'jumpbutton2', 'jumptitle2L7', 'deljmp2L7'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L7" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L7" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L7" href="#" onclick="javascript:deleteJumpButton('jmp3L7', 'jumpbutton3', 'jumptitle3L7', 'deljmp3L7'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L7" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L7" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L7" href="#" onclick="javascript:deleteJumpButton('jmp4L7', 'jumpbutton4', 'jumptitle4L7', 'deljmp4L7'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>	
		</div><!-- end layout 7 -->
		
		<div id="layout8" <?php echo $layout_styledisplay[8]; ?>>
			<div class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                    <div id="before_menu_txt_7" <?php echo $style_before_menu_txt_7;?>>
                        <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=7 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=7&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                        </script>
                    </div>
                    
                    <div id="after_menu_txt_7" <?php echo $style_after_menu_txt_7;?>>
                        <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=7');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                        <?php if ($text_is_quiz == 0) {?>
                        <script type="text/javascript">
                            <?php
                            if( $the_text_id == 0){
                                
                            ?>
                            document.write('<a id="a_edit_text_7" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a  id="a_edit_text_7" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                            <?php }?>
                        </script>
                        <?php } else {?>
                        <script type="text/javascript">
                            document.write('<a id="a_edit_text_7" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        </script>
                        <?php } ?>
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=7&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                        </script>
                    </div>
                    <br/>
                    <div id="text_7">
                        <?php
                            if($layout_text7 == 0){
                        ?>
                                <div class="g_admin_text">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_text7_content;
                            }
                        ?>
                    </div>
				</div>
				
				<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
					<div id="menu_med_9">
						<div id="before_menu_med_9"  <?php echo $style_before_menu_med_9;?>>
						 <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=9 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
						&nbsp;&nbsp;&nbsp;&nbsp;
						<script type="text/javascript">
							document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=9&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
						</script>
						</div>
						<div id="after_menu_med_9"  <?php echo $style_after_menu_med_9;?>>
							<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=9 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
							<script type="text/javascript">
								<?php
								if( $layout_media9 == 0){
									
								?>
								document.write('<a  id="a_edit_media_9" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media9;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
								<?php }
									else{
								?>
									document.write('<a  id="a_edit_media_9" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media9;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
								<?php }?>
							</script>
							<script type="text/javascript">
								document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=9&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
							</script>
						</div>										
					</div>
					<br />
					<div id="media_9">
						<?php
							if($layout_media9 == 0){
						?>
								<div class="g_admin_media">&nbsp;</div>
						<?php
							}
							else{
								echo $layout_media9_content;
							}
						?>
					</div>
				</div>
				
			</div>		
		
			<br/>
		<div id="menu_med_10">
			<div class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-center uk-push-5-10 wk-width-5-10 wk-text-center wk-push-5-10">
                    <div id="before_menu_med_10"  <?php echo $style_before_menu_med_10;?>>
                        <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=10 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=10&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                    </div>
                    <div id="after_menu_med_10"  <?php echo $style_after_menu_med_10;?>>
                        <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=10 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                        <script type="text/javascript">
                        <?php
                        if( $layout_media10 == 0){
                            
                        ?>
                        document.write('<a  id="a_edit_media_10" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media10;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a  id="a_edit_media_10" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media10;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                        <?php }?>
                        </script>
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=10&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                    </div>
                    
                    <div id="media_10">
                        <?php
                            if($layout_media10 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media10_content;
                            }
                        ?>
                    </div>
				</div>
			</div>
		</div>
	
	   	<div id="g_jump_button_ref" class="uk-grid wk-grid">
			<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button1)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L8" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle1L8" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>       
                <?php
                    if(isset($button1)){
                ?>
                        <a id="deljmp1L8" href="#" onclick="javascript:deleteJumpButton('jmp1L8', 'jumpbutton1', 'jumptitle1L8', 'deljmp1L8'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
            
            <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button2)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L8" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle2L8" <?php if(isset($button2->id)){echo '  ';}?>>
                        <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                <?php
                    if(isset($button2)){
                ?>
                        <a id="deljmp2L8" href="#" onclick="javascript:deleteJumpButton('jmp2L8', 'jumpbutton2', 'jumptitle2L8', 'deljmp2L8'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
		</div>
		
		<div id="g_jump_button_ref" class="uk-grid wk-grid">
			<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button3)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L8" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle3L8" <?php if(isset($button3->id)){echo '  ';}?>>
                        <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                <?php
                    if(isset($button3)){
                ?>
                        <a id="deljmp3L8" href="#" onclick="javascript:deleteJumpButton('jmp3L8', 'jumpbutton3', 'jumptitle3L8', 'deljmp3L8'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
            
            <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button4)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L8" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle4L8" <?php if(isset($button4->id)){echo '  ';}?>>
                        <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                
                <?php
                    if(isset($button4)){
                ?>
                        <a id="deljmp4L8" href="#" onclick="javascript:deleteJumpButton('jmp4L8', 'jumpbutton4', 'jumptitle4L8', 'deljmp4L8'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
		</div>
	</div><!-- end layout 8-->
		
        
		<div id="layout9" <?php echo $layout_styledisplay[9]; ?>>
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div id="before_menu_txt_8" <?php echo $style_before_menu_txt_8;?>>
                         <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=8 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=8&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                        </script>
                    </div>
                    <div id="after_menu_txt_8" <?php echo $style_after_menu_txt_8;?>>
                        <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=8');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                        <?php if ($text_is_quiz == 0) {?>
                        <script type="text/javascript">
                        <?php
                        if( $the_text_id == 0){
                            
                        ?>
                        document.write('<a id="a_edit_text_8" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a  id="a_edit_text_8" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }?>
                        </script>
                        <?php } else {?>
                        <script type="text/javascript">
                        document.write('<a id="a_edit_text_8" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        </script>
                        <?php } ?>
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=8&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                        </script>
                    </div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">					
                    <div id="text_8">
                        <?php
                            if($layout_text8 == 0){
                        ?>
                                <div class="g_admin_text">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_text8_content;
                            }
                        ?>
                    </div>
				</div>
			</div>
			<br />
			<div id="menu_med_11">
				<div class="uk-grid wk-grid">
					<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">						
                        <div id="before_menu_med_11"  <?php echo $style_before_menu_med_11;?>>
                            <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=11 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=11&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                            </script>
                        </div>
                        <div id="after_menu_med_11"  <?php echo $style_after_menu_med_11;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=11 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                            <script type="text/javascript">
                            <?php
                            if( $layout_media11 == 0){
                                
                            ?>
                            document.write('<a  id="a_edit_media_11" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media11;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a  id="a_edit_media_11" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media11;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }?>
                            </script>
                            <script type="text/javascript">
                            document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=11&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                            </script>
                        </div>
					</div>
				</div>
			</div>
			<br />
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">			
                    <div id="media_11">
                        <?php
                            if($layout_media11 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media11_content;
                            }
                        ?>
                    </div>
				</div>
			</div>
			
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L9" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L9" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L9" href="#" onclick="javascript:deleteJumpButton('jmp1L9', 'jumpbutton1', 'jumptitle1L9', 'deljmp1L9'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
				
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L9" href="#" class="<?php echo $class; ?>">
                            <span id="jumptitle2L9" <?php if(isset($button2->id)){echo '  ';}?>>
                                <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                            </span>
                        </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L9" href="#" onclick="javascript:deleteJumpButton('jmp2L9', 'jumpbutton2', 'jumptitle2L9', 'deljmp2L9'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L9" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L9" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L9" href="#" onclick="javascript:deleteJumpButton('jmp3L9', 'jumpbutton3', 'jumptitle3L9', 'deljmp3L9'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
                <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L9" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L9" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L9" href="#" onclick="javascript:deleteJumpButton('jmp4L9', 'jumpbutton4', 'jumptitle4L9', 'deljmp4L9'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
	</div>	<!-- end layout 9 -->	
	
	<div id="layout10" <?php echo $layout_styledisplay[10]; ?>>
		<div class="uk-grid wk-grid">
			<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                <div id="before_menu_txt_9" <?php echo $style_before_menu_txt_9;?>>
                    <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=9 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <script type="text/javascript">
                        document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=9&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                    </script>
                </div>
                <div id="after_menu_txt_9" <?php echo $style_after_menu_txt_9;?>>
                    <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=9');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                    <?php if ($text_is_quiz == 0) {?>
                    <script type="text/javascript">
                        <?php
                        if( $the_text_id == 0){
                            
                        ?>
                        document.write('<a id="a_edit_text_9" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a  id="a_edit_text_9" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }?>
                    </script>
                    <?php } else {?>
                    <script type="text/javascript">
                        document.write('<a id="a_edit_text_9" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                    </script>
                    <?php } ?>
                    <script type="text/javascript">
                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=9&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                    </script>
                </div>
                <br />
                <div id="text_9">
                    <?php
                        if($layout_text9 == 0){
                    ?>
                            <div class="g_admin_text">&nbsp;</div>
                    <?php
                        }
                        else{
                            echo $layout_text9_content;
                        }
                    ?>
                </div>
			</div>
		</div>
	
		
		<div id="menu_med_12">
			<div class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">					
                    <div id="before_menu_med_12"  <?php echo $style_before_menu_med_12;?>>
                        <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=12 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=12&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                    </div>
                    <div id="after_menu_med_12" <?php echo $style_after_menu_med_12;?>>
                        <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=12 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                        <script type="text/javascript">
                        <?php
                        if( $layout_media12 == 0){
                            
                        ?>
                        document.write('<a  id="a_edit_media_12" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media12;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a  id="a_edit_media_12" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media12;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                        <?php }?>
                        </script>
                        <script type="text/javascript">
                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=12&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                    </div>
                    <br />
                    <div id="media_12">
                        <?php
                            if($layout_media12 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media12_content;
                            }
                        ?>
                    </div>
				</div>
				
                <div class="uk-width-5-10 uk-text-center wk-width-5-10 wk-text-center">
                    <div id="menu_med_13">
                        <div id="before_menu_med_13" <?php echo $style_before_menu_med_13;?>>
                         <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=13 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=13&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                        </div>
                        <div id="after_menu_med_13" <?php echo $style_after_menu_med_13;?>>
                            <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=13 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                            <script type="text/javascript">
                            <?php
                            if( $layout_media13 == 0){
                                
                            ?>
                            document.write('<a  id="a_edit_media_13" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media13;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a  id="a_edit_media_13" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media13;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                            <?php }?>
                            </script>
                            <script type="text/javascript">
                            document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=13&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                            </script>
                        </div>
                        <br />
                        <div id="media_13">
                            <?php
                                if($layout_media13 == 0){
                            ?>
                                    <div class="g_admin_media">&nbsp;</div>
                            <?php
                                }
                                else{
                                    echo $layout_media13_content;
                                }
                            ?>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
				<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button1)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L10" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle1L10" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>       
                    <?php
                        if(isset($button1)){
                    ?>
                            <a id="deljmp1L10" href="#" onclick="javascript:deleteJumpButton('jmp1L10', 'jumpbutton1', 'jumptitle1L10', 'deljmp1L10'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
				<div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button2)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                        
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L10" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L10" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button2)){
                    ?>
                            <a id="deljmp2L10" href="#" onclick="javascript:deleteJumpButton('jmp2L10', 'jumpbutton2', 'jumptitle2L10', 'deljmp2L10'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
		
			<div id="g_jump_button_ref" class="uk-grid wk-grid">
            	<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button3)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L10" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle3L10" <?php if(isset($button3->id)){echo '  ';}?>>
                            <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    <?php
                        if(isset($button3)){
                    ?>
                            <a id="deljmp3L10" href="#" onclick="javascript:deleteJumpButton('jmp3L10', 'jumpbutton3', 'jumptitle3L10', 'deljmp3L10'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
                
            	<div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
					<?php
                        $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                        if(isset($button4)){
                            $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                        }
                    ?>
                    
                    <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L10" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle4L10" <?php if(isset($button4->id)){echo '  ';}?>>
                            <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                    
                    <?php
                        if(isset($button4)){
                    ?>
                            <a id="deljmp4L10" href="#" onclick="javascript:deleteJumpButton('jmp4L10', 'jumpbutton4', 'jumptitle4L10', 'deljmp4L10'); return false;" >
                                <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                            </a>
                    <?php
                        }
                    ?>
				</div>
			</div>
	</div><!-- end layout 10-->
	
	
	<div id="layout11" <?php echo $layout_styledisplay[11]; ?>>
		<div class="uk-grid wk-grid">
			<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                <div id="before_menu_txt_10" <?php echo $style_before_menu_txt_10;?>>
                    <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=10 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <script type="text/javascript">
                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=10&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                    </script>
                </div>
                <div id="after_menu_txt_10" <?php echo $style_after_menu_txt_10;?>>
                    <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=10');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                    <?php if ($text_is_quiz == 0) {?>
                    <script type="text/javascript">
                        <?php
                        if(isset($the_text_id1) && $the_text_id1 == 0){
                        ?>
                        document.write('<a id="a_edit_text_10" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id1)) echo $the_text_id1; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a  id="a_edit_text_10" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id1)) echo $the_text_id1; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                        <?php }?>
                    </script>
                    <?php } else {?>
                    <script type="text/javascript">
                        document.write('<a id="a_edit_text_10" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php echo $the_text_id1;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                    </script>
                    <?php } ?>
                    <script type="text/javascript">
                        document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=10&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                    </script>
                </div>
                <br />
                <div id="text_10">
                    <?php
                        if(isset($layout_text10) && $layout_text10 == 0){
                    ?>
                            <div class="g_admin_text">&nbsp;</div>
                    <?php
                        }
                        else{
                            echo $layout_text10_content;
                        }
                    ?>
                </div>
			</div>
		</div>
		<br />
		<div id="menu_med_14">
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                    <div id="before_menu_med_14"  <?php echo $style_before_menu_med_14;?>>
                        <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=14 ');" href="#"><?php echo JText::_("GURU_SELECT_MEDIA"); ?></a>				
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=14&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                    </div>
                    <div id="after_menu_med_14"  <?php echo $style_after_menu_med_14;?>>
                        <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=14 ');" href="#"><?php echo JText::_("GURU_REPLACE_MEDIA"); ?></a>				
                        <script type="text/javascript">
                        <?php
                        if( $layout_media14 == 0){
                            
                        ?>
                        document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" id="a_edit_media_14" data-toggle="modal" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media14;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                        <?php }
                            else{
                        ?>
                            document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" id="a_edit_media_14" data-toggle="modal" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php echo $layout_media14;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_DAY_EDIT_MEDIA"); ?></a>');
                        <?php }?>
                        </script>
                        <script type="text/javascript">
                        document.write('<a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-toggle="modal" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=0&med=14&scr=<?php echo $program->id;?>&action=addmedia\');" href="#"><?php echo JText::_('GURU_DAY_NEW_MEDIA');?></a>');
                        </script>
                    </div>
                    <br />
                    <div id="media_14">
                        <?php
                            if($layout_media14 == 0){
                        ?>
                                <div class="g_admin_media">&nbsp;</div>
                        <?php
                            }
                            else{
                                echo $layout_media14_content;
                            }
                        ?>
                    </div>
				</div>
			</div>
		</div>
		<br />
		<div class="uk-grid wk-grid">
			<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">
                <div id="before_menu_txt_11" <?php echo $style_before_menu_txt_11;?>>
                    <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom"  data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=11 ');" href="#"><?php echo JText::_("GURU_SELECT_TEXT"); ?></a>			
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <script type="text/javascript">
                        document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=0&txt=11&scr=<?php echo $program->id;?>&action=addtext\');" href="#"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                    </script>
                </div>
                <div id="after_menu_txt_11" <?php echo $style_after_menu_txt_11;?>>
                    <a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addtext&tmpl=component&cid=<?php echo $program->id;?>&txt=11');" href="#"><?php echo JText::_("GURU_REPLACE_TEXT"); ?></a>
                    <?php if ($text_is_quiz == 0) {?>
                    <script type="text/javascript">
                    <?php
                    if( $the_text_id == 0){
                        
                    ?>
                    document.write('<a id="a_edit_text_11" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                    <?php }
                        else{
                    ?>
                        document.write('<a  id="a_edit_text_11" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>\');" href="#"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                    <?php }?>
                    </script>
                    <?php } else {?>
                    <script type="text/javascript">
                    document.write('<a id="a_edit_text_11" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsboxx&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_EDIT_TEXT"); ?></a>');
                    </script>
                    <?php } ?>
                    <script type="text/javascript">
                    document.write('<a data-toggle="modal" data-target="#myModal1" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editMedia&tmpl=component&cid=<?php if(isset($the_text_id)) echo $the_text_id; else echo 0;?>&scr=<?php echo $program->id;?>"><?php echo JText::_("GURU_ADD_NEW_TEXT"); ?></a>');
                    </script>
                </div>
                <br />
                <div id="text_11">
                    <?php
                        if($layout_text11 == 0){
                    ?>
                            <div class="g_admin_text">&nbsp;</div>
                    <?php
                        }
                        else{
                            echo $layout_text11_content;
                        }
                    ?>
                </div>
			</div>
		</div>
		
		<div id="g_jump_button_ref" class="uk-grid wk-grid">
			<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button1)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L11" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle1L11" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>       
                <?php
                    if(isset($button1)){
                ?>
                        <a id="deljmp1L11" href="#" onclick="javascript:deleteJumpButton('jmp1L11', 'jumpbutton1', 'jumptitle1L11', 'deljmp1L11'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
            
            <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button2)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                    
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L11" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L11" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                <?php
                    if(isset($button2)){
                ?>
                        <a id="deljmp2L11" href="#" onclick="javascript:deleteJumpButton('jmp2L11', 'jumpbutton2', 'jumptitle2L11', 'deljmp2L11'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
		</div>
		
		<div id="g_jump_button_ref" class="uk-grid wk-grid">
			<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button3)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L11" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle3L11" <?php if(isset($button3->id)){echo '  ';}?>>
                        <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                <?php
                    if(isset($button3)){
                ?>
                        <a id="deljmp3L11" href="#" onclick="javascript:deleteJumpButton('jmp3L11', 'jumpbutton3', 'jumptitle3L11', 'deljmp3L11'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
            
			<div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button4)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L11" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle4L11" <?php if(isset($button4->id)){echo '  ';}?>>
                        <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                
                <?php
                    if(isset($button4)){
                ?>
                        <a id="deljmp4L11" href="#" onclick="javascript:deleteJumpButton('jmp4L11', 'jumpbutton4', 'jumptitle4L11', 'deljmp4L11'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
		</div>
	</div><!-- end layout 11 -->	
	
	<div id="layout12" <?php echo $layout_styledisplay[12]; ?>>
		<div id="menu_med_15">
			<div class="uk-grid wk-grid">
				<div class="uk-width-1-1 uk-text-center wk-width-1-1 wk-text-center">					
                    <div id="before_menu_med_15" <?php echo $style_before_menu_med_15;?> >
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal"  class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=addquiz&tmpl=component&cid=<?php echo $program->id;?>&med=15&type=quiz\');" href="#"><?php echo JText::_("GURU_TASK_SEL_QIZ"); ?></a>');
                            
                        </script>
                    </div>
                    <div id="after_menu_med_15" <?php echo $style_after_menu_med_15;?>>
                        <script type="text/javascript">
                            document.write('<a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1" onClick = "showContent1(\'<?php echo JUri::root() ?>index.php?option=com_guru&controller=guruAuthor&task=addquiz&tmpl=component&cid=<?php echo $program->id;?>&med=15&type=quiz\');" href="#"><?php echo JText::_("GURU_REPLACE_QUIZ"); ?></a>');
                        </script>
                        <script type="text/javascript">
                            <?php
                            if( $layout_media15 == 0){
                                
                            ?>
                            document.write('<a  id="a_edit_media_15" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&tmpl=component&cid=<?php echo $layout_media15;?>&scr=<?php echo $program->id;?>&type=quiz"><?php echo JText::_("GURU_EDITQUIZ"); ?></a>');
                            <?php }
                                else{
                            ?>
                                document.write('<a  id="a_edit_media_15" data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" data-target="#myModal1"  onClick = "showContent1(this.getAttribute(\'href\'));" href="<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editQuiz&tmpl=component&cid=<?php echo $layout_media15;?>&scr=<?php echo $program->id;?>&type=quiz"><?php echo JText::_("GURU_EDITQUIZ"); ?></a>');
                            <?php }?>
                        </script>
                    </div>
				</div>
			</div>
		</div>
			 
		<br />
		
		<div class="uk-grid wk-grid">
			<div id="media_15" class="uk-width-1-1 wk-width-1-1">
			<?php 
				if ($layout_media15 == 0){
			?>
					<div id="g_admin_quiz" class="g_admin_quiz"></div>
			<?php 
				}
				else{
					echo $layout_media15_content;
				}
			 ?>									
			</div>
		</div>
        
		<div id="g_jump_button_ref" class="uk-grid wk-grid">
			<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button1)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=1<?php if(isset($button1)) { echo "&id=".$button1->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp1L12" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle1L12" <?php if(isset($button1->id)){echo '  ';}?>><?php if(isset($button1)) { echo $button1->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>       
                <?php
                    if(isset($button1)){
                ?>
                        <a id="deljmp1L12" href="#" onclick="javascript:deleteJumpButton('jmp1L12', 'jumpbutton1', 'jumptitle1L12', 'deljmp1L12'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
            
            <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button2)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?> 
                    
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=2<?php if(isset($button2)) { echo "&id=".$button2->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp2L12" href="#" class="<?php echo $class; ?>">
                        <span id="jumptitle2L12" <?php if(isset($button2->id)){echo '  ';}?>>
                            <?php if(isset($button2)) { echo $button2->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                        </span>
                    </a>
                <?php
                    if(isset($button2)){
                ?>
                        <a id="deljmp2L12" href="#" onclick="javascript:deleteJumpButton('jmp2L12', 'jumpbutton2', 'jumptitle2L12', 'deljmp2L12'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
		</div>
		
		<div id="g_jump_button_ref" class="uk-grid wk-grid">
			<div class="uk-width-5-10 uk-text-right wk-width-5-10 wk-text-right">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button3)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=3<?php if(isset($button3)) { echo "&id=".$button3->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp3L12" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle3L12" <?php if(isset($button3->id)){echo '  ';}?>>
                        <?php if(isset($button3)) { echo $button3->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                <?php
                    if(isset($button3)){
                ?>
                        <a id="deljmp3L12" href="#" onclick="javascript:deleteJumpButton('jmp3L12', 'jumpbutton3', 'jumptitle3L12', 'deljmp3L12'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
            
            <div class="uk-width-5-10 uk-text-left wk-width-5-10 wk-text-left">
				<?php
                    $class = "uk-button uk-button-primary uk-button-small wk-button wk-button-primary wk-button-small";
                    if(isset($button4)){
                        $class = "uk-button uk-button-danger uk-button-small wk-button wk-button-danger wk-button-small";
                    }
                ?>
                
                <a onclick="showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=jumpbts&progrid=<?php echo intval($progrid);?>&button=4<?php if(isset($button4)) { echo "&id=".$button4->id;}?>');" data-target="#myModal1" data-toggle="modal" id="jmp4L12" href="#" class="<?php echo $class; ?>">
                    <span id="jumptitle4L12" <?php if(isset($button4->id)){echo '  ';}?>>
                        <?php if(isset($button4)) { echo $button4->text;} else { echo JText::_('GURU_TASK_JMP_BUT');} ?>
                    </span>
                </a>
                
                <?php
                    if(isset($button4)){
                ?>
                        <a id="deljmp4L12" href="#" onclick="javascript:deleteJumpButton('jmp4L12', 'jumpbutton4', 'jumptitle4L12', 'deljmp4L12'); return false;" >
                            <i class="uk-icon-trash-o wk-icon-trash-o"></i>
                        </a>
                <?php
                    }
                ?>
			</div>
		</div>
	</div>	<!-- end layout 12 -->
		
									
													
			</div>
        </li>
        
        <li id="naration-tab-content" class="content-tab">
        	<script type="text/javascript">
                function removeNarration(){
                    document.getElementById("db_media_"+99).value="";
                    document.getElementById("media_"+99).style.display="none";
                    document.getElementById("before_menu_med_99").style.display="";
                    document.getElementById("after_menu_med_99").style.display="none";
                }
            </script>
             <div class="control-group clearfix">
             	<div class="clearfix">
                	<label class="control-label g_cell span4" for="name"><?php echo JText::_("GURU_NARAT_MED");?>:</label>
                </div>    
                <div class="controls g_cell span8">
                    &nbsp;&nbsp;
                    <div id="after_menu_med_99" <?php if(isset($style_after_menu_med_99)){echo $style_after_menu_med_99;}?>><p><strong><?php echo JText::_("GURU_SELECTED_AUDIO"); ?>:</strong>&nbsp;&nbsp;&nbsp;<span id="description_med_99" style="font-weight:bold"><?php if(isset($narration_title)){echo $narration_title;}?></span>&nbsp;&nbsp;
                    
                    <a data-toggle="modal" class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" style="text-decoration:none;" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id; ?>&med=99&type=audio');"  style="text-decoration:underline;" href="#">
						<?php echo JText::_("GURU_REPLACE_AUDIO"); ?>
                    </a>
                    &nbsp;&nbsp;
                    <span class="uk-button uk-button-danger wk-button uk-button-danger" onclick="javascript:removeNarration();"><?php echo JText::_("GURU_REMOVE"); ?></span>
                    </div>
                    <br />
                    
                    <div id="before_menu_med_99" <?php if(isset($style_before_menu_med_99)){echo $style_before_menu_med_99;}?> class="pull-left">
                        <a class="uk-button uk-button-primary uk-margin-bottom wk-button wk-button-primary wk-margin-bottom" style="text-decoration:none;" data-toggle="modal" data-target="#myModal1" onClick = "showContent1('<?php echo JURI::root();?>index.php?option=com_guru&controller=guruAuthor&task=addmedia&tmpl=component&cid=<?php echo $program->id;?>&med=99&type=audio ');"  style="text-decoration:underline;" href="#">
							<?php echo JText::_("GURU_SELECT_AUDIO"); ?>
						</a>
                    </div>
                    
                    <div id="media_99" style="width:250px;"><?php if(isset($narration_content)){echo $narration_content;}?></div>
                   </span>    
                </div>
            </div>
        </li>
        
        <li id="publishing-tab-content" class="content-tab">
        	<div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                	<?php echo JText::_("GURU_PRODLPBS");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <?php echo $lists['published']; ?>
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_PRODLPBS"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                	<?php echo JText::_("GURU_PRODLSPUB");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <?php 
					$jnow = new JDate('now');
					$now = $jnow->toSQL(true);
						
					if($program->id<1){
						$start_publish = date("".$dateformat."", strtotime($now));
					}
					else{
						$start_publish = date("".$dateformat."", strtotime($program->startpublish));
					}
					
					echo JHTML::_('calendar', $start_publish, 'startpublish', 'startpublish', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); // calendar change

					?>
					
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_PRODLSPUB"); ?>" >
						<img border="0" src="components/com_guru/images/icons/tooltip.png">
					</span>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                	<?php echo JText::_("GURU_PRODLEPUB");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <?php 
					if(substr($program->endpublish,0,4) =='0000'|| $program->endpublish == JText::_('GURU_NEVER') || $program->id<1){
						$end_publish = "";
					}
					else{
						$end_publish = date("".$dateformat."", strtotime($program->endpublish));
					}
					
					echo JHTML::_('calendar', $end_publish, 'endpublish', 'endpublish', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); // calendar change
					
					?>
					<span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LESSON_PRODLEPUB"); ?>" >
						<img border="0" src="components/com_guru/images/icons/tooltip.png">
					</span>
                </div>
            </div>
        </li>
        
        <li id="access-tab-content" class="content-tab">
        	<script type="text/javascript" language="javascript">
				function changeAccess(access){
					form = document.adminForm;
					
					if(form['layout_db'].value == 12 && form['step_access'].value == 2){
						alert("<?php echo JText::_("GURU_CAN_NOT_CREATE_QUIIZ"); ?>");
						return false;
					}
					
					if(access == 1){
						document.getElementById("all-groups").style.display = "block";
					}
					else{
						document.getElementById("all-groups").style.display = "none";
					}
				}
			</script>
			<?php
				$display = 'none';
				if($program->step_access == 1){
					$display = 'block';
				}
			?>
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_ACCESS_STEPS");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <span>
                        <select name="step_access" onchange="javascript:changeAccess(this.value);" class="uk-float-left wk-float-left">
                            <option value="0" <?php if($program->step_access==0) echo 'selected="selected"'; ?> ><?php echo JText::_('GURU_COU_STUDENTS'); ?></option>
                            <option value="1" <?php if($program->step_access==1) echo 'selected="selected"'; ?> ><?php echo JText::_('GURU_REG_MEMBERS'); ?></option>
                            <option value="2" <?php if($program->step_access==2) echo 'selected="selected"'; ?> ><?php echo JText::_('GURU_REG_GUESTS'); ?></option> 
                        </select>
                        <span class="editlinktip hasTip uk-float-left wk-float-left" title="<?php echo JText::_("GURU_TIP_ACCESS_STEPS"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                        &nbsp;&nbsp;
                        <span style="display:<?php echo $display; ?>; float:left; margin-left:20px;" id="all-groups">
                            <?php
                                $groups = array();
                                if(isset($program->groups_access) && trim($program->groups_access) != ""){
                                    $groups = explode(",", trim($program->groups_access));
                                }
                                echo JHtml::_('access.usergroups', 'groups', $groups, true);
                            ?>
                        </span>
                   </span>
                </div>
            </div>
        </li>
    </ul>

	
		<input type="hidden" name="jumpbutton1" id="jumpbutton1" value="<?php if(isset($button1)) { echo $button1->id;} else {echo "0";}?>" />
        <input type="hidden" name="jumpbutton2" id="jumpbutton2" value="<?php if(isset($button2)) { echo $button2->id;} else {echo "0";}?>" />
        <input type="hidden" name="jumpbutton3" id="jumpbutton3" value="<?php if(isset($button3)) { echo $button3->id;} else {echo "0";}?>" />
        <input type="hidden" name="jumpbutton4" id="jumpbutton4" value="<?php if(isset($button4)) { echo $button4->id;} else {echo "0";}?>" />
		<input type="hidden" name="id" value="<?php echo $program->id; ?>" />
		<input type="hidden" name="task" value="" />
		
		<input type="hidden" name="option" value="com_guru" />
		<input type="hidden" name="controller" value="guruAuthor" />
		<input type="hidden" name="oldTitle" id="oldTitle" value="<?php echo $program->name; ?>" />		
		<input type="hidden" name="layout_db" id="layout_db" value="<?php echo $the_layout_is; ?>" />
		<input type="hidden" name="db_media_1" id="db_media_1" value="<?php echo $layout_media1; ?>" />
		<input type="hidden" name="db_media_2" id="db_media_2" value="<?php echo $layout_media2; ?>" />
		<input type="hidden" name="db_media_3" id="db_media_3" value="<?php echo $layout_media3; ?>" />
		<input type="hidden" name="db_media_4" id="db_media_4" value="<?php echo $layout_media4; ?>" />
		<input type="hidden" name="db_media_5" id="db_media_5" value="<?php echo $layout_media5; ?>" />
		<input type="hidden" name="db_media_6" id="db_media_6" value="<?php echo $layout_media6; ?>" />
		<input type="hidden" name="db_media_7" id="db_media_7" value="<?php echo $layout_media7; ?>" />
		<input type="hidden" name="db_media_8" id="db_media_8" value="<?php echo $layout_media8; ?>" />
		<input type="hidden" name="db_media_9" id="db_media_9" value="<?php echo $layout_media9; ?>" />
		<input type="hidden" name="db_media_10" id="db_media_10" value="<?php echo $layout_media10; ?>" />
		<input type="hidden" name="db_media_11" id="db_media_11" value="<?php echo $layout_media11; ?>" />
		<input type="hidden" name="db_media_12" id="db_media_12" value="<?php echo $layout_media12; ?>" />
		<input type="hidden" name="db_media_13" id="db_media_13" value="<?php echo $layout_media13; ?>" />
		<input type="hidden" name="db_media_14" id="db_media_14" value="<?php echo $layout_media14; ?>" />
		<input type="hidden" name="db_media_15" id="db_media_15" value="<?php echo $layout_media15; ?>" />
		<input type="hidden" name="db_media_16" id="db_media_16" value="<?php echo $layout_media16; ?>" />
		<input type="hidden" name="db_media_17" id="db_media_17" value="<?php echo $layout_media17; ?>" />
		<input type="hidden" name="db_text_1" id="db_text_1" value="<?php echo $layout_text1; ?>" />
		<input type="hidden" name="db_text_2" id="db_text_2" value="<?php echo $layout_text2; ?>" />
		<input type="hidden" name="db_text_3" id="db_text_3" value="<?php echo $layout_text3; ?>" />
		<input type="hidden" name="db_text_4" id="db_text_4" value="<?php echo $layout_text4; ?>" />
		<input type="hidden" name="db_text_5" id="db_text_5" value="<?php echo $layout_text5; ?>" />	
		<input type="hidden" name="db_text_6" id="db_text_6" value="<?php echo $layout_text6; ?>" />	
		<input type="hidden" name="db_text_7" id="db_text_7" value="<?php echo $layout_text7; ?>" />
		<input type="hidden" name="db_text_8" id="db_text_8" value="<?php echo $layout_text8; ?>" />	
		<input type="hidden" name="db_text_9" id="db_text_9" value="<?php echo $layout_text9; ?>" />	
		<input type="hidden" name="db_text_10" id="db_text_10" value="<?php echo $layout_text10; ?>" />	
		<input type="hidden" name="db_text_11" id="db_text_11" value="<?php echo $layout_text11; ?>" />		
		<input type="hidden" name="temp_lays" id="temp_lays" value="0" />	
		<input type="hidden" name="day" id="day" value="<?php echo intval($progrid);?>" />
		<input type="hidden" name="db_media_99" id="db_media_99" value="<?php if(isset($narration)){echo $narration;} ?>" />
		<input type="hidden" name="my_menu_id" value="<?php echo JFactory::getApplication()->input->get("day"); ?>" />
        <a id="close_gb" style="display:none;">#</a>
        <input type="hidden" name="time_format" id="time_format" value="<?php echo $format; ?>" />
        <input type="hidden" name="kunenabuttonactive" id="kunenabuttonactive" value="">
        <input type="hidden" name="module" id="module" value="<?php echo intval($module); ?>">
</form>
<script language="javascript">
	 jQuery('#myModal1').on('hide', function () {
	 jQuery('#myModal1 .modal-body iframe').attr('src', '');
	 jQuery('#myModal1 .modal-body ').html('');
});
</script>