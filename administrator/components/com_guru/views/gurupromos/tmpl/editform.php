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
JHtml::_('behavior.calendar');

require_once(JPATH_BASE.'/../components/com_guru/helpers/helper.php');
$guruHelper = new guruHelper();

	$lists = $this->lists;
	$promo=$this->promo;
	$nullDate = 0;
	$config = guruAdminModelguruPromos::getConfig();
	$currency = $config->currency;
	$character = JText::_("GURU_CURRENCY_".$currency);
	
	$db = JFactory::getDBO();
	$sql = "Select datetype FROM #__guru_config where id=1 ";
	$db->setQuery($sql);
	$format_date = $db->loadColumn();
	$dateformat = $format_date[0];
	$courses = $this->courses;

	$format = "%m-%d-%Y";
	switch($dateformat){
		case "d-m-Y H:i:s": $format = "%d-%m-%Y %H:%M:%S";
			  break;
        case "d.m.Y H:i": $format = "%d.%m.%Y %H:%M"; 
            break;
		case "d/m/Y H:i:s": $format = "%d/%m/%Y %H:%M:%S"; 
			  break;
		case "m-d-Y H:i:s": $format = "%m-%d-%Y %H:%M:%S"; 
			  break;
		case "m/d/Y H:i:s": $format = "%m/%d/%Y %H:%M:%S"; 
			  break;
		case "Y-m-d H:i:s": $format = "%Y-%m-%d %H:%M:%S"; 
			  break;
		case "Y/m/d H:i:s": $format = "%Y/%m/%d %H:%M:%S"; 
			  break;
		case "d-m-Y": $format = "%d-%m-%Y"; 
			  break;
		case "d/m/Y": $format = "%d/%m/%Y"; 
			  break;
		case "m-d-Y": $format = "%m-%d-%Y"; 
			  break;
		case "m/d/Y": $format = "%m/%d/%Y"; 
			  break;
		case "Y-m-d": $format = "%Y-%m-%d"; 
			  break;
		case "Y/m/d": $format = "%Y/%m/%d";		
			  break;  	  	  	  	  	  	  	  	  	  
	}
?>

<script language="javascript" type="text/javascript">
	
	function alphanumeric(alphane){
		var numaric = alphane;
		for(var j=0; j<numaric.length; j++){
			var alphaa = numaric.charAt(j);
			var hh = alphaa.charCodeAt(0);
			if((hh > 47 && hh<58) || (hh > 64 && hh<91) || (hh > 96 && hh<123)){
			
			}
			else{
				return false;
			}
		}
		 return true;
	}		
	
	function IsNumeric(sText){
		var ValidChars = "0123456789.";
		var IsNumber=true;
		var Char;
		for(i = 0; i < sText.length && IsNumber == true; i++){ 
			Char = sText.charAt(i); 
			if(ValidChars.indexOf(Char) == -1){ 
				IsNumber = false; 
			}
		}
		return IsNumber;
	}
	<!--
	Joomla.submitbutton = function(pressbutton){
	//function submitbutton(pressbutton){
		var form = document.adminForm;
		
		if (pressbutton=='save' || pressbutton=='apply') {
			var i;
			
			var sDate = document.getElementById('codestart').value;
			sDate = sDate.split(" ");
			sDate = sDate[0];
			
			var eDate = document.getElementById('codeend').value;
			eDate = eDate.split(" ");
			eDate = eDate[0];
			
			sDate = new Date(sDate+"");
			eDate = new Date(eDate+"");
			
			sDate = sDate.getTime();
			eDate = eDate.getTime();
			
			i = alphanumeric(form['code'].value);
			
			if(form['title'].value==''){
				alert('<?php echo JText::_('GURU_PROMTITLERR');?>'); 
				return false;
			} 
			if ((form['code'].value=='')||(i==false)){
				alert('<?php echo JText::_('GURU_PROMCODEERR');?>'); 
				return false;
			} 
			if (form['discount'].value==''){
				alert('<?php echo JText::_('GURU_PROMDISCOUNT');?>'); 
				return false;
			}
			/*if (IsNumeric(form['discount'].value)==false){
				alert('<?php echo JText::_('GURU_PROMDISCOUNT2');?>'); 
				return false;
			}*/
			if(IsNumeric(form['codelimit'].value)==false){
				alert('<?php echo JText::_('GURU_CODELIMIT_UNSAFE');?>'); 
				return false;
			}
			if(sDate > eDate && document.getElementById('codestart').value != '' && document.getElementById('codeend').value != '' )
			{
				alert("<?php echo JText::_("GURU_DATE_GRATER");?>");
				return false;
			}	
			
			if(document.getElementById("typediscount2").checked == true){
				if(form["discount"].value > 100){
					alert("<?php echo JText::_("GURU_INCORECT_DISCOUNT");?>");
					return false;
				}
			}
			//submitform( pressbutton );
            form.task.value = pressbutton;
            form.submit();
		}
		else {
			//submitform( pressbutton );
            form.task.value = pressbutton;
            form.submit();
		}
	}
	-->
