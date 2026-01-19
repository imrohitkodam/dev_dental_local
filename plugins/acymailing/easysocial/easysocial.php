<?php
/**
 * @copyright    Copyright (C) 2009-2016 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingeasysocial extends JPlugin{
	var $cats = array();
	var $catvalues = array();
	var $newslanguage;
	var $readmore = '';
	var $tags = array();

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		$this->name = 'easysocial';
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', $this->name);
			$this->params = new acyParameter($plugin->params);
		}

		$this->db = JFactory::getDBO();
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
		$this->component_installed = file_exists(JPATH_SITE.DS.'components'.DS.'com_easysocial');
	}

	function acymailing_getPluginType(){
		$app = JFactory::getApplication();
		if(!$this->component_installed || ($this->params->get('frontendaccess') == 'none' && !$app->isAdmin())) return;
		$onePlugin = new stdClass();
		$onePlugin->name = 'EasySocial';
		$onePlugin->function = 'acymailing_'.$this->name.'_show';
		$onePlugin->help = 'plugin-'.$this->name;
		return $onePlugin;
	}

	function acymailing_easysocial_show(){
		$config = acymailing_config();
		if($config->get('version') < '5.2.0'){
			acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
			return;
		}

		if(!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'foundry.php')){
			acymailing_display('You must update your EasySocial component otherwise this plugin will NOT work', 'error');
			return;
		}

		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'foundry.php');
		if(version_compare(Foundry::getLocalVersion(), '1.2.0', '<')){
			acymailing_display('You must update your EasySocial component otherwise this plugin will NOT work', 'error');
			return;
		}

		$tabs = acymailing_get('helper.acytabs');

		$lang = JFactory::getLanguage();
		$lang->load('com_easysocial', JPATH_SITE);
		$lang->load('com_easysocial', JPATH_ADMINISTRATOR);
		$lang->load('plg_app_user_calendar', JPATH_ADMINISTRATOR);

		//is this a Smart-Newsletter or a simple one ?
		$this->type = JRequest::getString('type');

		// yes/no for the options
		$this->choice = array();
		$this->choice[] = JHTML::_('select.option', "1", JText::_('JOOMEXT_YES'));
		$this->choice[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		// picture options
		$this->valImages = array();
		$this->valImages[] = JHTML::_('select.option', "1", JText::_('JOOMEXT_YES'));
		$this->valImages[] = JHTML::_('select.option', "resized", JText::_('RESIZED'));
		$this->valImages[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		// filter options
		$this->contentfilter = array();
		$this->contentfilter[] = JHTML::_('select.option', "0", JText::_('ACY_ALL'));
		$this->contentfilter[] = JHTML::_('select.option', "created", JText::_('ONLY_NEW_CREATED'));

		// options for the Nb. columns dropdown (1 to 10)
		$this->column = array();
		for($i = 1; $i < 11; $i++){
			$this->column[] = JHTML::_('select.option', "$i", $i);
		}

		acymailing_display('This plugin will load only public content (except for "'.JText::_('USER_FIELDS').'" tab)', 'notice');

		echo $tabs->startPane($this->name.'_tab');
		$this->_showUsersTab($tabs);
		$this->_showProfilesTab($tabs);
		$this->_showAlbumsTab($tabs);
		$this->_showEventsTab($tabs);
		$this->_showUserFieldsTab($tabs);
		$this->_loadJavascript();
		echo $tabs->endPane();
	}

	function acymailing_replaceusertags(&$email, &$user, $send = true){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$app = JFactory::getApplication();
		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'easysocialfields');
		$this->tags = array();

		if(empty($tags) || empty($user)) return $return;

		if(!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'foundry.php')){
			if($app->isAdmin()) $app->enqueueMessage('You must update your EasySocial component to include user fields', 'warning');
			return $return;
		}

		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'foundry.php');
		$receiver = Foundry::user($user->userid);

		$db = JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$lang->load('com_easysocial', JPATH_SITE);

		foreach($tags as $oneTag => $parameter){
			$db->setQuery('SELECT unique_key FROM #__social_fields WHERE id = '.intval($parameter->id));
			$uniqueKey = $db->loadResult();

			if(empty($uniqueKey)){
				$this->tags[$oneTag] = '';
				continue;
			}

			$fieldValue = $receiver->getFieldValue($uniqueKey);

			if(empty($fieldValue)){
				$this->tags[$oneTag] = '';
				continue;
			}

			if(is_string($fieldValue)){
				$this->tags[$oneTag] = $fieldValue;
				continue;
			}

			if(is_string($fieldValue->value)){
				if(strstr($fieldValue->unique_key, 'BOOLEAN')){
					$this->tags[$oneTag] = JText::_(empty($fieldValue->value) ? 'JOOMEXT_NO' : 'JOOMEXT_YES');
				}elseif(strstr($fieldValue->unique_key, 'RELATIONSHIP')){
					$this->tags[$oneTag] = json_decode($fieldValue->value)->type;
				}elseif(strstr($fieldValue->unique_key, 'COUNTRY')){
					$this->tags[$oneTag] = implode(', ', json_decode($fieldValue->value));
				}else{
					$this->tags[$oneTag] = $fieldValue->value;
				}
			}elseif(is_object($fieldValue->value)){
				$arrayValue = (array)$fieldValue->value;
				if(!empty($arrayValue['day'])){
					if(!empty($email->language)){
						$lang = JFactory::getLanguage();
						if(!in_array($email->language, $lang->getLocale())){
							$db->setQuery('SELECT lang_code FROM #__languages WHERE sef = '.$db->quote($email->language).' LIMIT 1');
							$emaillangcode = $db->loadResult();
							if(!empty($emaillangcode)){
								$previousLanguage = $lang->setLanguage($emaillangcode);
								$lang->load(ACYMAILING_COMPONENT, JPATH_SITE, $emaillangcode, true);
								$lang->load(ACYMAILING_COMPONENT.'_custom', JPATH_SITE, $emaillangcode, true);
								$lang->load('joomla', JPATH_BASE, $emaillangcode, true);
							}
						}
					}

					$date = $arrayValue['year'].'-'.$arrayValue['month'].'-'.$arrayValue['day'];
					if(isset($arrayValue['hour'])){
						$date .= ' '.$arrayValue['hour'].':'.$arrayValue['minute'].':'.$arrayValue['second'];
					}else $date .= ' 00:00:00';
					if(!ACYMAILING_J16){
						$this->tags[$oneTag] = acymailing_getDate(acymailing_getTime($date), JText::_('DATE_FORMAT_LC'.(empty($parameter->format) ? '' : intval($parameter->format))));
					}else{
						$date = JFactory::getDate(strtotime($date));
						$this->tags[$oneTag] = $date->calendar(JText::_('DATE_FORMAT_LC'.(empty($parameter->format) ? '' : intval($parameter->format))), true);
					}

					if(!empty($previousLanguage)){
						$lang->setLanguage($previousLanguage);
						$lang->load(ACYMAILING_COMPONENT, JPATH_SITE, $previousLanguage, true);
						$lang->load(ACYMAILING_COMPONENT.'_custom', JPATH_SITE, $previousLanguage, true);
						$lang->load('joomla', JPATH_BASE, $previousLanguage, true);
						$previousLanguage = '';
					}
				}elseif(!empty($arrayValue['address1'])){
					$this->tags[$oneTag] = $arrayValue['address1'].', '.$arrayValue['zip'].' '.$arrayValue['city'].', '.$arrayValue['country'];
				}elseif(!empty($arrayValue['text'])){
					$this->tags[$oneTag] = JText::_($arrayValue['text']);
				}else{
					$this->tags[$oneTag] = implode(', ', $arrayValue);
				}
			}elseif(is_array($fieldValue->value)){
				$this->tags[$oneTag] = implode(', ', $fieldValue->value);
			}else{
				$this->tags[$oneTag] = '';
			}
		}

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags), $this->tags, $email->body);
			$this->mailerHelper = acymailing_get('helper.mailer');
			$textTags = array();
			$subjectTags = array();

			foreach($this->tags as $tag => $result){
				$subjectTags[$tag] = strip_tags(preg_replace('#</tr>[^<]*<tr[^>]*>#Uis', ' | ', $result));
				$textTags[$tag] = $this->mailerHelper->textVersion($result);
			}

			if(!empty($email->altbody)){
				$email->altbody = str_replace(array_keys($textTags), $textTags, $email->altbody);
			}
			$email->subject = str_replace(array_keys($subjectTags), $subjectTags, $email->subject);
		}

		return $return;
	}

	function acymailing_replacetags(&$email, $send = true){
		$lang = JFactory::getLanguage();
		$lang->load('com_easysocial', JPATH_SITE);
		$lang->load('com_easysocial', JPATH_ADMINISTRATOR);
		$lang->load('plg_app_user_calendar', JPATH_ADMINISTRATOR);

		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'easysocial.php');
		$this->readmore = empty($email->template->readmore) ? JText::_('JOOMEXT_READ_MORE') : '<img src="'.ACYMAILING_LIVE.$email->template->readmore.'" alt="'.JText::_('JOOMEXT_READ_MORE', true).'" />';
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id, title FROM #__usergroups');
		$this->usergroups = $db->loadObjectList();
		$this->_replaceProfiles($email);
		$this->_replaceAlbums($email);
		$this->_replaceCalendarEvents($email);
		$this->_replaceEvents($email);
		$this->_replaceUsers($email);

		if(empty($this->tags)) return;
		$this->acypluginsHelper->replaceTags($email, $this->tags, true);
	}

	function _replaceUsers(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		$db = JFactory::getDBO();

		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'easysocialusers');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		if(empty($this->tags)) $this->tags = array();
		if(empty($tags)) return $return;

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])) continue;

			if(!empty($parameter->display)){
				$fields = explode(',', $parameter->display);
				foreach($fields as $i => $oneDisplay){
					$fields[$i] = trim($oneDisplay);
					if(!is_int($fields[$i])) $parameter->{$fields[$i]} = true;
				}
				JArrayHelper::toInteger($fields);
			}else{
				continue;
			}

			$query = 'SELECT a.id, a.name, a.email FROM #__users AS a JOIN #__social_profiles_maps AS b ON a.id = b.user_id ';

			$where = array();

			if(!empty($parameter->profile)) $where[] = 'b.profile_id = '.intval($parameter->profile);

			// filter results newly created
			if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate']) && $parameter->filter == 'created'){
				$where[] = 'a.registerDate > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
			}

			// Apply filters
			if(!empty($where)) $query .= ' WHERE ('.implode(') AND (', $where).')';

			// Ordering
			if(!empty($parameter->order)){
				if($parameter->order == 'rand'){
					$query .= ' ORDER BY rand()';
				}else{
					$ordering = explode(',', $parameter->order);
					$query .= ' ORDER BY a.'.acymailing_secureField(trim($ordering[0])).' '.acymailing_secureField(trim($ordering[1]));
				}
			}

			// If there is no limit, then we add one
			if(!empty($parameter->max)){
				$query .= ' LIMIT '.intval($parameter->max);
			}else{
				$query .= ' LIMIT 20';
			} // limit, because we do not want to display one bilion content in the newsletter...

			$db->setQuery($query);
			$allArticles = $db->loadObjectList();

			// Check the number of results
			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				//We will not generate the Newsletter
				$return->status = false;
				$return->message = 'Not enough '.$this->name.' users for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min;
			}

			$stringTag = '';
			if(!empty($allArticles)){// If the user created its own template...
				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'users.php')){
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'users.php');
					$stringTag = ob_get_clean();
				}else{
					//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
					$arrayElements = array();
					foreach($allArticles as $oneArticle){
						$result = '<div class="acymailing_content"><table cellspacing="0" cellpadding="0" border="0" width="100%">';

						$query = 'SELECT a.data, a.raw, b.unique_key, b.display_title, b.title, b.id '.'FROM #__social_fields_data AS a '.'JOIN #__social_fields AS b '.'ON a.field_id = b.id '.'LEFT JOIN #__social_privacy_items AS c '.'ON b.id = c.uid '.'WHERE ((c.value IS NULL OR c.value = 0) '.'AND (c.type IS NULL OR c.type LIKE "field") '.'AND (c.user_id IS NULL OR c.user_id = '.intval($oneArticle->id).')) '.'AND a.uid = '.intval($oneArticle->id).' '.'AND a.field_id IN ('.implode(',', $fields).') '.'AND a.data NOT LIKE "" '.'AND b.unique_key NOT LIKE "JOOMLA_FULLNAME"';
						$db->setQuery($query);
						$userFields = $db->loadObjectList();

						$varFields = array();
						foreach($oneArticle as $key => $oneField){
							$varFields['{'.$key.'}'] = $oneField;
						}

						$link = 'index.php?option=com_easysocial&view=profile&id='.$oneArticle->id;
						$varFields['{link}'] = $link;

						if(!empty($parameter->name)){
							$result .= '<tr><td colspan="2">';
							if(empty($parameter->nolink)) $result .= '<a href="'.$link.'">';
							$result .= '<h2 class="acymailing_title">'.$oneArticle->name.'</h2>';
							if(empty($parameter->nolink)) $result .= '</a>';
							$result .= '</td></tr>';
						}

						if(!empty($parameter->avatar)){
							$db->setQuery('SELECT square FROM #__social_avatars WHERE uid = '.intval($oneArticle->id).' AND type LIKE "user" LIMIT 1');
							$avatar = $db->loadResult();
							if(!empty($avatar)){
								$result .= '<tr><td colspan="2"><img src="media/com_easysocial/avatars/user/'.intval($oneArticle->id).'/'.$avatar.'"/></td></tr>';
							}else{
								$result .= '<tr><td colspan="2"><img src="media/com_easysocial/defaults/avatars/user/square.png"/></td></tr>';
							}
						}

						if(!empty($parameter->email)){
							$result .= '<tr><td nowrap style="padding-right:10px;" valign="top"><strong>'.JText::_('EMAILCAPTION').' <span nowrap style="float:right;">:</span></strong></td><td valign="top">'.$oneArticle->email.'</td></tr>';
						}

						if(!empty($fields)){
							$displayValue = '';
							foreach($userFields as $data){
								if(!in_array($data->id, $fields)) continue;

								$db->setQuery('SELECT * FROM #__social_fields_options WHERE parent_id = '.intval($parameter->id));
								$labels = $db->loadObjectList();

								$decoded = json_decode($data->raw);
								$dataDecoded = json_decode($data->data);

								if(is_int($decoded) || ($decoded === null && empty($labels) && empty($dataDecoded->day))){
									$displayValue = $data->raw;
								}else{
									if(is_object($decoded) || !empty($dataDecoded->day)){
										$decoded = (array)$decoded;
										if(!empty($decoded['dollar'])){
											$displayValue = implode('.', $decoded).'$';
										}elseif(!empty($dataDecoded->day)){
											$displayValue = acymailing_getDate(acymailing_getTime($dataDecoded->year.'-'.$dataDecoded->month.'-'.$dataDecoded->day.' 00:00:00'), JText::_('DATE_FORMAT_LC'));
										}else{
											$displayValue = implode(', ', $decoded);
										}
									}elseif(is_array($decoded)){
										$values = array();
										foreach($decoded as $oneOption){
											if(!is_string($oneOption)){
												if(!empty($oneOption->name)) $values[] = '<a href="index.php?option=com_easysocial&view=fields&group=user&element=file&task=download&id='.$data->id.'&uid='.$oneOption->id.'">'.$oneOption->name.'</a>';
											}else{
												if(!empty($labels)){
													foreach($labels as $oneProp){
														if($oneProp->value != $oneOption) continue;
														$values[] = $oneProp->title;
														break;
													}
												}else{
													$values[] = $oneOption;
												}
											}
										}
										if(!empty($values)) $displayValue = implode(', ', $values);
									}else{
										foreach($labels as $oneProp){
											if($oneProp->value != $data->raw) continue;
											$displayValue = $oneProp->title;
											break;
										}
									}
								}

								if(strstr($data->unique_key, 'BOOLEAN')){
									$displayValue = empty($data->raw) ? JText::_('JOOMEXT_NO') : JText::_('JOOMEXT_YES');
								}

								if(strstr($data->unique_key, 'GENDER')){
									if($data->raw == 1){
										$displayValue = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_MALE');
									}elseif($data->raw == 2){
										$displayValue = JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_FEMALE');
									}else{
										$displayValue = '';
									}
								}

								if($data->display_title == 1){
									$result .= '<tr><td nowrap style="padding-right:10px;" valign="top"><strong>'.JText::_($data->title).' <span nowrap style="float:right;">:</span></strong></td><td valign="top">'.$displayValue.'</td></tr>';
								}else{
									$result .= '<tr><td></td><td valign="top">'.$displayValue.'</td></tr>';
								}
								$varFields['{'.$data->title.'}'] = $displayValue;
							}
						}

						if(empty($parameter->noreadmore)){
							$result .= '<tr><td colspan="2"><br/><a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$this->readmore.'</span></a></td></tr>';
						}

						// If the user created its own template...
						if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'user.php')){
							ob_start();
							require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'user.php');
							$result = ob_get_clean();
							$result = str_replace(array_keys($varFields), $varFields, $result);
						}
						$arrayElements[] = $result.'</table></div>';
					}
					$stringTag = $acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}
			}
			if(isset($parameter->pict) && $parameter->pict !== 'resized') $parameter->pict = '0';
			$stringTag = $acypluginsHelper->managePicts($parameter, $stringTag);
			$this->tags[$oneTag] = $stringTag;
		}
		return $return;
	}

	function _replaceCalendarEvents(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		$db = JFactory::getDBO();
		$time = time();

		$db->setQuery('SELECT id FROM #__social_apps WHERE element LIKE "calendar"');
		$calendar = $db->loadObject();

		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'easysocialevents');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		if(empty($this->tags)) $this->tags = array();
		if(empty($tags)) return $return;

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])) continue;
			if(empty($parameter->from)) $parameter->from = date('Y-m-d H:i:s', $time);

			if(!empty($parameter->display)){
				$parameter->display = explode(',', $parameter->display);

				foreach($parameter->display as $i => $oneDisplay){
					$oneDisplay = trim($oneDisplay);
					$parameter->$oneDisplay = true;
				}
				unset($parameter->display);
			}

			$query = 'SELECT a.*, b.name '.'FROM #__social_apps_calendar AS a '.'JOIN #__users AS b '.'ON a.user_id = b.id '.'JOIN #__social_stream_item AS c '.'ON a.id = c.context_id ';

			$where = array();

			$where[] = 'c.context_type LIKE "calendar"';
			$where[] = 'c.verb LIKE "create"';

			if(!empty($parameter->addcurrent)){
				//not finished and next events
				$where[] = 'a.`date_end` >= '.$db->Quote($parameter->from);
			}else{
				//not started events
				$where[] = 'a.`date_start` >= '.$db->Quote($parameter->from);
			}

			//should we display only events starting in the sending day ?
			if(!empty($parameter->todaysevent)){
				$where[] = 'a.`date_start` <= '.$db->Quote(date('Y-m-d 23:59:59', $time));
			}

			if(!empty($parameter->mindelay)) $where[] = 'a.`date_start` >= '.$db->Quote(date('Y-m-d H:i:s', $time + $parameter->mindelay));
			if(!empty($parameter->delay)) $where[] = 'a.`date_start` <= '.$db->Quote(date('Y-m-d H:i:s', $time + $parameter->delay));
			if(!empty($parameter->to)) $where[] = 'a.`date_start` <= '.$db->Quote($parameter->to);

			// Apply filters
			if(!empty($where)) $query .= ' WHERE ('.implode(') AND (', $where).')';

			// Ordering
			if(!empty($parameter->order)){
				if($parameter->order == 'rand'){
					$query .= ' ORDER BY rand()';
				}else{
					$ordering = explode(',', $parameter->order);
					$query .= ' ORDER BY a.'.acymailing_secureField(trim($ordering[0])).' '.acymailing_secureField(trim($ordering[1]));
				}
			}

			// If there is no limit, then we add one
			if(!empty($parameter->max)){
				$query .= ' LIMIT '.intval($parameter->max);
			}else{
				$query .= ' LIMIT 20';
			} // limit, because we do not want to display one bilion content in the newsletter...

			$db->setQuery($query);
			$allArticles = $db->loadObjectList();

			// Check the number of results
			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				//We will not generate the Newsletter
				$return->status = false;
				$return->message = 'Not enough '.$this->name.' events for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min;
			}

			$stringTag = '';
			if(!empty($allArticles)){
				// If the user created its own template...
				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'events.php')){
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'events.php');
					$stringTag = ob_get_clean();
				}else{
					//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
					$arrayElements = array();
					foreach($allArticles as $oneArticle){
						$varFields = array();
						foreach($oneArticle as $fieldName => $oneField){
							$varFields['{'.$fieldName.'}'] = $oneField;
						}
						$link = 'index.php?option=com_easysocial&view=apps&id='.$calendar->id.'&layout=canvas&userid='.$oneArticle->user_id;
						$varFields['{link}'] = $link;
						$result = '<div class="acymailing_content"><table cellspacing="0" cellpadding="0" border="0" width="100%">';

						if(!empty($parameter->title)){
							$result .= '<tr><td colspan="2"><h2 class="acymailing_title">';
							if(empty($parameter->nolink)) $result .= '<a href="'.$link.'">';
							$result .= $oneArticle->title;
							if(empty($parameter->nolink)) $result .= '</a>';
							$result .= '</h2></td></tr>';
						}

						if(!empty($parameter->description) && !empty($oneArticle->description)){
							$result .= '<tr><td colspan="2">'.$acypluginsHelper->wrapText($oneArticle->description, $parameter).'</td></tr>';
						}

						if(!empty($parameter->author)){
							$result .= '<tr><td>'.JText::_('ACY_AUTHOR').' :</td><td>'.$oneArticle->name.'</td></tr>';
						}

						if(!empty($parameter->date) && !empty($oneArticle->date_start) && $oneArticle->date_start != '0000-00-00 00:00:00'){
							$result .= '<tr><td>'.JText::_('APP_CALENDAR_CREATE_NEW_SCHEDULE_STARTDATE').' :</td><td>'.acymailing_getDate(acymailing_getTime($oneArticle->date_start), JText::_('DATE_FORMAT_LC')).'</td></tr>';
						}

						if(!empty($parameter->endate) && !empty($oneArticle->date_end) && $oneArticle->date_end != '0000-00-00 00:00:00'){
							$result .= '<tr><td>'.JText::_('APP_CALENDAR_CREATE_NEW_SCHEDULE_ENDDATE').' :</td><td>'.acymailing_getDate(acymailing_getTime($oneArticle->date_end), JText::_('DATE_FORMAT_LC')).'</td></tr>';
						}

						if(empty($parameter->noreadmore)){
							$result .= '<tr><td colspan="2"><br /><a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$this->readmore.'</span></a></td></tr>';
						}

						// If the user created its own template...
						if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'event.php')){
							ob_start();
							require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'event.php');
							$result = ob_get_clean();
							$result = str_replace(array_keys($varFields), $varFields, $result);
						}
						$arrayElements[] = $result.'</table></div>';
					}
					$stringTag = $acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}
			}
			if(isset($parameter->pict) && $parameter->pict !== 'resized') $parameter->pict = '0';
			$stringTag = $acypluginsHelper->managePicts($parameter, $stringTag);
			$this->tags[$oneTag] = $stringTag;
		}
		return $return;
	}

	function _replaceEvents(&$email){
		$db = JFactory::getDBO();
		$time = time();

		//load the tags
		$tags = $this->acypluginsHelper->extractTags($email, 'easysocialrevents');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		if(empty($this->tags)) $this->tags = array();
		if(empty($tags)) return $return;

		$config = FD::config();
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'storage'.DS.'storage.php');
		$storage = new SocialStorage($config->get('storage.avatars', 'joomla'));

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])) continue;
			$allcats = explode('-', $parameter->id);
			$selectedArea = array();
			foreach($allcats as $oneCat){
				if(empty($oneCat)) continue;
				$selectedArea[] = intval($oneCat);
			}
			if(empty($parameter->from)) $parameter->from = date('Y-m-d H:i:s', $time);

			if(!empty($parameter->display)){
				$parameter->display = explode(',', $parameter->display);

				foreach($parameter->display as $i => $oneDisplay){
					$oneDisplay = trim($oneDisplay);
					$parameter->$oneDisplay = true;
				}
				unset($parameter->display);
			}

			$query = 'SELECT a.*, b.name, c.start, c.end, c.all_day, av.large AS avatar '.'FROM #__social_clusters AS a '.'LEFT JOIN #__users AS b '.'ON a.creator_uid = b.id '.'JOIN #__social_events_meta AS c '.'ON a.id = c.cluster_id '.'LEFT JOIN #__social_avatars AS av '.'ON av.type = "event" AND av.uid = a.id ';

			$where = array();

			if(!empty($selectedArea)) $where[] = 'a.category_id IN ('.implode(',', $selectedArea).')';

			$where[] = 'a.cluster_type LIKE "event"';
			if(empty($parameter->unpublished)){
				$where[] = 'a.state = 1';
			}else{
				$where[] = 'a.state = 1 OR a.state = 0';
			}

			if(!empty($parameter->featured)) $where[] = 'a.featured != 0';
			$where[] = 'a.type = 1 OR a.type = 2';

			if(!empty($parameter->creator)){
				if($parameter->creator == 'group'){
					if(!empty($parameter->groupid)){
						$where[] = 'c.group_id = '.intval($parameter->groupid);
					}else{
						$where[] = 'c.group_id != 0';
					}
				}else{
					$where[] = 'c.group_id = 0';
				}
			}

			if(!empty($parameter->addcurrent)){
				//not finished and next events
				$where[] = 'c.`end` >= '.$db->Quote($parameter->from);
			}else{
				//not started events
				$where[] = 'c.`start` >= '.$db->Quote($parameter->from);
			}

			//should we display only events starting in the sending day ?
			if(!empty($parameter->todaysevent)){
				$where[] = 'c.`start` <= '.$db->Quote(date('Y-m-d 23:59:59', $time));
			}

			if(!empty($parameter->mindelay)) $where[] = 'c.`start` >= '.$db->Quote(date('Y-m-d H:i:s', $time + $parameter->mindelay));
			if(!empty($parameter->delay)) $where[] = 'c.`start` <= '.$db->Quote(date('Y-m-d H:i:s', $time + $parameter->delay));
			if(!empty($parameter->to)) $where[] = 'c.`start` <= '.$db->Quote($parameter->to.' 23:59:59');

			// Apply filters
			if(!empty($where)) $query .= ' WHERE ('.implode(') AND (', $where).') ';

			$query .= 'GROUP BY a.id ';

			// Ordering
			if(!empty($parameter->order)){
				if($parameter->order == 'rand'){
					$query .= ' ORDER BY rand()';
				}else{
					$ordering = explode(',', str_replace(array('date_start', 'date_end'), array('start', 'end'), $parameter->order));
					if(in_array($ordering[0], array('start', 'end'))){
						$query .= ' ORDER BY c.'.acymailing_secureField(trim($ordering[0])).' '.acymailing_secureField(trim($ordering[1]));
					}else{
						$query .= ' ORDER BY a.'.acymailing_secureField(trim($ordering[0])).' '.acymailing_secureField(trim($ordering[1]));
					}
				}
			}

			// If there is no limit, then we add one
			if(!empty($parameter->max)){
				$query .= ' LIMIT '.intval($parameter->max);
			}else{
				$query .= ' LIMIT 20';
			} // limit, because we do not want to display one bilion content in the newsletter...

			$db->setQuery($query);
			$allArticles = $db->loadObjectList();

			// Check the number of results
			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				//We will not generate the Newsletter
				$return->status = false;
				$return->message = 'Not enough '.$this->name.' events for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min;
			}

			$stringTag = '';
			if(!empty($allArticles)){
				//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
				$arrayElements = array();
				foreach($allArticles as $oneArticle){
					$varFields = array();
					foreach($oneArticle as $fieldName => $oneField){
						$varFields['{'.$fieldName.'}'] = $oneField;
					}
					$link = 'index.php?option=com_easysocial&view=events&id='.$oneArticle->id.'&layout=item';
					$varFields['{link}'] = $link;
					$result = '<div class="acymailing_content"><table cellspacing="0" cellpadding="0" border="0" width="100%">';

					if(!empty($parameter->title)){
						$result .= '<tr><td colspan="2"><h2 class="acymailing_title">';
						if(empty($parameter->nolink)) $result .= '<a href="'.$link.'">';
						$result .= $oneArticle->title;
						if(empty($parameter->nolink)) $result .= '</a>';
						$result .= '</h2></td></tr>';
					}

					if((!empty($parameter->description) && !empty($oneArticle->description)) || (!empty($parameter->avatar) && !empty($oneArticle->avatar))){
						$result .= '<tr><td colspan="2">';
						if(!empty($parameter->avatar) && !empty($oneArticle->avatar)){
							$relativePath = FD::cleanPath($config->get('avatars.storage.container')).'/'.FD::cleanPath($config->get('avatars.storage.event')).'/'.$oneArticle->id.'/'.$oneArticle->avatar;
							if(file_exists(JPATH_SITE.DS.$relativePath)) $oneArticle->avatar = $storage->getPermalink($relativePath);
							$result .= '<img style="'.(empty($parameter->style) ? 'float:left;' : $parameter->style).'" alt="" src="'.$oneArticle->avatar.'"/>';
						}
						if(!empty($parameter->description) && !empty($oneArticle->description)){
							$result .= $this->acypluginsHelper->wrapText($oneArticle->description, $parameter);
						}
						$result .= '</td></tr>';
					}

					if(!empty($parameter->author) && $oneArticle->creator_type == 'user'){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('ACY_AUTHOR').' :</td><td class="acyvalueeasysocial">'.$oneArticle->name.'</td></tr>';
					}

					$format = empty($oneArticle->all_day) ? 'DATE_FORMAT_LC2' : 'DATE_FORMAT_LC';
					if(!empty($parameter->date) && !empty($oneArticle->start) && $oneArticle->start != '0000-00-00 00:00:00'){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('APP_CALENDAR_CREATE_NEW_SCHEDULE_STARTDATE').' :</td><td class="acyvalueeasysocial">'.acymailing_getDate(acymailing_getTime($oneArticle->start), JText::_($format)).'</td></tr>';
					}

					if(!empty($parameter->endate) && !empty($oneArticle->end) && $oneArticle->end != '0000-00-00 00:00:00'){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('APP_CALENDAR_CREATE_NEW_SCHEDULE_ENDDATE').' :</td><td class="acyvalueeasysocial">'.acymailing_getDate(acymailing_getTime($oneArticle->end), JText::_($format)).'</td></tr>';
					}

					if(!empty($parameter->location) && !empty($oneArticle->address)){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('COM_EASYSOCIAL_LOCATION').' :</td><td>'.$oneArticle->address.'</td></tr>';
					}

					if(!empty($parameter->guestlim) && !empty($oneArticle->params)){
						$eventParams = json_decode($oneArticle->params);
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('FIELDS_EVENT_GUESTLIMIT_DEFAULT_TITLE').' :</td><td class="acyvalueeasysocial">'.(empty($eventParams->guestlimit) ? JText::_('ACY_NONE') : $eventParams->guestlimit).'</td></tr>';
					}

					if(!empty($parameter->website)){
						$db->setQuery('SELECT a.data '.'FROM #__social_fields_data AS a '.'JOIN #__social_fields AS b '.'ON a.field_id = b.id '.'WHERE a.type = "event" '.'AND a.uid = '.intval($oneArticle->id).' '.'AND b.unique_key = "URL"');
						$website = $db->loadResult();
						if(!empty($website)){
							$result .= '<tr><td class="acylabeleasysocial">'.JText::_('PLG_FIELDS_URL_DEFAULT_TITLE').' :</td><td class="acyvalueeasysocial"><a href="'.$website.'">'.$website.'</a></td></tr>';
						}
					}

					if(empty($parameter->noreadmore)){
						$result .= '<tr><td colspan="2"><br /><a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$this->readmore.'</span></a></td></tr>';
					}

					$result .= '</table></div>';

					// If the user created its own template...
					if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'event.php')){
						ob_start();
						require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'event.php');
						$result = ob_get_clean();
						$result = str_replace(array_keys($varFields), $varFields, $result);
					}
					$arrayElements[] = $result;
				}
				$stringTag = $this->acypluginsHelper->getFormattedResult($arrayElements, $parameter);
			}
			if(isset($parameter->pict) && $parameter->pict !== 'resized') $parameter->pict = '0';
			$stringTag = $this->acypluginsHelper->managePicts($parameter, $stringTag);
			$this->tags[$oneTag] = $stringTag;
		}
		return $return;
	}

	function _replaceAlbums(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		$db = JFactory::getDBO();
		$time = time();

		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'easysocialalbums');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		if(empty($this->tags)) $this->tags = array();
		if(empty($tags)) return $return;

		$config = FD::config();
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'storage'.DS.'storage.php');
		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysocial'.DS.'includes'.DS.'photos'.DS.'photos.php');
		$storage = new SocialStorage($config->get('storage.photos', 'joomla'));

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])) continue;

			if(!empty($parameter->display)){
				$parameter->display = explode(',', $parameter->display);

				foreach($parameter->display as $i => $oneDisplay){
					$oneDisplay = trim($oneDisplay);
					$parameter->$oneDisplay = true;
				}
				unset($parameter->display);
			}

			$query = 'SELECT a.*, b.name, c.address FROM #__social_albums AS a LEFT JOIN #__users AS b ON a.user_id = b.id LEFT JOIN #__social_locations AS c ON a.id = c.uid AND c.type = "albums" ';

			$where = array();

			$where[] = 'a.core != 1 AND a.core != 2 AND a.core != 3';

			if(!empty($parameter->type)){
				$types = explode(',', $parameter->type);
				foreach($types as &$oneType){
					$oneType = $db->quote($oneType);
				}
				$where[] = '(a.type = '.implode(' OR a.type = ', $types).')';
			}

			$where[] = 'a.id NOT IN (SELECT uid FROM #__social_privacy_items WHERE type LIKE "albums" AND value <> 0)';

			// filter results newly created
			if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate']) && $parameter->filter == 'created'){
				$where[] = 'a.created > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
			}

			// Apply filters
			if(!empty($where)) $query .= ' WHERE ('.implode(') AND (', $where).')';

			// Ordering
			if(!empty($parameter->order)){
				if($parameter->order == 'rand'){
					$query .= ' ORDER BY rand()';
				}else{
					$ordering = explode(',', $parameter->order);
					$query .= ' ORDER BY a.'.acymailing_secureField(trim($ordering[0])).' '.acymailing_secureField(trim($ordering[1]));
				}
			}

			// If there is no limit, then we add one
			if(!empty($parameter->max)){
				$query .= ' LIMIT '.intval($parameter->max);
			}else{
				$query .= ' LIMIT 20';
			} // limit, because we do not want to display one bilion content in the newsletter...

			$db->setQuery($query);
			$allArticles = $db->loadObjectList();
			// Check the number of results
			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				//We will not generate the Newsletter
				$return->status = false;
				$return->message = 'Not enough '.$this->name.' albums for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min;
			}

			$stringTag = '';
			if(!empty($allArticles)){
				//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
				$arrayElements = array();
				foreach($allArticles as $oneArticle){
					$varFields = array();
					foreach($oneArticle as $fieldName => $oneField){
						$oneArticle->$fieldName = JText::_($oneField);
						$varFields['{'.$fieldName.'}'] = $oneField;
					}
					$link = 'index.php?option=com_easysocial&view=albums&id='.$oneArticle->id.'&layout=item';
					$varFields['{link}'] = $link;

					$result = '<div class="acymailing_content"><table cellspacing="0" cellpadding="0" border="0" width="100%">';

					if(!empty($parameter->title)){
						$result .= '<tr><td colspan="2"><h2 class="acymailing_title">';
						if(empty($parameter->nolink)) $result .= '<a href="'.$link.'">';
						$result .= $oneArticle->title;
						if(empty($parameter->nolink)) $result .= '</a>';
						$result .= '</h2></td></tr>';
					}

					if(!isset($parameter->pict) || $parameter->pict !== 0){
						$query = 'SELECT a.*, b.value AS path '.'FROM #__social_photos AS a '.'JOIN #__social_photos_meta AS b '.'ON a.id = b.photo_id '.'WHERE b.group = "path" '.'AND b.property LIKE "original" '.'AND a.album_id = '.intval($oneArticle->id).' '.'AND a.id NOT IN (SELECT uid FROM #__social_privacy_items WHERE type LIKE "photos" AND value <> 0) '.'GROUP BY a.id ';
						if(!empty($parameter->pictnb)){
							$query .= 'LIMIT '.intval($parameter->pictnb);
						}else $query .= 'LIMIT 20';

						$db->setQuery($query);
						$images = $db->loadObjectList();
						$result .= '<tr><td colspan="2" align="center" style="width:100%">';
						$varFields['{images}'] = '';
						foreach($images as $i => $oneImage){
							$oneImage->path = trim($oneImage->path, '\\/');
							if(!file_exists(JPATH_SITE.DS.$oneImage->path)) $oneImage->path = $storage->getPermalink(SocialPhotos::getStoragePath($oneImage->album_id, $oneImage->id, false).substr($oneImage->path, strrpos($oneImage->path, '/')));
							$linkimg = 'index.php?option=com_easysocial&view=photos&layout=item&id='.$oneImage->id.'&type='.$oneImage->type.'&uid='.$oneImage->uid;
							$varFields['{images}'] .= '<a href="'.$linkimg.'"><img style="padding:5px;" src="'.$oneImage->path.'"/></a>';
						}
						if(!empty($varFields['{images}'])) $result .= $varFields['{images}'];
						$result .= '</td></tr>';
					}

					if(!empty($parameter->description) && !empty($oneArticle->caption)){
						$result .= '<tr><td colspan="2">'.$acypluginsHelper->wrapText($oneArticle->caption, $parameter).'</td></tr>';
					}

					if(!empty($parameter->author) && !empty($oneArticle->name)){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('ACY_AUTHOR').' :</td><td class="acyvalueeasysocial">'.$oneArticle->name.'</td></tr>';
					}

					if(!empty($parameter->location) && !empty($oneArticle->address)){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('COM_EASYSOCIAL_LOCATION').' :</td><td class="acyvalueeasysocial">'.$oneArticle->address.'</td></tr>';
					}

					if(!empty($parameter->created) && !empty($oneArticle->created) && $oneArticle->created != '0000-00-00 00:00:00'){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('CREATED_DATE').' :</td><td class="acyvalueeasysocial">'.acymailing_getDate(acymailing_getTime($oneArticle->created), JText::_('DATE_FORMAT_LC')).'</td></tr>';
					}

					if(!empty($parameter->assigned) && !empty($oneArticle->assigned_date) && $oneArticle->assigned_date != '0000-00-00 00:00:00'){
						$result .= '<tr><td class="acylabeleasysocial">'.JText::_('FIELD_DATE').' :</td><td class="acyvalueeasysocial">'.acymailing_getDate(acymailing_getTime($oneArticle->assigned_date), JText::_('DATE_FORMAT_LC')).'</td></tr>';
					}

					if(empty($parameter->noreadmore)){
						$result .= '<tr><td colspan="2"><br /><a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$this->readmore.'</span></a></td></tr>';
					}

					$result = $result.'</table></div>';

					// If the user created its own template...
					if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'album.php')){
						ob_start();
						require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'album.php');
						$result = ob_get_clean();
					}
					$arrayElements[] = $result;
				}
				$stringTag = $acypluginsHelper->getFormattedResult($arrayElements, $parameter);
			}
			if(isset($parameter->pict) && $parameter->pict !== 'resized') $parameter->pict = '0';
			$stringTag = $acypluginsHelper->managePicts($parameter, $stringTag);
			$this->tags[$oneTag] = $stringTag;
		}
		return $return;
	}

	function _replaceProfiles(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		$db = JFactory::getDBO();
		$time = time();

		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'easysocialprofiles');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		if(empty($this->tags)) $this->tags = array();
		if(empty($tags)) return $return;

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])) continue;

			if(!empty($parameter->display)){
				$parameter->display = explode(',', $parameter->display);

				foreach($parameter->display as $i => $oneDisplay){
					$oneDisplay = trim($oneDisplay);
					$parameter->$oneDisplay = true;
				}
				unset($parameter->display);
			}

			$query = 'SELECT a.*, b.large, b.type FROM #__social_profiles AS a LEFT JOIN #__social_avatars AS b ON a.id = b.uid';

			$where = array();

			// filter results newly created
			if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate']) && $parameter->filter == 'created'){
				$where[] = 'a.created > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
			}

			// Only published content
			$where[] = 'a.state = 1';

			// Apply filters
			if(!empty($where)) $query .= ' WHERE ('.implode(') AND (', $where).')';

			// Ordering
			if(!empty($parameter->order)){
				if($parameter->order == 'rand'){
					$query .= ' ORDER BY rand()';
				}else{
					$ordering = explode(',', $parameter->order);
					$query .= ' ORDER BY a.'.acymailing_secureField(trim($ordering[0])).' '.acymailing_secureField(trim($ordering[1]));
				}
			}

			// If there is no limit, then we add one
			if(!empty($parameter->max)){
				$query .= ' LIMIT '.intval($parameter->max);
			}else{
				$query .= ' LIMIT 20';
			} // limit, because we do not want to display one bilion content in the newsletter...

			$db->setQuery($query);
			$allArticles = $db->loadObjectList();
			if(!empty($allArticles)){
				foreach($allArticles as $i => $oneArticle){
					if(!empty($oneArticle->type) && $oneArticle->type != 'profiles') unset($allArticles[$i]);
				}
			}
			// Check the number of results
			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				//We will not generate the Newsletter
				$return->status = false;
				$return->message = 'Not enough '.$this->name.' profiles for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min;
			}

			$stringTag = '';
			if(!empty($allArticles)){
				// If the user created its own template...
				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'profiles.php')){
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'profiles.php');
					$stringTag = ob_get_clean();
				}else{
					//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
					$arrayElements = array();
					foreach($allArticles as $oneArticle){
						$varFields = array();
						foreach($oneArticle as $key => $oneField){
							$varFields['{'.$key.'}'] = $oneField;
						}
						$varFields['{link}'] = 'index.php?option=com_easysocial&view=registration';
						$result = '<div class="acymailing_content"><table cellspacing="0" cellpadding="0" border="0" width="100%">';

						if(!empty($parameter->title)){
							$result .= '<tr><td colspan="2"><h2 class="acymailing_title">';
							if(empty($parameter->nolink)) $result .= '<a href="'.$varFields['{link}'].'">';
							$result .= $oneArticle->title;
							if(empty($parameter->nolink)) $result .= '</a>';
							$result .= '</h2></td></tr>';
						}

						if(!((empty($parameter->image) || empty($oneArticle->large)) && (empty($parameter->description) || empty($oneArticle->description)))){
							$result .= '<tr><td colspan="2">';
							if(!empty($parameter->image) && !empty($oneArticle->large)){
								$varFields['{imagehtml}'] = '<img style="float:left;padding-right:5px;" src="media/com_easysocial/avatars/profiles/'.$oneArticle->id.'/'.$oneArticle->large.'"/>';
								$result .= $varFields['{imagehtml}'];
							}

							if(!empty($parameter->description) && !empty($oneArticle->description)){
								$result .= $acypluginsHelper->wrapText($oneArticle->description, $parameter);
							}
							$result .= '</td></tr>';
						}

						if(!empty($parameter->groups)){
							$labels = array();
							$groups = json_decode($oneArticle->gid);
							$i = 0;
							$varFields['{groups}'] = '';
							foreach($this->usergroups as $group){
								if(!in_array($group->id, $groups)) continue;
								$varFields['{groups}'] .= ', '.$group->title;
								if($i != 0){
									$labels[] = '<tr><td></td><td>'.$group->title.'</td></tr>';
								}else{
									$labels[] = '<tr><td>'.JText::_('ACY_GROUP').' :</td><td>'.$group->title.'</td></tr>';
									$i++;
								}
							}
							$varFields['{groups}'] = trim($varFields['{groups}'], ',');

							$result .= implode('', $labels);
						}

						if(!empty($parameter->created) && !empty($oneArticle->created) && $oneArticle->created != '0000-00-00 00:00:00'){
							$result .= '<tr><td>'.JText::_('CREATED_DATE').' :</td><td>'.acymailing_getDate(acymailing_getTime($oneArticle->created), JText::_('DATE_FORMAT_LC')).'</td></tr>';
						}

						// If the user created its own template...
						if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'profile.php')){
							ob_start();
							require(ACYMAILING_MEDIA.'plugins'.DS.$this->name.'profile.php');
							$result = ob_get_clean();
							$result = str_replace(array_keys($varFields), $varFields, $result);
						}
						$arrayElements[] = $result.'</table></div>';
					}
					$stringTag = $acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}
			}
			if(isset($parameter->pict) && $parameter->pict !== 'resized') $parameter->pict = '0';
			$stringTag = $acypluginsHelper->managePicts($parameter, $stringTag);
			$this->tags[$oneTag] = $stringTag;
		}
		return $return;
	}

	function _loadJavascript(){
		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedCat = new Array();
			function applyAuto(catid, rowClass){
				if(catid == 'all'){
					if(window.document.getElementById('cat' + catid).className == 'selectedrow'){
						window.document.getElementById('catall').className = rowClass;
					}else{
						window.document.getElementById('cat' + catid).className = 'selectedrow';
						for(key in selectedCat){
							if(!isNaN(key)){
								window.document.getElementById('cat' + key).className = rowClass;
								delete selectedCat[key];
							}
						}
					}
				}else{
					window.document.getElementById('catall').className = 'row0';
					if(selectedCat[catid]){
						window.document.getElementById('cat' + catid).className = rowClass;
						delete selectedCat[catid];
					}else{
						window.document.getElementById('cat' + catid).className = 'selectedrow';
						selectedCat[catid] = 'selectedone';
					}
				}
				updateTag('events');
			}

			var userFieldsTable = 0;
			function updateFields(tabname){
				if(document.adminForm[tabname + '_profile'].length && document.adminForm[tabname + '_profile'].value){
					if(userFieldsTable != 0 && document.getElementById(tabname + '_' + userFieldsTable)){
						document.getElementById(tabname + '_' + userFieldsTable).style.display = 'none';
					}
					userFieldsTable = document.getElementById(tabname + '_profile').value;
					if(document.getElementById(tabname + '_' + userFieldsTable)){
						document.getElementById(tabname + '_' + userFieldsTable).style.display = '';
					}
				}
			}
			function updateTag(tabname){
				var tag = '{easysocial' + tabname + ':';
				var tmp = 0;
				var displayField = '_cbdisplay';

				if(tabname == 'events'){
					var displayCalendar = document.getElementsByClassName('calendardisplays');
					var displayEvent = document.getElementsByClassName('eventsdisplays');
					if(document.adminForm['chooseevents_type'].value == 'calendar'){
						displayField = '_cbdisplaycalendar';
						for(var i = 0; i < displayCalendar.length; i++){
							displayCalendar[i].style.display = '';
						}
						for(var i = 0; i < displayEvent.length; i++){
							displayEvent[i].style.display = 'none';
						}
						document.getElementById('events_categories').style.display = 'none';
					}else{
						tag = '{easysocialrevents:';
						for(var i = 0; i < displayCalendar.length; i++){
							displayCalendar[i].style.display = 'none';
						}
						for(var i = 0; i < displayEvent.length; i++){
							displayEvent[i].style.display = '';
						}
						document.getElementById('events_categories').style.display = '';

						for(var icat in selectedCat){
							if(selectedCat[icat] == 'selectedone'){
								tag += icat + '-';
							}
						}

						if(document.adminForm['josevents_type'][0].checked || document.adminForm['josevents_type'][1].checked){
							var entities = [];
							if(document.adminForm['josevents_type'][0].checked){
								entities.push('user');
							}
							if(document.adminForm['josevents_type'][1].checked){
								entities.push('group');
							}
							if(entities.length != 2) tag += '| creator:' + entities.join(',');
						}

						for(var i = 0; i < document.adminForm.pict.length; i++){
							if(!document.adminForm.pict[i].checked) continue;
							if(document.adminForm.pict[i].value != '1') tag += '| pict:' + document.adminForm.pict[i].value;

							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) tag += '| maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) tag += '| maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
				}

				if(document.adminForm[tabname + '_type']){
					var entities = [];
					for(var i = 0; i < document.adminForm[tabname + '_type'].length; i++){
						if(document.adminForm[tabname + '_type'][i].checked){
							entities.push(document.adminForm[tabname + '_type'][i].value);
						}
					}

					if(entities.length != 0 && entities.length != document.adminForm[tabname + '_type'].length) tag += '| type:' + entities.join(',');
				}

				if(document.adminForm[tabname + displayField]){
					for(var i = 0; i < document.adminForm[tabname + displayField].length; i++){
						if(document.adminForm[tabname + displayField][i].checked){
							if(tmp == 0){
								tmp += 1;
								tag += "| display:";
								tag += document.adminForm[tabname + displayField][i].value;
							}else{
								tag += ", ";
								tag += document.adminForm[tabname + displayField][i].value;
							}
						}
					}
				}

				if(document.adminForm[tabname + '_clickable']){
					for(var i = 0; i < document.adminForm[tabname + '_clickable'].length; i++){
						if(document.adminForm[tabname + '_clickable'][i].checked && document.adminForm[tabname + '_clickable'][i].value == '0'){
							tag += '| nolink';
						}
					}
				}

				<?php
					//do we have to wrap the text ? (description for example...)
				?>
				if(document.adminForm[tabname + '_wrap'] && document.adminForm[tabname + '_wrap'].value != 0 && !isNaN(document.adminForm[tabname + '_wrap'].value)){
					tag += "| wrap:" + document.adminForm[tabname + '_wrap'].value;
				}

				<?php
					//do we have to add a "read more" button ?
				?>
				if(document.adminForm[tabname + '_readmore']){
					for(var i = 0; i < document.adminForm[tabname + '_readmore'].length; i++){
						if(document.adminForm[tabname + '_readmore'][i].checked && document.adminForm[tabname + '_readmore'][i].value == '0'){
							tag += '| noreadmore';
						}
					}
				}

				<?php
					//picture management
				?>
				if(document.adminForm[tabname + '_pict']){
					for(var i = 0; i < document.adminForm[tabname + '_pict'].length; i++){
						if(document.adminForm[tabname + '_pict'][i].checked){
							if(document.adminForm[tabname + '_pict'][i].value != '1'){
								tag += '| pict:' + document.adminForm[tabname + '_pict'][i].value;
							}

							if(document.adminForm[tabname + '_pict'][i].value == 'resized'){
								document.getElementById(tabname + '_pictsize').style.display = '';
								if(document.adminForm[tabname + '_pictwidth'].value) tag += '| maxwidth:' + document.adminForm[tabname + '_pictwidth'].value;
								if(document.adminForm[tabname + '_pictheight'].value) tag += '| maxheight:' + document.adminForm[tabname + '_pictheight'].value;
							}else{
								document.getElementById(tabname + '_pictsize').style.display = 'none';
							}

							if(document.adminForm[tabname + '_pict'][i].value == '0'){
								document.getElementById(tabname + '_pictnumber').style.display = 'none';
							}else{
								document.getElementById(tabname + '_pictnumber').style.display = '';
								if(document.adminForm[tabname + '_number'].value) tag += '| pictnb:' + document.adminForm[tabname + '_number'].value;
							}
						}
					}
				}

				if(document.adminForm[tabname + '_profiles'] && document.adminForm[tabname + '_profiles'].value != 0){
					tag += "| profile:" + document.adminForm[tabname + '_profiles'].value;
				}

				<?php
					//on how many columns do we have to display the contents ?
				?>
				if(document.adminForm[tabname + '_cols'] && document.adminForm[tabname + '_cols'].value){
					tag += "| cols:" + document.adminForm[tabname + '_cols'].value;
				}

				<?php
					//limit the number of content displayed
				?>
				if(document.adminForm[tabname + '_max'] && document.adminForm[tabname + '_max'].value){
					tag += "| max:" + document.adminForm[tabname + '_max'].value;
				}

				<?php
					//with what order will we display the contents
				?>
				if(document.adminForm[tabname + '_order'] && document.adminForm[tabname + '_order'].value){
					tag += "| order:" + document.adminForm[tabname + '_order'].value;
				}

				<?php
					//minimum number of content for the newsletter to be displayed... yes, a perfect english !
				?>
				if(document.adminForm[tabname + '_min'] && document.adminForm[tabname + '_min'].value){
					tag += "| min:" + document.adminForm[tabname + '_min'].value;
				}

				<?php
					//filter the content : only newly created/modified
				?>
				if(document.adminForm[tabname + '_filter'] && document.adminForm[tabname + '_filter'].value != 0){
					tag += "| filter:" + document.adminForm[tabname + '_filter'].value;
				}

				if(tabname == "events"){
					if(document.adminForm.mindelayevent && document.adminForm.mindelayevent.value && document.adminForm.mindelayevent.value > 0){
						tag += '| mindelay:' + document.adminForm.mindelayevent.value;
					}
					if(document.adminForm.delayevent && document.adminForm.delayevent.value && document.adminForm.delayevent.value > 0){
						tag += '| delay:' + document.adminForm.delayevent.value;
					}
					if(document.adminForm.from_date && document.adminForm.from_date.value){
						tag += '| from:' + document.adminForm.from_date.value;
					}
					if(document.adminForm.to_date && document.adminForm.to_date.value){
						tag += '| to:' + document.adminForm.to_date.value;
					}
				}

				tag = tag + '}';
				setTag(tag);
			}
			-->
		</script>
	<?php
	}

function _showTabStart($name){
	?>
<br style="font-size:1px"/>
	<table width="100%" class="adminform">
		<tr>
			<td nowrap><label for="<?php echo $name; ?>_max"><?php echo JText::_('MAX_ARTICLE')?></label></td>
			<td><input name="<?php echo $name; ?>_max" id="<?php echo $name; ?>_max" type="text" onchange="updateTag('<?php echo $name; ?>');" value="20" style="width:50px;"/></td>
			<td><label for="<?php echo $name; ?>_cols"><?php echo JText::_('FIELD_COLUMNS')?></label></td>
			<td>
				<?php echo JHTML::_('select.genericlist', $this->column, $name.'_cols', 'style="width:50px;" size="1" onchange="updateTag(\''.$name.'\');"', 'value', 'text', $name.'_cols'); ?>
			</td>
		</tr>
		<?php if(JRequest::getString('type') === 'autonews'){
			?>
			<tr>
				<td nowrap>
					<label for="<?php echo $name; ?>_min"><?php echo JText::_('MIN_ARTICLE') ?></label>
				</td>
				<td>
					<input name="<?php echo $name; ?>_min" id="<?php echo $name; ?>_min" type="text" onchange="updateTag('<?php echo $name; ?>');" value="1" style="width:50px;"/>
				</td>
				<td>
					<?php echo JText::_('JOOMEXT_FILTER'); ?>
				</td>
				<td>
					<?php
					echo JHTML::_('select.genericlist', $this->contentfilter, $name.'_filter', 'size="1" onchange="updateTag(\''.$name.'\');"', 'value', 'text', 'created');
					?>
				</td>
			</tr>
		<?php
		}
		}

		function _showUserFieldsTab($tabs){
			echo $tabs->startPanel(JText::_('USER_FIELDS'), $this->name.'_userfields');
			acymailing_display('If the user does not have the selected field or if this one is empty, the tag will be removed in the generated newsletter', 'notice');
			echo '<br style="font-size:1px"/>';

			$db = JFactory::getDBO();
			$db->setQuery('SELECT a.id, a.title, c.uid FROM #__social_fields AS a JOIN #__social_fields_steps AS c ON a.step_id = c.id WHERE c.type = "profiles" AND a.unique_key NOT LIKE "'.implode('%" AND a.unique_key NOT LIKE "', array("JOOMLA_", "HEADER", "SEPARATOR", "TERMS", "COVER", "AVATAR", "HTML", "TEXT-", "FILE", "CURRENCY")).'%"');
			$fields = $db->loadObjectList();

			$db->setQuery('SELECT id, title FROM #__social_profiles');
			$profiles = $db->loadObjectList();
			$profilesList = array();
			$profilesList[] = JHTML::_('select.option', 0, JText::_('COM_EASYSOCIAL_REGISTRATIONS_SELECT_PROFILE_TYPE_TITLE'));
			foreach($profiles as $oneProfile){
				$profilesList[] = JHTML::_('select.option', $oneProfile->id, $oneProfile->title);
			}

			echo JHTML::_('select.genericlist', $profilesList, 'userfields_profile', 'style="margin-bottom: 0;width:150px;" onchange="updateFields(\'userfields\');"');

			$tables = array();
			foreach($fields as $oneField){
				if(!isset($tables[$oneField->uid])) $tables[$oneField->uid] = '<table id="userfields_'.$oneField->uid.'" class="adminlist table table-striped table-hover" cellpadding="1" style="display:none;width:100%;">';
				$tables[$oneField->uid] .= '<tr style="cursor:pointer" class="row1" onclick="setTag(\'{easysocialfields:'.$oneField->id.'}\');insertTag();" ><td class="acytdcheckbox"/><td>'.JText::_($oneField->title).'</td></tr>';
			}

			foreach($tables as $i => $table){
				$tables[$i] .= '</table>';
			}

			echo implode($tables);
			echo $tabs->endPanel();
		}

		function _showEventsTab($tabs){
		echo $tabs->startPanel(JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_EVENTS'), $this->name.'_events');
		$orderBy = array();
		$orderBy[] = JHTML::_('select.option', "id,DESC", JText::_('ACY_ID'));
		$orderBy[] = JHTML::_('select.option', "title,ASC", JText::_('FIELD_TITLE'));
		$orderBy[] = JHTML::_('select.option', 'date_start,DESC', JText::_('APP_CALENDAR_CREATE_NEW_SCHEDULE_STARTDATE'));
		$orderBy[] = JHTML::_('select.option', 'date_end,DESC', JText::_('APP_CALENDAR_CREATE_NEW_SCHEDULE_ENDDATE'));
		$orderBy[] = JHTML::_('select.option', 'rand', JText::_('ACY_RANDOM'));
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td nowrap="nowrap"><?php echo JText::_('CLICKABLE_TITLE'); ?></td>
				<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'events_clickable', 'size="1" onclick="updateTag(\'events\');"', 'value', 'text', 1); ?></td>

				<td nowrap="nowrap"><?php echo JText::_('JOOMEXT_READ_MORE'); ?></td>
				<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'events_readmore', 'size="1" onclick="updateTag(\'events\');"', 'value', 'text', 1); ?></td>
			</tr>
			<tr>
				<td><label for="events_order"><?php echo JText::_('ACY_ORDER'); ?></label></td>
				<td><?php echo JHTML::_('select.genericlist', $orderBy, 'events_order', 'style="margin-bottom: 0;width:150px;" onchange="updateTag(\'events\');"'); ?></td>
				<td nowrap="nowrap" colspan="2"><?php echo JText::sprintf('TRUNCATE_AFTER', '<input type="text" name="events_wrap" style="width:50px" value="0" onchange="updateTag(\'events\');"/>'); ?></td>
			</tr>
			<tr>
				<td nowrap><label for="events_max"><?php echo JText::_('MAX_ARTICLE')?></label></td>
				<td><input name="events_max" id="events_max" type="text" onchange="updateTag('events');" value="20" style="width:50px;"/></td>
				<td><label for="events_cols"><?php echo JText::_('FIELD_COLUMNS')?></label></td>
				<td>
					<?php echo JHTML::_('select.genericlist', $this->column, 'events_cols', 'style="width:50px;" size="1" onchange="updateTag(\'events\');"', 'value', 'text', 'events_cols'); ?>
				</td>
			</tr>
			<?php if($this->type == 'autonews'){ ?>
				<tr>
					<td>
						<?php echo JText::_('MIN_ARTICLE'); ?>
					</td>
					<td colspan="3">
						<input type="text" name="events_min" style="width:50px" value="1" onchange="updateTag('events');"/>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('ACY_FROM_DATE'); ?>
					</td>
					<td>
						<?php $delayType = acymailing_get('type.delay');
						$delayType->onChange = 'updateTag(\'events\');';
						echo $delayType->display('mindelayevent', 0, 3); ?>
					</td>
					<td nowrap="nowrap">
						<?php echo JText::_('ACY_TO_DATE'); ?>
					</td>
					<td nowrap="nowrap">
						<?php $delayType = acymailing_get('type.delay');
						$delayType->onChange = 'updateTag(\'events\');';
						echo $delayType->display('delayevent', 7776000, 3); ?>
					</td>
				</tr>
			<?php }else{ ?>
				<tr>
					<td>
						<?php echo JText::_('ACY_FROM_DATE'); ?>
					</td>
					<td>
						<?php echo JHTML::_('calendar', '', 'from_date', 'from_date', '%Y-%m-%d', array('style' => 'width:80px', 'onchange' => 'updateTag(\'events\');')); ?>
					</td>
					<td>
						<?php echo JText::_('ACY_TO_DATE'); ?>
					</td>
					<td>
						<?php echo JHTML::_('calendar', '', 'to_date', 'to_date', '%Y-%m-%d', array('style' => 'width:80px', 'onchange' => 'updateTag(\'events\');')); ?>
					</td>
				</tr>
			<?php }

			$fieldsDisplayCalendar = array();
			$fieldsDisplayCalendar[] = array('title' => 'title', 'label' => 'ACY_TITLE', 'checked' => 'yes');
			$fieldsDisplayCalendar[] = array('title' => 'description', 'label' => 'ACY_DESCRIPTION', 'checked' => 'yes');
			$fieldsDisplayCalendar[] = array('title' => 'author', 'label' => 'ACY_AUTHOR', 'checked' => '');
			$fieldsDisplayCalendar[] = array('title' => 'date', 'label' => 'APP_CALENDAR_CREATE_NEW_SCHEDULE_STARTDATE', 'checked' => 'yes');
			$fieldsDisplayCalendar[] = array('title' => 'endate', 'label' => 'APP_CALENDAR_CREATE_NEW_SCHEDULE_ENDDATE', 'checked' => 'yes');

			$fieldsDisplay = $fieldsDisplayCalendar;
			$fieldsDisplay[] = array('title' => 'avatar', 'label' => 'PLG_FIELDS_AVATAR_DEFAULT_TITLE', 'checked' => 'yes');
			$fieldsDisplay[] = array('title' => 'location', 'label' => 'COM_EASYSOCIAL_LOCATION_POSTED_FROM', 'checked' => 'yes');
			$fieldsDisplay[] = array('title' => 'guestlim', 'label' => 'FIELDS_EVENT_GUESTLIMIT_DEFAULT_TITLE', 'checked' => '');
			$fieldsDisplay[] = array('title' => 'website', 'label' => 'PLG_FIELDS_URL_DEFAULT_TITLE', 'checked' => '');

			$eventType = array();
			$eventType[] = JHTML::_('select.option', "calendar", JText::_('COM_EASYSOCIAL_STREAM_APP_FILTER_CALENDAR').' <small>('.JText::_('COM_EASYSOCIAL_STREAM_CONTEXT_TITLE_APPS').')</small>');
			$eventType[] = JHTML::_('select.option', "event", JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_EVENTS'));

			$valImages = array();
			$valImages[] = JHTML::_('select.option', "1", JText::_('JOOMEXT_YES'));
			$valImages[] = JHTML::_('select.option', "resized", JText::_('RESIZED'));
			$valImages[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));
			?>
			<tr>
				<td>
					<?php echo JText::_('ACY_TYPE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $eventType, 'chooseevents_type', 'size="1" onclick="updateTag(\'events\');"', 'value', 'text', 'calendar'); ?>
				</td>
				<td colspan="2">
					<div class="eventsdisplays" style="display:none;">
						<input type="checkbox" name="josevents_type" id="josevents_type1" onclick="updateTag('events');" checked="checked"/>
						<label for="josevents_type1"><?php echo JText::_('COM_EASYSOCIAL_ROUTER_FIELDS_USER') ?></label>
						<input type="checkbox" name="josevents_type" id="josevents_type2" onclick="updateTag('events');" checked="checked"/>
						<label for="josevents_type2"><?php echo JText::_('COM_EASYSOCIAL_ROUTER_DASHBOARD_GROUP') ?></label>
					</div>
				</td>
			</tr>
			<tr class="calendardisplays">
				<td nowrap="nowrap"><?php echo JText::_('DISPLAY');?></td>
				<?php
				$i = 1;
				foreach($fieldsDisplayCalendar as $oneField){
					if($i == 4){
						echo '</tr><tr class="calendardisplays"><td/>';
						$i = 1;
					}
					echo '<td nowrap="nowrap"><input type="checkbox" name="events_cbdisplaycalendar" value="'.$oneField['title'].'" id="'.$oneField['title'].'eventscalendar" '.(($oneField['checked'] == 'yes') ? 'checked' : '').' onclick="updateTag(\'events\');"/><label style="margin-left:5px" for="'.$oneField['title'].'eventscalendar">'.JText::_($oneField['label']).'</label></td>';
					$i++;
				}
				while($i != 4){
					echo '<td/>';
					$i++;
				}
				?>
			</tr>
			<tr class="eventsdisplays" style="display:none;">
				<td nowrap="nowrap"><?php echo JText::_('DISPLAY');?></td>
				<?php
				$i = 1;
				foreach($fieldsDisplay as $oneField){
					if($i == 4){
						echo '</tr><tr class="eventsdisplays" style="display:none;"><td/>';
						$i = 1;
					}
					echo '<td nowrap="nowrap"><input type="checkbox" name="events_cbdisplay" value="'.$oneField['title'].'" id="'.$oneField['title'].'events" '.(($oneField['checked'] == 'yes') ? 'checked' : '').' onclick="updateTag(\'events\');"/><label style="margin-left:5px" for="'.$oneField['title'].'events">'.JText::_($oneField['label']).'</label></td>';
					$i++;
				}
				while($i != 4){
					echo '<td/>';
					$i++;
				}
				?>
			</tr>
			<tr class="eventsdisplays" style="display:none;">
				<td nowrap="nowrap" valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
				<td nowrap="nowrap" colspan="3"><?php echo JHTML::_('acyselect.radiolist', $valImages, 'pict', 'size="1" onclick="updateTag(\'events\');"', 'value', 'text', 1); ?>
					<span id="pictsize" style="display:none;"><br/>
						<?php echo JText::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag('events');" value="150" style="width:30px;"/>
						x <?php echo JText::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheight" type="text" onchange="updateTag('events');" value="150" style="width:30px;"/>
					</span>
				</td>
			</tr>
			<?php

			echo '</table>';
			$db = JFactory::getDBO();
			$db->setQuery('SELECT id, title, description FROM #__social_clusters_categories WHERE type = "event" AND state = 1');
			$categories = $db->loadObjectList();

			?>
			<table style="display:none;" id="events_categories" class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
				<thead>
				<tr>
					<th class="title"></th>
					<th class="title">
						<?php echo JText::_('TAG_CATEGORIES'); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<tr id="catall" class="<?php echo "row1"; ?>" onclick="applyAuto('all','<?php echo "row1" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td><?php echo JText::_('ACY_ALL'); ?></td>
				</tr>
				<?php foreach($categories as $index => $cat){ ?>
				<tr id="cat<?php echo $cat->id ?>" class="row<?php echo $index % 2; ?>" onclick="applyAuto('<?php echo $cat->id ?>','<?php echo "row".($index % 2); ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td title="<?php echo $cat->description ?>"><?php echo $cat->title; ?></td>
					</tr><?php
				} ?>
				</tbody>
			</table>
			<?php
			echo $tabs->endPanel();
			}

			function _showAlbumsTab($tabs){
				echo $tabs->startPanel(JText::_('COM_EASYSOCIAL_PRIVACY_GROUP_ALBUMS'), $this->name.'_albums');

				$this->_showTabStart('albums');

				$orderBy = array();
				$orderBy[] = JHTML::_('select.option', "id,DESC", JText::_('ACY_ID'));
				$orderBy[] = JHTML::_('select.option', "title,ASC", JText::_('FIELD_TITLE'));
				$orderBy[] = JHTML::_('select.option', 'created,DESC', JText::_('CREATED_DATE'));
				$orderBy[] = JHTML::_('select.option', 'assigned_date,DESC', JText::_('FIELD_DATE'));
				$orderBy[] = JHTML::_('select.option', 'ordering,ASC', JText::_('ACY_ORDERING'));
				$orderBy[] = JHTML::_('select.option', 'rand', JText::_('ACY_RANDOM'));

				?>
				<tr>
					<td><label for="users_order"><?php echo JText::_('ACY_ORDER'); ?></label></td>
					<td><?php echo JHTML::_('select.genericlist', $orderBy, 'albums_order', 'style="margin-bottom: 0;width:150px;" onchange="updateTag(\'albums\');"'); ?></td>
					<td colspan="2"/>
				</tr>
				<?php

				$fieldsDisplay = array();
				$fieldsDisplay[] = array('title' => 'title', 'label' => 'ACY_TITLE', 'checked' => 'yes');
				$fieldsDisplay[] = array('title' => 'description', 'label' => 'ACY_DESCRIPTION', 'checked' => 'yes');
				$fieldsDisplay[] = array('title' => 'author', 'label' => 'ACY_AUTHOR', 'checked' => 'yes');
				$fieldsDisplay[] = array('title' => 'created', 'label' => 'CREATED_DATE', 'checked' => '');
				$fieldsDisplay[] = array('title' => 'assigned', 'label' => 'FIELD_DATE', 'checked' => '');
				$fieldsDisplay[] = array('title' => 'location', 'label' => 'COM_EASYSOCIAL_LOCATION', 'checked' => '');

				?>
				<tr>
					<?php
					// what should we display ?
					?>
					<td nowrap="nowrap"><?php echo JText::_('DISPLAY');?></td>
					<?php
					$i = 1;
					foreach($fieldsDisplay as $oneField){
						if($i == 4){
							echo '</tr><tr><td/>';
							$i = 1;
						}
						echo '<td nowrap="nowrap"><input type="checkbox" name="albums_cbdisplay" value="'.$oneField['title'].'" id="'.$oneField['title'].'albums" '.(($oneField['checked'] == 'yes') ? 'checked' : '').' onclick="updateTag(\'albums\');"/><label style="margin-left:5px" for="'.$oneField['title'].'albums">'.JText::_($oneField['label']).'</label></td>';
						$i++;
					}
					while($i != 4){
						echo '<td/>';
						$i++;
					}
					?>
				</tr>
				<tr>
					<td nowrap="nowrap"><?php echo JText::_('CLICKABLE_TITLE'); ?></td>
					<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'albums_clickable', 'size="1" onclick="updateTag(\'albums\');"', 'value', 'text', 1); ?></td>

					<td nowrap="nowrap"><?php echo JText::_('JOOMEXT_READ_MORE'); ?></td>
					<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'albums_readmore', 'size="1" onclick="updateTag(\'albums\');"', 'value', 'text', 1); ?></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
					<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->valImages, 'albums_pict', 'size="1" onclick="updateTag(\'albums\');"', 'value', 'text', 1); ?>
						<span id="albums_pictsize" style="display:none;"><br/>
							<?php echo JText::_('CAPTCHA_WIDTH') ?>
							<input name="albums_pictwidth" type="text" onchange="updateTag('albums');" value="150" style="width:30px;"/>
					x <?php echo JText::_('CAPTCHA_HEIGHT') ?>
							<input name="albums_pictheight" type="text" onchange="updateTag('albums');" value="150" style="width:30px;"/>
				</span><br/>
				<span id="albums_pictnumber">
					<?php echo JText::_('APP_PHOTOS_PROFILE_NOTIFICATION_TITLE'); ?>
					<input name="albums_number" type="text" onchange="updateTag('albums');" value="5" style="width:30px;"/>
				</span>
					</td>
					<td nowrap="nowrap" colspan="2" valign="top"><?php echo JText::sprintf('TRUNCATE_AFTER', '<input type="text" name="albums_wrap" style="width:50px" value="0" onchange="updateTag(\'albums\');"/>'); ?></td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('ACY_TYPE'); ?>
					</td>
					<td colspan="3">
						<input type="checkbox" name="albums_type" id="albums_type1" onclick="updateTag('albums');" value="user" checked="checked"/>
						<label for="albums_type1"><?php echo JText::_('COM_EASYSOCIAL_ROUTER_FIELDS_USER'); ?></label>
						<input type="checkbox" name="albums_type" id="albums_type2" onclick="updateTag('albums');" value="group" checked="checked"/>
						<label for="albums_type2"><?php echo JText::_('COM_EASYSOCIAL_ROUTER_DASHBOARD_GROUP'); ?></label>
						<input type="checkbox" name="albums_type" id="albums_type3" onclick="updateTag('albums');" value="event" checked="checked"/>
						<label for="albums_type3"><?php echo JText::_('COM_EASYSOCIAL_ROUTER_FIELDS_EVENT'); ?></label>
					</td>
				</tr>
				<?php

				echo '</table>';
				echo $tabs->endPanel();
			}

			function _showProfilesTab($tabs){
				echo $tabs->startPanel(JText::_('COM_EASYSOCIAL_STREAM_CONTEXT_TITLE_PROFILES'), $this->name.'_profiles');

				$this->_showTabStart('profiles');

				$orderBy = array();
				$orderBy[] = JHTML::_('select.option', "id,DESC", JText::_('ACY_ID'));
				$orderBy[] = JHTML::_('select.option', "title,ASC", JText::_('FIELD_TITLE'));
				$orderBy[] = JHTML::_('select.option', 'created,DESC', JText::_('CREATED_DATE'));
				$orderBy[] = JHTML::_('select.option', 'ordering,ASC', JText::_('ACY_ORDERING'));
				$orderBy[] = JHTML::_('select.option', 'rand', JText::_('ACY_RANDOM'));

				?>
				<tr>
					<td><label for="users_order"><?php echo JText::_('ACY_ORDER'); ?></label></td>
					<td><?php echo JHTML::_('select.genericlist', $orderBy, 'profiles_order', 'style="margin-bottom: 0;width:150px;" onchange="updateTag(\'profiles\');"'); ?></td>
					<td nowrap="nowrap" colspan="2"><?php echo JText::sprintf('TRUNCATE_AFTER', '<input type="text" name="profiles_wrap" style="width:50px" value="0" onchange="updateTag(\'profiles\');"/>'); ?></td>
				</tr>
				<tr>
					<td nowrap="nowrap"><?php echo JText::_('CLICKABLE_TITLE'); ?></td>
					<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'profiles_clickable', 'size="1" onclick="updateTag(\'profiles\');"', 'value', 'text', 1); ?></td>
					<td colspan="2"/>
				</tr>
				<?php

				$fieldsDisplay = array();
				$fieldsDisplay[] = array('title' => 'title', 'label' => 'ACY_TITLE', 'checked' => 'yes');
				$fieldsDisplay[] = array('title' => 'image', 'label' => 'ACY_IMAGE', 'checked' => 'yes');
				$fieldsDisplay[] = array('title' => 'description', 'label' => 'ACY_DESCRIPTION', 'checked' => 'yes');
				$fieldsDisplay[] = array('title' => 'groups', 'label' => 'ACY_GROUP', 'checked' => '');
				$fieldsDisplay[] = array('title' => 'created', 'label' => 'CREATED_DATE', 'checked' => '');

				?>
				<tr>
					<?php
					// what should we display ?
					?>
					<td nowrap="nowrap"><?php echo JText::_('DISPLAY');?></td>
					<?php
					$i = 1;
					foreach($fieldsDisplay as $oneField){
						if($i == 4){
							echo '</tr><tr><td/>';
							$i = 1;
						}
						echo '<td nowrap="nowrap"><input type="checkbox" name="profiles_cbdisplay" value="'.$oneField['title'].'" id="'.$oneField['title'].'profiles" '.(($oneField['checked'] == 'yes') ? 'checked' : '').' onclick="updateTag(\'profiles\');"/><label style="margin-left:5px" for="'.$oneField['title'].'profiles">'.JText::_($oneField['label']).'</label></td>';
						$i++;
					}
					while($i != 4){
						echo '<td/>';
						$i++;
					}
					?>
				</tr>
				<?php

				echo '</table>';
				echo $tabs->endPanel();
			}

			function _showUsersTab($tabs){
			echo $tabs->startPanel(JText::_('COM_EASYSOCIAL_STREAM_CONTEXT_TITLE_USERS'), $this->name.'_users');

			$this->_showTabStart('users');

			$orderBy = array();
			$orderBy[] = JHTML::_('select.option', "id,DESC", JText::_('ACY_ID'));
			$orderBy[] = JHTML::_('select.option', "name,ASC", JText::_('ACY_NAME'));
			$orderBy[] = JHTML::_('select.option', 'registerDate,DESC', JText::_('CREATED_DATE'));
			$orderBy[] = JHTML::_('select.option', 'rand', JText::_('ACY_RANDOM'));

			$db = JFactory::getDBO();
			$db->setQuery('SELECT id, title FROM #__social_profiles WHERE state = 1');
			$allProfiles = $db->loadObjectList();

			$profiles = array();
			$profiles[] = JHTML::_('select.option', 0, '- - -');
			foreach($allProfiles as $oneProfile){
				$profiles[] = JHTML::_('select.option', $oneProfile->id, $oneProfile->title);
			}

			?>
			<tr>
				<td><label for="users_order"><?php echo JText::_('ACY_ORDER'); ?></label></td>
				<td><?php echo JHTML::_('select.genericlist', $orderBy, 'users_order', 'style="margin-bottom: 0;width:150px;" onchange="updateTag(\'users\');"'); ?></td>
				<td><?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_PROFILE'); ?></td>
				<td><?php echo JHTML::_('select.genericlist', $profiles, 'users_profiles', 'style="margin-bottom: 0;width:150px;" onchange="updateTag(\'users\');"'); ?></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><?php echo JText::_('CLICKABLE_TITLE'); ?></td>
				<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'users_clickable', 'size="1" onclick="updateTag(\'users\');"', 'value', 'text', 1); ?></td>

				<td nowrap="nowrap"><?php echo JText::_('JOOMEXT_READ_MORE'); ?></td>
				<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $this->choice, 'users_readmore', 'size="1" onclick="updateTag(\'users\');"', 'value', 'text', 1); ?></td>
			</tr>
			<?php
			$db->setQuery('SELECT a.id, a.title, b.uid, c.title AS ptitle FROM #__social_fields AS a JOIN #__social_fields_steps AS b ON a.step_id = b.id JOIN #__social_profiles AS c ON b.uid = c.id WHERE a.unique_key NOT LIKE "'.implode('%" AND a.unique_key NOT LIKE "', array("JOOMLA_", "HEADER", "SEPARATOR", "TERMS", "COVER", "AVATAR")).'%"');
			$allFields = $db->loadObjectList();

			$fieldsDisplay = array();
			foreach($allFields as $oneField){
				if(!empty($oneField->title)){
					if(empty($fieldsDisplay[$oneField->uid])){
						$fieldsDisplay[$oneField->uid] = array();
					}
					$fieldsDisplay[$oneField->uid][] = array('title' => $oneField->id, 'label' => $oneField->title, 'checked' => '', 'ptitle' => $oneField->ptitle);
				}
			}
			?>
			<tr>
				<td colspan="4"><?php echo JText::_('COM_EASYSOCIAL_APPS_TYPE_FIELDS').' :'; ?></td>
			</tr>
			<?php
			foreach($fieldsDisplay as $j => $oneProfileFields){
				$oneProfileFields[] = array('title' => 'name', 'label' => 'COM_EASYSOCIAL_FIELDS_PROFILE_DEFAULT_YOUR_NAME', 'checked' => 'yes', 'ptitle' => '');
				$oneProfileFields[] = array('title' => 'email', 'label' => 'COM_EASYSOCIAL_FIELDS_PROFILE_DEFAULT_EMAIL_ADDRESS', 'checked' => 'yes', 'ptitle' => '');
				$oneProfileFields[] = array('title' => 'avatar', 'label' => 'COM_EASYSOCIAL_FIELDS_PROFILE_DEFAULT_PROFILE_PICTURE', 'checked' => 'yes', 'ptitle' => '');
				?>
				<tr>
					<td nowrap="nowrap"><?php echo $oneProfileFields[0]['ptitle']; ?></td>
					<?php
					$i = 1;
					foreach($oneProfileFields as $oneField){
						if($i == 4){
							echo '</tr><tr><td/>';
							$i = 1;
						}
						echo '<td nowrap="nowrap"><input type="checkbox" name="users_cbdisplay" value="'.$oneField['title'].'" id="'.$oneField['title'].'users'.$j.'" '.(($oneField['checked'] == 'yes') ? 'checked' : '').' onclick="updateTag(\'users\');"/><label style="margin-left:5px" for="'.$oneField['title'].'users'.$j.'">'.JText::_($oneField['label']).'</label></td>';
						$i++;
					}
					while($i != 4){
						echo '<td/>';
						$i++;
					}
					?>
				</tr>
			<?php
			}
			?>
		</table>
		<?php
		echo $tabs->endPanel();
	}

	function onAcyDisplayFilters(&$type){
		$lang = JFactory::getLanguage();
		$lang->load('com_easysocial', JPATH_SITE);
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id, title FROM #__social_profiles');
		$allProfiles = $db->loadObjectList();
		$profiles = array();
		$profiles[] = JHTML::_('select.option', 0, '- - -');
		foreach($allProfiles as $oneProfile){
			$profiles[] = JHTML::_('select.option', $oneProfile->id, JText::_($oneProfile->title));
		}

		$jsOnChange = "displayCondFilter('displayFields', 'toChange__num__',__num__,'profile='+document.getElementById('filter__num__easysocialfieldsprofile').value); ";

		$operators = acymailing_get('type.operators');
		$operators->extra = 'onchange="countresults(__num__)"';
		$return = '<div id="filter__num__easysocialfields">'.JHTML::_('select.genericlist', $profiles, "filter[__num__][easysocialfields][profile]", 'onchange="'.$jsOnChange.'countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0, 'filter__num__easysocialfieldsprofile').'<span id="toChange__num__"><input onchange="countresults(__num__)" class="inputbox" type="text" name="filter[__num__][easysocialfields][map]" style="width:200px" value="" id="filter__num__easysocialfieldsmap" readonly/></span>';
		$return .= ' '.$operators->display("filter[__num__][easysocialfields][operator]").' <span id="toChange__num__value"><input onchange="countresults(__num__)" class="inputbox" type="text" name="filter[__num__][easysocialfields][value]" style="width:200px" value="" id="filter__num__easysocialfieldsvalue" readonly></span></div>';
		$type['easysocialfields'] = 'EasySocial User Fields';


		$inoperator = acymailing_get('type.operatorsin');
		$inoperator->js = 'onchange="countresults(__num__)"';

		$return .= '<div id="filter__num__easysocialprofiles">'.$inoperator->display("filter[__num__][easysocialprofiles][type]").JHTML::_('select.genericlist', $profiles, "filter[__num__][easysocialprofiles][map]", 'onchange="countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0, 'filter__num__easysocialprofilesmap');
		$return .= '<br /><input type="text" name="filter[__num__][easysocialprofiles][cdateinf]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /> < '.JText::_('CREATED_DATE').' < <input type="text" name="filter[__num__][easysocialprofiles][cdatesup]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /></div>';
		$type['easysocialprofiles'] = 'EasySocial User Profile';

		if(in_array($db->getPrefix().'social_clusters_categories', $db->getTableList())){
			$db->setQuery('SELECT id, title FROM #__social_clusters WHERE cluster_type LIKE "group" ORDER BY title');
			$allGroups = $db->loadObjectList();
			$groups = array();
			$groups[] = JHTML::_('select.option', 0, '- - -');
			foreach($allGroups as $oneGroup){
				$groups[] = JHTML::_('select.option', $oneGroup->id, JText::_($oneGroup->title));
			}
			$return .= '<div id="filter__num__easysocialgroups">'.$inoperator->display("filter[__num__][easysocialgroups][type]").JHTML::_('select.genericlist', $groups, "filter[__num__][easysocialgroups][map]", 'onchange="countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0, 'filter__num__easysocialgroupsmap');
			$db->setQuery('SELECT id, title FROM #__social_clusters_categories WHERE type LIKE "group" ORDER BY title');
			$allCats = $db->loadObjectList();
			$cats = array();
			$cats[] = JHTML::_('select.option', 0, JText::_('ACY_ANY_CATEGORY'));
			foreach($allCats as $oneCat){
				$cats[] = JHTML::_('select.option', $oneCat->id, JText::_($oneCat->title));
			}

			$return .= JHTML::_('select.genericlist', $cats, "filter[__num__][easysocialgroups][cat]", 'onchange="countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0, 'filter__num__easysocialgroupscat');
			$return .= '<br /><input type="text" name="filter[__num__][easysocialgroups][cdateinf]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /> < '.JText::_('CREATED_DATE').' < <input type="text" name="filter[__num__][easysocialgroups][cdatesup]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /></div>';
			$type['easysocialgroups'] = 'EasySocial Groups';

			if(file_exists(JPATH_SITE.DS.'components'.DS.'com_easysocial'.DS.'controllers'.DS.'events.php')){
				$db->setQuery('SELECT a.id, a.title '.'FROM #__social_clusters AS a '.'JOIN #__social_events_meta AS b '.'ON a.id = b.cluster_id '.'WHERE a.cluster_type LIKE "event" '.'AND b.start > '.$db->Quote(date('Y-m-d H:i:s', time() - 5184000)).' '.'AND state = 1 '.'ORDER BY title');
				$allEvents = $db->loadObjectList();
				$events = array();
				$events[] = JHTML::_('select.option', 0, '- - -');
				foreach($allEvents as $oneEvent){
					$events[] = JHTML::_('select.option', $oneEvent->id, JText::_($oneEvent->title));
				}
				$return .= '<div id="filter__num__easysocialevent">'.JHTML::_('select.genericlist', $events, "filter[__num__][easysocialevent][map]", 'onchange="countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0, 'filter__num__easysocialeventmap');
				$db->setQuery('SELECT id, title FROM #__social_clusters_categories WHERE type LIKE "event" ORDER BY title');
				$allCats = $db->loadObjectList();
				$cats = array();
				$cats[] = JHTML::_('select.option', 0, JText::_('ACY_ANY_CATEGORY'));
				foreach($allCats as $oneCat){
					$cats[] = JHTML::_('select.option', $oneCat->id, JText::_($oneCat->title));
				}

				$state = array();
				$state[] = JHTML::_('select.option', '0', JText::_('ALL_STATUS'));
				$state[] = JHTML::_('select.option', '1', JText::_('COM_EASYSOCIAL_EVENTS_GUEST_GOING'));
				$state[] = JHTML::_('select.option', '3', JText::_('COM_EASYSOCIAL_EVENTS_GUEST_MAYBE'));
				$state[] = JHTML::_('select.option', '4', JText::_('COM_EASYSOCIAL_EVENTS_GUEST_NOTGOING'));

				$return .= JHTML::_('select.genericlist', $cats, "filter[__num__][easysocialevent][cat]", 'onchange="countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0, 'filter__num__easysocialgroupscat');
				$return .= ' '.JHTML::_('select.genericlist', $state, "filter[__num__][easysocialevent][state]", 'onchange="countresults(__num__)" class="inputbox" size="1"', 'value', 'text', 0);
				$return .= '<br /><input type="text" name="filter[__num__][easysocialevent][cdateinf]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /> < '.JText::_('CREATED_DATE').' < <input type="text" name="filter[__num__][easysocialevent][cdatesup]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /></div>';
				$type['easysocialevent'] = 'EasySocial Event registration';
			}
		}

		$acyconfig = acymailing_config();
		if(version_compare($acyconfig->get('version'), '4.8.0', '<')){
			echo 'Please update AcyMailing, the EasySocial plugin may not work properly with this version';
		}
		return $return;
	}

	function onAcyTriggerFct_displayFields(){
		$num = JRequest::getInt('num');
		$profile = JRequest::getString('profile');

		if(empty($profile)) return '<input onchange="countresults('.$num.')" class="inputbox" type="text" name="filter['.$num.'][easysocialfields][map]" style="width:200px" value="" id="filter'.$num.'easysocialfieldsmap" readonly/>';

		$lang = JFactory::getLanguage();
		$lang->load('com_easysocial', JPATH_ADMINISTRATOR);
		$lang->load('com_easysocial', JPATH_SITE);

		$db = JFactory::getDBO();
		$db->setQuery('SELECT a.id, a.title FROM #__social_fields AS a JOIN #__social_fields_steps AS c ON a.step_id = c.id WHERE c.type = "profiles" AND c.uid = '.intval($profile).' AND a.unique_key NOT LIKE "'.implode('%" AND a.unique_key NOT LIKE "', array("JOOMLA_", "HEADER", "SEPARATOR", "TERMS", "COVER", "AVATAR", "HTML", "TEXT-", "FILE", "CURRENCY")).'%"');
		$fields = $db->loadObjectList();
		$list = array();
		$list[] = JHTML::_('select.option', 0, '- - -');
		foreach($fields as $field){
			$list[] = JHTML::_('select.option', $field->id, JText::_($field->title));
		}

		$jsOnChange = "displayCondFilter('displayOptions', 'toChange".$num."value',".$num.",'map='+document.getElementById('filter".$num."easysocialfieldsmap').value+'&cond='+document.getElementById('filter__num__easysocialfieldsoperator').value+'&value='+document.getElementById('filter__num__easysocialfieldsvalue').value); ";

		return JHTML::_('select.genericlist', $list, "filter[".$num."][easysocialfields][map]", 'onchange="'.$jsOnChange.'countresults('.$num.')" class="inputbox" size="1"', 'value', 'text', 0, 'filter'.$num.'easysocialfieldsmap');
	}

	function onAcyTriggerFct_displayOptions(){
		$num = JRequest::getInt('num');
		$map = JRequest::getString('map');
		$cond = JRequest::getVar('cond', '', '', 'string', JREQUEST_ALLOWHTML);
		$value = JRequest::getVar('value', '', '', 'string', JREQUEST_ALLOWHTML);

		$emptyInputReturn = '<input onchange="countresults('.$num.')" class="inputbox" type="text" name="filter['.$num.'][easysocialfields][value]" style="width:200px" value="'.$value.'" id="filter'.$num.'easysocialfieldsvalue">';

		if(empty($map) || !in_array($cond, array('=', '!='))) return $emptyInputReturn;

		$db = JFactory::getDBO();
		$db->setQuery('SELECT DISTINCT a.raw, c.title, d.unique_key
						FROM #__social_fields_data AS a
						JOIN #__acymailing_subscriber AS b
							ON a.uid = b.userid
						LEFT JOIN #__social_fields_options AS c
							ON a.field_id = c.parent_id AND a.raw = c.value
						JOIN #__social_fields AS d
							ON d.id = a.field_id
						WHERE a.field_id = '.intval($map).'
							AND a.type LIKE "user" LIMIT 100');
		$options = $db->loadObjectList();

		if(empty($options) || count($options) >= 100 || (count($options) == 1 && (empty($options[0]->raw) || $options[0]->raw == '-'))) return $emptyInputReturn;

		$lang = JFactory::getLanguage();
		$lang->load('com_easysocial', JPATH_SITE);

		foreach($options as &$oneOption){
			if(empty($oneOption->title)) $oneOption->title = $oneOption->raw;
			if(strpos($oneOption->unique_key, 'GENDER') !== false) $oneOption->title = str_replace(array(1, 2), array(JText::_('PLG_FIELDS_GENDER_SELECT_MALE'), JText::_('PLG_FIELDS_GENDER_SELECT_FEMALE')), $oneOption->title);
		}

		return JHTML::_('select.genericlist', $options, "filter[".$num."][easysocialfields][value]", 'onchange="countresults('.$num.')" class="inputbox" size="1" ', 'raw', 'title', $value, 'filter'.$num.'easysocialfieldsvalue');
	}

	function onAcyProcessFilterCount_easysocialfields(&$query, $filter, $num){
		$this->onAcyProcessFilter_easysocialfields($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilter_easysocialfields(&$query, $filter, $num){
		$query->join[] = '#__social_fields_data AS sfd'.$num.' ON sub.userid = sfd'.$num.'.uid';
		$query->where[] = 'sfd'.$num.'.field_id = '.intval($filter['map']);
		$query->where[] = $query->convertQuery('sfd'.$num, 'raw', $filter['operator'], $filter['value']);
	}

	function onAcyProcessFilterCount_easysocialprofiles(&$query, $filter, $num){
		$this->onAcyProcessFilter_easysocialprofiles($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilter_easysocialprofiles(&$query, $filter, $num){
		$query->join['easysocialprofiles'] = '#__social_profiles_maps AS easysocialprofiles ON easysocialprofiles.user_id = sub.userid';
		if(!empty($filter['cdateinf'])){
			$filter['cdateinf'] = acymailing_replaceDate($filter['cdateinf']);
			if(!is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strtotime($filter['cdateinf']);
			if(is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strftime('%Y-%m-%d %H:%M:%S', $filter['cdateinf']);
			$query->where[] = 'easysocialprofiles.created > '.$query->db->Quote($filter['cdateinf']);
		}
		if(!empty($filter['cdatesup'])){
			$filter['cdatesup'] = acymailing_replaceDate($filter['cdatesup']);
			if(!is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strtotime($filter['cdatesup']);
			if(is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strftime('%Y-%m-%d %H:%M:%S', $filter['cdatesup']);
			$query->where[] = 'easysocialprofiles.created < '.$query->db->Quote($filter['cdatesup']);
		}
		$filter['map'] = intval($filter['map']);
		if($filter['type'] == 'IN'){
			$query->where[] = 'easysocialprofiles.profile_id = '.$filter['map'];
		}elseif($filter['type'] == 'NOT IN' && !empty($filter['map'])){
			$query->where[] = 'easysocialprofiles.profile_id <> '.$filter['map'];
		}else{
			$query->where[] = 'easysocialprofiles.profile_id = 0';
		}
	}

	function onAcyProcessFilterCount_easysocialgroups(&$query, $filter, $num){
		$this->onAcyProcessFilter_easysocialgroups($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilter_easysocialgroups(&$query, $filter, $num){
		$db = JFactory::getDBO();
		$query->join['easysocialgroups'] = '#__social_clusters_nodes AS easysocialgroups ON easysocialgroups.uid = sub.userid';
		$query->join['easysocialcatgroups'] = '#__social_clusters AS easysocialcatgroups ON easysocialcatgroups.id = easysocialgroups.cluster_id';
		if(!empty($filter['cdateinf'])){
			$filter['cdateinf'] = acymailing_replaceDate($filter['cdateinf']);
			if(!is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strtotime($filter['cdateinf']);
			if(is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strftime('%Y-%m-%d %H:%M:%S', $filter['cdateinf']);
			$query->where[] = 'easysocialgroups.created > '.$query->db->Quote($filter['cdateinf']);
		}
		if(!empty($filter['cdatesup'])){
			$filter['cdatesup'] = acymailing_replaceDate($filter['cdatesup']);
			if(!is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strtotime($filter['cdatesup']);
			if(is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strftime('%Y-%m-%d %H:%M:%S', $filter['cdatesup']);
			$query->where[] = 'easysocialgroups.created < '.$query->db->Quote($filter['cdatesup']);
		}
		$filter['map'] = intval($filter['map']);
		$filter['cat'] = intval($filter['cat']);

		if(!empty($filter['map'])){
			if($filter['type'] == 'IN'){
				$query->where[] = 'easysocialgroups.cluster_id = '.$filter['map'];
			}elseif($filter['type'] == 'NOT IN'){
				$db->setQuery('SELECT uid FROM #__social_clusters_nodes WHERE cluster_id = '.$filter['map']);
				$excludedUsers = array_merge(acymailing_loadResultArray($db), array(0));
				$query->where[] = 'sub.userid NOT IN ('.implode(',', $excludedUsers).')';
			}
		}elseif(!empty($filter['cat'])){
			if($filter['type'] == 'IN'){
				$query->where[] = 'easysocialcatgroups.category_id = '.$filter['cat'];
			}elseif($filter['type'] == 'NOT IN'){
				$db->setQuery('SELECT a.uid FROM #__social_clusters_nodes AS a JOIN #__social_clusters AS b ON a.cluster_id = b.id WHERE b.category_id = '.$filter['cat']);
				$excludedUsers = array_merge(acymailing_loadResultArray($db), array(0));
				$query->where[] = 'sub.userid NOT IN ('.implode(',', $excludedUsers).')';
			}
		}elseif($filter['type'] == 'NOT IN'){
			$query->where[] = 'easysocialcatgroups.category_id = 0';
		}
	}

	function onAcyProcessFilterCount_easysocialevent(&$query, $filter, $num){
		$this->onAcyProcessFilter_easysocialevent($query, $filter, $num);
		return JText::sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilter_easysocialevent(&$query, $filter, $num){
		$filter['map'] = intval($filter['map']);
		$filter['cat'] = intval($filter['cat']);
		$filter['state'] = intval($filter['state']);

		$joinRegTable = '#__social_clusters_nodes AS easysocialeventreg'.$num.' ON easysocialeventreg'.$num.'.uid = sub.userid AND easysocialeventreg'.$num.'.type = "user"';
		if(!empty($filter['state'])) $joinRegTable .= ' AND easysocialeventreg'.$num.'.state IN ('.$filter['state'].')';
		$joinEventTable = '#__social_clusters AS easysocialevent'.$num.' ON easysocialevent'.$num.'.id = easysocialeventreg'.$num.'.cluster_id';

		if(!empty($filter['map'])){ // Event selected
			$query->join['easysocialeventreg'.$num] = $joinRegTable.' AND easysocialeventreg'.$num.'.cluster_id = '.$filter['map'];
		}elseif(!empty($filter['cat'])){ // If not, category selected
			$query->join['easysocialeventreg'.$num] = $joinRegTable;
			$query->join['easysocialevent'.$num] = $joinEventTable.' AND easysocialevent'.$num.'.state = 1 AND easysocialevent'.$num.'.category_id = '.$filter['cat'];
		}else{ // Every attender of every event...
			$query->join['easysocialeventreg'.$num] = $joinRegTable;
			$query->join['easysocialevent'.$num] = $joinEventTable.' AND easysocialevent'.$num.'.cluster_type = "event" AND easysocialevent'.$num.'.state = 1';
		}

		if(!empty($filter['cdateinf'])){
			$filter['cdateinf'] = acymailing_replaceDate($filter['cdateinf']);
			if(!is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strtotime($filter['cdateinf']);
			if(is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strftime('%Y-%m-%d %H:%M:%S', $filter['cdateinf'] - date('Z'));
			$query->where[] = 'easysocialeventreg'.$num.'.created > '.$query->db->Quote($filter['cdateinf']);
		}

		if(!empty($filter['cdatesup'])){
			$filter['cdatesup'] = acymailing_replaceDate($filter['cdatesup']);
			if(!is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strtotime($filter['cdatesup']);
			if(is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strftime('%Y-%m-%d %H:%M:%S', $filter['cdatesup'] - date('Z'));
			$query->where[] = 'easysocialeventreg'.$num.'.created < '.$query->db->Quote($filter['cdatesup']);
		}
	}
}//endclass
?>