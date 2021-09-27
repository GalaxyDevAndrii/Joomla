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

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

JText::script('ONLINE');
JText::script('LOGIN_FAILED');
JText::script('CHECKING');
JText::script('YOUR_SETTING_IS_SUCCESSFULLY_SAVED');
JText::script('WRONG_USERNAME_AND_PASSWORD_LOGIN_FAILED_PLEASE_TRY_AGAIN');
JText::script('NEW_SERVICE_SUCCESSFULLY_ADDED');

$services = $this->services; 
$lists = $this->lists; 
$page = $this->pageNav; 

$backLink = 'index.php?option=com_jaextmanager&view=services';

$view = 'services';
$viewLink = 'index.php?tmpl=component&option=com_jaextmanager&view='.$view.'&viewmenu=0&task=%s&cid[]=%d&number=%d';
$linkNew = sprintf($viewLink, 'edit', 0, 0);
?>
<script type="text/javascript">
/*<![CDATA[*/
Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'add' || pressbutton == 'edit') {
		jaCreatePopup('<?php echo $linkNew; ?>', 550, 370, '<?php echo JText::_("NEW_REMOTE_SERVICE", true)?>');
	} else if (pressbutton == 'remove') {
		var selected = jQuery('input[name^=cid]:checked').val();
		if(jQuery('#chkDel' + selected).val() == 0) {
			alert('<?php echo JText::_('CAN_NOT_DELETE_CORE_OR_DEFAULT_SERVICE', true); ?>');
			return false;
		} else {
			form.task.value = pressbutton;
			form.submit();
		}
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}
/*]]>*/
</script>

<form method="post" name="adminForm" id="adminForm">
  <?php echo JHtml::_( 'form.token' ); ?>
  <input type="hidden" name="option" value="com_jaextmanager" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="view" value="<?php echo $view; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" />
  <fieldset>
  <legend><?php echo JText::_('SERVICES_MANAGER'); ?></legend>
  <table class="adminlist table table-striped ja-uc">
    <thead>
      <tr>
        <th width="2%" align="left"> <?php echo JText::_('NUM' ); ?> </th>
        <th width="2%">&nbsp;    </th>
        <th> <?php echo JText::_("SERVICE_NAME"); ?> </th>
        <th width="12%"> <?php echo JText::_("SERVICE_STATUS"); ?> </th>
        <th> <?php echo JText::_("MODE"); ?> </th>
        <th> <?php echo JText::_("SERVICE_URL"); ?> </th>
        <th> <?php echo JText::_("USERNAME"); ?> </th>
        <th width="5%"> <?php echo JText::_("DEFAULT"); ?> </th>
        <th width="5%">&nbsp;</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="12"><?php echo $page->getListFooter(); ?> </td>
      </tr>
    </tfoot>
    <?php
	$count=count($services);
	$ids = array();
	if( $count>0 ) {
	for ($i=0;$i<$count; $i++) {
		$item	= $services[$i];
		if ($item->ws_mode == 'remote') {
			$ids[] = $item->id;
		}
		JFilterOutput::objectHtmlSafe($item);
		$title=JText::_('EDIT_SERVICES_')." ID: ".$item->id;	
		$linkEdit = sprintf($viewLink, 'edit', $item->id, $i);
		
		$deleted = ($item->ws_core || $item->ws_default) ? 0 : 1;
		$core = ($item->ws_core) ? '<sup style="color:red;">['.JText::_('CORE').']</sub>' : '';
		?>
    <tr>
      <td><?php echo $page->getRowOffset( $i ); ?> </td>
      <td>
        <input type="radio" id="cb<?php echo $item->id; ?>" name="cid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);" />
        <input type="hidden" id="chkDel<?php echo $item->id; ?>" name="chkDel<?php echo $item->id; ?>" value="<?php echo $deleted; ?>" />
      </td>
      <td><span id="ws_name<?php echo $item->id?>"> <?php echo $item->ws_name . $core;?> </span></td>
      <td>
				<span id="ws_status<?php echo $item->id?>">
					<small>
						<?php if($item->ws_mode == 'remote'): ?>
							<img style="height:12px" src="<?php echo JUri::base().'components/com_jaextmanager/assets/images/checking.gif' ?>" />
							<?php echo JText::_('CHECKING') ?>
						<?php endif ?>
					</small>
				</span>
			</td>
      <td><span id="ws_mode<?php echo $item->id?>"> <?php echo $item->ws_mode;?> </span></td>
      <td><span id="ws_uri<?php echo $item->id?>"> <?php echo $item->ws_uri;?> </span></td>
      <td><span id="ws_user<?php echo $item->id?>"> <?php echo $item->ws_user;?> </span></td>
      <td align="center">
      <span id="ws_default<?php echo $item->id?>">
        <?php if($item->ws_default ==1): ?>
        <img  border="0" alt="" src="components/com_jaextmanager/assets/images/icon-16-default.png"/>
        <?php endif; ?>
        </span>        
      </td>
      <td align="center"><a href="#" title="<?php echo JText::_('EDIT'); ?>" onclick="jaCreatePopup('<?php echo $linkEdit; ?>', 550, 370, '<?php echo JText::_($item->ws_name . " [Edit]", true)?>'); return false;"><?php echo JText::_('EDIT'); ?></a></td>
    </tr>
    <?php }?>
    <?php }else{ ?>
    <tr>
      <td colspan="5"><?php echo JText::_("HAVE_NO_RESULT")?> </td>
    </tr>
    <?php } ?>
  </table>
  </fieldset>
