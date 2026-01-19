<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.helper');
jimport('techjoomla.common');
require_once JPATH_SITE . "/components/com_tjfields/filterFields.php";

require_once JPATH_SITE . '/components/com_tjlms/helpers/courses.php';
/**
 * Methods supporting a list of Tjlms records.
 *
 * @since  1.0.0
 */
class TjlmsModelCourse extends JModelAdmin
{
	use TjfieldsFilterField;

	/**
	 * Constructor.
	 *
	 * @see     JControllerLegacy
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function __construct()
	{
		$this->ComtjlmsHelper   = new ComtjlmsHelper;

		// Added by renu
		$this->coursesHelper = new TjlmsCoursesHelper;
		$this->techjoomlacommon = new TechjoomlaCommon;

		parent::__construct();
	}

	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_TJLMS';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return   JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Course', $prefix = 'TjlmsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_tjlms.course', 'course', array(
																	'control' => 'jform',
																	'load_data' => $loadData
																)
								);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tjlms.edit.course.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return   mixed  Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			$input       = JFactory::getApplication()->input;
			$params      = JComponentHelper::getParams('com_tjlms');
			$enable_tags = $params->get('enable_tags', '0', 'INT');
			$user = JFactory::getUser();

			// Do any procesing on fields here if needed
			if ($input->get('id', '', 'INT'))
			{
				$db    = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('tsp.* FROM #__tjlms_subscription_plans as tsp');
				$query->where('tsp.course_id=' . $input->get('id', '', 'INT'));
				$db->setQuery($query);
				$subsplans = $db->loadObjectlist();

				if ($subsplans)
				{
					$item->subsplans = $subsplans;
				}

				$item->access = explode('|', $item->access);
				$item->access = array_filter($item->access, "trim");

				if (JVERSION >= '3.0')
				{
					if ($enable_tags == 1)
					{
						if (!empty($item->id))
						{
							$item->tags = new JHelperTags;
							$item->tags->getTagIds($item->id, 'com_tjlms.course');
						}
					}
				}

				if ($params->get('social_integration', '', 'STRING') == 'easysocial')
				{
					if (isset($item->params['esbadges']) && !empty($item->params['esbadges']))
					{
						$item->esbadges = $item->params['esbadges'];
					}
				}

				// Convert parameter fields to objects.
				$registry = new Registry;
				$item->params = $registry->loadArray($item->params);

				// Technically guest could edit an course, but lets not check that to improve performance a little.
				if (!$user->get('guest'))
				{
					$userId = $user->get('id');
					$asset = 'com_tjlms.course.' . $item->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$item->params->set('access-edit', true);
					}

					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.create', $asset))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $item->created_by)
						{
							$item->params->set('access-edit', true);
						}
					}

					// Check edit state permission.
					if ($pk)
					{
						// Existing item
						$item->params->set('access-change', $user->authorise('core.edit.state', $asset) || $user->authorise('core.create', $asset));
					}
					else
					{
						// New item.
						$catId = (int) $this->getState('course.cat_id');

						if ($catId)
						{
							$item->params->set('access-change', $user->authorise('core.edit.state', 'com_tjlms.category.' . $catId));
							$item->cat_id = $catId;
						}
						else
						{
							$item->params->set('access-change', $user->authorise('core.edit.state', 'com_tjlms') || $user->authorise('core.create', 'com_tjlms'));
						}
					}
				}
			}
			else
			{
				// To set today's date as default for new corse
				$item->start_date = JFactory::getDate();
			}
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Jtable  $table  table instance
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tjlms_courses');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Method to  save course and its subscription plans
	 *
	 * @param   ARRAY  $data              course data
	 * @param   ARRAY  $extra_jform_data  extra form data
	 * @param   ARRAY  $post              post data
	 *
	 * @return  course ID
	 *
	 * @since    1.0.0
	 */
	public function save($data, $extra_jform_data='', $post='')
	{
		$db    = JFactory::getDBO();
		$files = $post->files->get('jform', '', 'array');

		/*@TODO : add commented code for tjField integration*/

		if (!empty($extra_jform_data))
		{
			$data['content_id'] = $data['id'];
			$data['client'] = 'com_tjlms.course';
			$data['fieldsvalue'] = $extra_jform_data;

			$this->saveExtraFields($data);
		}

		$table = $this->getTable();

		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('course.id');

		$user = JFactory::getUser();
		$userId = $user->get('id');
		$app  = JFactory::getApplication();
		$params      = JComponentHelper::getParams('com_tjlms');
		$integration = $params->get('social_integration', '', 'STRING');

		$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

		if (empty($ownerId))
		{
			$data['created_by'] = $user->id;
		}

		$canCreate 	= $user->authorise('core.create', 'com_tjlms');
		$canManage	= $user->authorise('core.manage', 'com_checkin');
		$manageOwn	= $canCreate && $userId == $data['created_by'];

		if ($id)
		{
			$authorised 	= $user->authorise('core.edit', 'com_tjlms.course.' . $data['id']) || $manageOwn;
			$data['modified'] = $this->techjoomlacommon->getDateInUtc(JHtml::date('now', 'Y-m-d H:i:s', true));
		}
		else
		{
			$authorised 	= $canCreate;
			$data['created'] = $this->techjoomlacommon->getDateInUtc(JHtml::date('now', 'Y-m-d H:i:s', true));
		}

		if (!$authorised)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		$table = $this->getTable();

		// Save ES badge if applicable
		if ($integration == 'easysocial' && $data['esbadges'])
		{
			// Load the row if saving an existing category.
			if ($id > 0)
			{
				$table->load($id);
			}

			$courseParams = array();

			if ($table->params)
			{
				$courseParams = (array) json_decode($table->params);
			}

			$courseParams['esbadges'] = $data['esbadges'];

			$data['params'] = json_encode($courseParams);
		}

		// Bind data
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Tweak for image file upload.
		// Store uploaded file path in a temp variable.
		$tempImage = '';

		if (!empty($files['image']['name']))
		{
			$tempImage = $files['image'];
		}

		// Attempt to save data
		if (parent::save($data))
		{
			$id = (int) $this->getState($this->getName() . '.id');

			// Trigger on after course.
			if (empty($data['id']) && $id && $data['state'] == 1)
			{
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('system');
				$dispatcher->trigger('onAfterCourseCreation', array(
																		$id,
																		$user->id,
																		$data['title']
																	)
									);
			}
		}

		// Save subscription plans for the course.
		if (isset($data['type']) && $data['type'] == 1)
		{
			$insert_subs_plan = $this->insert_subs_plan($id, $data);
		}

		// Restore the unsetted image file index from data array.
		if ($tempImage != '')
		{
			// Save uploaded image.
			require_once JPATH_SITE . "/components/com_tjlms/helpers/media.php";

			$tjlmsmediaHelper  = new TjlmsMediaHelper;
			$orginale_filename = $tjlmsmediaHelper->imageupload('course');

			if (!$orginale_filename)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_TJLMS_UPLOAD_IMAGE_ERROR'), 'error');
				$link = JUri::root() . 'administrator/index.php?option=com_tjlms&view=course&layout=edit&id=' . $id;
				$app->redirect($link);

				return false;
			}

			// Save event id into integration xref table.
			$obj        = new stdclass;
			$obj->id    = $id;
			$obj->image = $orginale_filename;
			$obj->storage = 'local';

			if ($orginale_filename)
			{
				if (!$db->updateObject('#__tjlms_courses', $obj, 'id'))
				{
					echo $db->stderr();

					return false;
				}
			}
		}

