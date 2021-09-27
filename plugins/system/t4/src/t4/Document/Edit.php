<?php
namespace T4\Document;

use Joomla\CMS\Factory as JFactory;

class Edit extends Template {
	var $layout = '/t4/edit';
	var $mode = 'edit';

	protected function loadTypelistData () {
		parent::loadTypelistData();
		// disable optimize
		$this->doc->params->set('system_optimizecss', false);
		$this->doc->params->set('system_optimizejs', false);
		// disable addons
		$this->doc->params->set('system_addons', null);
	}

	// protected function renderHead() {
	// 	// load google fonts
	// 	$this->loadGoogleFonts();
	// }

	public function getHead() {
		$wam = \T4\Helper\Asset::getWebAssetManager();
		$wam->enableAsset('font.awesome5');
		$wam->enableAsset('font.awesome4');
		$wam->enableAsset('font.iconmoon');
		//$wam->enableAsset('chosen');
		$wam->enableAsset('fronend.edit');

		return parent::getHead();
	}


	// disable cache for edit
	protected function getCachekey() {
		return null;
	}

}
