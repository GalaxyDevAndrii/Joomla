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
class JFormFieldT4Layout extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 't4layout';
	protected $layout = 'field.t4layout';
	protected function getInput()
	{
		$htmls = '';
  	$t4_layout_path = T4PATH_ADMIN . '/layouts/';
    $rows = json_decode($this->value);
    if(empty($rows->sections)){
      $layout_file = T4\Helper\Path::getFileContent('etc/layout/default.json');
  	$rows = $layout_file ? json_decode($layout_file ) : [];
    }
    $data['id'] = $this->id;
    $data['name'] = $this->name;
    $data['value'] = $this->value;
    $data['layout'] = $rows;
    return \JLayoutHelper::render ($this->layout, $data, T4PATH_ADMIN . '/layouts');

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
		return null;
	}
}
