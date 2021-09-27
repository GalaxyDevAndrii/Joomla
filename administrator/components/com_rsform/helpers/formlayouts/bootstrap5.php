<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/../formlayout.php';

class RSFormProFormLayoutBootstrap5 extends RSFormProFormLayout
{
	public $errorClass      = '';
    public $fieldErrorClass = 'is-invalid';

	public $progressContent = '<div><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100" style="width: {percent}%;"><em>{page_lang} <strong>{page}</strong> {of_lang} {total}</em></div></div></div>';
	
	public function __construct() {
		if (JFactory::getDocument()->direction == 'rtl') {
			$this->progressContent = '<div><div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100" style="width: {percent}%;"><em>{total} {of_lang} <strong>{page}</strong> {page_lang}</em></div></div></div>';
		}
		$this->progressOverwritten = true;
		parent::__construct();
	}
	
	public function loadFramework()
	{
		if (version_compare(JVERSION, '4.0', '<'))
		{
			// Load the CSS files
			$this->addStyleSheet('com_rsform/frameworks/bootstrap5/bootstrap.min.css');

			// Load jQuery
			$this->addjQuery();

			// Load Javascript
			$this->addScript('com_rsform/frameworks/bootstrap5/bootstrap.bundle.min.js');

			// Set the script for the tooltips
			$script = array();
			$script[] = 'window.addEventListener(\'DOMContentLoaded\', function() {';
			$script[] = '   var tooltipTriggerList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="tooltip"]\'));';
			$script[] = '   var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {';
			$script[] = '       return new bootstrap.Tooltip(tooltipTriggerEl)';
			$script[] = '   });';
			$script[] = '});';

			$this->addScriptDeclaration(implode("\n", $script));
		}
		else
		{
			// Joomla! 4 has Bootstrap 5.0 built-in
			try
			{
				JHtml::_('jquery.framework');
				JHtml::_('bootstrap.framework');
				JHtml::_('bootstrap.loadCss', true, JFactory::getDocument()->direction);
				JHtml::_('bootstrap.tooltip');
			}
			catch (Exception $e)
			{
				
			}
		}
	}

    public function generateButton($goto)
    {
        return '<button type="button" class="rsform-submit-button rsform-thankyou-button btn btn-primary" name="continue" onclick="'.$goto.'">'.JText::_('RSFP_THANKYOU_BUTTON').'</button>';
    }
}