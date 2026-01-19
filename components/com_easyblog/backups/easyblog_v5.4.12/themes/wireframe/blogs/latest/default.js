EasyBlog.require()
.script('site/posts/listings')
<?php if ($this->config->get('layout_dropcaps')) { ?>
.script('site/dropcap')
<?php } ?>
.done(function($) {

	$('[data-blog-listings]').implement(EasyBlog.Controller.Listings, {
		"ratings": <?php echo $this->config->get('main_ratings') ? 'true' : 'false';?>,
		"autoload": <?php echo $showLoadMore ? 'true' : 'false'; ?>,
		"gdpr_enabled": <?php echo $this->config->get('gdpr_iframe_enabled') ? 'true' : 'false'; ?>,
		"hasPinterestEmbedBlock": <?php echo $hasPinterestEmbedBlock ? 'true' : 'false'; ?>,
		"pinterestExternalShareBtnEnabled": <?php echo EB::isExternalPinterestShareEnabled() ? 'true' : 'false'; ?>
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

		if (paragraph.length) {
			paragraph.addClass('has-drop-cap');
			return;
		}

		// Find a text node and wrap it with a paragraph when needed to
		item.contents()
			.filter(function(index, element) {

				if (this.nodeType !== 3) {
					return false;
				}

				var value = $.trim($(element).text());

				if (!value) {
					return false;
				}

				return true;
			})
			.wrap('<p class="has-drop-cap">');
	});
	<?php } ?>
});
