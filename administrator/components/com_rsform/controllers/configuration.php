<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerConfiguration extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}
	
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_rsform');
	}
	
	public function save()
	{
		$data = JFactory::getApplication()->input->get('rsformConfig', array(), 'array');

		// Get model and save
		$model = $this->getModel('configuration');
		$model->save($data);

		// Reload config
		RSFormProHelper::readConfig(true);
		
		$task = $this->getTask();
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_rsform&view=configuration';
			break;
			
			case 'save':
				$link = 'index.php?option=com_rsform';
			break;
		}
		
		$this->setRedirect($link, JText::_('RSFP_CONFIGURATION_SAVED'));
	}
}