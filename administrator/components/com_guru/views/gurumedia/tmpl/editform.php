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

use Joomla\Registry\Registry;

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.tooltip');
JHtml::_('bootstrap.framework');
    $data = $this->data;
    $_row = $this->media;
    
    $lists = $_row->lists;
    $nullDate = 0;
    $livesite = JURI::base();   
    $configuration = guruAdminModelguruMedia::getConfig();  
    //$editorul  = JFactory::getEditor();
    $editorul  = new JEditor(JFactory::getConfig()->get("editor"));
    
    $UPLOAD_MAX_SIZE = guruAdminHelper::getUploadMaxSize();

    $maxUpload = "<font color='#FF0000'>";
    $maxUpload .= JText::_('GURU_MEDIA_MAX_UPL_V_1')." ";
    $maxUpload .= $UPLOAD_MAX_SIZE.'M ';
    $maxUpload .= JText::_('GURU_MEDIA_MAX_UPL_V_2');
    $maxUpload .= "</font>";
    
    
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'createUploader.php');
    ?>
    <?php //WE ARE NOT LONGER BEEN USING AJAX FROM prototype-1.6.0.2.js, INSTEAD WE WILL BE USING jQuery.ajax({}) function ?>
    <!-- <script type="text/javascript" src="<?php //echo JURI::base().'components/com_guru/views/gurutasks/tmpl/prototype-1.6.0.2.js'; ?>"></script> -->
    <script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/views/gurumedia/tmpl/js.js"></script>                
    <script language="javascript" type="text/javascript">
        function changefolder() {                               
            submitbutton('changes');
        }
        
        function isFloat(nr){
            return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
        }
        
        Joomla.submitbutton = function(pressbutton){
            var form = document.adminForm;

            if (pressbutton == 'save' || pressbutton == 'apply' || pressbutton == 'save2new') { 
                if (form['name'].value == "") {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_NAME_ERR");?>" );
                } 
                else if (form['type'].value == 0 || form['type'].value == "-" ) {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_TYPE_ERR");?>" ); 
                }
                else if (form['type'].value == 'image' && form['is_image'].value == 0) {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_IMAGE_ERR");?>" );
                }
                else if(form['author'].value == 'Select'){
                    alert( "<?php echo JText::_("GURU_CS_QAUTHOR");?>" );
                }
                else if (form['type'].value == 'text' && document.getElementById('textblock').value =='') {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_TEXT_ERR");?>" );
                }               
                else {
                    type = document.getElementById("type").value;
                    
                    if(type == "video"){
                        //width_v = document.getElementById("width_v").value;
                        height_v = document.getElementById("height_v").value;
                        
                        if(document.getElementById("option_video_size").checked == true){
                            /*if(!isFloat(width_v) || width_v <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                            else*/ if(!isFloat(height_v) || height_v <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                        }
                        
                        if(document.getElementById("source_code_v").checked == true){
                            if(form['code_v'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_VIDEO_CODE"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_url_v").checked == true){
                            if(form['url_v'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_VIDEO_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_local_v2").checked == true){
                            if(form['localfile'].value == ""){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                        else{
                            alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                            return false;
                        }
                    }
                    else if(type == "audio"){
                        width_a = document.getElementById("width_a").value;
                        if(document.getElementById("source_local_a2").checked == true){
                            if(!isFloat(width_a) || width_a <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                        }
                        
                        if(document.getElementById("source_code_a").checked == true){
                            if(form['code_a'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_AUDIO_CODE"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_url_a").checked == true){
                            if(form['url_a'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_AUDIO_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_local_a2").checked == true){
                            if(form['localfile_a'].value == ""){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                        else{
                            alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                            return false;
                        }
                    }
                    else if(type == "docs"){
                        width = document.getElementById("width_d").value;
                        height = document.getElementById("height_d").value;
                        link = document.getElementById("display_as").value;
                        
                        if(document.getElementById("source_local_d2").checked == true && link == 'wrapper'){
                            if(!isFloat(width) || width <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                            else if(!isFloat(height) || height <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                        }
                        
                        if(document.getElementById("source_url_d").checked == true){
                            if(form['url_d'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_DOC_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_local_d2").checked == true){
                            if(form['localfile_d'].value == ""){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                        else{
                            alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                            return false;
                        }
                    }
                    else if(type == "image"){
                        media_fullpx = document.getElementById("media_fullpx").value;
                        if(!isFloat(media_fullpx) || media_fullpx <= 0){
                            alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                            return false;
                        }
                    }
                    else if(type == "file"){
                        if(document.getElementById("source_url_f").checked == true){
                            if(document.getElementById("url_f").value == ""){
                                alert("<?php echo JText::_("GURU_ADD_FILE_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_local_f2").checked == true){
                            if(document.getElementById("localfile_f").value == "" || document.getElementById("localfile_f").value == "root"){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                        else{
                            alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                            return false;
                        }
                    }
                    
                    submitform( pressbutton );
                }
            }
            else{
                submitform( pressbutton );
            }
        }
        
        function SelectArticleg(id, title, object){ 
            document.getElementById('articleid').value = id;
            document.getElementById('article_name').value = title;
            //if joomla <= 3.8 means that it will include modal.js script witch generate sbox-window with modal
            if(document.getElementById('sbox-window')){
                window.parent.SqueezeBox.close();
            }
            //if joomla > 3.8 means that it will not include modal.js anymore and will use boostrap modal
            else{
                window.parent.jQuery('#GuruModal').modal('toggle');
            }  
            
        }
        </script>
<?php 
    $document = JFactory::getDocument();
    $document->addStyleSheet("components/com_guru/css/ytb.css"); 
?>
<form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <fieldset class="adminform">
        <div class="well"><?php if(isset($_row->id)) {echo  JText::_('GURU_MEDIADET_EDIT');} else{echo  JText::_('GURU_MEDIADET_NEW');} ?></div>

        <div class="grm-media-wrapper">
            <!-- FORM -->
            <div class="grm-media-block">
                <div class="grm-media-fieldset-row">
                    <label for="type"><?php echo JText::_('GURU_TYPE'); ?>: <span class="grm-required">*</span></label>
                    <div class="row-control">
                        <div class="grm-media-tooltip">
                            <?php 
                                echo $lists['type'];
                            ?>
                            <span title="<?php echo JText::_("GURU_TIP_TYPE"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grm-media-fieldset-row">
                    <label for="name">
                        <?php echo JText::_('GURU_NAME');?>: <span class="grm-required">*</span>
                    </label>

                    <div class="row-control">
                        <?php
                            $_row->name = str_replace('"', '&quot;', $_row->name);                  
                        ?>
                        <div class="grm-media-tooltip">
                            <input class="formField" type="text" name="name" id="name" size="60" value="<?php echo $_row->name; ?>">
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_NAME"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                        
                        <div class="grm-checkbox">
                            <input type="checkbox" value="1" name="hide_name" <?php if($_row->hide_name == 1){ echo 'checked="checked"'; } ?> />
                            <span class="lbl"><?php echo JText::_("LM_HIDE_NAME"); ?></span>
                        </div>
                    </div>
                </div>

                <div class="grm-media-fieldset-row">
                    <label for="category_id"><?php echo JText::_('GURU_CATEGORY');?>: <span class="grm-required">*</span></label>

                    <div class="row-control">
                        <div class="grm-media-tooltip">
                            <?php echo $this->parentCategory($_row->category_id);?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_CATEGORY"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grm-media-fieldset-row">
                    <label for="author"><?php echo JText::_('GURU_AUTHOR'); ?>: <span class="grm-required">*</span></label>

                    <div class="row-control">
                        <div class="grm-media-tooltip">
                            <?php echo $lists['author']; ?>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUTHOR"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grm-media-fieldset-row">
                    <label for="published"><?php echo JText::_('NEWADAPPROVED');?>:</label>

                    <div class="row-control">
                        <?php echo $lists['approved'];?>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_NEWADAPPROVED"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>

                <div class="grm-media-fieldset-row">
                    <label for="instructions"><?php echo JText::_('GURU_INSTR');?>:</label>

                    <div class="row-control">
                        <div class="grm-media-tooltip">
                            <textarea class="formField" type="text" name="instructions" rows="2" cols="60" ><?php echo stripslashes($_row->instructions); ?></textarea>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_INSTR"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grm-media-fieldset-row">
                    <label for="show_instruction"><?php echo JText::_('GURU_SHOW_INSTRUCTION');?>:</label>

                    <div class="row-control">
                        <select name="show_instruction" id="show_instruction">
                            <option value="0" <?php if($_row->show_instruction == "0"){echo 'selected="selected"'; } ?> ><?php echo JText::_("GURU_SHOW_ABOVE"); ?></option>
                            <option value="1" <?php if($_row->show_instruction == "1"){echo 'selected="selected"'; } ?> ><?php echo JText::_("GURU_SHOW_BELOW"); ?></option>
                            <option value="2" <?php if($_row->show_instruction == "2"){echo 'selected="selected"'; } ?> ><?php echo JText::_("GURU_DONT_SHOW"); ?></option>
                        </select>
                    </div>
                </div>

                <?php
                    if($_row->auto_play == NULL){
                        $_row->auto_play = "1";
                    }
                    $edit_play = 'style="display:none;"';
                    $edit_play_a = 'style="display:none;"';
                    if($_row->type=='video'){
                        $edit_play = 'style=""';
                    }
                    if($_row->type=='audio'){
                        $edit_play_a = 'style=""';
                    }
                ?>

                <div class="grm-media-fieldset-row" id="auto_play" <?php echo $edit_play; ?>>
                    <label><?php echo JText::_("GURU_AUTO_PLAY"); ?>:</label>

                    <div class="row-control">
                        <input type="hidden" name="auto_play" value="0">
                        <?php
                            $checked = '';
                            if($_row->auto_play == 1){
                                $checked = 'checked="checked"';
                            }
                        ?>
                        <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="auto_play">
                        <span class="lbl"></span>
                    </div>
                </div>

            </div>

            <!-- SOURCE -->
            <div class="grm-media-block">
                <div class="grm-media-block-box">
                    <?php
                        $edit_default = 'style=""';
                        $display_list_of_dir = $lists['video_dir'];
                        $display_list_of_files = $lists['video_url'];
                        $folder_of_files = $configuration->videoin;
                        $edit_video = 'style="display:none;"';

                        if($_row->type=='video'){
                            $edit_video = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                           //echo'<pre>';var_dump($_row->uploaded);echo'</pre>';die;
                    ?>
                    <!-- Video - media block -->
                    <div id="videoblock" <?php echo $edit_video; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEVIDEOS'); ?> <span class="grm-required">*</span></h4>

                        <div class="grm-media-fieldset-inline" id="code_of_file">
                            <label>
                                <input id="source_code_v" <?php if($_row->source=='code' && $_row->type=='video') echo 'checked="checked"';?> type="radio" value="code" name="source_v"/>
                                <span class="lbl">
                                    <?php echo JText::_('GURU_MEDIATYPEVIDEO').' '.JText::_('GURU_MEDIATYPECODE');?>
                                </span>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <textarea cols="35" rows="1" name="code_v" onKeyPress="javascript:change_radio_code()" onPaste="javascript:change_radio_code()"><?php echo ($_row->type=='video') ? stripslashes($_row->code) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <input id="source_url_v" type="radio" <?php if($_row->source=='url' && $_row->type=='video') echo 'checked="checked"';?> value="url" name="source_v" onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_('GURU_MEDIATYPEVIDEO').' '.JText::_('GURU_MEDIATYPEURLURL');?>
                                </span>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <div class="grm-media-fieldset-group">
                                    <span><input type="text" value="<?php echo ($_row->type=='video') ? $_row->url : ''; ?>" name="url_v" id="url_v" size="40" onPaste="javascript:change_radio_url()" onblur="javascript:addVideoFromUrl('<?php echo JURI::root(); ?>');" /></span>
                                    <span><input type="button" class="btn btn-success" value="<?php echo JText::_("COM_GURU_GET_VIDEO_NFO"); ?>" name="video-info" onclick="javascript:addVideoFromUrl('<?php echo JURI::root(); ?>');" /></span>
                                </div>
                                <div class="grm-helper grm-helper-text"><?php echo JText::_("COM_GURU_VIDEO_SUPPORTED"); ?></div>
                                <div style="margin-top: 5px">
                                    <span><img alt="YouTube" title="YouTube" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>youtube.png"></span>
                                    <span><img alt="Yahoo Video" title="Yahoo Video" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>yahoo.png"></span>
                                    <span><img alt="MySpace Video" title="MySpace Video" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>myspace.png"></span>
                                    <span><img alt="Flickr" title="Flickr" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>flickr.png"></span>
                                    <span><img alt="Vimeo" title="Vimeo" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>vimeo.png"></span>
                                    <span><img alt="Metacafe" title="Metacafe" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>metacafe.png"></span>
                                    <span><img alt="Blip.tv" title="Blip.tv" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>bliptv.png"></span>
                                    <span><img alt="Dailymotion" title="Dailymotion" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>dailymotion.png"></span>
                                    <span><img alt="Break" title="Break" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>break.png"></span>
                                    <span><img alt="Live Leak" title="Live Leak" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>liveleak.png"></span>
                                    <span><img alt="Viddler" title="Viddler" src="<?php echo JURI::root()."components/com_guru/images/video_support/"; ?>viddler.png"></span>
                                </div>

                                <div class="grm-media-progress" id="progress-video-upload" style="display:none; clear: both;">
                                    <div class="progress progress-success progress-striped">
                                        <div class="bar" style="width: 100%"></div>
                                    </div>
                                </div>

                                <div id="video_details" class="grm-media-video-details">
                                </div>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <input id="source_local_v2" type="radio" <?php if($_row->source=='local' && $_row->uploaded==1) echo 'checked="checked"';?> value="local" name="source_v"  onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_("GURU_LOCAL"); ?>
                                </span>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <div id="uploadblock">
                                    <div id="videoUploader"></div>
                                </div>

                                <div class="grm-media-upload">
                                    <span class="max-upload"><?php echo $maxUpload; ?></span>
                                    <span class="uploaded-file" id="to_hide_row_v" style="display:none"><?php echo JText::_('GURU_MEDIA_UPLOADED_FILE');?>:</span>

                                    <div><?php echo $_row->local;  ?></div>
                                </div>

                                <hr />

                                <div class="grm-media-file-list">
                                    <h5><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEVIDEO_');?>:</h5>
                                    <small class="grm-helper-text"><?php echo JText::_('GURU_MEDIA_UPLOADTO_V_1').' '.$folder_of_files.' '.JText::_('GURU_MEDIA_UPLOADTO_V_2'); ?></small>

                                    <?php
                                        echo $display_list_of_dir;

                                        if(isset($now_selected)&&($now_selected!='')) {echo str_replace($now_selected.'"',$now_selected.'" selected="selected"',$display_list_of_files); }//$lists['image_url'];
                                        else echo $display_list_of_files;
                                    ?>
                                </div>

                                <hr />

                                <div class="grm-media-player-size" id="player_size">
                                    <h5><?php echo JText::_('GURU_MEDIA_SIZE'); ?>:</h5>

                                    <?php                                   
                                        if($_row->option_video_size == NULL){
                                            $_row->option_video_size = "0";
                                        }
                                    ?>
                                    
                                    <div class="grm-checkbox">
                                        <input type="radio" name="option_video_size" value="0" <?php if($_row->option_video_size == "0"){echo 'checked="checked"';} ?>/>
                                        <span class="lbl">
                                            <?php 
                                                $default_size = $configuration->default_video_size;
                                                $default_zize_array = explode("x", $default_size);
                                                $dafault_size_height = $default_zize_array["0"];
                                                $dafault_size_width = $default_zize_array["1"];
                                                //echo JText::_("GURU_USE_GLOBAL")." (".$dafault_size_height." px x ".$dafault_size_width." px)"; 
												echo JText::_("GURU_USE_GLOBAL")." (".$dafault_size_height." px)";
                                            ?>
                                        </span>
                                    </div>

                                    <div class="grm-media-custom-size">
                                        <div class="grm-checkbox">
                                            <input type="radio" name="option_video_size" id="option_video_size" value="1" <?php if($_row->option_video_size == "1"){echo 'checked="checked"';} ?>/>
                                            <span class="lbl"></span>
                                        </div>

                                        <?php
                                            if($_row->id == "0"){
                                                $_row->height = "";
                                                $_row->width = "";
                                            }
                                        ?>

                                        <div class="block-control addon">
                                            <input type="text" id="height_v" size="5" value="<?php echo $_row->height;?>" name="height_v"/>
                                            <span>px</span>
                                        </div>
										<!--
                                        <div class="block-control separate">x</div>

                                        <div class="block-control addon">
                                            <input type="text" id="width_v" size="5" value="<?php echo $_row->width;?>" name="width_v"/>
                                            <span>px</span>
                                        </div>
										-->
                                        <div class="grm-helper grm-helper-text">
                                            <?php
                                                //echo " (".JText::_("GURU_HEIGHT")." x ".JText::_("GURU_WIDTH").")";
												echo " (".JText::_("GURU_HEIGHT").")";
                                            ?>
                                        </div>
                                    </div>

                                    <input type="hidden" id="video_size" name="video_size" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $display_list_of_dir = $lists['audio_dir'];
                        $display_list_of_files = $lists['audio_url'];
                        $folder_of_files = $configuration->audioin;
                        $edit_audio = 'style="display:none;"';

                        if($_row->type=='audio'){
                            $edit_audio = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                    ?>
                    <!-- Audio - media block -->
                    <div id="audioblock" <?php echo $edit_audio; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEAUDIOS'); ?> <span class="grm-required">*</span></h4>

                        <div class="grm-media-fieldset-inline" id="code_of_file">
                            <label>
                                <input id="source_code_a" <?php if($_row->source=='code' && $_row->type=='audio') echo 'checked="checked"';?> type="radio" value="code" name="source_a"/>
                                <span class="lbl">
                                    <?php echo JText::_('GURU_MEDIATYPEAUDIO').' '.JText::_('GURU_MEDIATYPECODE');?>
                                </span>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <textarea cols="35" rows="1" name="code_a" onKeyPress="javascript:change_radio_code()" onPaste="javascript:change_radio_code()"><?php echo ($_row->type=='audio') ? stripslashes($_row->code) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <input id="source_url_a" type="radio" <?php if($_row->source=='url' && $_row->type=='audio') echo 'checked="checked"';?> value="url" name="source_a" onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_('GURU_MEDIATYPEAUDIO').' '.JText::_('GURU_MEDIATYPEURLURL');?>
                                </span>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <input type="text" onKeyPress="javascript:change_radio_url()" onPaste="javascript:change_radio_url()" size="40" value="<?php echo ($_row->type=='audio') ? $_row->url : '';?>" name="url_a"  onChange="javascript:hide_hidden_row();"/>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                            	<input id="source_local_a2" type="radio" <?php if($_row->source=='local' && $_row->uploaded==1) echo 'checked="checked"';?> value="local" name="source_a"  onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_("GURU_LOCAL"); ?>
                                </span>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <div id="uploadblock">
                                    <div id="audioUploader"></div>
                                </div>

                                <div class="grm-media-upload">
                                    <span class="max-upload"><?php echo $maxUpload; ?></span>
                                    <span class="uploaded-file" id="to_hide_row_a" style="display:none"><?php echo JText::_('GURU_MEDIA_UPLOADED_FILE');?>:</span>

                                    <div><?php echo $_row->local;  ?></div>
                                </div>

                                <hr />

                                <div class="grm-media-file-list">
                                    <h5><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEAUDIO_');?>:</h5>
                                    <small class="grm-helper-text"><?php echo JText::_('GURU_MEDIA_UPLOADTO_V_1').' '.$folder_of_files.' '.JText::_('GURU_MEDIA_UPLOADTO_V_2'); ?></small>

                                    <?php
                                        echo $display_list_of_dir;

                                        $now_selected = guruAdminModelguruMedia::now_selected_media ($_row->id);
                                        if (isset($now_selected)) { echo $now_selected; }

                                        echo $display_list_of_files;
                                    ?>
                                </div>

                                <hr />

                                <div class="grm-media-player-size" id="player_size">
                                    <h5><?php echo JText::_('GURU_MEDIA_SIZE'); ?>:</h5>

                                    <?php
                                            $media_size_val = "";
                                            if( isset($this->audio_set) && (( $this->audio_set==1) || (isset($_row->id) && ($_row->id>0)))){
                                                if($this->audio_set==0){
                                                    $media_size_val = $_row->width;
                                                }
                                                else{
                                                    $media_size_val = "250";
                                                }
                                            }
                                            else{
                                                $media_size_val = "250";
                                            }
                                    ?>

                                    <div class="grm-media-custom-size">
                                        <div class="block-control addon">
                                            <input type="text" size="10" value="<?php echo $media_size_val; ?>" name="width_a" id="width_a" />
                                            <span>px</span>
                                        </div>

                                        <div class="grm-helper grm-helper-text"><?php echo JText::_('GURU_MEDIA_WIDTH'); ?></div>

                                        <input type="hidden" size="10" value="50" name="height_a"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $display_list_of_dir = $lists['docs_dir'];
                        $display_list_of_files = $lists['docs_url'];
                        $folder_of_files = $configuration->docsin;
                        $edit_docs = 'style="display:none;"';   
                        if($_row->type=='docs'){
                            $edit_docs = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                    ?>
                    <!-- Document - media block -->
                    <div id="docsblock" <?php echo $edit_docs; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEDOCSS'); ?> <span class="grm-required">*</span></h4>

                        <div class="grm-media-fieldset-inline" id="code_of_file">
                            <label>
                                <input id="source_url_d" type="radio" <?php if($_row->source=='url') echo 'checked="checked"';?> value="url" name="source_d" onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_('GURU_MEDIATYPEDOCS').' '.JText::_('GURU_MEDIATYPEURLURL');?>
                                </span>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <input type="text" onKeyPress="javascript:change_radio_url()" onPaste="javascript:change_radio_url()" size="40" value="<?php echo $_row->url;?>" name="url_d"  onChange="javascript:hide_hidden_row();"/>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <input id="source_local_d2" type="radio" <?php if($_row->source=='local') echo 'checked="checked"';?> value="local" name="source_d"  onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_("GURU_LOCAL"); ?>
                                </span>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <div id="uploadblock">
                                    <div id="docUploader"></div>
                                </div>

                                <div class="grm-media-upload">
                                    <span class="max-upload"><?php echo $maxUpload; ?></span>
                                    <span class="uploaded-file" id="to_hide_row_d" style="display:none"><?php echo JText::_('GURU_MEDIA_UPLOADED_FILE');?>:</span>

                                    <div><?php echo $_row->local;  ?></div>
                                </div>

                                <hr />

                                <div class="grm-media-file-list">
                                    <h5><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEDOCS_');?>:</h5>
                                    <small class="grm-helper-text"><?php echo JText::_('GURU_MEDIA_UPLOADTO_V_1').' '.$folder_of_files.' '.JText::_('GURU_MEDIA_UPLOADTO_V_2'); ?></small>

                                    <?php
                                        echo $display_list_of_dir;

                                        $now_selected = guruAdminModelguruMedia::now_selected_media ($_row->id);
                                        if (isset($now_selected)) { echo $now_selected; }

                                        echo $display_list_of_files;
                                        //echo $lists['image_url'];
                                    ?>
                                </div>

                                <hr />

                                <div class="grm-media-player-size" id="player_size" <?php //echo $code_of_file;?>>
                                    <h5><?php echo JText::_('GURU_MEDIA_DISPL_DOC'); ?>:</h5>

                                    <script type="text/javascript">
                                        function wh(y){
                                            if(y==1){
                                                document.getElementById('whdoc').style.display='';
                                            } 
                                            if (y==0) {
                                                document.getElementById('whdoc').style.display='none';
                                            }   
                                        }
                                    </script>

                                    <select id="display_as" name="display_as">
                                        <option value="wrapper" onclick="javascript:wh(1)"><?php echo JText::_('GURU_MEDIA_DISPL_DOC_W'); ?></option>
                                        <option value="link" onclick="javascript:wh(0)" <?php if($_row->type=='docs' && $_row->width==1) {echo 'selected = "selected"'; $sel_link=1;}?>><?php echo JText::_('GURU_MEDIA_DISPL_DOC_L'); ?></option>
                                    </select>
                                </div>

                                <hr />

                                <div class="grm-media-player-size" id="whdoc" <?php if(isset($sel_link)){ echo 'style="display:none;"';}?>>
                                    <h5><?php echo JText::_('GURU_MEDIA_SIZE'); ?>:</h5>

                                    <div class="grm-media-custom-size">
                                        <div class="block-control addon">
                                            <input type="text" size="10" value="<?php if($_row->width>99){echo $_row->width;}else {echo "600";} ?>" name="width" id="width_d"/>
                                            <span>px</span>
                                        </div>

                                        <div class="block-control separate">x</div>

                                        <div class="block-control addon">
                                            <input type="text" size="10" value="<?php if($_row->height>99){echo $_row->height;}else {echo "800";}?>" name="height" id="height_d"/>
                                            <span>px</span>
                                        </div>

                                        <div class="grm-helper grm-helper-text"><?php echo JText::_('GURU_MEDIA_WIDTH_HEIGHT'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $edit_url = 'style="display:none;"';
                        if($_row->type=='url'){
                            $edit_url = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                    ?>

                    <!-- URL - media block -->
                    <div id="urlblock" <?php echo $edit_url; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEURL_');?> <span class="grm-required">*</span></h4>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <?php echo JText::_('GURU_MEDIATYPEURL_');?>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <div class="grm-media-tooltip">
                                    <input type="text" size="80" value="<?php if (isset($_row->url) && $_row->url !="" ){echo $_row->url;}else{ echo "http://";}?>" name="url"/>
                                    <span title="<?php echo JText::_("GURU_TIP_MEDIATYPEURL_"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="grm-helper grm-required"><?php echo JText::_("GURU_ENTER_FULL_URL"); ?> http://ijoomla.com</div>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <?php echo JText::_('GURU_MEDIA_DISPL_DOC'); ?>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <select name="display_as2" >
                                    <option value="wrapper"><?php echo JText::_('GURU_MEDIA_DISPL_DOC_W'); ?></option>
                                    <option value="link" <?php if($_row->type=='url' && $_row->width==1) echo 'selected = "selected"'; ?>><?php echo JText::_('GURU_MEDIA_DISPL_DOC_L'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <?php echo JText::_('GURU_IFRAME_SIZE'); ?>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <?php
                                    if($_row->type == "url"){
                                        if($_row->width == "" || $_row->width == "0"){
                                            $_row->width = "800";
                                        }
                                        
                                        if($_row->height == "" || $_row->height == "0"){
                                            $_row->height = "600";
                                        }
                                    }
                                ?>
                                <div class="grm-media-custom-size">
                                    <div class="block-control addon">
                                        <input type="text" style="width:50px;" value="<?php echo $_row->width; ?>" name="width_u" id="width"/>
                                        <span>px</span>
                                    </div>

                                    <div class="block-control separate">x</div>

                                    <div class="block-control addon">
                                        <input type="text" style="width:50px;" value="<?php echo $_row->height; ?>" name="height_u" id="height"/>
                                        <span>px</span>
                                    </div>
                                    <div class="grm-helper">  
                                        <span title="<?php echo JText::_("GURU_TIP_IFRAME_SIZE"); ?>" >
                                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $edit_art = 'style="display:none;"';
                        if($_row->type=='Article'){
                            $edit_art = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                    ?>
                    <!-- Article - media block -->
                    <div id="artblock" <?php echo $edit_art; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEARTICLE_');?> <span class="grm-required">*</span></h4>

                        <?php
                            $headData = $doc->getHeadData();
                            $scripts = $headData['scripts'];
                            unset($scripts['/media/system/js/tabs.js']);
                            $headData['scripts'] = $scripts;
                            $doc->setHeadData($headData);
                           
                            unset($this->_scripts['/media/system/js/tabs.js']);
                            if($_row->id !=""){
                                $db = JFactory::getDBO();
                                $sql = "SELECT code FROM `#__guru_media` WHERE type='Article' and id=".$_row->id;
                                $db->setQuery($sql);
                                $guru_articleid = $db->loadColumn();
                             } 
                            if(@$code !=0){
                            $sql = "SELECT title FROM `#__content` WHERE id=".$guru_articleid;
                            $db->setQuery($sql);
                            $guru_article_name = $db->loadColumn();
                            }   
                        ?>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <?php echo JText::_('GURU_MEDIATYPEARTICLE_');?>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <?php echo $this->displayArticleguru(@$guru_articleid[0], @$guru_article_name[0]); ?>
                                <div class="grm-helper grm-helper-text"><?php echo JText::_("GURU_TIP_MEDIATYPEARTICLE_"); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php
                        $edit_image = 'style="display:none;"';
                        if($_row->type=='image'){
                            $edit_image = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                    ?>
                    <!-- Image - media block -->
                    <div id="imageblock" <?php echo $edit_image; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEIMAGE');?> <span class="grm-required">*</span></h4>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <?php echo JText::_('GURU_MEDIATYPEIMAGE');?> <span class="grm-required">*</span>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <div id="imageUploader"></div>
                                <input type="hidden" name="images" id="images" value="<?php echo $_row->local; ?>" />

                                <div class="grm-media-upload">
                                    <span class="max-upload"><?php echo $maxUpload; ?></span>
                                    <span class="uploaded-file" id="to_hide_row_d" style="display:none"><?php echo JText::_('GURU_MEDIA_UPLOADED_FILE');?>:</span>

                                    <div><?php echo $_row->local;  ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label><?php  echo JText::_('GURU_GEN_IM_FIS');?>:</label>

                            <?php 
                            $media_fullpx = 200;
                            $media_prop = 'w';
                            $is_image = 0;
                            if($_row->width>0 && $_row->height == 0)
                                {
                                    $media_fullpx = $_row->width;
                                    $media_prop = 'w';
                                    $is_image = 1;
                                }   
                            if($_row->height >0 && $_row->width == 0)
                                {
                                    $media_fullpx = $_row->height;                  
                                    $media_prop = 'h';
                                    $is_image = 1;
                                }   
                            ?>
                            <div class="grm-media-fieldset-control">
                                <div class="grm-media-custom-size">
                                    <div class="block-control addon">   
                                        <input type="text" size="8" id="media_fullpx" name="media_fullpx" value="<?php echo $media_fullpx;?>" />
                                        <span>px</span>
                                    </div>
                                    <div class="block-control separate">&nbsp;</div>
                                    <div class="block-control">
                                        <select name="media_prop" id="media_prop">
                                            <option value="w" <?php if($media_prop=='w') echo 'selected="selected"'; ?>><?php  echo JText::_('GURU_PROPW');?></option>
                                            <option value="h" <?php if($media_prop=='h') echo 'selected="selected"'; ?>><?php  echo JText::_('GURU_PROPH');?></option>
                                        </select>   
                                    </div>
                                </div>
                                <input type="hidden" id="is_image" name="is_image" value="<?php echo $is_image;?>" />  
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label><?php echo JText::_('GURU_PRODCIMG');?>:</label>

                            <div class="grm-media-fieldset-control">
                                <?php 
                                    if(trim($_row->local)!=""){
                                        $media_image = '<img id="view_imagelist23" name="view_imagelist" style="margin:5px;" border="0" alt="" src="'.JURI::root().$configuration->imagesin."/media/thumbs".$_row->local.'" />';
                                    }
                                    else
                                        $media_image = '<img id="view_imagelist23" name="view_imagelist" style="margin:5px;" border="0" alt="" src="'.JUri::base().'/components/com_guru/images/blank.png" />';
                                    // generating thumb image - stop                
                                ?>

                                <div class="grm-media-image-wrapper"><?php echo $media_image; ?></div>
                                <input type="hidden" id="image" name="image" value="<?php echo $_row->local;?>" />
                            </div>
                        </div>
                    </div>

                    <?php
                        $edit_text = 'style="display:none;"';
                        if($_row->type=='text'){
                            $edit_text = 'style=""';
                        }   
                    ?>
                    <!-- Text - media block -->
                    <div id="textblock" <?php echo $edit_text; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPETEXT');?> <span class="grm-required">*</span></h4>

                        <?php 
                            echo $editorul->display( 'text', ''.$_row->code,'100%', '300px', '20', '60' );          
                        ?>
                    </div>

                    <?php
                        $display_list_of_dir = $lists['files_dir'];
                        $display_list_of_files = $lists['files_url'];
                        $edit_file = 'style="display:none;"';
                        if($_row->type=='file'){
                            $edit_file = 'style=""';
                        }
                    ?>
                    <!-- File - media block -->
                    <div id="fileblock" <?php echo $edit_file; ?>>
                        <h4 class="grm-media-block-title"><?php echo JText::_('GURU_MEDIATYPEDOCS');?>: <span class="grm-required">*</span></h4>

                        <div class="grm-media-fieldset-inline" id="code_of_file">
                            <label>
                                <input id="source_url_f" type="radio" <?php if($_row->source=='url') echo 'checked="checked"';?> value="url" name="source_f" onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_('GURU_MEDIATYPEDOCS').' '.JText::_('GURU_MEDIATYPEURLURL');?>
                                </span>
                            </label>
                            <div class="grm-media-fieldset-control">
                                <input type="text" onKeyPress="javascript:change_radio_url()" onPaste="javascript:change_radio_url()" size="40" value="<?php echo $_row->url;?>" id="url_f" name="url_f"  onChange="javascript:hide_hidden_row();" onmouseout="doPreview();" on/>
                                <?php 
                                    if($_row->source=="url" && $_row->url!=""){
                                ?>
                                        <div id="filePreview" class="grm-helper">
                                            <a class="a_guru" href="<?php echo $_row->url; ?>"><?php echo JText::_("GURU_PREVIEW"); ?></a>
                                        </div>
                                <?php   
                                    } 
                                ?>
                                <div id="filePreview"></div>
                            </div>
                        </div>

                        <div class="grm-media-fieldset-inline">
                            <label>
                                <input id="source_local_f2" type="radio" <?php if($_row->source=='local') echo 'checked="checked"';?> value="local" name="source_f"  onChange="javascript:hide_hidden_row();"/>
                                <span class="lbl">
                                    <?php echo JText::_("GURU_LOCAL"); ?>
                                </span>
                            </label>

                            <div class="grm-media-fieldset-control">
                                <div id="uploadblock">
                                    <div id="fileUploader"></div>
                                </div>

                                <div class="grm-media-upload">
                                    <span class="max-upload"><?php echo $maxUpload; ?></span>
                                </div>

                                <hr />

                                <div class="grm-media-file-list">
                                    <h5><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEDOCS_');?>:</h5>
                                    <small class="grm-helper-text"><?php echo JText::_('GURU_MEDIA_UPLOADTO_V_1').' '.$folder_of_files.JText::_('GURU_MEDIA_UPLOADTO_V_2'); ?></small>

                                    <?php
                                        echo $display_list_of_dir;

                                        $now_selected = guruAdminModelguruMedia::now_selected_media ($_row->id);
                                        if (isset($now_selected)) { 
                                            echo $now_selected; 
                                        }

                                        echo $display_list_of_files;
                                    ?>

                                    <div id="filesFolder"><?php echo JURI::root().$configuration->filesin; ?></div>

                                    <?php 
                                    if($_row->source=="local" && $_row->local!=""){
                                    ?>
                                        <a class="a_guru" href="<?php echo JURI::root().$configuration->filesin."/".$_row->local; ?>" id="filePreviewList"><?php echo JText::_("GURU_PREVIEW"); ?></a>
                                    <?php 
                                    }else{ ?>
                                        <a class="a_guru" href="#" style="visibility:hidden" id="filePreviewList"><?php echo JText::_("GURU_PREVIEW"); ?></a>   
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zoom Meeting - media block -->
                    <?php
                        $meetingData = new JRegistry;
                        $edit_zoom = 'style="display:none;"';
                        if($_row->type=='zoom') {
                            $meetingData = new Registry($_row->code);
                            $edit_zoom = 'style=""';
                            $edit_default = 'style="display:none;"';
                        }
                    ?>
                    <div id="zoomblock" <?php echo $edit_zoom ?>>
                        <h3 class="grm-media-block-title">
                            <?php echo JText::_('GURU_MEDIA_ZOOM_MEETING'); ?>
                            <span class="grm-required">*</span>
                        </h3>
                        <div class="grm-media-fieldset-inline">
                            <label><?php echo JText::_('GURU_MEDIA_ZOOM_MEETING_ID') ?> <span class="grm-required">*</span></label>
                            <div class="grm-media-fieldset-control">
                                <div class="grm-media-tooltip">
                                    <input type="text" value="<?php echo $meetingData->get('meeting_id') ?>" placeholder="" name="meeting_id_z">
                                </div>
                            </div>
                        </div>
                        <div class="grm-media-fieldset-inline">
                            <label><?php echo JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD') ?> <span class="grm-required">*</span></label>
                            <div class="grm-media-fieldset-control">
                                <div class="grm-media-tooltip">
                                    <input type="text" value="<?php echo $meetingData->get('meeting_pass') ?>" name="meeting_password_z">
                                </div>
                            </div>
                        </div>
                        <div class="grm-media-fieldset-inline">
                            <label><?php echo JText::_('GURU_MEDIA_ZOOM_MEETING_INVITE_LINK') ?> <span class="grm-required">*</span></label>
                            <div class="grm-media-fieldset-control">
                                <div class="grm-media-tooltip">
                                    <input type="text" value="<?php echo $meetingData->get('invite_link') ?>" placeholder="https://" name="invite_link_z">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="defaultblock" <?php echo $edit_default; ?>>
                        <h3 class="grm-media-block-title">
                            <?php echo JText::_('GURU_MEDIA_SOURCE'); ?>
                        </h3>
                        <span class="grm-helper-text"><?php echo JText::_('GURU_MEDIA_TYPE'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="controller" value="guruMedia" />
    <input type="hidden" name="was_uploaded" id="was_uploaded" value="1" />  
</form>   
