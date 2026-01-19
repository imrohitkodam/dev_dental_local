// var freeEvent='';

	// var lesson_values = jQuery.parseJSON(lessonParams);

	// if (event_id)
	// {
	// 	jQuery('#eventInfo table tbody').html('');

	// 	if ("ticketid" in lesson_values)
	// 	{
	// 		getEventTickets(event_id, 'edit',lesson_values['ticketid']);
	// 	}else
	// 	{
	// 		getEventTickets(event_id, 'edit');
	// 	}
	// }else
	// {
	// 	jQuery('#type_result, #eventInfo').hide();
	// }

	/* Function to load the loading image. */
	function validateeventjtevents(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: "PLG_TJEVENT_JTEVENT_VAL_PASSES"};
		var format_lesson_form = jQuery("#lesson-format-form_"+ formid);
		var eventid = jQuery("#lesson_formatjteventsevent", format_lesson_form).val();
		// var tickettype = jQuery('input[name=tickettype_'+eventid+']:checked').val();

		if (eventid == '' || eventid == 0)
		{
			res.check = '0';
			res.message = "<?php echo JText::_('PLG_TJEVENT_JTEVENTS_EVENT_VALIDATION');?>";
		}
		else
		{
			jQuery("input[type='radio'][name='myEdit']:checked");
			// var eventticket = jQuery('input[name=tickettype_'+eventid+']:checked').val();
		}

		var source = {eventid: eventid};
		var jsonString = JSON.stringify(source);
		jQuery("#jtevents_params", format_lesson_form).val(jsonString);

		// var previousVal = minMaxId('#eventInfo .table-striped tbody tr');
		// var hasActive = jQuery("label[for='myEdit0']").hasClass('active');

		// if(res.check == 1 && hasActive ==true)
		// {
		// 	var source = {eventid: eventid, ticketid: tickettype};
		// 	var jsonString = JSON.stringify(source);
		// 	jQuery("#jtevents_params", format_lesson_form).val(jsonString);
		// }
		// else if (hasActive == false)
		// {
		// 	var source = {eventid: eventid};
		// 	var jsonString = JSON.stringify(source);
		// 	jQuery("#jtevents_params", format_lesson_form).val(jsonString);
		// }


		return res;
	}

	var form_id, format_lesson_form, eventid, course_id;

	jQuery('.lesson_format  #lesson_formatjteventsevent').change(function()
	{
		// jQuery('#eventInfo table tbody').html('');

		form_id = jQuery(this).closest('#lesson_format').siblings('#form_id').val();
		format_lesson_form = jQuery('#lesson-format-form_'+form_id);

		eventid = jQuery(this).val();
		// course_id = jQuery(coursedeatail).val();

		// jQuery(".tickettype").removeClass('active btn-danger');
		// jQuery(".tickettype #myEdit1").removeClass('active');
		// jQuery(".label_item").addClass('active btn-success');
		// jQuery(".tickettype #myEdit0").addClass('active');
		// getEventTickets(eventid,'new');
	});

	// function minMaxId(selector) {
	// 	var min=null, max=null;
	// 	var previousVal = 0;

	// 	jQuery(selector,format_lesson_form).each(function(index) {

	// 		if(index === 0)
	// 		{
	// 			previousVal = parseInt(jQuery(this).find('td').eq(3).text());
	// 		}
	// 		else
	// 		{
	// 			if( parseInt(jQuery(this).find('td').eq(3).text()) < previousVal )
	// 			{
	// 				previousVal = parseInt(jQuery(this).find('td').eq(3).text());
	// 			}
	// 		}
	// 	  });

	// 	  return previousVal;
	// }

	// jQuery(".btn-group.radio label").click(function()
	// {
	// 	var label = jQuery(this);
	// 	var input = jQuery('#' + label.attr('for'));

	// 	if (!input.prop('checked'))
	// 	{
	// 		label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
	// 		if (input.val() == '') {
	// 			label.addClass('active btn-success');
	// 		} else if (input.val() == 0) {
	// 			label.addClass('active btn-danger');
	// 			jQuery('#eventInfo').hide();
	// 			jQuery('.tickettypes').prop('checked', false);
	// 		} else {
	// 			label.addClass('active btn-success');
	// 			jQuery('#eventInfo').show();
	// 			jQuery('.tickettypes').prop('checked', true);
	// 		}
	// 		input.prop('checked', true);
	// 	}
	// });

	/*Get Ticket details of given eventid*/
	// function getEventTickets(eventid,operation,ticketId)
	// {
	// 	jQuery(".tickettype").removeClass('active btn-danger');
	// 	jQuery(".label_item").addClass('active btn-success');
	// 	jQuery(".tickettype #myEdit0").addClass('active');

	// 	jQuery.ajax({
	// 		type:'POST',
	// 		url:'index.php?option=com_jticketing&format=json&task=event.getEventsDetails&event_id='+ eventid +'',
	// 		data: {id:eventid},
	// 		beforeSend: function( xhr ) {
	// 			jQuery(".btn").attr('disabled','disabled');
	// 		},
	// 		success:function(data)
	// 		{
	// 			if (ticketId == undefined && operation == 'edit')
	// 			{
	// 				jQuery("label[for=myEdit0]").removeClass('active btn-success');
	// 				jQuery("label[for=myEdit1]").addClass('active btn-danger');
	// 				jQuery(".tickettype #myEdit0").removeClass('active');
	// 				jQuery(".tickettype #myEdit1").addClass('active');
	// 				jQuery('#eventInfo').hide();
	// 				jQuery('.tickettypes').prop('checked', false);
	// 			}

	// 			datatype: 'JSON'
	// 			var json = jQuery.parseJSON(data.data);
	// 			var jsonlenght = json.length;

	// 			isFreeEvent(eventid);

	// 				for (i = 0; i < jsonlenght; i++)
	// 				{
	// 					var output = '';
	// 					output += "<tr>"
	// 					+ "<td><input type='radio' name='tickettype_"+json[i].eventid+"' id='eventticket_"+json[i].eventid+"_"+i+"' class='wrap tickettypes' value="+ json[i].ticketid +" ></td>"
	// 					+ "<td>" + json[i].title + "</td>"
	// 					+ "<td>" + json[i].price + "</td>"
	// 					+ "</tr>";
	// 					output += "";

	// 					jQuery('#eventInfo table tbody').append(output);
	// 					jQuery('#eventdiv').show();

	// 					if(operation == 'new' && freeEvent == 0 && json[i].price > 0 && json[i].price != null)
	// 					{
	// 						jQuery('#type_result').show();
	// 						jQuery('#eventInfo').show();
	// 					}
	// 					else if(freeEvent == 1)
	// 					{
	// 						jQuery('#type_result').hide();
	// 						jQuery('#eventInfo').hide();
	// 					}

	// 					var selectedTicket;

	// 					if (json[i].ticketid == ticketId)
	// 					{
	// 						selectedTicket = '#eventticket_'+json[i].eventid+'_'+i;
	// 					}else if (ticketId == undefined)
	// 					{
	// 						selectedTicket = '.tickettype_'+json[i].eventid;
	// 					}
	// 				}

	// 				jQuery(selectedTicket).prop('checked', true);

	// 				minMaxId('#eventInfo .table-striped tbody tr');

	// 				if (json.length == 0)
	// 				{
	// 					jQuery('#eventdiv').hide();
	// 				}
	// 			jQuery(".btn").removeAttr('disabled');
	// 		},
	// 	});
	// }

	/*if event is paid than set paidEvent=0 */
	// function isFreeEvent(eventid)
	// {
	// 	jQuery.ajax({
	// 	type:'POST',
	// 	url:'index.php?option=com_jticketing&format=json&task=event.isFreeEvent&event_id='+ eventid +'',
	// 	data: {id:eventid},
	// 	async: false,
	// 	success:function(data)
	// 		{
	// 			freeEvent = data.data
	// 		}
	// 	});
	// }
