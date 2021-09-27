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

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_guru/css/install.css");
$document->addStyleSheet(JURI::root()."media/jui/css/bootstrap.min.css");

$step = JFactory::getApplication()->input->get("step", "start");

?>
<form id="adminForm" action="<?php echo JUri::root() ?>administrator/index.php" name="adminForm" method="post">
	
    <div class="install-content">
    	<div class="install-header">
    		<div class="install-header-left">
				<?php echo JText::_("GURU_INSTALL_HEADER"); ?>
            </div>
            <div class="install-header-right">
            	<img src="components/com_guru/images/guru-logo.png" />
            </div>
		</div>
        <div class="install-body">
        	<ul>
                <li>
                	<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_DATABASE"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('database');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
				<li>
                	<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INDEX_DATABASE"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('index');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
                <li>
					<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_DEFAULT_VALUES"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('default');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
                <li>
					<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_CREATE_FOLDERS"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('folders');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
                <li>
					<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_UPDATE_MENU_ITEMS"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('menu');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
                <li>
					<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_PLUG_INS"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('plugins');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
                <li>
					<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_QUESTIONS"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('questions');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
                <li>
					<table width="100%">
                    	<tr>
                        	<td valign="middle">
                            	<span class="install-step-label"><?php echo JText::_("GURU_INSTALL_QUIZ_TAKEN"); ?></span>
                            </td>
                            <td valign="middle">
                            	<?php
                                	echo $this->createDiagram('quiz');
								?>
                            </td>
                        </tr>
                    </table>
				</li>
            </ul>
            <?php
				if($step == "stop"){
			?>
					<div class="install-redirect-button">
                    	<input type="button" class="btn btn-success" value="<?php echo JText::_("GURU_GO_TO_GURU"); ?>" onclick="window.location='<?php echo JURI::root(); ?>administrator/index.php?option=com_guru'" />
                    </div>
			<?php
				}
			?>
        </div>
    </div>
    
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="guruInstall" />
    
    <?php
    	if($step == "start"){
	?>
			<script language="javascript" type="text/javascript">
                setTimeout(function(){
                                window.location.href = "<?php echo JURI::root(); ?>" + "administrator/index.php?option=com_guru&controller=guruInstall&step=database&tmpl=component";
                           }, 2000);
            </script>
	<?php
    	}
	?>
</form>

<?php
	sleep(3);
	$this->startAction();
?>