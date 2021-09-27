function equalHeight(a){
	var c = 0,
		b = [];
	
	elements = document.getElementsByClassName(a);
	max_size = 0;
	
	for(i=0; i<elements.length; i++){
		element = elements[i];
		size = element.offsetHeight;
		if(size > max_size){
			max_size = size;
		}
	}
	
	for(i=0; i<elements.length; i++){
		element = elements[i];
		first = element.children[0];
		second = first.children[0];
		third = second.children[0];
		third.style.minHeight = (max_size-30) + "px";
	}
}