<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Hierarchys helper.
 *
 * @since  1.6
 */
class ConfirmationHelper
{
	public static function updateRescheduleData($reschedule_id, $hr, $bh)
	{
		$db = JFactory::getDbo();

		if ($reschedule_id)
		{
			$insertObj             = new stdClass;
			$insertObj->id  = $reschedule_id;

			if ($hr)
			{
				$insertObj->hr       = 2;
			}

			if($bh)
			{
				$insertObj->bh       = 2;
			}
			$insertObj->modified_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);
			$db->updateObject('#__hierarchy_reschedule', $insertObj, 'id');
		}
	}
}
