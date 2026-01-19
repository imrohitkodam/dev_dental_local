<?php

/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');
include_once JPATH_ROOT.DS.'administrator/components/com_tjlms/js_defines.php';
$input = JFactory::getApplication()->input;
$this->mode = $input->get('mode', '', 'STRING');
?>
<link rel="stylesheet" type="text/css"  href="<?php echo JUri::root(true). '/plugins/tjtextmedia/' . $this->_name . '/' . $this->_name .'/style/tjlmslessonlink.css';?>"></link>
<script>
	jQuery(document).ready(function () {
		hideImage();
		jQuery(".tjlms-lesson-player").addClass('hide_lesson_scroll');
		var fHeight = jQuery(window).height();
		jQuery('.jt_link_lesson').css('height', fHeight * 0.9);
	});
	<?php 	if ($this->mode != 'preview')
	{	?>
		lessonStartTime = new Date();

		var plugdataObject = {
			plgtype:'<?php echo $config['plgtype']; ?>',
			plgname:'<?php echo $config['plgname']; ?>',
			plgtask:'<?php echo $config['plgtask']; ?>',
			lesson_id: <?php echo $config['lesson_id']; ?>,
			attempt: <?php echo $config['attempt']; ?>,
			mode: ' '
		};

		plugdataObject["current_position"] = 1;
		plugdataObject["total_content"] = 1;
		plugdataObject["lesson_status"] = "completed";
		updateData(plugdataObject);

		tjlessolinkInterval = setInterval(function () {
			lessonStoptime = new Date();
			var timespentonLesson = lessonStoptime - lessonStartTime;
			var timeinseconds = Math.round(timespentonLesson / 1000);
			plugdataObject.time_spent = timeinseconds;
			plugdataObject.lesson_status = "";
			lessonStartTime = new Date();
			updateData(plugdataObject);
		}, 10000);

	<?php
	} ?>
</script>

<iframe type="text/html" height="100%" width="100%"
class="jt_link_lesson" src="<?php echo $config['file'];?>"></iframe>';
