<?php
namespace T4\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\Utilities\ArrayHelper;

class ExtraField {

	public static function extendForm($form, $data) {

		$form_name = $form->getName();
		// Extend extra field
		$template = TemplateStyle::getDefault();

		if ($template) {
			// parse xml
			$filePath = JPATH_SITE . '/templates/' . $template . '/templateDetails.xml';
			$base = null;
			if (is_file ($filePath)) {
				$xml = simplexml_load_file($filePath);
				// check t4
				if (isset($xml->t4) && isset($xml->t4->basetheme)) {
					$base = trim(strtolower($xml->t4->basetheme));
				}
			}

			// not an T4 template, ignore
			if (!$base) return;
			// validate base
			$path = T4PATH_THEMES . '/' . $base;
			if (!is_dir($path)) return;

			// define const
			if(!defined('T4PATH_BASE')) define('T4PATH_BASE', T4PATH_THEMES . '/' . $base);
			if(!defined('T4PATH_BASE_URI')) define('T4PATH_BASE_URI', T4PATH_THEMES_URI . '/' . $base);

			// make it compatible with AMM
			if ($form_name == 'com_advancedmodules.module') $form_name = 'com_modules.module';

			$tplpath  = JPATH_ROOT . '/templates/' . $template;
			$formpath = $tplpath . '/etc/form/';
			\JForm::addFormPath($formpath);

			$extended = $formpath . $form_name . '.xml';
			if (is_file($extended)) {
				Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
				$form->loadFile($form_name, false);
			}

			// load extra fields for specified module in format com_modules.module.module_name.xml
			if ($form_name == 'com_modules.module') {
				$module = isset($data->module) ? $data->module : '';
				if (!$module) {
					$jform = Factory::getApplication()->input->get ("jform", null, 'array');
					$module = $jform['module'];
				}
				$extended = $formpath . $module . '.xml';

				if (is_file($extended)) {
					Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
					$form->loadFile($extended, false);
				}
			}

			//extend extra fields
			self::contentExtraFields($form, $data, $tplpath);

			//extend params on t4 plg
			self::onMenuCompareForm($form, $data);
		}
		self::onUserCompareForm($form, $data);

		// Extended by T4
		$extended = T4PATH_ADMIN . '/form/' . $form_name . '.xml';
		if (is_file($extended)) {
			Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
			$form->loadFile($extended, false);
		}

	}

	public static function contentExtraFields($form, $data, $tplpath){
		//load languages
		Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
		if ($form->getName() == 'com_categories.categorycom_content' || $form->getName() == 'com_content.article') {

			// check for extrafields overwrite
			$path = $tplpath . '/etc/extrafields';
			if (!is_dir ($path)) return ;

			$files = \JFolder::files($path, '.xml');
			if (!$files || !count($files)){
				return ;
			}

			$extras = array();
			foreach ($files as $file) {
				$extras[] = \JFile::stripExt($file);
			}
			if (count($extras)) {

				if ($form->getName() == 'com_categories.categorycom_content'){
					
				

					$_xml =
						'<?xml version="1.0"?>
						<form>
							<fields name="params">
								<fieldset name="t4_extrafields_params" label="T4_EXTRA_FIELDS_GROUP_LABEL" description="T4_EXTRA_FIELDS_GROUP_DESC">
									<field name="t4_extrafields" type="list" default="" show_none="true" label="T4_EXTRA_FIELDS_LABEL" description="T4_EXTRA_FIELDS_DESC">
										<option value="">JNONE</option>';
									
									foreach ($extras as $extra) {
										$_xml .= '<option value="' . $extra . '">' . ucfirst($extra) . '</option>';
									}

									$_xml .= '
									</field>
								</fieldset>
							</fields>
						</form>
						';
					$xml = simplexml_load_string($_xml);
					$form->load ($xml, false);

				} else {
					
					$app   = Factory::getApplication();
					$input = $app->input;
					$fdata = empty($data) ? $input->post->get('jform', array(), 'array') : (is_object($data) ? $data->getProperties() : $data);
					
					if (isset($data->attribs) && is_string($data->attribs))
			      	{
			      		$data->attribs = json_decode($data->attribs, true);
			      	}

					if(!empty($fdata['catid']) && is_array($fdata['catid'])) { // create new
						$catid = end($fdata['catid']);
					} else { // edit
						$catid = ($fdata['catid']);
					}

					if($catid){
						$categories = \JCategories::getInstance('Content', array('countItems' => 0 ));
						$category = $categories->get($catid);
						$params = $category->params;
						if(!$params instanceof \JRegistry) {
							$params = new \JRegistry;
							$params->loadString($category->params);
						}

						if($params instanceof \JRegistry){
							$extrafile = $path . '/' . $params->get('t4_extrafields') . '.xml';
							if(is_file($extrafile)){
								\JForm::addFormPath($path);
								$form->loadFile($params->get('t4_extrafields'), false);
							}
						}
					}
				}
			}
		}
	}
	public static function onContentBeforeSave($context, $data, $isNew)
	{
		if(isset($data->attribs)){
			$contentTable = \JTable::getInstance('Content', 'JTable',array());
			$contentTable->load($data->id);
			$oldAttribs = json_decode($contentTable->attribs, true);
			$attribs = json_decode($data->attribs, true);
			foreach ($attribs as $name => $attrib) {
				if(!empty($oldAttribs[$name])){
					$oldAttribs[$name] = $attrib;
				}

			}
			$data->attribs = json_encode($oldAttribs);
		}
	}
	public static function onMenuCompareForm($form,$data)
	{
		$formName = $form->getName();
		if($formName == 'com_menus.item'){
			if(empty($data->request)){
				return;
			}
			$component = $data->request['option'];
			$view = $data->request['view'];
			$layout = isset($data->request['layout']) ? $data->request['layout'] : "default";
			$template = TemplateStyle::getDefault();
			$xmlFile = T4PATH_BASE . '/html/' .$component .'/'.$view.'/'.$layout.'.xml';
			if(is_file($xmlFile)){
				if ($form->loadFile($xmlFile, true, '/metadata') == false)
				{
					throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
				}
			}
		}
		
	}
	public static function onUserCompareForm($form, $data)
	{
		
		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration')))
		{
			return true;
		}
		//check required plugin user profile
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('enabled')
		->from('#__extensions')
		->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
		->where($db->quoteName('element') . ' = ' . $db->quote('profile'));
		if(!$db->setQuery($query)->loadResult()){
			return true;
		}
		// Add the registration fields to the form.
		Form::addFormPath(T4PATH_BASE . '/params');
		Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
		$form->loadFile('user');
	}
}