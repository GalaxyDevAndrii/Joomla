<?php
namespace T4\Optimizer;

class Css extends Base {

	public function __construct($params) {
		parent::__construct($params);

		// default group
		$this->default_options = [
			'type' => 'text/css',
			'options' => []
		];

		// exclude
		$this->setExclude($params->get('system_optimizecss_exclude', ''));

		// output path
		$this->outputpath = '/optimize/css';
		$this->outputext = '.css';
	}

	// protected function getMinifier() {
	// 	return new Minify\Css();
	// }

	protected function getSources() {
		$doc = \JFactory::getDocument();
		return $doc->_styleSheets;
	}

	protected function setSources($output) {
		$doc = \JFactory::getDocument();
		$doc->_styleSheets = $output;
	}
}