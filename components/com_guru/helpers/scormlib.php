<?php

defined('_JEXEC') or die('Restricted access');

include_once(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."scormdbhelper.php");

class guruscormlib
{

	function __construct()
	{
		//$this->tjlmsHelper	=	new TjlmsHelper();
		$this->gurudbhelper	=	new gurudbhelper();
	}

	function scorm_parse($scorm, $scormFoldername)
	{
		$db = JFactory::getDbo();
		$lesson	= $scorm->lesson_id;

		$manifestfile	=   JPATH_SITE . '/media/scorm/lessons/'.$lesson.'/scorm/imsmanifest.xml';

		if (JFolder::exists(JPATH_SITE . '/media/scorm/lessons/'.$lesson.'/'. $scormFoldername))
		{
			$manifestfile	=   JPATH_SITE . '/media/scorm/lessons/'.$lesson.'/'. $scormFoldername .'/imsmanifest.xml';
		}

		$result	= $this->scorm_parse_scorm($manifestfile,$scorm);

		if($result){
			$db->UpdateObject( '#__guru_scorm', $result, 'id' );
		}
		else{
			return false;
		}
	}


	function scorm_get_resources($blocks) {
		$resources = array();
		foreach ($blocks as $block) {
			if ($block['name'] == 'RESOURCES' && isset($block['children'])) {
				foreach ($block['children'] as $resource) {
					if ($resource['name'] == 'RESOURCE') {
						$resources[$resource['attrs']['IDENTIFIER']] = $resource['attrs'];
					}
				}
			}
		}
		return $resources;
	}

