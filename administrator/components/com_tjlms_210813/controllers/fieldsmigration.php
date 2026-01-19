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

jimport('joomla.application.component.controllerform');

/**
 * Lesson controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerFieldsmigration extends JControllerForm
{
	public function migrationFields()
	{
		// Reading file here for getting limit start and end
		$fileData = file(JPATH_ROOT . '/administrator/components/com_tjlms/controllers/fieldsmigrationLimit.txt');
		$limit = 0;

		foreach ($fileData as $line)
		{
			if (strpos($line, 'limit') !== false)
			{
				$limit = explode(":", $line)[1];
			}
		}

		$courseIdList = array();
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__tjfields_fields_value'));
		$db->setQuery($query);
		$courseList = $db->loadObjectList();
		$courseList = array_chunk($courseList, 200);

		if($courseList[(int) $limit])
		{
			foreach ($courseList[(int) $limit] as $course)
			{
				$fieldName = $this->getFieldNameFromTJFields($course->field_id);

				$newObj = new stdClass;

				$newObj->field_id = $this->getFieldIdFromFields($fieldName);
				$newObj->item_id  = $course->content_id;
				$newObj->value = $course->value;

				$db->insertObject('#__fields_values', $newObj);
			}

			// Update limitstart and limitend value
			$filePath    = fopen(JPATH_ROOT . '/administrator/components/com_tjlms/controllers/fieldsmigrationLimit.txt', 'w+');

			if ($filePath !== false)
			{
				$newContents = "limit:" . ($limit + 1) . "\n";
				fwrite($filePath, $newContents);
				fclose($filePath);
			}
		}
		else
		{
			echo "Successfully completed.";
		}
	}

	public function getFieldNameFromTJFields($fieldId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('label');
		$query->from($db->quoteName('#__tjfields_fields'));
		$query->where('id = ' . (int) $fieldId);
		$db->setQuery($query);
		return $db->loadResult();
	}


	public function getFieldIdFromFields($fieldName)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__fields','f'));
		$query->where($db->quoteName('f.label') . ' = ' . $db->quote($fieldName));
		$db->setQuery($query);
		return $db->loadResult();
	}
}
