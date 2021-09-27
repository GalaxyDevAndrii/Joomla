<?php
namespace T4\Helper;

class J3J4 {
	public static function isJ4() {
		return self::major() > 3;
	}
	public static function isJ3() {
		return self::major() < 4;
	}
	public static function major() {
		return (int) explode('.', JVERSION)[0];
	}

	public static function j4EnableAsset($name) {
		\T4\Helper\Asset::getWebAssetManager()->enableAsset($name);
	}

	public static function enableBootstrap() {
		if (self::major() < 4) {
			\JHtml::_('bootstrap.framework');
		} else {
			self::j4EnableAsset('t4.bootstrap.js');
		}
	}

	public static function checkUnpublishedContent($item) {
		if (self::major() < 4) {
			return $item->state == 0;
		} else {
			$cond = \Joomla\Component\Content\Administrator\Extension\ContentComponent::CONDITION_UNPUBLISHED;
			if (isset($item->stage_condition)) return $item->stage_condition == $cond;
			if (isset($item->condition)) return $item->condition == $cond;
			return false;
		}
	}
}
