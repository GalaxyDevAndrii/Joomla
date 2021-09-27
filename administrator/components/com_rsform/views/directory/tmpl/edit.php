<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.popover');
JHtml::_('script', 'com_rsform/admin/directory.js', array('relative' => true, 'version' => 'auto'));

JText::script('RSFP_SUBM_DIR_AUTOGENERATE_LAYOUT_DISABLED');
JText::script('WARNING');
JText::script('RSFP_ARE_YOU_SURE_DELETE');
?>

<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div id="rsform_container">
		<div id="state" style="display: none;"><?php echo JHtml::_('image', 'com_rsform/admin/load.gif', JText::_('RSFP_PROCESSING'), null, true); ?><?php echo JText::_('RSFP_PROCESSING'); ?></div>
		
		<div id="rsform_directory_tab">
			<ul class="rsform_leftnav" id="rsform_secondleftnav">
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_DIRECTORY_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="editform"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_DIRECTORY_EDIT'); ?></span></a></li>
                <li><a href="javascript: void(0);" id="permissions"><span class="rsficon rsficon-shield"></span><span class="inner-text"><?php echo JText::_('RSFP_DIRECTORY_PERMISSIONS'); ?></span></a></li>
				<li><a href="javascript: void(0);" id="fields"><span class="rsficon rsficon-list-alt"></span><span class="inner-text"><?php echo JText::_('RSFP_DIRECTORY_FIELDS'); ?></span></a></li>
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_DESIGN_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="formlayout"><span class="rsficon rsficon-th-list"></span><span class="inner-text"><?php echo JText::_('RSFP_SUBM_DIR_DETAILS_LAYOUT'); ?></span></a></li>
				<li><a href="javascript: void(0);" id="cssandjavascript"><span class="rsficon rsficon-file-code-o"></span><span class="inner-text"><?php echo JText::_('RSFP_CSS_JS'); ?></span></a></li>
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_EMAILS_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="emails"><span class="rsficon rsficon-envelope-o"></span><span class="inner-text"><?php echo JText::_('RSFP_SUBM_DIR_EMAILS'); ?></span></a></li>
				<li class="rsform_navtitle"><?php echo JText::_('RSFP_SCRIPTS_TAB'); ?></li>
				<li><a href="javascript: void(0);" id="scripts"><span class="rsficon rsficon-code"></span><span class="inner-text"><?php echo JText::_('RSFP_FORM_SCRIPTS'); ?></span></a></li>
				<li><a href="javascript: void(0);" id="emailscripts"><span class="rsficon rsficon-file-code-o"></span><span class="inner-text"><?php echo JText::_('RSFP_EMAIL_SCRIPTS'); ?></span></a></li>
			</ul>
			
			<div id="propertiescontent">
				<div id="editformdiv">
					<?php echo $this->loadTemplate('general'); ?>
				</div>
                <div id="permissionsdiv">
                    <?php echo $this->loadTemplate('permissions'); ?>
                </div>
				<div id="fieldsdiv">
					<?php echo $this->loadTemplate('fields'); ?>
				</div>
				<div id="formlayoutdiv">
					<?php echo $this->loadTemplate('layout'); ?>
				</div>
				<div id="cssandjavascriptdiv">
					<?php echo $this->loadTemplate('cssjs'); ?>
				</div>
				<div id="emailsdiv">
					<p>
						<button type="button" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=emails.edit&type=directory&tmpl=component&formId='.$this->formId); ?>', 'Emails', '800x750');" class="btn btn-primary"><?php echo JText::_('RSFP_FORM_EMAILS_NEW'); ?></button>
					</p>
					<div id="emailsContent">
						<?php echo $this->loadTemplate('emails'); ?>
					</div>
				</div>
				<div id="scriptsdiv">
					<?php echo $this->loadTemplate('scripts'); ?>
				</div>
				<div id="emailscriptsdiv">
					<?php echo $this->loadTemplate('emailscripts'); ?>
				</div>
			</div>
			
		</div>
	</div>
	
	<input type="hidden" name="option" value="com_rsform">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="tab" id="ptab" value="<?php echo $this->tab; ?>" />
	<input type="hidden" name="jform[formId]" id="formId" value="<?php echo $this->formId; ?>">
</form>