<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldTotalfields extends JFormFieldList
{
	protected function getOptions()
    {
		$options = array();

		$types = array(RSFORM_FIELD_TEXTBOX, RSFORM_FIELD_HIDDEN);

		JFactory::getApplication()->triggerEvent('onRsformDefineTotalFields', array(&$types));

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$formId = $this->form->getValue('formId');

		// need to get the component type name so that we can load the specific class
		$query->clear()
			->select($db->qn('p.PropertyValue', 'name'))
			->from($db->qn('#__rsform_properties', 'p'))
			->join('LEFT', $db->qn('#__rsform_components', 'c').' ON ('.$db->qn('c.ComponentId').' = '.$db->qn('p.ComponentId').')')
			->join('LEFT', $db->qn('#__rsform_component_types', 'ct').' ON ('.$db->qn('ct.ComponentTypeId').' = '.$db->qn('c.ComponentTypeId').')')
			->where($db->qn('c.FormId') . ' = ' . $db->q($formId))
			->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'))
			->where($db->qn('ct.ComponentTypeId') . ' IN (' . implode(',', $db->q($types)) . ')')
			->order($db->qn('c.Order') . ' ASC');


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
