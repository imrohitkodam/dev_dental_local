function launchEvent(eventdata)
{
	jQuery.ajax(
	{
		type: "POST",
		url: "index.php?option=com_ajax&plugin=launchEventData&format=json",
		data: {eventID:eventdata},
		dataType: 'json',
		success: function(result)
		{
			console.log(result);
		}
	});
}
