<?php
$doc = JFactory::getDocument();
$rows = $displayData['layout'];
$inputId = $displayData['id'];
$inputName = $displayData['name'];
$inputVal = $displayData['value'];
if (\T4\Helper\J3J4::major() < 4) {
    $pathCodeMirrorCore = "editors";
} else {
    $pathCodeMirrorCore = "vendor";
}
// add assets
JFactory::getDocument()->addScript(T4PATH_ADMIN_URI . '/assets/js/t4layout.js');
$doc->addStyleSheet(JUri::root(true) . '/media/'.$pathCodeMirrorCore.'/codemirror/theme/monokai.css');
$doc->addStyleSheet(JUri::root(true) . '/media/'.$pathCodeMirrorCore.'/codemirror/addon/hint/show-hint.css');
$doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/addon/selection/active-line.js');
$doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/addon/hint/show-hint.js');
$doc->addScript(JUri::root(true)  . '/media/'.$pathCodeMirrorCore.'/codemirror/addon/hint/css-hint.js');
use T4Admin\Settings AS Settings;

echo Settings::getRowSettings();
echo Settings::getColSettings();


?>
<div style="display: none">
  <div id="t4-layout-section" class="t4-layout-section" data-sectionid="1" data-cols="1">
  	<div class="t4-section-settings clearfix">
  		<div class="pull-left">
  			<strong class="t4-section-title">Section</strong>
  		</div>
  		<div class="pull-right">
  			<ul class="t4-row-option-list">
  				<li><a class="t4-move-row" href="#" data-tooltip="Move"><i class="fal fa-arrows-alt"></i></a></li>
  				<li><a class="t4-row-options" href="#" data-tooltip="Configure"><i class="fal fa-cog fa-fw"></i></a></li>
  				<li><a class="t4-remove-row" href="#" data-tooltip="Remove"><i class="fal fa-trash-alt fa-fw"></i></a></li>
  			</ul>
  		</div>
  	</div>
  	<div class="t4-row-container ui-sortable">
  		<div class="row ui-sortable">
  			<div class="t4-col t4-layout-col col-md" data-type="block" data-col="12" data-name="none" data-xl="" data-lg="" data-md="" data-sm="" data-xs="">
  				<div class="col-inner clearfix">
  					<span class="t4-column-title">None</span>
  					<span class="t4-col-remove hidden" title="Remove column" data-content="Remove column"><i class="fal fa-minus"></i> </span>
  					<span class="t4-admin-layout-vis" title="Click here to hide this position on current device layout" style="display:none;" data-idx="0"><i class="fal fa-eye"></i></span>
  					<a class="t4-column-options" href="#"><i class="fal fa-cog fa-fw"></i></a>
  				</div>
  			</div>
  		</div>
  	</div>
  	<a class="t4-add-row" href="#"><i class="fal fa-plus"></i><span>Add Row</span></a>
  </div>
</div>
<div class="clearfix"></div>
<!-- Layout Builder Section -->
<div id="t4-layout-builder" class=""></div>

<div class="clearfix"></div>
<input type="hidden" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>" class="t4-layouts" value="<?php echo htmlspecialchars($inputVal); ?>">

<!-- modal block custom css -->
<div class="t4-block-css-modal" style="display:none;">
    <div class="t4-modal-overlay"></div>
    <div class="t4-modal t4-block-css-editor" data-target="#">
        <div class="t4-modal-header">
            <span class="t4-modal-header-title"><i class="fal fa-cog"></i>Block Css Editor</span>
            <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
        </div>
        <div class="t4-modal-inner t4-css-editor-inner">
            <div class="t4-modal-content tab-pane">
                <textarea id="t4_block_css" name="t4_block_css">block css</textarea>
            </div>
        </div>
        <div class="t4-modal-footer">
            <a href="#" class="btn btn-secondary btn-xs t4-block-css-cancel"><span class="fal fa-times"></span> <?php echo JText::_('JCANCEL');?></a>
            <a href="#" class="btn btn-success btn-xs block-css-editor-apply" data-flag="css-editors"><span class="fal fa-check"></span> <?php echo JText::_('JAPPLY');?></a>
        </div>
    </div>
</div>