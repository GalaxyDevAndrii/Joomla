<?php
namespace T4\Optimizer;

class Base {

	var $params;
	var $default_options;
	var $exclude;
	var $outputpath;
	var $outputext;

	public static function run() {
		if (!\T4\T4::isT4()) return;
		if (\T4\T4::inEdit()) return;

		require_once T4PATH . '/vendor/autoload.php';

		$tmpl = \JFactory::getApplication()->getTemplate(true);
		if ($tmpl->params->get('system_optimizecss', 0)) {
			$css = new Css($tmpl->params);
			$css->optimize();
		}

		if ($tmpl->params->get('system_optimizejs', 0)) {
			$js = new Js($tmpl->params);
			if ($tmpl->params->get('system_optimizejs', 0))
				$js->optimize();
			else $js->clean();
		}
	}

	public function __construct($params) {
		$this->params = $params;
		$this->docroot = substr(JPATH_ROOT, 0, strlen(JPATH_ROOT) - strlen(\JURI::root(true)));
	}

	public function setExclude ($exclude) {
		$exclude = trim($exclude);
		if ($exclude) {
			$this->exclude = '@(' . preg_replace('@[,\n\r]+@', '|', preg_quote($exclude)) . ')@';
		}
	}

	public function optimize() {

		$output = [];
		$group = [];
		$sources = $this->getSources();
		$last = [];
		foreach ($sources as $url => $attribs) {
			$furl = $this->fixUrl($url);
			$needOptimize = $this->needOptimize($furl, $attribs);

			if ($needOptimize === true) {
				$group[$furl] = $attribs;
			} else {
				// put to last if it is external css
				if ($this->outputext == '.css' && $needOptimize < 0) {
					$last[$url] = $attribs;
				} else {
					// ignore this file, then optimize prev group files
					if (count($group)) {
						$ourl = $this->optimizeGroup($group);
						if ($ourl) {
							$output[$ourl] = $this->default_options;
						} else {
							$output = array_merge($output, $group);
						}
						// reset for new group
						$group = [];
					}
					// put current ignore file to output
					$output[$url] = $attribs;
				}
			}
		}
		if (count($group)) {
			$ourl = $this->optimizeGroup($group);
			if ($ourl) {
				$output[$ourl] = $this->default_options;
			} else {
				$output = array_merge($output, $group);
			}
		}

		// then add last
		foreach ($last as $url => $attribs) {
			$output[$url] = $attribs;
		}

		//apply the change make change
		$this->setSources($output);
	}

	public function clean() {

	}


	protected function needOptimize($url, $attribs) {
		//exclude
		if($this->exclude && preg_match($this->exclude, $url)){
			return 0;
		}
		// external
		if (preg_match('#^(https?:)?//#', $url)) return -1;
		// Lib
		if (preg_match('/^[a-z_]+$/i', $url)) return 0;
		// condition
		if (!empty($attribs['options']) && !empty($attribs['options']['conditional'])) return -2;

		return true;
	}


	protected function optimizeGroup($group) {

		// check if need do optimize
		$files = [];
		$mt = 0;
		foreach ($group as $url => $options) {
			if(empty($url)) continue;
			$file = $this->getPath($url);
			if (!is_file($file)) {
				// unset($group[$url]);
				continue;
			}
			if (($t = @filemtime($file)) && $t > $mt) $mt = $t;
			$files[] = $file;
		}

		if (!count($files)) return false;

		$outputpath = T4PATH_MEDIA . $this->outputpath;
		$outputurl = T4PATH_MEDIA_URI . $this->outputpath;

		if (!\JFile::exists($outputpath)){
			\JFolder::create($outputpath);
			@chmod($outputpath, 0755);
		}

		if (!is_writeable($outputpath)) {
			return false;
		}

		$file = md5(serialize($files)) . $this->outputext;

		$tourl = $outputurl . '/' . $file;
		$tofile = $this->fixPath($outputpath . '/' . $file);

		if (is_file($tofile) && filemtime($tofile) >= $mt) return $tourl;

		$output = '';
		$arr = [];
		foreach ($files as $file) {

			if (preg_match('/media(\/|\\\\)(system|jui)/', $file) || preg_match('/[\.-]min\.(css|js)$/', $file)) {
				// compile last file
				if (count($arr)) {
					$minifier = $this->getMinifier($arr);
					$output .= $minifier->execute($tofile) . "\n";
					$arr = [];
				}
				
				// chang path webfont for font awesome
				if(preg_match('/[\.-]min\.(css)$/', $file)){
					$output .= "/*" . basename($file) . "*/\n";
					$awsminifier = $this->getMinifier($file);
					$output .= $awsminifier->execute($tofile) . "\n";
				}else{
					$output .= "/*" . basename($file) . "*/\n" . file_get_contents($file) . "\n";
				}
			} else {
				$arr[] = $file;
			}
		}

		if (count($arr)) {
			$minifier = $this->getMinifier($arr);
			$output .= $minifier->execute($tofile) . "\n";
			$arr = [];
		}
		// write to output
		\JFile::write($tofile, $output);

		// $minifier = $this->getMinifier();
		// if (!$minifier) return false;
		// $minifier->add($files);
		// $minifier->minify($tofile);

		return $tourl;
	}

	/**
	 * @param   string  $url  url to refine
	 * @return  string  the refined url
	 */
	public function fixUrl($url = ''){
		$url = str_replace(\JURI::root(), \JURI::root(true) . '/', $url);
		// replace uri query & hash
		$url = preg_replace('/([#\?]+.*)$/', '', $url);
		return $url;
	}


	protected function getPath ($url) {
		if ($url[0] === '/') {
			return $this->docroot . $this->fixPath($url);
		}

		return $url;
	}

	protected function fixPath ($path) {
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}


	/* Abstract function, need override in extended class */
	protected function getSources() {
		return [];
	}
	protected function setSources($output) {
	}
	protected function getMinifier($files = []) {
		$minifier = null;
		switch($this->outputext) {
			case '.js':
				$minifier = new Minify\Js($files);
				break;
			case '.css':
				$minifier = new Minify\Css($files);
				break;
		}
		return $minifier;
	}

}
