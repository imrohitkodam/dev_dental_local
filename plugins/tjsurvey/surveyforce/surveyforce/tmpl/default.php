<?php
/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');
include_once JPATH_ROOT.'/administrator/components/com_tjlms/js_defines.php';
$input = JFactory::getApplication()->input;
$this->mode = $input->get('mode', '', 'STRING');
$lessonId = $config['lesson_id'];
$attempts = $config['attempt'];
$surveyId = (int) $config['lesson_typedata']->source;
?>
<script>
	jQuery( document ).ready(function()
	{
		hideImage();
		jQuery("iframe").height(700);
		jQuery("iframe").width( jQuery(window).width());
		jQuery(window).resize(function()
		{
			jQuery("iframe").height( jQuery(this).height() );
		});
	});
	lessonStartTime = new Date();

	var plugdataObject = {
		plgtype:'<?php echo $config['plgtype']; ?>',
		plgname:'<?php echo $config['plgname']; ?>',
		plgtask:'<?php echo $config['plgtask']; ?>',
		lesson_id:<?php echo $surveyId; ?>,
		attempt: <?php echo $attempts; ?>,
		mode: '0'
	};

	plugdataObject["current_position"] = 1;
	plugdataObject["total_content"] = 1;
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
</script>
<?php
	$url = JUri::root() . 'index.php?option=com_surveyforce&view=survey&id=' . $config['lesson_typedata']->source . '&tmpl=component&lesson_id=' . $lessonId;
?>
<iframe name="surveyFrame" id="surveyFrame" src="<?php echo $url;?>"  width="100%" ></iframe>
