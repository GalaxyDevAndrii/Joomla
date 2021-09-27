<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2009 - 2020 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

/* Autoloader function and registration */

function RSFormProAutoload($class)
{
	$class = strtolower($class);
	$prefix = 'rsformproadapter';

	if (strpos($class, $prefix) === 0)
	{
		// Grab name and filter it
		$name = preg_replace('/[^a-z]/i', '', substr($class, strlen($prefix)));

		// Supported Joomla! versions
		$versions = array('4.0', '3.0');

		// Iterate through and find the first available class
		foreach ($versions as $version)
		{
			$file = __DIR__ . '/adapters/' . $version . '/' . $name . '.php';
			if (version_compare(JVERSION, $version, '>=') && file_exists($file))
			{
				require_once $file;
				break;
			}
		}
	}
}

spl_autoload_register('RSFormProAutoload');