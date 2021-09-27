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
?>
<script language="javascript" type="text/javascript">
    Joomla.submitbutton = function(button){
	//function submitbutton(button) {
        if(button == 'save' || button == 'apply') {
            if(document.adminForm.name.value == ""){
				alert("<?php echo JText::_("GURU_ENTER_EMAIL_NAME"); ?>");
				return false;
			}
			else if(document.adminForm.subject.value == ""){
				alert("<?php echo JText::_("GURU_ENTER_EMAIL_SUBJECT"); ?>");
				return false;
			}
			else if(document.adminForm.body.value == ""){
				alert("<?php echo JText::_("GURU_ENTER_BODY_SUBJECT"); ?>");
				return false;
			}
        }
        submitform(button);
    }
</script>


<form action="<?php echo JUri::root() ?>administrator/index.php" id="adminForm" name="adminForm" method="post">

<fieldset>
    <table>
        <tr>
            <td valign="top" width="100%" colspan="2">
                <div class="g_variables">
                	<table class="pull-left">
                        <tr>
                            <td class="span4"><?php echo JText::_('GURU_SITENAME'); ?></td>
                            <td><?php echo JText::_('GURU_SITENAME2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_CUSTEMAIL'); ?></td>
                            <td><?php echo JText::_('GURU_CUSTEMAIL2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_FIRSTNAME'); ?></td>
                            <td><?php echo JText::_('GURU_FIRSTNAME2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_SITEURL'); ?></td>
                            <td><?php echo JText::_('GURU_SITEURL2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_RUSERNAME'); ?></td>
                            <td><?php echo JText::_('GURU_RUSERNAME2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_RENEW_URL'); ?></td>
                            <td><?php echo JText::_('GURU_RENEW_URL2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_PRODUCT_URL'); ?></td>
                            <td><?php echo JText::_('GURU_PRODUCT_URL2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_LASTNAME'); ?></td>
                            <td><?php echo JText::_('GURU_LASTNAME2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_RTERMS'); ?></td>
                            <td><?php echo JText::_('GURU_RTERMS2'); ?></td>
                        </tr>
                    </table>
                    
                    <table class="pull-left">
                        <tr>
                            <td class="span4"><?php echo JText::_('GURU_LICENSE_NR'); ?></td>
                            <td><?php echo JText::_('GURU_LICENSE_NR2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_MYLICENSES'); ?></td>
                            <td><?php echo JText::_('GURU_MYLICENSES2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_PRODNAME'); ?></td>
                            <td><?php echo JText::_('GURU_PRODNAME2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_EXPDATE'); ?></td>
                            <td><?php echo JText::_('GURU_EXPDATE2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_MYORDER'); ?></td>
                            <td><?php echo JText::_('GURU_MYORDER2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_SUBSCRIPTION_TERM'); ?></td>
                            <td><?php echo JText::_('GURU_SUBSCRIPTION_TERM2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_LESSON_TITLE'); ?></td>
                            <td><?php echo JText::_('GURU_LESSON_TITLE2'); ?></td>
                        </tr>
                        <tr>
                        	<td><?php echo JText::_('GURU_LESSON_URL'); ?></td>
                            <td><?php echo JText::_('GURU_LESSON_URL2'); ?></td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                </div>
            </td>
        </tr>
        
        <tr>
            <td width="15%"><?php echo JText::_('GURU_PUBLISHED'); ?></td>
            <td>
                <?php  echo $this->lists['published']; ?>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REMIND_PUBLISHED"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </td>
        </tr>

        <tr>
            <td width="15%"><?php echo JText::_('GURU_NAME'); ?>&nbsp;<span style="color:#FF0000">*</span></td>
            <td>
                <input type="text" name="name" size="35" value="<?php echo $this->email->name; ?>"/>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REMIND_NAME"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </td>
        </tr>
        
        <tr>
            <td><?php echo JText::_('VIEWPACKAGETERMS'); ?></td>
            <td><?php
                echo $this->lists['term'];
                ?>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REMIND_VIEWPACKAGETERMS"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </td>
        </tr>

        <tr>
            <td><?php echo JText::_('GURU_EM_SUBJECT'); ?>&nbsp;<span style="color:#FF0000">*</span></td>
            <td>
                <?php echo $this->lists['subject']; ?>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REMIND_SUBJECT"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </td>
        </tr>
        <tr>
            <td><?php echo JText::_('GURU_EM_BODY'); ?>&nbsp;<span style="color:#FF0000">*</span></td>
            </td>
        </tr>
        <tr>
            <td valign="top" width="100%" colspan="2">
                <?php echo $this->editor->display('body', $this->email->body, '100%', '350', '75', '20', false ); ?>
            </td>
            <td>
                <span class="editlinktip hasTip" title="<?php echo JText::_("GURU_TIP_REMIND_BODY"); ?>" >
                    <img border="0" src="components/com_guru/images/icons/tooltip.png">
                </span>
            </td>
        </tr>
    </table>
</fieldset>
    <input type="hidden" name="id" value="<?php echo $this->email->id; ?>"/>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_guru" />
    <input type="hidden" name="controller" value="guruSubremind" />
</form>