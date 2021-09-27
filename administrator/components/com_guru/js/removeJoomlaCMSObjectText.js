// Removes the repetitive text "Joomla\CMS\Object\CMSObject at the bottom of editor comming from $editorul->display()"
jQuery(document).ready(function($){
	jQuery('.js-editor-tinymce').parent().contents().filter(function (){
		if(this.nodeType === 3 && this.nodeValue.indexOf("Joomla\\CMS\\Object") !== -1){
			//nodeType = 3 is for text node type
			jQuery(this).remove();
		} 
	});
})