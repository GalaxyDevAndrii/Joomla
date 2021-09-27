<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Registry\Registry; 
use Joomla\CMS\Form\Form as JForm;
use T4Admin\T4form AS T4form;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldTypeList extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'typelist';
	protected $layout = 'field.typelist';

	protected function getInput()
	{
		$data = [];

		// basic content
		$data['id'] = $this->id;
		$data['name'] = $this->name;
		$data['value'] = $this->value;

		// current type,
		// get type from name: typelist-[type]
		if (!preg_match('/\[typelist-(.+)\]$/', $data['name'], $match)) return '';
		$type = $match[1];

		//$type = (string)$this->element['contenttype'];
		$types = \T4\Helper\Path::files('etc/' . $type);
		if (empty($types)) $types = ['default'];

		$list = [];
		foreach ($types as $name) {
			$list[$name] = T4Admin\Action\Typelist::getStatus($type, $name);
		}

		$data['type'] = $type;
		$data['list'] = $list;
		$data['labelEl'] = parent::getLabel();
		$tplXml = T4PATH_TPL . '/templateDetails.xml';
		// new form
		$xmlfile = \T4\Helper\Path::findInTheme('params/typelist-' . $type . '.xml');
		$forms = JForm::getInstance('typelist-' . $type, $xmlfile);
		$form = new T4form($forms);
		if ($xmlfile) $form->loadFile($xmlfile);
		//load the template
		//overwrite / extend with params of templateDetails
		$form->loadFile($tplXml, true, '//'.$type);
		$data['form'] = $form;

		return \JLayoutHelper::render ($this->layout, $data, T4PATH_ADMIN . '/layouts');
	}

	protected function getLabel() {
		return null;
	}
}
