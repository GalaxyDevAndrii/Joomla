<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelRichtext extends JModelLegacy
{
	public function getLang()
	{
		return RSFormProHelper::getCurrentLanguage($this->getFormId());
	}

	public function getEditor()
	{
		return RSFormProHelper::getEditor();
	}

	public function getTextarea()
	{
		$xml = new SimpleXMLElement('<field type="textarea" />');

		$xml->addAttribute('name', $this->getEditorName());
		$xml->addAttribute('rows', 20);
		$xml->addAttribute('cols', 75);
		$xml->addAttribute('class', 'rs_90');

		$field = JFormHelper::loadFieldType('textarea');

		$field->setup($xml, $this->getEditorText());

		return $field;
	}

	public function getNoEditor()
	{
		return JFactory::getApplication()->input->getInt('noEditor');
	}

	public function getEditorName()
	{
		return JFactory::getApplication()->input->getCmd('opener');
	}

	public function getFormId()
	{
		return JFactory::getApplication()->input->getInt('formId');
	}

	public function getEditorText()
	{
		$db 	= $this->getDbo();
		$formId = $this->getFormId();
		$opener = $this->getEditorName();

		$query = $db->getQuery(true)
			->select($db->qn($opener))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));

		$value = $this->_db->setQuery($query)->loadResult();

		$translations = RSFormProHelper::getTranslations('forms', $formId, $this->getLang());
		if ($translations && isset($translations[$opener]))
		{
			$value = $translations[$opener];
		}

		return $value;
	}

	public function saveTranslation($value)
	{
		$formId 		= $this->getFormId();
		$opener 		= $this->getEditorName();
		$lang 			= $this->getLang();
		$translations 	= RSFormProHelper::getTranslations('forms', $formId, $lang, 'id');

		$translation = (object) array(
			'form_id'       => $formId,
			'lang_code'     => $lang,
			'reference'     => 'forms',
			'reference_id'  => $opener,
			'value'         => $value
		);

		if (!isset($translations[$opener]))
		{
			$this->_db->insertObject('#__rsform_translations', $translation);
		}
		else
		{
			$translation->id = $translations[$opener];
			$this->_db->updateObject('#__rsform_translations', $translation, array('id'));
		}
	}
}