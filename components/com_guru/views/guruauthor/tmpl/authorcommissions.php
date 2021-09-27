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

$div_menu = $this->authorGuruMenuBar();
$config = $this->config;
$Itemid = JFactory::getApplication()->input->get("Itemid", "0", "raw");
$authcomm = $this->authcomm;
$authcommpending = $this->authcommpending;
$authcommpaid = $this->authcommpaid;
$authpaymetoption = $this->authpaymetoption;

$db = JFactory::getDBO();
$sql = "SELECT teacher_earnings  FROM #__guru_commissions
WHERE id =".intval($authcomm[0]["commission_id"]);
$db->setQuery($sql);
$db->execute();
$teacher_earnings = $db->loadColumn();

$character = "GURU_CURRENCY_".$config->currency;
	
$doc = JFactory::getDocument();
//$doc->addScript('components/com_guru/js/guru_modal_commissions.js');
//$doc->addScript('components/com_guru/js/jquery-dropdown.js');

$doc->setTitle(trim(JText::_('GURU_AUTHOR'))." ".trim(JText::_('GURU_COMMISSIONS')));

//$doc->addScript('components/com_guru/js/guru_modal.js');
JHtml::stylesheet("components/com_guru/css/tabs.css");

?>

<script type="text/javascript" language="javascript">
	document.body.className = document.body.className.replace("modal", "");
</script>

<script language="javascript" type="text/javascript">
	function saveauthcomm (pressbutton){
		submitform( pressbutton );
	}
</script>
<style>
	div.g_inline_child button.btn{
		height:26px !important;
	}
	div.guru-content textarea{
		min-width :70%;
	}
	.guru-content .form-horizontal .control-label{
		padding-top:7px;
	}
</style>

<div id="g_commissions_main" class="clearfix com-cont-wrap">
    <?php echo $div_menu; //MENU TOP OF AUTHORS ?>
    
    <h2 class="gru-page-title"><?php echo JText::_('GURU_COMMISSIONS')." > ".JText::_('GURU_SUMMARY');?></h2>

    <hr class="uk-divider wk-divider">
    
    <div class="gru-commissions">
        <form action="<?php echo JUri::root() ?>index.php" class="uk-form uk-form-horizontal wk-form wk-form-horizontal" id="adminForm" method="post" name="adminForm" enctype="multipart/form-data">
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_COMMISSIONS_EARNINGS");?>:
                </label>
            	<div class="uk-form-controls wk-form-controls">
                	<?php
						if(trim($teacher_earnings["0"]) != ""){
                    		echo $teacher_earnings["0"]. "%";
						}
						else{
							echo "0%";
						}
					?>
                </div>
			</div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_COMMISSIONS_RECEIVED");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                     <?php
                        $temp = array();
                        if(isset($authcommpaid) && count($authcommpaid) > 0){
                            foreach($authcommpaid as $key=>$value){
                                if(isset($temp[$value["currency"]])){
                                    $temp[$value["currency"]] += $value["amount_paid_author"];
                                }
                                else{
                                    $temp[$value["currency"]] = $value["amount_paid_author"];
                                }
                            }
                        }
                        
                        if(isset($temp) && count($temp) > 0){
                            foreach($temp as $currency=>$value){
                                if($config->currencypos == 0){
                                    $paid_authcom = JText::_("GURU_CURRENCY_".$currency). number_format($value, 2);
                                }
                                else{
                                    $paid_authcom = number_format($value, 2).JText::_("GURU_CURRENCY_".$currency);
                                }
                                echo '<span class="gurugreen">'.$paid_authcom.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            }
                            ?>
							<a class="btn btn-primary" onclick="openMyModal('0','0','<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=paid_commission&tmpl=component')" href="#">
                                <?php echo JText::_("GURU_DETAILS"); ?>
							</a>
                            <?php
                        }
                        else{
                            if($config->currencypos == 0){
                                $paid_authcomo = JText::_("GURU_CURRENCY_".$config->currency). "0.00";
                            }
                            else{
                                $paid_authcomo = "0.00".JText::_("GURU_CURRENCY_".$config->currency);
                            }
                            echo '<span class="gurugreen">'.$paid_authcomo.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                    ?>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                    <?php echo JText::_("GURU_COMMISSIONS_PENDING");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                     <?php
                        $temp = array();
                        if(isset($authcommpending) && count($authcommpending) > 0){
                            foreach($authcommpending as $key=>$value){
                                if(isset($temp[$value["currency"]])){
                                    $temp[$value["currency"]] += $value["amount_paid_author"];
                                }
                                else{
                                    $temp[$value["currency"]] = $value["amount_paid_author"];
                                }
                            }
                        }
                        
                        if(isset($temp) && count($temp) > 0){
                            foreach($temp as $currency=>$value){
                                if($config->currencypos == 0){
                                    $pending_authcom = JText::_("GURU_CURRENCY_".$currency). number_format($value, 2);
                                }
                                else{
                                    $pending_authcom = number_format($value, 2).JText::_("GURU_CURRENCY_".$currency);
                                }
                                echo '<span class="gurured">'.$pending_authcom.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            }
                            ?>
                                <a class="btn btn-primary" onclick="openMyModal('0','0','<?php echo JURI::root(); ?>index.php?option=com_guru&view=guruauthor&task=pending_commission&tmpl=component')" href="#">
                                    <?php echo JText::_("GURU_DETAILS"); ?>
                                </a>
                            <?php
                        }
                        else{
                            if($config->currencypos == 0){
                                $pending_authcomo = JText::_("GURU_CURRENCY_".$config->currency). "0.00";
                            }
                            else{
                                $pending_authcomo = "0.00".JText::_("GURU_CURRENCY_".$config->currency);
                            }
                            echo '<span class="gurured">'.$pending_authcomo.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                    ?>
                </div>
            </div>
            
            
            <h3><?php echo JText::_('GURU_PICK_PREFERED_OPTION');?></h3>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                	<input type="radio" class="pull-left uk-margin-right wk-margin-right" name="payment_option" value="0" <?php if($authpaymetoption == "0"){echo 'checked="checked"';} ?>/>
                    <?php echo JText::_("GURU_PAYPAL_EMAIL");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <input type="text" id="paypal_email" name="paypal_email" value="<?php echo $authcomm[0]["paypal_email"]; ?>" class="inputbox" size="40" />
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_EMAIL2"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>
            
            <div class="uk-form-row wk-form-row">
                <label class="uk-form-label wk-form-label" for="name">
                	<input type="radio" class="pull-left uk-margin-right wk-margin-right" name="payment_option" value="1" <?php if($authpaymetoption == "1"){echo 'checked="checked"';} ?>/>
                    <?php echo JText::_("GURU_COMMISSIONS_DETAILS");?>:
                </label>
                <div class="uk-form-controls wk-form-controls">
                    <textarea name="paypal_other_information" rows="6"><?php echo $authcomm[0]["paypal_other_information"]; ?></textarea>
                    <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_AU_AUTHOR_OTHER_INFORMATION"); ?>" >
                        <img border="0" src="components/com_guru/images/icons/tooltip.png">
                    </span>
                </div>
            </div>

            <hr class="uk-divider wk-divider">
             
			<div class="uk-text-right wk-text-right">
	            <input type="button" class="uk-button uk-button-success wk-button wk-button-success" value="<?php echo JText::_("GURU_SAVE"); ?>" onclick="javascript:saveauthcomm('apply_commissions');" />
			</div>

  
            <input type="hidden" name="task" value="authorcommissions" />
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="controller" value="guruAuthor" />
            <input type="hidden" name="boxchecked" value="" />
        </form>
    </div>
</div>