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

?>
<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">
    <div class="span12">
    	<div>
        	<?php echo JText::_("GURU_ABOUTENDTEXT"); ?>
        </div>
        <div class="well well-minimized">
            <strong><?php echo JText::_("AD_ABOUTCOMPONENT"); ?> </strong>
        </div>
        <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('AD_ABOUTINSTALLED');?></h5></div>
			<div class="widget-body">
    			<div class="widget-main clearfix">
                    <div class="span12">
                        <div class="span3">
                            <?php echo $this->component['installed'] ?
                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                            ?>
                        </div>
                        <div class="span2">
                            + <?php echo $this->component['name'];?>
                        </div>
                        <div class="span4">
                            <?php echo $this->component['version'];?>
                        </div>
                    </div>
                  </div>
               </div>   
       <div class="clearfix"></div>
        <?php
            if(count($this->plugins) > 0){
        ?>
                <div class="well well-minimized">
                    <strong><?php echo JText::_("GURU_TREEPLUGINS"); ?> </strong>
                </div> 
                 <div class="widget-header widget-header-flat"><h5><?php echo  JText::_('AD_ABOUTINSTALLED');?></h5></div>
                    <div class="widget-body">
                        <div class="widget-main clearfix">
						<?php
                            foreach($this->plugins as $key=>$value){							
                        ?>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="span3">
                                            <?php echo $value['installed'] ?
                                            '<font color="green"><strong>'.JText::_("AD_ABOUTINSTALLED").'</strong></font>' :
                                            '<font color="red"><strong><nowrap>'.JText::_("AD_ABOUTNOTINSTALLED").'</nowrap></strong></font>';
                                            ?>
                                        </div>
                                        <div class="span2">
                                            + <?php echo $value['name'];?>
                                        </div>
                                        <div class="span4">
                                            <?php echo $value['version'];?>
                                        </div>
                                    </div>				
        						</div>
                        <?php
                            }
                    ?>
                    </div>
               </div> 
        <?php
            }
            ?>
            <input type="hidden" name="option" value="com_guru" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="controller" value="guruAbout" />
    
    </div>
</form>