EasyBlog.require()
.script('site/vendors/swiper')
.done(function($) {

	var swiper = new Swiper($('[data-eb-featured-container]'), {
		"freeMode": false,
		"slidesPerView": 'auto',

		<?php if ($this->params->get('featured_auto_slide', true)) { ?>
		"autoplay": {
			"delay": <?php echo $this->params->get('featured_auto_slide_interval', 8) * 1000;?>
		}
		<?php } ?>
	});

	var pagination = $('[data-swiper-slide-pagination]');

	swiper.on('slideChange', function () {
		pagination.removeClass('active');

		var active = pagination.filter('[data-index=' + swiper.activeIndex + ']');

		active.addClass('active');
	});

	pagination.on('click', function() {
		var current = $(this);
		var index = current.data('index');
		
		swiper.slideTo(index);
	});

});