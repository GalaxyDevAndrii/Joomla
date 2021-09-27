var mins, secs, minrem, TimerRunning, TimerID;
TimerRunning = false;
var hadalert=false;

function iJoomlaTimer(var_min, var_sec, quizid, var_rem, show_finish_alert){ //call the Init function when u need to start the timer
	mins = var_min;
	secs = var_sec;
	minrem = var_rem;
	quiz_id = quizid;
	showfa = show_finish_alert;
	iJoomlaStopTimer(quizid);
	iJoomlaStartTimer(quizid);
}
 
function iJoomlaStopTimer(quizid){
	if(TimerRunning){
		clearTimeout(TimerID);
		delete_cookie(quizid+"-"+"m1");
		delete_cookie(quizid+"-"+"m2");
	}
	TimerRunning=false;
}

function iJoomlaStartTimer(quizid){
	TimerRunning=true;

	setCookie(quizid+"-"+"m1", Pad(mins));
	setCookie(quizid+"-"+"m2", Pad(secs));
	
	if(eval(document.getElementById("ijoomlaguru_time"))){
		document.getElementById("ijoomlaguru_time").innerHTML = Pad(mins)+":"+Pad(secs);
	}
	
	var self = this;
	TimerID=self.setTimeout("iJoomlaStartTimer("+quizid+")", 1000);
	Check();
	if(mins==0 && secs==0){
		iJoomlaStopTimer(quizid);
		alert("Sorry, this quiz time has expired, see your results below");
	}
	
	if(secs==0){
		mins --;
		secs = 60;
	}
	secs --;
}

function Check(){
	if(minrem == 30 || minrem == 15){
		// 30 or 15 sec
		if(secs == minrem && secs != 0 && mins == 0){
			if(showfa == 0){
				document.body.style.opacity = "0.1";
				alert(you_have_lang+" "+minrem+" "+seconds_more_lang);
				document.body.style.opacity = "1";
			}
		}
		else if(mins==0 && secs==0){
			if(eval(document.getElementById("submitbutton"))){
				document.getElementById("autosubmit").value="1";
				document.getElementById("submitbutton").click();
			}
		}
	}
	else if(minrem == 1 || minrem == 2){
		// 1 or 2 min
		if(mins == minrem && secs == 0 && mins != 0){
			if(showfa == 0){
				min_text = minutes_lang;
				
				if(minrem == 1){
					min_text = minute_lang;
				}
				
				document.body.style.opacity = "0.1";
				alert(you_have_lang+" "+minrem+" "+min_text+" "+more_take_lang);
				document.body.style.opacity = "1";
			}
		}
		else if(mins==0 && secs==0){
			if(eval(document.getElementById("submitbutton"))){
				document.getElementById("autosubmit").value="1";
				document.getElementById("submitbutton").click();
			}
		}
	}
}

function Pad(number){ //pads the mins/secs with a 0 if its less than 10
	if(number < 10){
		number = 0+""+number;
	}
	return number;
}



function setCookie(c_name,value){
	document.cookie = c_name + "=" + value;
}

function delete_cookie(cookie_name){
  var cookie_date = new Date ( );  // current date & time
  cookie_date.setTime ( cookie_date.getTime() - 1 );
  document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}



/*-------------------------------------------------------*/
function show_hidde(id, patth){
	// desktop
	var div = document.getElementById("table_d_"+id);
	var td = document.getElementById("td_d_"+id);
	var img= document.getElementById("img_d_"+id);
	
	if(div != null){
		if(div.style.display == "block"){
			img.src=patth+"arrow-down.gif";
			div.style.display = "none";
			td.style.borderBottom="none";
		}
		else{
			img.src=patth+"arrow-right.gif";
			div.style.display = "block";
			td.style.borderBottom="2px solid rgb(247, 247, 247)";
		}
	}
	
	// phone
	var div = document.getElementById("table_p_"+id);
	var td = document.getElementById("td_p_"+id);
	var img= document.getElementById("img_p_"+id);
	
	if(div != null){
		if(div.style.display == "block"){
			img.src=patth+"arrow-down.gif";
			div.style.display = "none";
			td.style.borderBottom="none";
		}
		else{
			img.src=patth+"arrow-right.gif";
			div.style.display = "block";
			td.style.borderBottom="2px solid rgb(247, 247, 247)";
		}
	}
}

function screenwidth(){
	var w = screen.width;
	var h = screen.height;
	document.write(w+"----"+h);
}


