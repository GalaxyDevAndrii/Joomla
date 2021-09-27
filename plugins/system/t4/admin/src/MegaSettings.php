<?php
namespace T4Admin;

class MegaSettings{
    public static function getMenuItems(){
    	$html = '';
    	$menu = self::getMenu();

    	foreach ($menu as $type => $items) {
    		$html .= "<div class=\"$type\" style='display:none;'>";
    		$html .= '<ul>';
			foreach ($items as $Item) {
				$html .= "<li class=\"menu-item\" data-itemid='".$Item->id."' data-name=\"itemid-$Item->id\"><a href='#' class=''>$Item->title</a></li>";
			}
    		$html .= '</ul>';
    		$html .= "</div>";
    	}
        return $html;
    }
    public static function getMenu() {
		// get menu, module positions
		$menu = \JFactory::getApplication()->getMenu('site');
		$items = $menu->getMenu('site');

		$menus = array();
		foreach ($items as $item) {
			$menutype = $item->menutype;
			if (!isset($menus[$menutype])) $menus[$menutype] = array();
			if($item->level == 1){
				$mitem = new \stdClass();
				$mitem->id = $item->id;
				// get item link
				$link  = $item->link;
				$params = self::getItemParams($item);
				// Reverted back for CMS version 2.5.6
				switch ($item->type)
				{
					case 'separator':
					case 'heading':
						// No further action needed.
						break;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
						{
							// If this is an internal Joomla link, ensure the Itemid is set.
							$link = $item->link . '&Itemid=' . $item->id;
						}
						break;

					case 'alias':
						$link = 'index.php?Itemid=' . $params->get('aliasoptions');
						break;

					default:
						$link = 'index.php?Itemid=' . $item->id;
						break;
				}
				if (strcasecmp(substr($link, 0, 4), 'http') && (strpos($link, 'index.php?') !== false))
				{
					$link = \JRoute::_($link, true, $params->get('secure'));
				}
				else
				{
					$link = \JRoute::_($link);
				}
				$mitem->link = $item->link;

				$mitem->title = $item->title;
				$mitem->alias = $item->alias;
				$mitem->level = $item->level;
				$mitem->spacer = str_repeat('- ', $item->level);
				$mitem->parent = $item->parent_id;
				$mitem->linksef =  $link;
				$menus[$menutype][] = $mitem;
			}
		}
		return $menus;
	}
	static public function getMenuType(){
		$menu = \JFactory::getApplication()->getMenu('site');
		$menuList = $menu->getMenu('site');
		$option = array();
		foreach ($menuList as $menuItems) {

			$option[$menuItems->menutype] = $menuItems->menutype;
		}
		return $option;

	}
	static public function getItemType(){
		$data = array(
			'position' => 'Module position',
			'module' => 'module',
			'items' => 'menu Item',
		);
		return $data;
	}
	public static function getMenuItem($type = ''){
		$menu = self::getMenu();
		$items = $type ? $menu[$type] : $menu['mainmenu'];
		foreach ($items as $item) {
			$data[$item->id] = $item->title;
		}
		return $data;
	}
	public static function getAllItems($type = ''){
		// get menu, module positions
		$menu = \JFactory::getApplication()->getMenu('site');
		$items = $menu->getMenu('site');

		$menus = array();
		$all = array();
		foreach ($items as $item) {
			$menutype = $item->menutype;
			if (!isset($menus[$menutype])) $menus[$menutype] = array();
			$mitem = new \stdClass();
			$mitem->id = $item->id;
			// get item link
			$link  = $item->link;

			$params = self::getItemParams($item);

			// Reverted back for CMS version 2.5.6
			switch ($item->type)
			{
				case 'separator':
				case 'heading':
					// No further action needed.
					break;

				case 'url':
					if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
					{
						// If this is an internal Joomla link, ensure the Itemid is set.
						$link = $item->link . '&Itemid=' . $item->id;
					}
					break;

				case 'alias':
					$link = 'index.php?Itemid=' . $params->get('aliasoptions');
					break;

				default:
					$link = 'index.php?Itemid=' . $item->id;
					break;
			}
			if (strcasecmp(substr($link, 0, 4), 'http') && (strpos($link, 'index.php?') !== false))
			{
				$link = \JRoute::_($link, true, $params->get('secure'));
			}
			else
			{
				$link = \JRoute::_($link);
			}
			$mitem->link = $item->link;

			$mitem->title = $item->title;
			$mitem->alias = $item->alias;
			$mitem->level = $item->level;
			$mitem->spacer = str_repeat('- ', $item->level);
			$mitem->parent = $item->parent_id;
			$mitem->linksef =  $link;
			if($mitem->parent > 1){
				$menus[$mitem->parent][] = $mitem;
			}else{
				$menus[$menutype][] = $mitem;
			}
		}
		return $menus;
	}
	//add func support J4
	public static function getItemParams($item){

		if(version_compare(JVERSION, '4', 'ge')){
			$params = \JFactory::getApplication()->getMenu()->getParams($item->id);
		}else{
			$params = $item->params;
		}
		return $params;
	}

}
?>
