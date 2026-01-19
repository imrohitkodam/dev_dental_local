<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style>
	#es .es-weather .es-weather-icon {
		color: #9150A1;
	}
</style>

<div id="es" class="mod-es mod-es-weather <?php echo $lib->getSuffix();?>">
	<div class="es-weather es-weather--vertical is-loading" data-es-weather-<?php echo $uniqueId;?>>
		<div class="o-loader"></div>

		<div class="es-weather--wrapper t-hidden" data-es-weather-wrapper>
			<div class="es-weather__stats">
				<div class="es-weather__day"><?php echo JText::_('MOD_ES_WEATHER_TODAY'); ?></div>

				<div class="es-weather__icon">
					<i class="es-weather-icon wi" data-today-icon></i>
				</div>
				<div class="es-weather__temp-set">
					<div class="es-weather__cond" data-today-title></div>

					<div class="es-weather__temp">
						<div class="es-weather__temp-result" data-today-temperature></div>
					</div>
					<div class="es-weather__loc" data-today-location></div>
					<div class="es-weather__loc small" data-today-time></div>
				</div>
			</div>

			<div class="es-weather__forecast" data-forecast-list>
				<div class="es-weather__forecast-item t-hidden" data-forecast-template>
					<div class="es-weather__forecast-item-day" data-forecast-title></div>
					<div class="es-weather__forecast-item-icon">
						<i class="es-weather-icon wi" data-forecast-icon></i>
					</div>
					<div class="es-weather__forecast-item-temp">
						<span class="es-weather__forecast-item-temp-high" data-forecast-high></span>
						<span class="es-weather__forecast-item-temp-low" data-forecast-low></span>
					</div>
				</div>
			</div>

			<div class="es-weather__attribution">
				<a href="<?php echo $weather->attribution->link; ?>" target="_blank">
					<img src="<?php echo $weather->attribution->logo; ?>" alt="<?php echo $weather->attribution->title; ?>" width="134" height="29" />
				</a>
			</div>
		</div>
	</div>
</div>

<script>
EasySocial.require()
.done(function($){
	var isHttps = function() {
		// Determine whether the current is running on https.
		if (location.protocol == 'https:') {
			return true;
		}

		return false;
	};

	var latitude = "";
	var longitude = "";

	var getLocation = function() {

		var dfd = $.Deferred();

		var eslatitude = "<?php echo $weather->latitude; ?>";
		var eslongitude = "<?php echo $weather->longitude; ?>";

		// If es is set, then use it.
		if (eslatitude) {
			latitude = eslatitude;
			longitude = eslongitude;
			dfd.resolve();
			return dfd;
		}

		if (isHttps()) {
			if (!navigator.geolocation) {
				dfd.reject('<?php echo JText::_('MOD_ES_GEO_NOT_SUPPORTED');?>');
			}

			navigator.geolocation.getCurrentPosition(success, error);

			function error() {
				dfd.reject('<?php echo JText::_('MOD_ES_GEO_UNABLE_TO_LOCATE');?>');
			}

			function success(position) {
				latitude = position.coords.latitude;
				longitude = position.coords.longitude;
				dfd.resolve();
			};
		}
		else {
			$.getJSON("http://ip-api.com/json", function (data, status) {
				if (status === "success" ) {
					latitude = data.lat;
					longitude = data.lon;
					dfd.resolve();
				}
			});
		}

		return dfd;
	};

	getLocation().done(function() {

		// var ajaxUrl = "<?php echo rtrim(JURI::root(), '/');?>/modules/mod_easysocial_weather/ajax.php";
		var ajaxUrl = "<?php echo rtrim(JURI::root(), '/');?>/index.php?weatherModule=true";

		// Looping the language map
		var languageStrings = [
			<?php for ($i = 0; $i <= 47; $i++) { ?>
				'<?php echo JText::_('MOD_ES_WEATHER_' . $i, true);?>'<?php echo $i == 47 ? '' : ',';?>
			<?php } ?>
		];

		languageStrings[3200] = '<?php echo JText::_('MOD_ES_WEATHER_3200', true);?>';
		languageStrings['Mon'] = '<?php echo JText::_('MONDAY');?>';
		languageStrings['Tue'] = '<?php echo JText::_('TUESDAY');?>';
		languageStrings['Wed'] = '<?php echo JText::_('WEDNESDAY');?>';
		languageStrings['Thu'] = '<?php echo JText::_('THURSDAY');?>';
		languageStrings['Fri'] = '<?php echo JText::_('FRIDAY');?>';
		languageStrings['Sat'] = '<?php echo JText::_('SATURDAY');?>';
		languageStrings['Sun'] = '<?php echo JText::_('SUNDAY');?>';

		$.ajax({
			type: 'POST',
			url: ajaxUrl,
			data: {
				'latitude': latitude,
				'longitude': longitude,
				'tempUnit': '<?php echo $weather->tempUnit; ?>',
			}
		}).done(function(result) {

			var wrapper = $('[data-es-weather-<?php echo $uniqueId;?>]');
			wrapper.removeClass('is-loading');
			wrapper.find('[data-es-weather-wrapper]').removeClass('t-hidden');

			if (result === undefined || result === false) {
				return;
			}

			wrapper.attr('data-lon', btoa(longitude));
			wrapper.attr('data-lat', btoa(latitude));

			wrapper.find('[data-today-icon]').addClass(result.today.icon);
			wrapper.find('[data-today-temperature]').html(result.today.temperature);
			wrapper.find('[data-today-title]').html(languageMap(result.today.code));
			wrapper.find('[data-today-location]').html(result.location.region + ', ' + result.location.country);
			wrapper.find('[data-today-time]').html(result.location.time);

			$.each(result.forecast, function(i, day) {

				var tpl = wrapper.find('[data-forecast-template]').clone();

				tpl.removeClass('t-hidden');
				tpl.removeAttr('data-forecast-template');
				tpl.find('[data-forecast-title]').html(languageStrings[day.day]);
				tpl.find('[data-forecast-icon]')
					.attr('data-original-title', languageMap(day.code))
					.attr('data-es-provide', 'tooltip')
					.addClass(day.icon);
				tpl.find('[data-forecast-high]').html(day.high);
				tpl.find('[data-forecast-low]').html(day.low);

				wrapper.find('[data-forecast-list]').append(tpl);
			});
		});

		var languageMap = function(code) {
			return languageStrings[code].toLowerCase().replace(/\b[a-z]/g, function(letter) {
				return letter.toUpperCase();
			});
		};
	});
});
</script>
