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
defined ('_JEXEC') or die ("Go away.");
JHTML::_('behavior.tooltip');

    $data = $this->data;
    $_row = $this->media;
    $lists = @$_row->lists;
    $doc =JFactory::getDocument();
    $doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_MEDIA')));
    $nullDate = 0;
    $livesite = JURI::base();   
    $configuration = $this->getConfigsObject(); 
    $editorul = new JEditor(JFactory::getConfig()->get("editor"));

    $UPLOAD_MAX_SIZE = guruHelper::getUploadMaxSize();
    
    $maxUpload = "<font color='#FF0000'>";
    $maxUpload .= JText::_('GURU_MEDIA_MAX_UPL_V_1')." ";
    $maxUpload .= $UPLOAD_MAX_SIZE.'M ';
    $maxUpload .= "</font>";
    
    $div_menu = $this->authorGuruMenuBar();
    
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_guru'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'createUploader.php');
    ?>
    
<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>
    
    <style type="text/css">
        /* modal
        -------------------------*/
        .modal{
            left: 50px;
            margin: 0 !important;
            padding-top: 20px;
            right: 50px;
            top: 10%;
            width: auto;
            border:none !important;
            border-radius: 0px !important;
            box-shadow: none !important;
            position:inherit !important;
        }
        
    </style>
    
    <script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/views/guruauthor/tmpl/js/js.js"></script>                
    <script language="javascript" type="text/javascript">
        function changefolder() {                               
            submitbutton('changes');
        }
        
        function isFloat(nr){
            return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;
        }
        
        Joomla.submitbutton = function(pressbutton){
            var form = document.adminForm;
            if (pressbutton == 'save_media' || pressbutton == 'apply_media') { 
                if (form['name'].value == "") {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_NAME_ERR");?>" );
                } 
                else if (form['type'].value == 0 || form['type'].value == "-" ) {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_TYPE_ERR");?>" ); 
                }
                else if (form['type'].value == 'image' && form['is_image'].value == 0) {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_IMAGE_ERR");?>" );
                }
                else if (form['type'].value == 'text' && document.getElementById('textblock').value =='') {
                    alert( "<?php echo JText::_("GURU_MEDIA_JS_TEXT_ERR");?>" );
                }               
                else {
                    type = document.getElementById("type").value;
                    
                    if(type == "video"){
                        if(document.getElementById("source_v").value == 'code'){
                            if(form['code_v'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_VIDEO_CODE"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_v").value == 'url'){
                            if(form['url_v'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_VIDEO_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_v").value == 'local'){
                            if(form['localfile'].value == ""){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                        
                    }
                    else if(type == "audio"){
                        width_a = document.getElementById("width_a").value;
                        if(document.getElementById("source_a").value == 'local'){
                            if(!isFloat(width_a) || width_a <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                        }
                        
                        if(document.getElementById("source_a").value == 'code'){
                            if(form['code_a'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_AUDIO_CODE"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_a").value == 'url'){
                            if(form['url_a'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_AUDIO_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_a").value == 'local'){
                            if(form['localfile_a'].value == ""){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                    }
                    else if(type == "docs"){
                        width = document.getElementById("width").value;
                        height = document.getElementById("height").value;
                        link = document.getElementById("display_as").value;
                        
                        if(document.getElementById("source_d").value == 'local' && link == 'wrapper'){
                            if(!isFloat(width) || width <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                            else if(!isFloat(height) || height <= 0){
                                alert("<?php echo JText::_("GURU_MEDIAAPPLYFAILED").JText::_("GURU_MEDIASAVEDSIZE"); ?>");
                                return false;
                            }
                        }
                        
                        if(document.getElementById("source_d").value == 'url'){
                            if(form['url_d'].value == ""){
                                alert("<?php echo JText::_("GURU_ADD_DOC_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_d").value == 'local'){
                            if(form['localfile_d'].value == ""){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
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
                        if(document.getElementById("source_f").value == 'url'){
                            if(document.getElementById("url_f").value == ""){
                                alert("<?php echo JText::_("GURU_ADD_FILE_URL"); ?>");
                                return false;
                            }
                        }
                        else if(document.getElementById("source_f").value == 'local'){
                            if(document.getElementById("localfile_f").value == "" || document.getElementById("localfile_f").value == "root"){
                                alert("<?php echo JText::_("GURU_SELECT_FILE"); ?>");
                                return false;
                            }
                        }
                    }
                    
                    //submitform( pressbutton );
                    form.task.value = pressbutton;
                    form.submit();
                }
            }
            else{
                //submitform( pressbutton );
                form.task.value = pressbutton;
                form.submit();
            }
        }
        
        function SelectArticleg(id, title, object){ 
            document.getElementById('articleid').value = id;
            document.getElementById('article_name').value = title;  
            window.parent.SqueezeBox.close();
        }
        
        function makeActiveV(value){
            if(value == 'embeded'){
                /*document.getElementById("embeded").className ="tab-pane active";
                document.getElementById("addfromurl").className ="tab-pane";
                document.getElementById("upload").className ="tab-pane";
                document.getElementById("selectfile").className ="tab-pane";
                document.getElementById("li_embeded").className ="active";
                document.getElementById("li_addfromurl").className ="";
                document.getElementById("li_upload").className ="";
                document.getElementById("li_selectfile").className ="";*/
                document.getElementById("source_v").value = "code";
                document.getElementById("uploaded_tab").value = "-1";

            }
            if(value == 'addfromurl'){
                /*document.getElementById("embeded").className ="tab-pane";
                document.getElementById("addfromurl").className ="tab-pane active";
                document.getElementById("upload").className ="tab-pane";
                document.getElementById("selectfile").className ="tab-pane";
                document.getElementById("li_embeded").className ="";
                document.getElementById("li_addfromurl").className ="active";
                document.getElementById("li_upload").className ="";
                document.getElementById("li_selectfile").className ="";*/
                document.getElementById("source_v").value = "url";
                document.getElementById("uploaded_tab").value = "-1";
            }
            if(value == 'upload'){
                /*document.getElementById("embeded").className ="tab-pane";
                document.getElementById("addfromurl").className ="tab-pane";
                document.getElementById("upload").className ="tab-pane active";
                document.getElementById("selectfile").className ="tab-pane";
                document.getElementById("li_embeded").className ="";
                document.getElementById("li_addfromurl").className ="";
                document.getElementById("li_upload").className ="active";
                document.getElementById("li_selectfile").className ="";*/
                document.getElementById("source_v").value = "";
                document.getElementById("uploaded_tab").value = "0";
            }
            if(value == 'selectfile'){
                /*document.getElementById("embeded").className ="tab-pane";
                document.getElementById("addfromurl").className ="tab-pane";
                document.getElementById("upload").className ="tab-pane";
                document.getElementById("selectfile").className ="tab-pane active";
                document.getElementById("li_embeded").className ="";
                document.getElementById("li_addfromurl").className ="";
                document.getElementById("li_upload").className ="";
                document.getElementById("li_selectfile").className ="active";*/
                document.getElementById("source_v").value = "";
                document.getElementById("uploaded_tab").value = "1";
            }
        }
        function makeActiveA(value){
            if(value == 'embeded'){
                /*document.getElementById("embeded_a").className ="tab-pane active";
                document.getElementById("addfromurl_a").className ="tab-pane";
                document.getElementById("upload_a").className ="tab-pane";
                document.getElementById("selectfile_a").className ="tab-pane";
                document.getElementById("lia_embeded").className ="active";
                document.getElementById("lia_addfromurl").className ="";
                document.getElementById("lia_upload").className ="";
                document.getElementById("lia_selectfile").className ="";*/
                document.getElementById("source_a").value = "code";
                document.getElementById("uploaded_tab").value = "-1";

            }
            if(value == 'addfromurl'){
                /*document.getElementById("embeded_a").className ="tab-pane";
                document.getElementById("addfromurl_a").className ="tab-pane active";
                document.getElementById("upload_a").className ="tab-pane";
                document.getElementById("selectfile_a").className ="tab-pane";
                document.getElementById("lia_embeded").className ="";
                document.getElementById("lia_addfromurl").className ="active";
                document.getElementById("lia_upload").className ="";
                document.getElementById("lia_selectfile").className ="";*/
                document.getElementById("source_a").value = "url";
                document.getElementById("uploaded_tab").value = "-1";
            }
            if(value == 'upload'){
                /*document.getElementById("embeded_a").className ="tab-pane";
                document.getElementById("addfromurl_a").className ="tab-pane";
                document.getElementById("upload_a").className ="tab-pane active";
                document.getElementById("selectfile_a").className ="tab-pane";
                document.getElementById("lia_embeded").className ="";
                document.getElementById("lia_addfromurl").className ="";
                document.getElementById("lia_upload").className ="active";
                document.getElementById("lia_selectfile").className ="";*/
                document.getElementById("source_a").value = "";
                document.getElementById("uploaded_tab").value = "0";
            }
            if(value == 'selectfile'){
                /*document.getElementById("embeded_a").className ="tab-pane";
                document.getElementById("addfromurl_a").className ="tab-pane";
                document.getElementById("upload_a").className ="tab-pane";
                document.getElementById("selectfile_a").className ="tab-pane active";
                document.getElementById("lia_embeded").className ="";
                document.getElementById("lia_addfromurl").className ="";
                document.getElementById("lia_upload").className ="";
                document.getElementById("lia_selectfile").className ="active";*/
                document.getElementById("source_a").value = "";
                document.getElementById("uploaded_tab").value = "1";
            }
        }       
        function makeActiveD(value){

            if(value == 'addfromurl'){
                /*document.getElementById("addfromurl_d").className ="tab-pane active";
                document.getElementById("upload_d").className ="tab-pane";
                document.getElementById("selectfile_d").className ="tab-pane";
                document.getElementById("lid_addfromurl").className ="active";
                document.getElementById("lid_upload").className ="";
                document.getElementById("lid_selectfile").className ="";*/
                document.getElementById("source_d").value = "url";
                document.getElementById("uploaded_tab").value = "-1";
            }
            if(value == 'upload'){
                /*document.getElementById("addfromurl_d").className ="tab-pane";
                document.getElementById("upload_d").className ="tab-pane active";
                document.getElementById("selectfile_d").className ="tab-pane";
                document.getElementById("lid_addfromurl").className ="";
                document.getElementById("lid_upload").className ="active";
                document.getElementById("lid_selectfile").className ="";*/
                document.getElementById("source_d").value = "";
                document.getElementById("uploaded_tab").value = "0";
            }
            if(value == 'selectfile'){
                /*document.getElementById("addfromurl_d").className ="tab-pane";
                document.getElementById("upload_d").className ="tab-pane";
                document.getElementById("selectfile_d").className ="tab-pane active";
                document.getElementById("lid_addfromurl").className ="";
                document.getElementById("lid_upload").className ="";
                document.getElementById("lid_selectfile").className ="active";*/
                document.getElementById("source_d").value = "";
                document.getElementById("uploaded_tab").value = "1";
            }
        }
        function makeActiveF(value){
            if(value == 'addfromurl'){
                /*document.getElementById("addfromurl_f").className ="tab-pane active";
                document.getElementById("upload_f").className ="tab-pane";
                document.getElementById("selectfile_f").className ="tab-pane";
                document.getElementById("lif_addfromurl").className ="active";
                document.getElementById("lif_upload").className ="";
                document.getElementById("lif_selectfile").className ="";*/
                document.getElementById("source_f").value = "url";
                document.getElementById("uploaded_tab").value = "-1";
            }
            if(value == 'upload'){
                /*document.getElementById("addfromurl_f").className ="tab-pane";
                document.getElementById("upload_f").className ="tab-pane active";
                document.getElementById("selectfile_f").className ="tab-pane";
                document.getElementById("lif_addfromurl").className ="";
                document.getElementById("lif_upload").className ="active";
                document.getElementById("lif_selectfile").className ="";*/
                document.getElementById("source_f").value = "";
                document.getElementById("uploaded_tab").value = "0";
            }
            if(value == 'selectfile'){
                /*document.getElementById("addfromurl_f").className ="tab-pane";
                document.getElementById("upload_f").className ="tab-pane";
                document.getElementById("selectfile_f").className ="tab-pane active";
                document.getElementById("lif_addfromurl").className ="";
                document.getElementById("lif_upload").className ="";
                document.getElementById("lif_selectfile").className ="active";*/
                document.getElementById("source_f").value = "";
                document.getElementById("uploaded_tab").value = "1";
            }
        }

        function changeTab(tabid){
            jQuery(".header-tab").each(function(){
                jQuery(this).removeClass("uk-active");
            });

            jQuery(".content-tab").each(function(){
                jQuery(this).removeClass("uk-active");
            });

            jQuery("#"+tabid).addClass("uk-active");
            jQuery("#"+tabid+"-content").addClass("uk-active");
        }
</script>

<?php
    $tmpl = JFactory::getApplication()->input->get("tmpl", "");
    if($tmpl == "component"){
?>        
        <style type="text/css">
            .redactor_box {
                width:100%;
            }
            
            .component{
                background:transparent;
            }
        </style>
<?php
    }
?>
        

<div id="g_mymedia_addedit" class="gru-mediapage">        
<?php 
    $document = JFactory::getDocument();
    $tmpl = JFactory::getApplication()->input->get("tmpl", "");
    $margin_form = "uk-margin-left uk-margin-right wk-margin-left wk-margin-right";
    
    if($tmpl != "component"){
        echo $div_menu;
        $margin_form = "";
    }
?>

    <form class="uk-form uk-form-stacked wk-form wk-form-stacked gru-media-form <?php echo $margin_form; ?>" action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <div class="uk-grid uk-margin-top uk-margin-bottom wk-grid wk-margin-top wk-margin-bottom">
            <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2">
                <h2 class="gru-page-title">
                    <?php
                        if(isset($_row->id)){
                            echo  JText::_('GURU_DAY_EDIT_MEDIA');
                        }
                        else{
                            echo  JText::_('GURU_DAY_NEW_MEDIA');
                        }
                    ?>
                </h2>
            </div>
            <div class="uk-width-1-1 uk-width-medium-1-2 uk-text-right uk-hidden-small wk-width-1-1 wk-width-medium-1-2 wk-text-right wk-hidden-small">
                <div class="uk-button-group wk-button-group">
                <?php
                    if($tmpl != "component"){
                ?>
                    <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="Joomla.submitbutton('apply_media');" />
                    <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('save_media');" />
                    <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&controller=guruAuthor&task=authormymedia&layout=authormymedia"); ?>';" />
                <?php
                    }
                    else{
                ?>
                    <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('savesbox');" />
                <?php
                    }
                ?>
                </div>
            </div>
            <div class="uk-width-1-1 uk-width-medium-1-2 uk-text-right uk-visible-small wk-width-1-1 wk-width-medium-1-2 wk-text-right wk-visible-small">
                <div class="uk-button-group wk-button-group">
                <?php
                    if($tmpl != "component"){
                ?>
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="Joomla.submitbutton('apply_media');" />
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('save_media');" />
                    <input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&controller=guruAuthor&task=authormymedia&layout=authormymedia"); ?>';" />
                <?php
                    }
                    else{
                ?>
                    <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('savesbox');" />
                <?php
                    }
                ?>
                </div>
            </div>
        </div>
        
        <div class="uk-grid wk-grid guru-media-grid" data-uk-grid-margin>
            <div class="uk-width-1-1 wk-width-1-1">
                <div class="uk-panel uk-panel-box uk-panel-box-secondary wk-panel wk-panel-box wk-panel-box-secondary">
                    <div class="uk-grid wk-grid">
                        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2 gru-media-form-row">
                            <label for="name" class="uk-form-label wk-form-label">
                                <?php echo JText::_('GURU_TYPE'); ?>:
                                <span class="uk-text-danger wk-text-danger">*</span>
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <div class="gru-media-tooltip">
                                    <?php 
                                        echo $lists['type'];
                                    ?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_TYPE"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2 gru-media-form-row">
                            <label for="name" class="uk-form-label wk-form-label">
                                <?php echo JText::_('GURU_NAME'); ?>:
                                <span class="uk-text-danger wk-text-danger">*</span>
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <?php
                                    if(isset($_row->name)){
                                        $_row->name = str_replace('"', '&quot;', $_row->name);
                                    }
                                ?>
                                <div class="gru-media-tooltip">
                                    <input type="text" name="name" id="name" size="60" value="<?php echo $_row->name; ?>">
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_NAME"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <div class="uk-margin-small-top wk-margin-small-top">
                                    <input type="checkbox" value="1" name="hide_name" <?php if($_row->hide_name == 1){ echo 'checked="checked"'; } ?> />
                                    <span class="lbl"><?php echo JText::_("LM_HIDE_NAME"); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="published" value="1"/>
                        
                        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2 gru-media-form-row">
                            <label for="name" class="uk-form-label wk-form-label">
                                <?php echo JText::_('GURU_INSTR'); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <div class="gru-media-tooltip">
                                    <textarea class="formField" name="instructions" rows="2" cols="60" ><?php echo stripslashes($_row->instructions); ?></textarea>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_INSTR"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2 gru-media-form-row">
                            <label for="name" class="uk-form-label wk-form-label">
                                <?php echo JText::_('GURU_SHOW_INSTRUCTION'); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
                                <select class="uk-width-1-1 wk-width-1-1" name="show_instruction" id="show_instruction">
                                    <option value="0" <?php if($_row->show_instruction == "0"){echo 'selected="selected"'; } ?> ><?php echo JText::_("GURU_SHOW_ABOVE"); ?></option>
                                    <option value="1" <?php if($_row->show_instruction == "1"){echo 'selected="selected"'; } ?> ><?php echo JText::_("GURU_SHOW_BELOW"); ?></option>
                                    <option value="2" <?php if($_row->show_instruction == "2"){echo 'selected="selected"'; } ?> ><?php echo JText::_("GURU_DONT_SHOW"); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <?php
                            if(isset($_row->auto_play) && $_row->auto_play == NULL){
                                $_row->auto_play = "1";
                            }
                            
                            $auto_play_display = "none";
                            if(isset($_row->type) && ($_row->type=='audio' || $_row->type=='video')){
                                $auto_play_display = "table-row";
                            }
                        ?>
                        
                        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2 gru-media-form-row" id="auto_play" style="display:<?php echo $auto_play_display; ?>">
                            <label for="name" class="uk-form-label wk-form-label">
                                <?php echo JText::_('GURU_AUTO_PLAY'); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
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
                        
                        <?php
                            $display_docs = "none";
                            if(isset($_row->type) && $_row->type=='docs'){
                                $display_docs = "block";
                            }
                        ?>
                        
                        <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2 gru-media-form-row" id="display_docs" style="display:<?php echo $display_docs; ?>">
                            <label for="name" class="uk-form-label wk-form-label">
                                <?php echo JText::_('GURU_MEDIA_DISPL_DOC'); ?>:
                            </label>
                            <div class="uk-form-controls wk-form-controls">
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
                                <div class="gru-media-tooltip">
                                    <select id="display_as" name="display_as" class="uk-width-1-1 wk-width-1-1">
                                        <option value="wrapper" onclick="javascript:wh(1)"><?php echo JText::_('GURU_MEDIA_DISPL_DOC_W'); ?></option>
                                        <option value="link" onclick="javascript:wh(0)" <?php if($_row->type=='docs' && $_row->width==1) {echo 'selected = "selected"'; $sel_link=1;}?>><?php echo JText::_('GURU_MEDIA_DISPL_DOC_L'); ?></option>
                                    </select>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DISPL_DOC"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="uk-width-1-1 wk-width-1-1">
                <?php
                    //-------- VIDEO - BEGIN 
                    $display_list_of_dir = $lists['video_dir'];
                    $display_list_of_files = $lists['video_url'];
                    $folder_of_files = $configuration->videoin;
                    if(isset($_row->type) && $_row->type=='video'){
                        $stylev = 'style="table-row;"';
                    }
                    else{
                        $stylev = 'style="visibility: hidden;"';
                    }
                ?>
                
                <div class="uk-panel uk-panel-box wk-panel wk-panel-box" id="videoblock" <?php echo $stylev; ?>>
                    <h3 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPEVIDEOS'); ?>
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </h3>
                    <div class="uk-clearfix wk-clearfix">
                        <?php 
                            $tab_active1 = "uk-active";
                            $tab_active2 = "";
                            $tab_active3 = "";
                            $tab_active4 = "";
                            
                             if($_row->source=='code'){
                                $tab_active1 = "uk-active";
                                $tab_active2 = "";
                                $tab_active3 = "";
                                $tab_active4 = "";
                             }
                             if($_row->source=='url'){
                                $tab_active1 = "";
                                $tab_active2 = "uk-active";
                                $tab_active3 = "";
                                $tab_active4 = "";
                             }
                             if($_row->source=='local' && $_row->uploaded_tab==0){
                                $tab_active1 = "";
                                $tab_active2 = "";
                                $tab_active3 = "uk-active";
                                $tab_active4 = "";
                             }
                             if($_row->source=='local' && $_row->uploaded_tab==1){
                                $tab_active1 = "";
                                $tab_active2 = "";
                                $tab_active3 = "";
                                $tab_active4 = "uk-active";
                             }
                        ?>
                        
                        <ul class="uk-tab uk-padding-remove wk-tab wk-padding-remove" data-uk-tab="{connect:'#tab-content'}">
                            <li id="urlv-tab" class="header-tab <?php echo $tab_active2; ?>" onclick="makeActiveV('addfromurl'); changeTab('urlv-tab'); return false;"><a href="#"><?php echo JText::_("GURU_ADD_FROM_URL_MEDIA");?></a></li>
                            <li id="embedv-tab" class="header-tab <?php echo $tab_active1; ?>" onclick="makeActiveV('embeded'); changeTab('embedv-tab'); return false;"><a href="#"><?php echo JText::_("GURU_EMBEDED_CODE");?></a></li>
                            <li id="uploadv-tab" class="header-tab <?php echo $tab_active3; ?>" onclick="makeActiveV('upload'); changeTab('uploadv-tab'); return false;"><a href="#"><?php echo JText::_("GURU_UPLOAD_FILE");?></a></li>
                            <li id="filev-tab" class="header-tab <?php echo $tab_active4; ?>" onclick="makeActiveV('selectfile'); changeTab('filev-tab'); return false;"><a href="#"><?php echo JText::_("GURU_SELECT_EXISTING_FILE");?></a></li>
                        </ul>
                        
                        <ul class="uk-switcher uk-margin uk-padding-remove wk-switcher wk-margin wk-padding-remove" id="tab-content">
                            <li id="urlv-tab-content" class="content-tab <?php echo $tab_active2; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom">
                                    <?php echo JText::_('GURU_MEDIATYPEVIDEO').' '.JText::_('GURU_MEDIATYPEURLURL'); ?>
                                </h5>
                                
                                <div class="uk-grid uk-grid-small wk-grid wk-grid-small">
                                    <div class="uk-width-7-10 wk-width-7-10">
                                        <input style="width:100% !important;" type="text" value="<?php echo $_row->url; ?>" name="url_v" id="url_v" class="uk-form-width-large wk-form-width-large" onPaste="javascript:change_radio_url()" onblur="javascript:addVideoFromUrl('<?php echo JURI::root(); ?>');" />
                                    </div>
                                    
                                    <div class="uk-width-3-10 uk-text-right wk-width-3-10 wk-text-right">
                                        <input type="button" class="uk-button uk-button-warning wk-button wk-button-warning" value="<?php echo JText::_("COM_GURU_GET_VIDEO_NFO"); ?>" name="video-info" onclick="javascript:addVideoFromUrl('<?php echo JURI::root(); ?>');" />
                                    </div>
                                </div>
                                
                                <div class="uk-alert uk-alert-warning wk-alert wk-alert-warning">
                                    <small><?php echo JText::_("COM_GURU_VIDEO_SUPPORTED"); ?></small>
                                    <div class="uk-margin-small-top wk-margin-small-top">
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
                                </div>
                                
                                <div class="uk-margin-top wk-margin-top" id="video_details">
                                </div>
                                
                                <div id="progress-video-upload" style="display:none; clear: both; margin-top:10px;">
                                    <div class="progress progress-success progress-striped active input-large">
                                        <div class="bar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </li>

                            <li id="embedv-tab-content" class="content-tab <?php echo $tab_active1; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPEVIDEO').' '.JText::_('GURU_MEDIATYPECODE'); ?></h5>
                                <small class="uk-text-muted wk-text-muted"><?php echo JText::_("GURU_TIP_MEDIATYPEVIDEO"); ?></small>
                                <textarea class="uk-width-1-1 wk-width-1-1" cols="23" name="code_v"><?php echo stripslashes($_row->code); ?></textarea>
                            </li>
                            
                            <li id="uploadv-tab-content" class="content-tab <?php echo $tab_active3; ?> gru-media-tab-content">
                                <div id="video_add_from_upload">
                                    <?php echo JText::_("GURU_LOCAL"); ?>
                                    &nbsp;&nbsp;
                                    
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_LOCAL"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                    
                                    <div class="uk-alert uk-alert-warning wk-alert wk-alert-warning"><?php echo JText::_("GURU_ACCEPTED_FILE_TYPES").": flv, swf, mov, wmv, mp4 and divx"; ?></div>
                                    <div class="uk-alert uk-alert-danger wk-alert wk-alert-danger">
                                        <?php echo $maxUpload; ?>
                                    </div>
                                    <div class="uk-clearfix wk-clearfix"></div>
                                    <div id="videoUploader"></div>
                                    <div class="uk-clearfix wk-clearfix"></div>
                                    <?php
                                        echo '<strong>'.$_row->local.'</strong><br/>';
                                    ?>
                                </div>
                            </li>
                            
                            <li id="filev-tab-content" class="content-tab <?php echo $tab_active4; ?> gru-media-tab-content">
                                <div id="video_add_from_existings">
                                    <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEVIDEO_');?></h5>
                                    <?php
                                        $now_selected = $this->now_selected_media ($_row->id);

                                        if(isset($now_selected)&&($now_selected!='')){
                                            echo str_replace($now_selected.'"',$now_selected.'" selected="selected"',$display_list_of_files); 
                                        }
                                        else{
                                            echo $display_list_of_files;
                                        }
                                    ?>
                                </div>
                                 <?php                                  
                                    if($_row->option_video_size == NULL){
                                        $_row->option_video_size = "0";
                                    }
                                    $default_size = $configuration->default_video_size;
                                    $default_zize_array = explode("x", $default_size);
                                    $dafault_size_height = $default_zize_array["0"];
                                    $dafault_size_width = $default_zize_array["1"];
                                ?>
                                <input type="hidden" id="width_v"  value="<?php echo $dafault_size_width;?>" name="width_v" />
                                <input type="hidden" id="height_v"  value="<?php echo $dafault_size_height;?>" name="height_v"/>
                                <input type="hidden" id="check" id="video_size" name="video_size" value="" />
                                <input id="source_v" type="hidden" value="<?php if($_row->source==''){ echo 'url'; } else{ echo $_row->source;} ?>" name="source_v"/>
                            </li>
                        </ul>
                        
                    </div>
                </div>
                <!--  VIDEO - END  ---->
                
                
                
                <?php //-------- AUDIO - BEGIN 
                    $display_list_of_dir = $lists['audio_dir'];
                    $display_list_of_files = $lists['audio_url'];
                    $folder_of_files = $configuration->audioin;
                    
                    if(isset($_row->type) && $_row->type=='audio'){
                        $stylea = 'style="table-row;"';
                    }
                    else{
                        $stylea = 'style="visibility: hidden;"';
                    }
                ?>
                
                <div class="uk-panel uk-panel-box uk-margin-remove wk-panel wk-panel-box wk-margin-remove" id="audioblock" <?php echo $stylea; ?>>
                    <h2 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPEAUDIOS'); ?>
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </h2>
                    <div class="uk-clearfix wk-clearfix">
                        <?php 
                            $tab_active1a = "uk-active";
                            $tab_active2a = "";
                            $tab_active3a = "";
                            $tab_active4a = "";
                            
                             if($_row->source=='code'){
                                $tab_active1a = "uk-active";
                                $tab_active2a = "";
                                $tab_active3a = "";
                                $tab_active4a = "";
                             }
                             if($_row->source=='url'){
                                $tab_active1a = "";
                                $tab_active2a = "uk-active";
                                $tab_active3a = "";
                                $tab_active4a = "";
                             }
                             if($_row->source=='local' && $_row->uploaded_tab==0){
                                $tab_active1a = "";
                                $tab_active2a = "";
                                $tab_active3a = "uk-active";
                                $tab_active4a = "";
                             }
                             if($_row->source=='local' && $_row->uploaded_tab==1){
                                $tab_active1a = "";
                                $tab_active2a = "";
                                $tab_active3a = "";
                                $tab_active4a = "uk-active";
                             }
                        ?>
                        <ul class="uk-tab uk-padding-remove wk-tab wk-padding-remove" data-uk-tab="{connect:'#tab-content-audio'}">
                            <li id="urla-tab" class="header-tab <?php echo $tab_active1a; ?>" onclick="makeActiveA('addfromurl'); changeTab('urla-tab'); return false;"><a href="#"><?php echo JText::_("GURU_ADD_FROM_URL_MEDIA");?></a></li>
                            <li id="embeda-tab" class="header-tab <?php echo $tab_active2a; ?>" onclick="makeActiveA('embeded'); changeTab('embeda-tab'); return false;"><a href="#"><?php echo JText::_("GURU_EMBEDED_CODE");?></a></li>
                            <li id="uploada-tab" class="header-tab <?php echo $tab_active3a; ?>" onclick="makeActiveA('upload'); changeTab('uploada-tab'); return false;"><a href="#"><?php echo JText::_("GURU_UPLOAD_FILE");?></a></li>
                            <li id="filea-tab" class="header-tab <?php echo $tab_active4a; ?>" onclick="makeActiveA('selectfile'); changeTab('filea-tab'); return false;"><a href="#"><?php echo JText::_("GURU_SELECT_EXISTING_FILE");?></a></li>
                        </ul>
                        
                        <ul class="uk-switcher uk-padding-remove wk-switcher wk-padding-remove" id="tab-content-audio">
                            <li id="urla-tab-content" class="content-tab <?php echo $tab_active1a; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPEAUDIO').' '.JText::_('GURU_MEDIATYPEURLURL');?></h5>
                                <div class="gru-media-tooltip">
                                    <input type="text" style="width: 100% !important;" class="uk-form-width-large wk-form-width-large" value="<?php echo $_row->url;?>" name="url_a"/>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIATYPEAUDIO"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </li>

                            <li id="embeda-tab-content" class="content-tab <?php echo $tab_active2a; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPEAUDIO').' '.JText::_('GURU_MEDIATYPECODE'); ?></h5>
                                <div class="gru-media-tooltip">
                                    <textarea class="uk-width-1-1 wk-width-1-1" cols="35" name="code_a"><?php echo stripslashes($_row->code); ?></textarea>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIATYPEAUDIOS"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </li>
                            
                            <li id="uploada-tab-content" class="content-tab <?php echo $tab_active3a; ?> gru-media-tab-content">
                                <?php echo JText::_("GURU_LOCAL"); ?>
                                &nbsp;&nbsp;
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUDIO_LOCAL"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                                
                                <div class="uk-alert uk-alert-warning wk-alert wk-alert-warning"><?php echo JText::_("GURU_ACCEPTED_FILE_TYPES").": mp3 and wma"; ?></div>
                                <div class="uk-alert uk-alert-danger wk-alert wk-alert-danger"><?php echo $maxUpload; ?></div>
                                <div class="clearfix"></div>
                                <div id="audioUploader"></div>
                                <div class="clearfix"></div>
                                <?php
                                    echo $now_selected;
                                ?>
                            </li>
                            
                            <li id="filea-tab-content" class="content-tab <?php echo $tab_active4a; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEAUDIO_');?></h5>
                                <?php echo $display_list_of_files; ?>
                                <input type="hidden" value="<?php if(isset($_row->id)&&($_row->id>0)) {echo $_row->width;} else {echo "250";}?>" name="width_a" id="width_a"/>
                                <input type="hidden" value="50" name="height_a"/>
                            </li>
                        </ul>
                        <input id="source_a" value="<?php if($_row->source==''){echo 'code';}else{ echo $_row->source;}?>" type="hidden" name="source_a"/>
                    </div>
                </div>
                <!--  AUDIO - END  ---->
                
                
                <?php //-------- DOCUMENTS - BEGIN 
                $display_list_of_dir = $lists['docs_dir'];
                $display_list_of_files = $lists['docs_url'];
                $folder_of_files = $configuration->docsin;
                if(isset($_row->type) && $_row->type=='docs'){
                    $styled = 'style="table-row;"';
                }
                else{
                    $styled = 'style="visibility: hidden;"';
                }
                ?>
                
                <div class="uk-panel uk-panel-box uk-margin-remove wk-panel wk-panel-box wk-margin-remove" id="docsblock" <?php echo $styled; ?>>
                    <h2 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPEDOCSS'); ?>
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </h2>
                    <div class="uk-clearfix wk-clearfix">
                        <?php 
                            $tab_active2d = "active";
                            $tab_active3d = "";
                            $tab_active4d = "";
                            
                            if($_row->source=='url'){
                                $tab_active2d = "active";
                                $tab_active3d = "";
                                $tab_active4d = "";
                            }
                            
                            if($_row->source=='local' && $_row->uploaded_tab==0){
                                $tab_active2d = "";
                                $tab_active3d = "active";
                                $tab_active4d = "";
                            }
                            
                            if($_row->source=='local' && $_row->uploaded_tab==1){
                                $tab_active2d = "";
                                $tab_active3d = "";
                                $tab_active4d = "active";
                            }
                        ?>
                        <ul class="uk-tab uk-padding-remove wk-tab wk-padding-remove" data-uk-tab="{connect:'#tab-content-doc'}">
                            <li id="urld-tab" class="header-tab <?php echo $tab_active2d; ?>" onclick="makeActiveD('addfromurl'); changeTab('urld-tab'); return false;"><a href="#"><?php echo JText::_("GURU_ADD_FROM_URL_MEDIA");?></a></li>
                            <li id="uploadd-tab" class="header-tab <?php echo $tab_active3d; ?>" onclick="makeActiveD('upload'); changeTab('uploadd-tab'); return false;"><a href="#"><?php echo JText::_("GURU_UPLOAD_FILE");?></a></li>
                            <li id="filed-tab" class="header-tab <?php echo $tab_active4d; ?>" onclick="makeActiveD('selectfile'); changeTab('filed-tab'); return false;"><a href="#"><?php echo JText::_("GURU_SELECT_EXISTING_FILE");?></a></li>
                        </ul>
                        
                        <ul class="uk-switcher uk-padding-remove wk-switcher wk-padding-remove" id="tab-content-doc">
                            <li id="urld-tab-content" class="content-tab <?php echo $tab_active2d; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPEDOCS').' '.JText::_('GURU_MEDIATYPEURLURL');?></h5>
                                <div class="gru-media-tooltip">
                                    <input type="text" style="width: 100%;" value="<?php echo $_row->url;?>" name="url_d"/>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIATYPEDOCS"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                            </li>
                            
                            <li id="uploadd-tab-content" class="content-tab <?php echo $tab_active3d; ?> gru-media-tab-content">
                                <?php echo JText::_("GURU_LOCAL"); ?>
                                &nbsp;&nbsp;
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AUDIO_LOCAL"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                                
                                <div class="uk-alert uk-alert-warning wk-alert wk-alert-warning"><?php echo JText::_("GURU_ACCEPTED_FILE_TYPES").": txt, pdf and xls"; ?></div>
                                <div class="uk-alert uk-alert-danger wk-alert wk-alert-danger"><?php echo $maxUpload; ?></div>
                                <div class="clearfix"></div>
                                
                                <div class="gru-media-tooltip">
                                    <div id="docUploader"></div>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIA_UPLOAD"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                
                                <?php echo $now_selected; ?>
                                <div class="clearfix"></div>
                            </li>
                            
                            <li id="filed-tab-content" class="content-tab <?php echo $tab_active4d; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEDOCS_');?></h5>  
                                <?php echo $display_list_of_files; ?>
                            </li>
                        </ul>
                        <?php
                            if(intval($_row->width) == 0){
                                $_row->width = "600";
                            }
                            
                            if(intval($_row->height) == 0){
                                $_row->height = "800";
                            }
                        ?>
                    
                        <input type="hidden" value="<?php echo $_row->width; ?>" name="width" id="width"/> 
                        <input type="hidden"  value="<?php echo $_row->height; ?>" name="height" id="height"/>
                        <input id="source_d" value="<?php if($_row->source==''){echo 'url';}else{ echo $_row->source;}?>" type="hidden" name="source_d"/>
                    </div>
                </div>   
                <!--  DOCUMENTS - END  ---->
            
            
            
                <?php //-------- URL - BEGIN 
                if(isset($_row->type) && $_row->type=='url'){
                    $styleu = 'style="table-row;"';
                }
                else{
                    $styleu = 'style="display:none;"';
                }
                ?>
                
                <div id="urlblock" <?php echo $styleu; ?> class="uk-panel uk-panel-box uk-form uk-form-horizontal uk-margin-remove wk-panel wk-panel-box wk-form wk-form-horizontal wk-margin-remove">
                    <div class="uk-form-row wk-form-row">
                        <label for="name" class="uk-form-label wk-form-label">
                            <?php echo JText::_('GURU_MEDIATYPEURL_'); ?>
                        </label>
                        <div class="uk-form-controls wk-form-controls gru-media-tooltip">
                            <input type="text" style="width: 100% !important;" value="<?php if (isset($_row->url)){echo $_row->url;}else{ echo "http://";}?>" name="url"/>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIATYPEURL_"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                    
                    <div class="uk-form-row wk-form-row">
                        <label for="name" class="uk-form-label wk-form-label">
                            <?php echo JText::_('GURU_MEDIA_DISPL_DOC'); ?>
                        </label>
                        <div class="uk-form-controls gru-media-tooltip wk-form-controls">
                            <select name="display_as2" class="uk-width-1-1 wk-width-1-1">
                                <option value="wrapper"><?php echo JText::_('GURU_MEDIA_DISPL_DOC_W'); ?></option>
                                <option value="link" <?php if($_row->type=='url' && $_row->width==1) echo 'selected = "selected"'; ?>><?php echo JText::_('GURU_MEDIA_DISPL_DOC_L'); ?></option>
                            </select>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DISPL_DOC"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                    
                    <div class="uk-form-row wk-form-row">
                        <label for="name" class="uk-form-label wk-form-label">
                            <?php echo JText::_('GURU_IFRAME_SIZE'); ?>
                        </label>
                        <div class="uk-form-controls wk-form-controls gru-media-tooltip">
                            <?php
                                if($_row->width == "" || $_row->width == "0"){
                                    $_row->width = "800";
                                }
                                
                                if($_row->height == "" || $_row->height == "0"){
                                    $_row->height = "600";
                                }
                            ?>
                            <div style="float:left;">
                                <input type="text" style="width:50px;" value="<?php echo $_row->width; ?>" name="width" id="width"/>
                            </div>
                            <div style="float:left;">x</div>
                            <div style="float:left;">
                                <input type="text" style="width:50px;" value="<?php echo $_row->height; ?>" name="height" id="height"/>
                            </div>
                            <div style="float:left;">px</div>
                            <div class="uk-clearfix wk-clearfix"></div>
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_IFRAME_SIZE"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </div>
                    </div>
                </div>
                <!--  URL - END  ---->
                
                <?php //-------- ZOOM - BEGIN 
                if(isset($_row->type) && $_row->type=='zoom'){
                    $stylezoom = 'style="table-row;"';
                    $data = new JRegistry($_row->code);
                }
                else{
                    $stylezoom = 'style="display:none;"';
                    $data = new JRegistry;
                }
                ?>
                <div id="zoomblock" <?php echo $stylezoom; ?> class="uk-panel uk-panel-box uk-form uk-form-horizontal uk-margin-remove wk-panel wk-panel-box wk-form wk-form-horizontal wk-margin-remove">
                    <div class="uk-form-row wk-form-row">
                        <label for="meeting_id_z" class="uk-form-label wk-form-label">
                            <?php echo JText::_('GURU_MEDIA_ZOOM_MEETING_ID'); ?>
                        </label>
                        <div class="uk-form-controls wk-form-controls gru-media-tooltip">
                            <input id="meeting_id_z" type="text" style="width: 100% !important;" value="<?php echo $data->get('meeting_id') ?>" name="meeting_id_z"/>
                        </div>
                    </div>
                    
                    <div class="uk-form-row wk-form-row">
                        <label for="meeting_password_z" class="uk-form-label wk-form-label">
                            <?php echo JText::_('GURU_MEDIA_ZOOM_MEETING_PASSWORD'); ?>
                        </label>
                        <div class="uk-form-controls gru-media-tooltip wk-form-controls">
                            <input id="meeting_password_z" type="text" style="width: 100% !important;" value="<?php echo $data->get('meeting_pass') ?>" name="meeting_password_z"/>
                        </div>
                    </div>
                    
                    <div class="uk-form-row wk-form-row">
                        <label for="invite_link_z" class="uk-form-label wk-form-label">
                            <?php echo JText::_('GURU_MEDIA_ZOOM_MEETING_INVITE_LINK'); ?>
                        </label>
                        <div class="uk-form-controls gru-media-tooltip wk-form-controls">
                            <input id="invite_link_z" type="text" style="width: 100% !important;" value="<?php echo $data->get('invite_link') ?>" placeholder="https://" name="invite_link_z"/>
                        </div>
                    </div>
                </div>
                
                <?php //-------- Article - BEGIN 
                if(isset($_row->type) && $_row->type=='article'){
                    $styleu = 'style="table-row;"';
                }
                else{
                    $styleu = 'style="display:none;"';
                }
                ?>
                
                <div class="uk-panel uk-panel-box uk-margin-remove wk-panel wk-panel-box wk-margin-remove" id="artblock" <?php echo $styleu; ?>>
                    <h2 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPEARTICLE_'); ?>
                    </h2>
                    <div class="uk-clearfix wk-clearfix gru-media-tooltip">
                        <?php
                            if($_row->id !=""){
                                $db = JFactory::getDBO();
                                $sql = "SELECT code FROM `#__guru_media` WHERE type='article' and id=".$_row->id;
                                $db->setQuery($sql);
                                $guru_articleid = $db->loadColumn();
                            }
                            
                            if(isset($code) && $code !=0){
                                $sql = "SELECT title FROM `#__content` WHERE id=".$guru_articleid["0"];
                                $db->setQuery($sql);
                                $guru_article_name = $db->loadColumn();
                            }   
                        ?>
                        <?php echo $this->displayArticleguru(@$guru_articleid["0"], @$guru_article_name["0"]); ?>   
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_MEDIATYPEARTICLE_"); ?>" >
                            <img border="0" src="components/com_guru/images/icons/tooltip.png">
                        </span>
                    </div>
                </div>
                <!--  ARTICLE - END  ---->
                
                
                <?php //-------- IMAGE - BEGIN 
                if(isset($_row->type) && $_row->type=='image'){
                    $stylei = 'style="table-row;"';
                }
                else{
                    $stylei = 'style="display:none;"';
                }
                ?>
                
                <div class="uk-panel uk-panel-box uk-margin-remove wk-panel wk-panel-box wk-margin-remove" id="imageblock" <?php echo $stylei; ?>>
                    <h2 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPEIMAGE');?>
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </h2>
                    <div class="uk-clearfix wk-clearfix">
                        <div id="imageUploader"></div>
                        
                        <div class="uk-alert uk-alert-warning wk-alert wk-alert-warning">
                            <?php echo JText::_('GURU_ALLOWED_IMAGES_EXT');?>
                        </div>
                        <div class="uk-alert uk-alert-danger wk-alert wk-alert-danger">
                        <?php
                            echo $maxUpload;
                        ?>
                        </div>
                        
                        <?php 
                            $media_fullpx = 200;
                            $media_prop = 'w';
                            $is_image = 0;              
                            
                            if($_row->width>0 && $_row->height == 0){
                                $media_fullpx = $_row->width;
                                $media_prop = 'w';
                                $is_image = 1;
                            }   
                            
                            if($_row->height >0 && $_row->width == 0){
                                $media_fullpx = $_row->height;                  
                                $media_prop = 'h';
                                $is_image = 1;
                            }
                            
                            if(trim($_row->local)!=""){
                                $is_image = 1;
                            }
                        ?>
                        <div class="uk-clearfix wk-clearfix">
                            <input type="hidden" id="media_fullpx" name="media_fullpx" value="<?php echo $media_fullpx;?>" />
                            
                            <select name="media_prop" id="media_prop" class="input-small" style="display:none;">
                                <option value="w" <?php if($media_prop=='w') echo 'selected="selected"'; ?>><?php  echo JText::_('GURU_PROPW');?></option>
                                <option value="h" <?php if($media_prop=='h') echo 'selected="selected"'; ?>><?php  echo JText::_('GURU_PROPH');?></option>
                            </select>
                        </div>
                        <input type="hidden" id="is_image" name="is_image" value="<?php echo $is_image;?>" />
                        
                        <div class="uk-clearfix wk-clearfix"></div>
                        <?php echo JText::_('GURU_PRODCIMG');?>:
                        <?php
                            if(isset($_row->local) && trim($_row->local) != ""){
                                require_once(JPATH_SITE."/components/com_guru/helpers/helper.php");
                                $helper = new guruHelper();
                                $width = $_row->width;
                                $height = $_row->height;
                                $new_size = "";
                                $type = "";
                                if(intval($width) != 0){
                                    $new_size = $width;
                                    $type = "w";
                                }
                                else{
                                    $new_size = $height;
                                    $type = "h";
                                }
                                $q = "SELECT * FROM #__guru_config WHERE id = '1' ";
                                $db->setQuery( $q );
                                $configs = $db->loadObject();
                                
                                $helper->createThumb($_row->local, $configs->imagesin.'/media', $new_size, $type);
                                $media_image = '<img id="view_imagelist23" name="view_imagelist" style="margin:5px;" border="0" alt="" src="'.JURI::root().$configuration->imagesin."/media/thumbs".$_row->local.'" />';
                            }
                            else{
                                $media_image = '<img id="view_imagelist23" name="view_imagelist" style="margin:5px;" border="0" alt="" src="'.JURI::base().'components/com_guru/images/blank.png" />';
                            }
                            // generating thumb image - stop
                        ?>
                  
                        <?php echo $media_image; ?>
                        <input type="hidden" id="image" name="image" value="<?php echo $_row->local;?>" />
                    </div>
                </div>
                <!--  IMAGE - END  ---->
           
            
                <?php //-------- TEXT - BEGIN 
                if(isset($_row->type) && $_row->type=='text'){
                    $stylet = 'style="block;"';
                }
                else{
                    $stylet = 'style="display:none;"';
                }
                ?>

                 <div class="uk-panel uk-panel-box uk-margin-remove wk-panel wk-panel-box wk-margin-remove" id="textblock" <?php echo $stylet; ?>>
                    <h2 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPETEXT');?>
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </h2>
                    <div class="uk-clearfix wk-clearfix">
                        <?php
                            $doc = JFactory::getDocument();
                            //$doc->addScript(JURI::root().'components/com_guru/js/redactor.min.js');
                            $doc->addStyleSheet(JURI::root().'components/com_guru/css/redactor.css');
                        ?>
                        <textarea id="text" name="text" class="useredactor" style="width:100%; height:100px;"><?php echo $_row->code; ?></textarea>
                        
                        <?php
                            $upload_script = 'jQuery( document ).ready(function(){
                                                jQuery(".useredactor").redactor({
                                                     buttons: [\'bold\', \'italic\', \'underline\', \'link\', \'alignment\', \'unorderedlist\', \'orderedlist\']
                                                });
                                                jQuery(".redactor_useredactor").css("height","300px").css("width", "100%");
                                              });';
                            $doc->addScriptDeclaration($upload_script);
                        ?>
                    </div>
                </div>
                <!--  TEXT - END  ---->
            
                
                <?php
                    //-------- FILE - BEGIN 
                    $display_list_of_dir = $lists['files_dir'];
                    $display_list_of_files = $lists['files_url'];
                    
                    if(isset($_row->type) && $_row->type=='file'){
                        $stylef = 'style="block;"';
                    }
                    else{
                        $stylef = 'style="visibility: hidden;"';
                    }
                ?>
            
                <div class="uk-panel uk-panel-box uk-margin-remove wk-panel wk-panel-box wk-margin-remove" id="fileblock" <?php echo $stylef; ?>>
                    <h2 class="uk-panel-title wk-panel-title">
                        <?php echo JText::_('GURU_MEDIATYPEFILES');?>
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </h2>
                    <div class="uk-clearfix wk-clearfix">
                        <?php 
                            $tab_active2f = "active";
                            $tab_active3f = "";
                            $tab_active4f = "";
                            
                            
                            if($_row->source=='url'){
                                $tab_active2f = "active";
                                $tab_active3f = "";
                                $tab_active4f = "";
                            }
                            
                            if($_row->source=='local' && $_row->uploaded_tab == 0){
                                $tab_active2f = "";
                                $tab_active3f = "active";
                                $tab_active4f = "";
                            }
                            
                            if($_row->source=='local' && $_row->uploaded_tab == 1){
                                $tab_active2f = "";
                                $tab_active3f = "";
                                $tab_active4f = "active";
                            }
                        ?>
                        
                        <ul class="uk-tab uk-padding-remove wk-tab wk-padding-remove" data-uk-tab="{connect:'#tab-content-file'}">
                            <li id="urlf-tab" class="header-tab <?php echo $tab_active2f; ?>" onclick="makeActiveF('addfromurl'); changeTab('urlf-tab'); return false;"><a href="#"><?php echo JText::_("GURU_ADD_FROM_URL_MEDIA");?></a></li>
                            <li id="uploadf-tab" class="header-tab <?php echo $tab_active3f; ?>" onclick="makeActiveF('upload'); changeTab('uploadf-tab'); return false;"><a href="#"><?php echo JText::_("GURU_UPLOAD_FILE");?></a></li>
                            <li id="filef-tab" class="header-tab <?php echo $tab_active4f; ?>" onclick="makeActiveF('selectfile'); changeTab('filef-tab'); return false;"><a href="#"><?php echo JText::_("GURU_SELECT_EXISTING_FILE");?></a></li>
                        </ul>
                        
                        <ul class="uk-switcher uk-padding-remove wk-switcher wk-padding-remove" id="tab-content-file">
                            <li id="urlf-tab-content" class="content-tab <?php echo $tab_active2f; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_FILE').' '.JText::_('GURU_MEDIATYPEURLURL');?></h5>
                                <div class="gru-media-tooltip">
                                    <input type="text" onKeyPress="javascript:change_radio_url();" onPaste="javascript:change_radio_url()" style="width:100% !important" value="<?php echo $_row->url;?>" id="url_f" name="url_f"  onChange="javascript:hide_hidden_row();" onmouseout="doPreview();" on/>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_FILE_MEDIATYPEURLURL"); ?>" >
                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                    </span>
                                </div>
                                <?php 
                                    if($_row->source=="url" && $_row->url!=""){
                                ?>
                                        <div id="filePreview">
                                            <a class="a_guru" href="<?php echo $_row->url; ?>"><?php echo JText::_("GURU_DOWNLOAD"); ?></a>
                                        </div>
                                <?php   
                                    } 
                                ?>
                                <div id="filePreview"></div>
                            </li>
                            
                            <li id="uploadf-tab-content" class="content-tab <?php echo $tab_active3f; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_("GURU_LOCAL_FILE"); ?></h5>
                                <div class="uk-alert uk-alert-warning wk-alert wk-alert-warning"><?php echo JText::_("GURU_ACCEPTED_FILE_TYPES").": zip and exe"; ?></div>
                                <div class="uk-alert uk-alert-danger wk-alert wk-alert-danger">
                                    <?php echo $maxUpload; ?>
                                </div>
                                
                                <div id="uploadblock">
                                    <div id="fileUploader"></div>
                                </div>
                                
                                <div class="clearfix"></div>
                                <?php
                                    echo $now_selected;
                                ?>
                                <br/>
                                <?php 
                                    if($_row->source=="local" && $_row->local!=""){
                                ?>
                                        <a class="a_guru" href="<?php echo JURI::root().trim($configuration->filesin)."/".$_row->local; ?>" id="filePreviewList">
                                            <?php echo JText::_("GURU_DOWNLOAD"); ?>
                                        </a>
                                <?php 
                                    }
                                    else{
                                ?>
                                        <a class="a_guru" href="#" style="visibility:hidden" id="filePreviewList">
                                            <?php echo JText::_("GURU_DOWNLOAD"); ?>
                                        </a>
                                <?php
                                    }
                                ?>
                            </li>
                            
                            <li id="filef-tab-content" class="content-tab <?php echo $tab_active4f; ?> gru-media-tab-content">
                                <h5 class="uk-margin-small-bottom wk-margin-small-bottom"><?php echo JText::_('GURU_MEDIATYPECHOOSE').' '.JText::_('GURU_MEDIATYPEFILE_');?></h5>
                                <?php echo $display_list_of_files; ?>
                                
                                <div class="clearfix"></div>
                                <div id="filesFolder" class="uk-text-muted uk-margin-small wk-text-muted wk-margin-small">
                                    <small><?php echo JURI::root().$configuration->filesin; ?></small>
                                </div>
                                
                                <?php 
                                    if($_row->source=="local" && $_row->local!=""){
                                ?>
                                        <a class="a_guru" href="<?php echo JURI::root().trim($configuration->filesin)."/".$_row->local; ?>" id="filePreviewList">
                                            <?php echo JText::_("GURU_DOWNLOAD"); ?>
                                        </a>
                                <?php 
                                    }
                                    else{
                                ?>
                                        <a class="a_guru" href="#" style="visibility:hidden" id="filePreviewList">
                                            <?php echo JText::_("GURU_DOWNLOAD"); ?>
                                        </a>
                                <?php
                                    }
                                ?>
                            </li>
                        </ul>
                        <input id="source_f" value="<?php if($_row->source==''){echo 'url';}else{ echo $_row->source;}?>" type="hidden" name="source_f"/>
                    </div>
                </div>
                <!--  FILE - END  ---->
            </div>
        </div>

        <input type="hidden" name="option" value="com_guru" />
        <input type="hidden" name="task" value="edit" />
        <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
        <input type="hidden" name="controller" value="guruAuthor" />
        <input type="hidden" name="was_uploaded" id="was_uploaded" value="1" />
        <input type="hidden" name="uploaded_tab" id="uploaded_tab" value="-1" />
        <input type="hidden" name="tmpl" id="tmpl" value="<?php echo $tmpl; ?>" />
        <?php
            $user = JFactory::getUser();
            $user_id = $user->id;
        ?>
        <input type="hidden" name="author" id="author" value="<?php echo intval($user_id); ?>" />
        
        <?php
            if($tmpl == "component"){
                $mediaval1  = JFactory::getApplication()->input->get("med", "");
                $mediaval2  = JFactory::getApplication()->input->get("txt", "");
                $scr        = JFactory::getApplication()->input->get("scr", "0");
                $txt        = JFactory::getApplication()->input->get("txt", "0");
    
                $mediatext = "";
                if($mediaval1 != ""){
                    $mediatext = "med";
                }
                elseif($mediaval2 != ""){
                    $mediatext = "txt";
                }
                
                $mediatextvalue = "";
                if($mediaval1 != ""){
                    $mediatextvalue = $mediaval1;
                }
                elseif($mediaval2 != ""){
                    $mediatextvalue = $mediaval2;
                }
                
                $cid = JFactory::getApplication()->input->get("cid", "0");
                $progrid = JFactory::getApplication()->input->get("progrid", "0");
                $module = JFactory::getApplication()->input->get("module", "0");
        ?>
                <input type="hidden" name="action" value="addmedia" id="action" />
                <input type="hidden" name="mediatext" value="<?php echo $mediatext; ?>" id="mediatext" />
                <input type="hidden" name="mediatextvalue" value="<?php echo $mediatextvalue; ?>" id="mediatextvalue" />
                <input type="hidden" name="screen" id="screen"  value="<?php echo intval($scr); ?>" />
                <input type="hidden" name="redirect_to" value="<?php echo JURI::root().'index.php?option=com_guru&controller=guruAuthor&tmpl=component&task=editsbox&cid='.intval($cid).'&progrid='.intval($progrid).'&module='.intval($module); ?>" />
        <?php
            }
        ?>
        <?php
            if($tmpl != "component"){
        ?>
                <hr class="uk-divider wk-divider">
                <div class="uk-text-right uk-margin-top uk-hidden-small wk-text-right wk-margin-top wk-hidden-small">
                    <div class="uk-button-group wk-button-group">
                        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="Joomla.submitbutton('apply_media');" />
                        <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('save_media');" />
                        <input type="button" class="uk-button uk-button-primary wk-button wk-button-primary" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&controller=guruAuthor&task=authormymedia&layout=authormymedia"); ?>';" />
                    </div>
                </div>

                <div class="uk-text-right uk-margin-top uk-visible-small wk-text-right wk-margin-top wk-visible-small">
                    <div class="uk-button-group wk-button-group">
                        <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="Joomla.submitbutton('apply_media');" />
                        <input type="button" class="uk-button uk-button-success uk-width-1-1 wk-button wk-button-success wk-width-1-1" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('save_media');" />
                        <input type="button" class="uk-button uk-button-primary uk-width-1-1 wk-button wk-button-primary wk-width-1-1" value="<?php echo JText::_("GURU_CLOSE"); ?>" onclick="document.location.href='<?php echo JRoute::_("index.php?option=com_guru&controller=guruAuthor&task=authormymedia&layout=authormymedia"); ?>';" />
                    </div>
                </div>
        <?php
            }
            else{
        ?>
                <hr class="uk-divider wk-divider">
                <div class="uk-text-right uk-margin-top wk-text-right wk-margin-top">
                    <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onclick="Joomla.submitbutton('savesbox');" />
                </div>
        <?php
            }
        ?>
    </form>
    
    <script type="text/javascript" language="javascript">
        document.body.onload = function() {
            if(document.getElementById("videoblock").style.visibility == 'hidden'){
                document.getElementById("videoblock").style.visibility = 'visible';
                document.getElementById("videoblock").style.display = 'none';
            }
            
            if(document.getElementById("audioblock").style.visibility == 'hidden'){
                document.getElementById("audioblock").style.visibility = 'visible';
                document.getElementById("audioblock").style.display = 'none';
            }
            
            if(document.getElementById("docsblock").style.visibility == 'hidden'){
                document.getElementById("docsblock").style.visibility = 'visible';
                document.getElementById("docsblock").style.display = 'none';
            }
            
            if(document.getElementById("fileblock").style.visibility == 'hidden'){
                document.getElementById("fileblock").style.visibility = 'visible';
                document.getElementById("fileblock").style.display = 'none';
            }
        };
    </script>
</div>

<div id="defaultblock"></div>