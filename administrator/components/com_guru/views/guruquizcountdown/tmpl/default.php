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

$app = JFactory::getApplication();
$app->redirect(JURI::root()."administrator/index.php?option=com_guru");

JHtml::_('behavior.framework');

JHTML::_('behavior.tooltip');

	$lists = $this->lists;

	

?>

<script language="JavaScript" type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/js/freecourse.js"></script>

<script language="JavaScript" type="text/javascript" src="<?php echo JURI::base(); ?>components/com_guru/js/colorpicker.js"></script>

<script type="text/javascript" language="javascript">

	function isFloat(nr){

		return parseFloat(nr.match(/^-?\d*(\.\d+)?$/)) > 0;

	}

	

	function validateColor(color){

		if(color == ''){

			return true

		}

		if(/^[0-9A-F]{6}$/i.test(color)){

			return true;

		}

		return false;

	}

	

	Joomla.submitbutton = function(pressbutton){

		if(pressbutton == 'save' || pressbutton == 'apply'){

			st_width = document.getElementById("st_width").value;

			st_height = document.getElementById("st_height").value;

			border_color = document.getElementById("pick_donecolorfield").value; 

			minandsec_color = document.getElementById("pick_notdonecolorfield").value;

			title_color = document.getElementById("pick_txtcolorfield").value;

			background_color = document.getElementById("pick_xdonecolorfield").value; 

			

			if(!isFloat(st_width) || st_width <= 0){

				alert("<?php echo JText::_("GURU_ALERT_INVALID_WIDTH"); ?>");

				return false;

			}

			else if(!isFloat(st_height) || st_height <= 0){

				alert("<?php echo JText::_("GURU_ALERT_INVALID_HEIGHT"); ?>");

				return false;

			}

			else if(!validateColor(border_color)){

				alert("<?php echo JText::_("GURU_ALERT_INVALID_COLOR"); ?>");

				return false;

			}

			else if(!validateColor(minandsec_color)){

				alert("<?php echo JText::_("GURU_ALERT_INVALID_COLOR"); ?>");

				return false;

			}

			else if(!validateColor(title_color)){

				alert("<?php echo JText::_("GURU_ALERT_INVALID_COLOR"); ?>");

				return false;

			}

			else if(!validateColor(background_color)){

				alert("<?php echo JText::_("GURU_ALERT_INVALID_COLOR"); ?>");

				return false;

			}

		}

		submitform( pressbutton );

	}

</script>

    <style>

	input, textarea {

    	width: 135px!important;

    }

	select {

  	 width: 150px!important;

	}



</style>

