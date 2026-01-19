<?php
/**
 * @package     LMS_Shika
 * @subpackage  mod_lms_categorylist
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

if (JFile::exists(JPATH_SITE . '/components/com_tjlms/tjlms.php'))
{
	// Load js assets
	jimport('joomla.filesystem.file');
	$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

	if (JFile::exists($tjStrapperPath))
	{
		require_once $tjStrapperPath;
		TjStrapper::loadTjAssets('com_tjlms');
	}

	$showrecommend = 0;
	$mod_data = new stdClass;
	$mod_data->course_id = $course_id = JFactory::getApplication()->input->get('id', '', 'INT');

	if ($course_id)
	{
		$mod_data->tjlmsparams        = JComponentHelper::getParams('com_tjlms');
		$mod_data->social_integration = $mod_data->tjlmsparams->get('social_integration');
		$mod_data->oluser_id          = JFactory::getUser()->id;
		$mod_data->course_icons_path  = JUri::root(true) . '/media/com_tjlms/images/default/icons/';

		if (!class_exists('TjlmsModelcourses'))
		{
			$path = JPATH_SITE . '/components/com_tjlms/models/course.php';
			JLoader::register('TjlmsModelcourse', $path);
		}

		$model                      = new TjlmsModelcourse;
		$mod_data->course_extrainfo = $model->getDataExtra($course_id);

		foreach ($mod_data->course_extrainfo as $extrafields)
		{
			if ($extrafields->type == 'user' && $extrafields->value != 0)
			{
				$users[] = $extrafields;
			}
		}

		$mod_data->count = count($users);

		require JModuleHelper::getLayoutPath('mod_tjfield_presenter', $params->get('layout', 'default'));
	}
}
