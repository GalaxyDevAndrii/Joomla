<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory as JFactory;

class Typelist {
	public static function doLoad() {
		$input = JFactory::getApplication()->input;
		$name =  $input->get('name', 'default');
		$type = $input->get('type');
		$value = \T4\Helper\Path::getFileContent(self::getPath($type, $name));
		$value = @json_decode($value);
		if (empty($value)) $value = [];

		return [
			'value' => $value
		];
	}

	public static function doSave() {
		$input = JFactory::getApplication()->input->post;
		$name =  $input->get('name', 'default');

		$value =  $input->getRaw('value');
		$type =  $input->get('type');

		if (!$name || !$value || !$type) {
			return ['error' => 'Missing params'];
		}

		self::save($type, $name, $value);
		return ['ok' => 1, 'status' => self::getStatus($type, $name)];
	}


	public static function doClone() {
		$input = JFactory::getApplication()->input->post;
		$name =  $input->get('name', 'default');
		$newname =  $input->get('newname', 'default');
		$type =  $input->get('type');

		if (!$name || !$newname || !$type) {
			return ['error' => 'Missing params'];
		}
		// check exist
		if (\T4\Helper\Path::findInTheme(self::getPath($type, $newname))) {
			return ['error' => 'New name existed!'];
		}

		$value = \T4\Helper\Path::getFileContent(self::getPath($type, $name));
		self::save($type, $newname, $value);

		return ['ok' => 1, 'status' => self::getStatus($type, $newname)];

	}

	public static function doDelete() {
		$input = JFactory::getApplication()->input->post;
		$name =  $input->get('name', 'default');
		$type =  $input->get('type');

		if (!$name || !$type) {
			return ['error' => 'Missing params'];
		}
		// check exist
		$file = T4PATH_LOCAL . '/' . self::getPath($type, $name);
		if (!is_file($file)) {
			return ['error' => 'New name existed!'];
		}
		// delete
		if (!\JFile::delete($file)) {
			return ['error' => 'Cannot delete'];
		}

		return ['ok' => 1, 'status' => self::getStatus($type, $name)];
	}


	protected static function getPath($type, $name) {
		return 'etc/' . $type . '/' . $name . '.json';
	}

	protected static function save ($type, $name, $value) {
		if (is_string($value)) {
			$value = json_decode($value, true);
		}

		self::presave($type, $name, $value);

		$value = json_encode($value);

		$file = T4PATH_LOCAL . '/' . self::getPath($type, $name);
		$dir = dirname($file);
		if (!is_dir($dir)) \JFolder::create($dir);
		\JFile::write($file, $value);
	}

	private static function preSave($type, $name, &$value) {
		// preprocess for navigation, favicon file
		if ($type == 'site' && !empty($value['other_faviconFile'])) {
			$tpl = \T4Admin\Admin::getTemplate(true);
			// Convert favicon if it is image
			$faviconfile = $value['other_faviconFile'];
			/*
			if ($faviconfile) {
				if (!preg_match('/\.ico(\?\d+)?$/i', $faviconfile)) {
					// convert to icon format and update
					$source = JPATH_ROOT . '/' . $faviconfile;
					$descfile = $tpl->template . '/' . $name . '.ico';
					$destination = T4PATH_MEDIA . '/' . $descfile;
					if (is_file($source)) {
						require_once T4PATH . '/vendor/autoload.php';
						$ico_lib = new \PHP_ICO( $source, array( array( 16, 16 ), array( 24, 24 ), array( 32, 32 ) , array( 64, 64 ) ) );
						// make folder
						if (!is_dir(dirname($destination))) {
							\JFolder::create(dirname($destination));
						}
						if ($ico_lib->save_ico( $destination )) {
							$favicon = T4PATH_MEDIA_URI . '/' . $descfile;
							// find current version
							$currentvalue = @json_decode(\T4\Helper\Path::getFileContent(self::getPath($type, $name)), true);

							if (!empty($currentvalue['other_faviconFile']) && preg_match('/\.ico\??(\d*)$/i', $currentvalue['other_faviconFile'], $match)) {
								$favicon .= '?' . ((int)$match[1] + 1);
							}
							$value['other_faviconFile'] = substr(str_replace(\JUri::root(true), '', $favicon),1);
						}
					} else {
						// remove
						unset($value['other_faviconFile']);
					}
				}
			}
			*/
		}
	}


	public static function getStatus ($type, $name) {
		$local = $tpl = false;
		// status: org (origin), loc (local only), ovr (overwrite in local)
		$path = '/etc/' . $type . '/' . $name . '.json';
		// check local
		$lfile = T4PATH_LOCAL . $path;
		if (is_file($lfile)) $local = true;
		// check base & template
		$tfile = T4PATH_TPL . $path;
		if (is_file($tfile)) $tpl = true;
		$bfile = T4PATH_BASE . $path;
		if (is_file($bfile)) $tpl = true;

		if ($tpl && $local) return 'ovr';
		if ($tpl) return 'org';
		if ($local) return 'loc';
		return 'del';
	}
}
