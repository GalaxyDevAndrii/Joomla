<?php
namespace T4Admin;

class RowColumnSettings{

	public static function getTemplateName(){
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('template')));
		$query->from($db->quoteName('#__template_styles'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('home') . ' = 1');
		$db->setQuery($query);

		return $db->loadObject()->template;
	}

	public static function getColsType(){
		$data = array(
			// 'row' => 'row',
			'component' => 'component',
			'positions' => 'module position',
			'module' => 'module',
			'block' => 'block',
			'element' => 'element',
			// 'spacer' => 'spacer',
		);
		return $data;
	}

	public static function getModules(){
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title,module');
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('published') . ' = 1');
		$query->order('title ASC');
		$db->setQuery($query);
		$dbModNames = $db->loadObjectList();
		foreach($dbModNames as $ModName)
		{
			$options[str_replace('mod_',"",$ModName->module)] = $ModName->title;
		}
		return $dbModNames;
	}

	public static function getModuleStyle(){
		return self::getModuleStyles();

		$style = array('raw','JAxhtml');
		foreach($style as $value){
				$data[$value] = $value;
			}
			return $data;
	}
	public static function getModuleStyles()
	{

		$moduleStyles = [];
		$moduleStyles = array_merge($moduleStyles, self::_getModuleStyles(T4PATH_BASE . '/html/layouts/chromes'));
		$moduleStyles = array_merge($moduleStyles, self::_getModuleStyles(T4PATH_TPL . '/html/layouts/chromes'));
		$moduleStyles = array_merge($moduleStyles, self::_getModuleStyles(T4PATH_LOCAL . '/html/layouts/chromes'));

		//unset tabs styles unsupported
		unset($moduleStyles['tabs']);
		return $moduleStyles;
	}

	static function _getModuleStyles($chromeLayoutPath) {
		$moduleStyles = [];
		if (\JFolder::exists($chromeLayoutPath))
		{
			$layouts = \JFolder::files($chromeLayoutPath, '.*\.php');
			if ($layouts)
			{
				foreach ($layouts as $layout)
				{
					$style = basename($layout, '.php');
					$moduleStyles[$style] = $style;
				}
			}
		}
		return $moduleStyles;
	}

	public static function getBlocks () {
		$templ = self::getTemplateName();
		$bpath = T4PATH_THEMES . '/base/html/layouts/t4/block';
		$tpath = JPATH_ROOT . '/templates/'.$templ.'/html/layouts/t4/block';
		$lpath = JPATH_ROOT . '/templates/'.$templ.'/local/html/layouts/t4/block';
		$bfolder = glob($bpath . '/*' , GLOB_ONLYDIR);
		$lfolder = glob($lpath . '/*' , GLOB_ONLYDIR);
		$tfolder = glob($tpath . '/*' , GLOB_ONLYDIR);
		$directories = array_merge($bfolder,$lfolder,$tfolder);
		$bfile = glob($bpath . '/*.html');
		$lfile = glob($lpath . '/*.html');
		$tfile = glob($tpath . '/*.html');
		$directories_file = array_merge($lfile,$tfile,$bfile);

		if(empty($directories) && empty($directories_file)) return '';
		
		foreach($directories as $folder) {
		    if(is_dir($folder)) {
		        $arr = explode('/',$folder);
		        $folderOpt[] = $arr[count($arr) -1];
		    }
		}
		if(!empty($directories_file)){
			foreach($directories_file as $file) {
			    if(is_file($file)) {
			        $folderOpt[] = basename($file,".html");
			    }
			}
		}
		foreach($folderOpt as $value){
			$opt[$value] = $value;
		}
		asort($opt);
		return $opt;
	}
	public static function getElements () {
		$templ = self::getTemplateName();
		$bpath = T4PATH_THEMES . '/base/html/layouts/t4/element';
		$tpath = JPATH_ROOT . '/templates/'.$templ.'/html/layouts/t4/element';
		$lpath = JPATH_ROOT . '/templates/'.$templ.'/local/html/layouts/t4/element';

		$bfile = glob($bpath . '/*.php');
		$tfile = glob($tpath . '/*.php');
		$lfile = glob($lpath . '/*.php');
		$directories_file = array_merge($lfile,$tfile,$bfile);

		if(empty($directories_file)) return '';

		if(!empty($directories_file)){
			foreach($directories_file as $file) {
			    if(is_file($file)) {
			        $folderOpt[] = basename($file,".php");
			    }
			}
		}
		foreach($folderOpt as $value){
			$opt[$value] = $value;
		}
		asort($opt);
		return $opt;
	}

	public static function getPositions(){

		$db = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('position'));
		$query->from($db->quoteName('#__modules'));
		$query->where($db->quoteName('client_id') . ' = 0');
		$query->where($db->quoteName('published') . ' = 1');
		$query->group('position');
		$query->order('position ASC');
		$db->setQuery($query);
		$dbpositions = $db->loadObjectList();

		$template  = self::getTemplateName();

		$templateXML = \JPATH_SITE.'/templates/'.$template.'/templateDetails.xml';
		$templateXml = simplexml_load_file( $templateXML );
		$options = array();

		foreach($dbpositions as $positions)
		{
			$options[] = $positions->position;
		}

		foreach($templateXml->positions[0] as $position)
		{
			$options[] =  (string) $position;
		}

		ksort($options);

		$opts = array_unique($options);

		$options = array();

		foreach ($opts as $opt) {
			$options[$opt] = $opt;
		}

		return $options;
	}
	public static function getCols($config = ''){
		$data = '';
		if ($config) {
			$cols = explode('+', $config);
			$data = count($cols);
		}
		return $data;
	}
	public static function getColsSetting(){
		// $colGrid = array('12', '6+6', '4+4+4', '3+3+3+3', '4+8', '3+9', '3+6+3', '2+6+4', '2+10', '5+7', '2+3+7', '2+5+5', '2+8+2', '2+4+4+2');
		$colGrid = array('1', '2', '3', '4', '5', '6');
		foreach($colGrid as $value){
			$data[$value] = $value;
		}
		return $data;
	}

	public static function getSettings($config = ''){
		$data = '';
		if ($config) {
			foreach ($config as $key => $value) {

				if(!in_array($key,['contents','settings','uiresizable'])){
					if (!is_string($value)) {
						$value = json_encode($value);
					}
					$data .= ' data-'.$key.'="'.$value .'"';
				}
			}
		}
		return $data;
	}
	public static function getItemType(){
		$data = array(
			'position' => 'Module position',
			'module' => 'module',
			'items' => 'menu Item',
		);
		return $data;
	}
}