<div id="g_countdown">

    <form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">

        <div class="well well-minimized">

            <?php echo JText::_("GURU_QUIZC_SETTINGS_DESCRIPTION"); ?>

        </div>

        <?php 

            $db = JFactory::getDBO();

            $sql = "SELECT  qct_alignment, qct_border_color, qct_minsec, qct_title_color, qct_bg_color, qct_font , qct_width,  qct_height, qct_font_nb, qct_font_words  FROM  #__guru_config WHERE id=1";

            $db->setQuery($sql);

            $db->execute();

            $result=$db->loadObjectList();

            

            ?>

    <div id="guru_quizmanaging_options" class="adminform span7">	

         <div class="row-fluid">

                <div class="span12"> 

                    <div class="row-fluid">

                        <div class="span8">

                            <div class="row-fluid">

                                <div class="control-group">

                                    <label class="control-label" for="layout"><?php echo JText::_('GURU_ALIGN');?>		

                                  </label>

                                      <div class="controls">

                                        <div class="pull-left">

                                            <select id="timer_alignement" name="timer_alignement">

                                                <option value="1" <?php if($result[0]->qct_alignment == "1"){echo 'selected="selected"'; }?> ><?php echo JText::_("GURU_LEFT"); ?></option>

                                                <option value="2" <?php if($result[0]->qct_alignment == "2"){echo 'selected="selected"'; }?> ><?php echo JText::_("GURU_RIGHT"); ?></option>

                                                <option value="3" <?php if($result[0]->qct_alignment == "3"){echo 'selected="selected"'; }?> ><?php echo JText::_("GURU_CENTER"); ?></option>

                                            </select>

                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CT_ALIGNEMENT"); ?>" >

                                                <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                            </span>

                                        </div>

                                    </div>

                                </div>										

                            </div>

                        </div>							

                    </div>

                </div>						

            </div>

            <div class="row-fluid">

                <div class="span12"> 

                    <div class="row-fluid">

                        <div class="span8">

                            <div class="row-fluid">

                                <div class="control-group">

                                    <label class="control-label" for="layout"><?php echo JText::_('GURU_BORDER_COLOR');?>		

                                  </label>

                                      <?php $st_donecolor = "#".$result[0]->qct_border_color;?>

                                      <div class="controls">

                                        <div>

                                            <div style="float:left;">

                                                <input type="text" size="7" name="st_donecolor" ID="pick_donecolorfield" value="<?php echo substr($st_donecolor, 1, strlen($st_donecolor));?>" onChange="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_donecolor', this.value);}" size="6" maxlength="6" onkeyup="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_donecolor', this.value);}" />

                                                &nbsp;

                                                <a href="javascript:pickColor('pick_donecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_donecolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">

                                                &nbsp;&nbsp;&nbsp;

                                                </a>

                                                <SCRIPT LANGUAGE="javascript">relateColor('pick_donecolor', getObj('pick_donecolorfield').value);</script>

                                                &nbsp;&nbsp;&nbsp;

                                            </div>

                                            <div style="float:left;">

                                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CT_BORDERCOLOR"); ?>" >

                                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                </span>

                                            </div>                    

                                        </div>

                                    </div>

                                </div>										

                            </div>

                        </div>							

                    </div>

                </div>						

            </div>

            <div class="row-fluid">

                <div class="span12"> 

                    <div class="row-fluid">

                        <div class="span8">

                            <div class="row-fluid">

                                <div class="control-group">

                                    <label class="control-label" for="layout"><?php echo JText::_('GURU_MIN_SEC_COLOR');?>		

                                  </label>

                                      <?php $st_notdonecolor = "#".$result[0]->qct_minsec;?>

                                      <div class="controls">

                                        <div>

                                            <div style="float:left;">

                                                <input  type="text" size="7" name="st_notdonecolor" ID="pick_notdonecolorfield" value="<?php echo substr($st_notdonecolor, 1, strlen($st_notdonecolor));?>" onchange="changeBcolor(); relateColor('pick_notdonecolor', this.value);" size="6" maxlength="6" onkeyup="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_notdonecolor', this.value); changeBcolor();}" />

                                                &nbsp;

                                                <a href="javascript:pickColor('pick_notdonecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_notdonecolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">

                                                &nbsp;&nbsp;&nbsp;

                                                </a>

                                                <SCRIPT LANGUAGE="javascript">relateColor('pick_notdonecolor', getObj('pick_notdonecolorfield').value);</script>

                                                <span id='show_hide_box'></span>

                                                &nbsp;&nbsp;&nbsp;

                                            </div>

                                            <div style="float:left;">

                                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_MINSEC_COLOR"); ?>" >

                                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                </span>	

                                            </div>                    

                                        </div>

                                    </div>

                                </div>										

                            </div>

                        </div>							

                    </div>

                </div>	

                <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_TILTE_COLOR');?>		

                                      </label>

                                          <?php $st_txtcolor = "#".$result[0]->qct_title_color;?>

                                          <div class="controls">

                                           <div>

                                                <div style="float:left;">

                                                    <input type="text" size="7" name="st_txtcolor" ID="pick_txtcolorfield" value="<?php echo substr($st_txtcolor, 1, strlen($st_txtcolor));?>" onChange="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_txtcolor', this.value);}" SIZE="6" MAXLENGTH="6" onkeyup="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_txtcolor', this.value);}" />

                                                    &nbsp;

                                                    <a href="javascript:pickColor('pick_txtcolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_txtcolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">

                                                    &nbsp;&nbsp;&nbsp;

                                                    </a>

                                                    <SCRIPT LANGUAGE="javascript">relateColor('pick_txtcolor', getObj('pick_txtcolorfield').value);</script>

                                                    <span id='show_hide_box'></span>

                                                    &nbsp;&nbsp;&nbsp;

                                                </div>

                                                <div style="float:left;">

                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_CT_TITLE_COLOR"); ?>" >

                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                    </span>	

                                                </div>                    

                                            </div>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

               <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_BACKGROUND_COLOR');?>		

                                      </label>

                                          <?php $st_xdonecolor = "#".$result[0]->qct_bg_color;?>

                                          <div class="controls">

                                           <div>

                                                <div style="float:left;">

                                                    <input type="text" size="7" name="st_xdonecolor" ID="pick_xdonecolorfield" value="<?php echo substr($st_xdonecolor, 1, strlen($st_xdonecolor));?>" onChange="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_xdonecolor', this.value);}" size="6" maxlength="6" onkeyup="if (this.value.length == 6 || this.value.length == 0) {relateColor('pick_xdonecolor', this.value);}" />

                                                    &nbsp;

                                                    <a href="javascript:pickColor('pick_xdonecolor');" onClick="document.getElementById('show_hide_box').style.display='none';" id="pick_xdonecolor" style="border: 1px solid #000000; font-family:Verdana; font-size:10px; text-decoration: none;">

                                                    &nbsp;&nbsp;&nbsp;

                                                    </a>

                                                    <SCRIPT LANGUAGE="javascript">relateColor('pick_xdonecolor', getObj('pick_xdonecolorfield').value);</script>

                                                    <span id='show_hide_box'></span>

                                                    &nbsp;&nbsp;&nbsp;

                                                </div>

                                                <div style="float:left;">

                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_BG_COLOR"); ?>" >

                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                    </span>	

                                                </div>                    

                                            </div>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

                <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_FONT');?>		

                                      </label>

                                          <div class="controls">

                                           <select id="font" name="font" id="font" onchange="javascript:guruchangeFont(value)">

                                                <option value="Arial" <?php if($result[0]->qct_font  == "Arial"){echo 'selected="selected"'; } ?>>Arial</option>

                                                <option value="Helvetica" <?php if($result[0]->qct_font  == "Helvetica"){echo 'selected="selected"'; } ?>>Helvetica</option>

                                                <option value="Garamond" <?php if($result[0]->qct_font  == "Garamond"){echo 'selected="selected"'; } ?>>Garamond</option>

                                                <option value="sans-serif" <?php if($result[0]->qct_font  == "sans-serif"){echo 'selected="selected"'; } ?>>Sans Serif</option>				

                                                <option value="Verdana" <?php if($result[0]->qct_font  == "Verdana"){echo 'selected="selected"'; } ?>>Verdana</option>				

                                            </select>

                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_FONT_TOOLTIP"); ?>" >

                                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                            </span>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

               <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_WIDTH');?>		

                                      </label>

                                       <?php 

                                            if($result[0]->qct_width == ""){

                                                $qct_width = 200;

                                            }

                                            else{

                                                $qct_width = $result[0]->qct_width;

                                            }

                                            

                                        ?>

                                          <div class="controls">

                                           <div>

                                            <div style="float:left;">

                                                <input type="text" size="7" name="st_width" id="st_width" value="<?php echo $qct_width; ?>" onchange="javascript:guruchangeSizeW(value)" />

                                            </div>

                                            <div style="float:left;">

                                                px &nbsp;

                                            </div>

                                            <div style="float:left;">

                                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_WIDTH")."::".JText::_("GURU_CT_WIDTH"); ?>" >

                                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                </span>

                                            </div>

                                        </div>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

                <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_HEIGHT');?>		

                                      </label>

                                       <?php 

                                            if($result[0]->qct_height == ""){

                                                $qct_height = 80;

                                            }

                                            else{

                                                $qct_height= $result[0]->qct_height;

                                            }

                                            

                                        ?>

                                          <div class="controls">

                                           <div>

                                            <div style="float:left;">

                                                <input type="text" size="7" name="st_height" id="st_height" value="<?php echo $qct_height; ?>" onchange="javascript:guruchangeSizeH(value)" />

                                            </div>

                                            <div style="float:left;">

                                                px &nbsp;

                                            </div>

                                            <div style="float:left;">

                                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_HEIGHT")."::".JText::_("GURU_CT_HEIGHT"); ?>" >

                                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                </span>

                                            </div>

                                        </div>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

               <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_FONT_SIZE_NUMB');?>		

                                      </label>

                                          <div class="controls">

                                           <div>

                                                <div style="float:left;">

                                                    <select id="fontnb" name="fontnb" style="float:none !important;"  onchange="javascript:guruchangeSizeFN(value)">

                                                    <?php for( $i=10; $i<=50; $i++){?>

                                                        <option value="<?php echo $i;?>" <?php if($result[0]->qct_font_nb == $i){echo 'selected="selected"'; }?> ><?php echo $i;?></option>

                                                        <?php }?>

                                                    </select>

                                                </div>

                                                <div style="float:left;">

                                                    px &nbsp;

                                                </div>

                                                <div style="float:left;">

                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_NB_FONT_SIZE"); ?>" >

                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                    </span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

               <div class="row-fluid">

                    <div class="span12"> 

                        <div class="row-fluid">

                            <div class="span8">

                                <div class="row-fluid">

                                    <div class="control-group">

                                        <label class="control-label" for="layout"><?php echo JText::_('GURU_FONT_SIZE_WORDS');?>		

                                      </label>

                                          <div class="controls">

                                           <div>

                                                <div style="float:left;">

                                                    <select id="fontwords" name="fontwords" style="float:none !important;"  onchange="javascript:guruchangeSizeFM(value)">

                                                    <?php for( $i=10; $i<=50; $i++){?>

                                                        <option value="<?php echo $i;?>" <?php if($result[0]->qct_font_words == $i){echo 'selected="selected"'; }?> ><?php echo $i;?></option>

                                                        <?php }?>

                                                    </select>

                                                </div>

                                                <div style="float:left;">

                                                    px &nbsp;

                                                </div>

                                                <div style="float:left;">

                                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_NB_FONT_SIZE"); ?>" >

                                                        <img border="0" src="components/com_guru/images/icons/tooltip.png">

                                                    </span>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>										

                                </div>

                            </div>							

                        </div>

                    </div>							

               </div>

    </div>        

            

    <div class="pull-right span3">

        <div  style="padding-bottom:10px;"><?php echo JText::_("GURU_PREVIEW") ?></div>

        <div id = "divtotal" style="width:<?php echo $qct_width;?>px; height:<?php echo $qct_height;?>px; border: 1px solid; border-color:<?php echo $st_donecolor;?>; font-family:<?php echo $result[0]->qct_font;?>; background-color:<?php echo $st_xdonecolor;?>;">

            <div id="timeleft" align="center" style="border-bottom:1px <?php echo $st_donecolor;?> solid; font-size:<?php echo $result[0]->qct_font_words;?>px; color:<?php echo $st_txtcolor;?>; background-color:<?php echo $st_donecolor;?>;"><?php echo JText::_("GURU_TIMEPROMO");?></div>

                <div id="totalbg" style="background-color:<?php echo $st_xdonecolor;?>;">

                    <div id="timetest" align="center" style="font-size:<?php echo $result[0]->qct_font_nb ;?>px; color:<?php echo $st_notdonecolor;?>;">04  &nbsp;  26</div>

                    <div id="minsec" align="center" style="font-size:<?php echo $result[0]->qct_font_words;?>px;"><?php echo JText::_("GURU_PROGRAM_DETAILS_MINUTES")."  ".JText::_("GURU_PROGRAM_DETAILS_SECONDS") ;?></div>

                </div>

            </div>

        </div>

    </div>   

        <input type="hidden" name="option" value="com_guru" />

        <input type="hidden" name="task" value="" />

        <input type="hidden" name="controller" value="guruQuizCountdown" />

        <input type="hidden" name="id" value="0" />

    </form>

</div>    