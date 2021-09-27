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
    $pid = JFactory::getApplication()->input->get("pid", "");
    $doc =JFactory::getDocument();
    //These scripts are already been included from the administrator\components\com_guru\guru.php file
//$doc->addScript('components/com_guru/js/jquery.DOMWindow.js');
?>
<style>
    div.modal1 {
        left: 4% !important;
        width: 770px !important;
        top:6%!important;
        position: fixed;
        z-index: 9999;
}

.modal-backdrop, .modal-backdrop.fade.in {
    opacity: 0.4 !important;
}


</style>
<script language="javascript" type="text/javascript">
    
    
    function validateForm(){
        value = document.adminForm.title.value;
        if(value == ""){
            alert("<?php echo JText::_("GURU_TASKS_JS_NAME"); ?>");
            return false;
        }
        else{
            return true;
        }
    }
    
    function deleteMedia(div_id, hidden_id){
        document.getElementById(div_id).innerHTML = "";
        document.getElementById(hidden_id).value="";
        document.getElementById("after_menu_med_1").style.display = "none";
    }
    function showContent1(href){
        jQuery( '#myModal1 .modal-body iframe').attr('src', href);
    }

            
</script>
<link rel="StyleSheet" href="components/com_guru/css/guru-j30.css" type="text/css"/>

<script type="text/javascript" src="<?php echo JURI::root(); ?>plugins/content/jw_allvideos/jw_allvideos/includes/js/wmvplayer/silverlight.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>plugins/content/jw_allvideos/jw_allvideos/includes/js/wmvplayer/wmvplayer.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>plugins/content/jw_allvideos/jw_allvideos/includes/js/quicktimeplayer/AC_QuickTime.js"></script> 


<div class="guru-modal-content">
    <form method="post" name="adminForm" id="adminForm" onsubmit="return validateForm();">
        <div id="myModal1" class="modal1 hide" style="">
        <div class="modal-header">
            <button type="button" id="close" class="close" data-dismiss="modal" aria-hidden="true">x</button>
         </div>
         <div class="modal-body" style="background-color:#FFFFFF;" >
            <iframe height="415" width="700" frameborder="0"></iframe>
        </div>
    </div>

    <fieldset class="adminform">
    <div class="well"><?php if ((isset($program->id)) && ($program->id < 1)) echo JText::_('GURU_NEWDAY'); else echo JText::_('GURU_NEWDAY');?></div>
    <table>
        <tr>
            <td>
                <?php echo JText::_("GURU_DAYNAME"); ?><font color="#FF0000">*</font> :
            </td>
        </tr>
        <tr>
            <td>
                <input class="input-module" type="text" name="title" id="title" value="" size="50" />
            </td>
        </tr>       
        <tr>
            <td>
                <table>
                    <tr>                        
                        <td style="text-align:left;" id="menu_med_1">
                            <div id="before_menu_med_1" style="display:none"></div>                         
                            <div>
                                <a data-toggle="modal" data-target="#myModal1" onClick = "showContent1('index.php?option=com_guru&controller=guruTasks&task=addmedia&tmpl=component&cid[]=0&med=1&action=new_module');" href="#"><?php echo JText::_("GURU_MEDIA_SEARCH"); ?></a>               
                            or
                                <a data-toggle="modal" data-target="#myModal1" onClick = "showContent1('index.php?option=com_guru&controller=guruTasks&task=addtext&tmpl=component&cid[]=0&txt=1&action=new_module');" href="#"><?php echo JText::_("GURU_TEXT_SEARCH"); ?></a>
                            </div>
                            <div id="media_1" style="float:left; margin-top:5px;">
                            </div>
                            <div id="text_1" style="float:left; margin-top:5px;">
                            </div>
                            <div id="after_menu_med_1" align="right" style="display:none; float:left; margin-top:7px; margin-left:5px;"><img src="<?php echo JURI::root()."administrator/components/com_guru/images/delete.gif" ?>" title="Remove this media!" alt="Remove" onclick="javascript:deleteMedia('media_1', 'db_media_1');"></img></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td style="padding-bottom: 10px;">
                                        <?php echo JText::_("GURU_ACCESS"); ?>
                                    </td>
                                    <td>
                                        <select name="access">
                                            <option value="0"><?php echo JText::_("GURU_COU_STUDENTS"); ?></option>
                                            <option value="1"><?php echo JText::_("GURU_REG_MEMBERS"); ?></option>
                                            <option value="2"><?php echo JText::_("GURU_REG_GUESTS"); ?></option> 
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>   
                    </tr>
                </table>
            </td>
        </tr>               
        <tr>
            <td> 
                <a id="close_gb" style="display:none;">#</a>
                <input class="btn" type="submit" name="submit" value="<?php echo JText::_("GURU_SAVE"); ?>" />
            </td>
        </tr>
    </table>
    </fieldset>

    <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
    <input type="hidden" name="task" value="save_new_module" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruDays" />
    <input type="hidden" name="db_media_1" id="db_media_1" value="" />
</form>
</div>
