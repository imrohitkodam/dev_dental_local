EasyBlog.require()
.script('site/vendors/swiper')
.done(function($) {

	// Prev and Next button
	var nextButton = $('[data-featured-posts] [data-featured-next]');
	var previousButton = $('[data-featured-posts] [data-featured-previous]');

	var swiper = new Swiper($('[data-eb-featured-container]'), {
		"freeMode": false,
		"slidesPerView": 'auto',

		<?php if ($this->params->get('featured_auto_slide', true)) { ?>
		"autoplay": {
			"delay": <?php echo $this->params->get('featured_auto_slide_interval', 8) * 1000;?>
		}
		<?php } ?>
	});

	// bind slideChange on swiper dimming the next / previous button
	swiper.on('slideChange', function() {
		$(nextButton).removeClass('eb-gallery-button--disabled');
		$(previousButton).removeClass('eb-gallery-button--disabled');

		if (swiper.isBeginning) {
			$(previousButton).addClass('eb-gallery-button--disabled');
		}

		if (swiper.isEnd) {
			$(nextButton).addClass('eb-gallery-button--disabled');
		}
	});

	previousButton.on('click', function() {
		swiper.slidePrev();
	});

	nextButton.on('click', function() {
		swiper.slideNext();
	});
});