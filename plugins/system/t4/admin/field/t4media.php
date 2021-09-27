<?php

defined ('_JEXEC') or die ();
use Joomla\CMS\Form\Field\MediaField;
class JFormFieldT4Media extends MediaField
{
	protected $type = 'T4Media';
	protected function getInput()
	{

		$asset = 't4layout';
		$authorId = JFactory::getUser()->id;
		$mediaUrlj3 = (JFactory::getApplication()->isClient('site') ? '' : '') . "index.php?option=com_media&view=images&tmpl=component&asset=com_templates&author=&fieldid={field-media-id}&ismoo=0&folder=";
		$mediaUrlj4 = (JFactory::getApplication()->isClient('site') ? '' : '') . "index.php?option=com_media&amp;tmpl=component&amp;asset={$asset}&amp;author={$authorId}&amp;fieldid={field-media-id}&amp;path=";
		$mediaId = 't4layout_layout_media';
		// $mediaId = $this->id;
			$outputj3 = '<div id="t4-media-joomla" class="field-media-wrapper" data-basepath="'.\JUri::root().'" data-url="'.$mediaUrlj3.'" data-modal=".modal" data-modal-width="100%" data-modal-height="400px" data-input=".field-media-input" data-button-select=".button-select" data-button-clear=".button-clear" data-button-save-selected=".button-save-selected" data-preview="true" data-preview-as-tooltip="true" data-preview-container=".field-media-preview" data-preview-width="200" data-preview-height="200">
				<div id="imageModal_'.$mediaId.'" tabindex="-1" class="modal joomla-modal hide fade"data-url="'.$mediaUrlj3.'" data-iframe="<iframe class=&quot;iframe&quot; src=&quot;'.$mediaUrlj3.'&quot; name=&quot;Change Image&quot; height=&quot;100%&quot; width=&quot;100%&quot;></iframe>" aria-hidden="true">
					<div class="modal-header">
						<button type="button" class="close novalidate" data-dismiss="modal">×</button>
								<h3>Select Image</h3>
					</div>
					<div class="modal-body" style="max-height: initial; overflow-y: initial;"></div>
					<div class="modal-footer">
						<a class="btn" data-dismiss="modal">Cancel</a>
					</div>
				</div>
				<div class="input-group-append">
					<span rel="popover" class="add-on pop-helper field-media-preview" title="" data-content="No image selected." data-original-title="Selected image." data-trigger="hover">
						<span class="icon-eye" aria-hidden="true"></span>
					</span>
					<input name="'.$this->name.'" id="'.$mediaId.'" value="" readonly="readonly" class="form-control hasTooltip field-media-input t4-layout " data-attrname="background_image" type="text">
						<button type="button" class="btn button-select t4-layout-styles-settings">Select</button>
						<button type="button" class="btn hasTooltip button-clear" title="" aria-label="Clear" data-original-title="Clear">
						<span class="icon-remove" aria-hidden="true"></span>
					</button>
				</div>
			</div>';
				$outputj4 = '<joomla-field-media class="field-media-wrapper" type="image" base-path="'.\JUri::root().'" root-folder="images" url="'.$mediaUrlj4.'" modal-container=".modal" modal-width="100%" modal-height="400px" input=".field-media-input" button-select=".button-select" button-clear=".button-clear" button-save-selected=".button-save-selected" preview="static" preview-container=".field-media-preview" preview-width="200" preview-height="200">
			<div id="imageModal_'.$this->id.'" role="dialog" tabindex="-1" class="joomla-modal modal fade" data-url="'.$mediaUrlj4.'" data-iframe="<iframe class=&quot;iframe&quot; src=&quot;'.$mediaUrlj4.'&quot; name=&quot;Change Image&quot; title=&quot;Change Image&quot; height=&quot;100%&quot; width=&quot;100%&quot;></iframe>" style="display: none;" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title">Change Image</h3>
							<button type="button" class="close novalidate" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div class="modal-body jviewport-height60" style="max-height: initial; overflow-y: initial;">
							<iframe class="iframe" src="index.php?option=com_media&amp;tmpl=component&amp;asset=com_templates&amp;author=&amp;fieldid={field-media-id}&amp;path=" name="Change Image" title="Change Image" width="100%" height="100%"></iframe>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary button-save-selected">Select</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
						</div>
					</div>
				</div>
			</div>
			<div class="field-media-preview"><span class="field-media-preview-icon"></span></div>
			<div class="input-group">
				<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="" readonly="readonly" class="form-control field-media-input t4-layout" data-attrname="background_image" >
				<div class="input-group-append">
					<button type="button" class="btn btn-secondary button-select">Select</button>
					<button type="button" class="btn btn-secondary button-clear"><span class="fa fa-times" aria-hidden="true"></span><span class="sr-only">Clear</span></button>
				</div>
			</div>
		</joomla-field-media>';
			$output = (\T4\Helper\J3J4::isJ3()) ? $outputj3 : $outputj4;
			return $output;
	}
}

?>