function popup(url) {
	var w = 900;
	var h = 600;
	var left = (screen.width/2)-(w/2);
	var top = (screen.height/2)-(h/2);
	var targetWin = window.open (url, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

function setViewed(div_id, img_path){
	document.getElementById(div_id).innerHTML = '<img src="'+img_path+'" alt="viewed" />';
	document.getElementById(div_id).style.marginTop = "-20px";
	document.getElementById(div_id).style.display = "block";
}
function setViewed1(div_id, img_path, iframe_url, lesson_width, lesson_height ){
	document.getElementById(div_id).innerHTML = img_path ;
	document.getElementById(div_id).style.marginTop = "-20px";
	document.getElementById(div_id).style.display = "block";
	
	jQuery.ajax({
      url: iframe_url,
      success: function(response){
	   jQuery('#yourModalId').attr('data-url', iframe_url);  
       jQuery( '#yourModalId .modal-body').html(response);
      }
    });
}


function closeBox(){
	//window.parent.setTimeout('document.getElementById("myModal").close()', 1);
	window.parent.setTimeout('window.parent.location.reload(false)', 1);
}

function setQuestionValue(question_nr, answer, a){
	document.getElementById('question_answergivedbyuser'+question_nr).value  += a +"||";
}

function renewCourse(message, course_id, path){
	if(confirm(message)){
		window.location.href = path+"index.php?option=com_guru&view=guruPrograms&task=buy_action&course_id="+course_id;
	}
	else{
		window.location.href = path+"index.php?option=com_guru&view=guruPrograms&task=view&cid="+course_id;
	}
}

function notBuyCourse(){
	alert("You don't have access to this course, you must to buy this first.");
	return false;
}

function jumpNoAccess(course_id){
	window.parent.setTimeout('document.getElementById("sbox-window").close()', 1);
	link_element = "index.php?option=com_guru&controller=guruEditplans&tmpl=component&course_id="+course_id;
	window.location.href = link_element;
}

function changePage(current, total){
	for(i=1; i<=total; i++){
		if(i == current){
			document.getElementById("quiz_page_"+i).style.display = "block";
			document.getElementById("list_"+i).innerHTML = '<span class="pagenav">'+i+'</span>';
		}
		else{
			document.getElementById("quiz_page_"+i).style.display = "none";
			document.getElementById("list_"+i).innerHTML = '<a href="#" onclick="changePage('+i+', '+total+'); return false;">'+i+'</a>';
		}
		
		if(current == 1){
			document.getElementById("pagination-start").innerHTML = '<span class="pagenav">Start</span>';
			document.getElementById("pagination-prev").innerHTML = '<span class="pagenav">Prev</span>';
			
			document.getElementById("pagination-next").innerHTML = '<a onclick="changePage('+(current + 1)+', '+total+'); return false;" href="#">Next</a>';
			document.getElementById("pagination-end").innerHTML = '<a onclick="changePage('+total+', '+total+'); return false;" href="#">End</a>';
		}
		else if(current == total){
			document.getElementById("pagination-start").innerHTML = '<a onclick="changePage(1, '+total+'); return false;" href="#">Start</a>';
			document.getElementById("pagination-prev").innerHTML = '<a onclick="changePage('+(current - 1)+', '+total+'); return false;" href="#">Prev</a>';
			
			document.getElementById("pagination-next").innerHTML = '<span class="pagenav">Next</span>';
			document.getElementById("pagination-end").innerHTML = '<span class="pagenav">End</span>';
		}
		else{
			document.getElementById("pagination-start").innerHTML = '<a onclick="changePage(1, '+total+'); return false;" href="#">Start</a>';
			document.getElementById("pagination-prev").innerHTML = '<a onclick="changePage('+(current - 1)+', '+total+'); return false;" href="#">Prev</a>';
			
			document.getElementById("pagination-next").innerHTML = '<a onclick="changePage('+(current + 1)+', '+total+'); return false;" href="#">Next</a>';
			document.getElementById("pagination-end").innerHTML = '<a onclick="changePage('+total+', '+total+'); return false;" href="#">End</a>';
		}
	}
}

function answerTrueFalseSelected(question_id, answer_id){
	var radio = document.getElementsByName("truefs_ans["+question_id+"]");
	for(i=0; i<radio.length; i++){
		element = radio[i];
		if(element.checked){
			answer_id = element.value;
			document.getElementById("guru-question-answer-"+question_id+"-"+answer_id).style.borderColor = "#008000";
		}
		else{
			answer_id = element.value;
			document.getElementById("guru-question-answer-"+question_id+"-"+answer_id).style.borderColor = "#CCCCCC";
		}
	}
}

function answerSingleSelected(question_id, answer_id){
	var radio = document.getElementsByName("answers_single["+question_id+"]");
	for(i=0; i<radio.length; i++){
		element = radio[i];
		if(element.checked){
			answer_id = element.value;
			document.getElementById("guru-question-answer-"+question_id+"-"+answer_id).style.borderColor = "#008000";
		}
		else{
			answer_id = element.value;
			document.getElementById("guru-question-answer-"+question_id+"-"+answer_id).style.borderColor = "#CCCCCC";
		}
	}
}

function answerMultipleSelected(question_id, answer_id){
	var radio = document.getElementsByName("multiple_ans["+question_id+"][]");
	for(i=0; i<radio.length; i++){
		element = radio[i];
		if(element.checked){
			answer_id = element.value;
			document.getElementById("guru-question-answer-"+question_id+"-"+answer_id).style.borderColor = "#008000";
		}
		else{
			answer_id = element.value;
			document.getElementById("guru-question-answer-"+question_id+"-"+answer_id).style.borderColor = "#CCCCCC";
		}
	}
}

function changeSheet(key){
	nr_sheets = document.getElementById("nr-sheets").value;
	
	for(var i = 0; i < nr_sheets; i++){
		if(i == parseInt(key)){
			document.getElementById("sheet-"+i).style.display = "block";
			document.getElementById("btn-sheet-"+i).className = "uk-button uk-button-success sheet-btn";
		}
		else{
			document.getElementById("sheet-"+i).style.display = "none";
			document.getElementById("btn-sheet-"+i).className = "uk-button uk-button-primary sheet-btn";
		}
	}
}