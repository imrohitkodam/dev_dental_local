/* Function : For finding longitude latitude of selected address*/
function getLongitudeLatitude()
{
	var geocoder = new google.maps.Geocoder();
	var address = techjoomla.jQuery('#jform_address').val();

	geocoder.geocode({ 'address': address}, function(results, status)
	{
		if (status == google.maps.GeocoderStatus.OK)
		{
			var latitude = results[0].geometry.location.lat();
			var longitude = results[0].geometry.location.lng();

			techjoomla.jQuery('#jform_latitude').val(latitude);
			techjoomla.jQuery('#jform_longitude').val(longitude);
		}
	});
}

/* Function : For Get Current Location */
function getCurrentLocation()
{
	if (navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition(showLocation);
	}
	else
	{
		var address = Joomla.JText._('COM_JTICKETING_ADDRESS_NOT_FOUND');
		var lonlatval = Joomla.JText._('COM_JTICKETING_LONG_LAT_VAL');
		techjoomla.jQuery('#jform_address').val(address);
		techjoomla.jQuery("#jform_longitude").val(lonlatval);
		techjoomla.jQuery("#jform_latitude").val(lonlatval);
	}
}

/* Function : For Showing user current location */
function showLocation(position)
{
	var latitude = position.coords.latitude;
	var longitude = position.coords.longitude;

	techjoomla.jQuery.ajax({
		type:'POST',
		url:'index.php?option=com_jticketing&task=venue.getLocation',
		data:'latitude='+latitude+'&longitude='+longitude,
		dataType: 'json',
		success:function(data)
		{
			var address = data["location"];
			var longitude = data["longitude"];
			var latitude = data["latitude"];

			if(data)
			{
				techjoomla.jQuery("#jform_address").val(address);
				techjoomla.jQuery("#jform_longitude").val(longitude);
				techjoomla.jQuery("#jform_latitude").val(latitude);
			}
		}
	});
}
