<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';

class JFormFieldDirectorystatus extends JFormField
{
	public function getInput()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('formId'))
			->from($db->qn('#__rsform_directory'))
			->where($db->qn('formId') . ' = ' . $db->q($formId));

		$status = $db->setQuery($query)->loadResult();

		if ($status)
		{
			return '<span class="badge bg-success badge-success">' . JText::_('RSFP_SUBM_DIR_ENABLED') . '</span>';
		}
		else
		{
			return '<span class="badge badge-important bg-danger">' . JText::_('RSFP_SUBM_DIR_DISABLED') . '</span><p><small>' . JText::_('RSFP_SUBM_DIR_DISABLED_INSTRUCTIONS') . '</small></p>';
		}
	}
}
