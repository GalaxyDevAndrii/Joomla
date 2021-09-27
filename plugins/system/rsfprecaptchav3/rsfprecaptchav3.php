<?php
/**
* @package RSform!Pro
* @copyright (C) 2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

define('RSFORM_FIELD_RECAPTCHAV3', 2423);

class plgSystemRsfprecaptchav3 extends JPlugin
{
	protected $autoloadLanguage = true;
	
	// Show field in Form Components
	public function onRsformBackendAfterShowComponents() {
		$input 		= JFactory::getApplication()->input;
		$formId 	= $input->getInt('formId');
		$exists 	= RSFormProHelper::componentExists($formId, RSFORM_FIELD_RECAPTCHAV3);
		$link		= $exists ? "displayTemplate('" . RSFORM_FIELD_RECAPTCHAV3 . "', '{$exists[0]}')" : "displayTemplate('" . RSFORM_FIELD_RECAPTCHAV3 ."')";
		
		?>
		<li><a href="javascript: void(0);" onclick="<?php echo $link;?>;return false;" id="rsfpc<?php echo RSFORM_FIELD_RECAPTCHAV3; ?>"><span class="rsficon rsficon-spinner9"></span><span class="inner-text"><?php echo JText::_('PLG_SYSTEM_RSFPRECAPTCHAV3_LABEL'); ?></span></a></li>
		<?php
	}

	// Show the Configuration tab
	public function onRsformBackendAfterShowConfigurationTabs($tabs) {
		$tabs->addTitle(JText::_('PLG_SYSTEM_RSFPRECAPTCHAV3_LABEL'), 'page-recaptchav3');
		$tabs->addContent($this->showConfigurationScreen());
	}

	private function loadFormData()
	{
		$data 	= array();
		$db 	= JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__rsform_config'))
			->where($db->qn('SettingName') . ' LIKE ' . $db->q('recaptchav3.%', false));
		if ($results = $db->setQuery($query)->loadObjectList())
		{
			foreach ($results as $result)
			{
				$data[$result->SettingName] = $result->SettingValue;
			}
		}

		return $data;
	}

	protected function showConfigurationScreen()
	{
		ob_start();

		JForm::addFormPath(__DIR__ . '/forms');

		$form = JForm::getInstance( 'plg_system_rsfprecaptchav3.configuration', 'configuration', array('control' => 'rsformConfig'), false, false );
		$form->bind($this->loadFormData());

		?>
		<div id="page-recaptchav3" class="form-horizontal">
			<?php
			foreach ($form->getFieldsets() as $fieldset)
			{
				if ($fields = $form->getFieldset($fieldset->name))
				{
					foreach ($fields as $field)
					{
						// This is a workaround because our fields are named "recaptchav3." and Joomla! uses the dot as a separator and transforms the JSON into [recaptchav3][language] instead of [recaptchav3.language].
						echo str_replace('"rsformConfig[recaptchav3][', '"rsformConfig[recaptchav3.', $form->renderField($field->fieldname));
					}
				}
			}
			?>
		</div>
		<?php

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	public function onRsformFrontendInitFormDisplay($args)
	{
		if ($componentIds = RSFormProHelper::componentExists($args['formId'], RSFORM_FIELD_RECAPTCHAV3))
		{
			$all_data = RSFormProHelper::getComponentProperties($componentIds);

			if ($all_data)
			{
				foreach ($all_data as $componentId => $data)
				{
					$args['formLayout'] = preg_replace('/<label (.*?) for="' . preg_quote($data['NAME'], '/') .'"/', '<label $1', $args['formLayout']);
				}
			}
		}
	}

	public function onRsformDefineHiddenComponents(&$hiddenComponents)
	{
		$hiddenComponents[] = RSFORM_FIELD_RECAPTCHAV3;
	}

	public function onBeforeRender()
	{
		// we do not have to run in the administrator section
		if (JFactory::getApplication()->isClient('administrator'))
		{
			return true;
		}

		if (JFactory::getDocument()->getType() !== 'html')
		{
			return true;
		}

		// No RSForm! Pro installed
		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php'))
		{
			return true;
		}

		if (!class_exists('RSFormProHelper'))
		{
			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
		}

		RSFormProHelper::readConfig();

		if (!RSFormProHelper::getConfig('recaptchav3.allpages'))
		{
			return true;
		}

		$key = RSFormProHelper::getConfig('recaptchav3.sitekey');

		if (!$key)
		{
			return true;
		}

		RSFormProAssets::addScript('https://www.google.com/recaptcha/api.js?render=' . urlencode($key));
		RSFormProAssets::addScriptDeclaration('if (typeof window.grecaptcha !== \'undefined\') { grecaptcha.ready(function() { grecaptcha.execute(' . json_encode($key) . ', {action:\'homepage\'});}); }');
	}
}