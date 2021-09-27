<?php
/**
* @version 			SEBLOD 2.x Core ~ $Id: pkg_script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2012 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

// No Direct Access
defined( '_JEXEC' ) or die;

// Script
class plgsystemT4InstallerScript
{
	// install
	function install( $parent )
	{		
	}
	
	// uninstall
	function uninstall( $parent )
	{
	}
	
	// update
	function update( $parent )
	{
	}
	
	// preflight
	function preflight( $type, $parent )
	{
	}
	
	// postflight
	public function postflight($route, $adapter)
	{
			// Enable the helper plugin right after install it
		if ( $route == 'install' ) 
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__extensions')
			->set('enabled=1')
			->where(array('element=' . $db->quote('t4'), 'type=' . $db->quote('plugin')));
			$db->setQuery($query);   
			$db->execute();  
			
		}
		return true;
	}
}
?>