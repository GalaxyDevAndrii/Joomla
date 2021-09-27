<?php
/**
 * ------------------------------------------------------------------------
 * JA Extension Manager Component for J3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

//no direct access
defined( '_JEXEC' ) or die( 'Retricted Access' );

$JaextmanagerModelDefault = new JaextmanagerModelDefault();
$checkLog = $JaextmanagerModelDefault->getListExtensionSettings();
?>
<script language="javascript">
// Proccess for check update button
//<![CDATA[
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;
	// Check update
	if ( pressbutton == 'checkupdate'){
		checkNewVersions();
		return;
	}
	
	// Recovery
	if ( pressbutton == 'recovery'){
		recoveryAll();
		return;
	} 
	//config services
	<?php foreach($this->services as $service): ?>
	if ( pressbutton == 'config_extensions_<?php echo $service->id; ?>'){
		form.service_id.value = '<?php echo $service->id; ?>';
		form.service_name.value = '<?php echo JText::_($service->ws_name, true); ?>';
		submitform( 'config_multi_extensions' );
		return;
	} 
	<?php endforeach; ?>
	
	submitform( pressbutton );
}
//]]>
</script>

<form name="adminForm" id="adminForm" action="index.php" method="post">
  <div id="ja-filter">
    <table width="100%">
      <tr>
        <td align="left"><?php 
		$tipid = uniqid("ja-tooltip-");
		$linkRepo = "<a href=\"#\" id=\"{$tipid}\" class=\"ja-tips-title hasPopover\" data-original-title=\"".JText::_("JA_REPOSITORY")."\" data-content=\"".JA_WORKING_DATA_FOLDER."\">".JText::_("JA_REPOSITORY")."</a>";
		$linkEditRepo = "<a href=\"index.php?option=com_jaextmanager&view=default&layout=config_service\" title=\"\">".JText::_("EDIT")."</a>";
		$linkUpload = "<a href=\"#\" onclick=\"jaOpenUploader(); return false;\" title=\"".JText::_("UPLOAD")."\" class=\"highlight\">".JText::_("UPLOAD")."</a> ";
		$linkHelp = "<a href=\"index.php?option=com_jaextmanager&view=default&layout=help_support\" title=\"".JText::_("HELP_AND_SUPPORT")."\" class=\"highlight\">".JText::_("HELP_AND_SUPPORT")."</a>";
		$intro = "All versions of extensions are stored in %s (%s), to start using auto update of supported extension, %s new version of the extension now.<br /> Please read %s for more information.<br />";
		$intro = JText::sprintf($intro, $linkRepo, $linkEditRepo, $linkUpload, $linkHelp);
		echo $intro;
		?>
        </td>
        <td align="right" valign="top" width="260">
	        <?php echo JText::_("FILTER");?>:
			<input type="text" class="text_area" value="<?php echo $this->lists['search']; ?>" id="search" name="search"/>
        </td>
        <td align="right" valign="top" width="260">
          <?php echo $this->boxType;?>
          <input type="button" onclick="this.form.submit();" value="<?php echo JText::_('GO'); ?>" />
        </td>
      </tr>
    </table>
  </div>
  <fieldset>
  <legend><?php echo JText::_("EXTENSIONS");?></legend>
  <?php if (isset($this->showMessage) && $this->showMessage) : ?>
  <?php echo $this->loadTemplate('message'); ?>
  <?php endif; ?>
  <?php if (count($this->listExtensions)) : ?>
  <table class="adminlist table table-striped ja-uc" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th width="10"><?php echo JText::_('NUM' ); ?></th>
        <th width="20"> <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this, 'cId');" />        </th>
        <th width="200" style="text-align: left;" nowrap="nowrap"> <?php echo JText::_('EXTENSION_NAME' ); ?> </th>
        <th style="text-align: left;"><?php echo JText::_('AUTHOR' ); ?></th>
        <th><?php echo JText::_('TYPE' ); ?></th>
        <th width="80"> <?php echo JText::_('VERSION' ); ?> </th>
        <th> <?php echo JText::_('CREATED_DATE' ); ?> </th>
        <th width="150"><?php echo JText::_('SERVICE' ); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $index = 0;
      foreach ($this->listExtensions as $key=>$obj):
        $obj->index = $index++;
		
		$obj->img    = $obj->enabled ? 'tick.png' : 'publish_x.png';
		$obj->task   = $obj->enabled ? 'disable' : 'enable';
		$obj->alt    = $obj->enabled ? JText::_('ENABLED' ) : JText::_('DISABLED' );
		$obj->action = $obj->enabled ? JText::_('DISABLE' ) : JText::_('ENABLE' );

		if ($obj->protected) {
			$obj->cbd    = 'disabled';
			$obj->style  = 'color:#999999;';
		} else {
			$obj->cbd    = null;
			$obj->style  = null;
		}
		//$obj->author_info = @$obj->authorEmail .'<br />'. @$obj->authorUrl;
		$extID = $obj->extId;
		$css = "row".($index%2);
		
		$diffDate = $this->nicetime($obj->creationDate);
    ?>
      <tr class="row1" style="border-bottom:1px solid #CCC;">
        <td valign="top"><?php echo $this->pagination->getRowOffset( $obj->index ); ?></td>
        <td valign="top"><input type="checkbox" id="cId<?php echo $obj->index;?>" name="cId[]" value="<?php echo $extID; ?>" onclick="Joomla.isChecked(this.checked);" <?php echo $obj->cbd; ?> /></td>
        <td valign="top"><strong class="addon-name"><?php echo $obj->name; ?></strong> </td>
        <td valign="top"><?php 
			//fix url
			if(strpos($obj->authorUrl, "http") !== 0) {
				$obj->authorUrl = "http://".$obj->authorUrl;
			}
			
			$tipid = uniqid("ja-tooltip-");
			$authorTip = JText::_('WEBSITE') . ": <a href=\"{$obj->authorUrl}\" title=\"\">{$obj->authorUrl}</a><br />";
			$authorTip .= JText::_('EMAIL') . ": <a href=\"mailto:{$obj->authorEmail}\" title=\"\">{$obj->authorEmail}</a><br />";
			?>
			<a id="<?php echo $tipid; ?>" class="ja-tips-title author hasPopover" data-original-title="<?php echo $obj->author ?>" data-content="<?php echo JHtml::_('tooltipText', $authorTip) ?>" href="<?php echo $obj->authorUrl; ?>" target="_blank"><?php echo $obj->author; ?></a>
        </td>
        <td valign="top" align="left">
        <span class="icon-<?php echo $obj->type; ?>" title="<?php echo JText::_($obj->type); ?>"><?php echo JText::_($obj->type); ?></span>
        
        <?php if($obj->coreVersion == 'j15'): ?>
        <span class="joomla-legacy">&nbsp;</span>
        <?php endif; ?>
        </td>
        <td valign="top" align="center"><?php echo (isset($obj->version) && $obj->version != '') ? $obj->version : '&nbsp;'; ?></td>
        <td valign="top" align="center"><?php echo $obj->creationDate; ?>
          <?php if($diffDate !== false): ?>
          <small class="nicetime"><?php echo $diffDate; ?></small>
          <?php endif; ?>        </td>
        <td valign="top" align="center"><a href="#" id="config<?php echo $extID;?>" title="<?php echo addslashes($obj->name); ?>" onclick="configExtensions(this, '<?php echo $extID;?>'); return false;" > <?php echo $obj->ws_name; ?> </a> </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td valign="top">&nbsp;<img src="<?php echo JURI::root().'administrator/components/com_jaextmanager/assets/css/images/arrow_point_right.gif'; ?>" alt="" /></td>
        <td valign="top">
        <div class="clearfix"> 
            <a class="check-update" title="Check Update" href="#" onclick="checkNewVersion('<?php echo $extID;?>', 'LastCheckStatus_<?php echo $extID;?>'); return false;"><?php echo JText::_('CHECK_UPDATE'); ?></a> 
            <a class="recovery" title="<?php echo JText::_('ROLLBACK'); ?>" href="#" onclick="recoveryItem('<?php echo $extID;?>', 'LastCheckStatus_<?php echo $extID;?>'); return false;"><?php echo JText::_('ROLLBACK'); ?></a>        </div>        </td>
        <td colspan="6" class="checkstatus" id="LastCheckStatus_<?php echo $extID; ?>"><?php echo $JaextmanagerModelDefault->getLastCheckStatus($checkLog, $extID);?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
      </tr>
    </tfoot>
  </table>
  <?php else : ?>
  <?php echo JText::_('DATA_NOT_FOUND' ); ?>
  <?php endif; ?>
  <input type="hidden" name="option" value="com_jaextmanager" />
  <input type="hidden" name="view" value="<?php echo JRequest::getVar("view", "default")?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar( 'Itemid');?>" />
  <input type="hidden" name="service_id" id="service_id" value="" />
  <input type="hidden" name="service_name" id="service_name" value="" />
  <?php echo JHtml::_( 'form.token'); ?>
  </fieldset>
</form>
