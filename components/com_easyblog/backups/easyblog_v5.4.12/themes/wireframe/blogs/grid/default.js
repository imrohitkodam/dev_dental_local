EasyBlog.require()
.script('site/vendors/swiper', 'site/posts/listings')
.done(function($) {
	var swiper = new Swiper($('[data-eb-grid-featured-container]'), {
		"freeMode": false,
		"slidesPerView": 'auto'

		<?php if ($this->params->get('showcase_auto_slide', true)) { ?>
		,"autoplay": {
			"delay": <?php echo $this->params->get('showcase_auto_slide_interval', 8) * 1000;?>
		}
		<?php } ?>
	});

	// Prev and Next button
	var nextButton = $('[data-eb-grid-featured-container] [data-featured-next]');
	var previousButton = $('[data-eb-grid-featured-container] [data-featured-previous]');

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

	// Prev and Next button
	previousButton.on('click', function() {
		swiper.slidePrev();
	});

	nextButton.on('click', function() {
		swiper.slideNext();
	});
	
	$('[data-eb-grid-listings]').implement(EasyBlog.Controller.Listings, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>,
		"autoload": <?php echo $showLoadMore ? 'true' : 'false'; ?>,
		"isGrid": <?php echo $isGrid ? 'true' : 'false'; ?>,
		"excludeIds": <?php echo json_encode($excludeBlogs); ?>
	});	

});
