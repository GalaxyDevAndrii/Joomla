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
$my_media = $this->mymediath;
$config = $this->config;
$filters= $this->filters;
$all_categs =  $this->all_categs;
$allow_teacher_action = json_decode($config->st_authorpage);//take all the allowed action from administator settings
$teacher_add_media = @$allow_teacher_action->teacher_add_media; //allow or not action Add media
$teacher_edit_media = @$allow_teacher_action->teacher_edit_media; //allow or not action Edit media
$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
$page_from = JFactory::getApplication()->input->get("page_from","");
$tmpl_comp =  JFactory::getApplication()->input->get("tmpl","");

$doc =JFactory::getDocument();
//$doc->addScript('components/com_guru/js/guru_modal.js');
//$doc->addScript('components/com_guru/js/jquery-dropdown.js');
JHtml::stylesheet('components/com_guru/css/tabs.css');

$doc->addScript(JURI::root().'plugins/content/jw_allvideos/jw_allvideos/includes/js/mediaplayer/jwplayer.min.js');
$doc->addScript(JURI::root().'plugins/content/jw_allvideos/jw_allvideos/includes/js/wmvplayer/silverlight.js');
$doc->addScript(JURI::root().'plugins/content/jw_allvideos/jw_allvideos/includes/js/wmvplayer/wmvplayer.js');
$doc->addScript(JURI::root().'plugins/content/jw_allvideos/jw_allvideos/includes/js/quicktimeplayer/AC_QuickTime.js');
$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_AUTHOR_MY_MEDIA')));

$data_post = JFactory::getApplication()->input->post->getArray();
?>

<script type="text/javascript" language="javascript">
    document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="application/javascript">
    function deleteAuthorMedia(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            if(confirm("<?php echo JText::_("GURU_REMOVE_AUTHOR_MEDIA"); ?>")){
                document.adminForm.task.value='removeMedia';
                document.adminForm.submit();
            }
        }   
    }
    function unpublishMedia(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            document.adminForm.task.value='unpublishMedia';
            document.adminForm.submit();
        }   
    }
    
    function publishMedia(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            document.adminForm.task.value='publishMedia';
            document.adminForm.submit();
        }
    }
    
    function duplicateMedia(){
        if (document.adminForm['boxchecked'].value == 0) {
            alert( "<?php echo JText::_("GURU_MAKE_SELECTION_FIRT");?>" );
        } 
        else{
            document.adminForm.task.value='duplicateMedia';
            document.adminForm.submit();
        }
    }
    
    function newMedia(val){
        document.adminForm.task.value='editMedia';
        document.adminForm.selected_item_New.value=val;
        document.adminForm.submit();
    }
    
    function newAuthorMediaCategory(){
        document.adminForm.task.value='authoraddeditmediacat';
        document.adminForm.submit();
    }
    function editOptions(){
        display = document.getElementById("button-options").style.display;
        
        if(display == "none"){
            document.getElementById("button-options").style.display = "";
            document.getElementById("new-options").value = "<?php echo JText::_('GURU_NEW'); ?> \u2227";
        }
        else{
            document.getElementById("button-options").style.display = "none";
            document.getElementById("new-options").value = "<?php echo JText::_('GURU_NEW'); ?> \u2228";
        }
    }
    
    function editOptions2(){
        display = document.getElementById("button-options2").style.display;
        
        if(display == "none"){
            document.getElementById("button-options2").style.display = "";
            document.getElementById("new-options2").value = "<?php echo JText::_('GURU_NEW'); ?> \u2227";
        }
        else{
            document.getElementById("button-options2").style.display = "none";
            document.getElementById("new-options2").value = "<?php echo JText::_('GURU_NEW'); ?> \u2228";
        }
    }
    
    function putMediaToQuestion(id, name, class_name){
        if(!eval(parent.document.getElementById('question_media_ids_'+id))){
            var myDiv = document.createElement('div');
            myDiv.id = "guru_media"+id;
            myDiv.innerHTML = '<i class="'+class_name+'"></i>'+' '+name+'&nbsp;&nbsp;&nbsp;'+'<img border="0" src="<?php echo JURI::root();?>components/com_guru/images/delete2.gif" onclick="javascript:deleteQuestionMedia('+id+')">';
            myDiv.innerHTML += '<input type="hidden" id="question_media_ids_'+id+'" name="question_media_ids[]" value="'+id+'" />';
            
            //delete the old media assigned
            parent.document.getElementById('g_media_list').innerHTML = "";
            //change button label
            parent.document.getElementById('media_button_for_question').innerHTML = "<?php echo JText::_("GURU_CHANGE_MEDIA"); ?>";
            
            var media_list = parent.document.getElementById('g_media_list');
            media_list.appendChild(myDiv);
            
            parent.document.getElementById('iframe_media_list').style.display = "none";
        }
        return false;
    }
    
    function putMediaToAnswer(radio_value, id, name, class_name){
        if(!eval(parent.document.getElementById('ans_media_ids_'+radio_value+"_"+id))){
            var myDiv = document.createElement('div');
            myDiv.id = "ans_media_"+radio_value+"_"+id;
            myDiv.innerHTML = '<i class="'+class_name+'"></i>'+' '+name+'&nbsp;&nbsp;&nbsp;'+'<img border="0" src="<?php echo JURI::root();?>components/com_guru/images/delete2.gif" onclick="javascript:deleteAnswerMedia('+id+', '+radio_value+')">';
            myDiv.innerHTML += '<input type="hidden" id="ans_media_ids_'+radio_value+"_"+id+'" name="ans_media_ids['+radio_value+'][]" value="'+id+'" />';
            
            //delete the old media assigned
            parent.document.getElementById('ans_media_'+radio_value).innerHTML = "";
            //change button label
            parent.document.getElementById('button_media_answers_'+radio_value).innerHTML = "<?php echo JText::_("GURU_CHANGE_MEDIA"); ?>";
            
            var media_list = parent.document.getElementById('ans_media_'+radio_value);
            media_list.appendChild(myDiv);
            
            parent.document.getElementById('iframe_media_list_ans_'+radio_value).style.display = "none";
        }
        return false;
    }   
    
    function addNewMedia(){
        if(jQuery("#list-sub-button").is(":visible")){
            jQuery("#list-sub-button").css("top", "30px").css("left", "0px").hide();
        }
        else{
            jQuery("#list-sub-button").css("top", "30px").css("left", "0px").show();
        }
    }
