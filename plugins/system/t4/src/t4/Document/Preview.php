<?php
namespace T4\Document;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry as JRegistry;
use T4\Helper\Path;
use T4\Helper\Color;
use T4\Helper\Css;
use T4\Helper\Layout;

class Preview extends Template {
	var $key;
	var $mode = 'preview';

	public function __construct($doc) {
		parent::__construct($doc);

		$app = JFactory::getApplication();
		$this->key = $app->input->get('t4preview');

		define('T4PREVEW', 1);

		// check if current page is assigned to correct template style
		$itemid = $app->input->getInt('Itemid');
		$template = $app->getTemplate(true);
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('template_style_id');
        $query->from( $db->quoteName('#__menu') );
        $query->where( $db->quoteName('id') . ' = ' . $db->quote($itemid) );

        $db->setQuery($query);
        $tid = (int) $db->loadResult();

        if ($tid && $template->id != $tid) {
        	define('T4PREVEW_NOT_ASSIGNED', 1);
        }
	}


	protected function renderHead() {
		parent::renderHead();
		// add preview css
		$preview_css = T4PATH_BASE_URI . '/css/preview.css';
		$this->doc->addStylesheet($preview_css, ['version' => 'auto']);
		// \JHtml::_('stylesheet', 'preview.css', array('version' => 'auto', 'relative' => true));
		$preview_js = T4PATH_BASE_URI . '/js/preview.js';
		$this->doc->addScript($preview_js, ['version' => 'auto']);
	}

	protected function getCachekey() {
		return null;
	}


	protected function getCustomCssFilename() {
		$app = \JFactory::getApplication('site');
		$template = $app->getTemplate(true);
		return $template->id . '-preview.css';
	}


	protected function loadTypelistData() {
		parent::loadTypelistData();
		// disable optimize
		$this->doc->params->set('system_optimizecss', false);
		$this->doc->params->set('system_optimizejs', false);

		// load preview data
		$draft = \T4Admin\Draft::load($this->key);

		$groups = ['site', 'navigation', 'theme', 'layout'];
		foreach ($groups as $group) {
			// $name = 'typelist-' . $group;
			// $profile = $this->doc->params->get($name);
			$profile = $this->doc->params->get('typelist-' . $group);
			// check overwrite profile for sub layout
			if ($group == 'layout' && Layout::isSubpage()) {
				$_profile = isset($draft['sub-layout']) ? $draft['sub-layout'] : $this->doc->params->get('sub-layout');
				if ($_profile) {
					$profile = $_profile;
					$draft[$group] = '';
				}
			}

			if (!empty($draft[$group])) {
				$data = $draft[$group];
			} else {
				$content = Path::getFileContent('etc/' . $group . '/' . $profile . '.json');
				if (!$content) {
					$profile = 'default';
					$content = Path::getFileContent('etc/' . $group . '/' . $profile . '.json');
				}
				$data = json_decode($content, true);

			}

			$this->doc->params->set($group . '-settings', new JRegistry($data));

			// add body class
			$this->addBodyClass($group . '-' . str_replace(' ', '-', strtolower($profile)));
		}


	}

}
