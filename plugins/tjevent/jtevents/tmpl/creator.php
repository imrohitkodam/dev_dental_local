<?php
/**
 * @package    Tjlms
 * @copyright  Copyright (C) 2005 - 2018. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

$subformat = !empty($lesson->sub_format)?$lesson->sub_format:'';
$eventid = '';
$i = 0;
$lessonParams = '';

if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];

	if (!empty($source_option) && $source_plugin == 'jtevents')
	{
		$eventid = $lesson->source;

		$lessonParams = !empty($lesson->params)
    ? (is_string($lesson->params) ? json_decode($lesson->params, true) : $lesson->params)
    : '';
	}
}
?>

<script>
var event_id = "<?php echo $eventid;?>";
var lessonParams = '<?php echo json_encode($lessonParams);?>';
</script>

<div class="control-group">
	<label class="control-label" title="<?php echo Text::_("PLG_TJEVENT_JTEVENT_EVENT_LBL_TITLE"); ?>">
		<?php echo Text::_("PLG_TJEVENT_JTEVENT_EVENT_LBL"); ?>
	</label>
	<div class="controls">
		<?php
		if (!empty($source_option) && $source_plugin == 'jtevents')
		{
			$jtEventHelper = new JteventHelper;
			$firstOption = $jtEventHelper->getEventColumn($eventid, array('title','online_events','id'));

			if($firstOption->online_events){
				$eventlist = array_merge(array($firstOption), (array)$eventlist);
			}else{
				$finalEvents = array();
				$key = 0;
				foreach($eventlist as $key=>$event){
					if(!$event->online_events){
						break;
					}
				}
				array_splice( $eventlist, $key, 0, array($firstOption) );
			}
		}
		else
		{
			$options[] = JHTML::_('select.option', 0, Text::_('PLG_TJEVENT_SELECT_EVENTS'));
		}

		echo '<select name="lesson_format[jtevents][event]" class="inputbox required">';

		$onlineAdded  = false;
		$offlineAdded = false;

		foreach ($eventlist as $event)
		{
			if (!$onlineAdded && $event->online_events)
			{
				$onlineAdded = true;
				echo '<optgroup label="' . Text::_('PLG_TJEVENT_JTEVENT_ONLINE_EVENTS') . '">';
			}

			if (!$offlineAdded && !$event->online_events)
			{
				if ($onlineAdded) {
					echo '</optgroup>';
				}

				$offlineAdded = true;
				echo '<optgroup label="' . Text::_('PLG_TJEVENT_JTEVENT_OFFLINE_EVENTS') . '">';
			}

			$selected = ($eventid == $event->id) ? ' selected="selected"' : '';
			echo '<option value="' . $event->id . '"' . $selected . '>' . htmlspecialchars($event->title, ENT_QUOTES, 'UTF-8') . '</option>';
		}

		// Close final optgroup
		if ($onlineAdded || $offlineAdded)
		{
			echo '</optgroup>';
		}

		echo '</select>';
		 ?>
		<input type="hidden" id="subformatoption" name="lesson_format[jtevents][subformatoption]" value="event"/>
		<input type="hidden" id="coursedeatail" name="coursedeatail[jtevents][subformatoption]" value="<?php echo $courseDetail->type; ?>"/>
		<input type="hidden" id="jtevents_params" name="lesson_format[jtevents][params]" value=""/>
	</div><!--controls-->
</div><!--control-group-->

<script type="text/javascript">

/* Function to load the loading image. */
function validateeventjtevents(formid,format,subformat,media_id)
{
	var res = {check: 1, message: "PLG_TJEVENT_JTEVENT_VAL_PASSES"};
	var format_lesson_form = jQuery("#lesson-format-form_"+ formid);
	var eventid = jQuery("#lesson_formatjteventsevent", format_lesson_form).val();

	if (eventid == '' || eventid == 0)
	{
		res.check = '0';
		res.message = "<?php echo Text::_('PLG_TJEVENT_JTEVENTS_EVENT_VALIDATION');?>";
	}
	else
	{
		jQuery("input[type='radio'][name='myEdit']:checked");
	}

	return res;
}
</script>
<style>
#eventdiv{
display: none;
}
</style>