</script>
<style>
	.input-append{
		width:22%!important;
		float:left;
	}
	#js-cpanel input[type="checkbox"], #js-cpanel input[type="radio"]{
		/* position:relative;
        right : -20px; */
	}
</style>

<form action="<?php echo JUri::root() ?>administrator/index.php" method="post" name="adminForm" id="adminForm">
	<div class="clearfix"></div>
     <div class="adminform">	
        <div class="row-fluid">
            <div class="span12"> 
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="curency"><?php echo JText::_('GURU_PROMTITLE');?>&nbsp;<font color="#FF0000">*</font></label>
                            <div class="controls">
                       			<input type="text" value="<?php echo $promo->title; ?>" name="title"/>
                                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PROMTITLE"); ?>" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="layout"><?php echo JText::_('GURU_PROMCODE');?>&nbsp;<font color="#FF0000">*</font>		
               				  </label>
                              <div class="controls">
                              	<div class="pull-left">
                                  <input type="text" value="<?php echo $promo->code; ?>" name="code"/>
                                </div>
                                <div class="pull-left">&nbsp;&nbsp;&nbsp;		
                                    <?php echo JText::_('GURU_ALPAHPROMO');?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PROMCODE"); ?>" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="curency"><?php echo JText::_('GURU_SELECT_COURSE');?></label>
                                <div class="controls">
                                        <div style="float:left; overflow-y:scroll; height:200px; margin-bottom:20px;">
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                <?php 
                                                    for($i=0; $i<count($courses); $i++){
                                                        $id = $courses[$i]->id;
                                                        $courses_array = explode("|",$promo->courses_ids);
														$courses_array = array_values( array_filter($courses_array) );
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <input style="opacity: 1; position: initial; height: auto;" type="checkbox" <?php if(isset($promo->courses_ids)&&(in_array($id,$courses_array))) { echo 'checked="checked"'; } ?> name="cid[]" value="<?php echo $id;?>" />
                                                            </td>	
                                                            <td>
                                                                <?php echo $courses[$i]->name;?>
                                                            </td>		
                                                        </tr>
                                                        <?php 
                                                    }
                                                ?>
                                                
                                                </tbody>
                                            </table>
                                    </div>
                                    <div style="float:left;">
                                        <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_SELCT_COURSE_PROMO"); ?>">
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="layout"><?php echo JText::_('GURU_USAGELIMIT');?>		
               				  </label>
                              <div class="controls">
                              	<div class="pull-left">
                                  <input type="text" size="10" name="codelimit" value="<?php if(intval($promo->codelimit) > 0){echo $promo->codelimit;} ?>"/>
                                </div>
                                <div class="pull-left">&nbsp;&nbsp;&nbsp;		
                                    <?php echo JText::_('GURU_LEAVEPROMO');?>
                                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_USAGELIMIT"); ?>" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="layout"><?php echo JText::_('GURU_DISCOUNTPROMO');?><font color="#FF0000">*</font>		
               				  </label>
                              <div class="controls">
                              	<div class="pull-left">
                                  <input type="text" size="10" name="discount" value="<?php echo $guruHelper->displayPrice($promo->discount); ?>"/>&nbsp;&nbsp&nbsp;&nbsp
                                </div>
                                <div class="pull-left">
                                  <input type="radio" id="typediscount1" name="typediscount" value="0" <?php echo ($promo->typediscount == 0)?"checked":""; ?> />
                                  <span class="lbl"></span>
                                </div>
                                <div class="pull-left">
                                	&nbsp;<?php echo $character; ?>&nbsp;&nbsp;
                                </div>
                                <div class="pull-left">
                                  <input type="radio" id="typediscount2" name="typediscount" value="1" <?php echo ($promo->typediscount == 1 || $promo->typediscount != 0)?"checked":""; ?> />
                                  &nbsp;
                                  <span class="lbl"></span>
                                </div>
                                 <div class="pull-left">
                                  %&nbsp;&nbsp;
                                </div>
                                <div class="pull-left">
                                     <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_DISCOUNTPROMO"); ?>" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="curency"><?php echo JText::_('GURU_STARTPROMO');?></label>
                            <div class="controls">
                       			<?php 
									if ($promo->id<1) $start_publish = date('Y-m-d H:i:s', time()); 
									else $start_publish =  date("".$dateformat."", strtotime($promo->codestart));
									
                                    echo JHTML::_('calendar', $start_publish, 'codestart', 'codestart', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); // calendar change

                                    ?>
                                     <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_STARTPROMO"); ?>" style="margin-left:40px;" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="curency"><?php echo JText::_('GURU_ENDPROMO');?></label>
                            <div class="controls">
                       			<?php 
									if(substr($promo->codeend,0,4) =='0000' || $promo->id<1) $end_publish = ""; 
									else $end_publish = date("".$dateformat."", strtotime($promo->codeend));
									
                                    echo JHTML::_('calendar', $end_publish, 'codeend', 'codeend', $format, array("showTime"=>"", "todayBtn"=>"", "weekNumbers"=>"", "fillTable"=>"", "singleHeader"=>"")); // calendar change

                                    ?>
                                     <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_ENDPROMO"); ?>" style="margin-left:40px;" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="layout"><?php echo JText::_('GURU_ONLYCPROMO');?></label>
                              <div class="controls">
                                <input type="hidden" name="forexisting" value="0">
								<?php
                                    $checked = '';
                                    if($promo->forexisting == 1){
                                        $checked = 'checked="checked"';
                                    }
                                ?>
                                <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="forexisting">
                                <span class="lbl"></span>
                                 
                                 <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_ONLYCPROMO"); ?>" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="layout"><?php echo JText::_('GURU_ONLY_ONE_TIME');?></label>
                              <div class="controls">
                                <input type="hidden" name="one_time" value="0">
                                <?php
                                    $checked = '';
                                    if($promo->one_time == 1){
                                        $checked = 'checked="checked"';
                                    }
                                ?>
                                <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="one_time">
                                <span class="lbl"></span>
                                 
                                 <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_ONLY_ONE_TIME_TIP"); ?>" >
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
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="control-group">
                                <label class="control-label span2" for="layout"><?php echo JText::_('GURU_PUBPROMO');?>	
               				  </label>
                              <div class="controls">
                                <input type="hidden" name="published" value="0">
								<?php
                                    $checked = '';
                                    if($promo->published == 1){
                                        $checked = 'checked="checked"';
                                    }
                                ?>
                                <input type="checkbox" <?php echo $checked; ?> value="1" class="ace-switch ace-switch-5" name="published">
                                <span class="lbl"></span>
                                
                                 <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_PUBPROMO"); ?>" >
                                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                                </span>
                   			</div>
                            </div>										
                        </div>
                    </div>							
                </div>
            </div>						
        </div>
        <?php if ($promo->codelimit) {?>
                <div class="well"><?php echo JText::_('GURU_STATSPROMO');?>:</div>
                   <div class="row-fluid">
                    <div class="span12"> 
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="row-fluid">
                                    <div class="control-group">
                                        <label class="control-label span2" for="layout"><?php echo JText::_('GURU_USEDPROMO');?>&nbsp;	
                                      </label>
                                      <div class="controls">
                                        <div class="pull-left">
                                         <?php echo $promo->codeused; ?>
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
                            <div class="span12">
                                <div class="row-fluid">
                                    <div class="control-group">
                                        <label class="control-label span2" for="layout"><?php echo JText::_('GURU_USAGE_LEFT');?>&nbsp;		
                                      </label>
                                      <div class="controls">
                                        <div class="pull-left">
                                         <?php echo $promo->codelimit - $promo->codeused; ?>
                                        </div>
                                    </div>
                                    </div>										
                                </div>
                            </div>							
                        </div>
                    </div>						
                </div>
               
              <?php }
			  else{?>
              
              <div class="row-fluid">
                    <div class="span12"> 
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="row-fluid">
                                    <div class="control-group">
                                        <label class="control-label span2" for="layout"><?php echo JText::_('GURU_USAGE');?>&nbsp;		
                                      </label>
                                      <div class="controls">
                                        <div class="pull-left">
                                          - &nbsp;
                                        </div>
                                        <div style="float:left;">
                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_USAGE"); ?>" >
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
              <?php }?>	
              <div class="row-fluid">
                    <div class="span12"> 
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="row-fluid">
                                    <div class="control-group">
                                        <label class="control-label span2" for="layout"><?php echo JText::_('GURU_TIMEPROMO');?>&nbsp;		
                                      </label>
                                      <div class="controls">
                                        <div class="pull-left">
                                          <?php 
											if($promo->codeend!='0000-00-00 00:00:00'){
												if(time()<=strtotime($promo->codeend)){
													if(time()<strtotime($promo->codestart)){
														$start_time = strtotime($promo->codestart);
													}
													else{
														$start_time = time();	
													}
													$days_left = floor((strtotime($promo->codeend) - $start_time) / (3600*24) );
													if($days_left>1){
														$days_left_text = JText::_('GURU_PROMDAYS');
													}
													else{
														$days_left_text = JText::_('GURU_PROMDAY1');
													}
													$days_left_text_final = $days_left.' '.$days_left_text;
												} 
												else {
													$days_left_text_final = JText::_('GURU_PROMDAYSEXP');
												}		
												if(!$this->isNew){
													 echo '<div style="float:left;">'.$days_left_text_final.'&nbsp;</div>';
												} 
												else{
													echo '<div style="float:left;">-&nbsp;</div>';
												}//echo JText::_('GURU_UNLIMPROMO');
											} 
											else {
												echo '<div style="float:left;">'.JText::_('GURU_PROMDAYSNOTEXP').'&nbsp;</div>';
											}
											?>
                                        </div>
                                        <div style="float:left;">
                                            <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_TIMEPROMO"); ?>" >
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
    <input type="hidden" name="id" value="<?php echo $promo->id;?>" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="controller" value="guruPromos" />
</form>
