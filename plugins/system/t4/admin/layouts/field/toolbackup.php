<?php
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;

//group export | import
$groups = ['typelist-site','typelist-navigation','typelist-theme','typelist-layout','system','other'];

//add assets
JFactory::getDocument()->addScript(T4PATH_ADMIN_URI . '/assets/js/tools.js');
?>

<div class="tool-css">
    <h4><?php echo JText::_('T4_ADVANCED_TOOLS_CSS_TITLE') ?></h4>
    <p class="description">
      <?php echo JText::_('T4_ADVANCED_TOOLS_CSS_DESC') ?>
    </p>
    <span class="t4-btn btn-action btn-primary" data-action="tool.css"><i class="fal fa-file-edit"></i><?php echo JText::_('T4_ADVANCED_TOOLS_CSS_LABEL') ?></span>
</div>
<div class="t4-css-editor-modal" style="display:none;">
    <div class="t4-modal-overlay"></div>
    <div class="t4-modal t4-css-editor" data-target="#">
        <div class="t4-modal-header">
            <span class="t4-modal-header-title"><i class="fal fa-cog"></i>Css Editor</span>
            <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
        </div>
        <div class="t4-modal-inner t4-css-editor-inner">
            <div class="t4-modal-content tab-pane">
                <textarea id="t4_code_css" name="t4_css"></textarea>
            </div>
        </div>
        <div class="t4-modal-footer">
            <a href="#" class="btn btn-secondary btn-xs t4-settings-cancel"><span class="fal fa-times"></span> <?php echo JText::_('JCANCEL');?></a>
            <a href="#" class="btn btn-success btn-xs t4-css-editor-apply" data-flag="css-editors"><span class="fal fa-check"></span> <?php echo JText::_('JAPPLY');?></a>
        </div>
    </div>
</div>

<div class="tool-css">
    <h4><?php echo JText::_('T4_ADVANCED_TOOLS_SCSS_TITLE') ?></h4>
    <p class="description">
        <?php echo JText::_('T4_ADVANCED_TOOLS_SCSS_DESC') ?>
    </p>
    <span class="t4-btn btn-action btn-primary" data-action="tool.scss"><i class="fal fa-file-edit"></i><?php echo JText::_('T4_ADVANCED_TOOLS_SCSS_LABEL') ?></span>
</div>
<div id="t4-tool-scss-modal" style="display:none;">
	<div class="t4-modal-overlay"></div>
    <div class="t4-modal t4-css-editor" data-target="#">
        <div class="t4-modal-header">
            <span class="t4-modal-header-title"><i class="fal fa-cog"></i>SCSS Tools</span>
            <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
        </div>
        <div class="t4-modal-inner t4-css-editor-inner">
            <div class="t4-modal-content tab-pane">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#t4-scss-variables" aria-controls="variables" role="tab" data-toggle="tab">Variables</a></li>
                        <li role="presentation"><a href="#t4-scss-custom" aria-controls="custom" role="tab" data-toggle="tab">Custom Style</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="t4-scss-variables">
                            <textarea id="t4-scss-editor-variables">Variables</textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="t4-scss-custom">
                            <textarea id="t4-scss-editor-custom">Custom</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="t4-modal-footer">
            <a href="#" class="btn btn-secondary btn-xs t4-settings-cancel"><span class="fal fa-times"></span> Close</a>
            <a href="#" class="btn btn-success btn-xs" data-action="apply" data-flag="css-editors"><span class="fal fa-check"></span> Save & Compile</a>
            <a href="#" class="btn btn-danger btn-xs" data-action="clean" data-flag="css-editors"><span class="fal fa-trash"></span> Remove Local CSS</a>
        </div>
    </div>
</div>