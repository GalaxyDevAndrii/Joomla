function selectrealtime(val){
	var div = document.getElementById("free_courses");
	div.style.display = val == 0 ? "block" : "none";
	
	var div = document.getElementById("members-list");
	div.style.display = val == 1 ? "block" : "none";
}

function guruChangeBcolor(id, color){
	if(color == "none" || color == ""){
		color = "FFFFFF";	
	}
	
	if(id == "pick_notdonecolor"){
		document.getElementById('timetest').style.color = "#"+color;
	}
	else if(id == "pick_txtcolor"){
		document.getElementById('timeleft').style.color = "#"+color;
	}
	else if(id == "pick_xdonecolor"){
		document.getElementById('totalbg').style.backgroundColor = "#"+color;
		document.getElementById('divtotal').style.backgroundColor = "#"+color;
	}
	else if(id == "pick_donecolor"){
		document.getElementById('divtotal').style.borderColor = "#"+color;
		document.getElementById('timeleft').style.backgroundColor = "#"+color;
		document.getElementById('timeleft').style.borderColor = "#"+color;
	}
	else if(id == "pick_rdonecolor"){
		document.getElementById('success').style.backgroundColor = "#"+color;
	}
	else if(id == "pick_pnotdonecolor"){
		document.getElementById('danger').style.backgroundColor = "#"+color;
	}
	else if(id == "pick_stxtcolor"){
		document.getElementById('warning').style.backgroundColor = "#"+color;
	}
}

function guruchangeFont(value){
	document.getElementById('divtotal').style.fontFamily = "'"+value+"'";
}
function guruchangeSizeW(value){
	document.getElementById('divtotal').style.width = value+'px';
}
function guruchangeSizeH(value){
	document.getElementById('divtotal').style.height = value+'px';
}
function guruchangeSizeFN(value){
	document.getElementById('timetest').style.fontSize = value+'px';
}
function guruchangeSizeFM(value){
	document.getElementById('minsec').style.fontSize = value+'px';
}
function guruchangeSizeFW(value){
	document.getElementById('timeleft').style.fontSize = value+'px';
	document.getElementById('minsec').style.fontSize = value+'px';
	
}
function guruShowLessons(value){
	if(value == 1){
		document.getElementById('lessons_release_td').style.display = "table-row";
		document.getElementById('lessons_show_td').style.display = "table-row";
	}
	else{
		document.getElementById('lessons_release_td').style.display = "none";
		document.getElementById('lessons_show_td').style.display = "none";
	}
}
function guruPopupChangeOption(){
	alert("This change will be propagated over the way the lessons of this course will be released!");

	//$lesson_release_value = document.adminForm.lesson_release.value;
	var lesson_release = document.getElementById("lesson_release");
	var index = lesson_release.selectedIndex;
	$lesson_release_value = lesson_release.options[index].value;
	switch (parseInt($lesson_release_value)){
		case 4:
			document.getElementById("based-on-hour").style.display = "block";
			document.getElementById("based-on-lesson-per-release").style.display = "none";
		break;
		case 0:
		case 5:
			document.getElementById("based-on-hour").style.display = "none";
			document.getElementById("based-on-lesson-per-release").style.display = "none";
		break
		default:
			document.getElementById("based-on-lesson-per-release").style.display = "block";
			document.getElementById("based-on-hour").style.display = "none";
		break;
	}
}

function isFloat(nr){
	return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
}

function guruChangeHeight(value){
	if(isFloat(st_width) || st_width > 0){
		document.getElementById('progress').style.height = value+'px';
	}
}
function guruChangeWidth(value){
	if(isFloat(st_width) || st_width > 0){
		document.getElementById('progress').style.width = value+'px';
	}
}