		$group_created = '';

			// Auto create a group for each course depending upon the integration set.

			$autoCreateGroup = $params->get('group_integration', '0', 'INT');

			if ($autoCreateGroup == 1)
			{
				$group_created = $this->saveCourseGroup($data);
			}

		if ($group_created)
		{
			// Save group ID in courses table
			$obj           = new stdclass;
			$obj->id       = $id;
			$obj->group_id = $group_created;

			if ($group_created)
			{
				if (!$db->updateObject('#__tjlms_courses', $obj, 'id'))
				{
					echo $db->stderr();

					return false;
				}
			}
		}

		if (!$id)
		{
			return false;
		}

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$dispatcher->trigger('onAftercourseCreate', array(
														$id,
														$user->id
													)
							);

		return $id;
	}

	/**
	 * Create group depending upon the integration set
	 *
	 * @param   ARRAY  $data  Course data
	 *
	 * @return   INT  Group ID
	 *
	 * @since 1.0.0
	 */
	public function saveCourseGroup($data)
	{
		$params      = JComponentHelper::getParams('com_tjlms');
		$integration = $params->get('social_integration');
		$groupId = '';

		$catId = $params->get('groupCategory', '', 'INT');

		if ($catId)
		{
			$data['uid'] = $data['created_by'];
			$options['catId'] = $catId;
			$groupId = $this->ComtjlmsHelper->sociallibraryobj->createGroup($data, $options);

			// Add admin to the created group
			$this->ComtjlmsHelper->sociallibraryobj->addMemberToGroup($groupId, JFactory::getUser());
		}

		return $groupId;
	}

	/**
	 * Method to  save subscription plans for a course
	 *
	 * @param   INT    $course_id  Course ID
	 * @param   ARRAY  $data       Course related data
	 *
	 * @return  boolean  true false
	 *
	 * @since    1.0.0
	 */
	public function insert_subs_plan($course_id, $data)
	{
		$input = JFactory::getApplication()->input;
		$post  = $input->post;
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id FROM #__tjlms_subscription_plans');
		$query->where('course_id=' . $course_id);
		$db->setQuery($query);
		$plan_ids = $db->loadColumn();

		$coursType  = $data['type'];
		$subs_plans = $post->get('subs_plan', '', 'ARRAY');

		// Only if course is paid
		if ($coursType == 1)
		{
			foreach ($subs_plans as $each_plan)
			{
				$obj               = new stdClass;
				$obj->course_id    = $course_id;
				$obj->time_measure = $each_plan['time_measure'];
				$obj->duration     = $each_plan['duration'];
				$obj->price        = $each_plan['price'];

				if ($each_plan['id'] == '')
				{
					$obj->id = '';

					if ($obj->duration != '' && $obj->price != '')
					{
						if (!$db->insertObject('#__tjlms_subscription_plans', $obj, 'id'))
						{
							echo $db->stderr();

							return false;
						}
					}
				}
				else
				{
					$obj->id = $each_plan['id'];

					if (!$db->updateObject('#__tjlms_subscription_plans', $obj, 'id'))
					{
						echo $db->stderr();

						return false;
					}

					if (($key = array_search($each_plan['id'], $plan_ids)) !== false)
					{
						unset($plan_ids[$key]);
					}
				}
			}
		}

		$plan_to_delet = implode(',', $plan_ids);

		if ($plan_to_delet)
		{
			$query = "DELETE FROM #__tjlms_subscription_plans WHERE id IN (" . $plan_to_delet . ")";
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/**
	 * Function used to delete all data with respect to course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function onafterCourseDelete($cid)
	{
		// Delete all enrolled user list with respect to this course
		$deleteEnrolledUser = $this->deleteEnrolledUserForCourse($cid);

		// Delete all coursetracks of the course 'helper'
		$deleteCourseTracks = $this->coursesHelper->deleteCourseTracks($cid);

		// Delete all lessons with respect to this course
		$deleteLesson = $this->deleteLessonForCourse($cid);

		// Delete all subscription plans with respect to this course
		$deletePlan = $this->deleteSubsPlanForCourse($cid);

		// Delete all orders with respect to this course
		$deleteOrder = $this->deleteOrdersForCourse($cid);

		// Delete all activity of the course
		$deleteActivity = $this->deleteLmsActivities($cid);

		// Delete all modules of the course
		$deleteModules = $this->deleteModulesForCourse($cid);

		// Trigger on after course/s delete
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$dispatcher->trigger('onAfterCourseDelete', array($cid));

		return true;
	}

	/**
	 * Delete all activities of the course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function deleteLmsActivities($cid)
	{
		$cidString = implode(',', $cid);
		$db        = JFactory::getDbo();

		$query      = $db->getQuery(true);
		$conditions = array(
			$db->quoteName('parent_id') . ' IN (' . $cidString . ')'
		);

		$query->delete($db->quoteName('#__tjlms_activities'));
		$query->where($conditions);

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	/**
	 * Function used to delete all enrolled users with respect to course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function deleteEnrolledUserForCourse($cid)
	{
		$cidString = implode(',', $cid);
		$db        = JFactory::getDbo();

		$query      = $db->getQuery(true);
		$conditions = array(
			$db->quoteName('course_id') . ' IN (' . $cidString . ')'
		);

		$query->delete($db->quoteName('#__tjlms_enrolled_users'));
		$query->where($conditions);

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	/**
	 * Function used to delete all lessons and lessontracks with respect to course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function deleteLessonForCourse($cid)
	{
		$cidString = implode(',', $cid);
		$db        = JFactory::getDbo();

		// Get lesson Ids of selected course
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__tjlms_lessons');
		$query->where('course_id IN (' . $cidString . ')');
		$db->setQuery($query);

		$lessonIds = $db->loadColumn();

		require_once JPATH_SITE . '/components/com_tjlms/helpers/lesson.php';
		$this->lessonHelper = new TjlmsLessonHelper;
		$deleteLesson = $this->lessonHelper->deletLesson($cid, '0', $lessonIds);

	/*		// Delete all the lessons related to course
			$query      = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('course_id') . ' IN (' . $cidString . ')'
			);

			$query->delete($db->quoteName('#__tjlms_lessons'));
			$query->where($conditions);

			$db->setQuery($query);
			$results = $db->execute();

			return $results;
		}*/
	}

	/**
	 * Function used to delete all subsplan with respect to course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function deleteSubsPlanForCourse($cid)
	{
		$cidString = implode(',', $cid);
		$db        = JFactory::getDbo();

		$query      = $db->getQuery(true);
		$conditions = array(
			$db->quoteName('course_id') . ' IN (' . $cidString . ')'
		);

		$query->delete($db->quoteName('#__tjlms_subscription_plans'));
		$query->where($conditions);

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	/**
	 * Method to toggle the featured setting of Courses.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_TJLMS_COURSES_NO_ITEM_SELECTED'));

			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
						->update($db->quoteName('#__tjlms_courses'))
						->set('featured = ' . (int) $value)
						->where('id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Delete all modules of the course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function deleteModulesForCourse($cid)
	{
		$cidString = implode(',', $cid);
		$db        = JFactory::getDbo();

		$query      = $db->getQuery(true);
		$conditions = array(
			$db->quoteName('course_id') . ' IN (' . $cidString . ')'
		);

		$query->delete($db->quoteName('#__tjlms_modules'));
		$query->where($conditions);

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	/**
	 * Delete all orders for the course
	 *
	 * @param   ARRAY  $cid  array of course ids
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function deleteOrdersForCourse($cid)
	{
		$cidString = implode(',', $cid);
		$db        = JFactory::getDbo();

		// Get order Ids of selected course
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__tjlms_orders');
		$query->where('course_id IN (' . $cidString . ')');
		$db->setQuery($query);
		$orderIds = $db->loadColumn();

		$orderIdsString = implode(',', $orderIds);

		if ($orderIdsString)
		{
			// Delete order items
			$query      = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('order_id') . ' IN (' . $orderIdsString . ')',
				$db->quoteName('course_id') . ' IN (' . $cidString . ')'
			);

			$query->delete($db->quoteName('#__tjlms_order_items'));
			$query->where($conditions);

			$db->setQuery($query);
			$results = $db->execute();

			// Delete orders for course
			$query      = $db->getQuery(true);
			$conditions_course = array(
				$db->quoteName('course_id') . ' IN (' . $cidString . ')'
			);

			$query->delete($db->quoteName('#__tjlms_orders'));
			$query->where($conditions_course);

			$db->setQuery($query);
			$result = $db->execute();

			return $result;
		}
	}

	/**
	 * Method to test whether a record state can be edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		$canEdit = false;

		// Check edit own on the record asset (explicit or inherited)
		if ($user->authorise('core.edit.state', $this->option))
		{
			$canEdit = true;
		}
		elseif ($user->authorise('core.create', 'com_tjlms.course.' . $record->id))
		{
			// Grant if current user is owner of the record
			$canEdit = $user->get('id') == $record->created_by;
		}

		return $canEdit;
	}

	/**
	 * Method to test whether a record state can be edited.
	 *
	 * @param   INT  $recordId  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	public function canEditRecordState($recordId)
	{
		$user 	= JFactory::getUser();
		$record	= $this->getItem($recordId);
		$canEdit	= $this->canEditState($record);

		return $canEdit;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   12.2
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
		$canDelete = false;

		// Check edit own on the record asset (explicit or inherited)
		if ($user->authorise('core.delete', $this->option))
		{
			$canDelete = true;
		}
		elseif ($user->authorise('core.create', 'com_tjlms.course.' . $record->id))
		{
			// Grant if current user is owner of the record
			$canDelete = $user->get('id') == $record->created_by;
		}

		return $canDelete;
	}
}
