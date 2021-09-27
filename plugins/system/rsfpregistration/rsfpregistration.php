<?php
/**
 * @package RSForm!Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

define('RSFP_REG_NONE', 0);
define('RSFP_REG_SELF', 1);
define('RSFP_REG_ADMIN', 2);

class plgSystemRsfpregistration extends JPlugin
{
	private $sendUserEmail;
	private $sendAdminEmail;

	protected $autoloadLanguage = true;

	private $json_elements;

	// Check if we're allowed to run (to avoid fatal errors)
	protected function canRun() {
		if (!class_exists('RSFormProConfig')) {
			if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/config.php')) {
				return false;
			}

			require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/config.php';
		}

		return true;
	}

	// Escape a HTML string
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}

	// Get the registration settings from the database (cached)
	protected function _getRow($formId = null) {
		static $rows = array();

		// Run only once
		if (!isset($rows[$formId])) {
			$rows[$formId] = false;
			if ($row = $this->getTable()) {
				if ($row->load(array('form_id' => $formId, 'published' => 1))) {
					$rows[$formId] = $row;
				}
			}
		}

		return $rows[$formId];
	}

	protected function getTable()
	{
		return JTable::getInstance('RSForm_Registration', 'Table');
	}

	private function hasConsentPlugin()
	{
		return JPluginHelper::isEnabled('system', 'privacyconsent');
	}

	private function hasProfilePlugin()
	{
		return JPluginHelper::isEnabled('user', 'profile');
	}

	// Prepare the data for binding
	protected function _prepareData($post, $row) {
		$merge_vars = &$row->vars;

		$vars = array(
			'name' 		=> '',
			'username' 	=> '',
			'email' 	=> '',
			'email1' 	=> '',
			'email2' 	=> '',
			'password' 	=> '',
			'password1' => '',
			'password2' => ''
		);

		if ($this->hasConsentPlugin())
		{
			$vars['consent'] = '';
		}

		foreach ($vars as $field => $value) {
			if (isset($post[$merge_vars[$field]])) {
				$vars[$field] = $post[$merge_vars[$field]];

				if (is_array($vars[$field])) {
					array_walk($vars[$field], array($this, '_escapeCommas'));
					$vars[$field] = implode(',', $vars[$field]);
				}
			}
		}

		if ($this->hasProfilePlugin())
		{
			$vars['profile'] = array();
			if ($row->profile_fields)
			{
				foreach ($row->profile_fields as $profile_field => $our_field)
				{
					if (!empty($our_field) && isset($post[$our_field]))
					{
						if (is_array($post[$our_field]))
						{
							$value = implode('', $post[$our_field]);
						}
						else
						{
							$value = $post[$our_field];
						}

						$vars['profile'][$profile_field] = $value;
					}
				}
			}
		}

		return $vars;
	}

	public function onRsformFormSave($form)
	{
		$data 			 = JFactory::getApplication()->input->get('reg', array(), 'array');
		$data['form_id'] = $form->FormId;

		switch ($data['activation'])
		{
			case RSFP_REG_NONE:
				$data['defer_admin_email'] = 0;
				break;

			case RSFP_REG_ADMIN:
				$data['defer_admin_email'] = 1;
				break;
		}

		if ($row = $this->getTable())
		{
			if (!$row->save($data))
			{
				JFactory::getApplication()->enqueueMessage($row->getError(), 'error');
			}
		}
	}
	
	public function onRsformBackendFormCopy($args)
	{
		$formId 	= $args['formId'];
		$newFormId 	= $args['newFormId'];

		if ($row = $this->getTable())
		{
			if ($row->load($formId))
			{
				if (!$row->bind(array('form_id' => $newFormId)))
				{
					return false;
				}

				$row->check();
				
				$row->store();
			}
		}
	}

	public function onRsformBackendAfterShowFormEditTabs() {
		$formId = JFactory::getApplication()->input->getInt('formId');
		$row 	= $this->getTable();

		if (!$row) {
			return false;
		}

		$row->load($formId);

		// Fields
		$fields_array = $this->_getFields($formId);
		$fields = array();
		foreach ($fields_array as $field) {
			$fields[] = JHtml::_('select.option', $field, $field);
		}

		// add the json elements
		$joomla_fields = array();
		foreach ($this->getJsonElements() as $f_var)
		{
			$joomla_fields[$f_var] = $this->getFieldLabel($f_var);
		}

		$profile_fields = $this->getProfileFields();

		// Merge Vars
		$merge_vars = array(
			'name' 		=> JText::_('RSFP_REG_NAME'),
			'username' 	=> JText::_('RSFP_REG_USERNAME'),
			'email1' 	=> JText::_('RSFP_REG_EMAIL'),
			'email2' 	=> JText::_('RSFP_REG_EMAIL2'),
			'password1' => JText::_('RSFP_REG_PASSWORD1'),
			'password2' => JText::_('RSFP_REG_PASSWORD2')
		);

		if ($this->hasConsentPlugin())
		{
			$merge_vars['consent'] = JText::_('RSFP_REG_CONSENT');
		}

		// get the radio element
		$itemid = JFormHelper::loadFieldType('menuitem');

		// prepare the xml
		$element = new SimpleXMLElement('<field name="reg[itemid]" type="menuitem" default="0" label="RSFP_REG_ITEMID" description="RSFP_REG_ITEMID_DESC"><option value="0">RSFP_REG_SELECT_ITEMID</option></field>');

		// run
		$itemid->setup($element, $row->itemid);

		$editor = RSFormProHelper::getEditor();
		?>
		<div id="joomlaregistrationdiv">
			<div class="alert alert-info"><?php echo JText::_('PLG_SYSTEM_RSFPREGISTRATION_DESC'); ?></div>
			<fieldset>
				<legend><?php echo JText::_('RSFP_REG_TITLE'); ?></legend>
				<table class="table table-bordered">
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_REG_USE_INTEGRATION'); ?></td>
						<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'reg[published]', '', $row->published); ?></td>
					</tr>
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_SHOW_PASSWORD_STREGTH_DESC'); ?>"><?php echo JText::_('RSFP_REG_SHOW_PASSWORD_STREGTH'); ?></span></td>
						<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'reg[password_strength]', '', $row->password_strength); ?></td>
					</tr>
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ACTION_DESC'); ?>"><?php echo JText::_('RSFP_REG_ACTION'); ?></span></td>
						<td><?php echo JHtml::_('select.genericlist', array(1 => JText::_('RSFP_REG_ALWAYS_REGISTER'), 2 => JText::_('RSFP_REG_BASED_ON_SELECTION')), 'reg[action]', 'onchange="regChangeAction(this.value);"', 'value', 'text', $row->action); echo JHtml::_('select.genericlist', $fields, 'reg[action_field]', $row->action != 2 ? 'disabled="disabled"' : '', 'value', 'text', $row->action_field); ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo JText::_('RSFP_REG_ACTION_WARNING'); ?></td>
					</tr>
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ACTIVATION_DESC'); ?>"><?php echo JText::_('RSFP_REG_ACTIVATION'); ?></span></td>
						<td><?php echo JHtml::_('select.genericlist', array(JText::_('RSFP_REG_NONE'), JText::_('RSFP_REG_SELF'), JText::_('RSFP_REG_ADMIN')), 'reg[activation]', 'onchange="regChangeActivation(this.value);"', 'value', 'text', $row->activation); ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo JText::_('RSFP_REG_ACTIVATION_WARNING'); ?></td>
					</tr>
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_DEFER_ADMIN_EMAIL_DESC'); ?>"><?php echo JText::_('RSFP_REG_DEFER_ADMIN_EMAIL'); ?></span></td>
						<td><?php echo JHtml::_('select.genericlist', array(JText::_('JNO'), JText::_('JYES')), 'reg[defer_admin_email]', $row->activation != RSFP_REG_SELF ? 'disabled="disabled"' : '', 'value', 'text', $row->defer_admin_email); ?></td>
					</tr>
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_GROUPS_DESC'); ?>"><?php echo JText::_('RSFP_REG_GROUPS'); ?></span></td>
						<td><?php echo JHtml::_('select.genericlist', JHtml::_('user.groups'), 'reg[groups][]', 'multiple="multiple"', 'value', 'text', $row->groups); ?></td>
					</tr>
					<?php if (file_exists(JPATH_ADMINISTRATOR.'/components/com_comprofiler/admin.comprofiler.php')) { ?>
						<tr>
							<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ACTIVATION_CB_DESC'); ?>"><?php echo JText::_('RSFP_REG_ACTIVATION_CB'); ?></span></td>
							<td><?php echo RSFormProHelper::renderHTML('select.booleanlist', 'reg[cbactivation]', '', $row->cbactivation); ?></td>
						</tr>
					<?php } ?>
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ITEMID_DESC'); ?>"><?php echo JText::_('RSFP_REG_ITEMID'); ?></span></td>
						<td><?php echo $itemid->input; ?></td>
					</tr>
				</table>
			</fieldset>

			<fieldset id="user_activation_action_container" <?php echo !$row->activation ? 'style="display: none;"' : ''; ?>>
				<legend><?php echo JText::_('RSFP_REG_USER_ACTIVATION'); ?></legend>
				<table class="table table-bordered">
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_USER_ACTIVATION_ACTION_DESC'); ?>"><?php echo JText::_('RSFP_REG_USER_ACTIVATION_ACTION'); ?></span></td>
						<td><?php echo JHtml::_('select.genericlist', array(JText::_('RSFP_REG_REDIRECT_DEFAULT'), JText::_('RSFP_REG_REDIRECT_TO_URL'), JText::_('RSFP_REG_SHOW_MESSAGE')), 'reg[user_activation_action]', 'onchange="regChangeUserAction(this.value);"', 'value', 'text', $row->user_activation_action); ?></td>
					</tr>
					<tr id="reg_user_activation_url_container" <?php echo $row->user_activation_action != 1 ? 'style="display: none;"' : ''; ?>>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_USER_ACTIVATION_URL_DESC'); ?>"><?php echo JText::_('RSFP_REG_USER_ACTIVATION_URL'); ?></span></td>
						<td><input type="text" name="reg[user_activation_url]" value="<?php echo $this->escape($row->user_activation_url); ?>" /></td>
					</tr>
					<tr id="reg_user_activation_text_container" <?php echo $row->user_activation_action != 2 ? 'style="display: none;"' : ''; ?>>
						<td width="80" align="right" valign="top" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_USER_ACTIVATION_TEXT_DESC'); ?>"><?php echo JText::_('RSFP_REG_USER_ACTIVATION_TEXT'); ?></span></td>
						<td><?php echo $editor->display('reg[user_activation_text]', $this->escape($row->user_activation_text), 500, 320, 70, 10); ?></td>
					</tr>
				</table>
			</fieldset>

			<fieldset id="admin_activation_action_container" <?php echo $row->activation != RSFP_REG_ADMIN ? 'style="display: none;"' : ''; ?>>
				<legend><?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION'); ?></legend>
				<table class="table table-bordered">
					<tr>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION_ACTION_DESC'); ?>"><?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION_ACTION'); ?></span></td>
						<td><?php echo JHtml::_('select.genericlist', array(JText::_('RSFP_REG_REDIRECT_DEFAULT'), JText::_('RSFP_REG_REDIRECT_TO_URL'), JText::_('RSFP_REG_SHOW_MESSAGE')), 'reg[admin_activation_action]', 'onchange="regChangeAdminAction(this.value);"', 'value', 'text', $row->admin_activation_action); ?></td>
					</tr>
					<tr id="reg_admin_activation_url_container" <?php echo $row->admin_activation_action != 1 ? 'style="display: none;"' : ''; ?>>
						<td width="80" align="right" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION_URL_DESC'); ?>"><?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION_URL'); ?></span></td>
						<td><input type="text" name="reg[admin_activation_url]" value="<?php echo $this->escape($row->admin_activation_url); ?>" /></td>
					</tr>
					<tr id="reg_admin_activation_text_container" <?php echo $row->admin_activation_action != 2 ? 'style="display: none;"' : ''; ?>>
						<td width="80" align="right" valign="top" nowrap="nowrap" class="key"><span class="hasTip" title="<?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION_TEXT_DESC'); ?>"><?php echo JText::_('RSFP_REG_ADMIN_ACTIVATION_TEXT'); ?></span></td>
						<td><?php echo $editor->display('reg[admin_activation_text]', $this->escape($row->admin_activation_text), 500, 320, 70, 10); ?></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('RSFP_REG_MERGE_VARS'); ?></legend>
				<p><?php echo JText::_('RSFP_REG_MERGE_VARS_DESC'); ?></p>
				<table class="table table-bordered">
					<?php foreach ($merge_vars as $merge_var => $title) { ?>
						<tr>
							<td nowrap="nowrap" align="right"><?php echo $title; ?></td>
							<td><?php echo JHtml::_('select.genericlist', $fields, 'reg[vars]['.$merge_var.']', null, 'value', 'text', isset($row->vars[$merge_var]) ? $row->vars[$merge_var] : null); ?></td>
						</tr>
					<?php } ?>
				</table>
			</fieldset>

			<?php
			// Add the - IGNORE - value
			array_unshift($fields, JHtml::_('select.option', '', JText::_('PLG_SYSTEM_RSFPREGISTRATION_IGNORE')));
			?>
			<fieldset>
				<legend><?php echo JText::_('PLG_SYSTEM_RSFPREGISTRATION_JOOMLA_FIELDS'); ?></legend>
				<table class="table table-bordered">
					<?php
					if ($joomla_fields)
					{
						foreach ($joomla_fields as $merge_var => $title)
						{
							?>
							<tr>
								<td nowrap="nowrap" align="right"><?php echo $title; ?></td>
								<td><?php echo JHtml::_('select.genericlist', $fields, 'reg[joomla_fields]['.$merge_var.']', null, 'value', 'text', isset($row->joomla_fields[$merge_var]) ? $row->joomla_fields[$merge_var] : null); ?></td>
							</tr>
							<?php
						}

					}
					?>
				</table>
			</fieldset>

			<?php
			if ($this->hasProfilePlugin())
			{
				?>
				<fieldset>
					<legend><?php echo JText::_('PLG_SYSTEM_RSFPREGISTRATION_PROFILE_FIELDS'); ?></legend>
					<table class="table table-bordered">
						<?php
						if ($profile_fields)
						{
							foreach ($profile_fields as $merge_var)
							{
								?>
								<tr>
									<td nowrap="nowrap" align="right"><?php echo JText::_('PLG_SYSTEM_RSFPREGISTRATION_' . $merge_var); ?></td>
									<td><?php echo JHtml::_('select.genericlist', $fields, 'reg[profile_fields]['.$merge_var.']', null, 'value', 'text', isset($row->profile_fields[$merge_var]) ? $row->profile_fields[$merge_var] : null); ?></td>
								</tr>
								<?php
							}

						}
						?>
					</table>
				</fieldset>
				<?php
			}
			?>
			<script type="text/javascript">
				function regChangeAction(value) {
					document.getElementsByName('reg[action_field]')[0].disabled = value !== '2';
				}

				function regChangeUserAction(value) {
					// For convenience, grab the containers
					var url  = document.getElementById('reg_user_activation_url_container');
					var text = document.getElementById('reg_user_activation_text_container');

					// Hide them
					url.style.display 	= 'none';
					text.style.display 	= 'none';

					// Force the value to an integer
					value = parseInt(value);

					if (value === 1) {
						url.style.display = '';
					} else if (value === 2) {
						text.style.display = '';
					}
				}

				function regChangeAdminAction(value) {
					// For convenience, grab the containers
					var url  = document.getElementById('reg_admin_activation_url_container');
					var text = document.getElementById('reg_admin_activation_text_container');

					// Hide them
					url.style.display 	= 'none';
					text.style.display 	= 'none';

					// Force the value to an integer
					value = parseInt(value);

					if (value === 1) {
						url.style.display = '';
					} else if (value === 2) {
						text.style.display = '';
					}
				}

				function regChangeActivation(value) {
					var defer 	= document.getElementsByName('reg[defer_admin_email]')[0];
					var user 	= document.getElementById('user_activation_action_container');
					var admin 	= document.getElementById('admin_activation_action_container');
					switch (parseInt(value)) {
						case <?php echo RSFP_REG_NONE; ?>:
							defer.disabled 		= true;
							defer.value			= 0;
							user.style.display  = 'none';
							admin.style.display = 'none';
							break;

						case <?php echo RSFP_REG_SELF; ?>:
							defer.disabled 		= false;
							user.style.display 	= '';
							admin.style.display = 'none';
							break;

						case <?php echo RSFP_REG_ADMIN; ?>:
							defer.disabled 		= true;
							defer.value			= 1;
							user.style.display 	= '';
							admin.style.display = '';
							break;
					}
				}
			</script>
		</div>
		<?php
	}

	public function onRsformBackendAfterShowFormEditTabsTab() {
		?>
		<li><a href="javascript: void(0);" id="joomlaregistration"><span class="rsficon rsficon-joomla"></span><span class="inner-text"><?php echo JText::_('RSFP_REG_TITLE'); ?></span></a></li>
		<?php
	}

	protected function getPasswordFields($formId)
	{
		static $passwords = array();
		if (!isset($passwords[$formId]))
		{
			$passwords[$formId] = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PASSWORD);
		}

		return $passwords[$formId];
	}

	public function onRsformBackendAfterCreateFrontComponentBody($args)
	{
		$formId 	 = $args['formId'];
		$componentId = $args['componentId'];
		$passwords 	 = $this->getPasswordFields($formId);

		if ($row = $this->_getRow($formId))
		{
			if ($row->password_strength && !empty($passwords))
			{
				if (in_array($componentId, $passwords))
				{
					$data = $args['data'];

					if ($data['NAME'] == $row->vars['password1'])
					{
						require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/password.php';
						$passwordClass = RSFormProPassword::getInstance($formId);
						$passwordClass->setPasswordField($data['NAME']);
					}
				}
			}
		}
	}

	public function onRsformFrontendBeforeFormDisplay($args)
	{
		$formId 	= $args['formId'];
		$layoutName = $args['formLayoutName'];
		$passwords  = $this->getPasswordFields($formId);

		if ($row = $this->_getRow($formId))
		{
			if ($row->password_strength && !empty($passwords))
			{
				require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/password.php';
				$passwordClass = RSFormProPassword::getInstance($formId);
				$passwordClass->setLayout($layoutName);
				RSFormProAssets::addScriptDeclaration($passwordClass->printInlineScript());
			}
		}
	}

	// Validate our fields (change the validation message to point to the exact error as well)
	public function onRsformFrontendBeforeFormValidation($args)
	{
		$formId 	= $args['formId'];
		$post 		= &$args['post'];
		$invalid 	= &$args['invalid'];
		$post		= isset($post['form']) && is_array($post['form']) ? $post['form'] : $post;

		if ($row = $this->_getRow($formId))
		{
			// Check if the 'Based on field selection' value matches
			if ($row->action == 2)
			{
				if (!$this->userChoseRegistrationFromPost($row->action_field, $post))
				{
					return;
				}
			}

			$app	= JFactory::getApplication();
			$fields = &$row->vars;
			$vars	= $this->_prepareData($post, $row);

			JFactory::getLanguage()->load('com_users', JPATH_SITE);

			// Name validation
			if ($vars['name'] == '') {
				$invalid[] = RSFormProHelper::componentNameExists($fields['name'], $formId);
			}

			// Username validation
			try {
				$componentId = RSFormProHelper::componentNameExists($fields['username'], $formId);

				// Check for proper formatting
				if (preg_match('#[<>"\'%;()&\\\\]|\\.\\./#', $vars['username']) || strlen(utf8_decode($vars['username'])) < 2 || trim($vars['username']) != $vars['username']) {
					throw new Exception(JText::_('RSFP_REG_ERROR_VALID_AZ09'));
				}

				// Check for existing username
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select($db->qn('id'))
					->from($db->qn('#__users'))
					->where($db->qn('username') . ' = ' . $db->q($vars['username']));
				if ($db->setQuery($query)->loadResult()) {
					throw new Exception(JText::_('RSFP_REG_ERROR_USERNAME_INUSE'));
				}
			} catch (Exception $e) {
				$invalid[] = $componentId;

				// Override the validation message with our own specific error
				$properties = &RSFormProHelper::getComponentProperties($componentId);
				$properties['VALIDATIONMESSAGE'] = $e->getMessage();
			}

			// Email validation		
			try {
				$componentId = RSFormProHelper::componentNameExists($fields['email'], $formId);

				// Check for proper email address formatting
				if (!JMailHelper::isEmailAddress($vars['email'])) {
					throw new Exception(JText::_('RSFP_REG_ERROR_VALID_MAIL'));
				}

				// Check for existing email
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select($db->qn('id'))
					->from($db->qn('#__users'))
					->where($db->qn('email') . ' = ' . $db->q($vars['email']));
				if ($db->setQuery($query)->loadResult()) {
					throw new Exception(JText::_('RSFP_REG_ERROR_EMAIL_INUSE'));
				}

				if ($vars['email1'] != $vars['email2']) {
					$componentId = RSFormProHelper::componentNameExists($fields['email2'], $formId);
					throw new Exception(JText::_('RSFP_REG_EMAIL_DO_NOT_MATCH'));
				}
			} catch (Exception $e) {
				$invalid[] = $componentId;

				$properties = &RSFormProHelper::getComponentProperties($componentId);
				$properties['VALIDATIONMESSAGE'] = $e->getMessage();
			}

			try {
				$componentId = RSFormProHelper::componentNameExists($fields['password'], $formId);

				// Password validation
				if ($vars['password'] == '') {
					throw new Exception(JText::_('RSFP_REG_EMPTY_PASSWORD'));
				}
				
				$rule 	= JFormHelper::loadRuleType('password');
				$field 	= new SimpleXMLElement('<field></field>');
				if (!$rule->test($field, $vars['password'])) {
					// Try to grab the error message from the queue, no easier way to do it unfortunately.
					$messages 	= $app->getMessageQueue();
					$message 	= array_pop($messages);

					throw new Exception($message['message']);
				}

				if ($vars['password1'] != $vars['password2']) {
					$componentId = RSFormProHelper::componentNameExists($fields['password2'], $formId);

					throw new Exception(JText::_('RSFP_REG_ERROR_PASSWORD_NOT_MATCH'));
				}
			} catch (Exception $e) {
				$invalid[] = $componentId;

				$properties = &RSFormProHelper::getComponentProperties($componentId);
				$properties['VALIDATIONMESSAGE'] = $e->getMessage();
			}
		}
	}

	// Before storing the submission values, create the actual user so we can update the submission's user ID
	public function onRsformFrontendBeforeStoreSubmissions($args)
	{
		$formId 		= $args['formId'];
		$post 			= &$args['post'];
		$SubmissionId 	= &$args['SubmissionId'];

		if ($row = $this->_getRow($formId))
		{
			// Check if the 'Based on field selection' value matches
			if ($row->action == 2)
			{
				if (!$this->userChoseRegistrationFromPost($row->action_field, $post))
				{
					return;
				}
			}

			$vars = $this->_prepareData($post, $row);

			if ($userId = $this->_register($vars, $row))
			{
				$user	= JFactory::getUser($userId);
				$db 	= JFactory::getDbo();
				$query 	= $db->getQuery(true)
					->update($db->qn('#__rsform_submissions'))
					->set($db->qn('UserId').'='.$db->q($user->id))
					->set($db->qn('Username').'='.$db->q($user->username))
					->where($db->qn('SubmissionId').'='.$db->q($SubmissionId));

				$db->setQuery($query)->execute();

				$joomla_fields_found = array();
				$json_elements = $this->getJsonElements();
				if ($joomla_fields = $row->joomla_fields)
				{
					$query->clear()
						->select($db->qn('p.PropertyValue'))
						->from($db->qn('#__rsform_properties', 'p'))
						->leftJoin($db->qn('#__rsform_components', 'c').' ON ('.$db->qn('p.ComponentId').' = '.$db->qn('c.ComponentId').')')
						->where($db->qn('p.PropertyName').' = '.$db->q('NAME'))
						->where($db->qn('c.FormId').' = '.$db->q($formId))
						->where($db->qn('c.ComponentTypeId').' = '.$db->q(RSFORM_FIELD_FILEUPLOAD));

					if ($fileUploadNames = $db->setQuery($query)->loadColumn())
					{
						$query->clear()
							->select('*')
							->from($db->qn('#__rsform_submission_values'))
							->where($db->qn('SubmissionId') . ' = ' . $db->q($SubmissionId))
							->where($db->qn('FieldName') . ' IN (' . implode(',', $db->q($fileUploadNames)) . ')');

						$fileUploads = $db->setQuery($query)->loadObjectList('FieldName');
					}
					else
					{
						$fileUploads = array();
					}

					foreach ($joomla_fields as $k => $v)
					{
						if ($v === '' || !in_array($k, $json_elements))
						{
							continue;
						}

						if (isset($fileUploads[$v]))
						{
							$value = $fileUploads[$v]->FieldValue;

							// Let's handle multiple values
							if ($value && strpos($value, "\n") !== false)
							{
								$value = explode("\n", $value);
								$value = reset($value);
							}

							if ($value)
							{
								$joomla_fields_found[$k] = str_replace(array(JPATH_SITE.DIRECTORY_SEPARATOR, JUri::base()), '', $fileUploads[$v]->FieldValue);
							}
						}
						elseif (!empty($post[$v]))
						{
							$joomla_fields_found[$k] = $post[$v];
						}
					}
				}

				if ($joomla_fields_found)
				{
					// Emulate
					$data = array('com_fields' => $joomla_fields_found);

					// Load the model
					JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');
					$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));

					// Load the helper
					JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
					$fields = FieldsHelper::getFields('com_users.user');

					// Loop over the fields
					foreach ($fields as $field)
					{
						// Determine the value if it is (un)available from the data
						if (key_exists($field->name, $data['com_fields']))
						{
							$value = $data['com_fields'][$field->name] === false ? null : $data['com_fields'][$field->name];
						}
						// Field not available on form, use stored value
						else
						{
							$value = isset($field->rawvalue) ? $field->rawvalue : null;
						}

						// If no value set (empty) remove value from database
						if (is_array($value) ? !count($value) : !strlen($value))
						{
							$value = null;
						}

						// JSON encode value for complex fields
						if (is_array($value) && (count($value, COUNT_NORMAL) !== count($value, COUNT_RECURSIVE) || !count(array_filter(array_keys($value), 'is_numeric'))))
						{
							$value = json_encode($value);
						}

						// Setting the value for the field and the item
						$model->setFieldValue($field->id, $userId, $value);
					}
				}
			}
		}
	}

	public function onRsformAfterCreatePlaceholders($args)
	{
		$placeholders 	= &$args['placeholders'];
		$values			= &$args['values'];
		$submission		= &$args['submission'];
		$formId			= $args['form']->FormId;

		if ($row = $this->_getRow($formId))
		{
			$app = JFactory::getApplication();
			// Get the base link
			$base = $app->isClient('administrator') ? JUri::root() : JUri::getInstance()->toString(array('scheme','host', 'port'));

			// Reset for multiple resends
			$this->sendUserEmail = null;
			$this->sendAdminEmail = null;

			if ($row->action == 2)
			{
				if (!$this->userChoseRegistration($row->action_field, $placeholders, $values))
				{
					return;
				}
			}

			if (!$submission->UserId)
			{
				return;
			}

			$user = JFactory::getUser($submission->UserId);

			switch ($row->activation)
			{
				// No activation
				case RSFP_REG_NONE:
					$this->sendUserEmail = true;
					$this->sendAdminEmail = true;
					break;

				// User activation
				case RSFP_REG_SELF:
				    if ($user->get('activation'))
                    {
	                    $this->sendUserEmail = true;
	                    $placeholders[] = '{user_activation}';
	                    $values[]		= $base.self::toSef('index.php?option=com_rsform&task=plugin&formId='.$formId.'&submissionId='.$submission->SubmissionId.'&action=user.activate&token='.$user->activation, false, $row->itemid);
                    }
                    else
                    {
                        // User already activated, admin email should probably be sent
                        $this->sendAdminEmail = true;
                    }
					break;

				// Admin activation
				case RSFP_REG_ADMIN:
					if ($user->getParam('activate', 0))
					{
						$this->sendAdminEmail = true;
						$this->sendUserEmail = false;

						$placeholders[] = '{admin_activation}';
						$values[]		= $base.self::toSef('index.php?option=com_rsform&task=plugin&formId='.$formId.'&submissionId='.$submission->SubmissionId.'&action=user.activate&token='.$user->activation, false, $row->itemid);
					}
					else
					{
						$this->sendUserEmail = true;
						$this->sendAdminEmail = false;

						$placeholders[] = '{user_activation}';
						$values[]		= $base.self::toSef('index.php?option=com_rsform&task=plugin&formId='.$formId.'&submissionId='.$submission->SubmissionId.'&action=user.activate&token='.$user->activation, false, $row->itemid);
					}

					break;
			}
		}
	}

	private function userChoseRegistration($field, $placeholders, $values)
	{
		$post = array();
		if (($pos = array_search('{'.$field.':value}', $placeholders)) !== false)
		{
			$post[$field] = $values[$pos];
		}

		return $this->userChoseRegistrationFromPost($field, $post);
	}

	private function userChoseRegistrationFromPost($field, $post)
	{
		if (!isset($post[$field]))
		{
			return false;
		}

		$values = (array) $post[$field];
		if (!in_array('1', $values) && !in_array('register', $values))
		{
			return false;
		}

		return true;
	}

	// We're done, remove the data from the submission
	public function onRsformFrontendAfterFormProcess($args) {
		$SubmissionId 	= $args['SubmissionId'];
		$formId 		= $args['formId'];

		if ($row = $this->_getRow($formId)) {
			$db     = JFactory::getDbo();
			$fields = &$row->vars;

			$passwords = array();
			if (isset($fields['password'])) {
				$passwords[] = $db->q($fields['password']);
			}
			if (isset($fields['password1'])) {
				$passwords[] = $db->q($fields['password1']);
			}
			if (isset($fields['password2'])) {
				$passwords[] = $db->q($fields['password2']);
			}

			if ($passwords) {
				$query = $db->getQuery(true);
				$query->update($db->qn('#__rsform_submission_values'))
					->set($db->qn('FieldValue').'='.$db->q(''))
					->where($db->qn('FieldName').' IN ('.implode(',', $passwords).')')
					->where($db->qn('SubmissionId').'='.$db->q($SubmissionId))
					->where($db->qn('FormId').'='.$db->q($formId));
				$db->setQuery($query)->execute();
			}
		}
	}


	public function onRsformBeforeUserEmail($args)
	{
		$form =& $args['form'];

		// User is not created right now - don't send the User Email in this case.
		if (($row = $this->_getRow($form->FormId)))
		{
			// Check if the 'Based on field selection' value matches
			if ($row->action == 2)
			{
				if (!$this->userChoseRegistration($row->action_field, $args['placeholders'], $args['values']))
				{
					return;
				}
			}

			if ($this->sendUserEmail)
			{
				return;
			}

			$args['userEmail']['to'] = '';
		}
	}

	public function onRsformBeforeAdminEmail($args)
	{
		$form =& $args['form'];

		// User is created right now but Admin Email is being deferred.
		if (($row = $this->_getRow($form->FormId)) && $row->defer_admin_email)
		{
			// Check if the 'Based on field selection' value matches
			if ($row->action == 2)
			{
				if (!$this->userChoseRegistration($row->action_field, $args['placeholders'], $args['values']))
				{
					return;
				}
			}

			if ($this->sendAdminEmail)
			{
				return;
			}

			$args['adminEmail']['to'] = '';
		}
	}

	protected function _register($vars, $row) {
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();

		$useractivation = $row->activation;
		$cbactivation	= $row->cbactivation;

		$data 			= (object) $vars;
		$data->groups 	= $row->groups;

		// Get the dispatcher and load the users plugins.
		JPluginHelper::importPlugin('user');

		// Trigger the data preparation event.
		$results = $app->triggerEvent('onContentPrepareData', array('com_users.registration', $data));

		// Check for errors encountered while preparing the data.
		if (count($results) && in_array(false, $results, true)) {
			return false;
		}

		$data = (array) $data;

		$user = new JUser;

		// Check if the user needs to activate their account.
		if (($useractivation == RSFP_REG_SELF) || ($useractivation == RSFP_REG_ADMIN)) {
			$data['activation'] = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
			$data['block'] = 1;
		}

		// Bind the data.
		if (!$user->bind($data)) {
			$app->enqueueMessage(JText::sprintf('RSFP_REG_REGISTRATION_BIND_FAILED', $user->getError()), 'warning');
			return false;
		}

		// Store the data.
		if (!$user->save()) {
			$app->enqueueMessage(JText::sprintf('RSFP_REG_REGISTRATION_SAVE_FAILED', $user->getError()), 'warning');
			return false;
		}

		if ($this->hasConsentPlugin())
		{
			if (!empty($vars['consent']))
			{
				// Get the user's IP address
				$ip = $app->input->server->get('REMOTE_ADDR', '', 'string');

				// Get the user agent string
				$userAgent = $app->input->server->get('HTTP_USER_AGENT', '', 'string');

				$userNote = (object) array(
					'user_id' => $user->id,
					'subject' => 'PLG_SYSTEM_PRIVACYCONSENT_SUBJECT',
					'body'    => JText::sprintf('PLG_SYSTEM_PRIVACYCONSENT_BODY', $ip, $userAgent),
					'created' => JFactory::getDate()->toSql(),
				);

				try
				{
					$db->insertObject('#__privacy_consents', $userNote);
				}
				catch (Exception $e)
				{
					// Do nothing if the save fails
				}
			}
		}

		// Activate the Community Builder user so he can login
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_comprofiler/admin.comprofiler.php') && $cbactivation) {
			$query = $db->getQuery(true)
				->insert($db->qn('#__comprofiler'))
				->set(array(
					$db->qn('id').'='.$db->q($user->id),
					$db->qn('user_id').'='.$db->q($user->id),
					$db->qn('approved').'='.$db->q(1),
					$db->qn('confirmed').'='.$db->q(1)));
			$db->setQuery($query)->execute();
		}

		return $user->id;
	}

	protected function _getFields($formId) {
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('LEFT', $db->qn('#__rsform_properties', 'p').' ON ('.$db->qn('c.ComponentId').'='.$db->qn('p.ComponentId').')')
			->where($db->qn('c.FormId').'='.$db->q($formId))
			->where($db->qn('p.PropertyName').'='.$db->q('NAME'))
			->order($db->qn('c.Order').' '.$db->escape('ASC'));
		return $db->setQuery($query)->loadColumn();
	}

	protected function _escapeCommas(&$item) {
		$item = str_replace(',', '\,', $item);
	}

	public function onRsformBackendAfterShowConfigurationTabs($tabs) {
		$tabs->addTitle(JText::_('RSFP_REG_FORM_NAME_LABEL'), 'form-register');
		$tabs->addContent($this->configurationScreen());
	}

	protected function configurationScreen() {
		ob_start();

		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true)
			->select($db->qn('f.FormId', 'value'))
			->select($db->qn('f.FormName', 'text'))
			->from($db->qn('#__rsform_forms', 'f'))
			->join('left', $db->qn('#__rsform_registration', 'r').' ON ('.$db->qn('f.FormId').'='.$db->qn('r.form_id').')')
			->where($db->qn('r.published').'='.$db->q(1))
			->order($db->qn('f.FormName').' '.$db->escape('ASC'));
		$forms = $db->setQuery($query)->loadObjectList();

		array_unshift($forms, JHtml::_('select.option', 0, JText::_('RSFP_REG_DEFAULT_JOOMLA_FORM')));
		?>
		<div id="page-register">
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for=""><span class="hasTip" title="<?php echo JText::_('RSFP_REG_FORM_NAME_DESC'); ?>"><?php echo JText::_( 'RSFP_REG_FORM_NAME_LABEL' ); ?></span></label></td>
					<td>
						<?php echo JHtml::_('select.genericlist', $forms, 'rsformConfig[registration_form]', null, 'value', 'text', RSFormProHelper::getConfig('registration_form')); ?>
					</td>
				</tr>
				<tr>
					<td align="right"><strong><?php echo JText::_('RSFP_REG_OR'); ?></strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="redirect_url"><span class="hasTip" title="<?php echo JText::_('RSFP_REDIRECT_URL_DESC'); ?>"><?php echo JText::_( 'RSFP_REDIRECT_URL_LABEL' ); ?></span></label></td>
					<td>
						<input type="text" name="rsformConfig[redirect_url]" id="redirect_url" value="<?php echo $this->escape(RSFormProHelper::getConfig('redirect_url')); ?>" size="150" maxlength="150">
					</td>
				</tr>
			</table>
		</div>
		<?php

		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	public function onRsformFrontendSwitchTasks()
	{
		$app = JFactory::getApplication();

		$formId		  = $app->input->getInt('formId');
		$token		  = $app->input->getAlnum('token');
		$action		  = $app->input->getCmd('action');
		$submissionId = $app->input->getInt('submissionId');

		if ($action != 'user.activate')
		{
			return;
		}

		if ($row = $this->_getRow($formId))
		{
			$db				= JFactory::getDbo();
			$query			= $db->getQuery(true);
			$useractivation = $row->activation;

			// If user registration or account activation is disabled, throw a 403.
			if ($useractivation == RSFP_REG_NONE)
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
			}

			// Check that the token is in a valid format.
			if ($token === null || strlen($token) !== 32)
			{
				throw new Exception(JText::_('JINVALID_TOKEN'), 403);
			}

			// Check if there's a submissionId in the request.
			if (!$submissionId)
			{
				throw new Exception(JText::_('RSFP_REG_MISSING_ACTIVATION_PARAMETERS'), 403);
			}

			// Check if the submission ID exists
			$query->select('*')
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('SubmissionId').'='.$db->q($submissionId))
				->where($db->qn('FormId').'='.$db->q($formId));
			$submission = $db->setQuery($query)->loadObject();

			if (!$submission)
			{
				throw new Exception(JText::_('RSFP_REG_MISSING_SUBMISSION'), 403);
			}

			$query->clear()
				->select($db->qn('id'))
				->from($db->qn('#__users'))
				->where($db->qn('activation').' = '.$db->q($token))
				->where($db->qn('block').' = 1')
				->where('(' . $db->qn('lastvisitDate') . ' IS NULL OR ' . $db->qn('lastvisitDate').' = '.$db->q($db->getNullDate()) . ')');
			$db->setQuery($query);

			try
			{
				$userId = (int) $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				throw new Exception(JText::sprintf('RSFP_REG_DATABASE_ERROR', $e->getMessage()), 500);
			}

			// Check for a valid user id.
			if (!$userId)
			{
				throw new Exception(JText::_('RSFP_REG_ACTIVATION_TOKEN_NOT_FOUND'), 403);
			}

			if ($submission->UserId != $userId)
			{
				throw new Exception(JText::_('RSFP_REG_WRONG_USER_SUBMISSION'), 403);
			}

			// Load the users plugin group.
			JPluginHelper::importPlugin('user');

			// Activate the user.
			$user = JFactory::getUser($userId);

			// Admin activation is on and user is verifying their email
			if (($useractivation == RSFP_REG_ADMIN) && !$user->getParam('activate', 0)) {
				$user->set('activation', JApplicationHelper::getHash(JUserHelper::genRandomPassword()));
				$user->setParam('activate', 1);

				// Send Admin Email (it's been deferred until now)
				RSFormProHelper::sendSubmissionEmails($submissionId);
			}
			// Admin activation is on and admin is activating the account
			elseif (($useractivation == RSFP_REG_ADMIN) && $user->getParam('activate', 0)) {
				$user->set('activation', '');
				$user->set('block', '0');
				$user->setParam('activate', 0);

				// Compile the user activated notification mail values.
				$data 				= $user->getProperties();
				$config 			= JFactory::getConfig();
				$data['fromname'] 	= $config->get('fromname');
				$data['mailfrom'] 	= $config->get('mailfrom');
				$data['sitename'] 	= $config->get('sitename');
				$data['siteurl'] 	= JUri::base();
				$emailSubject = JText::sprintf(
					'RSFP_REG_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT',
					$data['name'],
					$data['sitename']
				);

				$emailBody = JText::sprintf(
					'RSFP_REG_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
					$data['name'],
					$data['siteurl'],
					$data['username']
				);

				list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);

				$emailSubject 	= str_replace($placeholders, $values, $emailSubject);
				$emailBody 		= str_replace($placeholders, $values, $emailBody);

				// Allow HTML if specified in the language overrides
				$mode = strip_tags($emailBody) !== $emailBody ? 1 : 0;

				RSFormProHelper::sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody, $mode);
			} else {
				$user->set('activation', '');
				$user->set('block', '0');

				// Send Admin Email (it's been deferred until now because the user opted for this)
				if ($row->defer_admin_email) {
					RSFormProHelper::sendSubmissionEmails($submissionId);
				}
			}

			if (!$user->save())
			{
				$app->enqueueMessage(JText::sprintf('RSFP_REG_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()), 'error');
				return false;
			}

			// Self activation
			if ($useractivation == RSFP_REG_SELF) {
				switch ($row->user_activation_action) {
					default:
						$app->enqueueMessage(JText::_('RSFP_REG_REGISTRATION_ACTIVATE_SUCCESS'));
						$app->redirect(self::toSef('index.php?option=com_users&view=login', false, $row->itemid));
						break;

					case 1:
						if (empty($placeholders)) {
							list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);
						}
						$user_activation_url = str_replace($placeholders, $values, $row->user_activation_url);
						
						$app->redirect($user_activation_url);
						break;

					case 2:
						// Load placeholders
						if (empty($placeholders)) {
							list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);
						}

						// Replace them in the message
						$message = str_replace($placeholders, $values, $row->user_activation_text);

						// Set the message in the session
						JFactory::getSession()->set('com_rsform.registration', base64_encode($message));

						$app->redirect(self::toSef('index.php?option=com_rsform&view=registration', false, $row->itemid));
						break;
				}
			} elseif ($user->getParam('activate')) {
				switch ($row->user_activation_action) {
					default:
						$app->enqueueMessage(JText::_('RSFP_REG_REGISTRATION_VERIFY_SUCCESS'));
						$app->redirect(self::toSef('index.php?option=com_users&view=registration&layout=complete', false, $row->itemid));
						break;

					case 1:
						if (empty($placeholders)) {
							list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);
						}
						$user_activation_url = str_replace($placeholders, $values, $row->user_activation_url);
						
						$app->redirect($user_activation_url);
						break;

					case 2:
						// Load placeholders
						if (empty($placeholders)) {
							list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);
						}

						// Replace them in the message
						$message = str_replace($placeholders, $values, $row->user_activation_text);

						// Set the message in the session
						JFactory::getSession()->set('com_rsform.registration', base64_encode($message));

						$app->redirect(self::toSef('index.php?option=com_rsform&view=registration', false, $row->itemid));
						break;
				}
			} else {
				switch ($row->admin_activation_action) {
					default:
						$app->enqueueMessage(JText::_('RSFP_REG_REGISTRATION_ADMINACTIVATE_SUCCESS'));
						$app->redirect(self::toSef('index.php?option=com_users&view=registration&layout=complete', false, $row->itemid));
						break;

					case 1:
						if (empty($placeholders)) {
							list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);
						}
						$admin_activation_url = str_replace($placeholders, $values, $row->admin_activation_url);
						
						$app->redirect($admin_activation_url);
						break;

					case 2:
						// Load placeholders
						if (empty($placeholders)) {
							list($placeholders, $values) = RSFormProHelper::getReplacements($submissionId);
						}

						// Replace them in the message
						$message = str_replace($placeholders, $values, $row->admin_activation_text);

						// Set the message in the session
						JFactory::getSession()->set('com_rsform.registration', base64_encode($message));

						$app->redirect(self::toSef('index.php?option=com_rsform&view=registration', false, $row->itemid));
						break;
				}
			}
		}
	}

	public function onRsformAfterFormBuildRoute(&$segments, &$query)
	{
		if (isset($query['view']) && $query['view'] == 'registration')
		{
			$segments = array('registration-message');

			unset($query['view']);
		}
	}

	public function onRsformAfterFormParseRoute(&$segments, &$query)
	{
		if (isset($segments[0]) && $segments[0] == 'registration-message')
		{
			$query['view'] = 'registration';
		}
	}

	// Redirect frontend users to the assigned registration form or registration URL
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();

		// No point in running in the administrator section
		if ($app->isClient('administrator'))
		{
			return;
		}

		// Look for com_users
		if ($app->input->getCmd('option') != 'com_users')
		{
			return;
		}

		// Are we on the registration view?
		if ($app->input->getCmd('view') != 'registration')
		{
			return;
		}

		// There's also the 'layout=complete' case we have to take into account
		if ($app->input->getCmd('layout', 'default') != 'default')
		{
			return;
		}
		
		// Also the tasks
		if ($app->input->getCmd('task'))
		{
			return;
		}
		
		if (!$this->canRun()) {
			return;
		}
		
		$config = RSFormProConfig::getInstance();

		if ($formId = $config->get('registration_form'))
		{
			$app->redirect(JRoute::_('index.php?option=com_rsform&formId='.$formId, false));
		}
		elseif ($url = $config->get('redirect_url'))
		{
			$app->redirect($url);
		}
	}

	// When a form is deleted, delete the reference as well
	public function onRsformFormDelete($formId)
	{
		if ($row = $this->getTable())
		{
			$row->delete($formId);
		}
	}

	// Inject the registration settings in the form backup
	public function onRsformFormBackup($form, $xml, $fields)
	{
		if ($row = $this->getTable())
		{
			if ($row->load($form->FormId))
			{
				$row->check();

				$data = $row->getProperties();
				unset($data['form_id']);

				$xml->add('registration');
				foreach ($data as $property => $value)
				{
					$xml->add($property, $value);
				}
				$xml->add('/registration');
			}
		}
	}

	// Restore the registration settings from a form backup
	public function onRsformFormRestore($form, $xml, $fields)
	{
		if (isset($xml->registration))
		{
			$data = array(
				'form_id' => $form->FormId
			);

			foreach ($xml->registration->children() as $property => $value)
			{
				$data[$property] = (string) $value;
			}

			if (isset($data['reg_merge_vars']) && !isset($data['vars']))
			{
				$data['vars'] = $data['reg_merge_vars'];
			}

			if (!isset($data['groups']))
			{
				$params	= JComponentHelper::getParams('com_users');
				$data['groups'] = array($params->get('new_usertype', 2));
			}

			if ($row = $this->getTable())
			{
				$row->save($data);
			}
		}
	}

	// Truncate this table when we're restoring clean
	public function onRsformBackendFormRestoreTruncate()
	{
		JFactory::getDbo()->truncateTable('#__rsform_registration');
	}

	protected function toSef($link, $xhtml = true, $itemid = null)
	{
		if ($itemid)
		{
			$link .= '&Itemid='.$itemid;
		}

		if (JFactory::getApplication()->isClient('administrator'))
		{
			return $xhtml ? $this->escape($link) : $link;
		}
		else
		{
			return JRoute::_($link, $xhtml);
		}
	}

	public function onRsformAfterCreateQuickAddGlobalPlaceholders(& $placeholders, $type)
	{
		static $hasRegistration, $newPlaceholders;

		if ($hasRegistration === null)
		{
			$hasRegistration = false;

			if ($row = $this->getTable())
			{
				$formId = JFactory::getApplication()->input->getInt('formId');
				if ($row->load($formId) && $row->published)
				{
					$hasRegistration = true;

					$newPlaceholders = array();

					switch ($row->activation)
					{
						case RSFP_REG_SELF:
							$newPlaceholders[] = '{user_activation}';
							break;

						case RSFP_REG_ADMIN:
							$newPlaceholders[] = '{user_activation}';
							$newPlaceholders[] = '{admin_activation}';
							break;
					}
				}
			}
		}

		if ($hasRegistration && $type == 'display' && $newPlaceholders)
		{
			$placeholders = array_merge($placeholders, $newPlaceholders);
		}
	}

	private function getJsonElements()
	{
		static $done;

		if (!$done)
		{
			$done = true;

			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->qn('name'))
				->from($db->qn('#__fields'))
				->where($db->qn('context') . ' = ' . $db->q('com_users.user'));

			$this->json_elements = $db->setQuery($query)->loadColumn();
		}

		return $this->json_elements;
	}

	private function getFieldLabel($name)
	{
		static $cache;

		if (!is_array($cache))
		{
			$cache = array();

			JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
			if ($fields = FieldsHelper::getFields('com_users.user'))
			{
				foreach ($fields as $field)
				{
					$cache[$field->name] = RSFormProHelper::htmlEscape($field->label) . ' (<small>' . RSFormProHelper::htmlEscape($field->name) . '</small>)';
				}
			}
		}

		return isset($cache[$name]) ? $cache[$name] : $name;
	}

	private function getProfileFields()
	{
		return array(
			'address1',
			'address2',
			'city',
			'region',
			'country',
			'postal_code',
			'phone',
			'website',
			'favoritebook',
			'aboutme',
			'dob',
			'tos'
		);
	}
}