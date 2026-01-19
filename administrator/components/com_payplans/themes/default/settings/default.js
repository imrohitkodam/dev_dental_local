PayPlans.ready(function($) {

	// Append the settings search to the toolbar
	jQuery(document).on('fd.payplans.search.settings', function(e, search, popup) {

		PayPlans.ajax('admin/views/config/search', {
			'text': search
		}).done(function(output) {
			popup.html(output).removeClass('t-hidden');
		});
	});

	<?php if ($goto) { ?>
	var element = jQuery('#<?php echo $goto;?>');
	var wrapper = element.parents('[data-fd-form-group]');

	wrapper.css({
		'background': '#fff9c4',
		'transition': 'background 1.0s ease-in-out'
	});

	var resetBackground = function() {
		wrapper.css({
			'background': 'none'
		});
	};

	setInterval(function() {
		resetBackground();
	}, 5000);

	function scrollToElement(element, offset) {
		var elementPosition = element.getBoundingClientRect().top;
		var offsetPosition = elementPosition - offset;

		setTimeout(function() {
			window.scrollTo({
				top: offsetPosition,
				behavior: "smooth"
			});
		}, 200);
	}

	var offset = $('nav.navbar').height() + $('header.header').height() + $('.subhead').height();

	scrollToElement(element[0], offset);
	<?php } ?>
});