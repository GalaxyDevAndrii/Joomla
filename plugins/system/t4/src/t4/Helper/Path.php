<?php
namespace T4\Helper;

use Joomla\CMS\Factory as JFactory;

class Path {
	public static function findInTheme($which, $getUri = false, $relative = false) {
		// clean $file
		$arr = preg_split('/\?|\#/', $which);
		$file = $arr[0];

		$path = T4PATH_LOCAL . '/' . $file;
		$uri = T4PATH_LOCAL_URI . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		$path = T4PATH_TPL . '/' . $file;
		$uri = T4PATH_TPL_URI . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		$path = T4PATH_BASE . '/' . $file;
		$uri = T4PATH_BASE_URI . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		$path = JPATH_ROOT . '/' . $file;
		$uri = \JUri::root(true) . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		return null;
	}

	public static function findInBase($which, $getUri = false, $relative = false) {
		// clean $file
		$arr = preg_split('/\?|\#/', $which);
		$file = $arr[0];

		$path = T4PATH_TPL . '/' . $file;
		$uri = T4PATH_TPL_URI . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		$path = T4PATH_BASE . '/' . $file;
		$uri = T4PATH_BASE_URI . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		$path = JPATH_ROOT . '/' . $file;
		$uri = \JUri::root(true) . '/' . $which;
		if (is_file($path)) return $getUri ? self::getUri($uri, $relative) : $path;

		return null;
	}

	public static function getUri($uri, $rel = false) {
		if ($rel) $uri = substr($uri, strlen(\JUri::root(true) . '/'));
		return $uri;
	}


	public static function findT4Layout($layout_file) {
		return self::findInTheme('html/layouts/t4/' . $layout_file);
	}


	public static function getFileContent($path, $local = true) {
		$file = $local ? self::findInTheme($path) : self::findInBase($path);
		return $file ? file_get_contents($file) : '';
	}


	public static function getBaseContent($path) {
		$file = self::findInBase($path);
		return $file ? file_get_contents($file) : '';
	}

	public static function getLocalContent($path) {
		$file = T4PATH_LOCAL . '/' . $path;
		return is_file($file) ? file_get_contents($file) : '';
	}

	public static function removeLocalContent($path) {
		$file = T4PATH_LOCAL . '/' . $path;
		if(is_file($file)) unlink($file);
		return is_file($file) ? false : true;
	}

	public static function saveLocalContent($path, $value) {
		$file = T4PATH_LOCAL . '/' . $path;
		$dir = dirname($file);
		if (!is_dir($dir)) \JFolder::create($dir);
		if (is_array($value)) $value = json_encode($value);
		return \JFile::write($file, $value);
	}


	public static function addIncludePath (&$path) {
		//JPATH_THEMES
		$template_path = str_replace('\\', '/', T4PATH_TPL);
		for($i = count($path)-1; $i >= 0; $i--) {
			$p = str_replace('\\', '/', $path[$i]);
			if (strpos($p, $template_path) === 0) {
				$file = substr($p, strlen($template_path));
				// add base path theme after template path
				$base_path = T4PATH_BASE . $file;
				array_splice($path, $i+1, 0, $base_path);
				// add template local path before template path
				$local_path = T4PATH_LOCAL . $file;
				array_splice($path, $i, 0, $local_path);
			}
		}
		return $path;
	}


	public static function getLayoutPath($path, $layout) {
		// Do 3rd party stuff to detect layout path for the module
		// onGetLayoutPath should return the path to the $layoutFile of $module or false
		// $results holds an array of results returned from plugins, 1 from each plugin.
		// if a path to the $layoutFile is found and it is a file, return that path
		$app	= \JFactory::getApplication();
		$result = $app->triggerEvent( 'onGetLayoutPath', array( $path, $layout ) );
		if (is_array($result))
		{
			foreach ($result as $file)
			{
				if ($file !== false && is_file ($file))
				{
					return $file;
				}
			}
		}
		return false;
	}


	// get file $type in $dir from local, template, base
	public static function files($dir, $ext = 'json') {
		$files = [];

		$path = T4PATH_LOCAL . '/' . $dir;
		$tmp = is_dir($path) ? \JFolder::files($path, '.' . $ext) : [];
		if (!empty($tmp)) $files = array_merge($files, $tmp);

		$path = T4PATH_TPL . '/' . $dir;
		$tmp = is_dir($path) ? \JFolder::files($path, '.' . $ext) : [];
		if (!empty($tmp)) $files = array_merge($files, $tmp);

		$path = T4PATH_BASE . '/' . $dir;
		$tmp = is_dir($path) ? \JFolder::files($path, '.' . $ext) : [];
		if (!empty($tmp)) $files = array_merge($files, $tmp);

		// remove duplicate and sort
		$files = array_unique($files);
		asort($files);

		// put default on top
		$output = ['default' => 'default'];

		foreach ($files as $i => $file) {
			$val = substr($file, 0, -strlen($ext) - 1);
			$output[$val] = $val;
		}

		return array_values($output);
	}
}
