<?php
$datas = $displayData['googlefont'];
$customfont = $displayData['customfont'];
if(!$datas) return "";
$classEvent = array(
		'events' => 'google-font',
		'filter' => 'font-filter',
		'tabId' 	 => 'google-content'
	);

$li = '';
$i = 0;
$pash = 1;
$base_li = '';

$filter_class = 't4-font-filter';

?>
<div class="t4-<?php echo $classEvent['events'];?>-modal t4-fonts-manager"  style="display:none">
	<div class="t4-modal-overlay"></div>
	<div class="t4-modal t4-<?php echo $classEvent['events'];?>-setting">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fal fa-font"></i>Fonts Manager</h5>
      <a type="button" class="action-t4-modal-close" data-dismiss="modal" aria-label="Close">
        <span class="fal fa-times" aria-hidden="true"></span>
      </a>
    </div>
    <div class="modal-body t4-<?php echo $classEvent['tabId'];?> t4-font">
			<div class="t4-fonts-filter cleafix">
				<input class="font-filter" name="jform-filter" id="t4-<?php echo $classEvent['filter'];?>" type="text" placeholder="Search..">
				<ul class="nav nav-tabs" id="managerFontsTab" role="tablist">
				  <li class="nav-item active">
				    <a class="nav-link tab-google-font active" data-toggle="tab" href="#jub-<?php echo $classEvent['tabId'];?>" role="tab" aria-controls="home" aria-selected="true">Google Fonts</a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link tab-custom-font" data-toggle="tab" href="#custom-<?php echo $classEvent['tabId'];?>" role="tab" aria-controls="profile" aria-selected="false">Custom Fonts</a>
				  </li>
				</ul>
			</div>
			<div class="tab-content" id="managerTabContent">
			  	<div class="tab-pane active" id="jub-<?php echo $classEvent['tabId'];?>" role="tabpanel" aria-labelledby="nav-home-tab">
					<div class="t4-font-filter-message <?php echo $filter_class;?>" style="display:none;"></div>
			      	<div class="tab-content-wrap">
				      	<ul class="jub-fonts">
			      		<?php foreach((array) $datas AS $data):
									if($i == '100'){$pash++;$i = 0;}
									$dataStyle = implode(',',$data->styles);
									?>
									<li class="jub-font jub-<?php echo $classEvent["events"];?>" data-bash="<?php echo $pash;?>" data-pos="<?php echo $i;?>" style="--pos:<?php echo $i;?>;" data-name="<?php echo $data->name;?>" data-category="" data-styles="<?php echo $dataStyle;?>">
											<div class="jub-font-container" title="<?php echo $data->name;?> : <?php echo $dataStyle;?>" style="background-position: 0 calc(-40px * <?php echo $i;?>)">
												<span class="jub-font-name"><?php echo $data->name;?></span>
												<span class="jub-font-styles"><?php echo count($data->styles);?> Styles</span>
											</div>
										</li>
								<?php $i++; endforeach;?>
				      	</ul>
			      	</div>
		  		</div>
			  <div class="tab-pane" id="custom-<?php echo $classEvent['tabId'];?>" role="tabpanel" aria-labelledby="nav-profile-tab">
			  	<div class="t4-font-filter-message <?php echo $filter_class;?>" style="display:none;"></div>
			  	<div class="tab-content-wrap">
				  	<ul class="custom-fonts">
			  		 	<?php if(!empty($customfont->fonts)):?>
					  		<?php foreach((array) $customfont->fonts AS $customFont):
									if(!empty($customFont->styles))$dataStyle = implode(',',$customFont->styles);
								?>
								<li class="custom-font custom-<?php echo $classEvent["events"];?>" data-name="<?php echo $customFont->name;?>" data-category="custom" data-styles="<?php if(!empty($dataStyle)) echo $dataStyle;?>">
									<div class="custom-font-container" title="<?php echo $customFont->name;?>">
										<span class="custom-font-name"><?php echo $customFont->name;?></span>
										<span class="t4-btn btn-action" data-action="fonts.remove" data-tooltip="<?php echo JText::_('T4_FIELD_ADDONS_REMOVE') ?>"><i class="fal fa-trash-alt"></i></span>
									</div>
								</li>
								<?php endforeach;?>
							<?php endif;?>
							<li id="custom-local" class="custom-font custom-<?php echo $classEvent["events"];?> hide" data-name="custom-font" data-category="custom" data-styles="">
								<div class="custom-font-container" title="custom-font">
									<span class="custom-font-name"></span>
									<span class="t4-btn btn-action" data-action="fonts.remove" data-tooltip="<?php echo JText::_('T4_FIELD_ADDONS_REMOVE') ?>"><i class="fal fa-trash-alt"></i></span>
								</div>
							</li>
				  	</ul>
			  	</div>


			  	<div class="form-add-custom-font">
				  	<div class="add-more-custom-font">
							<span class="btn-action active" data-action="font.addcss" data-type="css"><?php echo JText::_('T4_THEME_FIELD_ADD_CSS_CUSTOM') ?></span>
							<span class="btn-action" data-action="font.addfont" data-type="font"><?php echo JText::_('T4_THEME_FIELD_ADD_FONT_CUSTOM') ?></span>
						</div>

						<div class="custom-font-form custom-<?php echo $classEvent["events"];?>">
							<div class="control-group custom-font-url" style="display: none;">
								<div class="control-label"><label><?php echo JText::_('T4_THEME_FONT_CUSTOM_FONT_LABEL') ?></label></div>
								<div class="controls"><textarea id="custom-font-url" class="custom-font-input" name="custom-font-url" rows="3" data-value=""></textarea></div>
								<!-- <div class="control-helper"><?php echo JText::_('T4_THEME_FONT_CUSTOM_FONT_DESC') ?></div> -->
							</div>

							<div class="control-group custom-css">
								<div class="control-label"><label><?php echo JText::_('T4_THEME_FONT_CSS_LABEL') ?></label></div>
								<div class="controls"><textarea id="custom-css" class="custom-font-input" name="custom-css" rows="3" data-value=""></textarea></div>
								<!-- <div class="control-helper"><?php echo JText::_('T4_THEME_FONT_CSS_DESC') ?></div> -->
							</div>

							<div class="fonts-actions">
								<span class="t4-btn btn-action btn-primary" data-action="fonts.save" data-type="custom-<?php echo $classEvent["events"];?>"><?php echo JText::_('T4_THEME_FONT_CUSTOM_ADD') ?></span>
							</div>
				  	</div>
			  	</div>
			  </div>
			</div>
    </div>
  </div>
</div>
<div class="t4-font-weight-popup" style="position:absolute; display: none"></div>
