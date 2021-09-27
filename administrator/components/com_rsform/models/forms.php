<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class RsformModelForms extends JModelList
{
	public $_mdata = null;

	/* @var TableRSForm_Forms */
	public $_form = null;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'FormTitle',
				'FormName',
				'Published',
				'FormId',
				'state'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$this->setState('filter.search', 	$this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search'));
		$this->setState('filter.state', 	$this->getUserStateFromRequest($this->context.'.filter.state', 	'filter_state'));

		// List state information.
		parent::populateState('FormId', 'asc');
	}

	protected function getListQuery()
	{
		$filter_search = $this->getState('filter.search');
		$lang		   = JFactory::getLanguage();
		$query		   = $this->_db->getQuery(true);
		$or 	= array();
		$ids 	= array();

		// Flag to know if we need translations - no point in doing a join if we're only using the default language.
		if (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') == 'en-GB')
		{
			$needs_translation = false;
		}
		else
		{
			// Must check if we've changed the language for some forms (each form has its own remembered language).
			if ($sessions = JFactory::getSession()->get('com_rsform.form'))
			{
				// For each form in the session, we join a specific language and form id.
				foreach ($sessions as $form => $data)
				{
					if (strpos($form, 'formId') === 0 && isset($data->lang))
					{
						$id 	= (int) substr($form, strlen('formId'));
						$ids[] 	= $id;
						$or[] 	= '(' . $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q($data->lang) . ' AND ' . $this->_db->qn('t.form_id') . ' = ' . $this->_db->q($id) . ')';
					}
				}

				// Now that we've joined the session forms, we must remove them so they do not show up as duplicates.
				if ($ids)
				{
					$or[] = '(' . $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q($lang->getTag()) . ' AND ' . $this->_db->qn('t.form_id') . ' NOT IN (' . implode(',', $this->_db->q($ids)) . '))';
				}
			}

			$needs_translation = $lang->getTag() != $lang->getDefault() || $ids || (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') != 'en-GB');
		}

		$query->select($this->_db->qn('f.FormId'))
			->select($this->_db->qn('f.FormName'))
			->select($this->_db->qn('f.Backendmenu'))
			->select($this->_db->qn('f.Published'))
			->from($this->_db->qn('#__rsform_forms', 'f'));

		if ($needs_translation)
		{
			$query->select('IFNULL(' . $this->_db->qn('t.value') . ', ' . $this->_db->qn('f.FormTitle') . ') AS FormTitle');
		}
		else
		{
			$query->select($this->_db->qn('f.FormTitle'));
		}

		if ($needs_translation)
		{
			$on = array(
				$this->_db->qn('f.FormId') . ' = ' . $this->_db->qn('t.form_id'),
				$this->_db->qn('t.reference') . ' = ' . $this->_db->q('forms'),
				$this->_db->qn('t.reference_id') . ' = ' . $this->_db->q('FormTitle')
			);

			if ($or && !RSFormProHelper::getConfig('global.disable_multilanguage'))
			{
				$on[] = '(' . implode(' OR ', $or) . ')';
			}
			else
			{
				if (RSFormProHelper::getConfig('global.default_language') == 'en-GB')
				{
					$on[] = $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q($lang->getTag());
				}
				else
				{
					$on[] = $this->_db->qn('t.lang_code') . ' = ' . $this->_db->q(RSFormProHelper::getConfig('global.default_language'));
				}
			}

			$query->join('left', $this->_db->qn('#__rsform_translations', 't') . ' ON (' . implode(' AND ', $on) . ')');
		}

		if (strlen($filter_search))
		{
			if (stripos($filter_search, 'id:') === 0)
			{
				$query->where($this->_db->qn('f.FormId') . ' = ' . (int) substr($filter_search, 3));
			}
			else
			{
				$query->having('(' . $this->_db->qn('FormTitle') . ' LIKE ' . $this->_db->q('%' . $filter_search . '%') . ' OR ' . $this->_db->qn('FormName') . ' LIKE ' . $this->_db->q('%' . $filter_search . '%') . ')');
			}
		}

		$state = $this->getState('filter.state');
		if ($state != '')
		{
			$query->where($this->_db->qn('Published') . '=' . $this->_db->q($state));
		}

		$query->order($this->_db->qn($this->getSortColumn()) . ' ' . $this->_db->escape($this->getSortOrder()));

		return $query;
	}

	public function getForms()
	{
		$items = $this->getItems();

		$formIds = array();
		foreach ($items as $row)
		{
			$formIds[] = $row->FormId;
		}

		if ($formIds)
		{
			$date = JFactory::getDate();

			// Count submissions
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('FormId') . ' IN (' . implode(',', $db->q($formIds)) . ')')
				->group($db->qn('FormId'));
			$allSubmissions = $db->setQuery($query)->loadObjectList('FormId');

			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('FormId') . ' IN (' . implode(',', $db->q($formIds)) . ')')
				->where('DATE_FORMAT(' . $db->qn('DateSubmitted') . ', ' . $db->q('%Y-%m') . ') = ' . $db->q($date->format('Y-m')))
				->group($db->qn('FormId'));
			$monthSubmissions = $db->setQuery($query)->loadObjectList('FormId');

			$query = $db->getQuery(true)
				->select('COUNT(' . $db->qn('SubmissionId') . ') AS ' . $db->qn('total'))
				->select($db->qn('FormId'))
				->from($db->qn('#__rsform_submissions'))
				->where($db->qn('FormId') . ' IN (' . implode(',', $db->q($formIds)) . ')')
				->where('DATE_FORMAT(' . $db->qn('DateSubmitted') . ', ' . $db->q('%Y-%m-%d') . ') = ' . $db->q($date->format('Y-m-d')))
				->group($db->qn('FormId'));
			$todaySubmissions = $db->setQuery($query)->loadObjectList('FormId');

			foreach ($items as $form)
			{
				$form->_todaySubmissions = isset($todaySubmissions[$form->FormId]) ? $todaySubmissions[$form->FormId]->total : 0;
				$form->_monthSubmissions = isset($monthSubmissions[$form->FormId]) ? $monthSubmissions[$form->FormId]->total : 0;
				$form->_allSubmissions = isset($allSubmissions[$form->FormId]) ? $allSubmissions[$form->FormId]->total : 0;
			}
		}

		return $items;
	}

	public function getSortColumn()
	{
		return $this->getState('list.ordering', 'FormId');
	}

	public function getSortOrder()
	{
		return $this->getState('list.direction', 'ASC');
	}

	public function getHasSubmitButton()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$query = $this->_db->getQuery(true)
            ->select($this->_db->qn('ComponentId'))
            ->from($this->_db->qn('#__rsform_components'))
            ->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId))
            ->where($this->_db->qn('ComponentTypeId') . ' = ' . $this->_db->q(RSFORM_FIELD_SUBMITBUTTON));

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	public function getFields()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$return = array();

		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('p.PropertyValue', 'ComponentName'))
			->select('c.*')
			->select($this->_db->qn('ct.ComponentTypeName'))
			->from($this->_db->qn('#__rsform_components', 'c'))
			->leftJoin($this->_db->qn('#__rsform_properties', 'p') . ' ON (' . $this->_db->qn('c.ComponentId') . ' = ' . $this->_db->qn('p.ComponentId') . ' AND ' . $this->_db->qn('p.PropertyName') . ' = ' . $this->_db->q('NAME') . ')')
			->leftJoin($this->_db->qn('#__rsform_component_types', 'ct') . ' ON (' . $this->_db->qn('ct.ComponentTypeId') . ' = ' . $this->_db->qn('c.ComponentTypeId') . ')')
			->where($this->_db->qn('c.FormId') . ' = ' . $this->_db->q($formId))
			->order($this->_db->qn('c.Order') . ' ' . $this->_db->escape('asc'));

		$this->_db->setQuery($query);
		$components = $this->_db->loadObjectList();

		$properties = RSFormProHelper::getComponentProperties($components);

		$pages = array();
		foreach ($components as $component)
		{
			if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK) {
				$pages[] = $component->ComponentId;
			}
		}

		foreach ($components as $component)
		{
			$data = $properties[$component->ComponentId];
			$data['componentId'] = $component->ComponentId;
			$data['componentTypeId'] = $component->ComponentTypeId;
			$data['ComponentTypeName'] = $component->ComponentTypeName;

			// Pagination
			if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
			{
				$data['PAGES'] = $pages;
			}

			$field = new stdClass();
			$field->id = $component->ComponentId;
			$field->type_id = $component->ComponentTypeId;
			$field->type_name = $component->ComponentTypeName;
			$field->name = $component->ComponentName;
			$field->published = $component->Published;
			$field->ordering = $component->Order;
			$field->preview = $this->showPreview($formId, $field->id, $data);

			$field->caption = isset($data['CAPTION']) && strlen($data['CAPTION']) ? $data['CAPTION'] : $field->name;

			if (!empty($data['REQUIRED']))
			{
				$field->hasRequired = true;
				$field->required = $data['REQUIRED'] == 'YES';
			}
			else
			{
				$field->required = false;
			}

			if (isset($data['VALIDATIONRULE']) && $data['VALIDATIONRULE'] != 'none')
			{
				$field->validation = $data['VALIDATIONRULE'];
			}
			elseif (isset($data['VALIDATIONRULE_DATE']) && $data['VALIDATIONRULE_DATE'] != 'none')
			{
				$field->validation = $data['VALIDATIONRULE_DATE'];
			}

			$return[$field->id] = $field;
		}
		
		return $return;
	}

	protected function showPreview($formId, $componentId, $data)
	{
		$mainframe 		= JFactory::getApplication();
		$formId 		= (int) $formId;
		$componentId 	= (int) $componentId;

		// Legacy
		$r = array();
		$r['ComponentTypeName'] = $data['ComponentTypeName'];

		$out = '';

		//Trigger Event - onRsformBackendBeforeCreateComponentPreview
		$mainframe->triggerEvent('onRsformBackendBeforeCreateComponentPreview',array(array('out'=>&$out,'formId'=>$formId,'componentId'=>$componentId,'ComponentTypeName'=>$r['ComponentTypeName'],'data'=>$data)));

		$config    = array(
			'formId' 		=> $formId,
			'componentId' 	=> $componentId,
			'data' 			=> $data,
			'preview' 		=> true,
			'value' 		=> array(),
			'invalid' 		=> false,
			'errorClass' 	=> ''
		);

		$type = $r['ComponentTypeName'];
		$classFile = JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/'.strtolower($type).'.php';
		if (file_exists($classFile)) {
			$class = 'RSFormProField'.$type;

			if (!class_exists($class)) {
				require_once $classFile;
			}

			// Create the field
			$field = new $class($config, true);

			// Return the output
			$out .= $field->output;
		}

		if (empty($out)) {
			$out = '<td colspan="2" style="color:#333333"><em>'.JText::_('RSFP_COMP_PREVIEW_NOT_AVAILABLE').'</em></td>';
		}

		//Trigger Event - onRsformBackendAfterCreateComponentPreview
		$mainframe->triggerEvent('onRsformBackendAfterCreateComponentPreview',array(array('out'=>&$out, 'formId'=>$formId, 'componentId'=>$componentId, 'ComponentTypeName'=>$r['ComponentTypeName'],'data'=>$data)));

		return $out;
	}

	public function getFieldsTotal()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$db 	= &$this->_db;

		$query = $db->getQuery(true)
			->select('COUNT(' . $db->qn('ComponentId') . ')')
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));

		return $this->_db->setQuery($query)->loadResult();
	}

	public function getFieldsPagination()
	{
		$pagination	= new JPagination($this->getFieldsTotal(), 1, 0);
		// hack to show the order up icon for the first item
		$pagination->limitstart = 1;
		return $pagination;
	}

	public function getForm()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		if (empty($this->_form))
		{
			$this->_form = JTable::getInstance('RSForm_Forms', 'Table');
			$this->_form->load($formId);

			if (empty($this->_form->Lang))
			{
				$this->_form->Lang = JFactory::getLanguage()->getDefault();
			}

			if ($this->_form->FormLayoutAutogenerate)
			{
				$this->autoGenerateLayout();
			}

			$lang = $this->getLang();
			if ($lang != $this->_form->Lang)
			{
				if ($translations = RSFormProHelper::getTranslations('forms', $this->_form->FormId, $lang))
				{
					foreach ($translations as $field => $value)
					{
						if (isset($this->_form->{$field}))
						{
							$this->_form->{$field} = $value;
						}
					}
				}
			}
		}

		return $this->_form;
	}

	public function getFormPost()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');

		$post = JTable::getInstance('RSForm_Posts', 'Table');
		$post->load($formId, false);

		return $post;
	}

	public function autoGenerateLayout()
	{
		$formId = $this->_form->FormId;
		$filter = JFilterInput::getInstance();

		$layout = JPATH_ADMINISTRATOR.'/components/com_rsform/layouts/'.$filter->clean($this->_form->FormLayoutName, 'path').'.php';
		if (!file_exists($layout)) {
			return false;
		}

		// check if the form title should be shown
		$showFormTitle =  $this->_form->ShowFormTitle;
		// set the required field marker
		$requiredMarker = isset($this->_form->Required) ? $this->_form->Required : '(*)';
		// get the form fields
		$fieldsets 		= $this->getFieldNames('fieldsets');
		// get the form options
		$formOptions = RSFormProHelper::getForm($formId);

		// Generate the layout
		ob_start();
		// include the layout selected
		include $layout;
		$out = ob_get_contents();
		ob_end_clean();

		if ($out != $this->_form->FormLayout && $this->_form->FormId) {
			// Clean it
			// Update the layout
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->update($db->qn('#__rsform_forms'))
				->set($db->qn('FormLayout').'='.$db->q($out))
				->where($db->qn('FormId').'='.$db->q($formId));

			$db->setQuery($query)->execute();
		}

		$this->_form->FormLayout = $out;
	}

	public function getJForm()
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.form', 'form', array('control' => '', 'load_data' => false));
		if (empty($form))
		{
			return false;
		}

		if (empty($this->_form))
		{
			$this->getForm();
		}

		$data = clone $this->_form;
		$data->Language = $this->getLang();

		$form->bind($data->getProperties());

		return $form;
	}

	public function getPostJForm()
	{
		// Get the form.
		$form = $this->loadForm('com_rsform.post', 'post', array('control' => 'form_post', 'load_data' => false));
		if (empty($form))
		{
			return false;
		}

		$form->bind($this->getFormPost()->getProperties());

		return $form;
	}

	public function getProperty($fieldData, $prop, $default = null)
	{
		// Special case, we no longer use == 'YES' or == 'NO'
		if (isset($fieldData[$prop]))
		{
			if ($fieldData[$prop] === 'YES')
			{
				return true;
			}
			elseif ($fieldData[$prop] === 'NO')
			{
				return false;
			}
			else
			{
				return $fieldData[$prop];
			}
		}

		if ($default === 'YES')
		{
			return true;
		}
		elseif ($default === 'NO')
		{
			return false;
		}
		else
		{
			return $default;
		}
	}

	public function getComponentType($componentId, $formId)
	{
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->qn('ComponentTypeId'))
			->from($db->qn('#__rsform_components'))
			->where($db->qn('FormId').'='.$db->q($formId))
			->where($db->qn('ComponentId').'='.$db->q($componentId));
		
		$query->setLimit(1);
		
		$db->setQuery($query);

		return $db->loadResult();
	}

	protected function getFieldNames($type = 'all')
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/quickfields.php';
		return RSFormProQuickFields::getFieldNames($type);
	}

	public function getRequiredFields() {
		return $this->getFieldNames('required');
	}

	public function getHiddenFields() {
		return $this->getFieldNames('hidden');
	}

	public function getQuickFields() {
		return $this->getFieldNames('all');
	}

	public function getPageFields() {
		return $this->getFieldNames('pages');
	}

	public function getFormList()
	{
		$return = array();
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		// Workaround to ignore searches
		$filter_search = $this->getState('filter.search');
		$filter_state = $this->getState('filter.state');
		$this->setState('filter.search', null);
		$this->setState('filter.state', null);

		$query = $this->getListQuery();
		
		// Revert
		$this->setState('filter.search', $filter_search);
		$this->setState('filter.state', $filter_state);

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		foreach ($results as $result)
		{
			$return[] = JHtml::_('select.option', $result->FormId, $result->FormTitle, 'value', 'text', $result->FormId == $formId);
		}

		return $return;
	}

	public function save()
	{
		$mainframe 	= JFactory::getApplication();
        $post 		= array();
		$form       = JTable::getInstance('RSForm_Forms', 'Table');

		foreach ($form->getProperties(true) as $property => $value)
		{
			$post[$property] = $mainframe->input->post->get($property, $value, 'raw');
		}

		$post['formId'] = $post['FormId'];

		unset($form->Thankyou);
		unset($form->UserEmailText);
		unset($form->AdminEmailText);
		unset($form->DeletionEmailText);
		unset($form->ErrorMessage);

		if (!isset($post['FormLayoutAutogenerate']))
		{
			$post['FormLayoutAutogenerate'] = 0;
		}

		try
		{
			$form->bind($post);

			if (!$form->check())
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			$mainframe->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		$this->saveFormTranslation($form, $this->getLang());

		if ($form->store())
		{
			// Post to another location
			$row = JTable::getInstance('RSForm_Posts', 'Table');

            $form_post 				= $mainframe->input->get('form_post', array(), 'array');
            $form_post['form_id'] 	= $post['formId'];
			$form_post['fields'] 	= array();
			if (isset($form_post['name'], $form_post['value']) && is_array($form_post['name']) && is_array($form_post['value']))
			{
				for ($i = 0; $i < count($form_post['name']); $i++)
				{
					$form_post['fields'][] = array(
						'name'  => $form_post['name'][$i],
						'value' => $form_post['value'][$i],
					);
				}
			}
			$form_post['headers'] 	= array();
			if (isset($form_post['headers_name'], $form_post['headers_value']) && is_array($form_post['headers_name']) && is_array($form_post['headers_value']))
			{
				for ($i = 0; $i < count($form_post['headers_name']); $i++)
				{
					$form_post['headers'][] = array(
						'name'  => $form_post['headers_name'][$i],
						'value' => $form_post['headers_value'][$i],
					);
				}
			}

			$row->save($form_post);

			// Trigger event
			$mainframe->triggerEvent('onRsformFormSave', array(&$form));
			return true;
		}
		else
		{
			$mainframe->enqueueMessage($form->getError(), 'error');
			return false;
		}
	}

	public function saveFormTranslation(&$form, $lang)
	{
		if ($form->Lang == $lang || (RSFormProHelper::getConfig('global.disable_multilanguage') && RSFormProHelper::getConfig('global.default_language') == 'en-GB'))
        {
            return true;
        }

		$fields 	  = array('FormTitle', 'UserEmailFromName', 'UserEmailSubject', 'AdminEmailFromName', 'AdminEmailSubject', 'DeletionEmailFromName', 'DeletionEmailSubject', 'DeletionEmailReplyToName', 'MetaDesc', 'MetaKeywords', 'UserEmailReplyToName', 'AdminEmailReplyToName', 'ReturnUrl');
		$translations = RSFormProHelper::getTranslations('forms', $form->FormId, $lang, 'id');
		foreach ($fields as $field)
		{
            $translation = (object) array(
                'form_id'       => $form->FormId,
                'lang_code'     => $lang,
                'reference'     => 'forms',
                'reference_id'  => $field,
                'value'         => $form->{$field}
            );

			if (!isset($translations[$field]))
			{
			    $this->_db->insertObject('#__rsform_translations', $translation);
			}
			else
			{
			    $translation->id = $translations[$field];
			    $this->_db->updateObject('#__rsform_translations', $translation, array('id'));
			}
			unset($form->$field);
		}

		return true;
	}

	public function saveFormPropertyTranslation($formId, $componentId, &$params, $lang, $just_added, $properties)
	{
		$fields 	  = RSFormProHelper::getTranslatableProperties();
		$translations = RSFormProHelper::getTranslations('properties', $formId, $lang, 'id');

		foreach ($fields as $field)
		{
			if (!isset($params[$field])) continue;

			$reference_id = $componentId.'.'.$this->_db->escape($field);

			$translation = (object) array(
                'form_id'       => $formId,
                'lang_code'     => $lang,
                'reference'     => 'properties',
                'reference_id'  => $reference_id,
                'value'         => $params[$field]
            );

			if (!isset($translations[$reference_id]))
			{
			    $this->_db->insertObject('#__rsform_translations', $translation);
			}
			else
			{
			    $translation->id = $translations[$reference_id];
                $this->_db->updateObject('#__rsform_translations', $translation, array('id'));
			}

			if (!$just_added && in_array($field, $properties))
			{
                unset($params[$field]);
            }
		}
	}

	public function getLang()
	{
		if (empty($this->_form))
		{
			$this->getForm();
		}

		return RSFormProHelper::getCurrentLanguage($this->_form->FormId);
	}

	public function getMappings()
	{
		if (empty($this->_mdata))
		{
			$db 	= JFactory::getDbo();
			$formId	= JFactory::getApplication()->input->getInt('formId');

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_mappings'))
				->where($db->qn('formId') . ' = ' . $db->q($formId))
				->order($db->qn('ordering') . ' ' . $db->escape('asc'));

			$this->_mdata = $this->_getList($query);
		}

		return $this->_mdata;
	}

	public function getCalculations()
	{
		$formId	= JFactory::getApplication()->input->getInt('formId');

		return RSFormProHelper::getCalculations($formId);
	}

	public function getConditions()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/conditions.php';

		$formId	= JFactory::getApplication()->input->getInt('formId');
		$lang	= $this->getLang();

		return RSFormProConditions::getConditions($formId, $lang, null);
	}

	public function getEmails()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		if (!$formId)
		{
			return array();
		}

		$db 	= &$this->_db;
		$query 	= $db->getQuery(true)
			->select($db->qn(array('id', 'to', 'subject', 'formId')))
			->from($db->qn('#__rsform_emails'))
			->where($db->qn('type') . ' = ' . $db->q('additional'))
			->where($db->qn('formId') . ' = ' . $db->q($formId));

		$emails = $this->_getList($query);
		if (!empty($emails))
		{
			$translations = RSFormProHelper::getTranslations('emails', $formId, JFactory::getSession()->get('com_rsform.form.formId'.$formId.'.lang', JFactory::getLanguage()->getDefault()));
			foreach ($emails as $id => $email)
			{
				if (isset($translations[$email->id.'.fromname']))
				{
					$emails[$id]->fromname = $translations[$email->id.'.fromname'];
				}

				if (isset($translations[$email->id.'.subject']))
				{
					$emails[$id]->subject = $translations[$email->id.'.subject'];
				}

				if (isset($translations[$email->id.'.message']))
				{
					$emails[$id]->message = $translations[$email->id.'.message'];
				}
			}
		}

		return $emails;
	}

	public function copyComponent($sourceComponentId, $toFormId)
	{
		$sourceComponentId 	= (int) $sourceComponentId;
		$toFormId 			= (int) $toFormId;
		$db 				= JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('c.*')
			->select('ct.CanBeDuplicated')
			->from($db->qn('#__rsform_components', 'c'))
			->join('left', $db->qn('#__rsform_component_types', 'ct') . ' ON (' . $db->qn('c.ComponentTypeId') . ' = ' . $db->qn('ct.ComponentTypeId') . ')')
			->where($db->qn('ComponentId').'='.$db->q($sourceComponentId));
		if ($component = $db->setQuery($query)->loadObject())
		{
			if (!$component->CanBeDuplicated)
			{
				if ($toFormId == $component->FormId)
				{
					throw new Exception(JText::_('COM_RSFORM_CANNOT_DUPLICATE_THIS_FIELD'));
				}
				else
				{
					// Check if the new form has this type of field already
					$query->clear()
						->select($db->qn('ComponentId'))
						->from($db->qn('#__rsform_components'))
						->where($db->qn('FormId') . ' = ' . $db->q($toFormId))
						->where($db->qn('ComponentTypeId') . ' = ' . $db->q($component->ComponentTypeId));

					if ($db->setQuery($query)->loadResult())
					{
						throw new Exception(JText::_('COM_RSFORM_CANNOT_COPY_THIS_FIELD_TO_FORM_ALREADY_EXISTS'));
					}
				}
			}
			// Get max ordering
			$query->clear()
				->select('MAX('.$db->qn('Order').')')
				->from($db->qn('#__rsform_components'))
				->where($db->qn('FormId').'='.$db->q($toFormId));
			$component->Order = (int) $db->setQuery($query)->loadResult() + 1;

			// Insert the new field
			$query->clear()
				->insert($db->qn('#__rsform_components'))
				->set($db->qn('FormId').'='.$db->q($toFormId))
				->set($db->qn('ComponentTypeId').'='.$db->q($component->ComponentTypeId))
				->set($db->qn('Order').'='.$db->q($component->Order))
				->set($db->qn('Published').'='.$db->q($component->Published));
			$db->setQuery($query)->execute();

			// Get the newly created field ID
			$newComponentId = $db->insertid();

			// Get the properties of the field so we can duplicate them
			$query->clear()
				->select('*')
				->from($db->qn('#__rsform_properties'))
				->where($db->qn('ComponentId').'='.$db->q($sourceComponentId));
			$properties = $db->setQuery($query)->loadObjectList();
			foreach ($properties as $property) {
				// Handle duplicated fields
				if ($property->PropertyName == 'NAME' && $toFormId == $component->FormId) {
					$property->PropertyValue .= ' copy';

					while (RSFormProHelper::componentNameExists($property->PropertyValue, $toFormId)) {
						$property->PropertyValue .= mt_rand(0,9);
					}
				}

				$query->clear()
					->insert('#__rsform_properties')
					->set($db->qn('ComponentId').'='.$db->q($newComponentId))
					->set($db->qn('PropertyName').'='.$db->q($property->PropertyName))
					->set($db->qn('PropertyValue').'='.$db->q($property->PropertyValue));
				$db->setQuery($query)->execute();
			}

			// Copy language
			$query->clear()
				->select('*')
				->from($db->qn('#__rsform_translations'))
				->where($db->qn('reference').'='.$db->q('properties'))
				->where($db->qn('reference_id').' LIKE '.$db->q($sourceComponentId.'.%'));
			$translations = $db->setQuery($query)->loadObjectList();
			foreach ($translations as $translation) {
				list($oldComponentId, $property) = explode('.', $translation->reference_id, 2);
				$reference_id = $newComponentId.'.'.$property;

				$query->clear()
					->insert('#__rsform_translations')
					->set($db->qn('form_id').'='.$db->q($toFormId))
					->set($db->qn('lang_code').'='.$db->q($translation->lang_code))
					->set($db->qn('reference').'='.$db->q('properties'))
					->set($db->qn('reference_id').'='.$db->q($reference_id))
					->set($db->qn('value').'='.$db->q($translation->value));

				$db->setQuery($query)->execute();
			}

			return $newComponentId;
		}

		return false;
	}
}