<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry;

class Author {
	public static function render($item, $params, $displayType,$templateParams) {
		$siteConfig = Path::getFileContent('etc/site/' . $templateParams->get('typelist-site') . '.json');
		if(!$siteConfig){
			$siteConfig = Path::getFileContent('etc/site/default.json');
		}
		$params = new Registry(json_decode($siteConfig,true));
		if($params->get('author_position','') == $displayType){
			$model = \JModelLegacy::getInstance('Author', 'ContentModel', array('ignore_request' => true));
			$author = $model->getAuthor($item->created_by);

			return LayoutHelper::render('t4.content.author_info', ["author"=> $author,'link' =>true,'class'=>'author-block-post-detail pos_'.$displayType] , T4PATH_BASE . '/html/layouts');
		}
		return "";
	}
}