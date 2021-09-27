<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
use Joomla\CMS\Layout\LayoutHelper;
class JFormFieldTempDetail extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'tempDetail';
	protected function getInput()
	{
		$html = '';
		// render modal google font
		// get path name
		// get google fonts name
		$file = T4PATH_ADMIN . "/etc/googlefonts/fonts.json";
		$googleFont = '';
		if(is_file($file)) $googleFont = json_decode(file_get_contents($file));

		//get base font name
		$customFont = \T4\Helper\Path::getFileContent('/etc/customfonts.json');

		// inject data-value attribute
		$data['googlefont'] = $googleFont;
		$data['customfont'] = @json_decode($customFont);
		return \JLayoutHelper::render ('field.customfont', $data, T4PATH_ADMIN . '/layouts');
	}

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{

		$html = '';
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;
		$desc = $this->element['description1'] ? (string) $this->element['description1'] : '';
		$desc = $this->translateLabel ? JText::_($desc) : $desc;
		$class = !empty($this->class) ? ' ' . $this->class : '';
		$data = self::getTemplateInfo();
		$tplName = self::getTemplate();
		$layout = $tplName.'.templateInfo';
		return LayoutHelper::render($layout, ['lable' =>$text, 'desc'=> $desc, 'info'=> $data, 'tplName'=> $tplName], JPATH_ROOT.'/templates');
	}

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$input = JFactory::getApplication()->input;
		// get template name
		$path = str_replace (JPATH_ROOT, '', dirname(__DIR__));
		$path = str_replace ('\\', '/', substr($path, 1));

		$doc = JFactory::getDocument();


		// get google fonts name
		$file = T4PATH_ADMIN . "/etc/googlefonts/fonts.json";
		$datas = json_decode(file_get_contents($file));
		//get base font name
		$customfile =  T4PATH_LOCAL . '/etc/customfonts.json';
		if(is_file($customfile)) $customFont = json_decode(file_get_contents($customfile));
		if(!empty($customFont->fonts)){
			foreach ($customFont->fonts as $fonts) {
				$fontCustom[] = $fonts;
			}
			$dataFonts = array_merge($datas,$fontCustom);
		}else{
			$dataFonts = array_merge($datas);
		}
		$JVersion = \JVersion::MAJOR_VERSION;
		// add custom-style-style
		$script = 'var t4_ajax_url = "' . \JUri::base() . '"';
		$script .= ', jversion = "' . $JVersion . '"';
		$script .= ', t4_site_root_url = "' . JUri::root() . '"';
		$script .= ', site_all_fonts = ' . json_encode($dataFonts);
		$script .= ', templateName = "' . $this->getTemplate(). '"';
		$script .= ', defaultTemp = "' . $this->getDefaultTemp(). '"';
		$script .= ', allTempl = '.json_encode(str_replace("\r\n","",$this->getAllT4Template()));
		$script .= ',tempId = "'.$input->get('id'). '"';

		$doc->addScriptDeclaration ($script);
		if($doc->direction == 'rtl'){
			$doc->addStyleSheet (JUri::root() . $path . '/assets/css/admin-rtl.css');
		}else{
			$doc->addStyleSheet (JUri::root() . $path . '/assets/css/admin.css');
		}
		$doc->addStyleSheet (JUri::root() . $path . '/assets/fonts/font-awesome5/css/all.min.css');
		$doc->addStyleSheet (JUri::root() . $path . '/assets/css/legend.css');
		$doc->addScript (JUri::root() . $path . '/assets/js/jquery-resizable.js');
		$doc->addScript (JUri::root() . $path . '/assets/js/t4admin.js');
		//$doc->addScript ('http://livejs.com/live.js#css');
		return parent::setup($element, $value, $group);
	}
	public function getTemplate() {
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$id = $input->get('id');
		$query = $db->getQuery(true);
		$query
			->select('template')
			->from('#__template_styles')
			->where('client_id = 0');
		$query->where('id='. $id);
		$db->setQuery($query);
		return $db->loadResult();
	}
	public function getDefaultTemp() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('id')
			->from('#__template_styles')
			->where('client_id = 0');
		$query->where('home = '. $db->quote('1'));
		$db->setQuery($query);
		return $db->loadResult();
	}
	public function getAllT4Template(){
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$id = $input->get('id');
		$tempName = self::getTemplate();
		$query = $db->getQuery(true);
		$query
			->select('a.id AS value, a.title AS title, a.home AS home, l.image AS image')
			->from($db->quoteName('#__template_styles', 'a'))
			// Join over the language.
			->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.home'))
			->where('client_id = 0')
			//add check oder by a.id
			->order('a.id ASC');
		$query->where('template=' .$db->quote($tempName));
		// $query->where('id !='. $id);
		$db->setQuery($query);
		$tempObj = $db->loadObjectList();
		return LayoutHelper::render('field.currentstyle', ['data' => $tempObj, 'cid' => $id], T4PATH_ADMIN . '/layouts');

	}

	public function getTemplateInfo(){
		$telem = 't4_blank';
		$felem = 't4';
		$db = \JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$id = $input->get('id');
		$query = $db->getQuery(true);
		$query
	  ->select('*')
	  ->from('#__updates')
	  ->where('(element = ' . $db->q($telem) . ') OR (element = ' . $db->q($felem) . ')');
		$db->setQuery($query);
		$results = $db->loadObjectList('element');
		return $results;
	}


}
