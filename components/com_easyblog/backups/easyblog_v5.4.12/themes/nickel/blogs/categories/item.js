<?php echo $this->output('site/blogs/nickel'); ?>

EasyBlog.require()
.library('masonry', 'imagesloaded')
.script('site/posts/posts')
<?php if ($this->config->get('layout_dropcaps')) { ?>
.script('site/dropcap')
<?php } ?>
.done(function($) {
	$('[data-blog-posts]').implement(EasyBlog.Controller.Posts, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>,
		"hasPinterestEmbedBlock": <?php echo $hasPinterestEmbedBlock ? 'true' : 'false'; ?>,
		"pinterestExternalShareBtnEnabled": <?php echo EB::isExternalPinterestShareEnabled() ? 'true' : 'false'; ?>
	});

	// MASONRY
	var container = $('.eb-posts-masonry');

	$('img').load(function(){
		container.imagesLoaded(function(){
			container.masonry({
				itemSelector : '.eb-post',
				isRTL: false
			});
		});
	});


	$('.eb-masonry').imagesLoaded( function(){
		$('.eb-masonry').masonry({
			itemSelector: '.eb-masonry-post'
		});
	});

	$('.eb-masonry').masonry({
		itemSelector: '.eb-masonry-post'
	});

	setTimeout(function() {
		// Cheap fix to fix social buttons
		$('.eb-masonry').masonry();
	}, 5000);

	$('[data-show-all-authors]').on('click', function(){
		$('[data-author-item]').each(function() {
			$(this).find('img').attr('src', $(this).data('src'));

			$(this).removeClass('hide');
		});

		// Hide the button block
		$(this).addClass('hide');
	});

	$('[data-more-categories-link]').on('click', function() {
		$(this).hide();
		$('[data-more-categories]').css('display', 'inline-block');
	});

	<?php if ($this->config->get('layout_dropcaps')) { ?>
	var posts = $('.eb-post-body');

	$.each(posts, function(i, item) {
		var item = $(item);

		// Built-in composer
		// If user uses readmore or doesn't have automated truncation,
		// we can get the first composer block
		var block = $.getFirstTextBlock(item);

		if (block) {
			block.addClass('has-drop-cap');
			return;
		}

		// Handle legacy editor that uses readmore tag in the editor.
		var paragraph = $.getFirstParagraph(item);

		if (paragraph) {
			paragraph.addClass('has-drop-cap');
		}
	});

	<?php } ?>
});
