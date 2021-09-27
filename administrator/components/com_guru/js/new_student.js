function showUsername(){
	 var div = document.getElementById("user_name");
	 div.style.display = "block";
	 document.adminForm.action.value="new_existing_student";
	 return true;
}

function hideUsername(){
	 var div = document.getElementById("user_name");
	 div.style.display = "none";
	 document.adminForm.action.value="new_student";
	 return true;
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