<?php
/**
* @package RSform!Pro
* @copyright (C) 2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemRsformInstallerScript
{
	protected static $minJoomla = '3.7.0';
	protected static $minComponent = '3.0.0';
	
	public function preflight($type, $parent)
	{
		if ($type == 'uninstall')
		{
			return true;
		}

		try
		{
			$jversion = new JVersion();

			if (!$jversion->isCompatible(static::$minJoomla))
			{
				throw new Exception('Please upgrade to at least Joomla! ' . static::$minJoomla . ' before continuing!');
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php'))
			{
				throw new Exception('Please install the RSForm! Pro component before continuing.');
			}

			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/assets.php') || !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php'))
			{
				throw new Exception('Please upgrade RSForm! Pro to at least version ' . static::$minComponent . ' before continuing!');
			}

			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/version.php';

			if (!class_exists('RSFormProVersion') || version_compare((string) new RSFormProVersion, static::$minComponent, '<'))
			{
				throw new Exception('Please upgrade RSForm! Pro to at least version ' . static::$minComponent . ' before continuing!');
			}
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}
}