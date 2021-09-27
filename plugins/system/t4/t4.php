<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors.none
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Application\CMSApplication;
use Joomla\Event\Event;
use Joomla\CMS\Router\Router;
use T4\MVC\Router\T4 as T4Router;
/**
 * Plain Textarea Editor Plugin
 *
 * @since  1.5
 */
class PlgSystemT4 extends JPlugin
{

	var $editorHelper;
	var $updatedRef = false;
	var $menuChanged = false;
	var $t4 = null;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JLoader::registerNamespace('T4', __DIR__ . '/src/t4', false, false, 'psr4');
		JLoader::registerNamespace('T4Admin', __DIR__ . '/admin/src', false, false, 'psr4');

		$this->t4 = \T4\T4::getInstance();

		// add field
		//\Joomla\CMS\Form\FormHelper::addFieldPrefix('T4\\Field');
		define('T4_PLUGIN', $config['name']);

		define('T4PATH', __DIR__);
		define('T4PATH_URI', JUri::root(true) . '/plugins/system/' . T4_PLUGIN);
		define('T4PATH_THEMES', T4PATH . '/themes');
		define('T4PATH_THEMES_URI', T4PATH_URI . '/themes');
		define('T4PATH_MEDIA', JPATH_ROOT . '/media/'. T4_PLUGIN);
		define('T4PATH_MEDIA_URI', JUri::root(true) . '/media/'. T4_PLUGIN);