</script>

<style>
    div.g_inline_child button.btn{
        height:26px !important;
    }
</style>

<?php
    if(($page_from == 'answers' || $page_from == 'question') && $tmpl_comp =='component'){
        echo '<style>.page-title{display:none;}</style>';
    }
?>

<div class="gru-mycoursesauthor">
    <?php   echo $div_menu; //MENU TOP OF AUTHORS ?>
    <!--BUTTONS -->
    
    <?php if($page_from != 'question' && $tmpl_comp !='component'){?>
        <div class="uk-grid uk-margin wk-grid wk-margin">
            <div class="uk-width-1-1 uk-width-medium-1-2 wk-width-1-1 wk-width-medium-1-2"><h2 class="gru-page-title"><?php echo JText::_('GURU_AUTHOR_MY_MEDIA');?></h2></div>
            <div class="uk-width-1-2 uk-hidden-small uk-text-right wk-width-1-2 wk-hidden-small wk-text-right">
                <div class="uk-button-group wk-button-group">
                   <?php if($teacher_add_media == 0){?>
                                <div class="uk-button-dropdown wk-button-dropdown no-padding inline-block" onclick="addNewMedia(); return false;" data-uk-dropdown="{mode:'click'}">                
                                    <!-- This is the button toggling the dropdown -->
                                    <button class="uk-button uk-button-success wk-button wk-button-success"><?php echo JText::_('GURU_DAY_NEW_MEDIA'); ?>&nbsp;<span class="fa fa-caret-down"></span></button>
                                    <div id="list-sub-button" class="uk-dropdown uk-dropdown-small wk-dropdown wk-dropdown-small">
                                        <ul class="uk-nav uk-nav-dropdown uk-padding-remove uk-text-left wk-nav wk-nav-dropdown wk-padding-remove wk-text-left">
                                            <li>
                                                <a href="#" onclick="newMedia('video');">
                                                    <?php echo JText::_("GURU_VIDEO"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('audio');">
                                                    <?php echo JText::_("GURU_AUDIO"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('docs');">
                                                    <?php echo JText::_("GURU_DOCS"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('url');">
                                                    <?php echo JText::_("GURU_URL"); ?>
                                                </a>
                                            </li>
                                            <!--
                                            <li>
                                                <a href="#" onclick="newMedia('article');">
                                                    <?php echo JText::_("GURU_ARTICLE"); ?>
                                                </a>
                                            </li>
                                            -->
                                            <li>
                                                <a href="#" onclick="newMedia('image');">
                                                    <?php echo JText::_("GURU_IMAGE"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('zoom');">
                                                    <?php echo JText::_("GURU_ZOOM_MEETING"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('text');">
                                                    <?php echo JText::_("GURU_text"); ?>
                                                </a>
                                            </li>
                                            
                                            <li>
                                                <a href="#" onclick="newMedia('file');">
                                                    <?php echo JText::_("GURU_MEDIATYPEFILE_"); ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                   <?php }?>
                    <button class="uk-button wk-button" onclick="duplicateMedia();"><?php echo JText::_('GURU_DUPLICATE'); ?></button>
                    <button class="uk-button uk-button-danger wk-button wk-button-danger" onclick="deleteAuthorMedia();"><?php echo JText::_('GURU_DELETE'); ?></button>
                </div>
            </div>

            <div class="uk-width-1-2 uk-visible-small wk-width-1-2 wk-visible-small">
                <div class="uk-button-group wk-button-group">
                   <?php if($teacher_add_media == 0){?>
                                <div class="uk-button-dropdown wk-button-dropdown no-padding uk-width-1-1 wk-width-1-1" data-uk-dropdown="{mode:'click'}">                
                                    <!-- This is the button toggling the dropdown -->
                                    <button class="uk-button uk-button-success wk-button wk-button-success"><?php echo JText::_('GURU_DAY_NEW_MEDIA'); ?>&nbsp;<span class="fa fa-caret-down"></span></button>
                                    <div class="uk-dropdown uk-dropdown-small wk-dropdown wk-dropdown-small">
                                        <ul class="uk-nav uk-nav-dropdown uk-padding-remove wk-nav wk-nav-dropdown wk-padding-remove">
                                            <li>
                                                <a href="#" onclick="newMedia('video');">
                                                    <?php echo JText::_("GURU_VIDEO"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('audio');">
                                                    <?php echo JText::_("GURU_AUDIO"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('docs');">
                                                    <?php echo JText::_("GURU_DOCS"); ?>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="#" onclick="newMedia('url');">
                                                    <?php echo JText::_("GURU_URL"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('image');">
                                                    <?php echo JText::_("GURU_IMAGE"); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="newMedia('text');">
                                                    <?php echo JText::_("GURU_text"); ?>
                                                </a>
                                            </li>
                                            
                                            <li>
                                                <a href="#" onclick="newMedia('file');">
                                                    <?php echo JText::_("GURU_MEDIATYPEFILE_"); ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                   <?php }?>
                    <button class="uk-button uk-width-1-1 wk-button wk-width-1-1" onclick="duplicateMedia();"><?php echo JText::_('GURU_DUPLICATE'); ?></button>
                    <button class="uk-button uk-button-danger uk-width-1-1 wk-button wk-button-danger wk-width-1-1" onclick="deleteAuthorMedia();"><?php echo JText::_('GURU_DELETE'); ?></button>
                </div>
            </div>
        </div>
            
    <?php } ?>
    
    <div class="gru-mymediaauthorcontent">
        <form  action="<?php echo JUri::getInstance() ?>" class="form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
            <?php
                if($page_from != 'question' && $tmpl_comp !='component'){
            ?>
                    <div class="gru-page-filters">
                        <div class="gru-filter-item">
                            <?php echo $filters->status; ?>
                        </div>
                        
                        <div class="gru-filter-item">
                            <?php echo $filters->type; ?>
                        </div>
                        
                        <div class="gru-filter-item">
                            <input type="text" class="form-control" name="search_media" id="filter_search" value="<?php if(isset($data_post['search_media'])) echo $data_post['search_media'];?>" class="uk-form-width-medium wk-form-width-medium" />
                            <button class="uk-button uk-button-primary wk-button wk-button-primary" type="submit"><?php echo JText::_("GURU_SEARCH"); ?></button>
                        </div>
                    </div>
            <?php
                }
            ?>
           
           <div class="clearfix"></div>
           
            <table class="uk-table uk-table-striped wk-table wk-table-striped">
                <tr>
                     <?php
                        if($page_from != 'question' && $tmpl_comp !='component'){?>
                            <th class="g_cell_1"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                    <?php }?>
                    
                    <?php
                        if($page_from != 'question' && $tmpl_comp !='component'){
                    ?>
                    <th class="g_cell_2 hidden-phone"><?php echo JText::_('GURU_ID'); ?></th>
                    <?php
                        }
                    ?>
                    <th class="g_cell_3"><?php echo JText::_('GURU_NAME'); ?></th>
                    <th class="g_cell_4 hidden-phone"><?php echo JText::_("GURU_TYPE"); ?></th>
                    <?php
                        if($page_from != 'question' && $tmpl_comp !='component'){
                    ?>
                    <th class="g_cell_6 hidden-phone"><?php echo JText::_("GURU_PREVIEW"); ?></th>
                    <?php
                        }
                    ?>
                    <th class="g_cell_7"><?php echo JText::_("GURU_PROGRAM_DETAILS_STATUS"); ?></th>
                </tr>
                                    
                           
                <?php 
                $n =  count($my_media);
                for ($i = 0; $i < $n; $i++):
                    $id = $my_media[$i]->id;
                    $checked = JHTML::_('grid.id', $i, $id);
                   
                    $published = JHTML::_('grid.published', $my_media, $i );
                    $alias = isset($my_media[$i]->alias) ? trim($my_media[$i]->alias) : JFilterOutput::stringURLSafe($my_media[$i]->name);
                ?>
                    <?php
                       switch ($my_media[$i]->type) {
                            case "video": $class = "fa fa-video-camera";                                
                                break;
                            case "audio": $class = "fa fa-play-circle";                             
                                break;
                            case "docs": $class = "fa fa-folder-open";                              
                                break;
                            case "quiz": $class = "fa fa-question";                             
                                break;
                            case "url": $class = "fa fa-link";                              
                                break;
                            case "Article": $class = "fa fa-file-text";                             
                                break;                                          
                            case "image": $class = "fa fa-picture-o";                               
                                break;
                            case "text": $class = "fa fa-book";                             
                            break;  
                            case "file": $class = "fa fa-file";                             
                                break;       
                                
                            case 'zoom': $class = "fa fa-users";
                            break;
                        }
                     ?>
                    <tr class="guru_row">
                         <?php if($page_from != 'question' && $tmpl_comp !='component'){?>
                                <td class="g_cell_1"><?php echo $checked;?></td>
                         <?php }?>
                        <?php
                            if($page_from != 'question' && $tmpl_comp !='component'){
                        ?>
                         <td class="g_cell_2 hidden-phone"><?php echo $id;?></td>
                        <?php
                            }
                        ?>
                         <td class="guru_product_name g_cell_3">
                         <?php 
                         if($teacher_edit_media == 0){
                         ?>
                         <?php 
                            if($page_from != 'question' && $tmpl_comp != 'component'){?>
                                <a href="<?php echo JRoute::_("index.php?option=com_guru&view=guruauthor&task=editMedia&cid=".$id."-".$alias."&Itemid=".$Itemid); ?>"><?php echo $my_media[$i]->name; ?></a>

                            <?php 
                            }
                            else{
                                if($page_from == 'question'){
                            ?>
                                    <a class="a_guru" href="#" onclick="putMediaToQuestion('<?php echo $id; ?>', '<?php echo addslashes($my_media[$i]->name); ?>', '<?php echo $class;?>'); return false;" ><?php echo $my_media[$i]->name;?></a>
                                <?php
                                }
                                else{
                                    $radio_value = JFactory::getApplication()->input->get("radio_value", "0");
                                ?>
                                    <a class="a_guru" href="#" onclick="putMediaToAnswer('<?php echo intval($radio_value); ?>', '<?php echo $id; ?>', '<?php echo addslashes($my_media[$i]->name); ?>','<?php echo $class;?>'); return false;" ><?php echo $my_media[$i]->name;?></a>
                                <?php
                                }
                            }
                            ?>
                         <?php
                         }
                         else{
                            echo $my_media[$i]->name;
                         }
                         ?>
                         </td>
                        
                        <td class="g_cell_4 hidden-phone">
                             <i class="<?php echo $class; ?>"></i>
                        </td>
                        <?php
                            if($page_from != 'question' && $tmpl_comp !='component'){
                        ?>
                        <td class="g_cell_6 hidden-phone">
                            <?php
                                $width = "700";
                                $height = "530";
                                
                                if($my_media[$i]->type == "video"){
                                    $width = "850";
                                    $height = "615";
                                }
                                elseif($my_media[$i]->type == "audio"){
                                    $width = "400";
                                    $height = "200";
                                }
                                elseif($my_media[$i]->type == "docs"){
                                    $width = "700";
                                    $height = "530";
                                }
                                elseif($my_media[$i]->type == "url"){
                                    if($my_media[$i]->width == 1){ // link
                                        $width = "400";
                                        $height = "200";
                                    }
                                    else{ // wrap
                                        $width = "700";
                                        $height = "530";
                                    }
                                }
                                elseif($my_media[$i]->type == "image"){
                                    $width = "700";
                                    $height = "530";
                                }
                                elseif($my_media[$i]->type == "text"){
                                    $width = "700";
                                    $height = "530";
                                }
                                elseif($my_media[$i]->type == "file"){
                                    $width = "400";
                                    $height = "200";
                                }
                            ?>
                            <a class="hidden-phone" onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=preview&tmpl=component&id=<?php echo $my_media[$i]->id;?>'); return false;" href="#">
                                <?php echo JText::_('GURU_MEDIA_PREVIEW_LOWER');?>
                            </a>
                            
                            <a class="uk-hidden-large uk-hidden-medium wk-hidden-large wk-hidden-medium" onclick="javascript:openMyModal(0, 0, '<?php echo JURI::root(); ?>index.php?option=com_guru&controller=guruAuthor&task=preview&tmpl=component&id=<?php echo $my_media[$i]->id;?>'); return false;" href="#">
                                <i class="fa fa-search-plus"></i>
                            </a>
                        </td>
                        <?php
                            }
                        ?>
                        <td class="g_cell_7">    
                            <?php
                                if($my_media[$i]->published == 0){
                                    echo '<i class="fa fa-times-circle"></i>';
                                }
                                else{
                                    echo '<i class="fa fa-check-circle-o"></i>';
                                }
                            ?>
                        </td>
                     </tr>   
                <?php 
                    endfor;
                    ?>  
              </table>
           
           <?php
                echo $this->pagination->getLimitBox();
                $pages = $this->pagination->getPagesLinks();
                include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."helper.php");
                $helper = new guruHelper();
                $pages = $helper->transformPagination($pages);
                echo $pages;
            ?>
            <?php
                if($page_from == 'question' && $tmpl_comp =='component'){
            ?>
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="page_from" value="question" />
            <?php
                }
                elseif($page_from == 'answers' && $tmpl_comp =='component'){
                ?>
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="page_from" value="answers" />
                    <input type="hidden" name="radio_value" value="<?php echo $radio_value; ?>" />
                <?php    
                }
            ?>
            <input type="hidden" name="task" value="authormymedia" />
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="controller" value="guruAuthor" />
            <input type="hidden" name="boxchecked" value="" />
            <input type="hidden" name="selected_item_New" value="" />
        </form>
        
   </div>  
</div>
<script type="text/javascript">
    //$doc->addScript('components/com_guru/js/guru_modal_commissions.js');
    //$doc->addScript('components/com_guru/js/guru_modal.js');
    (function() {
      var x = document.createElement("script"); x.type = "text/javascript";
      x.src = "components/com_guru/js/guru_modal_commissions.js";
      var y = document.createElement("script"); y.type = "text/javascript";
      y.src = "components/com_guru/js/guru_modal.js";

      var a = document.getElementById("js-cpanel");

      a.appendChild(x, a);
      a.appendChild(y, a);
    })();

</script>                    
<script language="javascript">
    var first = false;
    function showContentVideo(href){
        first = true;
        jQuery( '#myModal .modal-body iframe').attr('src', href);
    }

    function closeModal(){
        jQuery('#myModal .modal-body iframe').attr('src', '');
    }

    jQuery('#myModal').on('hide', function () {
        jQuery('#myModal .modal-body iframe').attr('src', '');
    });

    if(!first){
        jQuery('#myModal .modal-body iframe').attr('src', '');
    }
    else{
        first = false;
    }
</script>