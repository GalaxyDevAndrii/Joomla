<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelConfiguration extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.configuration', 'configuration', array('control' => 'rsformConfig', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData()
	{
		$data = (array) $this->getConfig()->getData();
		
		return $data;
	}
	
	public function getConfig()
	{
		return RSFormProConfig::getInstance();
	}
	
	public function getRSTabs()
	{
		$tabs = new RSFormProAdapterTabs('com-rsform-configuration');
		return $tabs;
	}

	public function save($data)
	{
		JFactory::getApplication()->triggerEvent('onRsformConfigurationSave', array(&$data));

		$db = $this->getDbo();

		if ($data)
		{
			foreach ($data as $name => $value)
			{
				if ($name == 'global.register.code')
				{
					$value = trim($value);
				}

				$object = (object) array(
					'SettingValue' => $value,
					'SettingName' => $name
				);

				$db->updateObject('#__rsform_config', $object, array('SettingName'));
			}
		}
	}
}