<?php
/**
 * @package    Shika
 * @author     TechJoomla | <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.plugin.plugin');

$lang = Factory::getLanguage();
$lang->load('plg_tjvideo_videourl', JPATH_ADMINISTRATOR);

/**
 * Videourl plugin from techjoomla
 *
 * @since  4.1.3
 */
class PlgTjvideoVideourl extends CMSPlugin
{
	/**
	 * Function to get Sub Format options when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_type>ContentInfo
	 *
	 * @param   ARRAY  $config  config specifying allowed plugins
	 *
	 * @return  object.
	 *
	 * @since 4.1.3
	 */
	public function onGetSubFormat_tjvideoContentInfo($config = array('videourl'))
	{
		if (!in_array($this->_name, $config))
		{
			return;
		}

		$obj 			= array();
		$obj['name']	= $this->params->get('plugin_name', 'Video URL');
		$obj['id']		= $this->_name;
		$obj['assessment'] = $this->params->get('assessment', '0');

		return $obj;
	}

	/**
	 * Function to get Sub Format HTML when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_name>ContentHTML
	 *
	 * @param   INT    $mod_id       id of the module to which lesson belongs
	 * @param   INT    $lesson_id    id of the lesson
	 * @param   MIXED  $lesson       Object of lesson
	 * @param   ARRAY  $comp_params  Params of component
	 *
	 * @return  html
	 *
	 * @since 4.1.3
	 */
	public function onGetSubFormat_videourlContentHTML($mod_id , $lesson_id, $lesson, $comp_params)
	{
		$result = array();
		$plugin_name = $plg = $this->_name;

		// Load the layout & push variables
		ob_start();
		$layout = PluginHelper::getLayoutPath($this->_type, $this->_name, 'creator');
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Function to check if the related tables has been uploaded while adding lesson
	 *
	 * @param   INT  $lessonId  lessonId
	 * @param   OBJ  $mediaObj  media object
	 *
	 * @return  media object of format and subformat
	 *
	 * @since 4.1.3
	 */
	public function onAdditionalvideourlFormatCheck($lessonId, $mediaObj)
	{
		return $mediaObj;
	}

	/**
	 * Function to get needed data for this API
	 *
	 * @return  id from tjlms_lesson_tracking
	 *
	 * @since 4.1.3
	 */
	public function onupdateData()
	{
		$input = Factory::getApplication()->input;

		$mode = $input->get('mode', '', 'STRING');
		$trackingid = '';

		if ($mode != 'preview')
		{
			$post = $input->post;
			$lesson_id = $post->get('lesson_id', '', 'INT');
			$oluser_id = Factory::getUser()->id;

			$trackObj = new stdClass;
			$trackObj->attempt = $post->get('attempt', '', 'INT');
			$trackObj->score = 0;
			$trackObj->total_content = '';
			$trackObj->current_position = '';
			$trackObj->time_spent = '';

			$lesson_status = $post->get('lesson_status', '', 'STRING');

			if (!empty($lesson_status))
			{
				$trackObj->lesson_status = $lesson_status;
			}

			$current_position = $post->get('current_position', '', 'FLOAT');

			if (!empty($current_position))
			{
				$trackObj->current_position = round($current_position, 2);
			}

			$total_content = $post->get('total_content', '', 'FLOAT');

			if (!empty($total_content))
			{
				$trackObj->total_content = round($total_content, 2);
			}

			$time_spent = $post->get('time_spent', '', 'FLOAT');

			if (!empty($time_spent))
			{
				$trackObj->time_spent = round($time_spent, 2);
			}

			require_once JPATH_SITE . '/components/com_tjlms/helpers/tracking.php';
			$comtjlmstrackingHelper = new comtjlmstrackingHelper;
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id, $oluser_id, $trackObj);
		}

		return $trackingid;
	}

	/**
	 * Function to render the video
	 *
	 * @param   ARRAY  $config  data to be used to play video
	 *
	 * @return  complete html along with script is return.
	 *
	 * @since 4.1.3
	 */
	public function onvideourlrenderPluginHTML($config)
	{
		$videoURL = trim($config['file']);

		$autoplayParam = $this->params->get('autoplay', '0');
		$mutedParam = $this->params->get('muted', '0');
		$downloadParam = $this->params->get('download', '0');
		$seekbarParam = $this->params->get('show_seekbar', '0');
		$autoplayUrl = $mutedUrl = $download = $showSeekBar = '';
		
		if ($autoplayParam == 1)
		{
			$autoplayUrl   = 'autoplay';

			if ($mutedParam == 1)
			{
				$mutedUrl = 'muted';
			}
		}

		if ($downloadParam == 0)
		{
			$download = 'controlsList="nodownload"';
		}

		if ($seekbarParam == 0)
		{
			$showSeekBar = '<style type="text/css"> video::-webkit-media-controls-timeline { display: none; } </style>';
		}

		$input = Factory::getApplication()->input;
		$mode = $input->get('mode', '', 'STRING');
		$scriptfile = URI::root(true) . '/plugins/tjvideo/videourl/videourl/assets/tjvideourl-track-events.js';
		$html = '<video id="myVideoUrl" width="100%" height="100%" playsinline controls ' . $mutedUrl .' ' . $autoplayUrl . ' ' . $download . '>
					<source src=' . $videoURL . ' type="video/mp4">
				</video>';
		
		$html .= '<script type="text/javascript">
		var plugdataObject = {
			plgtype: "' . $this->_type . '",
			plgname: "' . $this->_name . '",
			plgtask:"updateData",
			lesson_id: ' . $config['lesson_id'] . ',
			attempt: ' . $config['attempt'] . ',
			file_id : "' . $videoURL . '",
			seekTo : "' . $config['current'] . '",
			mode:  "' . $mode . '"
		};
		</script>';
		
		$html .=  $showSeekBar;
		$html .= "<script src=" . $scriptfile . "></script>";

		return $html;
	}
}
