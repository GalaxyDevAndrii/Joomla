<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformViewRegistration extends JViewLegacy
{
	protected $message;
	
	public function display($tpl = null) {
		$session = JFactory::getSession();
		// Get the message from the session
		$message = $session->get('com_rsform.registration');
		
		if ($message !== null) {
			$this->message = base64_decode($message);
		}
		
		parent::display($tpl);
	}
}