</form>
<script>
jQuery( document ).ready(function( $ ) {
	// auto check status
	var ids = <?php echo json_encode($ids) ?>;
	for (var i = 0; i < ids.length; i++) {
    $.ajax({
			url: "index.php?option=com_jaextmanager&view=services&task=status&sid="+ids[i],
			cache: false
		})
		.done(function( result ) {
			var msg = JSON.parse(result),
			ele = $('#ws_status'+msg.sid+' small');
			
			if (msg.status) 
				ele.html('<b style="color:green">'+Joomla.JText._('ONLINE')+'</b>');
			else 
				ele.html('<b style="color:red">'+Joomla.JText._('LOGIN_FAILED')+'</b>');
		});
	}
});

jQuery(document).on('afterCreatePopup', function(){
	jQuery('#japopup_as').on('click', function(e){
		var iframe = jQuery('#iContent').contents(),
		data = iframe.find("#service_info").serialize(),
		sbtn = jQuery(e.target),
		sbtn_text = sbtn.text(),
		cbtn = jQuery('#japopup_ac'),
		noti = jQuery('<small>',{
			id: 'savenoti',
			html: '<img style="float:left" src="<?php echo JUri::base().'components/com_jaextmanager/assets/images/checking.gif' ?>" />&nbsp;'+Joomla.JText._('CHECKING'),
			css: ({
				'position':'absolute',
				'top':'9px',
				'left':'6px'
			})
		});
		
		jQuery('#savenoti').remove();
		sbtn.prop( "disabled", true );
		sbtn.parent().append(noti);
		jQuery.post(
			"index.php?option=com_jaextmanager&view=services&task=saveIFrame",
			data
		).done(function( result ) {
			var response = JSON.parse(result);
			jQuery('#japopup-wait').css('display','none');
			sbtn.prop( "disabled", false );
			sbtn.text(sbtn_text);
			switch (response.status) {
				case 1:
				case 2:
					noti.css('color','green');
					noti.html('<span class="icon-checkmark-2"></span>'+Joomla.JText._(response.msg));	
					setTimeout(function (){
						jaFormHideIFrame();
						location.reload();
					},1000);
					break;
					
				case 3:	
				default:
					noti.html('<span class="icon-unpublish"></span>'+Joomla.JText._(response.msg));
					break;
			}

		});
	});
});
</script>