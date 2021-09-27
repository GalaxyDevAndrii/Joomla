<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformControllerForms extends RsformController
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'changestatus');
		$this->registerTask('unpublish', 'changestatus');
		
		$this->_db = JFactory::getDbo();
	}

	public function changeLanguage()
	{
		$formId  	 = JFactory::getApplication()->input->getInt('formId');
		$tabposition = JFactory::getApplication()->input->getInt('tabposition');
		$tab		 = JFactory::getApplication()->input->getInt('tab',0);
		$tab 		 = $tabposition ? '&tab='.$tab : '';
		JFactory::getSession()->set('com_rsform.form.formId'.$formId.'.lang', JFactory::getApplication()->input->getString('Language'));

		$this->setRedirect('index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId.'&tabposition='.$tabposition.$tab);
	}

	public function manage()
	{
		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'default');
		
		parent::display();
	}
	
	public function directory() {
		$formId = JFactory::getApplication()->input->getInt('formId',0);
		$this->setRedirect('index.php?option=com_rsform&view=directory&layout=edit&formId='.$formId);
	}
	
	public function edit()
	{
		JFactory::getApplication()->input->set('view', 	'forms');
		JFactory::getApplication()->input->set('layout', 	'edit');
		
		parent::display();
	}
	
	public function menuAddScreen()
	{
		JFactory::getApplication()->input->set('view', 	'menus');
		JFactory::getApplication()->input->set('layout', 	'default');
		
		parent::display();
	}
	
	public function setMenu()
	{
		$app    	= JFactory::getApplication();
		$formId 	= $app->input->getInt('formId');
		$component 	= JComponentHelper::getComponent('com_rsform');

		$app->setUserState('com_menus.edit.item.type', 'component');
		$app->setUserState('com_menus.edit.item.link', 'index.php?option=com_rsform&view=rsform&formId='.$formId);
		$app->setUserState('com_menus.edit.item.data', array(
			'component_id' => $component->id,
			'type'		   => 'component',
			'menutype'	   => $app->input->getString('menutype'),
			'formId'	   => $formId
		));
		$this->setRedirect(JRoute::_('index.php?option=com_menus&view=item&layout=edit', false));
	}
	
	public function menuAddBackend()
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId');
		
		// No form ID provided, redirect back.
		if (!$formId)
		{
			$app->redirect('index.php?option=com_rsform&view=forms');
		}
		
		// Get the form title
		$query = $db->getQuery(true)
			->select($db->qn('FormTitle'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId') . ' = ' . $db->q($formId));
		$title = $db->setQuery($query)->loadResult();
		
		// Use a default title to prevent showing an empty menu item
		if (!strlen($title))
		{
			$title = JText::_('RSFP_FORM_DEFAULT_TITLE');
		}

		if ($component = JComponentHelper::getComponent('com_rsform'))
		{
			$componentId = $component->id;
		}
		
		$table = JTable::getInstance('Menu');
		$data = array(
			'menutype' 		=> 'main',
			'title'			=> trim($title),
			'alias'			=> JFilterOutput::stringURLSafe(trim($title)),
			'link'			=> 'index.php?option=com_rsform&view=forms&layout=show&formId=' . $formId,
			'component_id' 	=> $componentId,
			'type'			=> 'component',
			'published' 	=> 1,
			'parent_id' 	=> 1,
			'img'			=> 'class:component',
			'home'			=> 0,
			'path'			=> '',
			'params'		=> '',
			'client_id'		=> 1
		);
		
		try
		{
			$table->setLocation(1, 'last-child');
		}
		catch (InvalidArgumentException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect('index.php?option=com_rsform&view=forms');
			return false;
		}
		
		if (!$table->save($data))
		{
			$app->enqueueMessage($table->getError(), 'error');
			$app->redirect('index.php?option=com_rsform&view=forms');
			return false;
		}
		
		$table->rebuild(1);
		
		// Mark this form as added
		$object = (object) array(
			'FormId'        => $formId,
			'Backendmenu'   => 1
		);
		$db->updateObject('#__rsform_forms', $object, array('FormId'));
		
		// Redirect
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::_('RSFP_FORM_ADDED_BACKEND'));
	}
	
	/**
	 * Forms Menu Remove Backend
	 */
	public function menuRemoveBackend()
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$formId	= $app->input->getInt('formId');
		
		// No form ID provided, redirect back.
		if (!$formId)
		{
			$app->redirect('index.php?option=com_rsform&view=forms');
		}
		
		// Remove from menu
		$table = JTable::getInstance('Menu');

		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__menu'))
			->where($db->qn('client_id') . ' = ' . $db->q(1))
			->where($db->qn('link') . ' = ' . $db->q('index.php?option=com_rsform&view=forms&layout=show&formId=' . $formId));
		if ($ids = $db->setQuery($query)->loadColumn())
		{
			foreach ($ids as $id)
			{
				$table->delete($id);
				$table->rebuild(1);
			}
		}
		
		// Mark this form as removed
		$object = (object) array(
			'FormId'        => $formId,
			'Backendmenu'   => 0
		);
		$db->updateObject('#__rsform_forms', $object, array('FormId'));
		
		// Redirect
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::_('RSFP_FORM_REMOVED_BACKEND'));
	}
	
	public function getProperty($fieldData, $prop, $default=null) {
		$model = $this->getModel('forms');
		
		return $model->getProperty($fieldData, $prop, $default);
	}
	
	public function getComponentType($componentId, $formId){
		$model = $this->getModel('forms');
		
		return $model->getComponentType($componentId, $formId);
	}
	
	public function save()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		
		$model = $this->getModel('forms');
		$saved = $model->save();

		$task = $this->getTask();
		switch ($task)
		{
			case 'save':
				$link = 'index.php?option=com_rsform&view=forms';
			break;
			
			case 'apply':
				$tabposition = JFactory::getApplication()->input->getInt('tabposition', 0);
				$tab		 = JFactory::getApplication()->input->getInt('tab', 0);
				$link		 = 'index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId.'&tabposition='.$tabposition.'&tab='.$tab;
			break;
		}
		
		if (JFactory::getApplication()->input->getCmd('tmpl') == 'component') {
            $link .= '&tmpl=component';
        }

		$msg = $saved ? JText::_('RSFP_FORM_SAVED') : null;

		$this->setRedirect($link, $msg);
	}
	
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_rsform&view=forms');
	}
	
	public function delete() {
		$db = JFactory::getDbo();
		
		// Get the selected items
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		// Force array elements to be integers
		$cid = array_map('intval', $cid);
		
		$total = count($cid);
		foreach ($cid as $formId) {
			// No point in continuing if FormId = 0.
			if (!$formId) {
				$total--;
				continue;
			}
			
			// Delete forms
			$query = $db->getQuery(true);
			$query->delete('#__rsform_forms')
				  ->where($db->qn('FormId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Get all form fields
			$query = $db->getQuery(true);
			$query->select($db->qn('ComponentId'))
				  ->from('#__rsform_components')
				  ->where($db->qn('FormId').' = '.$db->q($formId));
			if ($fields = $db->setQuery($query)->loadColumn()) {
				// Delete fields
				$query = $db->getQuery(true);
				$query->delete('#__rsform_components')
					  ->where($db->qn('FormId').' = '.$db->q($formId));
				$db->setQuery($query)->execute();
				
				// Delete field properties
				$query = $db->getQuery(true);
				$query->delete('#__rsform_properties')
					  ->where($db->qn('ComponentId').' IN ('.implode(',', $fields).')');
				$db->setQuery($query)->execute();
			}

			// Delete calculations
			$query = $db->getQuery(true);
			$query->delete('#__rsform_calculations')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Get all conditions
			$query = $db->getQuery(true);
			$query->select($db->qn('id'))
				  ->from('#__rsform_conditions')
				  ->where($db->qn('form_id').' = '.$db->q($formId));
			if ($conditions = $db->setQuery($query)->loadColumn()) {
				// Delete conditions
				$query = $db->getQuery(true);
				$query->delete('#__rsform_conditions')
					  ->where($db->qn('form_id').' = '.$db->q($formId));
				$db->setQuery($query)->execute();
				
				// Delete condition details
				$query = $db->getQuery(true);
				$query->delete('#__rsform_condition_details')
					  ->where($db->qn('condition_id').' IN ('.implode(',', $conditions).')');
				$db->setQuery($query)->execute();
			}
			
			// Delete directory
			$query = $db->getQuery(true);
			$query->delete('#__rsform_directory')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			$query = $db->getQuery(true);
			$query->delete('#__rsform_directory_fields')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete extra emails
			$query = $db->getQuery(true);
			$query->delete('#__rsform_emails')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete mappings
			$query = $db->getQuery(true);
			$query->delete('#__rsform_mappings')
				  ->where($db->qn('formId').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete post to location
			$query = $db->getQuery(true);
			$query->delete('#__rsform_posts')
				  ->where($db->qn('form_id').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Delete translations
			$query = $db->getQuery(true);
			$query->delete('#__rsform_translations')
				  ->where($db->qn('form_id').' = '.$db->q($formId));
			$db->setQuery($query)->execute();
			
			// Remove from menu
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
			
			$table = JTable::getInstance('Menu', 'MenusTable');
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn('#__menu'))
				->where($db->qn('client_id') . ' = ' . $db->q(1))
				->where($db->qn('link') . ' = ' . $db->q('index.php?option=com_rsform&view=forms&layout=show&formId=' . $formId));
			if ($ids = $db->setQuery($query)->loadColumn())
			{
				foreach ($ids as $id)
				{
					$table->delete($id);
					$table->rebuild(1);
				}
			}

            require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
			RSFormProSubmissionsHelper::deleteAllSubmissions($formId);
			
			// Trigger Event - onFormDelete
			JFactory::getApplication()->triggerEvent('onRsformFormDelete', array(
				'formId' => $formId
			));
		}
		
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::sprintf('RSFP_FORMS_DELETED', $total));
	}
	
	public function changeStatus()
	{
		$task = $this->getTask();
		$db   = JFactory::getDbo();
		
		// Get the selected items
		$cid = JFactory::getApplication()->input->post->get('cid', array(), 'array');
		
		// Force array elements to be integers
		$cid = array_map('intval', $cid);
		
		$value = $task == 'publish' ? 1 : 0;
		
		$total = count($cid);
		if ($total > 0)
		{
			$formIds = implode(',', $cid);
			$db->setQuery("UPDATE #__rsform_forms SET Published = '".$value."' WHERE FormId IN (".$formIds.")");
			$db->execute();
		}
		
		$msg = $value ? JText::sprintf('RSFP_FORMS_PUBLISHED', $total) : JText::sprintf('RSFP_FORMS_UNPUBLISHED', $total);

		$this->setRedirect('index.php?option=com_rsform&view=forms', $msg);
	}
	
	public function copy()
	{
		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$model 	= $this->getModel('forms');
		
		// Get the selected items
		$cid = $app->input->get('cid', array(), 'array');
		
		// Force array elements to be integers
		$cid = array_map('intval', $cid);
		
		$total = 0;
		foreach ($cid as $formId)
		{
			if (empty($formId))
			{
				continue;
			}

			$original = JTable::getInstance('RSForm_Forms', 'Table');
			if (!$original->load($formId))
			{
				continue;
			}

			$total++;

			$data = $original->getProperties();
			$data['FormName'] .= '-copy';
			$data['FormTitle'] .= ' copy';
			$data['FormId'] = null;

			$copy = JTable::getInstance('RSForm_Forms', 'Table');
			if (!$copy->save($data))
			{
				$app->enqueueMessage($copy->getError(), 'error');
				continue;
			}
			
			$newFormId = $copy->FormId;
			
			$componentRelations = array();
			$conditionRelations = array();
			$emailRelations		= array();
			
			// copy language
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_translations'))
				->where($db->qn('reference') . ' = ' . $db->q('forms'))
				->where($db->qn('form_id') . ' = ' . $db->q($formId));
			if ($translations = $db->setQuery($query)->loadObjectList())
			{
				foreach ($translations as $translation)
				{
				    $translation->id = null;
				    $translation->form_id = $newFormId;

                    $db->insertObject('#__rsform_translations', $translation);
				}
			}
			
			// copy additional emails
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_emails'))
				->where($db->qn('formId') . ' = ' . $db->q($formId));
			if ($emails = $db->setQuery($query)->loadObjectList()) {
				foreach ($emails as $email) {
					$new_email = JTable::getInstance('RSForm_Emails', 'Table');
					$new_email->bind($email);
					$new_email->id = null;
					$new_email->formId = $newFormId;
					$new_email->store();
					
					$emailRelations[$email->id] = $new_email->id;
				}

                // Copy language
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->qn('#__rsform_translations'))
                    ->where($db->qn('form_id') . ' = ' . $db->q($formId))
                    ->where($db->qn('reference') . ' = ' . $db->q('emails'));
				if ($translatedEmails = $db->setQuery($query)->loadObjectList())
                {
                    foreach ($translatedEmails as $translatedEmail) {

                        list($oldEmailId, $property) = explode('.', $translatedEmail->reference_id, 2);

                        if (!isset($emailRelations[$oldEmailId])) {
                            continue;
                        }

                        $emailTranslation = (object) array(
                            'form_id'       => $newFormId,
                            'lang_code'     => $translatedEmail->lang_code,
                            'reference'     => 'emails',
                            'reference_id'  => $emailRelations[$oldEmailId] . '.' . $property,
                            'value'         => $translatedEmail->value
                        );

                        $db->insertObject('#__rsform_translations', $emailTranslation);
                    }
                }
			}
			
			// copy mappings
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_mappings'))
				->where($db->qn('formId') . ' = ' . $db->q($formId));
			if ($mappings = $db->setQuery($query)->loadObjectList())
			{
				foreach ($mappings as $mapping)
				{
					$new_mapping = JTable::getInstance('RSForm_Mappings', 'Table');
					$new_mapping->bind($mapping);
					$new_mapping->id = null;
					$new_mapping->formId = $newFormId;
					$new_mapping->store();
				}
			}
			
			// copy post to location
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_posts'))
				->where($db->qn('form_id') . ' = ' . $db->q($formId));
			if ($post = $db->setQuery($query)->loadObject())
			{
				$post->form_id = $newFormId;

				$db->insertObject('#__rsform_posts', $post);
			}
			
			// copy calculations
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_calculations'))
				->where($db->qn('formId') . ' = ' . $db->q($formId));
			if ($calculations = $db->setQuery($query)->loadObjectList())
			{
				foreach ($calculations as $calculation)
				{
					unset($calculation->id);
					$calculation->formId = $newFormId;

					$db->insertObject('#__rsform_calculations', $calculation);
				}
			}

			$query = $db->getQuery(true)
				->select($db->qn('ComponentId'))
				->from($db->qn('#__rsform_components'))
				->where($db->qn('FormId') . ' = ' . $db->q($formId))
				->order($db->qn('Order'));

			if ($components = $db->setQuery($query)->loadColumn())
			{
				foreach ($components as $r)
				{
					try
					{
						$componentRelations[$r] = $model->copyComponent($r, $newFormId);
					}
					catch (Exception $e)
					{
						$app->enqueueMessage($e->getMessage(), 'warning');

						continue;
					}
				}
			}
			
			// Handle dynamic properties
			if ($componentRelations)
			{
				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__rsform_properties'))
					->where($db->qn('ComponentId') . ' IN (' . implode(',', $db->q($componentRelations)) . ')')
					->where($db->qn('PropertyName') . ' IN (' . implode(',', $db->q(array('EMAILATTACH', 'VALIDATIONCALENDAR'))) . ')');
				if ($properties = $db->setQuery($query)->loadObjectList())
				{
					foreach ($properties as $property)
					{
						if ($property->PropertyName == 'EMAILATTACH' && $property->PropertyValue)
						{
							$values 	= explode(',', $property->PropertyValue);
							$newValues 	= array();

							foreach ($values as $value)
							{
								if (isset($emailRelations[$value]))
								{
									$newValues[] = $emailRelations[$value];
								}
								elseif (in_array($value, array('adminemail', 'useremail')))
								{
									$newValues[] = $value;
								}
							}

							$property->PropertyValue = implode(',', $newValues);
						}

						if ($property->PropertyName == 'VALIDATIONCALENDAR' && $property->PropertyValue)
						{
							list($type, $oldCalendarId) = explode(' ', $property->PropertyValue, 2);
							if (isset($componentRelations[$oldCalendarId]))
							{
								$property->PropertyValue = $type.' '.$componentRelations[$oldCalendarId];
							}
						}

						$object = (object) array(
							'PropertyValue' => $property->PropertyValue,
							'PropertyId' => $property->PropertyId,
						);

						$db->updateObject('#__rsform_properties', $object, array('PropertyId'));
					}
				}
			}
			
			// copy conditions
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_conditions'))
				->where($db->qn('form_id') . ' = ' . $db->q($formId));
			if ($conditions = $db->setQuery($query)->loadObjectList())
			{
				require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/conditions.php';

				foreach ($conditions as $condition)
				{
					$component_ids = RSFormProConditions::parseComponentIds($condition->component_id);
					$json_ids = array();
					foreach ($component_ids as $component_id)
					{
						if (isset($componentRelations[$component_id]))
						{
							$json_ids[] = $componentRelations[$component_id];
						}
					}

					$new_condition = JTable::getInstance('RSForm_Conditions', 'Table');
					$new_condition->save(array(
						'form_id' 		=> $newFormId,
						'action'  		=> $condition->action,
						'block'			=> $condition->block,
						'component_id'	=> $json_ids,
						'condition'		=> $condition->condition,
						'lang_code'		=> $condition->lang_code,
					));
					
					$conditionRelations[$condition->id] = $new_condition->id;
				}

				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__rsform_condition_details'))
					->where($db->qn('condition_id') . ' IN (' . implode(',', $db->q(array_keys($conditionRelations))) . ')');
				if ($details = $db->setQuery($query)->loadObjectList())
				{
					foreach ($details as $detail)
					{
						$new_detail = JTable::getInstance('RSForm_Condition_Details', 'Table');
						$new_detail->bind($detail);
						$new_detail->id = null;
						$new_detail->condition_id = $conditionRelations[$detail->condition_id];
						$new_detail->component_id = $componentRelations[$detail->component_id];
						$new_detail->store();
					}
				}
			}

			// Copy directory
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__rsform_directory'))
				->where($db->qn('formId') . ' = ' . $db->q($formId));
			if ($directory = $db->setQuery($query)->loadObject())
			{
				$table = JTable::getInstance('RSForm_Directory', 'Table');

				$table->bind($directory);
				$table->formId = $newFormId;
				if ($table->check())
				{
					$table->store();

					// Copy directory fields
					$query->clear()
						->select('*')
						->from($db->qn('#__rsform_directory_fields'))
						->where($db->qn('formId') . ' = ' . $db->q($formId));
					if ($dirFields = $db->setQuery($query)->loadObjectList())
					{
						foreach ($dirFields as $dirField)
						{
							$dirField->formId = $newFormId;

							// Negative Field IDs are special fields from plugins, we keep them intact.
							// Only positive IDs are mapped to new field IDs.
							if ($dirField->componentId > 0)
							{
								// Field does not exist, skip this
								if (!isset($componentRelations[$dirField->componentId]))
								{
									continue;
								}

								$dirField->componentId = $componentRelations[$dirField->componentId];
							}

							$db->insertObject('#__rsform_directory_fields', $dirField);
						}
					}
				}
			}

			// Rebuild Grid Layout
            if (!empty($copy->GridLayout))
            {
                $data   = json_decode($copy->GridLayout, true);
                $rows 	= array();
                $hidden	= array();

                // If decoding is successful, we should have $rows and $hidden
                if (is_array($data) && isset($data[0], $data[1]))
                {
                    $rows 	= $data[0];
                    $hidden = $data[1];
                }

                if ($rows)
                {
                    foreach ($rows as $row_index => &$row)
                    {
                        foreach ($row['columns'] as $column_index => $fields)
                        {
                            foreach ($fields as $position => $id)
                            {
                                if (isset($componentRelations[$id]))
                                {
                                    $row['columns'][$column_index][$position] = $componentRelations[$id];
                                }
                                else
                                {
                                    // Field doesn't exist, remove it from grid
                                    unset($row['columns'][$column_index][$position]);
                                }
                            }
                        }
                    }
					unset($row);
                }

                if ($hidden)
                {
                    foreach ($hidden as $hidden_index => $id)
                    {
                        if (isset($componentRelations[$id]))
                        {
                            $hidden[$hidden_index] = $componentRelations[$id];
                        }
                        else
                        {
                            // Field doesn't exist, remove it from grid
                            unset($hidden[$hidden_index]);
                        }
                    }
                }

                $query = $db->getQuery(true);
                $query->update('#__rsform_forms')
                    ->set($db->qn('GridLayout') .'='. $db->q(json_encode(array($rows, $hidden))))
                    ->where($db->qn('FormId') .'='. $db->q($copy->FormId));
                $db->setQuery($query)->execute();
            }
			
			//Trigger Event - onFormCopy
			$app->triggerEvent('onRsformBackendFormCopy', array(
				array(
					'formId' => $formId,
					'newFormId' => $newFormId,
					'components' => $components,
					'componentRelations' => $componentRelations
				)
			));
		}
		
		$this->setRedirect('index.php?option=com_rsform&view=forms', JText::sprintf('RSFP_FORMS_COPIED', $total));
	}
	
	public function changeAutoGenerateLayout()
	{
		$app			= JFactory::getApplication();
		$formId 		= $app->input->getInt('formId');
		$status 		= $app->input->getInt('status');
		$formLayoutName = $app->input->getCmd('formLayoutName');
		$db 			= JFactory::getDbo();

		$query = $db->getQuery(true)
			->update($db->qn('#__rsform_forms'))
			->set($db->qn('FormLayoutAutogenerate').'='.$db->q($status))
			->set($db->qn('FormLayoutName').'='.$db->q($formLayoutName))
			->where($db->qn('FormId').'='.$db->q($formId));

		$db->setQuery($query)
			->execute();

		echo json_encode(array(
			'status' => true
		));

		$app->close();
	}

    public function changeFormLayoutFlow()
    {
        $app			= JFactory::getApplication();
        $formId 		= $app->input->getInt('formId');
        $status 		= $app->input->getInt('status');
        $db 			= JFactory::getDbo();

        $query = $db->getQuery(true)
            ->update($db->qn('#__rsform_forms'))
            ->set($db->qn('FormLayoutFlow').'='.$db->q($status))
            ->where($db->qn('FormId').'='.$db->q($formId));

        $db->setQuery($query)
            ->execute();

        echo json_encode(array(
            'status' => true
        ));

        $app->close();
    }
	
	public function saveGridLayout()
	{
		$app	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$query  = $db->getQuery(true);
		$formId = $app->input->getInt('formId');
		$layout = $app->input->getString('GridLayout');
		$model	= $this->getModel('forms');
		
		$data = json_decode($layout, true);
		
		if (is_array($data) && isset($data[0], $data[1]))
		{
			$rows 	= $data[0];
			$hidden = $data[1];
			
			$flat = array();
			foreach ($rows as $row)
			{
				foreach ($row['columns'] as $column => $fields)
				{
					foreach ($fields as $field)
					{
						$flat[] = $field;
					}
				}
			}
			
			$flat = array_merge($flat, $hidden);
			
			foreach ($flat as $position => $id)
			{
				$query->update($db->qn('#__rsform_components'))
					->set($db->qn('Order').'='.$db->q($position))
					->where($db->qn('ComponentId').'='.$db->q($id));

				$db->setQuery($query)
					->execute();
				
				$query->clear();
			}
		}

		$query->update($db->qn('#__rsform_forms'))
			->set($db->qn('GridLayout').'='.$db->q($layout))
			->where($db->qn('FormId').'='.$db->q($formId));

		$db->setQuery($query)
			->execute();

		// Auto generate layout
		$model->getForm();
		if ($model->_form->FormLayoutAutogenerate)
		{
			$model->autoGenerateLayout();
		}
		
		echo $model->_form->FormLayout;

		$app->close();
	}
}