	function scorm_get_manifest($blocks, $scoes) {
		global $OUTPUT;
		static $parents = array();
		static $resources;

		static $manifest;
		static $organization;

		$manifestresourcesnotfound = array();
		if (count($blocks) > 0) {
			foreach ($blocks as $block) {
				switch ($block['name']) {
					case 'METADATA':
						if (isset($block['children'])) {
							foreach ($block['children'] as $metadata) {
								if ($metadata['name'] == 'SCHEMAVERSION') {
									if (empty($scoes->version)) {
										if (isset($metadata['tagData']) && (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/", $metadata['tagData'], $matches))) {
											$scoes->version = 'SCORM_'.$matches[count($matches)-1];
										} else {
											if (isset($metadata['tagData']) && (preg_match("/^2004 (3rd|4th) Edition$/", $metadata['tagData'], $matches))) {
												$scoes->version = 'SCORM_1.3';
											} else {
												$scoes->version = 'SCORM_1.2';
											}
										}
									}
								}
							}
						}
					break;
					case 'MANIFEST':
						$manifest = $block['attrs']['IDENTIFIER'];
						$organization = '';
						$resources = array();
						$resources = $this->scorm_get_resources($block['children']);
						$scoes = $this->scorm_get_manifest($block['children'], $scoes);
						if (empty($scoes->elements) || count($scoes->elements) <= 0) {
							foreach ($resources as $item => $resource) {
								if (!empty($resource['HREF'])) {
									$sco = new stdClass();
									$sco->identifier = $item;
									$sco->title = $item;
									$sco->parent = '/';
									$sco->launch = $resource['HREF'];
									$sco->scormtype = $resource['ADLCP:SCORMTYPE'];
									$scoes->elements[$manifest][$organization][$item] = $sco;
								}
							}
						}
					break;
					case 'ORGANIZATIONS':
						if (!isset($scoes->defaultorg) && isset($block['attrs']['DEFAULT'])) {
							$scoes->defaultorg = $block['attrs']['DEFAULT'];
						}
						if (!empty($block['children'])) {
							$scoes = $this->scorm_get_manifest($block['children'], $scoes);
						}
					break;
					case 'ORGANIZATION':
						$identifier = $block['attrs']['IDENTIFIER'];
						$organization = '';
						$scoes->elements[$manifest][$organization][$identifier] = new stdClass();
						$scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
						$scoes->elements[$manifest][$organization][$identifier]->parent = '/';
						$scoes->elements[$manifest][$organization][$identifier]->launch = '';
						$scoes->elements[$manifest][$organization][$identifier]->scormtype = '';

						$parents = array();
						$parent = new stdClass();
						$parent->identifier = $identifier;
						$parent->organization = $organization;
						array_push($parents, $parent);
						$organization = $identifier;

						if (!empty($block['children'])) {
							$scoes = $this->scorm_get_manifest($block['children'], $scoes);
						}

						array_pop($parents);
					break;
					case 'ITEM':
						$parent = array_pop($parents);
						array_push($parents, $parent);

						$identifier = $block['attrs']['IDENTIFIER'];
						$scoes->elements[$manifest][$organization][$identifier] = new stdClass();
						$scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
						$scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
						if (!isset($block['attrs']['ISVISIBLE'])) {
							$block['attrs']['ISVISIBLE'] = 'true';
						}
						$scoes->elements[$manifest][$organization][$identifier]->isvisible = $block['attrs']['ISVISIBLE'];
						if (!isset($block['attrs']['PARAMETERS'])) {
							$block['attrs']['PARAMETERS'] = '';
						}
						$scoes->elements[$manifest][$organization][$identifier]->parameters = $block['attrs']['PARAMETERS'];
						if (!isset($block['attrs']['IDENTIFIERREF'])) {
							$scoes->elements[$manifest][$organization][$identifier]->launch = '';
							$scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
						} else {
							$idref = $block['attrs']['IDENTIFIERREF'];
							$base = '';
							if (isset($resources[$idref]['XML:BASE'])) {
								$base = $resources[$idref]['XML:BASE'];
							}
							if (!isset($resources[$idref])) {
								$manifestresourcesnotfound[] = $idref;
								$scoes->elements[$manifest][$organization][$identifier]->launch = '';
							} else {
								$scoes->elements[$manifest][$organization][$identifier]->launch = $base.$resources[$idref]['HREF'];
								if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
									$resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
								}
								$scoes->elements[$manifest][$organization][$identifier]->scormtype = $resources[$idref]['ADLCP:SCORMTYPE'];
							}
						}

						$parent = new stdClass();
						$parent->identifier = $identifier;
						$parent->organization = $organization;
						array_push($parents, $parent);

						if (!empty($block['children'])) {
							$scoes = $this->scorm_get_manifest($block['children'], $scoes);
						}

						array_pop($parents);
					break;
					case 'TITLE':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!isset($block['tagData'])) {
							$block['tagData'] = '';
						}
						$scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = $block['tagData'];
					break;
					case 'ADLCP:PREREQUISITES':
						if ($block['attrs']['TYPE'] == 'aicc_script') {
							$parent = array_pop($parents);
							array_push($parents, $parent);
							if (!isset($block['tagData'])) {
								$block['tagData'] = '';
							}
							$scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = $block['tagData'];
						}
					break;
					case 'ADLCP:MAXTIMEALLOWED':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!isset($block['tagData'])) {
							$block['tagData'] = '';
						}
						$scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = $block['tagData'];
					break;
					case 'ADLCP:TIMELIMITACTION':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!isset($block['tagData'])) {
							$block['tagData'] = '';
						}
						$scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = $block['tagData'];
					break;
					case 'ADLCP:DATAFROMLMS':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!isset($block['tagData'])) {
							$block['tagData'] = '';
						}
						$scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = $block['tagData'];
					break;
					case 'ADLCP:MASTERYSCORE':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!isset($block['tagData'])) {
							$block['tagData'] = '';
						}
						$scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = $block['tagData'];
					break;
					case 'ADLCP:COMPLETIONTHRESHOLD':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!isset($block['attrs']['MINPROGRESSMEASURE'])) {
							$block['attrs']['MINPROGRESSMEASURE'] = '1.0';
						}
						$scoes->elements[$manifest][$parent->organization][$parent->identifier]->threshold = $block['attrs']['MINPROGRESSMEASURE'];
					break;
					case 'ADLNAV:PRESENTATION':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!empty($block['children'])) {
							foreach ($block['children'] as $adlnav) {
								if ($adlnav['name'] == 'ADLNAV:NAVIGATIONINTERFACE') {
									foreach ($adlnav['children'] as $adlnavInterface) {
										if ($adlnavInterface['name'] == 'ADLNAV:HIDELMSUI') {
											if ($adlnavInterface['tagData'] == 'continue') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hidecontinue = 1;
											}
											if ($adlnavInterface['tagData'] == 'previous') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideprevious = 1;
											}
											if ($adlnavInterface['tagData'] == 'exit') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideexit = 1;
											}
											if ($adlnavInterface['tagData'] == 'exitAll') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideexitall = 1;
											}
											if ($adlnavInterface['tagData'] == 'abandon') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideabandon = 1;
											}
											if ($adlnavInterface['tagData'] == 'abandonAll') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideabandonall = 1;
											}
											if ($adlnavInterface['tagData'] == 'suspendAll') {
												$scoes->elements[$manifest][$parent->organization][$parent->identifier]->hidesuspendall = 1;
											}
										}
									}
								}
							}
						}
					break;
					case 'IMSSS:SEQUENCING':
						$parent = array_pop($parents);
						array_push($parents, $parent);
						if (!empty($block['children'])) {
							foreach ($block['children'] as $sequencing) {
								if ($sequencing['name']=='IMSSS:CONTROLMODE') {
									if (isset($sequencing['attrs']['CHOICE'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->choice = $sequencing['attrs']['CHOICE'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['CHOICEEXIT'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->choiceexit = $sequencing['attrs']['CHOICEEXIT'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['FLOW'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->flow = $sequencing['attrs']['FLOW'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['FORWARDONLY'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->forwardonly = $sequencing['attrs']['FORWARDONLY'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptobjectinfo = $sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptprogressinfo = $sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'] == 'true'?1:0;
									}
								}
								if ($sequencing['name'] == 'IMSSS:DELIVERYCONTROLS') {
									if (isset($sequencing['attrs']['TRACKED'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->tracked = $sequencing['attrs']['TRACKED'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['COMPLETIONSETBYCONTENT'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->completionsetbycontent = $sequencing['attrs']['COMPLETIONSETBYCONTENT'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['OBJECTIVESETBYCONTENT'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivesetbycontent = $sequencing['attrs']['OBJECTIVESETBYCONTENT'] == 'true'?1:0;
									}
								}
								if ($sequencing['name']=='ADLSEQ:CONSTRAINEDCHOICECONSIDERATIONS') {
									if (isset($sequencing['attrs']['CONSTRAINCHOICE'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->constrainChoice = $sequencing['attrs']['CONSTRAINCHOICE'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['PREVENTACTIVATION'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->preventactivation = $sequencing['attrs']['PREVENTACTIVATION'] == 'true'?1:0;
									}
								}
								if ($sequencing['name']=='IMSSS:OBJECTIVES') {
									$objectives = array();
									foreach ($sequencing['children'] as $objective) {
										$objectivedata = new stdClass();
										$objectivedata->primaryobj = 0;
										switch ($objective['name']) {
											case 'IMSSS:PRIMARYOBJECTIVE':
												$objectivedata->primaryobj = 1;
											case 'IMSSS:OBJECTIVE':
												$objectivedata->satisfiedbymeasure = 0;
												if (isset($objective['attrs']['SATISFIEDBYMEASURE'])) {
													$objectivedata->satisfiedbymeasure = $objective['attrs']['SATISFIEDBYMEASURE']== 'true'?1:0;
												}
												$objectivedata->objectiveid = '';
												if (isset($objective['attrs']['OBJECTIVEID'])) {
													$objectivedata->objectiveid = $objective['attrs']['OBJECTIVEID'];
												}
												$objectivedata->minnormalizedmeasure = 1.0;
												if (!empty($objective['children'])) {
													$mapinfos = array();
													foreach ($objective['children'] as $objectiveparam) {
														if ($objectiveparam['name']=='IMSSS:MINNORMALIZEDMEASURE') {
															if (isset($objectiveparam['tagData'])) {
																$objectivedata->minnormalizedmeasure = $objectiveparam['tagData'];
															} else {
																$objectivedata->minnormalizedmeasure = 0;
															}
														}
														if ($objectiveparam['name']=='IMSSS:MAPINFO') {
															$mapinfo = new stdClass();
															$mapinfo->targetobjectiveid = '';
															if (isset($objectiveparam['attrs']['TARGETOBJECTIVEID'])) {
																$mapinfo->targetobjectiveid = $objectiveparam['attrs']['TARGETOBJECTIVEID'];
															}
															$mapinfo->readsatisfiedstatus = 1;
															if (isset($objectiveparam['attrs']['READSATISFIEDSTATUS'])) {
																$mapinfo->readsatisfiedstatus = $objectiveparam['attrs']['READSATISFIEDSTATUS'] == 'true'?1:0;
															}
															$mapinfo->writesatisfiedstatus = 0;
															if (isset($objectiveparam['attrs']['WRITESATISFIEDSTATUS'])) {
																$mapinfo->writesatisfiedstatus = $objectiveparam['attrs']['WRITESATISFIEDSTATUS'] == 'true'?1:0;
															}
															$mapinfo->readnormalizemeasure = 1;
															if (isset($objectiveparam['attrs']['READNORMALIZEDMEASURE'])) {
																$mapinfo->readnormalizemeasure = $objectiveparam['attrs']['READNORMALIZEDMEASURE'] == 'true'?1:0;
															}
															$mapinfo->writenormalizemeasure = 0;
															if (isset($objectiveparam['attrs']['WRITENORMALIZEDMEASURE'])) {
																$mapinfo->writenormalizemeasure = $objectiveparam['attrs']['WRITENORMALIZEDMEASURE'] == 'true'?1:0;
															}
															array_push($mapinfos, $mapinfo);
														}
													}
													if (!empty($mapinfos)) {
														$objectivesdata->mapinfos = $mapinfos;
													}
												}
											break;
										}
										array_push($objectives, $objectivedata);
									}
									$scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectives = $objectives;
								}
								if ($sequencing['name']=='IMSSS:LIMITCONDITIONS') {
									if (isset($sequencing['attrs']['ATTEMPTLIMIT'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptLimit = $sequencing['attrs']['ATTEMPTLIMIT'];
									}
									if (isset($sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptAbsoluteDurationLimit = $sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'];
									}
								}
								if ($sequencing['name']=='IMSSS:ROLLUPRULES') {
									if (isset($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupobjectivesatisfied = $sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupprogresscompletion = $sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'] == 'true'?1:0;
									}
									if (isset($sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'])) {
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivemeasureweight = $sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'];
									}

									if (!empty($sequencing['children'])) {
										$rolluprules = array();
										foreach ($sequencing['children'] as $sequencingrolluprule) {
											if ($sequencingrolluprule['name']=='IMSSS:ROLLUPRULE' ) {
												$rolluprule = new stdClass();
												$rolluprule->childactivityset = 'all';
												if (isset($sequencingrolluprule['attrs']['CHILDACTIVITYSET'])) {
													$rolluprule->childactivityset = $sequencingrolluprule['attrs']['CHILDACTIVITYSET'];
												}
												$rolluprule->minimumcount = 0;
												if (isset($sequencingrolluprule['attrs']['MINIMUMCOUNT'])) {
													$rolluprule->minimumcount = $sequencingrolluprule['attrs']['MINIMUMCOUNT'];
												}
												$rolluprule->minimumpercent = 0.0000;
												if (isset($sequencingrolluprule['attrs']['MINIMUMPERCENT'])) {
													$rolluprule->minimumpercent = $sequencingrolluprule['attrs']['MINIMUMPERCENT'];
												}
												if (!empty($sequencingrolluprule['children'])) {
													foreach ($sequencingrolluprule['children'] as $rolluproleconditions) {
														if ($rolluproleconditions['name']=='IMSSS:ROLLUPCONDITIONS') {
															$conditions = array();
															$rolluprule->conditioncombination = 'all';
															if (isset($rolluproleconditions['attrs']['CONDITIONCOMBINATION'])) {
																$rolluprule->conditioncombination = $rolluproleconditions['attrs']['CONDITIONCOMBINATION'];
															}
															foreach ($rolluproleconditions['children'] as $rolluprulecondition) {
																if ($rolluprulecondition['name']=='IMSSS:ROLLUPCONDITION') {
																	$condition = new stdClass();
																	if (isset($rolluprulecondition['attrs']['CONDITION'])) {
																		$condition->cond = $rolluprulecondition['attrs']['CONDITION'];
																	}
																	$condition->operator = 'noOp';
																	if (isset($rolluprulecondition['attrs']['OPERATOR'])) {
																		$condition->operator = $rolluprulecondition['attrs']['OPERATOR'];
																	}
																	array_push($conditions, $condition);
																}
															}
															$rolluprule->conditions = $conditions;
														}
														if ($rolluproleconditions['name']=='IMSSS:ROLLUPACTION') {
															$rolluprule->rollupruleaction = $rolluproleconditions['attrs']['ACTION'];
														}
													}
												}
												array_push($rolluprules, $rolluprule);
											}
										}
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->rolluprules = $rolluprules;
									}
								}

								if ($sequencing['name']=='IMSSS:SEQUENCINGRULES') {
									if (!empty($sequencing['children'])) {
										$sequencingrules = array();
										foreach ($sequencing['children'] as $conditionrules) {
											$conditiontype = -1;
											switch($conditionrules['name']) {
												case 'IMSSS:PRECONDITIONRULE':
													$conditiontype = 0;
												break;
												case 'IMSSS:POSTCONDITIONRULE':
													$conditiontype = 1;
												break;
												case 'IMSSS:EXITCONDITIONRULE':
													$conditiontype = 2;
												break;
											}
											if (!empty($conditionrules['children'])) {
												$sequencingrule = new stdClass();
												foreach ($conditionrules['children'] as $conditionrule) {
													if ($conditionrule['name']=='IMSSS:RULECONDITIONS') {
														$ruleconditions = array();
														$sequencingrule->conditioncombination = 'all';
														if (isset($conditionrule['attrs']['CONDITIONCOMBINATION'])) {
															$sequencingrule->conditioncombination = $conditionrule['attrs']['CONDITIONCOMBINATION'];
														}
														foreach ($conditionrule['children'] as $rulecondition) {
															if ($rulecondition['name']=='IMSSS:RULECONDITION') {
																$condition = new stdClass();
																if (isset($rulecondition['attrs']['CONDITION'])) {
																	$condition->cond = $rulecondition['attrs']['CONDITION'];
																}
																$condition->operator = 'noOp';
																if (isset($rulecondition['attrs']['OPERATOR'])) {
																	$condition->operator = $rulecondition['attrs']['OPERATOR'];
																}
																$condition->measurethreshold = 0.0000;
																if (isset($rulecondition['attrs']['MEASURETHRESHOLD'])) {
																	$condition->measurethreshold = $rulecondition['attrs']['MEASURETHRESHOLD'];
																}
																$condition->referencedobjective = '';
																if (isset($rulecondition['attrs']['REFERENCEDOBJECTIVE'])) {
																	$condition->referencedobjective = $rulecondition['attrs']['REFERENCEDOBJECTIVE'];
																}
																array_push($ruleconditions, $condition);
															}
														}
														$sequencingrule->ruleconditions = $ruleconditions;
													}
													if ($conditionrule['name']=='IMSSS:RULEACTION') {
														$sequencingrule->action = $conditionrule['attrs']['ACTION'];
													}
													$sequencingrule->type = $conditiontype;
												}
												array_push($sequencingrules, $sequencingrule);
											}
										}
										$scoes->elements[$manifest][$parent->organization][$parent->identifier]->sequencingrules = $sequencingrules;
									}
								}
							}
						}
					break;
				}
			}
		}
		if (!empty($manifestresourcesnotfound)) {
			//throw warning to user to let them know manifest contains references to resources that don't appear to exist.
			if (!defined('DEBUGGING_PRINTED')) { //prevent redirect and display warning
				define('DEBUGGING_PRINTED', 1);
			}
			echo $OUTPUT->notification(get_string('invalidmanifestresource', 'scorm').' '. implode(', ',$manifestresourcesnotfound));
		}
		return $scoes;
	}

	function scorm_parse_scorm($manifest,$scorm) {

		$db = JFactory::getDbo();

		$launch = $entry = 0;
		$parent	=	'';

		$xmltext = @file_get_contents($manifest);
		$objXML = new xml2Array();
		$manifests = $objXML->parse($xmltext);

		$scoes = new stdClass();
		$scoes->version = '';
		$scoes = $this->scorm_get_manifest($manifests, $scoes);

		if (isset($scoes->elements) && count($scoes->elements) > 0) {
			$olditems = $this->gurudbhelper->get_records('*','guru_scorm_scoes', array('scorm_id'=>$scorm->id),'','loadObjectList');

			foreach ($scoes->elements as $manifest => $organizations) {

				foreach ($organizations as $organization => $items) {

					foreach ($items as $identifier => $item) {

						// This new db mngt will support all SCORM future extensions
						$newitem = new stdClass();
						$newitem->scorm_id = $scorm->id;
						$newitem->manifest = $manifest;
						$newitem->organization = $organization;
						$standarddatas = array('parent', 'identifier', 'launch', 'scormtype', 'title');
						foreach ($standarddatas as $standarddata) {
							if (isset($item->$standarddata)) {
								$newitem->$standarddata = $item->$standarddata;
							}
						}
						$db->insertObject( '#__guru_scorm_scoes', $newitem, 'id' );
						$id	=	$db->insertid();
						if (!empty($olditems) && ($olditemid = $this->scorm_array_search('identifier', $newitem->identifier, $olditems))) {
							$olditems[$olditemid]->newid = $id;
						}

						if ($optionaldatas = $this->scorm_optionals_data($item, $standarddatas)) {
							$data = new stdClass();
							$data->sco_id = $id;
							foreach ($optionaldatas as $optionaldata) {
								if (isset($item->$optionaldata)) {
									$data->name =  $optionaldata;
									$data->value = $item->$optionaldata;
									$dataid = $db->insertObject('#__guru_scorm_scoes_data', $data);
								}
							}
						}

						if (isset($item->sequencingrules)) {
							foreach ($item->sequencingrules as $sequencingrule) {
								$rule = new stdClass();
								$rule->sco_id = $id;
								$rule->ruletype = $sequencingrule->type;
								$rule->conditioncombination = $sequencingrule->conditioncombination;
								$rule->action = $sequencingrule->action;
								$ruleid = $db->insertObject('#__guru_scorm_seq_ruleconds', $rule);
								if (isset($sequencingrule->ruleconditions)) {
									foreach ($sequencingrule->ruleconditions as $rulecondition) {
										$rulecond = new stdClass();
										$rulecond->sco_id = $id;
										$rulecond->ruleconditionsid = $ruleid;
										$rulecond->referencedobjective = $rulecondition->referencedobjective;
										$rulecond->measurethreshold = $rulecondition->measurethreshold;
										$rulecond->operator = $rulecondition->operator;
										$rulecond->cond = $rulecondition->cond;
										$rulecondid = $db->insertObject('#__guru_scorm_seq_rulecond', $rulecond);
									}
								}
							}
						}

						if (isset($item->rolluprules)) {
							foreach ($item->rolluprules as $rolluprule) {
								$rollup = new stdClass();
								$rollup->sco_id =  $id;
								$rollup->childactivityset = $rolluprule->childactivityset;
								$rollup->minimumcount = $rolluprule->minimumcount;
								$rollup->minimumpercent = $rolluprule->minimumpercent;
								$rollup->action = $rolluprule->rollupruleaction;
								$rollup->conditioncombination = $rolluprule->conditioncombination;
								$rollupruleid = $db->insertObject('#__guru_scorm_seq_rolluprule', $rollup);
								if (isset($rollup->conditions)) {
									foreach ($rollup->conditions as $condition) {
										$cond = new stdClass();
										$cond->sco_id = $rollup->sco_id;
										$cond->rollupruleid = $rollupruleid;
										$cond->operator = $condition->operator;
										$cond->cond = $condition->cond;
										$conditionid = $db->insertObject('#__guru_scorm_seq_rolluprulecond', $cond);
									}
								}
							}
						}

						if (isset($item->objectives)) {
							foreach ($item->objectives as $objective) {
								$obj = new stdClass();
								$obj->sco_id = $id;
								$obj->primaryobj = $objective->primaryobj;
								$obj->satisfiedbymeasure = $objective->satisfiedbymeasure;
								$obj->objectiveid = $objective->objectiveid;
								$obj->minnormalizedmeasure = trim($objective->minnormalizedmeasure);
								$objectiveid = $db->insertObject('#__guru_scorm_seq_objective', $obj);
								if (isset($objective->mapinfos)) {
									foreach ($objective->mapinfos as $objmapinfo) {
										$mapinfo = new stdClass();
										$mapinfo->sco_id = $id;
										$mapinfo->objectiveid = $objectiveid;
										$mapinfo->targetobjectiveid = $objmapinfo->targetobjectiveid;
										$mapinfo->readsatisfiedstatus = $objmapinfo->readsatisfiedstatus;
										$mapinfo->writesatisfiedstatus = $objmapinfo->writesatisfiedstatus;
										$mapinfo->readnormalizedmeasure = $objmapinfo->readnormalizedmeasure;
										$mapinfo->writenormalizedmeasure = $objmapinfo->writenormalizedmeasure;
										$mapinfoid = $db->insertObject('#__guru_scorm_seq_mapinfo', $mapinfo);
									}
								}
							}
						}

						if (($launch == 0) && ((empty($scoes->defaultorg)) || ($scoes->defaultorg == $identifier)) && $item->parent==$scoes->defaultorg) {
							$launch = $id;
						}
						if ( ($entry==0) && $item->parent==$scoes->defaultorg) {
							if($item->launch	==	'')
								$parent=	$item->identifier;
							else
								$entry = $id;
						}
						if(($entry==0) && $parent	!=	''){
							if($parent == $item->parent)
								$entry = $id;
						}
					}
				}
			}

			if (!empty($olditems)) {
				foreach ($olditems as $olditem) {
					$this->gurudbhelper->delete_records('guru_scorm_scoes',array('id'=>$olditem->id));

					$this->gurudbhelper->delete_records('guru_scorm_scoes_data', array('sco_id'=>$olditem->id));
					if (isset($olditem->newid)) {
						$query = "UPDATE `#__guru_scorm_scoes_track` SET `sco_id`=$olditem->newid WHERE `sco_id`	=$olditem->id ";
						$db->setQuery($query);
						$db->execute();
					}
					$this->gurudbhelper->delete_records('guru_scorm_scoes_track',array('sco_id'=>$olditem->id));
					$this->gurudbhelper->delete_records('guru_scorm_seq_objective', array('sco_id'=>$olditem->id));
					$this->gurudbhelper->delete_records('guru_scorm_seq_mapinfo', array('sco_id'=>$olditem->id));
					$this->gurudbhelper->delete_records('guru_scorm_seq_ruleconds', array('sco_id'=>$olditem->id));
					$this->gurudbhelper->delete_records('guru_scorm_seq_rulecond', array('sco_id'=>$olditem->id));
					$this->gurudbhelper->delete_records('guru_scorm_seq_rolluprule', array('sco_id'=>$olditem->id));
					$this->gurudbhelper->delete_records('guru_scorm_seq_rolluprulecond', array('sco_id'=>$olditem->id));
					//$DB->delete_records('scorm_scoes_track', array('sco_id'=>$olditem->id));
					/*$DB->delete_records('scorm_seq_objective', array('scoid'=>$olditem->id));
					$DB->delete_records('scorm_seq_mapinfo', array('scoid'=>$olditem->id));
					$DB->delete_records('scorm_seq_ruleconds', array('scoid'=>$olditem->id));
					$DB->delete_records('scorm_seq_rulecond', array('scoid'=>$olditem->id));
					$DB->delete_records('scorm_seq_rolluprule', array('scoid'=>$olditem->id));
					$DB->delete_records('scorm_seq_rolluprulecond', array('scoid'=>$olditem->id));*/
				}
			}
			if (empty($scoes->version)) {
				$scoes->version = 'SCORM_1.2';
			}
			//$DB->set_field('scorm', 'version', $scoes->version, array('id'=>$scorm->id));
			$scorm->version = $scoes->version;
		}

		$scorm->launch = $launch;
		$scorm->entry = $entry;

		return $scorm;
	}

	function scorm_optionals_data($item, $standarddata) {
		$result = array();
		$sequencingdata = array('sequencingrules', 'rolluprules', 'objectives');
		foreach ($item as $element => $value) {
			if (! in_array($element, $standarddata)) {
				if (! in_array($element, $sequencingdata)) {
					$result[] = $element;
				}
			}
		}
		return $result;
	}
	function scorm_array_search($item, $needle, $haystacks, $strict=false) {
    if (!empty($haystacks)) {
        foreach ($haystacks as $key => $element) {
            if ($strict) {
                if ($element->{$item} === $needle) {
                    return $key;
                }
            } else {
                if ($element->{$item} == $needle) {
                    return $key;
                }
            }
        }
    }
    return false;
}
}
class xml2Array {

		var $arrOutput = array();
		var $resParser;
		var $strXmlData;

		/**
		 * Convert a utf-8 string to html entities
		 *
		 * @param string $str The UTF-8 string
		 * @return string
		 */
		function utf8_to_entities($str) {
			global $CFG;

			$entities = '';
			$values = array();
			$lookingfor = 1;

			return $str;
		}

		/**
		 * Parse an XML text string and create an array tree that rapresent the XML structure
		 *
		 * @param string $strInputXML The XML string
		 * @return array
		 */
		function parse($strInputXML) {
			$this->resParser = xml_parser_create ('UTF-8');
			xml_set_object($this->resParser, $this);
			xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

			xml_set_character_data_handler($this->resParser, "tagData");

			$this->strXmlData = xml_parse($this->resParser, $strInputXML );
			if (!$this->strXmlData) {
				die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($this->resParser)),
				xml_get_current_line_number($this->resParser)));
			}

			xml_parser_free($this->resParser);

			return $this->arrOutput;
		}

		function tagOpen($parser, $name, $attrs) {
			$tag=array("name"=>$name, "attrs"=>$attrs);
			array_push($this->arrOutput, $tag);
		}

		function tagData($parser, $tagData) {
			if (trim($tagData)) {
				if (isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
					$this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $this->utf8_to_entities($tagData);
				} else {
					$this->arrOutput[count($this->arrOutput)-1]['tagData'] = $this->utf8_to_entities($tagData);
				}
			}
		}

		function tagClosed($parser, $name) {
			$this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
			array_pop($this->arrOutput);
		}

	}
