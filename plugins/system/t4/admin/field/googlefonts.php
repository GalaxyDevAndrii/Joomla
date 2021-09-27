<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');
/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldGooglefonts extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'GoogleFonts';
	protected $layout = 'field.googlefont';
	protected function getInput()
	{

		// inject data-value attribute
		$data['class'] = $this->class;
		$data['value'] = $this->value;
		$data['id'] = $this->id;
		$data['name'] = $this->name;
		$data['attrName'] = $this->element['name'];
		$data['fontType'] = $this->checkFontType($this->value);

		return \JLayoutHelper::render ($this->layout, $data, T4PATH_ADMIN . '/layouts');

	}
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		// get path name
		$path = str_replace (JPATH_ROOT, '', dirname(__DIR__));
		$path = str_replace ('\\', '/', substr($path, 1));

		$doc = JFactory::getDocument();
		$doc->addScript (JUri::root() . $path . '/assets/js/googlefonts.js');
		//$doc->addScript ('http://livejs.com/live.js#css');
		return parent::setup($element, $value, $group);
	}
	public function checkFontType($nameFont){
		// get path name
		$path = str_replace (JPATH_ROOT, '', dirname(__DIR__));
		$path = str_replace ('\\', '/', substr($path, 1));
		//get base font name
		$customFont =  T4PATH_LOCAL ."/etc/fontcustoms.json";$customFont = '';
		if(is_file($customFont)) $datacustomFont = json_decode(file_get_contents($customFont));
		$checkFontType = 'google';
		if(!empty($datacustomFont)){
			forEach((array) $datacustomFont AS $font_name => $baseFont){
				if($nameFont == $font_name){
					 $checkFontType = 'custom';
				}
			}
		}
		return $checkFontType;
	}
}
