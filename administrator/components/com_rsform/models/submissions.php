<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelSubmissions extends JModelList
{
	public $_data = array();
	
	public $firstFormId = 0;
	public $allFormIds = array();
	
	public $export = false;
	public $rows = 0;
	public $exportType = '';
	public $stripLines = false;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array_merge(array_keys($this->getHeaders()), array_keys($this->getStaticHeaders()));

			// Need these so that 'Filter Options' is shown
			$config['filter_fields'][] = 'dateFrom';
			$config['filter_fields'][] = 'dateTo';
			$config['filter_fields'][] = 'language';
		}

		parent::__construct($config);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.dateFrom');
		$id .= ':' . $this->getState('filter.dateTo');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$sortColumn 	= $this->getSortColumn();
		$sortOrder 		= $this->getSortOrder();
		$formId 		= $this->getFormId();
		$filter 		= $this->getFilter();
		$languageFilter	= $this->getLang();
		$isStaticHeader = in_array($sortColumn, array_keys($this->getStaticHeaders()));
		$join			= false;

		$query = $this->_db->getQuery(true)
			->select('s.*')
			->from($this->_db->qn('#__rsform_submissions', 's'))
			->where($this->_db->qn('s.FormId') . ' = ' . $this->_db->q($formId));

		// Only for export - export selected rows
		if ($this->export && !empty($this->rows))
		{
			$query->where($this->_db->qn('s.SubmissionId') . ' IN (' . implode(',', $this->_db->q($this->rows)) . ')');
		}

		// Check if there's a filter (search) set
		if (!$this->export)
		{
			if ($filter !== '')
			{
				$or 			= array();
				$join 			= true;
				$escapedFilter  = $this->_db->q('%' . $this->_db->escape($filter) . '%', false);

				if (!preg_match('#([^0-9\-: ])#', $filter))
				{
					$or[] = $this->_db->qn('s.DateSubmitted') . ' LIKE ' . $escapedFilter;
				}
				$or[] = $this->_db->qn('s.Username') . ' LIKE ' . $escapedFilter;
				$or[] = $this->_db->qn('s.UserIp') . ' LIKE ' . $escapedFilter;

				if ($isStaticHeader)
				{
					$or[] = $this->_db->qn('sv.FieldValue') . ' LIKE ' . $escapedFilter;
				}

				else
				{
					$subquery = $this->_db->getQuery(true)
						->select($this->_db->qn('SubmissionId'))
						->from($this->_db->qn('#__rsform_submission_values'))
						->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId))
						->where($this->_db->qn('FieldValue') . ' LIKE ' . $escapedFilter);

					$or[] = $this->_db->qn('s.SubmissionId') . ' IN (' . $subquery . ')';
				}

				$query->where('(' . implode(' OR ', $or) . ')');
			}

			if ($languageFilter)
			{
				$query->where($this->_db->qn('s.Lang') . ' = ' . $this->_db->q($languageFilter));
			}

			if ($from = $this->getDateFrom())
			{
				$query->where($this->_db->qn('s.DateSubmitted') . ' >= ' . $this->_db->q(JFactory::getDate($from)->toSql()));
			}

			if ($to = $this->getDateTo())
			{
				$query->where($this->_db->qn('s.DateSubmitted') . ' <= ' . $this->_db->q(JFactory::getDate($to)->toSql()));
			}
		}

		// Order by static headers
		if ($isStaticHeader)
		{
			$query->order($this->_db->qn('s.' . $sortColumn) . ' ' . $this->_db->escape($sortOrder));
		}
		else
		{
			$join = true;

			if ($this->isOrderingPossible($sortColumn))
			{
				$query->where($this->_db->qn('sv.FieldName') . ' = ' . $this->_db->q($sortColumn));
			}

			$query->order($this->_db->qn('sv.FieldValue') . ' ' . $this->_db->escape($sortOrder));
		}

		if ($join)
		{
			$query->join('left', $this->_db->qn('#__rsform_submission_values', 'sv') . ' ON (' . $this->_db->qn('s.SubmissionId') . ' = ' . $this->_db->qn('sv.SubmissionId') . ')')
				->group(array($this->_db->qn('s.SubmissionId')));
		}
		
		return $query;
	}
	
	public function isOrderingPossible($field)
	{
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('SubmissionValueId'))
			->from($this->_db->qn('#__rsform_submission_values'))
			->where($this->_db->qn('FieldName') . ' = ' . $this->_db->q($field))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($this->getFormId()));

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	public function getDateFrom()
	{
		return $this->getState('filter.dateFrom');
	}
	
	public function getDateTo()
	{
		return $this->getState('filter.dateTo');
	}

	public function getLang()
	{
		return $this->getState('filter.language');
	}

	public function getFilter()
	{
		return $this->getState('filter.search');
	}

	public function getSpecialFields()
	{
		return RSFormProHelper::getDirectoryFormProperties($this->getFormId(), true);
	}

	public function getFormProperties()
	{
		return RSFormProHelper::getForm($this->getFormId());
	}
	
	public function getSubmissions()
	{
		if (empty($this->_data))
		{
			$formId = $this->getFormId();
			$app	= JFactory::getApplication();
			$form   = $this->getFormProperties();

			if (empty($form))
			{
				return $this->_data;
			}
			
			$uploadFields 	= array();
			$multipleFields = array();
			$textareaFields = array();
			$fieldTypes = $this->getSpecialFields();
			if (isset($fieldTypes['uploadFields']))
			{
				$uploadFields = $fieldTypes['uploadFields'];	
			}
			if (isset($fieldTypes['multipleFields']))
			{
				$multipleFields = $fieldTypes['multipleFields'];	
			}
			if (isset($fieldTypes['textareaFields']))
			{
				$textareaFields = $fieldTypes['textareaFields'];	
			}
			
			$this->_db->setQuery("SET SQL_BIG_SELECTS=1")->execute();
			
			$results = $this->_getList($this->getListQuery(), $this->getStart(), $this->getState('list.limit'));

			foreach ($results as $result)
			{
				$this->_data[$result->SubmissionId] = array(
					'SubmissionId'     => $result->SubmissionId,
					'FormId'           => $result->FormId,
					'DateSubmitted'    => RSFormProHelper::getDate($result->DateSubmitted),
					'UserIp'           => $result->UserIp,
					'Username'         => $result->Username,
					'UserId'           => $result->UserId,
					'Lang'             => $result->Lang,
					'confirmed'        => $result->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO'),
					'SubmissionValues' => array(),
				);
			}

			$submissionIds = array_keys($this->_data);
			
			if (!empty($submissionIds))
			{
				$must_escape = $app->input->get('view') == 'submissions' && $app->input->get('layout') == 'default';

				$query = $this->_db->getQuery(true)
					->select('*')
					->from($this->_db->qn('#__rsform_submission_values'))
					->where($this->_db->qn('SubmissionId') . ' IN (' . implode(',', $this->_db->q($submissionIds)) . ')');
				
				$results = $this->_db->setQuery($query)->loadObjectList();
				foreach ($results as $result)
				{
					// Check if this is an upload field
					if (in_array($result->FieldName, $uploadFields) && !empty($result->FieldValue) && !$this->export)
					{
						$files = RSFormProHelper::explode($result->FieldValue);

						$values = array();
						foreach ($files as $file)
						{
							$values[] = '<a href="index.php?option=com_rsform&amp;task=submissions.viewfile&amp;id='.$result->SubmissionValueId.'&amp;file='.md5($file).'">'.RSFormProHelper::htmlEscape(basename($file)).'</a>';
						}

						$result->FieldValue = implode('<br />', $values);
					}
					else
					{
						// Check if this is a multiple field
						if (in_array($result->FieldName, $multipleFields))
						{
							$result->FieldValue = str_replace("\n", $form->MultipleSeparator, $result->FieldValue);
						}
						// Transform new lines
						elseif ($form->TextareaNewLines && in_array($result->FieldName, $textareaFields))
						{
							if ($must_escape)
							{
								$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
							}
							elseif ($this->exportType === 'csv' && !$this->stripLines)
							{
								$result->FieldValue = nl2br($result->FieldValue);
							}
						}
						elseif ($must_escape)
						{
							$result->FieldValue = RSFormProHelper::htmlEscape($result->FieldValue);
						}
					}
						
					$this->_data[$result->SubmissionId]['SubmissionValues'][$result->FieldName] = array(
						'Value' => $result->FieldValue,
						'Id'    => $result->SubmissionValueId
					);
				}
				
				JFactory::getApplication()->triggerEvent('onRsformBackendManageSubmissions', array(array(
                    'formId'   		=> $formId,
                    'submissions' 	=> &$this->_data,
                    'export'  		=> $this->export,
                    'escape'  		=> $must_escape,
                )));
			}
			unset($results);
		}
		
		return $this->_data;
	}
	
	public function getSubmission()
	{
		$query = $this->_db->getQuery(true)
			->select('*')
			->from($this->_db->qn('#__rsform_submissions'))
			->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($this->getSubmissionId()));

		return $this->_db->setQuery($query)->loadObject();
	}

	protected function getSkippedFields()
	{
		$skippedFields = array(RSFORM_FIELD_BUTTON, RSFORM_FIELD_CAPTCHA, RSFORM_FIELD_FREETEXT, RSFORM_FIELD_SUBMITBUTTON);
		$skippedFields = array_merge($skippedFields, RSFormProHelper::$captchaFields);

		JFactory::getApplication()->triggerEvent('onRsformBackendGetSkippedFields', array(&$skippedFields));

		return $skippedFields;
	}
	
	public function getHeaders()
	{
		$db     = JFactory::getDbo();
		$formId = $this->getFormId();

		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('left', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ' AND ' . $db->qn('p.PropertyName') . ' = ' . $db->q('NAME') . ')')
			->join('left', $db->qn('#__rsform_component_types', 'ct') . ' ON (' . $db->qn('c.ComponentTypeId') . '=' . $db->qn('ct.ComponentTypeId') . ')')
			->where($db->qn('c.FormId') . ' = ' . $db->q($formId))
			->where($db->qn('c.Published') . ' = ' . $db->q(1));

		$query->where($db->qn('ct.ComponentTypeId') . ' NOT IN (' . implode(',', $db->q($this->getSkippedFields())) . ')');

		$query->order($db->qn('c.Order'));

		$headers = $db->setQuery($query)->loadColumn();

        JFactory::getApplication()->triggerEvent('onRsformBackendGetSubmissionHeaders', array(&$headers, $formId));

        // Get labels
		$results = array();
		if ($headers)
		{
			foreach ($headers as $header)
			{
				$value = $header;

				JFactory::getApplication()->triggerEvent('onRsformBackendGetHeaderLabel', array(&$header, $formId));

				$results[$value] = new RSFormProSubmissionHeader($value, $header, 0, $formId);
			}
		}

		return $results;
	}
	
	public function getUnescapedFields()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('p.PropertyValue'))
			->from($db->qn('#__rsform_components', 'c'))
			->join('left', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . '=' . $db->qn('p.ComponentId') . ')')
			->where($db->qn('c.FormId') . ' = ' . $db->q($this->getFormId()))
			->where($db->qn('c.ComponentTypeId') . ' = ' . $db->q(RSFORM_FIELD_FILEUPLOAD))
			->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'));
		$fields = $db->setQuery($query)->loadColumn();
		
		JFactory::getApplication()->triggerEvent('onRsformBackendManageSubmissionsCreateUnescapedFields', array(array(
            'formId'    => $this->getFormId(),
            'fields'    => &$fields
        )));
        
        return $fields;
	}
	
	public function getStaticHeaders()
	{
		$headers = array('SubmissionId', 'DateSubmitted', 'UserIp', 'Username', 'UserId', 'Lang');

		if ($this->addConfirmedHeader())
		{
			$headers[] = 'confirmed';
		}

		$results = array();

		foreach ($headers as $header)
		{
			$results[$header] = new RSFormProSubmissionHeader($header, JText::_('RSFP_' . $header), 1, $this->getFormId());
		}
		
		return $results;
	}
	
	public function addConfirmedHeader()
	{
		if ($form = $this->getFormProperties())
		{
			return (bool) $form->ConfirmSubmission;
		}

		return false;
	}
	
	public function getFormTitle()
	{
		$formId = $this->getFormId();

		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormTitle'))
			->from($this->_db->qn('#__rsform_forms'))
			->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($formId));
		$title = $this->_db->setQuery($query)->loadResult();

		if ($translations = RSFormProHelper::getTranslations('forms', $formId, RSFormProHelper::getCurrentLanguage($formId)))
		{
			if (isset($translations['FormTitle']))
			{
				$title = $translations['FormTitle'];
			}
		}

		return $title;
	}
	
	public function getForms()
	{
		$mainframe 	= JFactory::getApplication();
		$db        	= JFactory::getDbo();
		$sortColumn = $mainframe->getUserState('com_rsform.forms.filter_order', 'FormId');
		$sortOrder  = $mainframe->getUserState('com_rsform.forms.filter_order_Dir', 'ASC');
		$return 	= array();

        $query = $db->getQuery(true)
            ->select($db->qn('FormId'))
            ->select($db->qn('FormTitle'))
            ->select($db->qn('Lang'))
            ->from($db->qn('#__rsform_forms'))
            ->order($db->qn($sortColumn) . ' ' . $db->escape($sortOrder));
        if ($results = $db->setQuery($query)->loadObjectList())
        {
            foreach ($results as $result)
            {
                $lang = RSFormProHelper::getCurrentLanguage($result->FormId);
                if ($lang != $result->Lang)
                {
                    if ($translations = RSFormProHelper::getTranslations('forms', $result->FormId, $lang))
                    {
                        foreach ($translations as $field => $value)
                        {
                            if (isset($result->{$field}))
                            {
                                $result->{$field} = $value;
                            }
                        }
                    }
                }

                $return[] = JHtml::_('select.option', $result->FormId, $result->FormTitle);
                $this->allFormIds[] = $result->FormId;
            }

            if (!empty($results[0]->FormId))
			{
				$this->firstFormId = $results[0]->FormId;
			}
        }
		
		return $return;
	}
	
	public function getSortColumn()
	{
		return $this->getState('list.ordering', 'DateSubmitted');
	}
	
	public function getSortOrder()
	{
		return $this->getState('list.direction', 'desc');
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// We do this here because it overrides our setUserState() below
		parent::populateState('DateSubmitted', 'desc');

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$app = JFactory::getApplication();
		$offset = JFactory::getConfig()->get('offset');

		foreach (array('dateFrom', 'dateTo') as $date)
		{
			$value = $this->getUserStateFromRequest($this->context . '.filter.' . $date, 'filter_' . $date);

			if (strlen($value))
			{
				// Test if date is valid
				try
				{
					$value = JFactory::getDate($value, $offset)->toSql();
				}
				catch (Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'warning');

					// Reset the value
					$value = '';
				}
			}

			$this->setState('filter.' . $date, $value);
			$app->setUserState($this->context . '.filter.' . $date, $value);
		}
	}
	
	public function getFormId()
	{
		$mainframe = JFactory::getApplication();
		
		if (empty($this->firstFormId))
		{
			$this->getForms();
		}

		$formId = $mainframe->getUserStateFromRequest('com_rsform.submissions.formId', 'formId', $this->firstFormId, 'int');
		if ($formId && !in_array($formId, $this->allFormIds))
		{
			$formId = $this->firstFormId;
			$mainframe->setUserState('com_rsform.submissions.formId', $formId);
		}
		
		return $formId;
	}
	
	public function getSubmissionId()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (is_array($cid))
		{
			$cid = (int) reset($cid);
		}
		else
		{
			$cid = (int) $cid;
		}
		
		return $cid;
	}
	
	public function getEditFields()
	{
        $mainframe  = JFactory::getApplication();
        $db         = $this->_db;
		$isPDF      = $mainframe->input->get('format') == 'pdf';
		$cid        = $this->getSubmissionId();
		$return     = array();

        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
        $submission = RSFormProSubmissionsHelper::getSubmission($cid);
		
		if (empty($submission))
		{
			$mainframe->redirect('index.php?option=com_rsform&view=submissions');
			return $return;
		}
		
		if ($isPDF)
		{
		    $query = $this->_db->getQuery(true)
                ->select($this->_db->qn('MultipleSeparator'))
                ->select($this->_db->qn('TextareaNewLines'))
                ->from($this->_db->qn('#__rsform_forms'))
                ->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($submission->FormId));
			$form = $this->_db->setQuery($query)->loadObject();

			$form->MultipleSeparator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);
		}

        $query = $db->getQuery(true);
        $query->select($db->qn('p.PropertyValue'))
            ->select($db->qn('ct.ComponentTypeName'))
            ->select($db->qn('c.ComponentId'))
            ->from($db->qn('#__rsform_components','c'))
            ->join('left', $db->qn('#__rsform_properties','p').' ON '.$db->qn('p.ComponentId').' = '.$db->qn('c.ComponentId'))
            ->join('left', $db->qn('#__rsform_component_types','ct').' ON '.$db->qn('c.ComponentTypeId').' = '.$db->qn('ct.ComponentTypeId'))
            ->where($db->qn('c.FormId') . ' = ' . $db->q($submission->FormId))
            ->where($db->qn('p.PropertyName') . ' = ' . $db->q('NAME'))
            ->where($db->qn('c.Published') . ' = ' . $db->q(1))
            ->order($db->qn('c.Order') . ' ' . $db->escape('asc'));

        // Skip some fields
		$query->where($db->qn('ct.ComponentTypeId') . ' NOT IN (' . implode(',', $db->q($this->getSkippedFields())) . ')');

        $fields = $db->setQuery($query)->loadObjectList();

		if (empty($fields))
        {
            return $return;
        }

		$componentIds = array();
		foreach ($fields as $field)
        {
            $componentIds[] = $field->ComponentId;
        }

		$properties = RSFormProHelper::getComponentProperties($componentIds);

		foreach ($fields as $field)
		{
			$data = $properties[$field->ComponentId];
            $name = $field->PropertyValue;
			
			$new_field = array();
			$new_field[0] = $name;
			$new_field[3] = $name;

			$value = isset($submission->values[$field->PropertyValue]) ? $submission->values[$field->PropertyValue] : '';
			
			switch ($field->ComponentTypeName)
			{
				// skip this field for now, no need to edit it
				case 'freeText':
					continue 2;
				break;
				
				default:
					if ($isPDF)
					{
						$new_field[1] = RSFormProHelper::htmlEscape($value);
					}
					else
					{
						if (strpos($value, "\n") !== false || strpos($value, "\r") !== false)
						{
							$new_field[1] = '<textarea style="width: 95%" class="rs_textarea" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
						}
						else
						{
							$new_field[1] = '<input class="rs_inp rs_80" size="105" type="text" name="form['.$name.']" value="'.RSFormProHelper::htmlEscape($value).'" />';
						}
					}
				break;
				
				case 'textArea':
					if ($isPDF)
					{
						if ($form->TextareaNewLines && (!isset($data['WYSIWYG']) || $data['WYSIWYG'] == 'NO'))
                        {
                            $value = nl2br(RSFormProHelper::htmlEscape($value));
                        }

						$new_field[1] = $value;
					}
					elseif (isset($data['WYSIWYG']) && $data['WYSIWYG'] == 'YES')
					{
						$new_field[1] = RSFormProHelper::WYSIWYG('form['.$name.']', RSFormProHelper::htmlEscape($value), '', 600, 100, 60, 10);
					}
					else
					{
						$new_field[1] = '<textarea style="width: 95%" class="rs_textarea" rows="10" cols="60" name="form['.$name.']">'.RSFormProHelper::htmlEscape($value).'</textarea>';
					}
				break;
				
				case 'radioGroup':
				case 'checkboxGroup':
				case 'selectList':
					if ($isPDF)
					{
						$new_field[1] = str_replace("\n", $form->MultipleSeparator, $value);
						break;
					}

					$options = array();
					
					if ($field->ComponentTypeName == 'radioGroup')
					{
						$data['SIZE'] = 0;
						$data['MULTIPLE'] = 'NO';
						$options[] = JHtml::_('select.option', '', JText::_('COM_RSFORM_NO_VALUE'));
					}
					elseif ($field->ComponentTypeName == 'checkboxGroup')
					{
						$data['SIZE'] = 5;
						$data['MULTIPLE'] = 'YES';
					}
					
					$value = RSFormProHelper::explode($value);

                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fielditem.php';
                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fieldmultiple.php';
                    $f = new RSFormProFieldMultiple(array(
                        'formId' 			=> $submission->FormId,
                        'componentId' 		=> $field->ComponentId,
                        'data' 				=> $data,
                        'value' 			=> array('formId' => $submission->FormId, $data['NAME'] => $value),
                        'invalid' 			=> false
                    ));

                    if ($items = $f->getItems())
                    {
                        foreach ($items as $item)
                        {
                            $item = new RSFormProFieldItem($item);

                            if ($item->flags['optgroup'])
                            {
                                $options[] = JHtml::_('select.option', '<OPTGROUP>', $item->label, 'value', 'text');
                            }
                            elseif ($item->flags['/optgroup'])
							{
                                $options[] = JHtml::_('select.option', '</OPTGROUP>', $item->label, 'value', 'text');
                            }
                            else
                            {
                                $options[] = JHtml::_('select.option', $item->value, $item->label, 'value', 'text', $item->flags['disabled']);
                            }
                        }
                    }

                    $attribs = array();

                    if ((int) $data['SIZE'] > 0)
                    {
                        $attribs[] = 'size="'.(int) $data['SIZE'].'"';
                    }

                    if ($data['MULTIPLE'] == 'YES')
                    {
                        $attribs[] = 'multiple="multiple"';
                    }

                    $attribs = implode(' ', $attribs);
					
					$new_field[1] = JHtml::_('select.genericlist', $options, 'form['.$name.'][]', $attribs, 'value', 'text', $value);
				break;
				
				case 'fileUpload':
					if ($isPDF)
					{
						if (!empty($data['FILESSEPARATOR']))
						{
							$separator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $data['FILESSEPARATOR']);
						}
						else
						{
							$separator = '<br />';
						}

						$new_field[1] = implode($separator, RSFormProHelper::explode($value));

						break;
					}

					if ($value)
					{
						$files = RSFormProHelper::explode($value);
					}
					else
					{
						$files = array();
					}

					$new_field[1] = '<ul class="rsfp-multiupload-list">';

					foreach ($files as $file)
					{
						$new_field[1] .= '<li><button type="button" class="btn btn-secondary btn-mini btn-sm" onclick="RSFormPro.removeFile(this);">' . JText::_('COM_RSFORM_REMOVE_FILE') . '</button> <input type="hidden" name="form[' . $data['NAME'] . '][]" value="' . RSFormProHelper::htmlEscape($file) . '" />' . RSFormProHelper::htmlEscape(basename($file)) . '</li>';
					}

					$multiple =  !empty($data['MULTIPLE']) && $data['MULTIPLE'] == 'YES';
					$new_field[1] .= '</ul>';

					$new_field[1] .= '<input size="45" type="file" name="upload['.$name.']' . ($multiple ? '[]' : '') . '" ' . ($multiple ? 'multiple' : '') . ' />';


				break;
			}
			
			$return[] = $new_field;
		}

        $mainframe->triggerEvent('onRsformBackendGetEditFields', array(&$return, $submission));
		
		return $return;
	}
	
	public function save()
	{
		$app	= JFactory::getApplication();
        $formId = $app->input->getInt('formId');
		$cid    = $this->getSubmissionId();
		$form   = $app->input->post->get('form', array(), 'array');
        $static = $app->input->post->get('formStatic', array(), 'array');
        $files  = $app->input->files->get('upload', array(), 'array');
		$date	= JFactory::getDate($static['DateSubmitted'], JFactory::getConfig()->get('offset'));

		$static['DateSubmitted'] = $date->toSql();

		require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
		$submission = RSFormProSubmissionsHelper::getSubmission($cid);

		// Check if submission exists
		if (!$submission)
		{
			return false;
		}

		// Handle file uploads first
		if (!empty($files))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/fields/fileupload.php';

            foreach ($files as $field => $file)
            {
				if ($componentId = RSFormProHelper::getComponentId($field, $formId))
				{
					$data = RSFormProHelper::getComponentProperties($componentId);

					// If we've cleared all files
					if (!isset($form[$field]))
					{
						$form[$field] = array();
					}

					$valueSent = (array) $form[$field];

					if (isset($submission->values[$field]))
					{
						$originals = RSFormProHelper::explode($submission->values[$field]);

						foreach ($originals as $k => $original)
						{
							// File has been removed from list, remove the original file to save up space
							if (!in_array($original, $valueSent) && file_exists($original) && is_file($original))
							{
								JFile::delete($original);
							}
						}
					}

					$f = new RSFormProFieldFileUpload(array(
						'formId' 		=> $formId,
						'componentId' 	=> $componentId,
						'data' 			=> $data,
					));

					if ($object = $f->processBeforeStore($submission->SubmissionId, $form, $files, false))
					{
						if (!is_array($form[$field]))
						{
							$form[$field] = (array) $form[$field];
						}

						if (!empty($data['MULTIPLE']) && $data['MULTIPLE'] === 'YES')
						{
							$form[$field] = array_merge($form[$field], RSFormProHelper::explode($object->FieldValue));
						}
						else
						{
							$form[$field] = RSFormProHelper::explode($object->FieldValue);
						}
					}
				}
            }
        }

        // Static submission data
        if ($static)
        {
            $object = new stdClass();
			$object->SubmissionId = $submission->SubmissionId;
            foreach ($static as $field => $value)
            {
				$object->{$field} = $value;
            }

            $this->_db->updateObject('#__rsform_submissions', $object, array('SubmissionId'));
        }

		// Checkboxes and other empty fields don't send a value, so just make sure we have them all here
        if ($fields = $this->getEditFields())
        {
        	foreach ($fields as $field)
	        {
	        	if (!isset($form[$field[0]]))
		        {
		        	$form[$field[0]] = '';
		        }
	        }
        }

		// Update dynamic fields
		foreach ($form as $field => $value)
		{
			if (is_array($value))
            {
                $value = implode("\n", $value);
            }

			$object = (object) array(
				'FormId'        => $formId,
				'SubmissionId'  => $submission->SubmissionId,
				'FieldName'     => $field,
				'FieldValue'    => $value
			);

			if (!isset($submission->values[$field]))
			{
			    $this->_db->insertObject('#__rsform_submission_values', $object);
			}
			elseif ($submission->values[$field] !== $value)
			{
				// Update only if we've changed something
				$this->_db->updateObject('#__rsform_submission_values', $object, array('SubmissionId', 'FormId', 'FieldName'));
			}
		}
	}
	
	public function getSubmissionFormId()
	{
		$query = $this->_db->getQuery(true)
			->select($this->_db->qn('FormId'))
			->from($this->_db->qn('#__rsform_submissions'))
			->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($this->getSubmissionId()));

		return $this->_db->setQuery($query)->loadResult();
	}
	
	public function getExportSelected()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$cid = array_map('intval', $cid);
		
		return $cid;
	}
	
	public function getExportFile()
	{
		return uniqid('');
	}
	
	public function getStaticFields()
	{
		$submissionid = $this->getSubmissionId();

		$query = $this->_db->getQuery(true)
			->select('*')
			->from($this->_db->qn('#__rsform_submissions'))
			->where($this->_db->qn('SubmissionId') . ' = ' . $this->_db->q($submissionid));
		
		$this->_db->setQuery($query);
		$submission = $this->_db->loadObject();
		
		if ($submission)
		{
			$submission->DateSubmitted = JHtml::_('date', $submission->DateSubmitted, 'Y-m-d H:i:s');
		}
		
		return $submission;
	}
	
	public function getExportType()
	{
		$task = JFactory::getApplication()->input->getCmd('task');
		$task = explode('.', $task);
		return end($task);
	}
	
	public function getExportTotal()
	{
		$rows = JFactory::getApplication()->input->get('ExportRows', '', 'string');

		switch ($rows)
		{
			case '0':
				$query = $this->_db->getQuery(true)
					->select('COUNT(' . $this->_db->qn('SubmissionId') . ')')
					->from($this->_db->qn('#__rsform_submissions'))
					->where($this->_db->qn('FormId') . ' = ' . $this->_db->q($this->getFormId()));
				$this->_db->setQuery($query);
				return $this->_db->loadResult();
				break;

			case '-1':
				return $this->getTotal();
				break;

			default:
				return count(explode(',', $rows));
				break;
		}
	}

	public function getImportTotal()
    {
        $config = JFactory::getConfig();
        $file   = $config->get('tmp_path') . '/' . md5($config->get('secret'));

        return file_exists($file) ? filesize($file) : 0;
    }
	
	public function getLanguages()
	{
		$languages 	= JLanguageHelper::getKnownLanguages(JPATH_SITE);
		$return 	= array();

		$return[] = JHtml::_('select.option', '', JText::_('RSFP_SUBMISSIONS_ALL_LANGUAGES'));
		foreach ($languages as $tag => $properties)
		{
			$return[] = JHtml::_('select.option', $tag, $properties['name']);
		}

		return $return;
	}

	public function getPreviewImportData()
    {
        $session    = JFactory::getSession();
        $config     = JFactory::getConfig();
        $file       = $config->get('tmp_path') . '/' . md5($config->get('secret'));
        $options    = $session->get('com_rsform.import.options', array());

        $skipHeaders    = !empty($options['skipHeaders']);
        $delimiter      = empty($options['delimiter']) ? ',' : $options['delimiter'];
        $enclosure      = empty($options['enclosure']) ? '"' : $options['enclosure'];
        $lines          = array();

        ini_set('auto_detect_line_endings', true);
        setlocale(LC_ALL, 'en_US.UTF-8');

        $h = fopen($file, 'r');

		$this->previewHeaders = array();

        if (is_resource($h))
        {
            for ($i = 0; $i < 5; $i++)
            {
                $data = fgetcsv($h, 0, $delimiter, $enclosure);

                if ($data !== false)
                {
                    if ($i == 0)
                    {
						$this->previewHeaders = $data;

						if ($skipHeaders)
						{
							continue;
						}
                    }
                    $lines[] = $data;
                }
                else
                {
                    break;
                }
            }
            fclose($h);
        }

        return $lines;
    }

	public function getPreviewSelectedData()
	{
		return $this->previewHeaders;
	}

    public function confirm($cid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_submissions'))
			->set($db->qn('confirmed') . ' = ' . $db->q(1))
			->where($db->qn('SubmissionId') . ' IN (' . implode(',', $db->q($cid)) . ')');

		return $db->setQuery($query)->execute();
	}
}

