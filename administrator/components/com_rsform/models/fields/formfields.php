<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFormfields extends JFormFieldList
{
	protected function getOptions()
    {
		$formId = JFactory::getApplication()->input->getInt('formId');

		$options = array();

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->select($db->qn('c.ComponentId'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('LEFT', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ')')
			->where($db->qn('c.FormId') . '=' . $db->q($formId))
			->where($db->qn('p.PropertyName') . '=' . $db->q('NAME'))
			->order($db->qn('c.Order') . ' ' . $db->escape('ASC'));

		if ($fields = $db->setQuery($query)->loadColumn())
		{
			foreach ($fields as $field)
			{
				$options[] = JHtml::_('select.option', $field, $field);
			}
		}

		reset($options);

		return array_merge(parent::getOptions(), $options);
	}
}
