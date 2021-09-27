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
jimport( 'joomla.html.pagination' );
JHTML::_('behavior.tooltip');
JHtml::_('behavior.framework');

jimport('joomla.html.pane');
$doc =JFactory::getDocument();


$db = JFactory::getDBO();

$sql ="select count(extension_id) from #__extensions WHERE element='ijoomlagurudiscussbox'";
$db->setQuery($sql);
$db->execute();
$plugin_installed = $db->loadResult();

$sql ="select enabled from #__extensions WHERE element='ijoomlagurudiscussbox'";
$db->setQuery($sql);
$db->execute();
$enabled = $db->loadResult();

$sql ="select count(extension_id) from #__extensions WHERE name='com_kunena'";
$db->setQuery($sql);
$db->execute();
$com_kunena = $db->loadResult();

$sql ="select enabled from #__extensions WHERE name='com_kunena'";
$db->setQuery($sql);
$db->execute();
$enabled_comkunena = $db->loadResult();

if($plugin_installed ==1){
	if($enabled == 0){
		$disabled = "disabled";
		$div = 1;
	}
	else{
		$disabled = "";
		$div = 0;
	
	}
}
else{
	$disabled = "disabled";
	$div = 2;
}

if($enabled_comkunena >= 1){
	if($com_kunena >= 1){
		$disabled1 = "";
		$div3 =1;
	}
	else{
		$disabled1 = "disabled";
		$div3 =0;
	}
}
else{
	$disabled1 = "disabled";
	$div3 = 0;
}


$kunenaforum_details = $this->kunenaforum_details;
$kunena_categories = $this->kunena_categories;

?>

<style>
	#rowsmedia {
		background-color:#eeeeee;
	}
	#rowsmedia tr{
		background-color:white;
	}
	#rowsmainmedia {
		background-color:#eeeeee;
	}
	#rowsmainmedia tr{
		background-color:#eeeeee;
	}
	.adminformCertificate{
	
		background-color: #FFFFFF;
		border: 1px solid #D5D5D5;
		border-collapse: collapse;
		margin: 8px 0 15px;
		width: 50%;
	}