class RSFormProSubmissionHeader
{
	/* @var string Holds the actual value of the header */
	public $value;

	/* @var string Holds the label (displayed) value of the header */
	public $label;

	/* @var int 0 - form field, 1 - static submission header */
	public $static;

	/* @var int Checks if this header is shown in the submissions list */
	public $enabled;

	public function __construct($value, $label, $static = 0, $formId = 0)
	{
		$this->value = $value;
		$this->label = $label;
		$this->static = $static;

		if ($formId)
		{
			$this->enabled = $this->isHeaderEnabled($formId);
		}
	}

	public function __toString()
	{
		return $this->value;
	}

	protected function isHeaderEnabled($formId)
	{
		static $cache = array();

		if (!isset($cache[$formId]))
		{
			$cache[$formId] = (object) array(
				'staticHeaders' => array(),
				'headers'       => array()
			);

			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select($db->qn('ColumnName'))
				->select($db->qn('ColumnStatic'))
				->from($db->qn('#__rsform_submission_columns'))
				->where($db->qn('FormId') . ' = ' . $db->q($formId));

			if ($results = $db->setQuery($query)->loadObjectList())
			{
				foreach ($results as $result)
				{
					if ($result->ColumnStatic)
					{
						$cache[$formId]->staticHeaders[] = $result->ColumnName;
					}
					else
					{
						$cache[$formId]->headers[] = $result->ColumnName;
					}
				}
			}
		}

		$array = $this->static ? 'staticHeaders' : 'headers';

		if (empty($cache[$formId]->headers) && empty($cache[$formId]->staticHeaders))
		{
			return true;
		}

		return in_array($this->value, $cache[$formId]->{$array});
	}
}