		define('T4PATH_ADMIN', T4PATH . '/admin');
		define('T4PATH_ADMIN_URI', T4PATH_URI . '/admin');

	}

	protected function isSite() {
		return JFactory::getApplication()->isClient('site');
	}

	/**
	 * Handle data process
	 */
	public function onAfterInitialise() {
		if (!$this->isSite()) return;
		$app = JFactory::getApplication();
		$mode_sef = $app->get('sef',0);
		$T4Router = new T4Router($app,$app->getMenu());
		// We need to make sure we are always using the site router, even if the language plugin is executed in admin app.
		$router = CMSApplication::getInstance('site')->getRouter('site');

		// Attach build rules for SEF.
		// $router->attachBuildRule(array($T4Router, 'preprocessBuildRule'), Router::PROCESS_BEFORE);
		if ($mode_sef)
		{
			$router->attachBuildRule(array($T4Router, 'buildRule'), Router::PROCESS_BEFORE);
			$router->attachBuildRule(array($T4Router, 'postprocessSEFBuildRule'), Router::PROCESS_AFTER);
		}

		// Attach parse rule.
		$router->attachParseRule(array($T4Router, 'parseRule'), Router::PROCESS_BEFORE);
	}
	public function onAfterRoute() {
		if (!$this->isSite()) return;
		$this->t4->init();
	}

	public function onAfterDispatch()
	{
		if(!$this->isSite()) return;
		$app = JFactory::getApplication();
		$temp = $app->getTemplate();
		//check if use t4 template then override layout edit
		if(file_exists(JPATH_ROOT .'/templates/'.$temp . '/error-t4.php')){
			$input = $app->input;
			$inedit = $input->get('layout') == 'edit' || ($input->get('option') == 'com_config' && $input->get('view') != 'templates');
			if($inedit){
				$app->set('themes.base', T4PATH_ADMIN);
				$app->set('theme','theme');
			}
		}

		
	}
	/**
	 * Init T4Admin if T4 template style is editting
	 */
	public function onContentBeforeSave($context, $data, $isNew)
	{
		// Check we are handling the frontend edit form.
		if ($context == 'com_content.form')
		{
			$this->t4->onContentBeforeSave($context, $data, $isNew);
		}
		return true;
	}
	
	public function onContentPrepareForm($form, $data) {

		// Override J3 for admin
		\T4Admin\Admin::initj3();

		$form_name = $form->getName();
		if (!$this->isSite() && $form_name == 'com_templates.style') {
			// load the language
			$this->loadLanguage();

			\T4Admin\Admin::init($form, $data);
		}

		$this->t4->contentPrepareForm($form, $data);

	}
		/**
	 * The display event.
	 *
	 * @param   string    $context     The context
	 * @param   stdClass  $item        The item
	 * @param   Registry  $params      The params
	 * @param   integer   $limitstart  The start
	 *
	 * @return  string
	 *
	 * @since   3.7.0
	 */
	public function onContentAfterTitle($context, $item, $params, $limitstart = 0)
	{
		return $this->t4->onContentAuthordisplay($context, $item, $params, 'after_title');
	}

	/**
	 * The display event.
	 *
	 * @param   string    $context     The context
	 * @param   stdClass  $item        The item
	 * @param   Registry  $params      The params
	 * @param   integer   $limitstart  The start
	 *
	 * @return  string
	 *
	 * @since   3.7.0
	 */
	public function onContentBeforeDisplay($context, $item, $params, $limitstart = 0)
	{
		return $this->t4->onContentAuthordisplay($context, $item, $params, 'before_content');
	}

	/**
	 * The display event.
	 *
	 * @param   string    $context     The context
	 * @param   stdClass  $item        The item
	 * @param   Registry  $params      The params
	 * @param   integer   $limitstart  The start
	 *
	 * @return  string
	 *
	 * @since   3.7.0
	 */
	public function onContentAfterDisplay($context, $item, $params, $limitstart = 0)
	{
		return $this->t4->onContentAuthordisplay($context, $item, $params, 'after_content');
	}

	public function onBeforeCompileHead() {
		if (!$this->isSite() || !\T4\T4::isT4()) return;
		$this->t4->compileHead();
	}


	/**
	 * Clean output html, remove empty column
	 */
	public function onBeforeRender() {
		if (!$this->isSite() || !\T4\T4::isT4()) return;
		$this->t4->beforeRender();
	}

	/**
	 * Clean output html, remove empty column
	 */
	public function onAfterRender() {
		if (!$this->isSite() || !\T4\T4::isT4()) return;
		$this->t4->afterRender();
	}


	/**
	 * Prepare save, make some data modification
	 */
	public function onExtensionBeforeSave($context, $table, $isNew = false) {
		if ($context == 'com_templates.style') {
			\T4Admin\Params::beforeSave($table);
		}
	}
	public static function onAfterGetMenuTypeOptions(&$list, MenusModelMenutypes $model)
	{
		\T4Admin\T4menutype::onAfterGetMenuTypeOptions($list,$model);
	}
	/* Clean T4 cache */
	public function onExtensionAfterSave($context, $table, $isNew) {
		if ($context == 'com_templates.style') {
			\T4\Helper\Cache::clean();
			\T4Admin\Draft::clean();
		}
	}
	/**
	 * Implement event onRenderModule to include the module chrome provide by T4
	 * This event is fired by overriding ModuleHelper class
	 * Return false for continueing render module
	 *
	 * @param   object &$module   A module object.
	 * @param   array $attribs   An array of attributes for the module (probably from the XML).
	 *
	 * @return  bool
	 */
	function onRenderModule(&$module, $attribs)
	{
		// only for Joomla 3 frontend
		if (!$this->isSite() || \T4\Helper\J3J4::major() >= 4) return false;

		static $chromed = false;
		// Chrome for module
		if (\T4\T4::isT4() && !$chromed) {
			$chromed = true;
			// We don't need chrome multi times
			$chromePath = T4PATH_BASE . '/html/modules.php';
			if (file_exists($chromePath)) {
				include_once $chromePath;
			}
		}
		return false;
	}


	/**
	 * Implement event to allow select layout from base theme inside plugin.
	 * These events are fireed by overriding Core Joomla lib: FileLayout, HtmlView, ModuleHelper
	*/
	public function onLayoutIncludePaths (&$path) {
		\T4\Helper\Path::addIncludePath($path);
	}
	public function onHtmlViewAddPath ($type, &$path) {
		\T4\Helper\Path::addIncludePath($path);
	}
	public function onGetLayoutPath($path, $layout)
	{
		if (!defined('T4PATH_BASE')) return false;

		$template = \JFactory::getApplication()->getTemplate();

		if (strpos($layout, ':') !== false)
		{
			$temp = explode(':', $layout);
			$template = $temp[0] === '_' ? $template : $temp[0];
			$layout = $temp[1];
		}

		$files = [];

		if (\T4\Helper\J3J4::isJ3()) {
			// specific for Joomla 3 layout
			$files[] = T4PATH_LOCAL . '/html/' . $path . '/' . $layout . '.j3.php';
			$files[] = T4PATH_TPL . '/html/' . $path . '/' . $layout . '.j3.php';
			$files[] = T4PATH_BASE . '/html/' . $path . '/' . $layout . '.j3.php';
		}

		// Detect layout path in T4 base
		$files[] = T4PATH_LOCAL . '/html/' . $path . '/' . $layout . '.php';
		$files[] = T4PATH_TPL . '/html/' . $path . '/' . $layout . '.php';
		$files[] = T4PATH_BASE . '/html/' . $path . '/' . $layout . '.php';

		foreach ($files as $file) {
			if (is_file($file)) return $file;
		}

		return false;
	}


	/* Process Ajax for T4 Admin */
	public function onAjaxT4(){
		// load the language
		$this->loadLanguage();
		// Clean T4 cache
		\T4\Helper\Cache::clean();
		// Saving
		\T4Admin\Action::run();
	}

	/* Clean media cache */
	public function onAfterPurge($group = null) {
		if ($group == 't4') {
			\JFolder::delete(T4PATH_MEDIA);
		}
	}
}
