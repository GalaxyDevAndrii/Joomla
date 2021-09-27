jQuery(document).ready(function(){
	//if joomla <= 3.8 means that it will include modal.js script witch generate sbox-window with modal
	if(jQuery('#sbox-window').length){
		jQuery('#GuruModal').remove();
	}
	//if joomla > 3.8 means that it will not include modal.js anymore and will use boostrap modal 
	else{
		jQuery(document).on('click', '.openModal', function(event){
			event.preventDefault();
			event.stopImmediatePropagation();
			var src = jQuery(this).attr('href')+'&openModal=true';
			jQuery('#GuruModal iframe').attr('src',src);
			jQuery("#GuruModal").modal('toggle');
		});
	}
	jQuery("#GuruModal").on("show.bs.modal", function() {
		var height = jQuery(window).height() - 200;
		var width = jQuery(window).width() - 200;
		jQuery(this).find(".modal-body").css({'height':height, 'max-height':height});
		jQuery(this).find(".modal-dialog").css({'max-width':width, 'margin-left':'auto', 'margin-right':'auto'});
	});
	jQuery("#GuruModal").on("hidden.bs.modal", function() {
		jQuery('#GuruModal iframe').attr('src','');
	});
})
