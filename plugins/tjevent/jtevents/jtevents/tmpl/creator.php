<?php
/**
 * @package    Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');

$subformat = $lesson->sub_format;
$eventid = '';

if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];

	if (!empty($source_option) && $source_plugin == 'jtevents')
	{
		$eventid = $lesson->source;
		$lessonParams = json_decode($lesson->params);
	}
} ?>

<div class="control-group">
	<label class="control-label" title="<?php echo JText::_("PLG_TJEVENT_JTEVENT_EVENT_LBL_TITLE") ?>">
		<?php echo JText::_("PLG_TJEVENT_JTEVENT_EVENT_LBL") ?>
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
			$options[] = JHTML::_('select.option', 0, JText::_('PLG_TJEVENT_SELECT_EVENTS'));
		}

		$onlineAdded = $offlineAdded = false;
		foreach ($eventlist as $event)
		{
			if(!$onlineAdded && $event->online_events){
				$onlineAdded = true;
				$options[] = JHTML::_('select.optgroup', '-- '.JText::_('PLG_TJEVENT_JTEVENT_ONLINE_EVENTS').' --');
			}
			if(!$offlineAdded && !$event->online_events){
				if($onlineAdded){
					$options[] = JHTML::_('select.optgroup', '-- '.JText::_('PLG_TJEVENT_JTEVENT_ONLINE_EVENTS').' --');
				}
				$offlineAdded = true;
				$options[] = JHTML::_('select.optgroup', '-- '.JText::_('PLG_TJEVENT_JTEVENT_OFFLINE_EVENTS').' --');
			}
			$options[] = JHTML::_('select.option', $event->id, $event->title);
		}
		if($onlineAdded && !$offlineAdded){
			$options[] = JHTML::_('select.optgroup', '-- '.JText::_('PLG_TJEVENT_JTEVENT_ONLINE_EVENTS').' --');
		}
		if($offlineAdded){
			$options[] = JHTML::_('select.optgroup', '-- '.JText::_('PLG_TJEVENT_JTEVENT_OFFLINE_EVENTS').' --');
		}
		echo JHTML::_('select.genericlist', $options, 'lesson_format[jtevents][event]', 'class = "inputbox required"', 'value', 'text', $eventid); ?>
		<input type="hidden" id="subformatoption" name="lesson_format[jtevents][subformatoption]" value="event"/>
		<input type="hidden" id="coursedeatail" name="coursedeatail[jtevents][subformatoption]" value="<?php echo $courseDetail->type; ?>"/>
		<input type="hidden" id="jtevents_params" name="lesson_format[jtevents][params]" value=""/>
	</div><!--controls-->
</div><!--control-group-->

<div id="eventdiv" class="eventdiv<?php echo $lesson->lesson_id;?>">
	<div id="type_result">
		<div class="control-group">
			<label class="control-label" title="<?php echo JText::_('PLG_TJEVENT_JTEVENT_PRICE_TITLE'); ?>">
				<?php echo JText::_('PLG_TJEVENT_JTEVENT_PRICE'); ?>
			</label>
			<div class="controls">
				<fieldset id="myEdit_<?php echo $lesson->lesson_id;?>" class="radio btn-group">
					<label for="myEdit0" class="btn tickettype label_item" checked="checked">
					<input type="radio" id="myEdit0" value="1" name="myEdit" class="" ><?php echo JText::_('PLG_TJEVENT_YES'); ?></label>
						<label for="myEdit1" class="btn tickettype">
							<input type="radio" id="myEdit1" value="0" name="myEdit" ><?php echo JText::_('PLG_TJEVENT_NO'); ?>
						</label>
				</fieldset>
			</div><!--controls-->
		</div><!--control-group-->
	</div><!--type_result-->
	<div class="control-group">
		<div id="eventInfo" name="eventInfo">
			<table class='table table-striped'>
			<thead>
				<tr>
				<td><?php echo JText::_('PLG_TJEVENT_SELECT'); ?></td>
				<td><?php echo JText::_('PLG_TJEVENT_TITLE'); ?></td>
				<td><?php echo JText::_('PLG_TJEVENT_PRICE'); ?></td>
				</tr>
			</thead>
			<tbody>
			</tbody>
			</table>
		</div><!--eventInfo-->
	</div><!--control-group-->
</div><!--eventdiv-->

<script type="text/javascript">
var freeEvent='';
<?php
	if ($eventid)
	{
		if (array_key_exists("ticketid",$lessonParams))
		{
			?>
				getEventTickets(<?php echo $eventid;?>, 'edit',<?php echo $lessonParams->ticketid;?>);
<?php
		}else
		{	?>
			getEventTickets(<?php echo $eventid;?>, 'edit');
<?php	}
	}else
	{	?>
		jQuery('#type_result, #eventInfo').hide();
<?php
	}	?>

	/* Function to load the loading image. */
	function validateeventjtevents(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: "PLG_TJEVENT_JTEVENT_VAL_PASSES"};
		var val_passed = '0';
		var format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);

		var eventid = techjoomla.jQuery("#lesson_formatjteventsevent", format_lesson_form).val();
		var tickettype = techjoomla.jQuery('input[name=tickettype]:checked').val();

		if (eventid == '' || eventid == 0)
		{
			res.check = '0';
			res.message = "<?php echo JText::_('PLG_TJEVENT_JTEVENTS_EVENT_VALIDATION');?>";
		}
		else
		{
			var selectedVal = "";
			var selected = techjoomla.jQuery("input[type='radio'][name='myEdit']:checked");
			var eventticket = techjoomla.jQuery('input[name=tickettype]:checked').val();
		}

		var previousVal = minMaxId('#eventInfo .table-striped tbody tr');
		var hasActive = techjoomla.jQuery("label[for='myEdit0']").hasClass('active');

		if(res.check == 1 && hasActive ==true)
		{
			var source = {eventid: eventid, ticketid: tickettype};
			var jsonString = JSON.stringify(source);
			techjoomla.jQuery("#jtevents_params", format_lesson_form).val(jsonString);
		}
		else if (hasActive == false)
		{
			var source = {eventid: eventid};
			var jsonString = JSON.stringify(source);
			techjoomla.jQuery("#jtevents_params", format_lesson_form).val(jsonString);
		}

		return res;
	}

	var form_id, format_lesson_form, eventid, course_id;

	techjoomla.jQuery('.lesson_format  #lesson_formatjteventsevent').change(function() {

		form_id = techjoomla.jQuery(this).closest('#lesson_format').siblings('#form_id').val();
		format_lesson_form = techjoomla.jQuery('#lesson-format-form_'+form_id);

		eventid = techjoomla.jQuery(this).val();
		course_id = techjoomla.jQuery(coursedeatail).val();

		techjoomla.jQuery(".tickettype").removeClass('active btn-danger');
		techjoomla.jQuery(".tickettype #myEdit1").removeClass('active');
		techjoomla.jQuery(".label_item").addClass('active btn-success');
		techjoomla.jQuery(".tickettype #myEdit0").addClass('active');

		getEventTickets(eventid,'new');
	});

	function minMaxId(selector) {
		var min=null, max=null;
		var previousVal = 0;

		techjoomla.jQuery(selector,format_lesson_form).each(function(index) {

			if(index === 0)
			{
				previousVal = parseInt(techjoomla.jQuery(this).find('td').eq(3).text());
			}
			else
			{
				if( parseInt(techjoomla.jQuery(this).find('td').eq(3).text()) < previousVal )
				{
					previousVal = parseInt(techjoomla.jQuery(this).find('td').eq(3).text());
				}
			}
		  });
		  return previousVal;
	}

		techjoomla.jQuery(".btn-group.radio label").click(function()
		{
			var label = techjoomla.jQuery(this);
			var input = techjoomla.jQuery('#' + label.attr('for'));

			if (!input.prop('checked'))
			{
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-success');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
					techjoomla.jQuery('#eventInfo').hide();
					techjoomla.jQuery('.tickettypes').prop('checked', false);
				} else {
					label.addClass('active btn-success');
					techjoomla.jQuery('#eventInfo').show();
					techjoomla.jQuery('.tickettypes').prop('checked', true);
				}
				input.prop('checked', true);
			}
		});

	/*Get Ticket details of given eventid*/
	function getEventTickets(eventid,operation,ticketId)
	{
		techjoomla.jQuery(".tickettype").removeClass('active btn-danger');
		techjoomla.jQuery(".label_item").addClass('active btn-success');
		techjoomla.jQuery(".tickettype #myEdit0").addClass('active');

		techjoomla.jQuery.ajax({
			type:'POST',
			url:'index.php?option=com_jticketing&task=event.getEventsDetails&event_id='+ eventid +'',
			data: {id:eventid},
			beforeSend: function( xhr ) {
				jQuery(".btn").attr('disabled','disabled');
			},
			success:function(data)
			{
				if (ticketId == undefined && operation == 'edit')
				{
					techjoomla.jQuery("label[for=myEdit0]").removeClass('active btn-success');
					techjoomla.jQuery("label[for=myEdit1]").addClass('active btn-danger');
					techjoomla.jQuery(".tickettype #myEdit0").removeClass('active');
					techjoomla.jQuery(".tickettype #myEdit1").addClass('active');
					techjoomla.jQuery('#eventInfo').hide();
					techjoomla.jQuery('.tickettypes').prop('checked', false);
				}

				datatype: 'JSON'
				var json = techjoomla.jQuery.parseJSON(data);
				var output="";
				var jsonlenght = json.length;
				isFreeEvent(eventid);

					for (i = 0; i < jsonlenght; i++)
					{
						output+="<tr>"
						+ "<td><input type='radio' name='tickettype' id='eventticket_"+i+"' class='wrap tickettypes' value="+ json[i].ticketid +" ></td>"
						+ "<td>" + json[i].title + "</td>"
						+ "<td>" + json[i].price + "</td>"
						+ "</tr>";
						output+="";

						techjoomla.jQuery('#eventInfo table tr:not(:first)').remove();
						techjoomla.jQuery('#eventInfo table tbody').append(output);
						techjoomla.jQuery('#eventdiv').show();

						if(operation == 'new' && freeEvent == 0 && json[i].price > 0 && json[i].price != null)
						{
							techjoomla.jQuery('#type_result').show();
							techjoomla.jQuery('#eventInfo').show();
						}
						else if(freeEvent != 0)
						{
							techjoomla.jQuery('#type_result').hide();
							techjoomla.jQuery('#eventInfo').hide();
						}

						var selectedTicket;

						if (json[i].ticketid == ticketId)
						{
							selectedTicket = '#eventticket_'+i;
						}else if (ticketId == undefined)
						{
							selectedTicket = '.tickettypes';
						}
					}
					techjoomla.jQuery(selectedTicket).prop('checked', true);

					minMaxId('#eventInfo .table-striped tbody tr');

					if (json.length == 0)
					{
						techjoomla.jQuery('#eventdiv').hide();
					}
				jQuery(".btn").removeAttr('disabled');
			},
		});
	}

	/*if event is paid than set paidEvent=0 */
	function isFreeEvent(eventid)
	{
		techjoomla.jQuery.ajax({
		type:'POST',
		url:'index.php?option=com_jticketing&task=event.isFreeEvent&event_id='+ eventid +'',
		data: {id:eventid},
		async: false,
		success:function(data)
			{
				freeEvent = data
			}
		});
	}
</script>
<style>
#eventdiv{
display: none;
}
</style>