</style>
<div id="g_kunena_forum">
    <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('GURU_KUNENA_FORUM1');?></h5></div>
    <div class="widget-body">
        <div class="widget-main"> 
            <form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <div class="gurutab-content">
                    <table class="adminform">
                    <tr>
                        <td colspan="4">
                          <div style=" background-color:#F7F7F7; height:15px; padding-left:7px;"><b><?php echo JText::_('GURU_KUNENA_SET');?></b></div>
                         </td> 
                    </tr>
                     <tr>
                            <td width="25%"; colspan="2">
                    <?php 
                        if($div3 == 0){
                            echo'<div style="background-color:#C3D2E5; border:solid 1px #84A7DB; padding-left:15px;">'.JText::_('GURU_KUNENA_COM1').' <a target="_blank" href = "http://www.ijoomla.com/redirect/guru/download_kunena.htm">'. JText::_('GURU_HERE') .'</a>'." ".JText::_('GURU_KUNENA_COM2').'</div>';
                        }
                    ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_AUTO_FORUMB');?>
                        </td>
                        <td>
                         <input type="hidden" name="autoforumk" value="0">
						<?php
                            $checked = '';
                            if($kunenaforum_details->forumboardcourse == "1"){
                                $checked = 'checked="checked"';
                            }
                        ?>
                        <input <?php echo $disabled1; ?>  type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="autoforumk">
                        <span class="lbl"></span>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_AUTO_FORUMB"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                     <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_AUTO_FORUMB1');?>
                        </td>
                        <td>
                        <input type="hidden" name="autoforumk1" value="0">
						<?php
                            $checked = '';
                            if($kunenaforum_details->forumboardlesson == "1"){
                                $checked = 'checked="checked"';
                            }
                        ?>
                        <input <?php echo $disabled1; ?>  type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="autoforumk1">
                        <span class="lbl"></span>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_AUTO_FORUMB1"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                     <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_AUTO_FORUMB2');?>
                        </td>
                        <td>
                        <input type="hidden" name="autoforumk2" value="0">
						<?php
                            $checked = '';
                            if($kunenaforum_details->forumboardteacher == "1"){
                                $checked = 'checked="checked"';
                            }
                        ?>
                        <input  <?php echo $disabled1; ?>  type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="autoforumk2">
                        <span class="lbl"></span>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_AUTO_FORUMB2"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    
                    </tr>
                    <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_DELETED_BOARDS');?>
                        </td>
                        <td>
                            <select <?php echo $disabled1; ?> id="deleted_boards" name="deleted_boards" style="float:left !important;" >
                                            <option value="0" <?php if($kunenaforum_details->deleted_boards == "0"){echo 'selected="selected"'; } ?>><?php  echo JText::_("GURU_KEEPB"); ?></option>
                                            <option value="1" <?php if($kunenaforum_details->deleted_boards == "1"){echo 'selected="selected"'; } ?>><?php  echo JText::_("GURU_DELETEB"); ?></option>
                                            <option value="2" <?php if($kunenaforum_details->deleted_boards == "2"){echo 'selected="selected"'; } ?>><?php  echo JText::_("GURU_UNPUB"); ?></option>
                
                            </select>
                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_DELETED_BOARDS"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_CREATE_KUNENA_CATEG');?>
                        </td>
                        <td>
                            <select id="kunena-category" name="kunena_category" style="float:left !important;" >
                                <option value="0" <?php if($kunenaforum_details->kunena_category == "0"){echo 'selected="selected"'; } ?>>
                                    <?php echo JText::_("GURU_KUNENA_COURSES_CATEG"); ?>
                                </option>
                                <?php
                                    if(isset($kunena_categories) && count($kunena_categories) > 0){
                                        foreach($kunena_categories as $key=>$category){
                                            for($i=0; $i<$category->level; $i++){
                                                $category->name = " - ".$category->name;
                                            }
                                ?>
                                            <option value="<?php echo intval($category->id); ?>" <?php if($kunenaforum_details->kunena_category == intval($category->id)){echo 'selected="selected"'; } ?>>
                                                <?php echo $category->name; ?>
                                            </option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                            &nbsp;
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CREATE_KUNENA_CATEG"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="4">
                          <div style=" background-color:#F7F7F7; height:15px; padding-left:7px;"><b><?php echo JText::_('GURU_KUNENAL_SET');?></b></div>
                         </td> 
                    </tr>
                    <tr>
                            <td width="25%"; colspan="2">
                    <?php 
                        if($div == 1){
                            echo'<div style="background-color:#C3D2E5; border:solid 1px #84A7DB; padding-left:15px;">'.JText::_('GURU_KUNENA_PLG_DIS1').'</div><br/>';
                        }
                        elseif($div == 2){
                            echo'<div style="background-color:#C3D2E5; border:solid 1px #84A7DB; padding-left:15px;">'.JText::_('GURU_KUNENA_PLG_DIS2').'</div><br/>';
                        }
                    ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_ALLOW_STUD_COM');?>
                        </td>
                        <td>
                        <input type="hidden" name="allow_stud" value="1">
						<?php
                            $checked = '';
                            if($kunenaforum_details->allow_stud == "0"){
                                $checked = 'checked="checked"';
                            }
                        ?>
                        <input <?php echo $disabled; ?> type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="allow_stud">
                        <span class="lbl"></span>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ALLOW_STUD_COM"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_ALLOW_STUD_COM_EDIT');?>
                        </td>
                        <td>
                        <input type="hidden" name="allow_edit" value="1">
						<?php
                            $checked = '';
                            if($kunenaforum_details->allow_edit == "0"){
                                $checked = 'checked="checked"';
                            }
                        ?>
                        <input <?php echo $disabled; ?> type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="allow_edit">
                        <span class="lbl"></span>
                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ALLOW_STUD_COM_EDIT"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            <?php echo JText::_('GURU_ALLOW_STUD_COM_DELETE');?>
                        </td>
                        <td>
                            <input type="hidden" name="allow_delete" value="1">
                            <?php
                                $checked = '';
                                if($kunenaforum_details->allow_delete == "0"){
                                    $checked = 'checked="checked"';
                                }
                            ?>
                            <input <?php echo $disabled; ?> type="checkbox" <?php echo $checked; ?> value="0" class="ace-switch ace-switch-5" name="allow_delete">
                            <span class="lbl"></span>    
                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ALLOW_STUD_COM_DELETE"); ?>" >
                                <img border="0" src="components/com_guru/images/icons/tooltip.png">
                            </span>
                        </td>
                    </tr>
                    </table>
            </div>
                <input type="hidden" name="option" value="com_guru" />
                <input type="hidden" name="task" value="savekunenadetails" />
                <input type="hidden" name="controller" value="guruKunenaForum" />
                <input type="hidden" name="id" value="0" />	
            </form>
        </div>
      </div>
</div>            