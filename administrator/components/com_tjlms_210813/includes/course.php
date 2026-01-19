<?php
/**
 * @package     TJLms
 * @subpackage  com_shika
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Factory;

/**
 * TjLms course class
 *
 * @since  1.3.30
 */
class TjLmsCourse extends CMSObject
{
	/**
	 * Id
	 *
	 * @var    int
	 * @since  1.3.30
	 */
	public $id = 0;

	/**
	 * Title
	 *
	 * @var    string
	 * @since  1.3.30
	 */
	public $title = null;

	/**
	 * Alias
	 *
	 * @var    string
	 * @since  1.3.30
	 */
	public $alias = null;

	/**
	 * Category id
	 *
	 * @var    int
	 * @since  1.3.30
	 */
	public $catid = 0;

	/**
	 * Shot description
	 *
	 * @var    string
	 * @since  1.3.30
	 */
	public $short_desc = null;

	/**
	 * Description
	 *
	 * @var    string
	 * @since  1.3.30
	 */
	public $description = null;

	/**
	 * Image
	 *
	 * @var    string
	 * @since  1.3.30
	 */
	public $image = null;

	/**
	 * Start date
	 *
	 * @var    datetime
	 * @since  1.3.30
	 */
	public $start_date = null;

	/**
	 * End date
	 *
	 * @var    datetime
	 * @since  1.3.30
	 */
	public $end_date = null;

	/**
	 * Access level
	 *
	 * @var    int
	 * @since  1.3.30
	 */
	public $access = 0;

	/**
	 * Free or Paid
	 *
	 * @var    int
	 * @since  1.3.30
	 */
	public $type = 0;

	/**
	 * Condition to get the certificate
	 *
	 * @var    int
	 * @since  1.3.30
	 */
	public $certificate_term = 0;

	/**
	 * Certificate table id
	 *
	 * @var    int
	 * @since  1.3.30
	 */
	public $certificate_id = 0;

	/**
	 * @var    array  Course instances container.
	 * @since  1.3.30
	 */
	protected static $instances = array();

	/**
	 * Constructor
	 *
	 * @param   int  $id  Unique key to load.
	 *
	 * @since   1.3.30
	 */
	public function __construct($id = 0)
	{
		if (!empty($id))
		{
			$this->load($id);
		}
	}

	/**
	 * Load a course by its id
	 *
	 * @param   integer  $id  course ID
	 *
	 * @return  void
	 *
	 * @since  1.3.30
	 */
	public function load($id)
	{
		$table = TjLms::table("Course");

		// Load the object based on the id or throw a warning.
		if (!$table->load($id))
		{
			return false;
		}

		$this->setProperties($table->getProperties());

		return true;
	}

	/**
	 * Returns the global course object
	 *
	 * @param   integer  $id  The primary key of the course to load (optional).
	 *
	 * @return  TjLmsCourse  The course object.
	 *
	 * @since   1.3.30
	 */
	public static function getInstance($id = 0)
	{
		if (!$id)
		{
			return new TjLmsCourse;
		}

		if (empty(self::$instances[$id]))
		{
			self::$instances[$id] = new TjLmsCourse($id);
		}

		return self::$instances[$id];
	}

	/**
	 * This function provides the passable lessons of course
	 *
	 * @return  array|void  array of lesson ids
	 *
	 * @since   1.3.39
	 */
	public function getPassableLessons()
	{
		if (!empty($this->id))
		{
			// Get lessons by setting the course id
			$model = Tjlms::model("lessons", array("ignore_request" => true));
			$model->setState('filter.in_lib', 0);
			$model->setState('filter.course_id', $this->id);
			$model->setState('filter.consider_marks', 1);
			$lessons = $model->getItems();

			$passableLesson = array();

			$lessonObj = Tjlms::lesson();

			foreach ($lessons as $lesson)
			{
				$result = $lessonObj->checkLessonIsPassable($lesson->format);

				if ($result)
				{
					$passableLesson[] = $lesson->id;
				}
			}

			return $passableLesson;
		}
	}
}
