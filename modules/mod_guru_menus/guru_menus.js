function showCourses(el, categ_id){
	var element = el;
	var rect = element.getBoundingClientRect();
	var elementLeft,elementTop; //x and y
	var scrollTop = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
	var scrollLeft = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft;
	top_poz = rect.top + scrollTop;
	left_poz = rect.left + scrollLeft;
	
	top_poz = el.offsetTop;
	left_poz = el.offsetLeft;
	width = el.offsetWidth;
	
	left_side = true;
	page_width = document.body.offsetWidth;
	
	if(page_width / 2 < rect.left){
		left_side = false;
	}
	
	if(left_side){
		document.getElementById("guru-menus-sidebar-"+categ_id).style.top = top_poz+"px";
		document.getElementById("guru-menus-sidebar-"+categ_id).style.left = (left_poz + width - 10)+"px";
		document.getElementById("guru-menus-sidebar-"+categ_id).style.visibility = "visible";
	}
	else{
		current_guru_menus_sidebar = document.getElementById("guru-menus-sidebar-"+categ_id);
		minus_surpluse = 0;
		if(current_guru_menus_sidebar.offsetWidth > 202){
			minus_surpluse = current_guru_menus_sidebar.offsetWidth - 202;
		}
		
		document.getElementById("guru-menus-sidebar-"+categ_id).style.top = top_poz+"px";
		document.getElementById("guru-menus-sidebar-"+categ_id).style.left = (left_poz - current_guru_menus_sidebar.offsetWidth)+"px";
		document.getElementById("guru-menus-sidebar-"+categ_id).style.visibility = "visible";
	}
}

function hideCourses(el, categ_id){
	document.getElementById("guru-menus-sidebar-"+categ_id).style.visibility = "hidden";
	el.className = "";
}

function markElement(el){
	el.className = "element-hover";
}

function unmarkElement(el){
	el.className = "";
}