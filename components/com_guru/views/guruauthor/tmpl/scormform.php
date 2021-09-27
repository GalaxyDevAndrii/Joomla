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
JHTML::_('behavior.tooltip');
$db = JFactory::getDBO();
$div_menu = $this->authorGuruMenuBar();

$doc = JFactory::getDocument();

//$doc->addScript('components/com_guru/js/fileuploader.js');
JHtml::stylesheet('components/com_guru/css/fileuploader.css');
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_COURSE')));

//$doc->addScript(JURI::root().'components/com_guru/js/redactor.min.js');
JHtml::stylesheet('components/com_guru/css/redactor.css');

$format = "%Y-%m-%d %H:%M:%S";

if(isset($this->scormDetail->id) && $this->scormDetail->id > 0){
    $user = JFactory::getUser();

    if(intval($this->scormDetail->author_id) != intval($user->id)){
        // teacher edit a scorm that is from another teacher
        $app = JFactory::getApplication();
        $app->redirect(JRoute::_("index.php?option=com_guru&view=guruauthor&task=scorm&layout=scorm"));
    }
}

?>
<div class="gru-mycoursesauthor">
	<?php echo $div_menu?>
</div>

<div class="uk-grid uk-margin wk-grid wk-margin">
            <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo empty($this->scormDetail->id)?JText::_('GURU_ADD_SCORM'):JText::_('GURU_EDIT_SCORM');?></h2></div>
            <div class="uk-width-1-2 uk-hidden-small uk-text-right uk-margin-top wk-width-1-2 wk-hidden-small wk-text-right wk-margin-top">
                <div class="uk-button-group wk-button-group">
                    <!-- This is the button toggling the dropdown -->
                    <button class="uk-button uk-button-success wk-button wk-button-success" onclick="saveScorm('apply');return false"><?php echo JText::_('GURU_SAVE'); ?></button>
                    <button class="uk-button uk-button-success wk-button wk-button-success" onclick="saveScorm('save');return false"><?php echo JText::_('GURU_SAVE_AND_CLOSE'); ?></button>
                    <button class="uk-button uk-button-danger wk-button wk-button-danger" onclick="closeScorm();return false"><?php echo JText::_('GURU_CLOSE'); ?></button>
                </div>
            </div>
            <form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="uk-form uk-form-horizontal wk-form wk-form-horizontal" autocomplete="off" style="width: 95%">
                <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_('GURU_TITLE')?>:
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <input type="text" id="title" name="title" value="<?php echo $this->scormDetail->title?>">
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SCORM_TITLE"); ?>">
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>

                <div class="uk-form-row wk-form-row">
	                <label class="uk-form-label wk-form-label" for="name">
	                    <?php echo JText::_('GURU_COURSE_NAMEF')?>:
	                    <span class="uk-text-danger wk-text-danger">*</span>
	                </label>
	                <div class="uk-form-controls wk-form-controls">
	                    <select class="uk-form-width-small wk-form-width-small" name="course_id" id="course_id" >
                        <option value="0" <?php if($this->scormDetail->course_id == "0"){echo 'selected="selected"';} ?>><?php echo JText::_("GURU_SELECT_COURSE");?></option>
                        <?php
                            if(!empty($this->my_courses)){
                                foreach($this->my_courses as $key=>$course){
                                    $selected = "";
                                    if($course["id"] == $this->scormDetail->course_id){
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="'.$course["id"].'" '.$selected.'>'.$course["name"].'</option>';
                                }
                            }
                        ?>
                        </select>
	                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SCORM_COURSE"); ?>">
	                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
	                    </span>
	                </div>
	            </div>
	            
	            <div class="uk-form-row wk-form-row">
	                <label class="uk-form-label wk-form-label" for="name">
	                    <?php echo JText::_('GURU_FILE')?>:
                        <span class="uk-text-danger wk-text-danger">*</span>
	                </label>
	                <div class="uk-form-controls wk-form-controls">
	                    <div style="float:left;">
                            <div id="fileUploader"></div>
                        </div> 
                        <?php
                            if(isset($this->scormDetail->file) && trim($this->scormDetail->file) != ""){
                        ?>
                                <div style="clear: both;"><?php echo $this->scormDetail->file; ?></div>
                        <?php
                            }
                        ?>
                        <input type="hidden" name="file" id="file" value="<?php echo $this->scormDetail->file; ?>" />
	                </div>
	            </div>

	            <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_('GURU_PUBL')?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls" id="show_correct_ans">
                        <input type="checkbox" name="published" value="1" <?php if($this->scormDetail->published == '1'){ echo 'checked';} ?> />
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SCORM_PUBLISHED"); ?>">
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>

	            <div class="uk-form-row wk-form-row">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_('GURU_PRODLSPUB')?>:
                    </label>
                    <div class="uk-form-controls wk-form-controls" id="show_correct_ans">
                    	<?php
                    		if(trim($this->scormDetail->start) == ""){
                    			$timezone = new DateTimeZone( JFactory::getConfig()->get('offset') );
								$jnow = new JDate('now');
								$jnow->setTimezone($timezone);
								$this->scormDetail->start = $jnow->toSQL(true);
                    		}
                    	?>
                        <?php echo JHTML::calendar($this->scormDetail->start, 'start', 'start', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); ?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SCORM_STARTDATE"); ?>">
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>

	            <div class="uk-form-row wk-form-row">
	                <label class="uk-form-label wk-form-label" for="name">
	                    <?php echo JText::_('GURU_PRODLEPUB')?>:
	                </label>
	                <div class="uk-form-controls wk-form-controls" id="show_correct_ans">
	                    <?php echo JHTML::calendar($this->scormDetail->end, 'end', 'end', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); ?>
	                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_SCORM_ENDTDATE"); ?>">
	                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
	                    </span>
	                </div>
	            </div>

	            <input type="hidden" name="scorm_id" value="<?php !empty($this->scormDetail->id)?'':$this->scormDetail->id?>">
	            <input type="hidden" name="task" value="saveScorm">
	            <input type="hidden" name="option" value="com_guru">
	            <input type="hidden" name="controller" value="guruScorm">
                <input type="hidden" name="action" id="action" value="apply">
                <input type="hidden" name="id" id="id" value="<?php echo $this->scormDetail->id?>">
                <?php echo JHTML::_('form.token'); ?>
        </form>
</div>
<script type="text/javascript">
	jQuery( document ).ready(function() {
        jQuery(".useredactor").redactor({
             buttons: ['bold', 'italic', 'underline', 'link', 'alignment', 'unorderedlist', 'orderedlist']
        });
        jQuery(".redactor_useredactor").css("height","400px");
    });

    /*window.addEvent( "domready", function(){
        jQuery(".useredactor").redactor({
             buttons: ['bold', 'italic', 'underline', 'link', 'alignment', 'unorderedlist', 'orderedlist']
        });
        jQuery(".redactor_useredactor").css("height","400px");
    });*/

    jQuery(function(){
        function createUploader(){
            var uploader = new qq.FileUploader({
                element: document.getElementById('fileUploader'),
                action: '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&tmpl=component&format=raw&task=upload_ajax_scorm',
                params:{
                    folder:'scorm',
                    mediaType:'scorm',
                    size: 500,
                    type: ''
                },
                onSubmit: function(id,fileName){
                    jQuery('.qq-upload-list li').css('display','none');
                },
                onComplete: function(id,fileName,responseJSON){
                    if(responseJSON.success == true){
                        jQuery('.qq-upload-success').append('- <span style="color:#387C44;"><?php echo JText::_('GURU_UPLOAD_SUCCESS')?></span>');
                        if(responseJSON.locate) {
                            jQuery('#view_imagelist23').attr("src", '<?php echo JURI::root()?>'+responseJSON.locate +'/'+ fileName+'?timestamp=' + new Date().getTime());
                            jQuery('#file').val(responseJSON.locate +'/'+ fileName);
                        }
                    }
                },
                allowedExtensions: ['zip', 'ZIP'],
                sizeLimit: '500M',
                multiple: false,
                maxConnections: 1
            });
        }
        createUploader();
    });

    function  closeScorm(){
        location.href='<?php echo JRoute::_('index.php?option=com_guru&view=guruauthor&task=scorm&layout=scorm',false)?>';
    }

    function saveScorm(act){
        jQuery('#action').val(act);
        if(jQuery('#course_id').val()==0){
            alert('<?php echo JText::_('GURU_ERR_SELECT_COURSE')?>');
            return false;
        }

        if(jQuery('#title').val()==''){
            alert('<?php echo JText::_('GURU_ERR_FILL_TITLE')?>');
            return false;
        }

        if(jQuery('#file').val()==''){
            alert('<?php echo JText::_('GURU_ERR_UPLOAD_FILE')?>');
            return false;
        }
        
        jQuery('#adminForm').submit();

    }
</script>