<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelRestorescreen extends JModelAdmin
{
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.restorescreen', 'restorescreen', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getIsWritable()
	{
		return is_writable($this->getTempDir());
	}
	
	public function getTempDir()
	{
		return JFactory::getConfig()->get('tmp_path');
	}
}