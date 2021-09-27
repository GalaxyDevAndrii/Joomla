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
include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
$helper = new guruHelper();
$div_menu = $helper->createStudentMenu();
$page_title_cart = $helper->createPageTitleAndCart();
$document = JFactory::getDocument();
$guruHelper = new guruHelper;

$document->setTitle(trim($this->projectDetail->title));

JHTML::_('behavior.tooltip');

//$document->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet('components/com_guru/css/tabs.css');
//$document->addScript('components/com_guru/js/fileuploader.js');
JHtml::stylesheet('components/com_guru/css/fileuploader.css');
//$document->addScript(JURI::root().'components/com_guru/js/redactor.min.js');
JHtml::stylesheet('components/com_guru/css/redactor.css');
?>
<div class="gru-mycoursesauthor">
	<?php echo $div_menu?>
    <?php echo $page_title_cart?>
</div>

<div class="uk-grid uk-margin wk-grid wk-margin">
    <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo $this->projectDetail->title;?></h2></div>
</div>
<div class="clearfix"></div>
<div class="g_sect clearfix">
	<i><?php echo JText::_('GURU_END')?> : <?php echo $guruHelper->getDate($this->projectDetail->end)?></i>
    <p><?php echo $this->projectDetail->description;?></p>
	 <form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" autocomplete="off" style="width: 95%">
		<div class="uk-form-controls wk-form-controls" style="background-color: #EEE;border:1px dashed #999;padding:10px">
			<div style="text-align: center;">
                <?php if($this->projectResultDetail->score):?>
                    <h3><?php echo JText::_('GURU_QUIZ_SCORE')?> : <?php echo $this->projectResultDetail->score?></h3>
                <?php endif?>
                <strong><?php echo JText::_('GURU_UPLOAD_RESULT')?></strong><div class="clearfix"></div>
    <br>        
    	        <div id="fileUploader"></div>
    	        &nbsp;
    	        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IMAGE"); ?>" >
    	            <img border="0" src="components/com_guru/images/icons/tooltip.png">
    	        </span>
    	        <input type="hidden" name="file" id="file" value=""/>
                <?php if($this->projectResultDetail->file):?>
                    <div class="clearfix"></div>
                    <br>
                <a href="<?php echo $this->projectResultDetail->file?>" class="uk-button uk-button-success wk-button wk-button-success" target="_blank"><i class="fa fa-download"></i> <?php echo JText::_('GURU_DOWNLOAD_FILE')?></a>
                <?php endif?>
            </div>
	        <div class="clearfix"></div>
	        <br>
            <textarea name="description" class="useredactor" style="width:100%;height: 100%;text-align: "><?php echo $this->projectResultDetail->desc ?></textarea>
            <div class="clearfix"></div>
            <div style="text-align: center;">
                <br>
    	        <button class="uk-button uk-button-success wk-button wk-button-success">
    				<?php echo JText::_('GURU_SUBMIT')?>
    			</button>
            </div>
	    </div>

	    <input type="hidden" name="project_id" value="<?php !empty($this->projectDetail->id)?'':$this->projectDetail->id?>">
        <input type="hidden" name="task" value="saveProjectResult">
        <input type="hidden" name="option" value="com_guru">
        <input type="hidden" name="controller" value="guruProjects">
        <input type="hidden" name="action" id="action" value="apply">
        <input type="hidden" name="id" id="id" value="<?php echo $this->projectDetail->id?>">
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
<script type="text/javascript">
    jQuery( document ).ready(function(){
    jQuery(".useredactor").redactor({
             buttons: ['bold', 'italic', 'underline', 'link', 'alignment', 'unorderedlist', 'orderedlist']
        });
        jQuery(".redactor_useredactor").css("height","400px");
      });
	jQuery(function(){
        
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('fileUploader'),
                action: '<?php JURI::root() ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_image',
                params:{
                    folder:'courses',
                    mediaType:'image',
                    size: 10,
                    type: ''
                },
                onSubmit: function(id,fileName){
                    jQuery('.qq-upload-list li').css('display','none');
                },
                onComplete: function(id,fileName,responseJSON){
                    if(responseJSON.success == true){
                        jQuery('.qq-upload-success').append('- <span style="color:#387C44;"><?php echo JText::_('GURU_UPLOAD_SUCCESS')?></span>');
                        if(responseJSON.locate) {
                            //jQuery(\'#view_imagelist23\').attr("src", "../"+responseJSON.locate +"/"+ fileName);
                            jQuery('#view_imagelist23').attr("src", '<?php echo JURI::root()?>'+responseJSON.locate +'/'+ fileName+'?timestamp=' + new Date().getTime());
                            jQuery('#file').val(responseJSON.locate +'/'+ fileName);
                        }
                    }
                },
                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF', 'xls', 'XLS','zip','pdf'],
                sizeLimit: '10M',
                multiple: false,
                maxConnections: 1
            });           
        }
        createUploader();
    });

</script>
