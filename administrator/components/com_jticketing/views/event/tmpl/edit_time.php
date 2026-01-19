<?php
/**
* @version     1.5
* @package     com_jticketing
* @copyright   Copyright (C) 2014. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
* @author      Techjoomla <extensions@techjoomla.com> - http://techjoomla.com
*/
// no direct access
defined('_JEXEC') or die;
$eventId = $this->app->input->get('id', '', 'STRING');
?>
<script>
function isEditVenue()
	{
		var isEdit = <?php if (!empty($this->item->id)) { echo '1'; } else { echo '0'; }?>;
		if(isEdit)
		{
			var itemId ='<?php echo $this->item->id;?>'

			jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: 'index.php?option=com_jticketing&task=event.getEditVenue',
			data:{itemId:itemId},
			success: function(data)
					{
						var op = jQuery("#jform_venue option:selected").text(data);
						console.log(op);
						techjoomla.jQuery('#jform_venue').append(op);

						if(data == 'failure')
						{
							alert("<?php echo JText::_('COM_JTICKETING_EVENTFORM_VENU_INVALID'); ?>");
							invalidVenue = 1;
						}
					}
			});
		}
	}
		jQuery(document).ready(function(){
				jQuery('input[type=radio][name="jform[online_events]"]').on('click', function(){
				var venuestatus = jQuery(this).val();
				validateVenue(venuestatus);
			});
		});



	function validateVenue(obj)
	{
		var venuetype = jQuery("input[name='jform[online_events]']:checked").val();
		var startdate = jQuery('#jform_startdate').val();
		var enddate = jQuery('#jform_enddate').val();
		var event_start_time_hour = jQuery('#event_start_time_hour').val();
		var event_online = jQuery("input[name='jform[online_events]']:checked").val();
		var event_start_time_min = jQuery('#event_start_time_min').val();
		var event_start_time_ampm = jQuery('#event_start_time_ampm').val();
		var event_end_time_hour = jQuery('#event_end_time_hour').val();
		var event_end_time_min = jQuery('#event_end_time_min').val();
		var event_end_time_ampm = jQuery('#event_end_time_ampm').val();
		var created_by = jQuery('#jform_created_by_id').val();
		var eventid = "<?php echo $eventId; ?>";
		var invalidVenue = 0;

		if(startdate === '' && enddate === '')
		{
			alert("<?php echo JText::_('COM_JTICKETING_EVENTFORM_VENUE_VALID'); ?>");
			return false;
		}

		jQuery.ajax({
		type: 'POST',
		dataType: 'JSON',
		url: 'index.php?option=com_jticketing&task=event.getAvailableVenue',
		data:{
				startdate : startdate, enddate : enddate,
				event_start_time_hour : event_start_time_hour,
				event_start_time_min: event_start_time_min,
				event_start_time_ampm : event_start_time_ampm,
				event_end_time_hour : event_end_time_hour,
				event_end_time_min : event_end_time_min,
				event_online : event_online,
				event_end_time_ampm : event_end_time_ampm,
				venuetype : venuetype,
				created_by : created_by,
				eventid : eventid
			},
				success: function(data){
					techjoomla.jQuery('#jform_venue option').remove();

					if (data !== undefined && data !== null)
						{
							var op = "<option value='"+"0"+"'> <?php echo JText::_('COM_JTICKETING_FORM_VENUE_DEFAULT_OPTION'); ?> </option>";
							techjoomla.jQuery("#jform_venue").append(op);
							for(index = 0; index < data.length; ++index)
							{
								var op="<option value="  +data[index]['id']+ ">"  +data[index]['name']+   '</option>' ;
								techjoomla.jQuery('#jform_venue').append(op);
							}
							 jQuery("#jform_venue").trigger("liszt:updated");
						}
				}
		});

		if(invalidVenue == 1)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
</script>

<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('booking_start_date'); ?></div>
	<div class="controls">
		<?php
			//echo $this->form->getInput('booking_start_date');
			if(!isset($this->item->booking_start_date) or $this->item->booking_start_date=='0000-00-00 00:00:00')
			{
				$booking_start_date = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
			}
			else
			{
				$booking_start_date = JHtml::date($this->item->booking_start_date, JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'), true);
			}

			echo $calendar = JHtml::_('calendar', $booking_start_date, 'jform[booking_start_date]','jform_booking_start_date', JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER'));

			echo "<br/>";

			echo "<i>" . JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER_DESC') . "</i>";
		?>
	</div>
</div>

<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('booking_end_date'); ?></div>
	<div class="controls">
		<?php
			//echo $this->form->getInput('booking_end_date');

			if(!isset($this->item->booking_end_date) or $this->item->booking_end_date=='0000-00-00 00:00:00')
			{
				$booking_end_date = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
			}
			else
			{
				$booking_end_date = JHtml::date($this->item->booking_end_date, JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'), true);
			}

			echo $calendar = JHtml::_('calendar', $booking_end_date, 'jform[booking_end_date]','jform_booking_end_date', JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER'));

			echo "<br/>";

			echo "<i>" . JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER_DESC') . "</i>";
		?>
	</div>
</div>


