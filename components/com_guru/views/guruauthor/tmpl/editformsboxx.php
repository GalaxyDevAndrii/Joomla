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

defined('_JEXEC') or die("Go away.");

$mediaval1	= JFactory::getApplication()->input->get("med", "");
$mediaval2	= JFactory::getApplication()->input->get("txt", "");
$scr		= JFactory::getApplication()->input->get("scr", "0");
$txt		= JFactory::getApplication()->input->get("txt", "0");
$cid = JFactory::getApplication()->input->get("cid", "0");

$data 	= $this->data;
$_row	= $this->media;
$lists 	= $_row->lists;

$nullDate = 0;
$livesite = JURI::base();
$configuration = $this->getConfigsObject();
$editorul = new JEditor(JFactory::getConfig()->get("editor"));

$UPLOAD_MAX_SIZE = guruAdminHelper::getUploadMaxSize();

$maxUpload = "<font color='#FF0000'>";
$maxUpload .= JText::_('GURU_MEDIA_MAX_UPL_V_1') . " ";
$maxUpload .= $UPLOAD_MAX_SIZE . 'M ';
$maxUpload .= JText::_('GURU_MEDIA_MAX_UPL_V_2');
$maxUpload .= "</font>";

$doc = JFactory::getDocument();
include(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_guru' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'createUploader.php');
JHtml::_('behavior.framework');
?>

<!-- <script type="text/javascript" language="javascript">
            document.body.className = document.body.className.replace("modal", "");
        </script> -->

<!-- <script type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/views/gurumedia/tmpl/js.js"></script> -->
<!-- <style type="text/css">
            .redactor_box {
                width:60%;
            }
        </style> -->

<?php
$document = JFactory::getDocument();
$action = JFactory::getApplication()->input->get("action", "");
?>

<form action="<?php echo JUri::root() ?>index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="uk-form uk-form-horizontal uk-margin-left uk-margin-right wk-form wk-form-horizontal wk-margin-left wk-margin-right">

    <div class="uk-grid uk-margin-top wk-grid wk-margin-top">
        <div class="uk-width-1-1 wk-width-1-1">
            <input class="uk-button uk-button-success wk-button wk-button-success" border="0" type="button" name="savesbox" id="savesbox" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onClick="javascript:submitbutton('savesbox');" />
        </div>
    </div>

    <h2 class="gru-page-title"><?php if (isset($_row->id)) {
                                    echo  JText::_('GURU_DAY_EDIT_MEDIA');
                                } else {
                                    echo  JText::_('GURU_DAY_NEW_MEDIA');
                                } ?></h2>


    <?php
    if ($cid > 0) {
    ?>
        <input type="hidden" name="type" value="text" />
        <?php
    } else {
        if ($action == "addtext") {
            $_row->type = 'text';
            echo '<input type="hidden" name="type" value="text" />';
        } else {
            if ($_row->type != 'text') {
        ?>
                <div class="uk-form-row wk-form-row" id="auto_play" style="display:<?php echo @$auto_play_display; ?>">
                    <label class="uk-form-label wk-form-label" for="name">
                        <?php echo JText::_("GURU_TYPE"); ?>:
                        <span class="uk-text-danger wk-text-danger">*</span>
                    </label>
                    <div class="uk-form-controls wk-form-controls">
                        <?php
                        echo $lists['type'];
                        ?>
                    </div>
                </div>
    <?php
            }
        }
    }
    ?>

    <div class="uk-form-row wk-form-row">
        <label class="uk-form-label wk-form-label" for="name">
            <?php echo JText::_("Name"); ?>:
            <span class="uk-text-danger wk-text-danger">*</span>
        </label>
        <div class="uk-form-controls wk-form-controls">
            <input class="formField" type="text" name="name" size="60" value="<?php echo str_replace('"', "&quot;", $_row->name); ?>">
        </div>
    </div>

    <div class="uk-form-row wk-form-row" id="auto_play" style="display:<?php echo @$auto_play_display; ?>">
        <label class="uk-form-label wk-form-label" for="name">
            <?php echo JText::_("GURU_INSTR"); ?>:
        </label>
        <div class="uk-form-controls wk-form-controls">
            <textarea class="formField" type="text" name="instructions" rows="2" cols="60"><?php echo stripslashes($_row->instructions); ?></textarea>
        </div>
    </div>

    <div class="uk-form-row wk-form-row" id="auto_play" style="display:<?php echo @$auto_play_display; ?>">
        <label class="uk-form-label wk-form-label" for="name">
            <?php echo JText::_("GURU_SHOW_INSTRUCTION"); ?>:
        </label>
        <div class="uk-form-controls wk-form-controls">
            <select name="show_instruction" id="show_instruction">
                <option value="0" <?php if ($_row->show_instruction == "0") {
                                        echo 'selected="selected"';
                                    } ?>><?php echo JText::_("GURU_SHOW_ABOVE"); ?></option>
                <option value="1" <?php if ($_row->show_instruction == "1") {
                                        echo 'selected="selected"';
                                    } ?>><?php echo JText::_("GURU_SHOW_BELOW"); ?></option>
                <option value="2" <?php if ($_row->show_instruction == "2") {
                                        echo 'selected="selected"';
                                    } ?>><?php echo JText::_("GURU_DONT_SHOW"); ?></option>
            </select>
        </div>
    </div>

    <div class="uk-form-row wk-form-row" id="auto_play" style="display:<?php echo @$auto_play_display; ?>">
        <label class="uk-form-label wk-form-label" for="name">
            <?php echo JText::_("GURU_MEDIATYPETEXT"); ?>:
            <span class="uk-text-danger wk-text-danger">*</span>
        </label>
        <div class="uk-form-controls wk-form-controls">
            <?php
            $doc = JFactory::getDocument();
            $doc->addStyleSheet(JURI::root() . 'components/com_guru/css/redactor.css');
            ?>
            <textarea id="text" name="text" class="useredactor" style="width:70%; height:100px;"><?php echo $_row->code; ?></textarea>


        </div>
    </div>

    <div class="uk-grid uk-margin-top wk-grid wk-margin-top">
        <div class="uk-width-1-1 wk-width-1-1">
            <input class="uk-button uk-button-success wk-button wk-button-success" border="0" type="button" name="savesbox" id="savesbox" value="<?php echo JText::_("GURU_SAVE_AND_CLOSE"); ?>" onClick="javascript:submitbutton('savesbox');" />
        </div>
    </div>

    <?php
    $action = JFactory::getApplication()->input->get("action", "addmedia");
    ?>
    <input type="hidden" name="action" value="<?php echo $action; ?>" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="id" value="<?php echo $_row->id; ?>" />
    <input type="hidden" name="mediatext" value="<?php

                                                    if ($mediaval1 != "") {
                                                        echo "med";
                                                    } elseif ($mediaval2 != "") {
                                                        echo "txt";
                                                    }
                                                    ?>" id="mediatext" />
    <input type="hidden" name="mediatextvalue" value="<?php
                                                        if ($mediaval1 != "") {
                                                            echo $mediaval1;
                                                        } elseif ($mediaval2 != "") {
                                                            echo $mediaval2;
                                                        }
                                                        ?>" id="mediatextvalue" />
    <input type="hidden" name="controller" value="guruAuthor" />
    <input type="hidden" name="screen" id="screen" value="<?php echo $scr; ?>" />
    <?php
    $user = JFactory::getUser();
    ?>
    <input type="hidden" name="author" value="<?php echo intval($user->id); ?>" />

    <script type="text/javascript">
        var currentURL = window.location;
        document.write('<input type="hidden" name="redirect_to" value="' + currentURL.href + '" />');
    </script>
</form>

<script language="javascript" type="text/javascript" src="<?php echo JURI::root() . 'components/com_guru/js/redactor.min.js'; ?>"></script>

<script type="text/javascript" language="javascript">
    //jQuery( document ).ready(function(){
    jQuery(".useredactor").redactor({
        buttons: ['bold', 'italic', 'underline', 'link', 'alignment', 'unorderedlist', 'orderedlist']
    });
    jQuery(".redactor_useredactor").css("height", "400px");
    //});
</script>