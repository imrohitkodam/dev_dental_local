<?php
/**
 * @version    SVN: <svn_id>
 * @package    Plg_System_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.html.html');
jimport('joomla.html.parameter');
jimport('joomla.utilities.date');
jimport('techjoomla.common');
jimport('techjoomla.tjmail.mail');

use Dompdf\Dompdf;
use Joomla\Utilities\ArrayHelper;

/**
 * Tjlms Main helper.
 *
 * @since  1.0.0
 */
class ComtjlmsHelper
{
	/**
	 * Method acts as a consturctor
	 *
	 * @since   1.0.0
	 */
	public function __construct()
	{
		$params = JComponentHelper::getParams('com_tjlms');
		$this->tjlmsparams = $params;
		$socialintegration = $params->get('social_integration');

		// Load main file
		jimport('techjoomla.jsocial.jsocial');
		jimport('techjoomla.jsocial.joomla');

		if ($socialintegration != 'none')
		{
			if ($socialintegration == 'jomsocial')
			{
				jimport('techjoomla.jsocial.jomsocial');
			}
			elseif ($socialintegration == 'easysocial')
			{
				jimport('techjoomla.jsocial.easysocial');
			}
		}

		$this->sociallibraryobj = $this->getSocialLibraryObject();
	}

	/**
	 * Function to get component params
	 *
	 * @param   STRING  $component  Component name
	 *
	 * @return  ARRAY  $params
	 *
	 * @since  1.0.0
	 */
	public function getcomponetsParams($component = 'com_tjlms')
	{
		$params = JComponentHelper::getParams($component);

		return $params;
	}

	/**
	 * Function to genrate PDF
	 *
	 * @param   STRING  $html       Html string
	 * @param   STRING  $pdffile    File path
	 * @param   STRING  $course_id  Course ID
	 * @param   STRING  $user_id    User iD
	 * @param   STRING  $download   Allow download
	 *
	 * @return  Pdf file
	 *
	 * @since  1.0.0
	 */
	public function generatepdf($html, $pdffile, $course_id, $user_id, $download = 0)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Removed from joomla 3.0 and greater
		if (JVERSION < 3.0)
		{
			if ($funcs = spl_autoload_functions())
			{
				foreach ($funcs as $func)
				{
					spl_autoload_unregister($func);
				}
			}
		}

		require_once JPATH_SITE . "/libraries/techjoomla/dompdf/autoload.inc.php";

		if (JVERSION < '2.5.0')
		{
			$classpath = JPATH_SITE . "/libraries/techjoomla/dompdf";

			foreach (JFolder::files($classpath) as $file)
			{
				JLoader::register(JFile::stripExt($file), $classpath . DS . $file);
			}
		}
		else
		{
			// This added in as well. ABP: Re-enable spl functions
			if ($funcs)
			{
				/*foreach ($funcs as $func)
				{
					if (is_callable($func))
					{
						spl_autoload_register($func);
					}
				}*/
				// Re-register
				// Import the library loader if necessary.
				if (!class_exists('JLoader'))
				{
					require_once JPATH_PLATFORM . '/loader.php';
				}

				class_exists('JLoader') or die('pdf generation failed');

				// Setup the autoloaders.
				JLoader::setup();

				// Import the cms loader if necessary.
				if (version_compare(JVERSION, '2.5.6', 'le'))
				{
					if (!class_exists('JCmsLoader'))
					{
						require_once JPATH_PLATFORM . '/cms/cmsloader.php';
						JCmsLoader::setup();
					}
				}
				else
				{
					if (!class_exists('JLoader'))
					{
						require_once JPATH_PLATFORM . '/cms.php';
						require_once JPATH_PLATFORM . '/loader.php';
						JLoader::setup();
					}
				}
			}

			require_once JPATH_PLATFORM . '/loader.php';
		}

		$html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>' . $html . '</body></html>';

		if (get_magic_quotes_gpc())
		{
			$html = stripslashes($html);
		}

		$dompdf    = new DOMPDF;
		$html      = utf8_decode($html);
		$dompdf->loadHTML($html);

		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_tjlms');
		$orientation = $params->get('orientation');

		if ($orientation == 1)
		{
			$dompdf->setPaper('A4', 'portrait');
		}
		else
		{
			$dompdf->setPaper('A4', 'landscape');
		}

		$dompdf->render();

		$output = $dompdf->output();

		if ($download == 1)
		{
			$dompdf->stream("Certificate_" . $course_id . "_" . $user_id . ".pdf", array(
																						"Attachment" => 1
																					)
							);
			jexit();
		}

