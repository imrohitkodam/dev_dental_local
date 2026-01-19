function initialize()
{
	var myLatlng = new google.maps.LatLng(lat,lon);
	var mapOptions = {
		zoom: defaultGMapLevel,
		center: myLatlng
	}

	var map = new google.maps.Map(document.getElementById('event-map'), mapOptions);

	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		});
}
