<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProRestoreForm
{
	// JDatabase instance
	protected $db;
	
	// Holds the form's structure as a JTable object
	protected $form;
	
	// Holds an array of the XML data.
	protected $xml;
	
	// Holds an array in the form of field ID => field name.
	protected $fields;
	
	// Holds the setting for keeping the form's ids from the backup
	protected $keepId;
	
	// Meta data information
	protected $metaData;
	
	public function __construct($options = array())
	{
		$this->keepId		= !empty($options['keepId']);
		$path 	  			= &$options['path'];
		$this->db 			= JFactory::getDbo();
		
		// Check if the form's xml exists
		if (!file_exists($path))
		{
			throw new Exception(sprintf('The file %s does not exist!', $path));
		}
		
		if (!is_readable($path))
		{
			throw new Exception(sprintf('File %s is not readable!', $path));
		}
		
		// Attempt to load the XML data
		libxml_use_internal_errors(true);
		
		if (class_exists('DOMDocument'))
		{
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->strictErrorChecking = false;
			$dom->validateOnParse = false;
			$dom->recover = true;
			$dom->loadXML(file_get_contents($options['path']));
			
			$this->xml = simplexml_import_dom($dom);
		}
		else
		{
			$this->xml = simplexml_load_file($options['path']);
		}
		
		if ($this->xml === false)
		{
			$errors = array();
			foreach (libxml_get_errors() as $error)
			{
				$errors[] = 'Message: '.$error->message.'; Line: '.$error->line.'; Column: '.$error->column;
			}
			throw new Exception(sprintf('Error while parsing XML: %s<br/>', implode('<br />', $errors)));
		}
		
		$this->metaData = $options['metaData'];
	}
	
	public function restore()
	{
		$this->restoreStructure();
		$this->restoreFields();
		$this->restoreCalculations();
		$this->restorePost();
		$this->restoreConditions();
		$this->restoreDirectory();
		$this->restoreEmails();
		$this->restoreMappings();
		$this->rebuildCalendarsValidationRules();
		$this->rebuildGridLayout();
		
		// Allow plugins to restore their own data from the backup.
		JFactory::getApplication()->triggerEvent('onRsformFormRestore', array($this->form, $this->xml, $this->fields));
	}
	
	public function getFormId()
	{
		return $this->form->FormId;
	}
	
	// Form structure
	// ==============
	
	protected function restoreStructure()
	{
		// Restore the form structure #__rsform_forms
		$data 			= array();
		$oldFormId 		= false;
		foreach ($this->xml->structure->children() as $property => $value)
		{
			// Skip translations for now
            // Skip ThemeParams, no longer exists
			if ($property == 'translations' || $property == 'ThemeParams')
			{
				continue;
			}
			
			if ($property == 'FormId')
			{
				if ($this->keepId)
				{
					$oldFormId = (string) $value;
				}

				continue;
			}
			
			$data[$property] = (string) $value;
		}
		
		$this->form = JTable::getInstance('RSForm_Forms', 'Table');
		
		// Responsive layout needs its own CSS to be loaded, make sure old forms still load it when restored.
		if (version_compare($this->metaData['version'], '1.51.12', '<') && $data['FormLayoutName'] == 'responsive')
		{
			$data['LoadFormLayoutFramework'] = 1;
		}
		
		if (!$this->form->save($data))
		{
			throw new Exception(sprintf('Form %s could not be saved!', $this->form->FormTitle));
		}

		if (!empty($oldFormId) && $oldFormId != $this->form->FormId)
		{
			// Is it free?
			$query = $this->db->getQuery(true)
				->select($this->db->qn('FormId'))
				->from($this->db->qn('#__rsform_forms'))
				->where($this->db->qn('FormId') . ' = ' . $this->db->q($oldFormId));

			// Requested form ID doesn't exist, update current form
			if (!$this->db->setQuery($query)->loadResult())
			{
				$query->clear()
					->update($this->db->qn('#__rsform_forms'))
					->set($this->db->qn('FormId') . ' = ' . $this->db->q($oldFormId))
					->where($this->db->qn('FormId') . ' = ' . $this->db->q($this->form->FormId));

				$this->db->setQuery($query)->execute();

				$this->form->FormId = $oldFormId;
			}
		}
		
		// Restore form translations
		if ($this->xml->structure->translations)
		{
			foreach ($this->xml->structure->translations->children() as $lang_code => $properties)
			{
				foreach ($properties->children() as $property => $value)
				{
					$data = array(
						'form_id' 		=> $this->form->FormId,
						'lang_code' 	=> (string) $lang_code,
						'reference' 	=> 'forms',
						'reference_id' 	=> (string) $property,
						'value' 		=> (string) $value
					);

					$data = (object) $data;

					$this->db->insertObject('#__rsform_translations', $data);
				}
			}
		}
	}
	
	// Fields
	// ======
	
	protected function restoreFields()
	{
		// Restore the form fields #__rsform_components
		if (isset($this->xml->fields))
		{
			foreach ($this->xml->fields->children() as $field)
			{
				$componentTypeId = (string) $field->ComponentTypeId;
				// change fieldType if needed
				$changedField = '';
				if ($componentTypeId == '12')
				{
					$componentTypeId = '13';
					$changedField = 'imageButton';
				}

				$data = array(
					'FormId'            => $this->form->FormId,
					'ComponentTypeId'   => $componentTypeId,
					'Order'             => (string) $field->Order,
					'Published'         => (string) $field->Published
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_components', $data, 'ComponentId');

				$componentId = $data->ComponentId;
				
				// we use the switch statement for further field types changes - at the moment we only need it for the image button
				$referenceProperties = array();
				if (!empty($changedField))
				{
					switch ($changedField)
					{
						case 'imageButton':
							$query = $this->db->getQuery(true);
							$query->select($this->db->qn('FieldName'))
								->from($this->db->qn('#__rsform_component_type_fields'))
								->where($this->db->qn('ComponentTypeId') . ' = ' . $this->db->q(13));
							$this->db->setQuery($query);
							$referenceProperties = $this->db->loadColumn();
						break;
					}
				}

				if (isset($field->properties))
				{
					$newProperties = array();
					foreach ($field->properties->children() as $property => $value)
					{
						$property = (string) $property;
						$value = (string) $value;
						
						if (!isset($newProperties[$componentId]))
						{
							$newProperties[$componentId] = array();
						}
						
						if (!empty($changedField))
						{
							switch ($changedField)
							{
								case 'imageButton':
									if (in_array($property, $referenceProperties))
									{
										if ($property == 'ADDITIONALATTRIBUTES' && isset($newProperties[$componentId]['ADDITIONALATTRIBUTES']))
										{
											$newProperties[$componentId]['ADDITIONALATTRIBUTES'] = $value."\r\n".$newProperties[$componentId]['ADDITIONALATTRIBUTES'];	
										}
										else
										{
											$newProperties[$componentId][$property] = $value;
										}
									}
									elseif ($property == 'IMAGEBUTTON' && !empty($value))
									{
										$additional = 'type="image"'."\r\n".'src="'.$value.'"';
										if (isset($newProperties[$componentId]['ADDITIONALATTRIBUTES']) && !empty($newProperties[$componentId]['ADDITIONALATTRIBUTES']))
										{
											$additional = $newProperties[$componentId]['ADDITIONALATTRIBUTES']."\r\n".$additional;
										}
										$newProperties[$componentId]['ADDITIONALATTRIBUTES'] = $additional;
									}
								break;
							}
						}
						else
						{
							$newProperties[$componentId][$property] = $value;
						}
					}
					
					// add the submit button extra properties
					if (!empty($changedField))
					{
						switch ($changedField)
						{
							case 'imageButton':
								foreach ($newProperties as $CompId => $property)
								{
									foreach ($referenceProperties as $referenceProperty)
									{
										$value = '';

										switch ($referenceProperty)
										{
											case 'DISPLAYPROGRESS':
												$value = 'NO';
											break;
											case 'BUTTONTYPE':
												$value = 'TYPEINPUT';
											break;
											case 'DISPLAYPROGRESSMSG':
												$value = '<div>'."\r\n".' <p><em>Page <strong>{page}</strong> of {total}</em></p>'."\r\n".' <div class="rsformProgressContainer">'."\r\n".'  <div class="rsformProgressBar" style="width: {percent}%;"></div>'."\r\n".' </div>'."\r\n".'</div>';
											break;
										}
										
										if (!empty($value))
										{
											$newProperties[$CompId][$referenceProperty] = $value;
										}
									}
								}
							break;
						}
					}

					foreach ($newProperties as $CompId => $property)
					{
						foreach ($property as $propertyName => $propertyValue)
						{
							$data = array(
								'ComponentId'   => $CompId,
								'PropertyName'  => $propertyName,
								'PropertyValue' => $propertyValue
							);

							$data = (object) $data;

							$this->db->insertObject('#__rsform_properties', $data);

							// store the ComponentId
							if ((string) $propertyName == 'NAME')
							{
								$this->fields[(string) $propertyValue] = $CompId;
							}
						}
					}
				}
				if (isset($field->translations))
				{
					foreach ($field->translations->children() as $lang_code => $properties)
					{
						foreach ($properties->children() as $property => $value)
						{
							$data = array(
								'form_id'       => $this->form->FormId,
								'lang_code'     => (string) $lang_code,
								'reference'     => 'properties',
								'reference_id'  => $componentId . '.' . (string) $property,
								'value'         => (string) $value
							);

							$data = (object) $data;

							$this->db->insertObject('#__rsform_translations', $data);
						}
					}
				}
			}
		}
	}
	
	protected function rebuildCalendarsValidationRules()
	{
		$db 	= &$this->db;
		$query 	= $db->getQuery(true);
		
		$query->clear()
			  ->select('c.ComponentId')
			  ->select('p.PropertyName')
			  ->select('p.PropertyValue')
			  ->from($db->qn('#__rsform_components', 'c'))
			  ->join('LEFT', $db->qn('#__rsform_properties', 'p') . ' ON (' . $db->qn('c.ComponentId') . ' = ' . $db->qn('p.ComponentId') . ')')
			  ->where($db->qn('c.FormId').' = '.$db->q($this->form->FormId))
			  ->where('('.$db->qn('p.PropertyName').' = '.$db->q('NAME').' OR '.$db->qn('p.PropertyName').' = '.$db->q('VALIDATIONCALENDAR').')');
		$db->setQuery($query);
		$formCalendarsComponents = $db->loadObjectList();
		
		$componentsNames = array();
		$componentsValidations = array();
		
		foreach ($formCalendarsComponents as $calendar)
		{
			if ($calendar->PropertyName == 'NAME')
			{
				$componentsNames[$calendar->PropertyValue] = $calendar->ComponentId;
			}
			
			if ($calendar->PropertyName == 'VALIDATIONCALENDAR')
			{
				$componentsValidations[$calendar->ComponentId] = $calendar->PropertyValue;
			}
		}
		
		foreach ($componentsValidations as $componentId => $value)
		{
			if (!empty($value))
			{
				$ruleParts = explode(' ', $value, 2);
				$otherComponentName = $ruleParts[1];
				
				$idOtherComponent = $componentsNames[$otherComponentName];
				$ruleParts[1] = $idOtherComponent; // replace the name with the id

				$data = array(
					'ComponentId'   => $componentId,
					'PropertyName'  => 'VALIDATIONCALENDAR',
					'PropertyValue' => implode(' ', $ruleParts)
				);

				$data = (object) $data;

				$this->db->updateObject('#__rsform_properties', $data, array('ComponentId', 'PropertyName'));
			}
		}
	}
	
	// Calculations
	// ============
	
	protected function restoreCalculations()
	{
		// Restore Calculations #__rsform_calculations
		if (isset($this->xml->calculations))
		{
			foreach ($this->xml->calculations->children() as $calculation)
			{
				$data = array(
					'formId'        => $this->form->FormId,
					'total'         => (string) $calculation->total,
					'expression'    => (string) $calculation->expression,
					'ordering'      => (string) $calculation->ordering
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_calculations', $data);
			}
		}
	}
	
	// Post
	// ====
	
	protected function restorePost()
	{
		// Restore Post to Location #__rsform_posts
		if (isset($this->xml->post))
		{
			foreach ($this->xml->post as $post)
			{
				// Some older versions might have left some data here due to a bug, must delete it first.
				$query = $this->db->getQuery(true);
				$query->delete('#__rsform_posts')
					  ->where($this->db->qn('form_id').' = '.$this->db->q($this->form->FormId));
				$this->db->setQuery($query)->execute();

				$data = array(
					'form_id'   => $this->form->FormId,
					'enabled'   => (string) $post->enabled,
					'method'    => (string) $post->method,
					'fields'    => (string) $post->fields,
					'headers'   => (string) $post->headers,
					'silent'    => (string) $post->silent,
					'url'       => (string) $post->url
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_posts', $data);
			}
		}
	}
	
	// Conditions
	// ==========
	
	protected function restoreConditions()
	{
		// Restore conditions #__rsform_conditions & #__rsform_condition_details
		if (isset($this->xml->conditions))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/conditions.php';

			foreach ($this->xml->conditions->children() as $condition)
			{
				$component_ids = (string) $condition->component_id;

				$tmp_ids = json_decode($component_ids);
				if (is_array($tmp_ids))
				{
					$component_ids = $tmp_ids;
				}
				else
				{
					$component_ids = array($component_ids);
				}

				$json_ids = array();
				foreach ($component_ids as $component_id)
				{
					if (isset($this->fields[$component_id]))
					{
						$json_ids[] = $this->fields[$component_id];
					}
				}
				$json_ids = json_encode($json_ids);

				$data = array(
					'form_id'       => $this->form->FormId,
					'action'        => (string) $condition->action,
					'block'         => (string) $condition->block,
					'component_id'  => $json_ids,
					'condition'     => (string) $condition->condition,
					'lang_code'     => (string) $condition->lang_code,
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_conditions', $data, 'id');

				$conditionId = $data->id;
				
				if (isset($condition->details))
				{
					foreach ($condition->details->children() as $detail)
					{
						if (!isset($this->fields[(string) $detail->component_id]))
						{
							continue;
						}

						$data = array(
							'condition_id'  => $conditionId,
							'component_id'  => $this->fields[(string) $detail->component_id],
							'operator'      => (string) $detail->operator,
							'value'         => (string) $detail->value
						);

						$data = (object) $data;

						$this->db->insertObject('#__rsform_condition_details', $data);
					}
				}
			}
		}
	}
	
	// Directory
	// =========
	
	protected function restoreDirectory()
	{
		// Restore directory #__rsform_directory & #__rsform_directory_fields
		if (isset($this->xml->directory))
		{
			foreach ($this->xml->directory as $directory)
			{
				$data = array(
					'formId'                    => $this->form->FormId,
					'filename'                  => (string) $directory->filename,
					'csvfilename'               => (string) $directory->csvfilename,
					'enablepdf'                 => (string) $directory->enablepdf,
					'enablecsv'                 => (string) $directory->enablecsv,
					'HideEmptyValues'           => (string) $directory->HideEmptyValues,
					'ShowGoogleMap'             => (string) $directory->ShowGoogleMap,
					'ViewLayout'                => (string) $directory->ViewLayout,
					'ViewLayoutName'            => (string) $directory->ViewLayoutName,
					'ViewLayoutAutogenerate'    => (string) $directory->ViewLayoutAutogenerate,
					'CSS'                       => (string) $directory->CSS,
					'JS'                        => (string) $directory->JS,
					'ListScript'                => (string) $directory->ListScript,
					'DetailsScript'             => (string) $directory->DetailsScript,
					'EmailsScript'              => (string) $directory->EmailsScript,
					'EmailsCreatedScript'       => (string) $directory->EmailsCreatedScript,
					'groups'                    => (string) $directory->groups,
					'DeletionGroups'            => (string) $directory->DeletionGroups
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_directory', $data);
				
				if (isset($directory->fields))
				{
					foreach ($directory->fields->children() as $field)
					{
						// check for the component ID
						$componentId = (string) $field->componentId;
						
						if (isset($this->fields[$componentId]))
						{
							$componentId = $this->fields[$componentId];
						}
						
						$componentId = (int) $componentId;
						
						if (is_int($componentId) && $componentId !== 0)
						{
							$data = array(
								'formId'        => $this->form->FormId,
								'componentId'   => $componentId,
								'viewable'      => (string) $field->viewable,
								'searchable'    => (string) $field->searchable,
								'editable'      => (string) $field->editable,
								'indetails'     => (string) $field->indetails,
								'incsv'         => (string) $field->incsv,
								'ordering'      => (string) $field->ordering,
							);

							$data = (object) $data;

							$this->db->insertObject('#__rsform_directory_fields', $data);
						}
					}
				}
			}
		}
	}
	
	// Emails
	// ======
	
	protected function restoreEmails()
	{
		// Restore Emails #__rsform_emails
		if (isset($this->xml->emails))
		{
			foreach ($this->xml->emails->children() as $email)
			{
				$data = array(
					'formId'        => $this->form->FormId,
					'type'          => (string) $email->type,
					'from'          => (string) $email->from,
					'fromname'      => (string) $email->fromname,
					'replyto'       => (string) $email->replyto,
					'replytoname'   => (string) $email->replytoname,
					'to'            => (string) $email->to,
					'cc'            => (string) $email->cc,
					'bcc'           => (string) $email->bcc,
					'subject'       => (string) $email->subject,
					'mode'          => (string) $email->mode,
					'message'       => (string) $email->message
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_emails', $data, 'id');

				$emailId = $data->id;
				
				if (isset($email->translations))
				{
					foreach ($email->translations->children() as $lang_code => $properties)
					{
						foreach ($properties->children() as $property => $value)
						{
							$data = array(
								'form_id' 		=> $this->form->FormId,
								'lang_code' 	=> (string) $lang_code,
								'reference' 	=> 'emails',
								'reference_id' 	=> $emailId . '.' . (string) $property,
								'value' 		=> (string) $value
							);

							$data = (object) $data;

							$this->db->insertObject('#__rsform_translations', $data);
						}
					}
				}
			}
		}
	}
	
	// Mappings
	// ========
	
	protected function restoreMappings()
	{
		// Restore Mappings #__rsform_mappings
		if (isset($this->xml->mappings))
		{
			$defaultDriver 	= JFactory::getConfig()->get('dbtype');
			$prefix			= JFactory::getConfig()->get('dbprefix');

			foreach ($this->xml->mappings->children() as $mapping)
			{
				$driver = (string) $mapping->driver;
				if (empty($driver))
				{
					$driver = $defaultDriver;
				}

				$table = (string) $mapping->table;
				if (strpos($table, '#__') === 0)
				{
					$table = substr_replace($table, $prefix, 0, strlen('#__'));
				}

				$data = array(
					'formId'        => $this->form->FormId,
					'connection'    => (string) $mapping->connection,
					'host'          => (string) $mapping->host,
					'port'          => (string) $mapping->port,
					'driver'        => $driver,
					'username'      => (string) $mapping->username,
					'password'      => (string) $mapping->password,
					'database'      => (string) $mapping->database,
					'method'        => (string) $mapping->method,
					'table'         => $table,
					'data'          => (string) $mapping->data,
					'wheredata'     => (string) $mapping->wheredata,
					'extra'         => (string) $mapping->extra,
					'andor'         => (string) $mapping->andor,
					'ordering'      => (string) $mapping->ordering
				);

				$data = (object) $data;

				$this->db->insertObject('#__rsform_mappings', $data);
			}
		}
	}

	// Grid Layout
    // ===========

	protected function rebuildGridLayout()
    {
        if (empty($this->form->GridLayout))
        {
            return false;
        }

        $data   = json_decode($this->form->GridLayout, true);
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
                        if (isset($this->fields[$id]))
                        {
                            $row['columns'][$column_index][$position] = $this->fields[$id];
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
                if (isset($this->fields[$id]))
                {
                    $hidden[$hidden_index] = $this->fields[$id];
                }
                else
                {
                    // Field doesn't exist, remove it from grid
                    unset($hidden[$hidden_index]);
                }
            }
        }

        $data = array(
        	'FormId'        => $this->form->FormId,
	        'GridLayout'    => json_encode(array($rows, $hidden))
        );

        $data = (object) $data;

        $this->db->updateObject('#__rsform_forms', $data, array('FormId'));
    }
}