		if (fopen($pdffile, 'w'))
		{
			$fh = fopen($pdffile, 'w');
			fwrite($fh, $output);
			fclose($fh);

			return $pdffile;
		}
	}

	/**
	 * Function used to get enrolled user
	 *
	 * @param   INT    $c_id     Course ID
	 * @param   ARRAY  $options  Optional parameter
	 *
	 * @return  Enrolled users
	 *
	 * @since  1.0.0
	 */
	public function getCourseEnrolledUsers($c_id = 0, $options = array())
	{
		$db    = JFactory::getDBO();
		$select = '*';

		if (isset($options['IdOnly']) && $options['IdOnly'] == 1)
		{
			$select = 'user_id';
		}

		$getResultType = isset($options['getResultType']) ? $options['getResultType'] : "loadObjectList";

		$query = $db->getQuery(true);
		$query->select($select);
		$query->from('`#__tjlms_enrolled_users` AS a');
		$query->join('inner', '`#__users` AS u ON a.user_id = u.id');
		$query->where('a.course_id = ' . $db->Quote($c_id));

		$state = 1;

		if (isset($options['state']))
		{
			$state = $options['state'];
		}

		if (is_array($state))
		{
			ArrayHelper::toInteger($state);
			$query->where('a.state IN (' . implode(',', $state) . ')');
		}
		else
		{
			$query->where('a.state = ' . (int) $state);
		}

		$db->setQuery($query);
		$enrolled_users = $db->$getResultType();

		return $enrolled_users;
	}

	/**
	 * Function to replace tags from certificate to actual result
	 *
	 * @param   ARRAY  $cer_data  Certificate data
	 *
	 * @return  Message body
	 *
	 * @since  1.0.0
	 */
	public function tagreplace($cer_data)
	{
		$userId = JFactory::getUser()->id;
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_easysocial/tables');
		$profileMap = JTable::getInstance('ProfileMap', 'SocialTable');
		$profileMap->load(array('user_id' => $userId));
		$profileId = $profileMap->profile_id;
		$profile = JTable::getInstance('Profile', 'SocialTable');
		$profile->load(array('id' => $profileId));

		$coursetrack = JTable::getInstance('coursetrack', 'TjlmsTable');
		$coursetrack->load(array('course_id' => $cer_data['course_id'], 'user_id' => $userId));

		$cer_data['profession'] = $profile->title;
		$cpdDate = $this->getCpdDate($userId);

		$answers = $this->getSurveyAnswer($userId, $cer_data['course_id']);

		$answers = implode(', ', $answers);
		$options = str_replace("0", "A", $answers);
		$options = str_replace("1", "B", $options);
		$options = str_replace("2", "C", $options);
		$options = str_replace("3", "D", $options);

		$cer_data['cpd_development_outcomes'] = $options;

		$cer_data['year_time'] = $this->timeSpentOnCourse($userId, $cer_data['course_id'], $cer_data['profession']);

		$cer_data['five_year_total_time'] = $this->totalFiveYearTimeSpentOnCourse($userId, $cer_data['course_id'], $cer_data['profession']);

		$cer_data['total_time'] = $this->totalTimeSpentOnCourse($userId, $cer_data['course_id']);

		$cer_data['time_spent'] = $this->timeSpentforCompletingCourse($userId, $cer_data['course_id']);

		$message_body = $cer_data['msg_body'];
		preg_match_all("/\[.*?\]/", strip_tags($cer_data['msg_body']), $matches);
		$message_body = stripslashes($message_body);

		foreach ($cer_data as $index => $data)
		{
			if (!empty($data))
			{
				$message_body = str_replace('[' . strtoupper($index) . ']', $data, $message_body);
			}
			elseif($index != 'cpd_development_outcomes')
			{
				$message_body = str_replace('[' . strtoupper($index) . ']', JText::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY'), $message_body);
			}
		}

		$jsFilePath = JPATH_ROOT . '/components/com_community/libraries/core.php';
		$esFilePath = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		foreach ($matches[0] as $fieldTag)
		{
			$res    = '';
			$field    = str_replace("[", " ", $fieldTag);
			$field    = str_replace("]", " ", $field);
			$field_arr = explode(":", $field);

			if (trim($field_arr[0]) == 'esfield' && file_exists($esFilePath))
			{
				require_once $esFilePath;
				$esuser = Foundry::user();
				$res = $esuser->getFieldValue($field_arr[1]);

				// Since we are printing user data, lets implode array and return string
				if (is_array($res))
				{
					$res = implode(",", $res);
				}

				if (!empty($res))
				{
					$message_body = str_replace($fieldTag, $res, $message_body);
				}
				else
				{
					$message_body = str_replace($fieldTag, " ", $message_body);
				}
			}

			if (trim($field_arr[0]) == 'jsfield' && file_exists($jsFilePath))
			{
				include_once $jsFilePath;
				$jsprofile = CFactory::getUser();
				$res = $jsprofile->getInfo($field_arr[1]);

				if (!empty($res))
				{
					$message_body = str_replace($fieldTag, $res, $message_body);
				}
				else
				{
					$message_body = str_replace($fieldTag, " ", $message_body);
				}
			}

			if (trim($field_arr[0]) == 'DATE')
			{
				$todaysDate = JHtml::date('now', 'j F, Y');
				$message_body = str_replace($fieldTag, $todaysDate, $message_body);
			}

			// Check if user is of Dentist profession
			if ($cer_data['profession'] == 'Dental professionals')
			{
				if (trim($field_arr[0]) == 'CYCLE_START_DATE')
				{
					if (!empty($cpdDate))
					{
						$cycleStartDate = new JDate($cpdDate);

						// For dentist Cycle start date is 1 January
						$cycleStartDate = $cycleStartDate->setDate($cycleStartDate->year, 1, 01)->format('j F Y');
						$message_body = str_replace($fieldTag, $cycleStartDate, $message_body);
					}
					else
					{
						$message_body = str_replace($fieldTag, "", $message_body);
					}
				}

				if (trim($field_arr[0]) == 'CYCLE_END_DATE')
				{
					if (!empty($cpdDate))
					{
						// For Dentist Cycle End date is 31 Dec after 5 years
						$cycleEndDate = new JDate($cycleStartDate . '+4 year');
						$cycleEndDate = $cycleEndDate->setDate($cycleEndDate->year, 12, 31)->format('j F Y');
						$message_body = str_replace($fieldTag, $cycleEndDate, $message_body);
					}
					else
					{
						$message_body = str_replace($fieldTag, JText::sprintf('COM_TJLMS_COURSE_CERTIFICATE_FIVE_YEAR_CYCLE', $userId), $message_body);
					}
				}
			}
			else
			{
				if (trim($field_arr[0]) == 'CYCLE_START_DATE')
				{
					$tempDate = new JDate($cpdDate);
					$tempDate->setDate($cpdDate, 8, 01);

					// Flag date set as 31 July for comparison
					$flagDate = $tempDate->setDate($tempDate->year, 7, 31);

					if ($cpdDate <= $flagDate->toSql())
					{
						if (!empty($cpdDate))
						{
							$cycleStartDate = new JDate($cpdDate . '-1 year');
							$cycleStartDate = $cycleStartDate->setDate($cycleStartDate->year, 8, 01)->format('j F Y');
							$message_body = str_replace($fieldTag, $cycleStartDate, $message_body);
						}
						else
						{
							$message_body = str_replace($fieldTag, '', $message_body);
						}
					}
					else
					{
						if (!empty($cpdDate))
						{
							$cycleStartDate = new JDate($cpdDate);
							$cycleStartDate = $cycleStartDate->setDate($cycleStartDate->year, 8, 01)->format('j F Y');
							$message_body = str_replace($fieldTag, $cycleStartDate, $message_body);
						}
						else
						{
							$message_body = str_replace($fieldTag, '', $message_body);
						}
					}
				}

				if (trim($field_arr[0]) == 'CYCLE_END_DATE')
				{
					if (!empty($cpdDate))
					{
						// CPD Cycle End date after 5 year
						$cycleEndDate = new JDate($cycleStartDate . '+5 year');
						$cycleEndDate = $cycleEndDate->setDate($cycleEndDate->year, 7, 31)->format('j F Y');
						$message_body = str_replace($fieldTag, $cycleEndDate, $message_body);
					}
					else
					{
						$message_body = str_replace($fieldTag, JText::sprintf('COM_TJLMS_COURSE_CERTIFICATE_FIVE_YEAR_CYCLE', $userId), $message_body);
					}
				}
			}

			$TjfieldsPath = JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';

			if (file_exists($TjfieldsPath))
			{
				require_once JPATH_SITE . '/components/com_tjlms/models/course.php';
				$tjlmsModelcourse = new TjlmsModelcourse;
				$TJValues = $tjlmsModelcourse->getDataExtra($cer_data['course_id']);

				$field_id_value = array();

				foreach ($TJValues as $TJValue)
				{
					if ($TJValue->type === "multi_select")
					{
						$field_id_value[$TJValue->field_id][] = $TJValue;
					}
					else
					{
						$field_id_value[$TJValue->field_id] = $TJValue;
					}
				}

				if (trim($field_arr[0]) == 'TJFIELD_CPD_HOURS')
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_CPD_HOURS]->value, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_GDC_RECOMMENDED_TOPIC' && !empty($TJValues[1]->name))
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_GDC_RECOMMENDED_TOPIC]->value[0]->options, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_GDC_HIGHLY_RECOMMENDED_SUBJECT' && !empty($TJValues[1]->name))
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_GDC_HIGHLY_RECOMMENDED_SUBJECT]->value[0]->options, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_CUSTOM_TEST_FIELDS')
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_CUSTOM_TEST_FIELDS]->value, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_AIMS')
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_AIMS]->value, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_GDC_OBJECTIVE')
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_GDC_OBJECTIVE]->value, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_OUTCOME')
				{
					$message_body = str_replace($fieldTag, $field_id_value[TJFIELD_OUTCOME]->value, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_PRESENTER_ONE')
				{
					if ($field_id_value[TJFIELD_PRESENTER_ONE]->value)
					{
						$presenter_one = JFactory::getUser($field_id_value[TJFIELD_PRESENTER_ONE]->value);
					}

					$message_body = str_replace($fieldTag, $presenter_one->name, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_PRESENTER_TWO')
				{
					if ($field_id_value[TJFIELD_PRESENTER_TWO]->value)
					{
						$presenter_two = JFactory::getUser($field_id_value[TJFIELD_PRESENTER_TWO]->value);
					}

					$message_body = str_replace($fieldTag, $presenter_two->name, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_PRESENTER_THREE')
				{
					if ($field_id_value[TJFIELD_PRESENTER_THREE]->value)
					{
						$presenter_three = JFactory::getUser($field_id_value[TJFIELD_PRESENTER_THREE]->value);
					}

					$message_body = str_replace($fieldTag, $presenter_three->name, $message_body);
				}
				elseif (trim($field_arr[0]) == 'TJFIELD_DEVELOPMENT_OUTCOMES')
				{
					$fieldArr = array();
					$i = 1;

					foreach ($field_id_value[TJFIELD_DEVELOPMENT_OUTCOMES] as $development_field)
					{
						$fieldArr[] = $i . "." . $development_field->value[0]->options;
						$i++;
					}

					$i = 1;
					$development_field_value = implode(",</br></br>", $fieldArr);
					$message_body = str_replace($fieldTag, $development_field_value, $message_body);
				}
				else
				{
					$message_body = str_replace($fieldTag, '', $message_body);
				}
			}
		}

		return $message_body;
	}

	/**
	 * Function to get usergroup of a user
	 *
	 * @return  ARRAY
	 *
	 * @since  1.0.0
	 */
	/*public function getChildGroupsByuser()
	{
		$oluser   = JFactory::getUser();
		$oluserid = $oluser->id;
		$db       = JFactory::getDBO();

		if (comtjlmsHelper::checkAdmin($oluser))
		{
			$query = "SELECT distinct(group_id) FROM #__user_usergroup_map ";

			if (isset($oluser->groups['7']))
				$query .= " where group_id!=8";

			$db->setQuery($query);
			$oluser_groups = $db->loadResultArray();
		}
		else
		{
			$query = "SELECT group_id FROM #__user_usergroup_map where user_id=" . $oluserid;
			$db->setQuery($query);
			$oluser_groups = $db->loadResultArray();
		}

		$default = '';

		if (JRequest::getVar('group_filter'))
			$default = JRequest::getVar('group_filter');

		$groups = array();
		foreach ($oluser_groups as $g)
		{
			if (!in_array($g, $groups))
				$groups[] = $g;

			$query = "SELECT id FROM #__usergroups where     parent_id=" . $g;
			$db->setQuery($query);
			$g_obj = $db->loadResultArray();
			if ($g_obj)
				foreach ($g_obj as $obj)
					$groups[] = $obj;
		}
		return $groups;
	}*/

	/**
	 * Function to check if the user is admin
	 *
	 * @param   OBJ  $user  User object
	 *
	 * @return  boolean
	 *
	 * @since  1.0.0
	 */
	public function checkAdmin($user)
	{
		if ($user->get('isRoot'))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to get Item id
	 *
	 * @param   STRING  $link  URL
	 *
	 * @return  INT  Item id
	 *
	 * @since  1.0.0
	 */
	public function getitemid($link)
	{
		$itemid = 0;

		$parsedLinked = array();
		parse_str($link, $parsedLinked);

		$layout = '';

		if (isset($parsedLinked['layout']))
		{
			$layout = $parsedLinked['layout'];
		}

		if (isset($parsedLinked['view']))
		{
			if ($parsedLinked['view'] == 'course')
			{
				$tjlmsCoursesHelper   = new TjlmsCoursesHelper;

				if (isset($parsedLinked['id']))
				{
					$itemid    = $tjlmsCoursesHelper->getCourseItemid($parsedLinked['id'], $layout);
				}

				if (!$itemid)
				{
					$link = 'index.php?option=com_tjlms&view=courses';
				}
			}

			if ($parsedLinked['view'] == 'buy' || $parsedLinked['view'] == 'certificate' ||  $parsedLinked['view'] == 'attempts')
			{
				$tjlmsCoursesHelper   = new TjlmsCoursesHelper;

				if (isset($parsedLinked['course_id']))
				{
					$itemid    = $tjlmsCoursesHelper->getCourseItemid($parsedLinked['course_id'], $layout);
				}
			}

			if ($parsedLinked['view'] == 'lesson')
			{
				$tjlmLessonHelper  = new TjlmsLessonHelper;

				if ($parsedLinked['lesson_id'])
				{
					$itemid    = $tjlmLessonHelper->getLessonItemid($parsedLinked['lesson_id']);
				}
			}
		}

		if (!$itemid)
		{
			$mainframe = JFactory::getApplication();

			if ($mainframe->issite())
			{
				$JSite = new JSite;
				$menu  = $JSite->getMenu();
				$menuItem = $menu->getItems('link', $link, true);

				if ($menuItem)
				{
					$itemid = $menuItem->id;
				}
			}

			if (!$itemid)
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__menu'));
				$query->where($db->quoteName('link') . ' LIKE ' . $db->Quote($link));
				$query->where($db->quoteName('published') . '=' . $db->Quote(1));
				$query->where($db->quoteName('type') . '=' . $db->Quote('component'));
				$db->setQuery($query);
				$itemid = $db->loadResult();
			}

			if (!$itemid)
			{
				$input = JFactory::getApplication()->input;
				$itemid = $input->get('Itemid', 0);
			}
		}

		return $itemid;
	}

	/**
	 * Function to get Order info
	 *
	 * @param   INT  $orderid  Order ID
	 * @param   INT  $step_id  Checkout step
	 *
	 * @return  ARRAY
	 *
	 * @since  1.0.0
	 */
	public function getorderinfo($orderid = '0', $step_id = '')
	{
		$db     = JFactory::getDBO();
		$user   = JFactory::getUser();
		$jinput = JFactory::getApplication()->input;
		$comtjlmsHelper = new comtjlmsHelper;

		if (empty($orderid))
		{
			return 0;
		}

		if ($step_id == 'step_select_subsplan')
		{
			$query = $db->getQuery(true);
			$query->select('o.*');
			$query->from('#__tjlms_orders as o');
			$query->where('o.id=' . $orderid);

			$db->setQuery($query);
			$order_result            = $db->loadObjectList();
			$orderlist['order_info'] = $order_result;

			return $orderlist;
		}
		else
		{
			$query = $db->getQuery(true);
			$query->select('o.*,u.*,o.order_id as orderid_with_prefix');
			$query->from('#__tjlms_orders as o');
			$query->join('LEFT', '#__tjlms_users as u ON o.id = u.order_id');
			$query->where('o.id=' . $orderid);

			$db->setQuery($query);
			$order_result = $db->loadObjectList();

			if (empty($order_result))
			{
				return;
			}

			$orderlist['order_info'] = $order_result;

			if (isset($orderlist['order_info'][0]->country_code) && !empty($orderlist['order_info'][0]->country_code))
			{
				$orderlist['order_info'][0]->country_code = $comtjlmsHelper->getCountryById($orderlist['order_info'][0]->country_code);
			}

			if (isset($orderlist['order_info'][0]->state_code) && !empty($orderlist['order_info'][0]->state_code))
			{
				$orderlist['order_info'][0]->state_code = $comtjlmsHelper->getRegionById($orderlist['order_info'][0]->state_code);
			}

			$query = $db->getQuery(true);
			$query->select('i.plan_id,CONCAT(s.duration," ",s.time_measure) as order_item_name, s.price');
			$query->from('#__tjlms_order_items as i');
			$query->join('LEFT', '#__tjlms_subscription_plans as s ON s.id=i.plan_id');
			$query->where('i.order_id=' . $orderid . ' GROUP BY i.plan_id');

			$db->setQuery($query);
			$orderlist['items'] = $db->loadObjectList();

			return $orderlist;
		}
	}

	/**
	 * This function Checks whether order user and current logged use is same or not
	 *
	 * @param   INT  $orderuser  User ID
	 *
	 * @return  boolean
	 *
	 * @since  1.0.0
	 */
	public function getorderAuthorization($orderuser)
	{
		$user = JFactory::getUser();

		if ($user->id == $orderuser)
		{
			return 1;
		}

		return 0;
	}

	/**
	 * This function get the view path
	 *
	 * @param   STRING  $component      Component name
	 * @param   STRING  $viewname       View name
	 * @param   STRING  $layout         Layout
	 * @param   STRING  $searchTmpPath  Site
	 * @param   STRING  $useViewpath    Site
	 *
	 * @return  boolean
	 *
	 * @since  1.0.0
	 */
	public function getViewpath($component, $viewname, $layout = 'default', $searchTmpPath = 'SITE', $useViewpath = 'SITE')
	{
		$app = JFactory::getApplication();

		$searchTmpPath = ($searchTmpPath == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;
		$useViewpath   = ($useViewpath == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;

		$layoutname = $layout . '.php';

		$override = $searchTmpPath . '/' . 'templates' . '/' . $app->getTemplate() . '/' . 'html' . '/' . $component . '/' . $viewname . '/' . $layoutname;

		if (JFile::exists($override))
		{
			return $view = $override;
		}
		else
		{
			return $view = $useViewpath . '/' . 'components' . '/' . $component . '/' . 'views' . '/' . $viewname . '/' . 'tmpl' . '/' . $layoutname;
		}
	}

	/**
	 * Function ised to get the way to display price
	 *
	 * @param   INT     $price  Amount to be displayed
	 * @param   STRING  $curr   Currency
	 *
	 * @return formatted price-currency string
	 *
	 * @since  1.0.0
	 */
	public function getFromattedPrice($price, $curr = null)
	{
		$curr_sym                   = $this->getCurrencySymbol();
		$params                     = JComponentHelper::getParams('com_tjlms');
		$currency_display_format    = $params->get('currency_display_format');
		$currency_display_formatstr = '';
		$currency_display_formatstr = str_replace('{AMOUNT}', "&nbsp;" . $price, $currency_display_format);
		$currency_display_formatstr = str_replace('{CURRENCY_SYMBOL}', "&nbsp;" . $curr_sym, $currency_display_formatstr);
		$html                       = '';
		$html                       = "<span>" . $currency_display_formatstr . "</span>";

		return $html;
	}

	/**
	 * Function used to get the currency symbol
	 *
	 * @param   STRING  $currency  Currency
	 *
	 * @return  STRING  Currency symbol
	 *
	 * @since  1.0.0
	 */
	public function getCurrencySymbol($currency = '')
	{
		$params   = JComponentHelper::getParams('com_tjlms');
		$curr_sym = $params->get('currency_symbol');

		if (empty($curr_sym))
		{
			$curr_sym = $params->get('currency');
		}

		return $curr_sym;
	}

	/**
	 * Function used to ID from order id
	 *
	 * @param   INT  $order_id  Currency
	 *
	 * @return  INT  Order Id
	 *
	 * @since  1.0.0
	 */
	public function getIDFromOrderID($order_id)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT id From #__tjlms_orders WHERE order_id = '" . $order_id . "'";
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Function used to get number of likes for an item.
	 *
	 * @param   INT     $item_id  Item id
	 * @param   STRING  $element  Element name
	 *
	 * @return  INT  Likes number
	 *
	 * @since  1.0.0
	 */
	public function getLikesForItem($item_id, $element)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('l.like_cnt');
		$query->from('#__jlike_content as l');
		$query->where('l.element="' . $element . '"');
		$query->where('l.element_id=' . $item_id);
		$db->setQuery($query);
		$likesforCourse = $db->loadResult();

		if (empty($likesforCourse))
		{
			$likesforCourse = 0;
		}

		return $likesforCourse;
	}

	/**
	 * Function used to get number of likes and Dislikes for an item.
	 *
	 * @param   INT     $item_id  Item id
	 * @param   STRING  $element  Element name
	 *
	 * @return  INT  Likes number
	 *
	 * @since  1.0.0
	 */
	public function getItemJlikes($item_id, $element)
	{
		$result = array();

		if ($item_id &&  $element)
		{
			$db = JFactory::getDBO();
			JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_jlike/tables');
			$jlikeContent = JTable::getInstance('Content', 'JlikeTable', array('dbo', $db));
			$jlikeContent->load(array('element' => $element, 'element_id' => $item_id));

			$result['likes'] = !empty($jlikeContent->like_cnt) ? (int) $jlikeContent->like_cnt : 0;
			$result['dislikes'] = !empty($jlikeContent->dislike_cnt) ? (int) $jlikeContent->dislike_cnt : 0;
		}

		return $result;
	}

	/**
	 * Function used as a push activity into Shika activity stream.
	 *
	 * @param   INT     $actor_id       user who perform the action ID
	 * @param   STRING  $action         action performed by the user
	 * @param   INT     $parent_id      Parent element ID.
	 * @param   STRING  $element_title  title for the element
	 * @param   INT     $element_id     Child element ID
	 * @param   STRING  $element_url    Child element URL
	 * @param   STRING  $params         additional info if provided
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function addActivity($actor_id, $action, $parent_id = 0, $element_title = '', $element_id = '', $element_url = '', $params = '')
	{
		$db = JFactory::getDBO();

		$data              = new stdClass;
		$data->actor_id    = $actor_id;
		$data->action      = $action;
		$data->parent_id   = $parent_id;
		$data->element     = $element_title;
		$data->element_id  = $element_id;
		$data->element_url = $element_url;
		$data->params      = $params;
		$data->added_time  = date('Y-m-d H:i:s');

		$db->insertObject('#__tjlms_activities', $data, 'id');

		return true;
	}

	/**
	 * Get social library object depending on the integration set.
	 *
	 * @param   STRING  $integration_option  Soical integration set
	 *
	 * @return  Soical library object
	 *
	 * @since 1.0.0
	 */
	public function getSocialLibraryObject($integration_option = '')
	{
		if (!$integration_option)
		{
			$params             = $this->getcomponetsParams();
			$integration_option = $params->get('social_integration', 'joomla');
		}

		if ($integration_option == 'jomsocial')
		{
			$SocialLibraryObject = new JSocialJomSocial;
		}
		elseif ($integration_option == 'easysocial')
		{
			$SocialLibraryObject = new JSocialEasySocial;
		}
		elseif ($integration_option == 'joomla')
		{
			$SocialLibraryObject = new JSocialJoomla;
		}

		return $SocialLibraryObject;
	}

	/**
	 * This function extracts the non-tags string and returns a correctly formatted string
	 * It can handle all html entities e.g. &amp;, &quot;, etc..
	 *
	 * @param   string        $s       To do
	 * @param   integer       $srt     To do
	 * @param   integer       $len     To do
	 * @param   bool/integer  $strict  If this is set to 2 then the last sentence will be completed.
	 * @param   string        $suffix  A string to suffix the value, only if it has been chopped.
	 *
	 * @return  STRING
	 *
	 * @since  1.0.0
	 */
	public function html_substr($s, $srt, $len = null, $strict = false, $suffix = null)
	{
		if (is_null($len))
		{
			$len = strlen($s);
		}

		$f = 'static $strlen=0;
				if ( $strlen >= ' . $len . ' ) { return "><"; }
				$html_str = html_entity_decode( $a[1] );
				$subsrt   = max(0, (' . $srt . '-$strlen));
				$sublen = ' . (empty($strict) ? '(' . $len . '-$strlen)' : 'max(@strpos( $html_str, "' .
							($strict === 2 ? '.' : ' ') . '", (' . $len . ' - $strlen + $subsrt - 1 )), ' . $len . ' - $strlen)') . ';
				$new_str = substr( $html_str, $subsrt,$sublen);
				$strlen += $new_str_len = strlen( $new_str );
				$suffix = ' . (!empty($suffix) ? '($new_str_len===$sublen?"' . $suffix . '":"")' : '""') . ';
				return ">" . htmlentities($new_str, ENT_QUOTES, "UTF-8") . "$suffix<";';

		return preg_replace(
								array(
									"#<[^/][^>]+>(?R)*</[^>]+>#",
									"#(<(b|h)r\s?/?>){2,}$#is"
								),
								"", trim(rtrim(ltrim(preg_replace_callback("#>([^<]+)<#", create_function('$a', $f), ">$s<"), ">"), "<"))
							);
	}

	/**
	 * Function to course order details
	 *
	 * @param   INT  $where  Where condition for query
	 *
	 * @return  Details of course irder
	 *
	 * @snce 1.0.0
	 */
	public function getallCourseDetailsByOrder($where = '')
	{
		$db    = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('c.id as course_id,c.title,c.image,c.storage,o.*,oi.*');
		$query->from('#__tjlms_orders as o');
		$query->join('LEFT', '#__tjlms_order_items as oi ON oi.order_id = o.id');
		$query->join('LEFT', '#__tjlms_courses as c ON c.id = oi.course_id');
		$query->where($where);

		$db->setQuery($query);
		$result = $db->loadObjectlist();

		return $result;
	}

	/**
	 * Function to get Country
	 *
	 * @param   INT  $countryId  Country Code
	 *
	 * @return  Country name
	 *
	 * @snce 1.0.0
	 */
	public function getCountryById($countryId)
	{
		$TjGeoHelper = JPATH_ROOT . DS . 'components/com_tjfields/helpers/geo.php';

		if (!class_exists('TjGeoHelper'))
		{
			JLoader::register('TjGeoHelper', $TjGeoHelper);
			JLoader::load('TjGeoHelper');
		}

		$this->TjGeoHelper = new TjGeoHelper;

		return $this->TjGeoHelper->getCountryNameFromId($countryId);
	}

	/**
	 * Function to get region
	 *
	 * @param   INT  $regionId  Region Code
	 *
	 * @return  region name
	 *
	 * @snce 1.0.0
	 */
	public function getRegionById($regionId)
	{
		$TjGeoHelper = JPATH_ROOT . DS . 'components/com_tjfields/helpers/geo.php';

		if (!class_exists('TjGeoHelper'))
		{
			JLoader::register('TjGeoHelper', $TjGeoHelper);
			JLoader::load('TjGeoHelper');
		}

		$this->TjGeoHelper = new TjGeoHelper;

		return $this->TjGeoHelper->getRegionNameFromId($regionId);
	}

	/**
	 * Function to send mail
	 *
	 * @param   STRING  $recipient       Email address of reciever
	 * @param   STRING  $subject         Email Subject
	 * @param   STRING  $body            Email Body
	 * @param   STRING  $bcc_string      BCC Email address
	 * @param   INT     $singlemail      Single mail
	 * @param   STRING  $attachmentPath  Attachmen Path
	 * @param   STRING  $cc_array        CC Email address
	 *
	 * @return  true
	 *
	 * @since 1.0.0
	 */
	public function sendmail($recipient, $subject, $body, $bcc_string, $singlemail = 1, $attachmentPath = "", $cc_array = array())
	{
		jimport('joomla.utilities.utility');
		global $mainframe;
		$mainframe = JFactory::getApplication();

		try
		{
			if (!$mainframe->getCfg('mailfrom'))
			{
				return JError::raiseError(404, JText::_('COM_TJLMS_EMAIL_ERROR_NO_FROMEMAIL'));
			}

			$from      = $mainframe->getCfg('mailfrom');
			$fromname  = $mainframe->getCfg('fromname');
			$recipient = trim($recipient);
			$mode      = 1;
			$cc        = array();
			$bcc       = array();

			if ($singlemail == 1)
			{
				if ($bcc_string)
				{
					$bcc = explode(',', $bcc_string);
				}
				else
				{
					$bcc = array(
						'0' => $mainframe->getCfg('mailfrom')
					);
				}
			}

			if (!empty($cc_array))
			{
				$cc = $cc_array;
			}

			$attachment = null;

			if (!empty($attachmentPath))
			{
				$attachment = $attachmentPath;
			}

			return JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment);
		}
		catch (Exception $e)
		{
			JError::raiseError(404, $e->getMessage());
		}

		return true;
	}

	/**
	 * Function to get html and send invoice mail
	 *
	 * @param   INT  $id  Order ID
	 *
	 * @return  true
	 *
	 * @since 1.0.0
	 */
	public function sendInvoiceEmail($id)
	{
		$com_params = JComponentHelper::getParams('com_tjlms');

		$comtjlmsHelper = new comtjlmsHelper;
		$orderItemid    = $comtjlmsHelper->getItemId('index.php?option=com_tjlms&view=orders');
		$jinput         = JFactory::getApplication()->input;
		$order          = $comtjlmsHelper->getorderinfo($id);
		$app            = JFactory::getApplication();
		$sitename       = $app->getCfg('sitename');

		$TjlmsCoursesHelper = new TjlmsCoursesHelper;
		$this->order_mail['courses']    = $TjlmsCoursesHelper->getcourseInfo($order['order_info'][0]->course_id);
		$this->order_mail['order']    = $order['order_info'][0];

		$this->orderinfo        = $order['order_info'];
		$this->orderitems       = $order['items'];
		$this->orders_site      = 1;
		$this->orders_email     = 1;
		$this->order_authorized = 1;

		if ($this->orderinfo[0]->address_type == 'BT')
		{
			$billemail = $this->orderinfo[0]->user_email;
		}
		elseif ($this->orderinfo[1]->address_type == 'BT')
		{
			$billemail = $this->orderinfo[1]->user_email;
		}

		$oWithSuf = $order['order_info'][0]->orderid_with_prefix;
		$processor = $order['order_info'][0]->processor;
		$orderUrl = 'index.php?option=com_tjlms&view=orders&layout=order&orderid=' . $oWithSuf . '&processor=' . $processor . '&Itemid=' . $orderItemid;

		$currenturl = JURI::root() . substr(JRoute::_($orderUrl, false), strlen(JURI::base(true)) + 1);

		$body = JText::_('COM_TJLMS_INVOICE_EMAIL_BODY');

		$status = $order['order_info'][0]->status;

		if ($status == 'I')
		{
			$body = JText::_('COM_TJLMS_ORDER_PLACED_EMAIL_BODY');
		}

		$invoicebody = TjMail::TagReplace($body, $this->order_mail);

		$invoicehtml = '<div class=""><div><span>' . $invoicebody . '</span></div>';

		// Check for view override
		$view = $comtjlmsHelper->getViewpath('com_tjlms', 'orders', 'default', 'SITE', 'SITE');
		ob_start();
		$usedinemail = 1;
		include $view;
		$invoicehtml .= ob_get_contents();
		ob_end_clean();

		$invoicehtml .= '<div class=""><div><span>' . JText::_("COM_TJLMS_INVOICE_LINK") . '</span></div>';
		$invoicehtml .= '<div><span><a href="' . $currenturl . '">' . JText::_("COM_TJLMS_CLICK_HERE") . '</a></span></div></div>';

		$subject = JText::sprintf('COM_TJLMS_INVOICE_EMAIL_SUBJECT');

		if ($status == 'I')
		{
			$subject = JText::sprintf('COM_TJLMS_ORDER_PLACED_EMAIL_SUBJECT');
		}

		$subject = TjMail::TagReplace($subject, $this->order_mail);

		// TRIGGER After Process Payment. Call the plugin and get the result
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$result = $dispatcher->trigger('tjlms_OnBeforeInvoiceEmail', array(
																				$billemail,
																				$subject,
																				$invoicehtml
																			)
									);

		// SEND INVOICE EMAIL
		$comtjlmsHelper->sendmail($billemail, $subject, $invoicehtml, '', 0, '');
	}

	/**
	 * Function to add User to social groups
	 *
	 * @param   INT  $actor_id   Course ID
	 * @param   INT  $course_id  Course ID
	 * @param   INT  $state      state
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function addUserToGroup($actor_id, $course_id, $state = 1)
	{
		$com_params         = JComponentHelper::getParams('com_tjlms');
		$comtjlmsHelper     = new comtjlmsHelper;
		$tjlmsCoursesHelper = new TjlmsCoursesHelper;
		$social_integration = $com_params->get('social_integration', 'joomla', 'STRING');

		$groupId = $tjlmsCoursesHelper->getCourseColumn($course_id, 'group_id');

		if (empty($groupId) || (is_object($groupId) && $groupId->group_id == 0))
		{
			return false;
		}
		else
		{
			$comtjlmsHelper->sociallibraryobj->addMemberToGroup($groupId->group_id, JFactory::getUser($actor_id), $state);
		}

		return true;
	}

	/**
	 * This function return array of js files which is loaded from tjassesloader plugin.
	 *
	 * @param   array  &$jsFilesArray                  Js file's array.
	 * @param   int    &$firstThingsScriptDeclaration  load script 1st
	 *
	 * @return   ARRAY  $jsFilesArray All JS files to be load
	 *
	 * @since  1.0.0
	 */
	public function getTjlmsJsFiles(&$jsFilesArray, &$firstThingsScriptDeclaration)
	{
		$app    = JFactory::getApplication();
		$input  = JFactory::getApplication()->input;
		$option = $input->get('option', '');
		$view   = $input->get('view', '');
		$layout = $input->get('layout', '');
		$extension = $input->get('extension', '');

		$config = JFactory::getConfig();
		$debug = $config->get('debug');

		$loadminifiedJs = '';

		if ($debug == 0)
		{
			$loadminifiedJs = '.min';
		}

		// Backend Js files
		if ($app->isAdmin())
		{
			if ($option == "com_tjlms")
			{
				$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/tjlms_admin.js';

				// Load the view specific js
				switch ($view)
				{
					// @TODO - get rid off two auto.js files
					case "modules":
							$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/jquery.form.js';
							$jsFilesArray[] = 'media/techjoomla_strapper/js/akeebajqui.js';
							$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/ajax_file_upload.js';
							$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/tjmodules.js';
							$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/tjlmsvalidator.js';
						break;
					case 'attemptreport':
					case 'course':
					case 'coupon':
							$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/tjlmsvalidator.js';
						break;

						case "dashboard":
						case "teacher_report":
							$jsFilesArray[] = 'components/com_tjlms/assets/js/morris.min.js';
							$jsFilesArray[] = 'components/com_tjlms/assets/js/raphael.min.js';
							$jsFilesArray[] = 'media/system/js/validate.js';
							$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/tjlmsvalidator.js';
						break;
				}
			}

			if ($option == "com_categories" && ($extension == "com_tjlms" || $extension == "com_tmt.questions"))
			{
				$jsFilesArray[] = 'administrator/components/com_tjlms/assets/js/cat_helper.js';
			}
		}
		else
		{
			if ($option == "com_tjlms")
			{
				// Needed for all view
				$jsFilesArray[] = 'components/com_tjlms/assets/js/tjlms' . $loadminifiedJs . '.js';
				$jsFilesArray[] = 'components/com_tjlms/assets/js/sco' . $loadminifiedJs . '.js';

				// Load the view specific js
				switch ($view)
				{
					case "buy":
							$jsFilesArray[] = 'components/com_tjlms/assets/js/fuelux2.3loader.min.js';
							$jsFilesArray[] = 'components/com_tjlms/assets/js/steps' . $loadminifiedJs . '.js';
						break;

					case "course":
							// Check id native sharing is enable
							if ($this->tjlmsparams->get('social_sharing'))
							{
								if ($this->tjlmsparams->get('social_shring_type') == 'native')
								{
									$jsFilesArray[] = 'components/com_tjlms/assets/js/native_share' . $loadminifiedJs . '.js';
								}
							}

						break;
				}
			}
		}

		$reqURI = JUri::root();

		// If host have wwww, but Config doesn't.
		if (isset($_SERVER['HTTP_HOST']))
		{
			if ((substr_count($_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0))
			{
				$reqURI = str_replace("://", "://www.", $reqURI);
			}
			elseif ((substr_count($_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0))
			{
				// Host do not have 'www' but Config does
				$reqURI = str_replace("www.", "", $reqURI);
			}
		}

		// Defind first thing script declaration.
		$loadFirstDeclarations          = "var root_url = '" . $reqURI . "';";
		$firstThingsScriptDeclaration[] = $loadFirstDeclarations;

		return $jsFilesArray;
	}

	/**
	 * This function return array of css files which is loaded from tjassesloader plugin.
	 *
	 * @param   array  &$cssFilesArray  Css file's array.
	 *
	 * @return   ARRAY  $cssFilesArray All Css files to be load
	 *
	 * @since  1.0.0
	 */
	public function getTjlmsCssFiles(&$cssFilesArray)
	{
		$app    = JFactory::getApplication();
		$input  = JFactory::getApplication()->input;
		$option = $input->get('option', '');
		$view   = $input->get('view', '');
		$layout = $input->get('layout', '');
		$extension = $input->get('extension', '');

		$config = JFactory::getConfig();
		$debug = $config->get('debug');

		$loadminifiedCss = '';

		if ($debug == 0)
		{
			$loadminifiedCss = '.min';
		}

		// Backend Css files
		if ($app->isAdmin())
		{
			if ($option == "com_tjlms")
			{
				$cssFilesArray[] = 'media/com_tjlms/font-awesome/css/font-awesome.min.css';
				$cssFilesArray[] = 'media/com_tjlms/css/tjlms_backend.css';

				switch ($view)
				{
					case 'dashboard':
						// $cssFilesArray[] = 'media/com_tjlms/bootstrap3/css/bootstrap.min.css';
						$cssFilesArray[] = 'media/techjoomla_strapper/css/bootstrap.j3.css';
						$cssFilesArray[] = 'media/com_tjlms/css/tjdashboard-sb-admin.css';
					break;
				}
			}
		}
		else
		{
			if ($option == "com_tjlms")
			{
				$cssFilesArray[] = 'components/com_tjlms/assets/css/tjlms' . $loadminifiedCss . '.css';
				$cssFilesArray[] = 'media/com_tjlms/font-awesome/css/font-awesome.min.css';

				switch ($view)
				{
					case 'buy':
						$cssFilesArray[] = 'components/com_tjlms/assets/css/tjlms_steps' . $loadminifiedCss . '.css';
						$cssFilesArray[] = 'components/com_tjlms/assets/css/fuelux2.3.1' . $loadminifiedCss . '.css';
						break;
				}
			}
		}

		return $cssFilesArray;
	}

	/**
	 * Converts date in UTC
	 *
	 * @param   date  $date  date of lesson
	 *
	 * @return   date in utc format
	 *
	 * @since   1.0
	 */
	public function getDateInUtc($date)
	{
		// Change date in UTC
		$user   = JFactory::getUser();
		$config = JFactory::getConfig();
		$offset = $user->getParam('timezone', $config->get('offset'));

		if (!empty($date) && $date != '0000-00-00 00:00:00')
		{
			$udate = JFactory::getDate($date, $offset);
			$date = $udate->toSQL();
		}

		return $date;
	}

	/**
	 * converts date into local time
	 *
	 * @param   date  $date  date of lesson
	 *
	 * @return   date in utc format
	 *
	 * @since   1.0
	 */
	public function getDateInLocal($date)
	{
		if (!empty($date) && $date != '0000-00-00 00:00:00')
		{
			// Create JDate object set to now in the users timezone.
			$date = JHtml::date($date, 'Y-m-d H:i:s', true);
		}

		return $date;
	}

	/**
	 * SOrt given array with the provided column and provided order
	 *
	 * @param   ARRAY   $array   array of data
	 * @param   STRING  $column  column name
	 * @param   STRING  $order   order in which array has to be sort
	 *
	 * @return  ARRAY
	 *
	 * @since   1.0
	 */
	public function multi_d_sort($array, $column, $order)
	{
		if (isset($array) && count($array))
		{
			foreach ($array as $key => $row)
			{
				$orderby[$key] = $row->$column;
			}

			if ($order == 'asc')
			{
				array_multisort($orderby, SORT_ASC, $array);
			}
			else
			{
				array_multisort($orderby, SORT_DESC, $array);
			}
		}

		return $array;
	}

	/**
	 * Wrapper to JRoute to handle itemid We need to try and capture the correct itemid for different view
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compliance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *
	 * @return  url with Itemid
	 *
	 * @since  1.0
	 */
	public function tjlmsRoute($url, $xhtml = true, $ssl = null)
	{
		static $tjlmsitemid = array();

		$mainframe = JFactory::getApplication();
		$jinput = $mainframe->input;

		if (empty($tjlmsitemid[$url]))
		{
			$tjlmsitemid[$url] = self::getitemid($url);
		}

		$pos = strpos($url, '#');

		if ($pos === false)
		{
			if (isset($tjlmsitemid[$url]))
			{
				if (strpos($url, 'Itemid=') === false && strpos($url, 'com_tjlms') !== false)
				{
					$url .= '&Itemid=' . $tjlmsitemid[$url];
				}
			}
		}
		else
		{
			if (isset($tjlmsitemid[$url]))
			{
				$url = str_ireplace('#', '&Itemid=' . $tjlmsitemid[$view] . '#', $url);
			}
		}

		$routedUrl = JRoute::_($url, $xhtml, $ssl);

		return $routedUrl;
	}

	/**
	 * Method to log the comment in provided file
	 *
	 * @param   String  $filename  filename
	 * @param   String  $filepath  filepath
	 * @param   Array   $params    params : Params includes userid, logEntryTitle, desc, component, logType
	 *
	 * @return  array of the replacements
	 *
	 * @since  1.0
	 */
	public function techjoomlaLog($filename, $filepath, $params = array())
	{
		$userid = $params['userid'];
		$desc = $params['desc'];

		$options = "{DATE}\t{TIME}\t{PRIORITY}\t{USER}\t{DESC}";
		jimport('joomla.log.log');
		JLog::addLogger(
				array(
					'text_file' => $filename,
					'text_entry_format' => $options,
					'text_file_path' => $filepath
				),
				JLog::ALL, $params['component']
			);

		$logEntry            = new JLogEntry(
									$params['logEntryTitle'], $params['logType'], $params['component']
								);
		$logEntry->desc      = json_encode($desc);
		$logEntry->user   = $userid;
		JLog::add($logEntry);
	}

	/**
	 * Function used to get users enrollment and account details
	 *
	 * @param   INT  $courseId       Course ID
	 * @param   INT  $enrolled_user  USER ID
	 *
	 * @return  INT  $getEnrollmentDetails  details
	 *
	 * @since  1.0.0
	 */
	public function getEnrollmentDetails($courseId, $enrolled_user)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('e.id as enrollment_id, e.*,u.id as user_id , u.*');
		$query->from($db->quoteName('#__tjlms_enrolled_users', 'e'));
		$query->join('INNER', $db->quoteName('#__users', 'u') .
				' ON (' . $db->quoteName('u.id') . ' = ' . $db->quoteName('e.user_id') . ')');
		$query->where($db->quoteName('e.course_id') . ' = ' . $db->quote($courseId));
		$query->where($db->quoteName('e.user_id') . ' = ' . $db->quote($enrolled_user));
		$db->setQuery($query);
		$enrollmentDetails = $db->loadObject();

		return $enrollmentDetails;
	}

	/**
	 * Function used to get user details
	 *
	 * @param   INT  $user_id  USER DETAILS
	 *
	 * @return  OBJECT  $userDetails  USER DETAILS
	 *
	 * @since  1.0.0
	 */
	public function getUserDetails($user_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('u.*');
		$query->from('#__users as u');
		$query->where('u.id = ' . $user_id);
		$db->setQuery($query);
		$userDetails = $db->loadObject();

		return $userDetails;
	}

	/**
	 * Function used to get course creator and his account details.
	 *
	 * @param   INT  $courseId  Course ID
	 *
	 * @return  INT  $courseCreator  Creator of the course
	 *
	 * @since  1.0.0
	 */
	public function getCourseCreatorDetails($courseId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('c.created_by, u.*');
		$query->from('#__tjlms_courses as c');
		$query->join('INNER', '#__users as u ON u.id =c.created_by');
		$query->where('c.id = ' . $courseId);
		$db->setQuery($query);
		$courseCreator = $db->loadObject();

		return $courseCreator;
	}

	/**
	 * Function used to get test data
	 *
	 * @param   INT  $lesson_id  Lesson ID
	 * @param   INT  $user_id    user_id
	 *
	 * @return  INT  $testData  Test Data
	 *
	 * @since  1.0.0
	 */
	public function getTestData($lesson_id, $user_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('t.test_id, test.total_marks,test.type');
		$query->from('#__tjlms_tmtquiz as t');
		$query->join('INNER', '#__tmt_tests as test ON test.id = t.test_id');
		$query->where('t.lesson_id = ' . $lesson_id);
		$db->setQuery($query);
		$testData = $db->loadObject();

		if ($testData->type == 'plain')
		{
			return $testData;
		}
		else
		{
			$ltquery = $db->getQuery(true);
			$ltquery->select('t.id');
			$ltquery->from('#__tjlms_lesson_track AS t');
			$ltquery->where('t.lesson_id ="' . $lesson_id . '"' . ' and t.user_id ="' . $user_id . '"');
			$ltquery->oder('id', 'DESC');
			$ltquery->setLimit(1);

			// Return id for set based quiz
			$query = $db->getQuery(true);
			$query->select(' ta.test_id');
			$query->from(' #__tmt_tests_attendees AS ta');
			$query->where(' ta.invite_id =(' . $ltquery . ')');

			$db->setQuery($query);

			$testData->test_id = $db->loadResult();
		}

		return $testData;
	}

	/**
	 * Function used to revenue data
	 *
	 * @param   INT  $data  Data
	 *
	 * @return  INT  $revenueData  Revenue Data
	 *
	 * @since  1.0.0
	 */
	public function getrevenueData($data)
	{
		$user = JFactory::getUser();
		$olUserid = $user->id;
		$isroot = $user->authorise('core.admin');

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('SUM(o.amount) as amount, DATE(o.cdate) as date');
		$query->from('#__tjlms_orders as o');
		$query->join('LEFT', '#__tjlms_courses as c ON c.id = o.course_id');
		$query->where('o.status="C"');

		if (!$isroot)
		{
			$query->where('created_by=' . $olUserid);
		}

		if (isset($data['course_id']) && $data['course_id'] != '')
		{
			$query->where('o.course_id=' . $data['course_id']);
		}

		if (isset($data['start']) && $data['start'] != '' && isset($data['end']) && $data['end'] != '')
		{
			$query->where("( o.cdate BETWEEN " . $db->quote($data['start']) . " AND " . $db->quote($data['end']) . " )");
		}

		$query->group('DATE(o.cdate)');

		$db->setQuery($query);
		$revenueData = $db->loadObjectlist();

		return $revenueData;
	}

	/**
	 * Get all jtext for javascript
	 *
	 * @return   void
	 *
	 * @since   1.0
	 */
	public static function getLanguageConstant()
	{
		JText::script('COM_TJLMS_WANTED_TO_APPLY_COP_BUT_NOT_APPLIED');
		JText::script('COM_TJLMS_REQUIRE_FIELDS');
		JText::script('COM_TJLMS_TERMS_AND_CONDITION');
		JText::script('COM_TJLMS_MAX_USER_VALIDATION');
	}

	/**
	 * Function to get order status
	 *
	 * @param   INT  $order_id  Order ID
	 *
	 * @return  Object of result
	 *
	 * @since   1.0.0
	 */
	public function getOrderStatus($order_id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Add Table Path
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');

		$orderTbl = JTable::getInstance('Orders', 'TjlmsTable', array('dbo', $db));
		$orderTbl->load(array('id' => $order_id));

		return $orderTbl->status;
	}

	/**
	 * Function of format number
	 *
	 * @param   int      $n          number
	 * @param   boolean  $precision  precision number to format
	 *
	 * @return  sting  	 Formatted value
	 *
	 * @since   1.0
	 */
	public function custom_number_format($n, $precision = 1)
	{
		if ($n < 1000)
		{
			$n_format = $n;
		}
		elseif ($n < 1000000)
		{
			// Anything less than a million
			$n_format = number_format($n / 1000, $precision) . 'K';
		}
		elseif ($n < 1000000000)
		{
			// Anything less than a billion
			$n_format = number_format($n / 1000000, $precision) . 'M';
		}
		else
		{
			// At least a billion
			$n_format = number_format($n / 1000000000, $precision) . 'B';
		}

		return $n_format;
	}

	/**
	 * Change seconds to readable format
	 *
	 * @param   int  $init  Time in second
	 *
	 * @return  string
	 */
	public function secToHours($init)
	{
		$hours = floor($init / 3600);
		$minutes = floor(($init / 60) % 60);
		$seconds = $init % 60;

		if ($hours)
		{
			return JText::sprintf('COM_TJLMS_HOURS_FORMAT', $hours, $minutes, $seconds);
		}
		elseif ($minutes)
		{
			return JText::sprintf('COM_TJLMS_MINUTES_FORMAT', $minutes, $seconds);
		}
		else
		{
			return JText::sprintf('COM_TJLMS_SECONDS_FORMAT', $seconds);
		}
	}

	/**
	 * Method to get allow rating to bought the product user
	 *
	 * @param   INTEGER  $access     View ACL Id
	 * @param   BOOLEAN  $recursive  Fetch Child groups as well
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function getACLGroups($access, $recursive=true)
	{
		$groups = array();

		if (!empty($access))
		{
			$db		= JFactory::getDBO();
			$query 	= $db->getQuery(true);
			$query->select('rules');
			$query->from($db->quoteName('#__viewlevels'));
			$query->where('id =' . (int) $access);
			$db->setQuery($query);
			$rules = $db->loadResult();

			if ($rules)
			{
				$rules 	= json_decode($rules, true);
				ArrayHelper::toInteger($rules);
				$level = false;

				if (!empty($rules))
				{
					$allGroups	 = JHelperUsergroups::getInstance()->getAll();

					foreach ($allGroups as $groupId => $group)
					{
						if ($recursive && $level !== false && $group->level > $level)
						{
							++$levelStr;
							$levelStr = ($group->level < $levelStr) ? ($group->level) : ($levelStr);
							$groups[$groupId] = $group;
							$group->title  = str_repeat('- ', $levelStr) . $group->title;
						}
						elseif (in_array($groupId, $rules) && !array_key_exists($groupId, $groups))
						{
							$groups[$groupId] = $group;
							$level = $group->level;
							$levelStr = 0;
							$group->title  = str_repeat('- ', $levelStr) . $group->title;
						}
						else
						{
							$level = false;
						}
					}
				}
			}
		}

		return $groups;
	}

	/**
	 * Check the user is Course creator or not
	 *
	 * @param   string  $reportName  reportname
	 *
 	* @return result format
 	*
 	* @since 1.0
 	*
 	*/
	public function getManagerFilter($reportName)
	{
		if (JFile::exists(JPATH_ROOT . '/components/com_hierarchy/hierarchy.php'))
		{
			if (JComponentHelper::isEnabled('com_hierarchy', true))
			{
				$path = JPATH_SITE . '/components/com_hierarchy/helpers/hierarchy.php';
				$user = JFactory::getUser();
				$user_id = $user->id;
				$db      = JFactory::getDBO();

				if (!class_exists('HierarchyFrontendHelper'))
				{
					// Require_once $path;
					JLoader::register('HierarchyFrontendHelper', $path);
					JLoader::load('HierarchyFrontendHelper');
				}

				$isroot = $user->authorise('core.admin');
				$HierarchyFrontendHelper = new HierarchyFrontendHelper;
				$this->isManager = $HierarchyFrontendHelper->checkManager();

				$mainframe = JFactory::getApplication();
				$stateVar = $mainframe->getUserState('com_tjreports' . '.' . $reportName . '_table_filters');

				$selectedField = isset($stateVar['userType']) ? $stateVar['userType'] : 'creator';

				// Check for course creator
				JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
				$table = JTable::getInstance('course', 'TjlmsTable', array('dbo', $db));

				// Check if user is course creator
				$this->isCourseCreator = $table->load(array('created_by' => (int) $user_id));

				if ($isroot || $this->isManager ||  $this->isCourseCreator)
				{
					$ind = 0;

					if ($isroot)
					{
						$data[$ind]['value'] = 'all';
						$data[$ind++]['text'] = JText::_('COM_TJLMS_ALL_REPORTS');
					}

					if ($this->isManager)
					{
						$data[$ind]['value'] = 'team';
						$data[$ind++]['text'] = JText::_('COM_TJLMS_TEAM_REPORTS');
					}

					if ($this->isCourseCreator)
					{
						$data[$ind]['value'] = 'creator';
						$data[$ind]['text'] = JText::_('COM_TJLMS_CREATED_BY_ME_REPORTS');
					}
				}

				$options = array(
								'id' => 'search-filter-userType', // HTML id for select field
								'name' => 'search-filter-userType',
								'list.attr' => array(
													// Additional HTML attributes for select field
													'class' => 'filter-input input-medium',
													'onchange' => 'getFilterdata();'),
								// True to translate
								'list.translate' => false,
								// Key name for value in data array
								'option.key' => 'value',
								// Key name for text in data array
								'option.text' => 'text',
								'list.select' => ($selectedField), // Value of the SELECTED field
								);

				$result = JHtmlSelect::genericlist($data, 'search-filter-userType', $options);

				return $result;
			}
		}
	}

	/**
	 * Function to get Time spent on Course for that year
	 *
	 * @param   INT  $userId      logged in userId
	 * 
	 * @param   INT  $courseId    course_id
	 * 
	 * @param   INT  $profession  profession
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function timeSpentOnCourse($userId, $courseId, $profession)
	{
		$cpdDate = $this->getCpdDate($userId);
		$date = JFactory::getDate()->Format('Y');

		if (!empty($cpdDate))
		{
			if ($profession == 'Dental professionals')
			{
				if ($date >= date("Y", strtotime($cpdDate)) && $date <= date("Y", strtotime($cpdDate)) + 4)
				{
					// Get a db connection.
					$db = JFactory::getDbo();

					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where('YEAR(' . $db->quoteName('lt.timestart') . ') = YEAR(CURRENT_DATE)');

					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$result = $db->loadResult();

					if ($result)
					{
						return $result;
					}
				}
			}
			else
			{
				$tempDate = new JDate($cpdDate);
				$tempDate->setDate($cpdDate, 8, 01);

				$flagDate = $tempDate->setDate($tempDate->year, 7, 31);

				if ($cpdDate <= $flagDate->toSql())
				{
					$cycleStartDateForOther = new JDate($cpdDate . '-1 year');
					$cycleStartDateForOther = $cycleStartDateForOther->setDate($cycleStartDateForOther->year, 8, 01)->format('Y-m-d');
				}
				else
				{
					$cycleStartDateForOther = new JDate($cpdDate);
					$cycleStartDateForOther = $cycleStartDateForOther->setDate($cycleStartDateForOther->year, 8, 01)->format('Y-m-d');
				}

				$cycleEndDateForOther = new JDate($cycleStartDateForOther . '+5 year');
				$cycleEndDateForOther = $cycleEndDateForOther->setDate($cycleEndDateForOther->year, 7, 31)->format('Y-m-d');

				$currentDate = JFactory::getDate();
				$currentDate = date('Y-m-d', strtotime($currentDate));
				$cycleStartDateForOther = date('Y-m-d', strtotime($cycleStartDateForOther));
				$cycleEndDateForOther = date('Y-m-d', strtotime($cycleEndDateForOther));

				if ($currentDate >= $cycleStartDateForOther && $currentDate <= $cycleEndDateForOther)
				{
					$tempDate = new JDate($currentDate);
					$tempDate->setDate($currentDate, 8, 01);

					$flagDate = $tempDate->setDate($tempDate->year, 7, 31);

					if ($currentDate <= $flagDate->toSql())
					{
						$thisYearStartDateForOther = new JDate($currentDate . '-1 year');
						$thisYearStartDateForOther = $thisYearStartDateForOther->setDate($thisYearStartDateForOther->year, 8, 01)->format('Y-m-d');
					}
					else
					{
						$thisYearStartDateForOther = new JDate($currentDate);
						$thisYearStartDateForOther = $thisYearStartDateForOther->setDate($thisYearStartDateForOther->year, 8, 01)->format('Y-m-d');
					}

					$thisYearEndDateForOther = new JDate($thisYearStartDateForOther . '+1 year');
					$thisYearEndDateForOther = $thisYearEndDateForOther->setDate($thisYearEndDateForOther->year, 7, 31)->format('Y-m-d');

					// Get a db connection.
					$db = JFactory::getDbo();

					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where($db->quoteName('lt.timestart') . ' >= ' . $db->quote($thisYearStartDateForOther));
					$query->where($db->quoteName('lt.timeend') . ' <= ' . $db->quote($thisYearEndDateForOther));

					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$result = $db->loadResult();

					if ($result)
					{
						return $result;
					}
				}
			}
		}
		elseif (empty($cpdDate))
		{
			$currentDate = JFactory::getDate();

			if ($profession == 'Dental professionals')
			{
				$cycleStartDateForDentist = new JDate($currentDate);
				$cycleStartDateForDentist = $cycleStartDateForDentist->setDate($cycleStartDateForDentist->year, 1, 01)->format('Y-m-d');

				$cycleEndDateForDentist = new JDate($cycleStartDateForDentist . '+4 year');
				$cycleEndDateForDentist = $cycleEndDateForDentist->setDate($cycleEndDateForDentist->year, 12, 31)->format('Y-m-d');

				if (date("Y", strtotime($currentDate)) >= date("Y", strtotime($cycleStartDateForDentist))
					&& date("Y", strtotime($currentDate)) <= date("Y", strtotime($cycleEndDateForDentist)))
				{
					// Get a db connection.
					$db = JFactory::getDbo();

					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where('YEAR(' . $db->quoteName('lt.timestart') . ') = YEAR(CURRENT_DATE)');

					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$result = $db->loadResult();

					if (!empty($result))
					{
						return $result;
					}
				}
			}
			else
			{
				$tempDate = new JDate($currentDate);
				$tempDate->setDate($currentDate, 8, 01);

				$flagDate = $tempDate->setDate($tempDate->year, 7, 31);

				if ($currentDate <= $flagDate->toSql())
				{
					$cycleStartDateForOther = new JDate($currentDate . '-1 year');
					$cycleStartDateForOther = $cycleStartDateForOther->setDate($cycleStartDateForOther->year, 8, 01)->format('Y-m-d');
				}
				else
				{
					$cycleStartDateForOther = new JDate($currentDate);
					$cycleStartDateForOther = $cycleStartDateForOther->setDate($cycleStartDateForOther->year, 8, 01)->format('Y-m-d');
				}

				$cycleEndDateForOther = new JDate($cycleStartDateForOther . '+5 year');
				$cycleEndDateForOther = $cycleEndDateForOther->setDate($cycleEndDateForOther->year, 7, 31)->format('Y-m-d');

				$currentDate = date('Y-m-d', strtotime($currentDate));
				$cycleStartDateForOther = date('Y-m-d', strtotime($cycleStartDateForOther));
				$cycleEndDateForOther = date('Y-m-d', strtotime($cycleEndDateForOther));

				if ($currentDate >= $cycleStartDateForOther && $currentDate <= $cycleEndDateForOther)
				{
					if ($currentDate <= $flagDate->toSql())
					{
						$thisYearStartDateForOther = new JDate($currentDate . '-1 year');
						$thisYearStartDateForOther = $thisYearStartDateForOther->setDate($thisYearStartDateForOther->year, 8, 01)->format('Y-m-d');
					}
					else
					{
						$thisYearStartDateForOther = new JDate($currentDate);
						$thisYearStartDateForOther = $thisYearStartDateForOther->setDate($thisYearStartDateForOther->year, 8, 01)->format('Y-m-d');
					}

					$thisYearEndDateForOther = new JDate($thisYearStartDateForOther . '+1 year');
					$thisYearEndDateForOther = $thisYearEndDateForOther->setDate($thisYearEndDateForOther->year, 7, 31)->format('Y-m-d');

					// Get a db connection.
					$db = JFactory::getDbo();

					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where($db->quoteName('lt.timestart') . ' >= ' . $db->quote($thisYearStartDateForOther));
					$query->where($db->quoteName('lt.timeend') . ' <= ' . $db->quote($thisYearEndDateForOther));

					// Reset the query using our newly populated query object.
					$db->setQuery($query);
					$result = $db->loadResult();

					if (!empty($result))
					{
						return $result;
					}
				}
			}
		}
	}

	/**
	 * Function to get Total Time, spent on Course for all years
	 *
	 * @param   INT  $userId    logged in userId
	 * 
	 * @param   INT  $courseId  course_id
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function totalTimeSpentOnCourse($userId, $courseId)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
		$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
		$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
		$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result)
		{
			return $result;
		}
	}

	/**
	 * Function to get Total Time spent on Course for 5 years cycle
	 *
	 * @param   INT     $userId      logged in userId
	 * 
	 * @param   INT     $courseId    course_id
	 * 
	 * @param   STRING  $profession  profession
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function totalFiveYearTimeSpentOnCourse($userId, $courseId, $profession)
	{
		$cpdDate = $this->getCpdDate($userId);

		if (!empty($cpdDate))
		{
			// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
			$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
			$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
			$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
			$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);

			if ($profession == 'Dental professionals')
			{
				$cycleStartDateForDentist = new JDate($cpdDate);
				$cycleStartDateForDentist = $cycleStartDateForDentist->setDate($cycleStartDateForDentist->year, 1, 01)->format('Y-m-d');

				$cycleEndDateForDentist = new JDate($cycleStartDateForDentist . '+4 year');
				$cycleEndDateForDentist = $cycleEndDateForDentist->setDate($cycleEndDateForDentist->year, 12, 31)->format('Y-m-d');

				$query->where('YEAR(' . $db->quoteName('lt.timestart') . ') >= YEAR(' . $db->quote($cycleStartDateForDentist) . ')');
				$query->where('YEAR(' . $db->quoteName('lt.timeend') . ') <= YEAR(' . $db->quote($cycleEndDateForDentist) . ')');
			}
			else
			{
				$tempDate = new JDate($cpdDate);
				$tempDate->setDate($cpdDate, 8, 01);

				$flagDate = $tempDate->setDate($tempDate->year, 7, 31);

				if ($cpdDate <= $flagDate->toSql())
				{
					$cycleStartDateForOther = new JDate($cpdDate . '-1 year');
					$cycleStartDateForOther = $cycleStartDateForOther->setDate($cycleStartDateForOther->year, 8, 01)->format('Y-m-d');
				}
				else
				{
					$cycleStartDateForOther = new JDate($cpdDate);
					$cycleStartDateForOther = $cycleStartDateForOther->setDate($cycleStartDateForOther->year, 8, 01)->format('Y-m-d');
				}

				$cycleEndDateForOther = new JDate($cycleStartDateForOther . '+5 year');
				$cycleEndDateForOther = $cycleEndDateForOther->setDate($cycleEndDateForOther->year, 7, 31)->format('Y-m-d');

				$query->where($db->quoteName('lt.timestart') . ' > ' . $db->quote($cycleStartDateForOther));
				$query->where($db->quoteName('lt.timeend') . ' <' . $db->quote($cycleEndDateForOther));
			}

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$result = $db->loadResult();

			if (!empty($result))
			{
				return $result;
			}
		}
		else
		{
			return JText::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_FIVE_YEAR_CYCLE');
		}
	}

	/**
	 * Function to get Time spent for completing course first time
	 *
	 * @param   INT  $userId    logged in userId
	 *
	 * @param   INT  $courseId  course_id
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function timeSpentforCompletingCourse($userId, $courseId)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select(array($db->quoteName('ct.user_id'), 'SEC_TO_TIME(SUM( TIME_TO_SEC(lt.time_spent))) AS firstCompleted'));
		$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
		$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
		$query->join('LEFT', $db->quoteName('#__tjlms_course_track', 'ct') . 'ON' . $db->quoteName('ct.course_id') . '=' . $db->quoteName('l.course_id'));
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
		$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
		$query->where($db->quoteName('lt.attempt') . ' = ' . (int) 1);
		$query->where('(' . $db->quoteName('lt.timestart') . ' BETWEEN ' . $db->quoteName('ct.timestart') . ' AND ' . $db->quoteName('ct.timeend') . ')');
		$query->group($db->quoteName('ct.id'));
		$query->having($db->quoteName('ct.user_id') . " = " . (int) $userId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$result = $result[0]->firstCompleted;

		if ($result)
		{
			return $result;
		}
	}

	/**
	 * Function to get Survey answer
	 *
	 * @param   INT  $userId    logged in userId
	 *
	 * @param   INT  $courseId  course_id
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getSurveyAnswer($userId, $courseId)
	{
		$questionText = "%For your CPD record, identify which of the following high level outcomes this training has met for you%";

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get Survey Id
		$query->select($db->quoteName('tm.source'));
		$query->from($db->quoteName('#__tjlms_media', 'tm'));
		$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.media_id') . '=' . $db->quoteName('tm.id'));
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
		$query->where($db->quoteName('l.format') . ' = ' . $db->quote('survey'));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$surveyIds = $db->loadColumn();

		if (!empty($surveyIds))
		{
			foreach ($surveyIds as $surveyId)
			{
				// Create a new query object.
				$query = $db->getQuery(true);

				// Get starts id
				$query->select($db->quoteName('sfus.id'));
				$query->from($db->quoteName('#__survey_force_user_starts', 'sfus'));
				$query->where($db->quoteName('sfus.survey_id') . ' = ' . (int) $surveyId);
				$query->where($db->quoteName('sfus.user_id') . ' = ' . (int) $userId);
				$db->setQuery($query);
				$startIds = $db->loadColumn();

				if (!empty($startIds))
				{
					foreach ($startIds as $startId)
					{
						$questionId = $this->getQuestionId($surveyId, $questionText);

						// Create a new query object.
						$query = $db->getQuery(true);

						// Get order of answer
						$query->select(' DISTINCT ' . $db->quoteName('sf.ordering'));
						$query->from($db->quoteName('#__survey_force_fields', 'sf'));
						$query->join(
						'LEFT', $db->quoteName('#__survey_force_user_answers', 'sa') . 'ON' . $db->quoteName('sa.quest_id') . '=' . $db->quoteName('sf.quest_id')
						);
						$query->join(
						'LEFT', $db->quoteName('#__survey_force_quests', 'sq') . 'ON' . $db->quoteName('sq.sf_survey') . '=' . $db->quoteName('sa.survey_id')
						);
						$query->where($db->quoteName('sa.start_id') . ' = ' . (int) $startId);
						$query->where($db->quoteName('sa.quest_id') . ' = ' . (int) $questionId);
						$query->where($db->quoteName('sf.id') . ' = ' . $db->quoteName('sa.answer'));
						$query->where($db->quoteName('sq.sf_qtext') . ' LIKE ' . $db->quote($db->escape($questionText)));

						$db->setQuery($query);
						$answers = $db->loadColumn();

						if (!empty($answers))
						{
							return $answers;
						}
					}
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Function to get CPD date
	 *
	 * @param   INT  $userId  logged in userId
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getCpdDate($userId)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('sfd.data'));
		$query->from($db->quoteName('#__social_fields_data', 'sfd'));
		$query->join(
						'LEFT', $db->quoteName('#__social_fields', 'sf') . 'ON' . $db->quoteName('sf.id') . '=' . $db->quoteName('sfd.field_id')
						);
		$query->where($db->quoteName('sfd.uid') . " = " . (int) $userId);
		$query->where($db->quoteName('sf.unique_key') . ' = "CPD5YEARCYCLESTARTS"');
		$query->where($db->quoteName('sfd.datakey') . ' = "date"');

		$db->setQuery($query);
		$result = $db->loadResult();

		if (!empty($result))
		{
			return $result;
		}
	}

	/**
	 * Function to get Question Id
	 *
	 * @param   INT     $surveyId      surveyId
	 * 
	 * @param   STRING  $questionText  questionText
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getQuestionId($surveyId, $questionText)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('sq.id'));
		$query->from($db->quoteName('#__survey_force_quests', 'sq'));
		$query->where($db->quoteName('sq.sf_survey') . " = " . (int) $surveyId);
		$query->where($db->quoteName('sq.sf_qtext') . " LIKE " . $db->quote($db->escape($questionText)));

		$db->setQuery($query);
		$result = $db->loadResult();

		if (!empty($result))
		{
			return $result;
		}
	}
}
