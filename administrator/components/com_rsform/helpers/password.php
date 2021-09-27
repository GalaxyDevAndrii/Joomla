<?php
/**
 * @package RSForm!Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class RSFormProPassword
{
	protected $passwordField;
	protected $userOptions;
	protected $layout;
	protected $formId;

	static $configSet = false;
	static $init = false;

	public function __construct($formId) {
		$this->formId = $formId;

		// get and set the global user parameters
		$usersparams = RSFormProPassword::getUsersParams();
		$this->userOptions = array();
		$this->userOptions['minLength']    = $usersparams->get('minimum_length');
		$this->userOptions['minIntegers']  = $usersparams->get('minimum_integers');
		$this->userOptions['minSymbols']   = $usersparams->get('minimum_symbols');
		$this->userOptions['minUppercase'] = $usersparams->get('minimum_uppercase');
		$this->userOptions['minLowercase'] = $usersparams->get('minimum_lowercase', 0);

		$this->setConfig();
	}

	public static function getInstance($formId) {
		static $instances = array();
		if (!isset($instances[$formId])) {
			$instances[$formId] = new RSFormProPassword($formId);
		}

		return $instances[$formId];
	}

	public static function getUsersParams() {
		static $params;
		if (is_null($params)) {
			$params = JComponentHelper::getParams('com_users');
		}

		return $params;
	}

	public function setConfig() {
		if (!RSFormProPassword::$configSet) {
			RSFormProAssets::addScript(JHtml::script('plg_system_rsfpregistration/password-strength.js', array('relative' => true, 'version' => 'auto', 'pathOnly' => true)));

			// all password fields from all the forms uses these user options, so we load them here
			$userOptionsScript = 'RSFormProPasswords.userOptions = '.json_encode($this->userOptions);
			RSFormProAssets::addScriptDeclaration($userOptionsScript);
			RSFormProPassword::$configSet = true;
		}
	}

	public function setLayout($layout) {
		$this->layout = $layout;
	}

	public function setPasswordField($field) {
		$this->passwordField = $field;
	}

	public function printInlineScript() {
		$script = '';
		if (!empty($this->passwordField) && !is_null($this->layout)) {
			$props = array(
				'layout' => $this->layout,
				'formId' => $this->formId,
				'field'  => $this->passwordField
			);
			$script .= 'RSFormProPasswords.addForm('.json_encode($props).');';

			// we need to load this just once regardless of the instance
			if (!RSFormProPassword::$init) {
				$script .= "\n";
				$script .= "window.addEventListener('DOMContentLoaded', function() {
					RSFormProPasswords.init();
				});";

				RSFormProPassword::$init = true;
			}
		}

		return $script;
	}
}